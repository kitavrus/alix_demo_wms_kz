<?php

namespace stockDepartment\modules\alix\controllers\api\v1;

use app\modules\ecommerce\controllers\alix\inbound\domain\mapper\InboundAPIMapper;
use stockDepartment\modules\alix\controllers\api\v1\inbound\service\InboundReturnService;
use stockDepartment\modules\alix\controllers\api\v1\inbound\service\InboundService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\VarDumper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use stockDepartment\modules\alix\controllers\common\notify\b2b\NewInboundOrderMsgDTO;
use stockDepartment\modules\alix\controllers\common\notify\b2b\TelegramalixB2BNotification;

class InboundController extends Controller
{
	public function init() {
		$this->enableCsrfValidation = false;
		$this->layout = "";
	}

    public function actionOrders()
    {
		$request = Yii::$app->getRequest()->getBodyParams();

//		VarDumper::dump($request,10,true);
//		die;
		$is = new InboundService();
		$isValid = $is->isNotValidAddOrderData($request);
		if ($isValid->isInvalid()) {
			$response = Yii::$app->getResponse();
			$response->format = Response::FORMAT_JSON;
			$response->setStatusCode(400);
			$response->data = [
				"status"=>"error",
				"message"=>$isValid->getMessage(),
				"code"=>"",
				"wms_id"=>"",
			];

			return $response;
		}
		
		$orderId = $is->addOrder($is->requestToCreateDTO($request));
		
		$this->notifyByTg($orderId);
		
        return $this->asJson([
        	"status"=>"success",
        	"message"=>"",
        	"code"=>"",
        	"wms_id"=>$orderId,
		]);
    }
	
	private function notifyByTg($orderId) {
		try {
			$is = new InboundService();
			$order = $is->getOrderByID($orderId);
			if (empty($order)) {
				return;
			}
			// TelegramalixB2BNotification::sendMessageIfNewInboundOrder(
			// 	new NewInboundOrderMsgDTO($order->order_number,$order->expected_qty,$order->comments)
			// );
		} catch (\Throwable $e) {
			Yii::error('Telegram notify failed: ' . $e->getMessage(), __METHOD__);
		}
	}

	public function actionReturns()
	{
		$request = Yii::$app->getRequest()->getBodyParams();

//		VarDumper::dump($request,10,true);
//		die;
		$is = new InboundReturnService();
		$isValid = $is->isNotValidAddOrderData($request);
		if ($isValid->isInvalid()) {
			$response = Yii::$app->getResponse();
			$response->format = Response::FORMAT_JSON;
			$response->setStatusCode(400);
			$response->data = [
				"status"=>"error",
				"message"=>$isValid->getMessage(),
				"code"=>"",
				"wms_id"=>"",
			];

			return $response;
		}
		$orderId = $is->addOrder($is->requestToCreateDTO($request));
		
		$this->notifyByTg($orderId);
		
		return $this->asJson([
			"status"=>"success",
			"message"=>"",
			"code"=>"",
			"wms_id"=>$orderId,
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
				]
			]
		];
		$behaviors['authenticator'] = [
			'class' => HttpBearerAuth::class,
			'only' => ['orders','returns','echo'],

		];
		return $behaviors;
	}


	public function actionEcho($value = "")
	{
		$result = [];
		$result['echo'] = $value;
		return $this->asJson(["response"=>$result]);
	}
}