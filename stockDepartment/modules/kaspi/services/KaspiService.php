<?php

namespace stockDepartment\modules\kaspi\services;

use stockDepartment\modules\kaspi\dto\PartialReturnRequestDto;
use stockDepartment\modules\kaspi\dto\PriceUpdateRequestDto;
use stockDepartment\modules\kaspi\dto\StockUpdateRequestDto;
use Yii;
use yii\base\Component;

class KaspiService extends Component
{
    /** @var KaspiAPIService|null */
    public $api;
    /** @var StockService|null */
    public $stockService;
    /** @var PriceListService|null */
    public $priceListService;
    /** @var PriceService|null */
    public $priceService;
    /** @var StockHistoryService|null */
    public $stockHistoryService;
    /** @var OrderReturnService|null */
    public $orderReturnService;

    public function init()
    {
        parent::init();

        $module = Yii::$app->getModule('kaspi');

        if ($this->api === null) {
            $this->api = $module !== null ? $module->get('apiService') : null;
        }
        if (!$this->api instanceof KaspiAPIService) {
            $this->api = new KaspiAPIService();
            $this->api->init();
        }

        if ($this->stockService === null) {
            $this->stockService = $module !== null ? $module->get('stockService') : null;
        }
        if (!$this->stockService instanceof StockService) {
            $this->stockService = new StockService();
        }

        if ($this->priceListService === null) {
            $this->priceListService = $module !== null ? $module->get('priceListService') : null;
        }
        if (!$this->priceListService instanceof PriceListService) {
            $this->priceListService = new PriceListService();
        }

        if ($this->priceService === null) {
            $this->priceService = $module !== null ? $module->get('priceService') : null;
        }
        if (!$this->priceService instanceof PriceService) {
            $this->priceService = new PriceService();
            $this->priceService->init();
        }

        if ($this->stockHistoryService === null) {
            $this->stockHistoryService = $module !== null ? $module->get('stockHistoryService') : null;
        }
        if (!$this->stockHistoryService instanceof StockHistoryService) {
            $this->stockHistoryService = new StockHistoryService();
            $this->stockHistoryService->init();
        }

        if ($this->orderReturnService === null) {
            $this->orderReturnService = $module !== null ? $module->get('orderReturnService') : null;
        }
        if (!$this->orderReturnService instanceof OrderReturnService) {
            $this->orderReturnService = new OrderReturnService();
            $this->orderReturnService->init();
        }
    }

    // MARK: - Orders

    public function orders(array $queryParams = [])
    {
        return $this->api->getOrdersResponse($queryParams);
    }

    public function orderById($orderId)
    {
        return $this->api->getOrderByIdRaw((string) $orderId);
    }

    // MARK: - Products / Price list

    /**
     * Категории товаров
     */
    public function productsClassificationCategories()
    {
        return $this->api->getProductsClassificationCategories();
    }

    /**
     * Характеристики товаров по коду категории
     */
    public function productsClassificationAttributes($categoryCode)
    {
        return $this->api->getProductsClassificationAttributes((string) $categoryCode);
    }

    /**
     * Генерирует единый Excel-прайс-лист kaspi-price-list.xlsx с остатками и ценами.
     *
     * Kaspi использует один файл для всего: цены + остатки по складам (PP1–PP5).
     * После генерации файл нужно вручную загрузить в кабинет:
     *   Товары → Загрузить прайс-лист
     *
     * Логика:
     * - Берёт все товары с status_availability = YES из ecommerce_stock.
     * - PP1 = количество единиц на складе.
     * - price = последняя активная цена из kaspi_price_history,
     *   либо product_price из ecommerce_stock если истории нет.
     * - Товары без цены в файл не попадают.
     * - После генерации помечает включённые SKU как SYNCED.
     */
    public function productsImportFromRequest()
    {
        // Фиксируем момент до запроса — защита от race condition при markKaspiStockAsSynced
        $beforeTime = time();
        $rows = $this->priceListService->buildCurrentPriceList();

        if (empty($rows)) {
            return [
                'status' => 'skipped',
                'saved'  => 0,
            ];
        }

        try {
            // Используем уже собранные строки — избегаем повторного запроса к БД
            $this->priceListService->generateFromRows($rows);
        } catch (\Exception $e) {
            Yii::error('Kaspi price list generation failed: ' . $e->getMessage(), 'kaspi');
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }

        // Помечаем только записи, созданные до начала генерации
        $skus = array_column($rows, 'sku');
        $this->stockService->markKaspiStockAsSynced($skus, $beforeTime);

        return [
            'status'   => 'generated',
            'products' => count($rows),
        ];
    }

    public function getAvailableStock()
    {
        return $this->stockService->getAvailableStock();
    }

    // MARK: - Prices

