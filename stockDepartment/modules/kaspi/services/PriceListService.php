<?php

namespace stockDepartment\modules\kaspi\services;

use common\ecommerce\entities\EcommerceStock;
use stockDepartment\modules\kaspi\models\KaspiPriceHistory;
use stockDepartment\modules\kaspi\models\KaspiStockHistory;
use stockDepartment\modules\kaspi\models\ProductV2;
use Yii;
use yii\base\Component;
use yii\helpers\BaseFileHelper;

/**
 * Генерирует Excel-прайс-лист для загрузки в Kaspi.
 *
 * Формат Kaspi (лист «Лист1», 10 колонок):
 *   SKU | model | brand | price | PP1 | PP2 | PP3 | PP4 | PP5 | preorder
 *
 * Правила из официального шаблона Kaspi:
 * - SKU      — строка (текст, не число!), уникальный артикул
 * - model    — название товара
 * - brand    — бренд
 * - price    — целое число в тенге (без копеек, без пробелов)
 * - PP1      — количество на складе (integer); у нас 1 склад → PP1 = qty
 * - PP2–PP5  — 0 (целое, не пусто — Kaspi требует все 5 столбцов)
 * - preorder — пустая ячейка если товар в наличии; число дней если предзаказ
 *
 * Один файл kaspi-price-list.xlsx перезаписывается при каждой генерации.
 * price берётся из kaspi_price_history (последняя активная запись по product_guid).
 * Если для товара нет истории цен — используется product_price из ecommerce_stock.
 *
 * @see https://guide.kaspi.kz/partner/ru/shop/goods/price_list/q2962
 */
class PriceListService extends Component
{
    const PRICE_LIST_FILE = 'kaspi-price-list.xlsx';
    const PRICE_LIST_XML_FILE = 'kaspi-price-list.xml';
    const PRICE_LIST_DIR_ALIAS = '@stockDepartment/modules/kaspi/price-list';
    const PRICE_LIST_WEB_DIR_ALIAS = '@stockDepartment/web';

    // TODO: заменить на реальные данные из кабинета Kaspi
    const KASPI_COMPANY = 'COMPANY_NAME';
    const KASPI_MERCHANT_ID = 'MERCHANT_ID';
    const KASPI_STORE_ID = 'STORE_ID';

    /**
     * Сгенерировать Excel-прайс-лист и вернуть путь к файлу.
     *
     * @return string Полный путь до сохранённого xlsx
     */
    public function generate()
    {
        $rows = $this->buildCurrentPriceList();
        $this->writeExcel($rows);
        $this->writeXml($rows);
    }

    /**
     * Сгенерировать Excel из уже собранных строк (чтобы не делать запрос к БД дважды).
     *
     * @param array $rows Результат buildCurrentPriceList()
     * @return string Полный путь до сохранённого xlsx
     */
    public function generateFromRows(array $rows)
    {
        $this->writeExcel($rows);
        $this->writeXml($rows);
    }

    /**
     * Собрать актуальный прайс-лист: все доступные товары с их текущими ценами.
     *
     * @return array<int, array{sku:string, model:string, brand:string, price:float, qty:int}>
     */
    public function buildCurrentPriceList()
    {
        // Получаем все доступные товары из стока
        $stockRows = EcommerceStock::find()
            ->select([
                'product_sku',
                'product_name'  => 'MIN(product_name)',
                'product_brand' => 'MIN(product_brand)',
                'product_price' => 'MAX(product_price)',
                'qty'           => 'COUNT(*)',
            ])
            ->andWhere([
                'status_availability' => EcommerceStock::STATUS_AVAILABILITY_YES,
                'deleted'             => 0,
            ])
            ->andWhere(['!=', 'product_sku', ''])
            ->andWhere(['not', ['product_sku' => null]])
            ->groupBy(['product_sku'])
            ->asArray()
            ->all();

        if (empty($stockRows)) {
            return [];
        }

        $allGuids = array_column($stockRows, 'product_sku');
        $historyPrices = $this->getLatestActivePrices($allGuids);
        $historyQuantities = $this->getLatestActiveQuantities($allGuids);

        // Маппинг GUID → article из product_v2 для колонки SKU в Excel
        $guidToArticle = ProductV2::find()
            ->select(['guid', 'article'])
            ->andWhere(['in', 'guid', $allGuids])
            ->asArray()
            ->all();
        $articleMap = [];
        foreach ($guidToArticle as $row) {
            $articleMap[$row['guid']] = (string) $row['article'];
        }

        $result = [];
        foreach ($stockRows as $row) {
            $guid = (string) $row['product_sku'];

            // SKU в Excel = артикул из product_v2, fallback на GUID
            $sku = isset($articleMap[$guid]) && $articleMap[$guid] !== ''
                ? $articleMap[$guid]
                : $guid;

            // Цена из истории имеет приоритет над product_price из стока.
            // Если цены нет нигде — ставим 0 (товар попадёт в файл, цену зададут позже).
            if (isset($historyPrices[$guid])) {
                $price = (float) $historyPrices[$guid];
            } elseif (!empty($row['product_price']) && (float) $row['product_price'] > 0) {
                $price = (float) $row['product_price'];
            } else {
                $price = 0;
            }

            // Остаток: override из kaspi_stock_history имеет приоритет над COUNT(*)
            if (array_key_exists($guid, $historyQuantities)) {
                $qty = (int) $historyQuantities[$guid];
            } else {
                $qty = (int) $row['qty'];
            }

            $result[] = [
                'sku'   => $sku,
                'model' => (string) $row['product_name'],
                'brand' => (string) $row['product_brand'],
                'price' => $price,
                'qty'   => $qty,
            ];
        }

        return $result;
    }

