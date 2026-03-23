<?php

namespace app\modules\intermode\controllers\api\v1;

use app\modules\ecommerce\controllers\intermode\outbound\domain\mapper\OutboundAPIMapper;
use app\modules\intermode\controllers\api\v1\outbound\service\OutboundService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\VarDumper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\intermode\controllers\common\notify\b2b\NewOutboundOrderMsgDTO;
use app\modules\intermode\controllers\common\notify\b2b\TelegramIntermodeB2BNotification;

class OutboundController extends Controller
{
	public function init() {
		$this->enableCsrfValidation = false;
		$this->layout = "";
	}

    public function actionOrders()
    {
		$request = Yii::$app->getRequest()->getBodyParams();
		file_put_contents("OutboundController_actionOrders.log", date(DATE_ISO8601)."\n"."request:"."\n".print_r($request,true)."\n"."\n",FILE_APPEND);

		$is = new OutboundService();
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
		$order = $is->addOrder($is->requestToCreateDTO($request));
		$this->notifyByTg($order->id);
        return $this->asJson([
        	"status"=>"success",
        	"message"=>"",
        	"code"=>"",
        	"wms_id"=>$order->id,
		]);
    }

	private function notifyByTg($orderId) {
		$is = new OutboundService();
		$order = $is->getOrderByID($orderId);
		if (empty($order)) {
			return;
		}
		$clientStoreArray = TLHelper::getStoreArrayByClientID();
		$storeName  = \yii\helpers\ArrayHelper::getValue($clientStoreArray,$order->to_point_id);
		TelegramIntermodeB2BNotification::sendMessageIfNewOutboundOrder(
			new NewOutboundOrderMsgDTO($order->order_number,$order->expected_qty,$storeName,$order->description)
		);
	}
	
	public function actionReports()
	{
		$request = Yii::$app->getRequest()->getBodyParams();
		file_put_contents("OutboundController_actionReports.log", date(DATE_ISO8601)."\n"."request:"."\n".print_r($request,true)."\n"."\n",FILE_APPEND);

		$content = "";
		$i = 1;
		$content .= 'Клиент'.";";
		$content .= 'Родительский номер заказа'.";";
		$content .= 'Номер заказа'.";";
		$content .= 'Куда'.";";
		$content .= 'Объем (м3)'.";";
		$content .= 'Вес (кг)'.";";
		$content .= 'Отсканированное кол-во мест'.";";
		$content .= 'Предполагаемое кол-во'.";";
		$content .= 'Зарезервированое кол-во'.";";
		$content .= 'Отсканированное кол-во'.";";
		$content .= 'Дата создания заявки у клиента'.";";
		$content .= 'Дата регистрации заказа'.";";
		$content .= 'Дата упаковки'.";";
		$content .= 'Дата отгрузки'.";";
		$content .= 'Дата доставки'.";";
		$content .= 'Статус'.";";
		$content .= 'Статус груза'.";";
//		$content .= 'WMS'.";";
//		$content .= 'TR'.";";
//		$content .= 'FULL'.";";
		$content .= "\n";

		$searchModel = new \app\modules\intermode\controllers\api\v1\outbound\repository\OutboundOrderGridSearch();

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dps = $dataProvider->getModels();
		foreach ($dps as $model) {
			$i++;
			$title = '-';
			if ($to = $model->toPoint) {
				$title = $to->getPointTitleByPattern('{city_name_lat} {shopping_center_name_lat} / {city_name} {shopping_center_name}');
				if (empty($to->shopping_center_name_lat)) {
					$title = str_replace('/', '', $title);
				}
			}
			$clientTitle = '';
			if ($client = $model->client) {
				$clientTitle = $client->title;
			}

			$content .=  $clientTitle.";";
			$content .=  $model->parent_order_number.";";
			$content .=  $model->order_number.";";
			$content .=  $title.";";
			$content .=  $model->mc.";";
			$content .=  $model->kg.";";
			$content .=  $model->accepted_number_places_qty.";";
			$content .=  $model->expected_qty.";";
			$content .=  $model->allocated_qty.";";
			$content .=  $model->accepted_qty.";";
		$content .=
				!empty ($model->data_created_on_client) ? Yii::$app->formatter->asDatetime($model->data_created_on_client) : '-'.";";
			$content .=
				!empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : '-'.";";
			$content .=
				!empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date) : '-'.";";
			$content .=
				!empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse) : '-'.";";
			$content .=
				!empty ($model->date_delivered) ? Yii::$app->formatter->asDatetime($model->date_delivered) : '-'.";";
			$content .= $model->getStatusValue().";";
			$content .= $model->getCargoStatusValue().";";
//			$content .= $model->calculateWMS().";";
//			$content .= $model->calculateTR().";";
//			$content .= $model->calculateFULL().";";
			$content .= "\n";
		}
		return $this->asJson([
			"i"=>$i,
			"items"=>$content,
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
			'only' => ['orders','reports','echo'],

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