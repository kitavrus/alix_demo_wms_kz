<?php

namespace stockDepartment\modules\kaspi\services;

use stockDepartment\modules\kaspi\constants\KaspiConstants;
use stockDepartment\modules\kaspi\exceptions\KaspiApiException;
use stockDepartment\modules\kaspi\enums\OrderStatus;
use stockDepartment\modules\kaspi\test\KaspiMockFactory;
use Yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;
use yii\log\Logger;
use yii\web\Response;

/**
 * @see https://guide.kaspi.kz/partner/ru/shop/api/goods
 * @see https://guide.kaspi.kz/partner/ru/shop/api/orders
 */
class KaspiAPIService extends Component
{
    public $useMock = false;

    public $apiKey;

    public $baseUrl = KaspiConstants::BASE_URL;

    public $productsApiBaseUrl = KaspiConstants::PRODUCTS_API_BASE_URL;

    public $httpLogEnabled = false;

    private $_httpClient;

    private $_productsHttpClient;

    public function init()
    {
        parent::init();
        if ($this->apiKey === null || $this->apiKey === '') {
            $this->apiKey = KaspiConstants::API_TOKEN_PLACEHOLDER;
        }
        $this->_httpClient = new HttpClient(['baseUrl' => $this->baseUrl]);
        $this->_productsHttpClient = new HttpClient(['baseUrl' => rtrim($this->productsApiBaseUrl, '/') . '/']);
    }

    public function getProductsImportSchema()
    {
        if ($this->useMock) {
            return KaspiMockFactory::getProductsImportSchema();
        }

        return $this->sendHttpRequest(
            $this->createProductsJsonRequest()
                ->setMethod('GET')
                ->setUrl(KaspiConstants::PRODUCTS_IMPORT_SCHEMA_PATH)
        );
    }

    public function postProductsImportResponse(array $payload)
    {
        return $this->runKaspiJson(function () use ($payload) {
            return $this->postProductsImport($payload);
        });
    }

    public function postProductsImport(array $payload)
    {
        if ($this->useMock) {
            return KaspiMockFactory::getProductsImportPostResponse();
        }

        return $this->sendHttpRequest(
            $this->createProductsJsonPostRequest()
                ->setMethod('POST')
                ->setUrl(KaspiConstants::PRODUCTS_IMPORT_PATH)
                ->setData($payload)
        );
    }

    public function getProductsImportStatus($importCode)
    {
        if ($this->useMock) {
            return KaspiMockFactory::getProductsImportStatusByCode($importCode);
        }

        return $this->sendHttpRequest(
            $this->createProductsJsonRequest()
                ->setMethod('GET')
                ->setFormat(HttpClient::FORMAT_URLENCODED)
                ->setUrl(KaspiConstants::PRODUCTS_IMPORT_PATH)
                ->setData(['i' => $importCode])
        );
    }

    public function getProductsClassificationCategories()
    {
        if ($this->useMock) {
            return KaspiMockFactory::getProductsClassificationCategories();
        }

        return $this->sendHttpRequest(
            $this->createProductsJsonRequest()
                ->setMethod('GET')
                ->setUrl(KaspiConstants::PRODUCTS_CLASSIFICATION_CATEGORIES)
        );
    }

    public function getProductsClassificationAttributes($categoryCode)
    {
        if ($this->useMock) {
            return KaspiMockFactory::getProductsClassificationAttributes($categoryCode);
        }

        return $this->sendHttpRequest(
            $this->createProductsJsonRequest()
                ->setMethod('GET')
                ->setFormat(HttpClient::FORMAT_URLENCODED)
                ->setUrl(KaspiConstants::PRODUCTS_CLASSIFICATION_ATTRIBUTES)
                ->setData(['c' => $categoryCode])
        );
    }

    public function getProductsClassificationAttributeValues($categoryCode, $attributeCode)
    {
        if ($this->useMock) {
            return KaspiMockFactory::getProductsClassificationAttributeValues($categoryCode, $attributeCode);
        }

        return $this->sendHttpRequest(
            $this->createProductsJsonRequest()
                ->setMethod('GET')
                ->setFormat(HttpClient::FORMAT_URLENCODED)
                ->setUrl(KaspiConstants::PRODUCTS_CLASSIFICATION_ATTRIBUTE_VALUES)
                ->setData([
                    'c' => $categoryCode,
                    'a' => $attributeCode,
                ])
        );
    }