    /**
     * Полный путь до текущего файла прайс-листа (может не существовать).
     */
    public function getFilePath()
    {
        return rtrim(Yii::getAlias(self::PRICE_LIST_DIR_ALIAS), '/') . '/' . self::PRICE_LIST_FILE;
    }

    public function getXmlFilePath()
    {
        return rtrim(Yii::getAlias(self::PRICE_LIST_DIR_ALIAS), '/') . '/' . self::PRICE_LIST_XML_FILE;
    }

    // MARK: - Private

    /**
     * Получить последнюю активную цену по каждому SKU из kaspi_price_history.
     *
     * «Активная» = effective_from уже наступил + статус SENT или PENDING.
     *
     * @param string[] $skus
     * @return array<string, float> [sku => price]
     */
    private function getLatestActivePrices(array $skus)
    {
        if (empty($skus)) {
            return [];
        }

        // Для каждого SKU выбираем запись с максимальным effective_from (не позже now),
        // а среди равных — с максимальным id.
        $rows = KaspiPriceHistory::find()
            ->select(['product_guid', 'price', 'effective_from', 'id'])
            ->andWhere(['in', 'product_guid', $skus])
            ->andWhere(['push_status' => KaspiPriceHistory::PUSH_STATUS_SENT])
            ->andWhere(['<=', 'effective_from', time()])
            ->orderBy(['product_guid' => SORT_ASC, 'effective_from' => SORT_DESC, 'id' => SORT_DESC])
            ->asArray()
            ->all();

        $prices = [];
        foreach ($rows as $row) {
            $sku = (string) $row['product_guid'];
            // Берём только первую (самую свежую) запись на SKU
            if (!isset($prices[$sku])) {
                $prices[$sku] = (float) $row['price'];
            }
        }

        return $prices;
    }

    /**
     * Получить последний активный override остатка по каждому SKU из kaspi_stock_history.
     *
     * @param string[] $skus
     * @return array<string, int> [sku => qty]
     */
    private function getLatestActiveQuantities(array $skus)
    {
        if (empty($skus)) {
            return [];
        }

        $rows = KaspiStockHistory::find()
            ->select(['product_guid', 'qty', 'effective_from', 'id'])
            ->andWhere(['in', 'product_guid', $skus])
            ->andWhere(['push_status' => KaspiStockHistory::PUSH_STATUS_SENT])
            ->andWhere(['<=', 'effective_from', time()])
            ->orderBy(['product_guid' => SORT_ASC, 'effective_from' => SORT_DESC, 'id' => SORT_DESC])
            ->asArray()
            ->all();

        $quantities = [];
        foreach ($rows as $row) {
            $sku = (string) $row['product_guid'];
            if (!array_key_exists($sku, $quantities)) {
                $quantities[$sku] = (int) $row['qty'];
            }
        }

        return $quantities;
    }

