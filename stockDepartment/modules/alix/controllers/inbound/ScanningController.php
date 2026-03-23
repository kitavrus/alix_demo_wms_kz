<?php

namespace stockDepartment\modules\intermode\controllers\inbound;


use common\models\ActiveRecord;
use common\modules\stock\models\Stock;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\store\models\Store;
use common\overloads\ArrayHelper;
use common\modules\client\models\Client;
use common\modules\inbound\models\InboundOrder;

use Yii;
use stockDepartment\modules\intermode\controllers\inbound\domain\InboundForm;
use stockDepartment\modules\intermode\controllers\inbound\domain\InboundScanningService;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;


class ScanningController extends Controller
{
	public function actionIndex()
	{
		$inboundForm = new InboundForm();
		$clientsArray = Client::getActiveWMSItems();
		$client_id = \common\modules\client\models\Client::CLIENT_ERENRETAIL;
		$partyNumberArray =  ArrayHelper::map(
			InboundOrder::find()
						->select('id, order_number,from_point_id ')
						->where(['status'=>[
							Stock::STATUS_INBOUND_NEW,
							Stock::STATUS_INBOUND_SCANNING,
							Stock::STATUS_INBOUND_SCANNED
						],'client_id'=>$client_id])
						->andWhere(['deleted'=>ActiveRecord::NOT_SHOW_DELETED])
						->asArray()->all(),'id', function($data, $defaultValue) {
				$title = $data['order_number'];
				if (!empty($data['from_point_id'])) {
					$store = Store::findOne($data['from_point_id']);
					if ($store) {
						$title .= " / ". $store->getPointTitleByPattern();
					}
				}

			return $title;
		});

		$inboundForm->client_id = $client_id;
		return $this->render('index', [
			'inboundForm' => $inboundForm,
			'clientsArray' => $clientsArray,
			'partyNumberArray' => $partyNumberArray,
		]);
	}

	/**
	 * Get inbound orders in status new and in process by client
	 * @param integer client_id
	 * @return array
	 * */
	public function actionGetInProcessInboundOrdersByClientId()
	{
		$clientID = 103;
//		if($cio =  ConsignmentInboundOrders::getNewAndInProcessItemByClientID($clientID)) {
//			$data += $cio;
//			$type = 'party-inbound';
//		} else {
			$data = InboundOrder::getNewAndInProcessItemByClientID($clientID);
			$type = 'inbound';
//		}

		Yii::$app->response->format = Response::FORMAT_JSON;
		return [
			'message' => 'Success',
			'type' => $type,
			'dataOptions' => $data,
		];
	}

	/**
	* Get inbound orders in status new and in process by party
	* @param integer client_id
	* @return array
	* */
	public function actionGetInProcessInboundOrdersByPartyId()
	{
		$expectedQtyParty = 0;
		$acceptedQtyParty = 0;

		$party_id = Yii::$app->request->post('party_id');

		$data = ['' => ''];
		$data +=  \yii\helpers\ArrayHelper::map(
			InboundOrder::find()
						->select('id, order_number')
						->where(['status'=>[
							Stock::STATUS_INBOUND_NEW,
							Stock::STATUS_INBOUND_SCANNING,
							Stock::STATUS_INBOUND_SCANNED
						],'id'=>$party_id])
						->andWhere(['deleted'=>ActiveRecord::NOT_SHOW_DELETED])
						->asArray()->all(),
			'id',
			'order_number');;

		if($cio  = InboundOrder::findOne($party_id)) {
			$expectedQtyParty = intval($cio->expected_qty);
			$acceptedQtyParty = intval($cio->accepted_qty);
		}


		Yii::$app->response->format = Response::FORMAT_JSON;
		return [
			'message' => 'Success',
			'dataOptions' => $data,
			'expectedQtyParty'=>$expectedQtyParty,
			'acceptedQtyParty'=>$acceptedQtyParty,
		];
	}

	/**
	 * Get inbound order in status complete by client
	 * @param integer client_id
	 * @return array
	 * */
	public function actionGetCompleteInboundOrdersByClientId()
	{
		$clientID = Yii::$app->request->post('client_id');
		Yii::$app->response->format = Response::FORMAT_JSON;
		$data = ['' => ''];
		$data += InboundOrder::getCompleteOrderByClientID($clientID);
		return [
			'message' => 'Success',
			'dataOptions' => $data,
		];
	}