    // public function sendStocks(array $stocksDto)
    // {
    //     if ($this->useMock) {
    //         return ['result' => 'mock', 'endpoint' => KaspiConstants::PRODUCTS_V2_STOCKS_ENDPOINT];
    //     }
    //     $stocks = array_map(function (StockDto $dto) {
    //         return $dto->toArray();
    //     }, $stocksDto);

    //     $request = $this->createRequest();
    //     $request->setMethod('POST');
    //     $request->setUrl(KaspiConstants::PRODUCTS_V2_STOCKS_ENDPOINT);
    //     $request->setData(['stocks' => $stocks]);

    //     return $this->sendRequest($request);
    // }

    public function sendOffers(array $offersPayload)
    {
        if ($this->useMock) {
            return ['result' => 'mock', 'endpoint' => KaspiConstants::PRODUCTS_V2_OFFERS_ENDPOINT];
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('POST')
                ->setUrl(KaspiConstants::PRODUCTS_V2_OFFERS_ENDPOINT)
                ->setData($offersPayload)
        );
    }

    public function getProductsV2OffersSchema(array $params = [])
    {
        if ($this->useMock) {
            return KaspiMockFactory::getProductsV2OffersSchema();
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setUrl(KaspiConstants::PRODUCTS_V2_OFFERS_SCHEMA_ENDPOINT)
                ->setData($params)
        );
    }