    /**
     * Записать Excel-файл в формате Kaspi.
     *
     * Соответствует официальному шаблону «Шаблон прайс-листа в формате Excel.xlsx»:
     *   - лист называется «Лист1»
     *   - SKU — текстовая ячейка (чтобы не отрезались ведущие нули и буквы)
     *   - price — целое число (тенге, без дробной части)
     *   - PP1 — целое число (количество на единственном складе)
     *   - PP2–PP5 — 0 (Kaspi требует все 5 столбцов, лишние = 0)
     *   - preorder — пустая ячейка (товар в наличии)
     *
     * @param array $rows
     * @return string Путь до файла
     */
    private function writeExcel(array $rows)
    {
        $dirPath = Yii::getAlias(self::PRICE_LIST_DIR_ALIAS);
        BaseFileHelper::createDirectory($dirPath);

        $fullPath = rtrim($dirPath, '/') . '/' . self::PRICE_LIST_FILE;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('WMS')
            ->setTitle('Kaspi Price List');

        // Название листа точно как в официальном шаблоне Kaspi
        $sheet = $objPHPExcel->setActiveSheetIndex(0)->setTitle('Лист1');

        // Заголовки строго по шаблону Kaspi (регистр важен)
        // col 0=SKU, 1=model, 2=brand, 3=price, 4=PP1, 5=PP2, 6=PP3, 7=PP4, 8=PP5, 9=preorder
        $headers = ['SKU', 'model', 'brand', 'price', 'PP1', 'PP2', 'PP3', 'PP4', 'PP5', 'preorder'];
        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col, 1, $header);
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $rowNum = 2;
        foreach ($rows as $item) {
            // SKU — явно текст, чтобы Excel не конвертировал в число
            $sheet->setCellValueExplicitByColumnAndRow(
                0, $rowNum, (string) $item['sku'], \PHPExcel_Cell_DataType::TYPE_STRING
            );
            $sheet->setCellValueByColumnAndRow(1, $rowNum, (string) $item['model']);
            $sheet->setCellValueByColumnAndRow(2, $rowNum, (string) $item['brand']);

            // price — целое число в тенге (по шаблону: 385000, не 385000.00)
            $sheet->setCellValueByColumnAndRow(3, $rowNum, (int) round($item['price']));

            // PP1 — количество на нашем единственном складе
            $sheet->setCellValueByColumnAndRow(4, $rowNum, (int) $item['qty']);

            // PP2–PP5 — 0, так как других складов нет
            // (Kaspi требует все 5 столбцов, нельзя оставить пустыми)
            $sheet->setCellValueByColumnAndRow(5, $rowNum, 0);
            $sheet->setCellValueByColumnAndRow(6, $rowNum, 0);
            $sheet->setCellValueByColumnAndRow(7, $rowNum, 0);
            $sheet->setCellValueByColumnAndRow(8, $rowNum, 0);

            // preorder — пустая ячейка (null) если товар в наличии
            // Число дней ставим только для предзаказа — в текущей логике всегда пусто
            $sheet->setCellValueByColumnAndRow(9, $rowNum, null);

            $rowNum++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($fullPath);

        return $fullPath;
    }

    /**
     * Записать XML-файл в формате Kaspi.
     *
     * @see https://guide.kaspi.kz/partner/ru/shop/goods/price_list/q3251
     *
     * @param array $rows
     * @return string Путь до файла
     */
    private function writeXml(array $rows)
    {
        $dirPath = Yii::getAlias(self::PRICE_LIST_DIR_ALIAS);
        BaseFileHelper::createDirectory($dirPath);

        $fullPath = rtrim($dirPath, '/') . '/' . self::PRICE_LIST_XML_FILE;

        $date = date('Y-m-d H:i');

        $xml = new \DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;

        $catalog = $xml->createElement('kaspi_catalog');
        $catalog->setAttribute('date', $date);
        $catalog->setAttribute('xmlns', 'kaspiShopping');
        $catalog->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $catalog->setAttribute('xsi:schemaLocation', 'kaspiShopping http://kaspi.kz/kaspishopping.xsd');
        $xml->appendChild($catalog);

        $catalog->appendChild($xml->createElement('company', self::KASPI_COMPANY));
        $catalog->appendChild($xml->createElement('merchantid', self::KASPI_MERCHANT_ID));

        $offers = $xml->createElement('offers');
        $catalog->appendChild($offers);

        foreach ($rows as $item) {
            $offer = $xml->createElement('offer');
            $offer->setAttribute('sku', (string) $item['sku']);

            $offer->appendChild($xml->createElement('model', htmlspecialchars((string) $item['model'])));
            $offer->appendChild($xml->createElement('brand', htmlspecialchars((string) $item['brand'])));

            $availabilities = $xml->createElement('availabilities');
            $availability = $xml->createElement('availability');
            $availability->setAttribute('available', (int) $item['qty'] > 0 ? 'yes' : 'no');
            $availability->setAttribute('storeId', self::KASPI_STORE_ID);
            $availability->setAttribute('stockCount', (string) (int) $item['qty']);
            $availabilities->appendChild($availability);
            $offer->appendChild($availabilities);

            $offer->appendChild($xml->createElement('price', (string) (int) round($item['price'])));

            $offers->appendChild($offer);
        }

        $xml->save($fullPath);

        // Копируем в web/ для публичного доступа (автозагрузка Kaspi)
        $webPath = rtrim(Yii::getAlias(self::PRICE_LIST_WEB_DIR_ALIAS), '/') . '/' . self::PRICE_LIST_XML_FILE;
        @copy($fullPath, $webPath);

        return $fullPath;
    }
}