	/**
	 * Get scanned products by inbound order id
	 * */
	public function actionGetScannedProductById()
	{
		$id = Yii::$app->request->post('inbound_id');
		Yii::$app->response->format = Response::FORMAT_JSON;
		$countScannedProductInOrder = InboundOrder::getCountItemByID($id);
		$items = [];
		if( $io = InboundOrder::findOne($id)) {
			$items = $io->getOrderItems()->orderBy([
				'accepted_qty'=>SORT_ASC
			])->asArray()->all();
		}

		return [
			'message' => 'Success',
			'countScannedProductInOrder' => $countScannedProductInOrder,
			'expected_qty' => $io->expected_qty,
			'items' =>$this->renderPartial('_order_items',['items'=>$items]),
		];
	}

	/**
	* Validate scanned box
	* @return array true or errors array
	* */
	public function actionValidateScannedBox()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$model = new InboundForm();
        $model->scenario = 'ScannedBox';
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {

			return [
				'success' => '1',
				'countProductInBox'=>InboundOrderItem::getScannedProductInBox($model->box_barcode,$model->order_number),
			];
		} else {
			$errors = ActiveForm::validate($model);
			return [
				'success'=>(empty($errors) ? '1' : '0'),
				'errors' => $errors
			];
		}
	}

	/**
	* Scanned product in box
	* @return array true or errors array
	* */
	public function actionScanProductInBox()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$expected_qty = 0;
		$model = new InboundForm();
		$model->scenario = 'ScannedProduct';
		$post = Yii::$app->request->post();
		//if ($post['InboundForm']['order_number'] == 116126) {
		//	$post['InboundForm']['product_barcode'] = ltrim($post['InboundForm']['product_barcode'],"0");
		//}
		$stockId = -1;
		if ($model->load($post) && $model->validate()) {

			$stock = $model->setScannedStatus();
			(new InboundScanningService())->sendStatusInWork($model->order_number);

			$ioi = InboundOrderItem::find()
								   ->andWhere(['inbound_order_id' => $model->order_number,
				'product_barcode' => $model->product_barcode,
			])->one();

//			$stock = Stock::setStatusInboundScannedValue($model->order_number,
//				$model->product_barcode,
//				$model->box_barcode,
//				$ioi->product_model,
//				$ioi->product_name
//			);


			$stockId = $stock->id;

			$countStockForItem =  Stock::find()->andWhere([
				'inbound_order_id' => $model->order_number,
				'product_barcode' => $model->product_barcode,
				'status' => Stock::STATUS_INBOUND_SCANNED,
				'client_id' => $model->client_id,
			])->count();

			if ( $ioi) {
				if(intval($ioi->accepted_qty) < 1) {
					$ioi->begin_datetime = time();
					$ioi->status = Stock::STATUS_INBOUND_SCANNING;
				}

				$ioi->accepted_qty = $countStockForItem;

				if($ioi->accepted_qty == $ioi->expected_qty) {
					$ioi->status = Stock::STATUS_INBOUND_SCANNED;
				}

				$ioi->end_datetime = time();
				$ioi->save(false);
			} else {}

			$countStockForOrder =  Stock::find()->where([
				'inbound_order_id' => $model->order_number,
				'status' => Stock::STATUS_INBOUND_SCANNED,
				'client_id' => $model->client_id,
			])->count();

			if($inboundModel = InboundOrder::findOne($model->order_number)) {

				if(intval($inboundModel->accepted_qty) < 1) {
					$inboundModel->begin_datetime = time();
					$inboundModel->status = Stock::STATUS_INBOUND_SCANNING;
				}

				$inboundModel->accepted_qty = $countStockForOrder;

				if( $inboundModel->accepted_qty == $inboundModel->expected_qty) {
					$inboundModel->status = Stock::STATUS_INBOUND_SCANNED;
				}

				$inboundModel->end_datetime = time();
				$inboundModel->save(false);

				$expected_qty = $inboundModel->expected_qty;
			}

			//S: PARTY
			$expectedQtyParty = 0;
			$acceptedQtyParty = 0;
//			if($coi = ConsignmentInboundOrders::findOne($inboundModel->consignment_inbound_order_id)) {
//
//				$inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inboundModel->consignment_inbound_order_id])->asArray()->column();
//
//				$countStockForConsignment =  Stock::find()->where([
//					'inbound_order_id' => $inboundIDs,
//					'status' => Stock::STATUS_INBOUND_SCANNED,
//					'client_id' => $model->client_id,
//				])->count();
//
//				$coi->accepted_qty = $countStockForConsignment;
//				$coi->save(false);
//				$expectedQtyParty = $coi->expected_qty;
//				$acceptedQtyParty = $coi->accepted_qty;
//			}
			//E: PARTY


			$colorRowClass = 'alert-danger';
			if( $ioi->accepted_qty == $ioi->expected_qty) {
				$colorRowClass = 'alert-success';
			}elseif($ioi->accepted_qty > 0) {
				$colorRowClass = 'alert-warning';
			}

			return [
				'success' => (empty($errors) ? '1' : '0'),
				'countProductInBox'=>InboundOrderItem::getScannedProductInBox($model->box_barcode,$model->order_number),
				'countScannedProductInOrder'=>InboundOrder::getCountItemByID($model->order_number),
				'expectedQtyParty'=>$expectedQtyParty,
				'acceptedQtyParty'=>$acceptedQtyParty,
				'expected_qty'=> $expected_qty,
				'stockId'=> $stockId,
				'dataScannedProductByBarcode'=> [
					'rowId'=>$ioi->id.'-'.$model->product_barcode,
					'expected_qty'=> $ioi->expected_qty,
					'countValue'=> $ioi->accepted_qty,
					'colorRowClass'=> $colorRowClass
				],
			];
		} else {
			$errors = ActiveForm::validate($model);
			return [
				'success' => (empty($errors) ? '1' : '0'),
				'errors' => $errors
			];
		}
	}

	/**
	 * Print the list of differences
	 * */
	public function actionPrintListDifferences()
	{
		$id = Yii::$app->request->get('inbound_id');
		$items = [];
		$orderNumber = '';
		$fromPointId = '';
		$expectedQtyCount = '';
		$acceptedQtyCount = '';
		if ($io = InboundOrder::findOne($id)) {
			$orderNumber = $io->order_number;
			$fromPointId = $io->from_point_id;
			$expectedQtyCount += $io->expected_qty;
			$acceptedQtyCount += $io->accepted_qty;

			$items = $io->getOrderItems()
				->orderBy([
					'accepted_qty' => SORT_ASC
				])
				->asArray()
				->all();
		}

		$store = Store::findOne($fromPointId);

		if ($this->printType == 'html') {
			Yii::$app->layout = 'print-html';
			return $this->render('print/list-differences-html', ['items' => $items]);
		}
		return $this->render('print/list-differences-pdf', [
			'orderNumber' => $orderNumber,
			'items' => $items,
			'store' => $store,
			'expectedQtyCount' => $expectedQtyCount,
			'acceptedQtyCount' => $acceptedQtyCount
		]);
	}

	/**
	* Confirm inbound order data
	* @return array true or errors array
	* */
	public function actionConfirmOrder()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = [];

		$model = new InboundForm();
		$model->scenario = 'ConfirmOrder';

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if($io = InboundOrder::findOne($model->order_number)) {

				if($io->status == Stock::STATUS_INBOUND_CONFIRM) {
					$messages [] = Yii::t('inbound/errors','Накладная с номером ' . $io->order_number . ' уже принята');
				} else {
					$io->status = Stock::STATUS_INBOUND_CONFIRM;
					$io->date_confirm = time();
					$io->save(false);

					Stock::updateAll([
						'status'=>Stock::STATUS_INBOUND_CONFIRM,
						'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
					],[
						'inbound_order_id'=>$io->id,
						'status'=>[
							Stock::STATUS_INBOUND_SCANNED,
							Stock::STATUS_INBOUND_OVER_SCANNED,
						]
					]);


					Stock::deleteAll('inbound_order_id = :inbound_order_id AND status != :status',[':inbound_order_id'=>$io->id,':status'=>Stock::STATUS_INBOUND_CONFIRM]);

					$messages [] =  Yii::t('inbound/errors','Накладная с номером ' . $io->order_number . ' успешно принята');
					(new InboundScanningService())->sendStatusCompleted($io->id);

				}
			} else {
				// TODO сделать уведомление на почту
			}
		} else {
			$errors = ActiveForm::validate($model); //TODO Нет обработчика на стороне клиента, т.е. ошибки не выводятся
		}

		return [
			'success'=>'OK',
			'errors'=>$errors,
			'messages'=>$messages,
		];
	}

	/**
	 * Delete product by barcode  in box
	 * @return array true or errors array
	 * */
	public function actionClearProductInBox()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = [];
		$countProductInBox = 0;
		$countValue = 0;
		$colorRowClass = '';
		$rowId = '';
		$expected_qty = 0;
		//S: PARTY
		$expectedQtyParty = 0;
		$acceptedQtyParty = 0;
		//E: PARTY

		$model = new InboundForm();
		$model->scenario = 'ClearProductInBox';

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {

//			VarDumper::dump($model,10,true);
//			die;
			Stock::deleteAll(                              [
				'primary_address'=>$model->box_barcode,
				'product_barcode'=>$model->product_barcode,
				'inbound_order_id'=>$model->order_number,
				'status'=>[
					Stock::STATUS_INBOUND_SCANNED,
					Stock::STATUS_INBOUND_OVER_SCANNED
				]
			]);

			$countStockForItem =  Stock::find()->where([
				'inbound_order_id' => $model->order_number,
				'product_barcode' => $model->product_barcode,
				'status' => Stock::STATUS_INBOUND_SCANNED,
			])->count();

			if($ioi =  InboundOrderItem::findOne(['product_barcode'=>$model->product_barcode,'inbound_order_id'=>$model->order_number])) {

				$ioi->accepted_qty = $countStockForItem;
				$ioi->status = Stock::STATUS_INBOUND_SCANNING;
				$ioi->save(false);

				$colorRowClass = 'alert-danger';
				if( $ioi->accepted_qty == $ioi->expected_qty) {
					$colorRowClass = 'alert-success';
				}elseif($ioi->accepted_qty > 0) {
					$colorRowClass = 'alert-warning';
				}

				$countValue = $ioi->accepted_qty;
				$rowId = $ioi->id.'-'.$model->product_barcode;
			};

			$countStockForOrder =  Stock::find()->where([
				'inbound_order_id' => $model->order_number,
				'status' => Stock::STATUS_INBOUND_SCANNED,
			])->count();

			if($inbound = InboundOrder::findOne($model->order_number)) {
				$inbound->status = Stock::STATUS_INBOUND_SCANNING;
//                   $inbound->accepted_qty -= 1;
				$inbound->accepted_qty = $countStockForOrder;
				$inbound->save(false);

				$expected_qty = $inbound->expected_qty;

				$expectedQtyParty = $inbound->expected_qty;
				$acceptedQtyParty = $inbound->accepted_qty;
//                    }
				//E: PARTY
			}

		} else {
			$errors = ActiveForm::validate($model);
		}

		return [
			'success'=>(empty($errors) ? '1' : '0'),
			'errors'=>$errors,
			'messages'=>$messages,
			'countProductInBox'=>InboundOrderItem::getScannedProductInBox($model->box_barcode,$model->order_number),
			'countScannedProductInOrder'=>InboundOrder::getCountItemByID($model->order_number),
			'expectedQtyParty'=>$expectedQtyParty,
			'acceptedQtyParty'=>$acceptedQtyParty,
			'expected_qty'=> $expected_qty,
			'dataScannedProductByBarcode'=> [
				'rowId'=>$rowId,
				'countValue'=> $countValue,
				'colorRowClass'=> $colorRowClass
			],
		];
	}

	/**
	 * Clear all product in box
	 * @param string $box_barcode Box barcode
	 * */
	public function actionClearBox()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = [];
		$dataScannedProductByBarcode = [];
		$expected_qty = 0;
		//S: PARTY
		$expectedQtyParty = 0;
		$acceptedQtyParty = 0;
		//E: PARTY

		$model = new InboundForm();
		$model->scenario = 'ClearBox';

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if($productsInBox = Stock::find()
									 ->select('count(product_barcode) as product_barcode_count, product_barcode')
									 ->where([
										 'primary_address'=>$model->box_barcode,
										 'inbound_order_id'=>$model->order_number,
										 'status'=>[
											 Stock::STATUS_INBOUND_SCANNED,
											 Stock::STATUS_INBOUND_OVER_SCANNED
										 ]])
									 ->groupBy('product_barcode')
									 ->all()
			) {

				foreach($productsInBox as $item) {

					if ($ioi = InboundOrderItem::findOne([
						'product_barcode' => $item->product_barcode,
						'inbound_order_id' => $model->order_number,
					])) {

						Stock::deleteAll(
							[
								'primary_address'=>$model->box_barcode,
								'inbound_order_id'=>$model->order_number,
								'product_barcode'=>$item->product_barcode,
								'status'=>[
									Stock::STATUS_INBOUND_SCANNED,
									Stock::STATUS_INBOUND_OVER_SCANNED
								]
							]);

						$countStockForItem =  Stock::find()->where([
							'inbound_order_id' => $model->order_number,
							'product_barcode' => $item->product_barcode,
							'status' => Stock::STATUS_INBOUND_SCANNED,
						])->count();

						$ioi->accepted_qty = $countStockForItem;
						$ioi->save(false);

						$colorRowClass = 'alert-danger';
						if ($ioi->accepted_qty == $ioi->expected_qty) {
							$colorRowClass = 'alert-success';
						} elseif ($ioi->accepted_qty >0) {
							$colorRowClass = 'alert-warning';
						}

						$countValue = $ioi->accepted_qty;
						$rowId = $ioi->id . '-' . $item->product_barcode;

						$dataScannedProductByBarcode [] = [
							'rowId' => $rowId,
							'countValue' => $countValue,
							'colorRowClass' => $colorRowClass
						];
					};
				}

				$countStockForOrder =  Stock::find()->where([
					'inbound_order_id' => $model->order_number,
					'status' => Stock::STATUS_INBOUND_SCANNED,
				])->count();


				if($inbound = InboundOrder::findOne($model->order_number)) {
					$inbound->status = Stock::STATUS_INBOUND_SCANNING;
					$inbound->accepted_qty = $countStockForOrder;
					$inbound->save(false);

					$expected_qty = $inbound->expected_qty;

					//S: PARTY
//					if($coi = ConsignmentInboundOrders::findOne($inbound->consignment_inbound_order_id)) {
//
//						$inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inbound->consignment_inbound_order_id])->asArray()->column();
//
//						$countStockForConsignment =  Stock::find()->where([
//							'inbound_order_id' => $inboundIDs,
//							'status' => Stock::STATUS_INBOUND_SCANNED,
//						])->count();
//
//						$coi->accepted_qty = $countStockForConsignment;
//
//
//						$coi->save(false);
//
//						$expectedQtyParty = $coi->expected_qty;
//						$acceptedQtyParty = $coi->accepted_qty;
//					}
					//E: PARTY
				}
			}

		} else {
			$errors = ActiveForm::validate($model);
		}

		return [
			'success'=>(empty($errors) ? '1' : '0'),
			'errors'=>$errors,
			'messages'=>$messages,
			'countScannedProductInOrder'=>InboundOrder::getCountItemByID($model->order_number),
			'expected_qty'=> $expected_qty,
			'dataScannedProductByBarcode'=> $dataScannedProductByBarcode,
			'expectedQtyParty'=>$expectedQtyParty,
			'acceptedQtyParty'=>$acceptedQtyParty,
		];
	}

	/**
	 *
	 * */
	public function actionPrintUnallocatedList()
	{
		$id = Yii::$app->request->get('inbound_id');

		$items = [];
		if($io = InboundOrder::findOne($id)) {
			$items = Stock::find()
						  ->select('primary_address, secondary_address')
						  ->where([
							  'inbound_order_id' => $io->id,
							  'secondary_address' => '',
						  ])
						  ->andWhere([
							  'not', ['primary_address'=>'']
						  ])
						  ->groupBy('primary_address')
						  ->orderBy([
							  'secondary_address' => SORT_DESC,
							  'primary_address' => SORT_DESC,
						  ])
						  ->asArray()
						  ->all();

		}
		if($this->printType == 'html'){
			Yii::$app->layout = 'print-html';
			return $this->render('print/print-unallocated-box-html',['items'=>$items]);
		}
		return $this->render('print/print-unallocated-box-pdf',['items'=>$items]);
	}

	/**
	 *
	 * */
	public function actionScanDatamatrix()
	{
		// scan-datamatrix
		Yii::$app->response->format = Response::FORMAT_JSON;

		$inboundForm = new InboundForm();
		$inboundForm->setScenario(InboundForm::SCENARIO_SCAN_DATAMATRIX);

		if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
			$inboundOrderService = new InboundScanningService($inboundForm->getDTO());
			$inboundOrderService->scanDataMatrix();

			return [
				'success' => '1',
			];
		}
		$errors = ActiveForm::validate($inboundForm);
		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors
		];
	}

}