    public function getProductsV2Categories(array $params = [])
    {
        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setUrl(KaspiConstants::PRODUCTS_V2_CATEGORIES_ENDPOINT)
                ->setData($params)
        );
    }

    public function getProductsV2Attributes(array $params = [])
    {
        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setUrl(KaspiConstants::PRODUCTS_V2_ATTRIBUTES_ENDPOINT)
                ->setData($params)
        );
    }

    public function getProductsV2AttributeValues(array $params = [])
    {
        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setUrl(KaspiConstants::PRODUCTS_V2_ATTRIBUTES_VALUES_PATH)
                ->setData($params)
        );
    }

    public function getProductsV2OffersImportStatus(array $params = [])
    {
        if ($this->useMock) {
            return KaspiMockFactory::getProductsV2OffersImportStatus();
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setUrl(KaspiConstants::PRODUCTS_V2_OFFERS_STATUS_ENDPOINT)
                ->setData($params)
        );
    }

    /** Получить список заказов. */
    public function getOrders(array $params = [])
    {
        return $this->getOrdersPage($params)->orders;
    }

    /** Получить список заказов. */
   public function getOrdersPage(array $params = [])
    {
        if ($this->useMock) {
            return KaspiOrderHydrator::hydrateOrderListResponse(KaspiMockFactory::getOrdersApiResponse());
        }

        // Kaspi требует pagination: page[number] и page[size].
        // Если клиент не передал их (или передал пустыми) — подставляем дефолты,
        // чтобы не получать 400 "Required pagination value [number] is empty.".
        $pageNumber = null;
        $pageSize = null;

        if (isset($params['page']) && is_array($params['page'])) {
            $pageNumber = $params['page']['number'] ?: null;
            $pageSize = $params['page']['size'] ?: null;
        }

        if ($pageNumber === null && array_key_exists('page[number]', $params)) {
            $pageNumber = $params['page[number]'];
        }
        if ($pageSize === null && array_key_exists('page[size]', $params)) {
            $pageSize = $params['page[size]'];
        }

        // Некоторые валидаторы на стороне Kaspi могут трактовать `0` как "empty",
        // поэтому безопаснее использовать >= 1.
        if ($pageNumber === null || $pageNumber === '' || is_array($pageNumber)) {
            $pageNumber = "0";
        }
        if ($pageSize === null || $pageSize === '' || is_array($pageSize)) {
            $pageSize = "100"; // Это максимум
        }

        // Гарантируем Kaspi-формат: оставляем только плоские ключи `page[number]` и `page[size]`.
        $paramsNormalized = $params;
        unset($paramsNormalized['page'], $paramsNormalized['page[number]'], $paramsNormalized['page[size]']);
        $paramsNormalized['page[number]'] = (int) $pageNumber;
        $paramsNormalized['page[size]'] = (int) $pageSize;

        // Kaspi также требует непустые creationDate диапазоны.
        // Если их не передали (или передали пустыми) — подставим дефолт.
        $nowMs = (int) floor(microtime(true) * 1000);

        // Kaspi ограничивает разницу по creationDate (max 14 дней).
        // Ставим дефолт чуть меньше, чтобы избежать пограничных случаев округления.
        $fromMsDefault = (int) floor((time() - 13 * 86400) * 1000);

        $flatGeKey = 'filter[orders][creationDate][$ge]';
        $flatLeKey = 'filter[orders][creationDate][$le]';

        // Возможны варианты хранения из Yii: плоские ключи или вложенные массивы.
        $ge = null;
        $le = null;

        if (array_key_exists($flatGeKey, $params) && $params[$flatGeKey] !== '' && $params[$flatGeKey] !== null) {
            $ge = $params[$flatGeKey];
        } elseif (isset($params['filter']['orders']['creationDate']['$ge']) && $params['filter']['orders']['creationDate']['$ge'] !== '') {
            $ge = $params['filter']['orders']['creationDate']['$ge'];
        }

        if (array_key_exists($flatLeKey, $params) && $params[$flatLeKey] !== '' && $params[$flatLeKey] !== null) {
            $le = $params[$flatLeKey];
        } elseif (isset($params['filter']['orders']['creationDate']['$le']) && $params['filter']['orders']['creationDate']['$le'] !== '') {
            $le = $params['filter']['orders']['creationDate']['$le'];
        }

        if ($ge === null || $ge === '' || is_array($ge)) {
            $ge = $fromMsDefault;
        }
        if ($le === null || $le === '' || is_array($le)) {
            $le = $nowMs;
        }

        // Уберём вложенные creationDate, если они были — чтобы не отправлять дубликаты.
        if (isset($paramsNormalized['filter']['orders']['creationDate'])) {
            unset($paramsNormalized['filter']['orders']['creationDate']);
        }
        unset($paramsNormalized[$flatGeKey], $paramsNormalized[$flatLeKey]);

        $paramsNormalized[$flatGeKey] = (string) ((int) $ge);
        $paramsNormalized[$flatLeKey] = (string) ((int) $le);

        $response = $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setFormat(HttpClient::FORMAT_URLENCODED)
                ->setUrl(KaspiConstants::ORDERS_ENDPOINT)
                ->setData($paramsNormalized)
        );

        return KaspiOrderHydrator::hydrateOrderListResponse($response);
    }

    public function getOrdersResponse(array $params = [])
    {
        return $this->runKaspiJson(function () use ($params) {
            $page = $this->getOrdersPage($params);
            return KaspiJsonApiSerializer::orderListToResponse($page);
        });
    }

    /**
     * Изменить статус заказа (POST /v2/orders, произвольные attributes).
     */
    public function postOrderPayload(array $payload)
    {
        if ($this->useMock) {
            return KaspiMockFactory::postOrderPayloadResponse($payload);
        }
        if (!isset($payload['data']) || !is_array($payload['data'])) {
            throw new \InvalidArgumentException('postOrderPayload: expected key "data"');
        }
        if (!isset($payload['data']['type'])) {
            $payload['data']['type'] = 'orders';
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('POST')
                ->setUrl(KaspiConstants::ORDERS_ENDPOINT)
                ->setData($payload)
        );
    }

    /** POST заказа: code + status (прибытие и т.п.). */
    public function postOrders($orderId, $code, $status)
    {
        return $this->postOrderPayload([
            'data' => [
                'type' => 'orders',
                'id' => $orderId,
                'attributes' => [
                    'code' => $code,
                    'status' => $status,
                ],
            ],
        ]);
    }

    /**
     * Получить адрес склада.
     * Получить склад заказа.
     */
    public function getOrderByIdRaw($orderId)
    {
        if ($this->useMock) {
            return KaspiMockFactory::getOrderByIdRawApiResponse($orderId);
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setUrl(KaspiConstants::ORDERS_ENDPOINT . '/' . rawurlencode($orderId))
        );
    }

    /** Один заказ по id (DTO). */
    public function getOrderById($orderId)
    {
        $response = $this->getOrderByIdRaw($orderId);
        if (!isset($response['data']) || !is_array($response['data'])) {
            return null;
        }

        $included = isset($response['included']) && is_array($response['included'])
            ? $response['included'] : [];

        return KaspiOrderHydrator::hydrateSingleOrder($response['data'], $included);
    }

    /** Получить заказ по коду. */
    public function getOrderByCode($code, array $extraParams = [], $single = true)
    {
        $params = $extraParams;
        unset($params['code']);
        $params['filter[orders][code]'] = $code;
        $orders = $this->getOrders($params);

        if ($single) {
            return count($orders) ? $orders[0] : null;
        }

        return $orders;
    }

    /** Получить заказ по коду (сырой JSON:API). */
    public function getOrdersByCodeRaw($code, array $extraParams = [])
    {
        $params = $extraParams;
        unset($params['code']);
        $params['filter[orders][code]'] = $code;
        if ($this->useMock) {
            $payload = KaspiMockFactory::getOrdersApiResponse();
            return $payload;
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setUrl(KaspiConstants::ORDERS_ENDPOINT)
                ->setData($params)
        );
    }

    public function isOrderCancelled($orderId)
    {
        $order = $this->getOrderById($orderId);

        return $order !== null && $order->status === OrderStatus::ORDER_CANCELLED;
    }

    /** Изменить статус заказа. */
    public function changeOrderStatus($orderId, $newStatus, array $extraData = [])
    {
        return $this->postOrderPayload([
            'data' => [
                'type' => 'orders',
                'id' => $orderId,
                'attributes' => array_merge(['status' => $newStatus], $extraData),
            ],
        ]);
    }

    /** Сформировать накладную (передача в доставку). */
    public function submitOrderKaspiDelivery($orderId)
    {
        return $this->changeOrderStatus($orderId, OrderStatus::ORDER_KASPI_DELIVERY);
    }

    /** Принять заказ. */
    public function acceptOrder($orderId)
    {
        return $this->changeOrderStatus($orderId, OrderStatus::ORDER_ACCEPTED_BY_MERCHANT);
    }

    /** Изменить статус на 'Выдан'. */
    public function completeOrder($orderId)
    {
        return $this->changeOrderStatus($orderId, OrderStatus::ORDER_COMPLETED);
    }

    /** Отменить заказ. */
    public function cancelOrder($orderId, $cancellationReason = null)
    {
        $extra = [];
        if ($cancellationReason !== null && $cancellationReason !== '') {
            $extra['cancellationReason'] = $cancellationReason;
        }

        return $this->changeOrderStatus($orderId, OrderStatus::ORDER_CANCELLED, $extra);
    }

    /** Указать IMEI товара. */
    public function setOrderImei($orderId, $imei)
    {
        return $this->postOrderPayload([
            'data' => [
                'type' => 'orders',
                'id' => $orderId,
                'attributes' => ['imei' => $imei],
            ],
        ]);
    }

    /** Изменить вес товаров. */
    public function setOrderWeight($orderId, $weight)
    {
        return $this->postOrderPayload([
            'data' => [
                'type' => 'orders',
                'id' => $orderId,
                'attributes' => ['weight' => $weight],
            ],
        ]);
    }

    /**
     * Получить товары заказа.
     * Получить описание товаров заказа.
     */
    public function getOrderEntries($orderId, array $params = [])
    {
        if ($this->useMock) {
            return KaspiMockFactory::getOrderEntriesApiResponse($orderId);
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setUrl($this->orderSubResourceUrl($orderId, KaspiConstants::ORDER_ENTRY_SUBPATH))
                ->setData($params)
        );
    }

    /**
     * Получить один товар из заказа.
     * Получить описание одного товара.
     * Получить IMEI товара.
     */
    public function getOrderEntry($orderId, $entryId)
    {
        if ($this->useMock) {
            return KaspiMockFactory::getOrderEntryApiResponse($orderId, $entryId);
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('GET')
                ->setUrl(
                    $this->orderSubResourceUrl($orderId, KaspiConstants::ORDER_ENTRY_SUBPATH)
                    . '/' . rawurlencode($entryId)
                )
        );
    }

    /** Удалить товар из заказа. */
    public function deleteOrderEntry($orderId, $entryId)
    {
        if ($this->useMock) {
            return KaspiMockFactory::getOrderEntryDeleteResponse();
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('DELETE')
                ->setUrl(
                    $this->orderSubResourceUrl($orderId, KaspiConstants::ORDER_ENTRY_SUBPATH)
                    . '/' . rawurlencode($entryId)
                )
        );
    }

    public function updateEntriesWeight($orderId, array $entriesPayload)
    {
        if ($this->useMock) {
            return KaspiMockFactory::getOrderEntriesApiResponse($orderId);
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('PATCH')
                ->setUrl($this->orderSubResourceUrl($orderId, KaspiConstants::ORDER_ENTRY_SUBPATH))
                ->setData($entriesPayload)
        );
    }

    public function createWaybill($orderId, array $payload)
    {
        if ($this->useMock) {
            return KaspiMockFactory::getOrderWaybillApiResponse($orderId);
        }

        return $this->sendRequest(
            $this->createRequest()
                ->setMethod('POST')
                ->setUrl($this->orderSubResourceUrl($orderId, KaspiConstants::ORDER_WAYBILL_SUBPATH))
                ->setData($payload)
        );
    }

    // Получить этикетку, пока TODO
    public function getShippingLabel($orderId)
    {
        return null;
    }

    // Замыкание для запросов с ответом или ошибкой

    public function runKaspiJson(callable $fn)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            return $fn();
        } catch (KaspiApiException $e) {
            $http = $e->getHttpStatusCode();
            Yii::$app->response->statusCode = ($http !== null && $http >= 400) ? $http : 502;

            return [
                'error' => true,
                'message' => $e->getMessage(),
                'kaspiBody' => $e->getResponseBody(),
            ];
        }
    }

    // MARK: - PRIVATES

    private function orderSubResourceUrl($orderId, $subPath)
    {
        return KaspiConstants::ORDERS_ENDPOINT . '/' . rawurlencode($orderId) . '/' . $subPath;
    }

    private function createRequest()
    {
        $request = $this->_httpClient->createRequest();
        $request->headers->set('X-Auth-Token', $this->apiKey);
        $request->headers->set('Content-Type', KaspiConstants::CONTENT_TYPE_JSON_API);
        $request->headers->set('Accept', KaspiConstants::CONTENT_TYPE_JSON_API);
        $request->setFormat(HttpClient::FORMAT_JSON);

        return $request;
    }

    private function createProductsJsonRequest()
    {
        $request = $this->_productsHttpClient->createRequest();
        $request->headers->set('X-Auth-Token', $this->apiKey);
        $request->headers->set('Accept', KaspiConstants::CONTENT_TYPE_JSON);
        $request->setFormat(HttpClient::FORMAT_JSON);

        return $request;
    }

    private function createProductsJsonPostRequest()
    {
        $request = $this->createProductsJsonRequest();
        $request->headers->set('Content-Type', KaspiConstants::CONTENT_TYPE_JSON);

        return $request;
    }

    private function sendRequest(Request $request)
    {
        return $this->sendHttpRequest($request);
    }

    private function sendHttpRequest(Request $request)
    {
        if ($this->httpLogEnabled && Yii::$app->has('log')) {
            Yii::getLogger()->log(
                "Kaspi HTTP request:\n" . $request->toString(),
                Logger::LEVEL_TRACE,
                KaspiConstants::LOG_CATEGORY
            );
        }

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            throw new KaspiApiException('Kaspi API transport error: ' . $e->getMessage(), 0, $e);
        }

        if ($this->httpLogEnabled && Yii::$app->has('log')) {
            Yii::getLogger()->log(
                "Kaspi HTTP response:\n" . $response->toString(),
                Logger::LEVEL_TRACE,
                KaspiConstants::LOG_CATEGORY
            );
        }

        if ($response->isOk) {
            $content = $response->content;
            if ($content === '' || $content === null || trim((string) $content) === '') {
                return [];
            }

            return Json::decode($content);
        }

        $body = $response->content;
        if (strlen($body) > 2000) {
            $body = substr($body, 0, 2000) . '…';
        }

        $reasonPhrase = '';
        if (method_exists($response, 'getReasonPhrase')) {
            $reasonPhrase = (string) $response->getReasonPhrase();
        }

        throw new KaspiApiException(
            'Kaspi API HTTP ' . $response->statusCode . ($reasonPhrase !== '' ? ': ' . $reasonPhrase : ''),
            (int) $response->statusCode,
            null,
            (int) $response->statusCode,
            $body
        );
    }
}
