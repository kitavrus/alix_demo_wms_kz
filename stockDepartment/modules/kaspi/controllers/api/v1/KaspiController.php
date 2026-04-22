<?php

namespace stockDepartment\modules\kaspi\controllers\api\v1;

use stockDepartment\components\Controller;
use stockDepartment\modules\kaspi\kaspi as KaspiModule;
use stockDepartment\modules\kaspi\services\KaspiService;
use stockDepartment\modules\kaspi\services\PriceListService;
use stockDepartment\modules\kaspi\services\ProductSyncService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class KaspiController extends Controller
{
    public $enableCsrfValidation = false;

    /** @var KaspiService */
    private $kaspiService;

    /** @var PriceListService */
    private $priceListService;

    public function init()
    {
        parent::init();
        $this->kaspiService     = $this->module->get('kaspiService');
        $this->priceListService = $this->module->get('priceListService');
    }

    /** Доступ: сессия, либо inboundApiToken / Bearer, либо allowGuestApi. */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'matchCallback' => function () {
                        /** @var KaspiModule|null $module */
                        $module = Yii::$app->getModule('kaspi');
                        if ($module !== null && $module->allowGuestApi) {
                            return true;
                        }
                        if (!Yii::$app->user->isGuest) {
                            return true;
                        }
                        $secret = $module !== null ? (string) $module->inboundApiToken : '';
                        if ($secret === '' || $secret === null) {
                            return false;
                        }
                        $hdr = Yii::$app->request->headers->get('X-Kaspi-Inbound-Token');
                        if ($hdr === null || $hdr === '') {
                            $auth = Yii::$app->request->headers->get('Authorization');
                            if (is_string($auth) && strncasecmp($auth, 'Bearer ', 7) === 0) {
                                $hdr = trim(substr($auth, 7));
                            }
                        }
                        return is_string($hdr) && hash_equals((string) $secret, $hdr);
                    },
                ],
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'orders' => ['GET'],
                'order' => ['GET'],
                'products-import' => ['POST'],
                'products-import-status' => ['GET'],
                'products-classification-categories' => ['GET'],
                'products-classification-attributes' => ['GET'],
                'price-update' => ['POST'],
                'price-list-download' => ['GET'],
                'price-list-download-xml' => ['GET'],
                'price-list-generate' => ['POST'],
                'stock-update' => ['POST'],
                'transfer-to-courier' => ['POST'],
                'order-label' => ['GET'],
                'cancel-return-to-stock' => ['POST'],
                'partial-return' => ['POST'],
                'confirm-return-completed' => ['POST'],
                'alix-sync-items' => ['POST', 'GET'],
            ],
        ];

        return $behaviors;
    }

    // MARK: - Orders

    /**
     * Получить список заказов
     * GET /v2/orders
     * */
    public function actionOrders()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->kaspiService->orders(Yii::$app->request->get());
    }

    /**
     * Получить один заказ по id (сырой JSON:API ответ Kaspi).
     *
     * GET /kaspi/api/v1/orders/<orderId>
     */
    public function actionOrder($orderId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->kaspiService->orderById((string) $orderId);
    }

    // MARK: - Products

    /**
     * Добавить товаров для продажи
     * POST /products-import
     */
    public function actionProductsImport()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->kaspiService->productsImportFromRequest();
    }

    /**
     * Список категорий товара
     * GET /products-classification-categories
     */
    public function actionProductsClassificationCategories()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->kaspiService->productsClassificationCategories();
    }

    /**
     * Список характеристик товара по категории
     * GET /products-classification-attributes?c=...
     */
    public function actionProductsClassificationAttributes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $categoryCode = Yii::$app->request->get('c');
        if ($categoryCode === null || trim((string) $categoryCode) === '') {
            throw new BadRequestHttpException('Query parameter c (category code) is required');
        }

        return $this->kaspiService->productsClassificationAttributes((string) $categoryCode);
    }

    // MARK: - Prices

    /**
     * Принять новую цену товара и отправить её на Kaspi.
     *
     * POST /kaspi/api/v1/price-update
     *
     * Тело запроса (JSON):
     * {
     *   "product_guid":   "KZ_SKU_12345",      // GUID / merchantProductCode товара
     *   "price":          19990.00,             // Новая цена в KZT
     *   "price_type":     "BASE",               // Тип цены
     *   "note":           "Весенняя коллекция", // Произвольная заметка (опционально)
     *   "effective_from": "2026-05-01"          // Дата активации Y-m-d (по умолчанию — сегодня)
     * }
     *
     * Если effective_from <= сегодня — цена отправляется в Kaspi API немедленно.
     * Если effective_from в будущем — запись сохраняется в kaspi_price_history со статусом PENDING
     * и будет активирована cron-задачей.
     */
    public function actionPriceUpdate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $body = Yii::$app->request->getBodyParams();
        if (empty($body)) {
            throw new BadRequestHttpException('Request body is required (JSON)');
        }

        return $this->kaspiService->priceUpdate($body);
    }

    /**
     * Скачать последний сгенерированный Excel-прайс-лист.
     *
     * GET /kaspi/api/v1/price-list-download
     *
     * Возвращает xlsx-файл для ручной загрузки в кабинет Kaspi:
     *   Товары → Загрузить прайс-лист
     */
    public function actionPriceListDownload()
    {
        $filePath = $this->priceListService->getFilePath();

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('Price list file not found. Generate it first via POST /kaspi/api/v1/price-list-generate');
        }

        return Yii::$app->response->sendFile($filePath, 'kaspi-price-list.xlsx');
    }

    /**
     * Скачать последний сгенерированный XML-прайс-лист.
     *
     * GET /kaspi/api/v1/price-list-download-xml
     */
    public function actionPriceListDownloadXml()
    {
        $filePath = $this->priceListService->getXmlFilePath();

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('XML price list file not found. Generate it first via POST /kaspi/api/v1/price-update');
        }

        return Yii::$app->response->sendFile($filePath, 'kaspi-price-list.xml');
    }

    /**
     * Принудительно перегенерировать Excel-прайс-лист по текущим ценам.
     *
     * POST /kaspi/api/v1/price-list-generate
     *
     * Полезно, если остатки изменились и нужно обновить файл без изменения цен.
     */
    public function actionPriceListGenerate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $rows = $this->priceListService->buildCurrentPriceList();
            $this->priceListService->generateFromRows($rows);

            return [
                'status'   => 'generated',
                'products' => count($rows),
            ];
        } catch (\Exception $e) {
            Yii::error('Kaspi price list generation failed: ' . $e->getMessage(), 'kaspi.price');
            Yii::$app->response->statusCode = 500;
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // MARK: - Stocks

    /**
     * Принять новый остаток товара (или батч) с датой активации.
     *
     * POST /kaspi/api/v1/stock-update
     *
     * Тело запроса (JSON): одиночный объект или массив.
     * {
     *   "product_guid":   "KZ_SKU_12345",
     *   "qty":            5,
     *   "note":           "Ручная корректировка",
     *   "effective_from": "2026-05-01"
     * }
     *
     * Если effective_from <= сегодня — остаток попадает в Excel немедленно.
     * Если в будущем — запись сохраняется как PENDING и активируется cron-задачей.
     */
    public function actionStockUpdate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $body = Yii::$app->request->getBodyParams();
        if (empty($body)) {
            throw new BadRequestHttpException('Request body is required (JSON)');
        }

        return $this->kaspiService->stockUpdate($body);
    }

    // MARK: - Products import status

    /**
     * Получить статус задачи импорта товаров в Kaspi.
     *
     * GET /kaspi/api/v1/kaspi/products-import-status?i=<importCode>
     */
    public function actionProductsImportStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $importCode = Yii::$app->request->get('i');
        if ($importCode === null || trim((string) $importCode) === '') {
            throw new BadRequestHttpException('Query parameter i (import code) is required');
        }

        return $this->kaspiService->productsImportStatus((string) $importCode);
    }

    // MARK: - Orders lifecycle

    /**
     * Передать заказ курьеру: создание накладной (waybill) + смена статуса на KASPI_DELIVERY.
     *
     * POST /kaspi/api/v1/orders/<orderId>/transfer-to-courier
     *
     * Тело запроса (опционально): атрибуты накладной для Kaspi createWaybill.
     */
    public function actionTransferToCourier($orderId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = Yii::$app->request->getBodyParams();
        if (!is_array($payload)) {
            $payload = [];
        }

        return $this->kaspiService->transferToCourier((string) $orderId, $payload);
    }

    /**
     * Скачать PDF-этикетку заказа от Kaspi (для печати и наклейки на коробку).
     *
     * GET /kaspi/api/v1/orders/<orderId>/label
     */
    public function actionOrderLabel($orderId)
    {
        $label = $this->kaspiService->getOrderLabel((string) $orderId);

        if (!is_array($label) || empty($label['body'])) {
            throw new NotFoundHttpException('Label not available for order ' . $orderId);
        }

        $mime = isset($label['mime']) ? (string) $label['mime'] : 'application/pdf';
        $fileName = 'kaspi-label-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $orderId) . '.pdf';

        return Yii::$app->response->sendContentAsFile(
            (string) $label['body'],
            $fileName,
            ['mimeType' => $mime, 'inline' => true]
        );
    }

    // MARK: - Returns

    /**
     * Отмена заказа до доставки с возвратом товаров в сток.
     *
     * POST /kaspi/api/v1/orders/<orderId>/cancel-return-to-stock
     *
     * Тело (опционально): { "reason": "..." }
     *
     * 1) Отправляет cancelOrder в Kaspi.
     * 2) Возвращает зарезервированные строки ecommerce_stock в статус YES.
     */
    public function actionCancelReturnToStock($orderId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $body = Yii::$app->request->getBodyParams();
        if (!is_array($body)) {
            $body = [];
        }

        return $this->kaspiService->cancelReturnToStock((string) $orderId, $body);
    }

    /**
     * Частичный возврат после доставки: оператор вручную заводит возврат.
     *
     * POST /kaspi/api/v1/orders/<orderId>/partial-return
     *
     * Тело:
     * {
     *   "items":       [{"product_guid":"SKU1","qty":1}],
     *   "refund_code": "KASPI_REFUND_123",
     *   "note":        "..."
     * }
     *
     * Создаётся EcommerceInbound (return) с source_kaspi_order_id / source_kaspi_refund_code.
     * Физическая приёмка идёт через существующий inbound-флоу.
     */
    public function actionPartialReturn($orderId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $body = Yii::$app->request->getBodyParams();
        if (empty($body)) {
            throw new BadRequestHttpException('Request body is required (JSON)');
        }

        return $this->kaspiService->partialReturn((string) $orderId, $body);
    }

    /**
     * Окончательное подтверждение возврата — после приёмки возврата на склад.
     *
     * POST /kaspi/api/v1/orders/<orderId>/confirm-return-completed
     *
     * Переводит Kaspi-заказ в статус RETURNED. На стороне Kaspi это триггерит
     * регистрацию возврата на кассе и перевод денежных средств покупателю.
     */
    public function actionConfirmReturnCompleted($orderId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->kaspiService->confirmReturnCompleted((string) $orderId);
    }

    // MARK: - Alix 1C items sync (ручной триггер крона)

    /**
     * Вручную запустить синхронизацию номенклатуры из Alix 1C
     * в product_v2 / product_barcodes_v2.
     *
     * Тот же процесс, что и cron `cron/alix-sync-items`, но через HTTP.
     *
     * POST /kaspi/api/v1/alix-sync-items
     *   (GET тоже разрешён для удобной отладки из браузера)
     *
     * Ответ (JSON):
     * {
     *   "status": "OK" | "PARTIAL" | "ERROR",
     *   "fetched": 1234,
     *   "created": 10,
     *   "updated": 1224,
     *   "barcodes_added": 15,
     *   "errors": 0,
     *   "message": "..."   // только при ERROR/PARTIAL
     * }
     */
    public function actionAlixSyncItems()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var ProductSyncService $service */
        $service = $this->module->get('productSyncService');

        $result = $service->syncFromApi();

        if (isset($result['status']) && $result['status'] === 'ERROR') {
            Yii::$app->response->statusCode = 502;
        }

        return $result;
    }
}
