<?php

namespace app\modules\intermode\controllers\ecommerce\api\v1;

use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundAPIStatus;
use app\modules\intermode\controllers\ecommerce\outbound\domain\mapper\OutboundAPIMapper;
use app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\httpclient\Client as HttpClient;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use app\modules\intermode\controllers\common\notify\TelegramIntermodeB2CNotification;
use app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundReservationService;
use app\modules\intermode\controllers\ecommerce\outbound\domain\repository\OutboundRepository;


// /intermode/ecommerce/api/v1/outbound/orders
class OutboundController extends Controller
{
	public function init() {
		$this->enableCsrfValidation = false;
		$this->layout = "";
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
//			'optional' => ['*'],
			'only' => ['add-order','orders','cancel'],

		];
		return $behaviors;
	}

	/**
	 * @throws \yii\base\InvalidConfigException
	 * @throws BadRequestHttpException
	 */
	public function actionOrders()
    {
		$request = Yii::$app->getRequest()->getBodyParams();
		file_put_contents("actionAddOrder-b2c.log", date(DATE_ISO8601)."\n"."request:"."\n".print_r($request,true)."\n"."\n",FILE_APPEND);
		$os = new OutboundService();
		if ($os->isNotValidAddOrderData($request)) {
			throw new BadRequestHttpException('Invalid data order_id or items');
		}
		$order = $os->addOrder($os->requestToCreateDTO($request));
		
		if ($order) {
			$data = [];
			$data["orderNumber"] = $order->order_number;
			$data["expectedQty"] = $order->expected_qty;

			TelegramIntermodeB2CNotification::sendMessageIfNewOrder($data);
			
			
			$reservationService = new OutboundReservationService();
			$repository = new OutboundRepository();
			$reservationService->run($repository->getOrderInfo($order->id));

			$orderInfo = $repository->getOrderInfo($order->id);
			 if ($orderInfo->order->allocated_qty < 1) {
				$repository->changeStatusOfOutOfStock($order->id);
				$response = (new OutboundAPIMapper())->makeByItemAcceptedAddOrderResponseDTO($orderInfo);
				// $apiService->sendStatusOutOfStock($response);
				return $this->asJson([
					"order_id"=>$order->order_number,
					"status"=>OutboundAPIStatus::OUT_OF_STOCK,
					"items"=> $response->items,
				]);
			}
		}
		
//		$response = (new OutboundAPIMapper())->makeByItemAddOrderResponseDTO($os->getOrderInfo($order->id));
        return $this->asJson([
			"order_id"=>$order->order_number,
			"status"=>OutboundAPIStatus::WAINING_PICKING,
			"items"=> [],//$response->items,
		]);
    }
	
   /**
	 * @throws \yii\base\InvalidConfigException
	 * @throws BadRequestHttpException
	 */
	public function actionCancel()
    {
		$request = Yii::$app->getRequest()->getBodyParams();
		file_put_contents("actionCancel-b2c.log", date(DATE_ISO8601)."\n"."request:"."\n".print_r($request,true)."\n"."\n",FILE_APPEND);
		$os = new OutboundService();
		if ($os->isNotValidCancelOrderData($request)) {
			throw new BadRequestHttpException('Invalid data order_id');
		}
		$order = $os->cancelOrder($request["order_id"]);
		return $this->asJson([
			"order_id"=>$order->order_number,
			"status"=>OutboundAPIStatus::CANCELED,
		]);
    }
	
}