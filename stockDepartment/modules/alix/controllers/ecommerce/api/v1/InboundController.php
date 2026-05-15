<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1;

use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\service\InboundService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\Response;

// POST alix/ecommerce/api/v1/inbound/orders
class InboundController extends Controller
{
    public function init()
    {
        $this->enableCsrfValidation = false;
        $this->layout = "";
    }

    public function actionOrders()
    {
        $request = Yii::$app->getRequest()->getBodyParams();
        $request = $this->normalizeUuidKey($request);

        $is = new InboundService();
        $isValid = $is->isNotValidAddOrderData($request);
        if ($isValid->isInvalid()) {
            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $response->setStatusCode(400);
            $response->data = [
                "status" => "error",
                "message" => $isValid->getMessage(),
                "code" => "",
                "wms_id" => "",
            ];

            return $response;
        }

        $orderId = $is->addOrder($is->requestToCreateDTO($request));

        $this->notifyByTg($orderId);

        return $this->asJson([
            "status" => "success",
            "message" => "",
            "code" => "",
            "wms_id" => $orderId,
        ]);
    }

    private function notifyByTg($orderId)
    {
        try {
            $is = new InboundService();
            $order = $is->getOrderByID($orderId);
            if (empty($order)) {
                return;
            }
            // Notification is a stub mirrored from legacy controller — enable when needed.
        } catch (\Throwable $e) {
            Yii::error('Telegram notify failed: ' . $e->getMessage(), __METHOD__);
        }
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['apiLogger'] = ['class' => \common\log\IncomingApiLogger::class];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['orders', 'echo'],
        ];
        return $behaviors;
    }

    public function actionEcho($value = "")
    {
        return $this->asJson(["response" => ["echo" => $value]]);
    }

    /**
     * Клиенты регулярно путают кириллическую «с» и латинскую "c" в ключе `1с_uuid`.
     * Визуально ключи одинаковые — нормализуем на входе.
     */
    private function normalizeUuidKey(array $request)
    {
        if (!isset($request['1с_uuid']) && isset($request['1c_uuid'])) {
            $request['1с_uuid'] = $request['1c_uuid'];
        }
        return $request;
    }
}
