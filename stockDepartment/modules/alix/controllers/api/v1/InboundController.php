<?php

namespace stockDepartment\modules\alix\controllers\api\v1;

use stockDepartment\modules\alix\controllers\api\v1\inbound\service\InboundReturnService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Legacy-контроллер. Теперь обслуживает только возвраты (actionReturns).
 * POST alix/api/v1/inbound/orders перенаправлен на ecommerce-контроллер
 * (см. URL rules в stockDepartment/config/main.php), который пишет в ecommerce_inbound.
 */
class InboundController extends Controller
{
    public function init()
    {
        $this->enableCsrfValidation = false;
        $this->layout = "";
    }

    public function actionReturns()
    {
        $request = Yii::$app->getRequest()->getBodyParams();
        $request = $this->normalizeUuidKey($request);

        $is = new InboundReturnService();
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

        return $this->asJson([
            "status" => "success",
            "message" => "",
            "code" => "",
            "wms_id" => $orderId,
        ]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
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
            'only' => ['returns', 'echo'],
        ];
        return $behaviors;
    }

    public function actionEcho($value = "")
    {
        return $this->asJson(["response" => ["echo" => $value]]);
    }

    /**
     * Клиенты регулярно путают кириллическую «с» и латинскую "c" в ключе `1с_uuid`.
     */
    private function normalizeUuidKey(array $request)
    {
        if (!isset($request['1с_uuid']) && isset($request['1c_uuid'])) {
            $request['1с_uuid'] = $request['1c_uuid'];
        }
        return $request;
    }
}
