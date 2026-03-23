<?php

//namespace stockDepartment\controllers;
namespace app\modules\ecommerce\controllers\intermode;

use app\modules\ecommerce\controllers\intermode\inbound\domain\constants\InboundAPIStatus;
use app\modules\ecommerce\controllers\intermode\inbound\domain\InboundService;
use app\modules\ecommerce\controllers\intermode\inbound\domain\mapper\InboundAPIMapper;
use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\OutboundAPIStatus;
use app\modules\ecommerce\controllers\intermode\outbound\domain\mapper\OutboundAPIMapper;
use app\modules\ecommerce\controllers\intermode\outbound\domain\OutboundService;
use app\modules\ecommerce\controllers\intermode\stock\domain\StockService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\httpclient\Client as HttpClient;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class ApiController extends Controller
{
	public function init() {
		$this->enableCsrfValidation = false;
		$this->layout = "";
	}

    public function actionEcho($value = "")
    {
		$result = [];
		$result['echo'] = $value;
        return $this->asJson(["response"=>$result]);
    }

    public function actionSendStatus()
    {
		//$clientHttp = new HttpClient();
//		$newUserResponse = $clientHttp->post(
//			'http://10.3.172.2/trade_for_Bakhtishat/hs/ordersapi/orders/',
//			[
//				'order_id' => "S_166",
//				'status' => "В работе",
//			]
//		)->send();

		$client = new HttpClient();
		$request = $client->createRequest();
		$username = 'WMS';
		$password = 'JA8gusyz';
		$request->headers->set('Authorization', 'Basic ' . base64_encode("$username:$password"));
		$request->setMethod('POST');
		$request->setFormat(HttpClient::FORMAT_JSON);
		$request->setUrl('http://10.3.172.2/trade_for_Bakhtishat/hs/ordersapi/orders/');
		$request->setData([
				'order_id' => "S_166",
				'status' => "В работе",
		]);
		$response =  $request->send();


        return $this->asJson(["response"=>$response,"statusCode"=>$response->getStatusCode()]);
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
			'only' => ['add-order','orders','send-status','add-inbound-order','get-stock-remains'],

		];
		return $behaviors;
	}

	// /ecommerce/intermode/api/orders
    public function actionOrdersV1()
    {
		$request = Yii::$app->getRequest()->getBodyParams();


		file_put_contents("orders.log",date(DATE_RFC822)."\n".print_r($request,true)."\n"."\n",FILE_APPEND);
		if (!isset($request['order_id']) || !isset($request['items'])) {
			throw new BadRequestHttpException('Invalid data order_id or items');
//			throw new \yii\web\HttpException(404, 'The requested Item could not be found.');
//			$response = Yii::$app->getResponse();
//			$response->format = Response::FORMAT_JSON;
//			$response->data = [];
//
//			return $response;
		}


//		VarDumper::dump($request['order_id'],10,true);
//		VarDumper::dump($request['items'],10,true);
//		die;
		$orderId = $request['order_id'];
		$items = $request['items'];
        return $this->asJson(["response"=>[
//			"request"=>$request,
//			"data"=>$data,
			"status"=>"В работе",
			"order_id"=>$orderId,
			"items"=>$items,
		]]);
    }

    // /ecommerce/intermode/api/add-order

	/**
	 * @throws \yii\base\InvalidConfigException
	 * @throws BadRequestHttpException
	 */
	public function actionAddOrder()
    {
		$request = Yii::$app->getRequest()->getBodyParams();
		file_put_contents("actionAddOrder.log", date(DATE_ISO8601)."\n"."request:"."\n".print_r($request,true)."\n"."\n",FILE_APPEND);
		$os = new OutboundService();
		if ($os->isNotValidAddOrderData($request)) {
			throw new BadRequestHttpException('Invalid data order_id or items');
		}
		$order = $os->addOrder($os->requestToCreateDTO($request));
		$response = (new OutboundAPIMapper())->makeByItemAddOrderResponseDTO($os->getOrderInfo($order->id));
        return $this->asJson([
			"order_id"=>$order->order_number,
			"status"=>OutboundAPIStatus::_NEW,
			"items"=>$response->items,
		]);
    }

    /**
	 * @throws \yii\base\InvalidConfigException
	 * @throws BadRequestHttpException
	 */
	public function actionAddInboundOrder()
    {
		$request = Yii::$app->getRequest()->getBodyParams();
		file_put_contents("actionAddInboundOrder.log", date(DATE_ISO8601)."\n"."request:"."\n".print_r($request,true)."\n"."\n",FILE_APPEND);
		$os = new InboundService();
		if ($os->isNotValidAddOrderData($request)) {
			throw new BadRequestHttpException('Invalid data order_id or items');
		}

		$order = $os->addOrder($os->requestToCreateDTO($request));
		$response = (new InboundAPIMapper())->makeByItemAddOrderResponseDTO($os->getOrderInfo($order->id));
        return $this->asJson([
			"order_id"=>$order->order_number,
			"wms_id"=>$order->id,
			"status"=>InboundAPIStatus::_NEW,
			"items"=>$response->items,
		]);
    }

    /**
	 * @throws \yii\base\InvalidConfigException
	 * @throws BadRequestHttpException
	 */
	public function actionGetStockRemains()
    {
		$items = [];
		$ss = new StockService();
		$stocks = $ss->getStockRemains();
		foreach ($stocks as $stock) {
			$items[] = [
				"barcode"=> $stock["product_barcode"],
				"article"=> empty($stock["product_model"]) ? "" :  $stock["product_model"],
				"quantity"=> $stock["product_quantity"],
			];
		}

		return $this->asJson([
			"status"=>"success",
			"message"=>"",
			"code"=>"",
			"items"=>$items
		]);
    }
}