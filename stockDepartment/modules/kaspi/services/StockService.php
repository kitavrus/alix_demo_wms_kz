<?php

namespace stockDepartment\modules\kaspi\services;

use Yii;
use common\ecommerce\entities\EcommerceStock;
use yii\helpers\BaseFileHelper;

/**
 * Доступные остатки для выгрузки в Kaspi.
 */
class StockService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAvailableStock()
    {
        return EcommerceStock::find()
            ->select([
                'product_sku',
                'product_name',
                'product_brand' => 'product_model',
                'product_category' => 'product_season_full'
            ])
            ->andWhere([
                'status_availability' => EcommerceStock::STATUS_AVAILABILITY_YES,
                'deleted' => 0,
            ])
            ->groupBy("product_sku, product_name, product_model, product_season_full")
            ->asArray()
            ->all();
    }

    /**
     * Получить SKU, которые нужно отправить в Kaspi.
     *
     * Критерии:
     * - товар доступен на складе (`status_availability = YES`)
     * - SKU еще не помечен как синхронизированный (`kaspi_stock_status != SYNCED`)
     *
     * Excel при этом формируется по полному доступному остатку (см. `getAvailableStock()`),
     * а тут — только то, что нужно реально импортировать.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getStockToImportToKaspi()
    {
        $rows = EcommerceStock::find()
            ->select([
                'product_sku',
                'product_name',
                'product_brand' => 'product_model',
                'product_category' => 'product_season_full'
            ])
            ->andWhere([
                'status_availability' => EcommerceStock::STATUS_AVAILABILITY_YES,
                'deleted' => 0,
            ])
            ->andWhere([
                // считаем "не импортированным", если поле пустое/NEW/NULL
                'or',
                ['kaspi_stock_status' => null],
                ['kaspi_stock_status' => ''],
                ['kaspi_stock_status' => EcommerceStock::KASPI_STOCK_STATUS_NEW],
            ])
            ->groupBy("product_sku, product_name, product_model, product_season_full")
            ->asArray()
            ->all();

        // Kaspi expects product import payload like:
        // [{ sku, title, brand, category, description, images, attributes }]
        return array_map(function (array $row) {
            return [
                'sku' => isset($row['product_sku']) ? (string) $row['product_sku'] : '',
                'title' => isset($row['product_name']) ? (string) $row['product_name'] : '',
                'brand' => isset($row['product_brand']) ? (string) $row['product_brand'] : '',
                'category' => isset($row['product_category']) ? (string) $row['product_category'] : '',
                // В текущей реализации у нас нет источника для описания/изображений/атрибутов.
                // Если нужно — добавим извлечение из таблиц/справочников.
                'description' => null,
                'images' => [],
                'attributes' => [],
            ];
        }, $rows);
    }

    /**
     * Пометить SKU как успешно синхронизированные в Kaspi.
     *
     * @param array<int, string> $productSkus
     * @return int number of affected rows
     */
    public function markKaspiStockAsSynced(array $productSkus)
    {
        $productSkus = array_values(array_filter(array_map('strval', $productSkus)));
        if (empty($productSkus)) {
            return 0;
        }

        return EcommerceStock::updateAll(
            ['kaspi_stock_status' => EcommerceStock::KASPI_STOCK_STATUS_SYNCED],
            [
                'and',
                ['deleted' => 0],
                ['in', 'product_sku', $productSkus],
            ]
        );
    }

    /**
     * Сохраняет остатки в Excel (xlsx) в папку модуля `modules/kaspi/stock`.
     *
     * @param array<int, array<string, mixed>> $stockProducts
     * @param string|null $directoryAlias alias папки назначения (по умолчанию - '@stockDepartment/modules/kaspi/stock')
     * @return string fullPath до сохраненного xlsx
     */
    public function exportAvailableStockToExcel(array $stockProducts, $directoryAlias = null)
    {
        if ($directoryAlias === null || $directoryAlias === '') {
            $directoryAlias = '@stockDepartment/modules/kaspi/stock';
        }
        $dirPath = Yii::getAlias($directoryAlias);
        BaseFileHelper::createDirectory($dirPath);

        // Файл должен быть всегда один: перезаписываем один и тот же xlsx.
        $fileName = 'kaspi-stock.xlsx';
        $fullPath = rtrim($dirPath, '/').'/'.$fileName;

        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Kaspi')
            ->setLastModifiedBy('Kaspi')
            ->setTitle('Kaspi stock export')
            ->setSubject('Kaspi stock export');

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('available-stock');

        // Чтобы текст в ячейках не обрезался: подгоним ширину и включим перенос строк.
        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);

        // Заголовки колонок
        $activeSheet->setCellValue('A1', 'product_sku');
        $activeSheet->setCellValue('B1', 'product_name');
        $activeSheet->setCellValue('C1', 'product_brand');
        $activeSheet->setCellValue('D1', 'product_category');

        $row = 2;
        foreach ($stockProducts as $product) {
            $productSku = isset($product['product_sku']) ? (string) $product['product_sku'] : '';
            $productName = isset($product['product_name']) ? (string) $product['product_name'] : '';
            $productBrand = isset($product['product_brand']) ? (string) $product['product_brand'] : '';
            $productCategory = isset($product['product_category']) ? (string) $product['product_category'] : '';

            $activeSheet->setCellValueExplicit(
                'A'.$row,
                $productSku,
                \PHPExcel_Cell_DataType::TYPE_STRING
            );
            $activeSheet->setCellValue('B'.$row, $productName);
            $activeSheet->setCellValue('C'.$row, $productBrand);
            $activeSheet->setCellValue('D'.$row, $productCategory);
            $row++;
        }

        $lastRow = max(1, $row - 1);
        $activeSheet->getStyle('A1:D'.$lastRow)->applyFromArray([
            'alignment' => [
                'wrap' => true,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
            ],
        ]);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($fullPath);

        return $fullPath;
    }
}

