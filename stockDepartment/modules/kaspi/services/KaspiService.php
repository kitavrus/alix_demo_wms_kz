<?php

namespace stockDepartment\modules\kaspi\services;

use Yii;
use yii\base\Component;

class KaspiService extends Component
{
    /** @var KaspiAPIService|null */
    public $api;
    /** @var StockService|null */
    public $stockService;

    public function init()
    {
        parent::init();
        if ($this->api === null) {
            $module = Yii::$app->getModule('kaspi');
            if ($module !== null) {
                $this->api = $module->get('apiService');
            }
        }
        if (!$this->api instanceof KaspiAPIService) {
            $this->api = new KaspiAPIService();
            $this->api->init();
        }
        if ($this->stockService === null) {
            $module = Yii::$app->getModule('kaspi');
            if ($module !== null) {
                $this->stockService = $module->get('stockService');
            }
        }
        if (!$this->stockService instanceof StockService) {
            $this->stockService = new StockService();
        }
    }

    // MARK: - Orders

    public function orders(array $queryParams = [])
    {
        return $this->api->getOrdersResponse($queryParams);
    }

    // MARK: - Products

    /**
     * Категории товаров
     * GET /products/classification/categories
     *
     * @return array
     */
    public function productsClassificationCategories()
    {
        return $this->api->getProductsClassificationCategories();
    }

    /**
     * Характеристики товаров по коду категории
     * GET /products/classification/attributes?c=...
     *
     * @param string $categoryCode
     * @return array
     */
    public function productsClassificationAttributes($categoryCode)
    {
        return $this->api->getProductsClassificationAttributes((string) $categoryCode);
    }

    /**
     * Импортирует товары в Kaspi из доступных остатков.
     * Большие объемы режет на пачки по 1000 товаров за один запрос.
     */
    public function productsImportFromRequest()
    {
        // Excel должен быть актуальным по текущему остатку на складе.
        $stockProductsForExcel = $this->stockService->getAvailableStock();
        // В Kaspi отправляем только то, что еще не помечено как синхронизированное.
        $stockProductsToImport = $this->stockService->getStockToImportToKaspi();

        $stockCount = is_array($stockProductsToImport) ? count($stockProductsToImport) : 0;

        // Экспортируем остатки в xlsx для отладки/контроля (генератор в StockService).
        $stockExcelFile = null;
        try {
            $stockExcelFile = $this->stockService->exportAvailableStockToExcel($stockProductsForExcel);
        } catch (\Exception $e) {
            Yii::error('Kaspi stock export to excel failed: '.$e->getMessage(), __METHOD__);
        }

        if ($stockCount === 0) {
            return [
                'status' => 'skipped',
                'message' => 'No new stock to import to Kaspi (everything is already SYNCED)',
                'stockCount' => 0,
                'stockExcelFile' => $stockExcelFile,
            ];
        }

        // Ограничение: если на складе слишком мало позиций, не дергаем Kaspi. Может это и не надо будет
        if ($stockCount < 5) {
            return [
                'status' => 'skipped',
                'message' => 'Not enough available stock to import to Kaspi (min 5)',
                'stockCount' => $stockCount,
                'stockExcelFile' => $stockExcelFile,
            ];
        }

        $batchSize = 1000;
        $processedCount = 0;
        $batchIndex = 0;

        for ($offset = 0; $offset < $stockCount; $offset += $batchSize) {
            $batchIndex++;
            $batch = array_slice($stockProductsToImport, $offset, $batchSize);
            if (empty($batch)) {
                break;
            }

            $kaspiResponse = $this->api->postProductsImportResponse($batch);

            if (is_array($kaspiResponse) && !empty($kaspiResponse['error'])) {
                return array_merge([
                    'stockCount' => $stockCount,
                    'failedBatchIndex' => $batchIndex,
                    'processedCount' => $processedCount,
                    'stockExcelFile' => $stockExcelFile,
                ], $kaspiResponse);
            }

            $processedCount += count($batch);

            // Если запрос к Kaspi прошёл успешно (и ошибок в ответе нет),
            // помечаем SKU как синхронизированные.
            $batchSkus = [];
            foreach ($batch as $row) {
                // В payload сейчас используется поле `sku`,
                // но оставим поддержку старого варианта `product_sku`.
                if (isset($row['sku']) && $row['sku'] !== '') {
                    $batchSkus[] = (string) $row['sku'];
                } elseif (isset($row['product_sku']) && $row['product_sku'] !== '') {
                    $batchSkus[] = (string) $row['product_sku'];
                }
            }
            $this->stockService->markKaspiStockAsSynced($batchSkus);
        }

        return [
            'status' => 'success',
            'message' => 'Products imported to Kaspi from stock',
            'stockCount' => $stockCount,
            'stockExcelFile' => $stockExcelFile,
        ];
    }

    public function getAvailableStock()
    {
        return $this->stockService->getAvailableStock();
    }
}