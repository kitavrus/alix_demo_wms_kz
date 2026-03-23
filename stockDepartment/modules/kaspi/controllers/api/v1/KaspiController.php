<?php

namespace stockDepartment\modules\kaspi\controllers\api\v1;

use stockDepartment\components\Controller;
use stockDepartment\modules\kaspi\kaspi as KaspiModule;
use stockDepartment\modules\kaspi\services\KaspiService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class KaspiController extends Controller
{
    public $enableCsrfValidation = false;

    /** @var KaspiService */
    private $kaspiService;

    public function init()
    {
        parent::init();
        $this->kaspiService = $this->module->get('kaspiService');
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
                'products-import' => ['POST'],
                'products-classification-categories' => ['GET'],
                'products-classification-attributes' => ['GET'],
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
}
