<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\defacto\api\ECommerceAPINew;
use common\ecommerce\defacto\outbound\service\OutboundService;
use common\ecommerce\entities\EcommerceOutboundItem;
use Yii;
use stockDepartment\components\Controller;
use yii\data\ArrayDataProvider;
use yii\helpers\VarDumper;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;

class InboundApiController extends Controller
{
	public function actionIndex()
	{
		$api = new ECommerceAPINew();
		$params['request'] = [
			'BusinessUnitId' => $api->BUSINESS_UNIT_ID(),
		];

		$appointmentsResult = $api->GetAppointmentsV2($params);
		$items = [];
		foreach ($appointmentsResult["response"]->GetAppointmentsV2Result->Data->GetAppointmentResponse as $key => $value) {
			$items[] = [
				"AppointmentBarcode" => $value->AppointmentBarcode,
				"AppointmentDate" => $value->AppointmentDate,
			];
		}
		$provider = new ArrayDataProvider([
			'allModels' => $items,
//      'sort' => [
//          'attributes' => ['id', 'username', 'email'],
//      ],
			'pagination' => false,
		]);
		return $this->render('index', [
			"provider" => $provider
		]);
	}

	public function actionSendReadAppointments($AppointmentBarcode)
	{
		// /ecommerce/defacto/inbound-api/send-read-appointments
		$api = new ECommerceAPINew();
		$params['request'] = [
			'BusinessUnitId' => $api->BUSINESS_UNIT_ID(),
			'AppointmentBarcode' => $AppointmentBarcode,
		];
		$result = [];
		$result['errors'] = [1];
		$result = $api->SendReadAppointments($params);
		if (empty($result['errors'])) {
			Yii::$app->session->setFlash('success', "Успешно подтвердили получение <b>[ " . $AppointmentBarcode . " ]</b>");
		} else {
			Yii::$app->session->setFlash('danger', "Ошибка api defacto <b>[ " . $AppointmentBarcode . " ]</b>");
		}
		return $this->redirect(['index']);
	}

	public function actionTest()
	{
		// /ecommerce/defacto/inbound-api/test
//		$api = new ECommerceAPINew();
		$s = new \common\ecommerce\defacto\outbound\service\OutboundService();
		$repository = new OutboundRepository();
		$api = new \common\ecommerce\defacto\outbound\service\OutboundAPIService();
		$ourOutboundId = "64530";
		$orderInfo = $repository->getOrderInfo($ourOutboundId);
		$apiResponse = $api->PartialCancellationRequestExistsByCustomer($ourOutboundId,$orderInfo->order->order_number);
		VarDumper::dump($apiResponse,10,true);
// start service

		if($apiResponse['HasError'] != false){
			return "--HasError = true--";
		}


		if(empty($apiResponse['Data'][0])) {
			return "--empty--";
		}

		$totalCancelQty = 0;
		$cancelItems = $apiResponse['Data'][0]->CancellationRequestDetail->CancellationRequestDetail;
		foreach ($cancelItems as $itemCancel) {
			$totalCancelQty +=  $itemCancel->Quantity;
		}

		if ($orderInfo->order->expected_qty == $totalCancelQty) {
			// Full cancel
			foreach ($cancelItems as $itemCancel) {
				$oItem = EcommerceOutboundItem::find()
											  ->andWhere(["outbound_id" => $ourOutboundId, "product_sku" => $itemCancel->Sku])
											  ->one();
				if ($oItem) {
					$oItem->cancel_qty = $itemCancel->Quantity;
					$oItem->save(false);
				}
			}
			//$this->CancelShipment($ourOutboundId,OutboundCancelStatus::CUSTOMER_REQUESTS_CANCELLATION);
			return "Full cancel";
		} else {
			// резервируем то что осталось после отмены
			foreach ($cancelItems as $itemCancel) {
				$oItem = EcommerceOutboundItem::find()
											  ->andWhere(["outbound_id"=>$ourOutboundId,"product_sku"=>$itemCancel->Sku])
											  ->one();
				if ($oItem) {
					$resultQty = $oItem->expected_qty  - $itemCancel->Quantity;
					$oItem->expected_qty = $resultQty;
					$oItem->cancel_qty = $itemCancel->Quantity;
					if ($resultQty == 0) {
						$oItem->deleted = 1;
					}
					$oItem->save(false);
				}
			}
		}


// end service
//			$result->items
//		}
		//$s->PartialCancellationRequestExistsByCustomer($ourOutboundId);
		VarDumper::dump($apiResponse['Data'][0]->CancellationRequestDetail->CancellationRequestDetail,10,true);
		//VarDumper::dump($apiResponse,10,true);
		die;

		//$s->PartialCancellationRequestExistsByCustomer($ourOutboundId);
		//VarDumper::dump($apiResponse['Data'],10,true);
		//VarDumper::dump($apiResponse,10,true);
		//die;
//		$params['request'] = [
//			'BusinessUnitId' => $api->BUSINESS_UNIT_ID(),
//			'AppointmentBarcode' => $AppointmentBarcode,
//		];
//		$result = [];
//		$result['errors'] = [1];
//		$result = $api->SendReadAppointments($params);
//		if (empty($result['errors'])) {
//			Yii::$app->session->setFlash('success', "Успешно подтвердили получение <b>[ " . $AppointmentBarcode . " ]</b>");
//		} else {
//			Yii::$app->session->setFlash('danger', "Ошибка api defacto <b>[ " . $AppointmentBarcode . " ]</b>");
//		}
		//return $this->redirect(['index']);
	}
}