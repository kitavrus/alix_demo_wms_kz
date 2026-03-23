<?php

namespace app\modules\wms\controllers\erenRetail;

use stockDepartment\modules\wms\models\erenRetail\InboundDataMatrix;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
/////--------------------------------------------------------------------------
/// use stockDepartment\modules\wms\models\erenRetail\InboundDataMatrix;
use common\models\ActiveRecord;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundUploadLog;
use common\modules\kpiSettings\models\KpiSetting;
use common\modules\stock\models\Stock;
use common\modules\inbound\models\InboundOrderItem;

use common\overloads\ArrayHelper;
use stockDepartment\modules\inbound\models\LoadFromDeFactoAPIForm;
use common\modules\client\models\Client;

use stockDepartment\modules\wms\models\erenRetail\form\DatamatrixForm;
use stockDepartment\modules\wms\models\erenRetail\form\InboundForm;
use stockDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;


// scan-data-matrix-api/index
//class ScanDataMatrixApiController extends \yii\web\Controller
class ScanDataMatrixApiController extends \yii\rest\Controller
{
	public function init()
	{
		parent::init();
		Yii::$app->user->enableSession = false;
	}

	/**
	 * @inheritdoc
	 */
	protected function verbs()
	{
		return [
			'index' => ['GET', 'HEAD'],
			'get-in-process-inbound-orders-by-party-id' => ['POST', 'HEAD'],
		];
	}

    public function actionIndex()
    {
		Yii::$app->response->format = Response::FORMAT_JSON;

		$clientsArray = Client::getActiveWMSItems();
		$client_id = Client::CLIENT_ERENRETAIL;
		$partyNumberArray =  ArrayHelper::map(
			InboundOrder::find()
						->select('id, order_number')
						->where(['status'=>[
							Stock::STATUS_INBOUND_NEW,
							Stock::STATUS_INBOUND_SCANNING,
							Stock::STATUS_INBOUND_SCANNED
						],'client_id'=>$client_id])
						->andWhere(['deleted'=>ActiveRecord::NOT_SHOW_DELETED])
						->asArray()->all(),'id', function($data, $defaultValue) {
			return $data['order_number'];
		});
		return  [
			'clientsArray' => $clientsArray,
			'partyNumberArray' => $partyNumberArray,
		];
    }

	/*
	 * Get inbound orders in status new and in process by party
	 * @param integer client_id
	 * @return JSON
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


	/*
 * Get scanned products by inbound order id
 *
 * */
	public function actionGetScannedProductById()
	{
		$id = Yii::$app->request->post('inbound_id');
		Yii::$app->response->format = Response::FORMAT_JSON;
		$countScannedProductInOrder = InboundOrder::getCountItemByID($id);
		$io = InboundOrder::findOne($id);
//		$items = [];
//		if( $io = InboundOrder::findOne($id)) {
//			$items = $io->getOrderItems()->orderBy([
//				'accepted_qty'=>SORT_ASC
//			])->asArray()->all();
//		}


		return [
			'message' => 'Success',
			'countScannedProductInOrder' => $countScannedProductInOrder,
			'expected_qty' => $io->expected_qty,
//			'items' =>$this->renderPartial('_order_items',['items'=>$items]),
		];
	}

	/*
* Scanned product in box
* @return JSON true or errors array
* */
	public function actionScanProductInBox()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$expected_qty = 0;
		$model = new DatamatrixForm();
		$model->scenario = 'ScannedProduct';

//        VarDumper::dump(Yii::$app->request->post(),10,true);
		$post = Yii::$app->request->post();
		if ($model->load($post) && $model->validate()) {
			$idm = InboundDataMatrix::find()
									->andWhere([
										'inbound_id' => $model->order_number,
										'product_barcode' => $model->product_barcode,
										'print_status' => InboundDataMatrix::PRINT_STATUS_NO,
									])
									->one();
			$idm->print_status = InboundDataMatrix::PRINT_STATUS_YES;
			$idm->save(false);
			$colorRowClass = 'alert-danger';

			return [
				'success' => (empty($errors) ? '1' : '0'),
				'countProductInBox'=>0,
				'countScannedProductInOrder'=>0,
				'expectedQtyParty'=>0,
				'acceptedQtyParty'=>0,
				'expected_qty'=> $expected_qty,
//                'xxxxx'=> $countStockForItem,
				'dataScannedProductByBarcode'=> [
					'rowId'=>'0'.'-'.$model->product_barcode,
					'expected_qty'=> 0,
					'countValue'=> 0,
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
}