    /**
     * Принять массив новых цен, сохранить в историю и перегенерировать Excel.
     *
     * Принимает как массив объектов, так и одиночный объект (оборачивает в массив).
     *
     * @param array $requestBody [{product_guid, price, price_type, note, effective_from}, ...]
     * @return array
     */
    public function priceUpdate(array $requestBody)
    {
        $items = isset($requestBody[0]) ? $requestBody : [$requestBody];

        $dtos   = [];
        $errors = [];

        foreach ($items as $index => $item) {
            $dto = new PriceUpdateRequestDto();
            $dto->load($item, '');

            if (!$dto->validate()) {
                $errors[] = ['index' => $index, 'errors' => $dto->getErrors()];
            } else {
                $dtos[] = $dto;
            }
        }

        if (!empty($errors)) {
            return [
                'status' => 'validation_error',
                'errors' => $errors,
            ];
        }

        return $this->priceService->applyBatchPriceUpdate($dtos);
    }

    // MARK: - Stocks

    /**
     * Принять массив новых остатков, сохранить в историю и перегенерировать Excel.
     *
     * @param array $requestBody [{product_guid, qty, note?, effective_from?}, ...]
     * @return array
     */
    public function stockUpdate(array $requestBody)
    {
        $items = isset($requestBody[0]) ? $requestBody : [$requestBody];

        $dtos   = [];
        $errors = [];

        foreach ($items as $index => $item) {
            $dto = new StockUpdateRequestDto();
            $dto->load($item, '');

            if (!$dto->validate()) {
                $errors[] = ['index' => $index, 'errors' => $dto->getErrors()];
            } else {
                $dtos[] = $dto;
            }
        }

        if (!empty($errors)) {
            return [
                'status' => 'validation_error',
                'errors' => $errors,
            ];
        }

        return $this->stockHistoryService->applyBatchStockUpdate($dtos);
    }

    // MARK: - Products import status

    /**
     * Прокси к Kaspi: статус задачи импорта товаров по коду.
     */
    public function productsImportStatus($importCode)
    {
        return $this->api->getProductsImportStatus((string) $importCode);
    }

    // MARK: - Order lifecycle

    /**
     * Передать заказ на Kaspi Доставку: перевод заказа в статус ASSEMBLE
     * с указанием numberOfSpace (кол-во мест/накладных). После этого на стороне
     * Kaspi формируется накладная, а в атрибутах заказа появляется URL
     * поля `waybill` (PDF скачивается через getOrderLabel()).
     *
     * @param string $orderId Kaspi order id
     * @param array  $payload Опции: { "numberOfSpace": int (>=1, default 1) }
     * @return array
     */
    public function transferToCourier($orderId, array $payload = [])
    {
        $numberOfSpace = 1;
        if (isset($payload['numberOfSpace'])) {
            $numberOfSpace = max(1, (int) $payload['numberOfSpace']);
        }

        $statusResponse = $this->api->assembleOrder($orderId, $numberOfSpace);

        $order = $this->api->getOrderById($orderId);
        $waybillUrl = $order !== null && isset($order->waybill) ? (string) $order->waybill : '';
        $waybillNumber = $order !== null && isset($order->waybillNumber) ? (string) $order->waybillNumber : '';

        return [
            'status'          => 'OK',
            'order_id'        => $orderId,
            'order_status'    => 'ASSEMBLE',
            'number_of_space' => $numberOfSpace,
            'waybill_url'     => $waybillUrl,
            'waybill_number'  => $waybillNumber,
            'status_response' => $statusResponse,
        ];
    }

    /**
     * Получить PDF-этикетку заказа от Kaspi.
     *
     * @param string $orderId
     * @return array{mime:string, body:string}
     */
    public function getOrderLabel($orderId)
    {
        return $this->api->getShippingLabel($orderId);
    }

    // MARK: - Returns

    /**
     * Сценарий A: отмена заказа до доставки с возвратом товаров на сток.
     *
     * @param string $kaspiOrderId
     * @param array  $body {reason?}
     * @return array
     */
    public function cancelReturnToStock($kaspiOrderId, array $body = [])
    {
        $reason = isset($body['reason']) ? (string) $body['reason'] : null;
        return $this->orderReturnService->returnToStock($kaspiOrderId, $reason);
    }

    /**
     * Сценарий B: частичный возврат после доставки (оператор заводит возврат).
     *
     * @param string $kaspiOrderId
     * @param array  $body {items: [{product_guid, qty}], refund_code?, note?}
     * @return array
     */
    public function partialReturn($kaspiOrderId, array $body)
    {
        $dto = new PartialReturnRequestDto();
        $dto->load($body, '');

        if (!$dto->validate()) {
            return [
                'status' => 'validation_error',
                'errors' => $dto->getErrors(),
            ];
        }

        return $this->orderReturnService->createPartialReturn($kaspiOrderId, $dto);
    }

    /**
     * Окончательное подтверждение возврата — перевод Kaspi-заказа в RETURNED.
     * Вызывается после приёмки возврата на склад (см. диаграмму return).
     */
    public function confirmReturnCompleted($kaspiOrderId)
    {
        $response = $this->orderReturnService->confirmReturnCompleted($kaspiOrderId);
        return [
            'status'         => 'OK',
            'order_id'       => (string) $kaspiOrderId,
            'order_status'   => 'RETURNED',
            'kaspi_response' => $response,
        ];
    }
}
