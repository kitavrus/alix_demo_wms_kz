<?php

namespace app\modules\intermode\controllers\outbound;

use app\modules\intermode\controllers\outbound\domain\OutboundService;
use common\components\BarcodeManager;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use app\modules\intermode\controllers\outbound\domain\ScanningForm;
use Yii;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use yii\db\Query;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\helpers\DateHelper;
use app\modules\intermode\controllers\stock\domain\StockService;
use stockDepartment\modules\intermode\controllers\product\domains\ProductService;


class ScanningController extends Controller
{
    /**
    * Begin scanning form
    *
    * */
    public function actionScanningForm()
    {
        return $this->render('scanning-form', ['model' => new ScanningForm()]);
    }

    public function actionEmployeeBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';

        $model = new ScanningForm();
        $model->scenario = 'IsEmployeeBarcode';

        if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }

    /**
    * Scanning form handler Is Picking List Barcode
    * */
    public function actionPickingListBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $plIds = '';
        $messages = '';
        $stockArrayByPL = [];
        $orderData = [];

        $model = new ScanningForm();
        $model->scenario = 'IsPickingListBarcode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $plIds = $model->picking_list_barcode_scanned;

            $qIDs = [];
            if ($opl = OutboundPickingLists::findOne(['barcode' => $model->picking_list_barcode, 'status' => OutboundPickingLists::STATUS_END])) {

                //S: TODO подумать как это сделать правильно
                $plIds = (empty($model->picking_list_barcode_scanned) ? '' : $model->picking_list_barcode_scanned . ',') . $opl->id;
                $plIds = OutboundPickingLists::prepareIDsHelper($plIds, true);
                $qIDs = OutboundPickingLists::prepareIDsHelper($plIds);
                //E: TODO подумать как это сделать правильно
            }

            $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);

            $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'plIds' => $plIds,
            'exp_qty' => isset($orderData['allocated_qty']) ? $orderData['allocated_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
    }

    /**
     * Scanning form handler Is Box Barcode
     * */
    public function actionBoxBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';
        $countInBox = 0;
        $stockArrayByPL = [];

        $model = new ScanningForm();
        $model->scenario = 'IsBoxBarcode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);
			$oID = OutboundPickingLists::getOutboundOrderIdByPickingLists($qIDs);
            $countInBox = OutboundPickingLists::getCountInBoxByOutboundOrderByOrderId($model->box_barcode, $oID);
            $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countInBox' => $countInBox,
            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
    }

    /**
     * Scanning form handler Is Product Barcode
     * */
    public function actionProductBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;


        $errors = [];
        $messages = '';
        $countInBox = '0';
        $stockArrayByPL = [];
        $orderData = [];

        $model = new ScanningForm();
        $model->scenario = 'IsProductBarcode';
		$post = Yii::$app->request->post();
			$post['ScanningForm']['product_barcode'] = ltrim($post['ScanningForm']['product_barcode'],"0");
//		}
        if ($model->load($post) && $model->validate()) {
            //TODO Найти товар по ШК в products ?
            $qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);

			$ooIDs = OutboundPickingLists::find()
										 ->select('outbound_order_id')
										 ->andWhere(['id'=>$qIDs])
										 ->limit(1)
										 ->scalar();

            // TODO Добавь проверку чбо был один заказ

            // Box barcode
            $dirPath = 'log/outbound/'.date('Ymd');
            BaseFileHelper::createDirectory($dirPath);

            file_put_contents($dirPath.'/actionProductBarcodeScanningHandler.log',$model->employee_barcode.' ; '.$model->box_barcode.' ; '.$model->product_barcode.' ; '.$model->picking_list_barcode_scanned.' ; '.date('Ymd-H:i:s')."\n",FILE_APPEND);
            file_put_contents($dirPath.'/actionProductBarcodeScanningHandler-full.log',date('Ymd-H:i:s')."\n"."\n",FILE_APPEND);
            file_put_contents($dirPath.'/actionProductBarcodeScanningHandler-full.log',print_r($model,true)."\n"."\n",FILE_APPEND);


            if(BarcodeManager::isM3BoxBorder($model->product_barcode)) {
                $m3BoxValue = BarcodeManager::getBoxM3($model->product_barcode);

                if(empty($m3BoxValue)) {
                    $m3BoxValue = 0.096;
                }

                Stock::updateAll([
                    'box_size_m3'=>$m3BoxValue,
                    'box_size_barcode'=>$model->product_barcode,
                ],[
                    'box_barcode'=>$model->box_barcode,
                    'outbound_order_id' => $ooIDs,
                    'status' => Stock::STATUS_OUTBOUND_SCANNED,
                ]);

                return [
                    'change_box'=>'ok'
                ];

            } else {

            }


			$productService = new ProductService();
			$productID = $productService->getProductIdByBarcode($model->product_barcode);
            if ($stock = Stock::find()->andWhere([
                'status' => [
                        Stock::STATUS_OUTBOUND_PICKED,
                        Stock::STATUS_OUTBOUND_SCANNING
                ],
                //'product_barcode' => $model->product_barcode,
				'product_id' => $productID,				
//                'outbound_picking_list_id' => $qIDs,
				'outbound_order_id' => $ooIDs,
            ])->one()) {

                $stock->status = Stock::STATUS_OUTBOUND_SCANNED;
                $stock->box_barcode = $model->box_barcode;
                $stock->save(false);

                $countStockForOrderItem = Stock::find()
											   ->andWhere([
											   	'status'=>Stock::STATUS_OUTBOUND_SCANNED,
												   // 'product_barcode'=>$model->product_barcode,
												   'product_id' => $productID,
//												   'outbound_picking_list_id' => $qIDs,
												   'outbound_order_id' => $ooIDs,
											   ])->count();

                if ($ioi = OutboundOrderItem::find()
											->andWhere([
													'outbound_order_id' => $ooIDs,
                    								// 'product_barcode' => $model->product_barcode,
													'product_id' => $productID,
                							])->one()
                ) {

                    if (intval($ioi->accepted_qty) < 1) {
                        $ioi->begin_datetime = time();
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                    }

//                    $ioi->accepted_qty += 1;
                    $ioi->accepted_qty = $countStockForOrderItem;

                    if ($ioi->accepted_qty == $ioi->expected_qty || $ioi->accepted_qty == $ioi->allocated_qty ) {
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNED;
                    }

                    $ioi->end_datetime = time();
                    $ioi->save(false);

                }

//                OutboundOrder::updateAllCounters(['accepted_qty' =>1], ['id' => $stock->outbound_order_id]);
                // TODO убрать этот говно код, по свободе сделать миграуию и все полям которые integer значение по умолчанию постать 0
               $oModel = OutboundOrder::findOne($ooIDs);

                $countStockForOrder = Stock::find()
										   ->andWhere([
										   		'status'=>Stock::STATUS_OUTBOUND_SCANNED,
											   'outbound_order_id'=>$ooIDs
										   ])
										   ->count();

                if(intval($oModel->accepted_qty) < 1) {
                    $oModel->begin_datetime = time();
                    $oModel->status = Stock::STATUS_OUTBOUND_SCANNING;
                }

//                $oModel->accepted_qty +=1;
                $oModel->accepted_qty = $countStockForOrder;

                if ($oModel->accepted_qty == $oModel->expected_qty || $oModel->accepted_qty == $oModel->allocated_qty ) {
                    $oModel->status = Stock::STATUS_OUTBOUND_SCANNED;
                }

                $oModel->end_datetime = time();
                $oModel->save(false);

            } else {
                $model->addError('product_barcode',Yii::t('outbound/errors',' Вы отсканировали [ <b>'.$model->product_barcode.'</b> ] лишний товар в короб '.' [<b> '.$model->box_barcode.' </b>]'));
                $errors = $model->getErrors();
//                $errors = ActiveForm::validate($model);
            }

//            $countInBox = OutboundPickingLists::getCountInBoxByPickingList($model->box_barcode, OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned));
//            $countInBox = OutboundPickingLists::getCountInBoxByOutboundOrder($model->box_barcode, $ooIDs);
//            $countInBox = OutboundPickingLists::getCountInBoxByOutboundOrder($model->box_barcode, $qIDs);
            $countInBox = OutboundPickingLists::getCountInBoxByOutboundOrderId($model->box_barcode, $ooIDs);
            //$stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);
//            $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);
            $orderData = OutboundPickingLists::getAccExpByPickingListInOrderId($ooIDs);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'change_box' => 'no',
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countInBox' => $countInBox,
            'exp_qty' => isset($orderData['allocated_qty']) ? $orderData['allocated_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
    * Delete product by barcode  in box
    * @return JSON true or errors array
    * */
    public function actionClearProductInBoxByOne()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $stockArrayByPL = [];
        $countInBox = '0';
        $orderData = [];

        $model = new ScanningForm();
        $model->scenario = 'ClearProductInBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {


            $qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);


            if ($stock = Stock::findOne(['box_barcode' => $model->box_barcode,
                'product_barcode' => $model->product_barcode,
                'outbound_picking_list_id' => $qIDs,
                'status' => Stock::STATUS_OUTBOUND_SCANNED
            ])
            ) {

//                $stock->status = Stock::STATUS_OUTBOUND_SCANNING;
                $stock->status = Stock::STATUS_OUTBOUND_PICKED;
                $stock->box_barcode = '';
                $stock->save(false);

                $countStockForOrderItem = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'product_barcode'=>$model->product_barcode,'outbound_picking_list_id' => $qIDs])->count();

                if ($ioi = OutboundOrderItem::findOne(['product_barcode' => $model->product_barcode, 'outbound_order_id' => $stock->outbound_order_id])) {

//                    $ioi->accepted_qty -= 1;
//                    if ($ioi->accepted_qty < 1) {
//                        $ioi->accepted_qty = 0;
//                    }

                    $ioi->accepted_qty = $countStockForOrderItem;

//                    $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                    $ioi->status = Stock::STATUS_OUTBOUND_PICKED;
                    $ioi->save(false);
                }


                $oo = OutboundOrder::findOne($stock->outbound_order_id);
//                $oo->accepted_qty -= 1;
//                if ($oo->accepted_qty < 1) {
//                    $oo->accepted_qty = 0;
//                }
                $countStockForOrder = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'outbound_order_id'=>$stock->outbound_order_id])->count();
                $oo->accepted_qty = $countStockForOrder;

//                $oo->status = Stock::STATUS_OUTBOUND_SCANNING;
//                $oo->status = Stock::STATUS_OUTBOUND_PICKED;
                $oo->save(false);

            }

            $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);
//            $countInBox = OutboundPickingLists::getCountInBoxByPickingList($model->box_barcode, $qIDs);
            $countInBox = OutboundPickingLists::getCountInBoxByOutboundOrder($model->box_barcode, $qIDs);
            $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countInBox' => $countInBox,
            'exp_qty' => isset($orderData['allocated_qty']) ? $orderData['allocated_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => '', // $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
//            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
    * Clear all product in box
    * @param string $box_barcode Box barcode
    * */
    public function actionClearBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $stockArrayByPL = [];
        $orderData = [];

        $model = new ScanningForm();
        $model->scenario = 'ClearBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);

            $outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->where(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->column();

            if ($productsInBox = Stock::find()->select('count(product_barcode) as product_barcode_count, product_barcode, outbound_order_id')
                ->where([
                    'box_barcode' => $model->box_barcode,
                    'outbound_order_id' => $outboundOrderIDs,
                    'status' => Stock::STATUS_OUTBOUND_SCANNED
                ])
                ->groupBy('product_barcode')->asArray()->all()
            ) {

				$productService = new ProductService();

                foreach ($productsInBox as $item) {
					$productID = $productService->getProductIdByBarcode($item['product_barcode']);
					$ioi = OutboundOrderItem::findOne(['product_id' =>$productID, 'outbound_order_id' => $item['outbound_order_id']]);
					if ($ioi) {

                        // STATUS
                        Stock::updateAll([
                            'status' => Stock::STATUS_OUTBOUND_PICKED,
                            'box_barcode' => ''
                        ],
                        [
                            'box_barcode' => $model->box_barcode,
							'product_id' => $productID,
                            'outbound_order_id' => $item['outbound_order_id'],
                            'status' => Stock::STATUS_OUTBOUND_SCANNED
                        ]
                        );

						$countStock = Stock::find()
										   ->andWhere([
											   'status'=>Stock::STATUS_OUTBOUND_SCANNED,
											   'product_id' => $productID,
											   'outbound_order_id' => $item['outbound_order_id']
										   ])->count();
                        $ioi->accepted_qty = $countStock;

                        // OUTBOUND ORDER ITEM
                        $ioi->status = Stock::STATUS_OUTBOUND_PICKED;
                        $ioi->save(false);

                        // OUTBOUND ORDER
                        $oo = OutboundOrder::findOne($item['outbound_order_id']);

						$countStockForOrder = Stock::find()->andWhere(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'outbound_order_id'=>$item['outbound_order_id']])->count();
                        $oo->accepted_qty = $countStockForOrder;
                        $oo->status = Stock::STATUS_OUTBOUND_PICKED;
                        $oo->save(false);

                        $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);
                    }
                }
            } else {
                $model->addError('box_barcode','<b>['.$model->box_barcode.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод короба или короб пуст'));
                $errors = $model->getErrors();
            }
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'exp_qty' => isset($orderData['allocated_qty']) ? $orderData['allocated_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => '', // $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
//            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
     * Print difference list
     *
     * */
    public function actionPrintingDifferencesList()
    {
        $plIDs = Yii::$app->request->get('plids');
        $qIDs = OutboundPickingLists::prepareIDsHelper($plIDs);
		$outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->where(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->column();
//        $subQueryStatusPicked = Stock::find()->select('');
// b0000041815
//        (select count(*) FROM stock as stck WHERE stck.status= "'.Stock::STATUS_OUTBOUND_PICKED.'"  AND stck.product_barcode = stock.product_barcode) as count_status_picked,
//                     (select count(*) FROM stock as stck WHERE stck.status= "'.Stock::STATUS_OUTBOUND_SORTING.'"  AND stck.product_barcode = stock.product_barcode) as count_status_sorting ,
//                     (select count(*) FROM stock as stck WHERE stck.status= "'.Stock::STATUS_OUTBOUND_SORTED.'"  AND stck.product_barcode = stock.product_barcode) as count_status_sorted,
//                     (select count(*) FROM stock as stck WHERE stck.product_barcode = stock.product_barcode AND stck.outbound_picking_list_id = stock.outbound_picking_list_id) as count_exp
//
//        $plIDs = OutboundPickingLists::prepareIDsHelper($plIDs,true);

        $subQuery = (new Query())
            ->select('count(*)')
            ->from('stock as stck')
//            ->where(['stck.status'=>Stock::STATUS_OUTBOUND_SCANNED,'stck.outbound_picking_list_id'=>$qIDs])
            ->where(['stck.status'=>Stock::STATUS_OUTBOUND_SCANNED,'stck.outbound_order_id'=>$outboundOrderIDs])
            ->andWhere('stck.product_barcode = stock.product_barcode');

//        select count(*) FROM stock as stck WHERE stck.status= "' . Stock::STATUS_OUTBOUND_SCANNED . '" AND stck.product_barcode = stock.product_barcode AND stck.outbound_picking_list_id IN ('.$in.')


//        ->select('id, outbound_order_id, product_barcode, box_barcode, status, primary_address, secondary_address, product_model, count(*) as items,
//                     (select count(*) FROM stock as stck WHERE stck.status= "' . Stock::STATUS_OUTBOUND_SCANNED . '" AND stck.product_barcode = stock.product_barcode AND stck.outbound_picking_list_id IN ('.$in.')) as count_status_scanned
//            ')

        $items = Stock::find()
            ->select(['id', 'outbound_order_id', 'product_barcode', 'box_barcode', 'status', 'primary_address', 'secondary_address', 'product_model', 'count(*) as items','count_status_scanned'=>$subQuery])
//                    ->select('id, outbound_order_id, product_barcode, box_barcode, status, primary_address, secondary_address, product_model, count(*) as items,
//                     (select count(*) FROM stock as stck WHERE stck.status= "' . Stock::STATUS_OUTBOUND_SCANNED . '" AND stck.product_barcode = stock.product_barcode AND stck.outbound_picking_list_id = stock.outbound_picking_list_id) as count_status_scanned
//            ')
            ->where([
//                'outbound_picking_list_id' => $qIDs,
                'outbound_order_id' => $outboundOrderIDs,
                'status' => [
                    Stock::STATUS_OUTBOUND_PICKED,
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_SCANNING
                ],
            ])
            ->groupBy('product_barcode') // , box_barcode
            ->orderBy([
                'product_barcode' => SORT_DESC,
//                'box_barcode' => SORT_DESC,
                'count_status_scanned' => SORT_DESC,
            ])
            ->asArray()
            ->all();

//        VarDumper::dump($items,10,true);
//        die('-----STOP-----');
//        if($this->printType=='html'){
//            Yii::$app->layout = 'print-html';
//            return $this->render('print/printing-differences-list-html', ['items' => $items,'plIDs'=>$qIDs]);
//        }
        return $this->render('print/printing-differences-list-pdf', ['items' => $items,'plIDs'=>$qIDs]);
    }

    /**
     * Printing box label
     * @param string $plids Picking list IDs
     * */
    public function actionPrintingBoxLabel()
    {
        $plIDs = Yii::$app->request->get('plids');
        $qIDs = OutboundPickingLists::prepareIDsHelper($plIDs);

        $outboundOrderIDs = OutboundPickingLists::find()
            ->select('outbound_order_id')
            ->andWhere(['id'=>$qIDs])
            ->groupBy('outbound_order_id')
			->asArray()
			->column();

        $items = Stock::find()
            ->select('id,outbound_order_id, box_barcode, box_size_m3')
            ->andWhere([
                'outbound_order_id' => $outboundOrderIDs,
                'status' => [
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
            ])
            ->groupBy('box_barcode')
            ->asArray()
            ->all();

        $model = '';
        $outboundOrderModel = '';
        if (isset($items[0]['outbound_order_id'])) {
            $outboundOrderModel = OutboundOrder::findOne($items[0]['outbound_order_id']);
            if ($dpo = TlDeliveryProposalOrders::findOne(['order_id' => $items[0]['outbound_order_id']])) {
                $model = TlDeliveryProposal::findOne($dpo->tl_delivery_proposal_id);
                $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
                $outboundOrderModel->packing_date = DateHelper::getTimestamp();
                $outboundOrderModel->save(false);
                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL],'id = :id AND accepted_qty > 0',[':id'=>$items[0]['outbound_order_id']]);
                Stock::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL],'outbound_order_id = :outbound_order_id AND status = :status',[':status'=>Stock::STATUS_OUTBOUND_SCANNED,':outbound_order_id'=>$items[0]['outbound_order_id']]);
            }

            OutboundPickingLists::updateAll(['status'=>OutboundPickingLists::STATUS_PRINT_BOX_LABEL],['id'=>$qIDs]);

            $m3Sum = 0;
            foreach($items as $boxM3) {
                if(isset($boxM3['box_size_m3']) && !empty($boxM3['box_size_m3']))
                $m3Sum += $boxM3['box_size_m3'];
            }

            if($model) {
                $model->mc = $m3Sum;
                $model->mc_actual = $m3Sum;
                $model->number_places_actual = count($items);
                $model->number_places = count($items);
                $model->save(false);
            }

            $outboundOrderModel->mc = $m3Sum;
            $outboundOrderModel->accepted_number_places_qty = count($items);
            $outboundOrderModel->save(false);
            if($dpOrderModel = TlDeliveryProposalOrders::findOne(['order_id'=>$outboundOrderModel->id])) {
                $dpOrderModel->number_places = count($items);
                $dpOrderModel->mc = $m3Sum;
                $dpOrderModel->mc_actual = $m3Sum;
                $dpOrderModel->save(false);
            }
            //E: Высчитываем m3 всех коробов заказа
        }

        return $this->render('print/_box-label-pdf', ['boxes' => $items, 'model' => $model,'outboundOrderModel'=>$outboundOrderModel]);
    }

    /**
    * Set status complete
    * */
    public function actionComplete()
    {
        $id = Yii::$app->request->get('id');
        if ($model = OutboundOrder::findOne($id)) {
        	if ($model->api_complete_status == "no" || 1) {
        		$os = new OutboundService();
        		$data = $os->sendStatusInCompleted($model->id);
				$model->status = Stock::STATUS_OUTBOUND_COMPLETE;
				$model->api_complete_status = empty($data->response_message) ? "yes" : $data->response_message;
				$model->save(false);
				Yii::$app->getSession()->setFlash('success',"Накладная принята со статусом: ".$model->api_complete_status);
				return  $this->redirect(["/intermode/outbound/report/view","id"=>$model->id]);
			} else {
				Yii::$app->getSession()->setFlash('error',"Накладная уже принята");
				return  $this->redirect(["/intermode/outbound/report/view","id"=>$model->id]);
			}
        }
		Yii::$app->getSession()->setFlash('error',"Накладная не найдена");
		return  $this->redirect(["/intermode/outbound/report/view","id"=>$id]);
    }

	/**
	*
	* */
	public function actionValidatePrintingBoxLabel()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
//		$orderId = 0;
		$scanForm = new ScanningForm();
		$scanForm->scenario = 'onPrintBoxLabel';
		$runNext = "no";
//		if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
//			$dto = $scanForm->getDTO();
//			$orderId = $dto->order->id;
			$runNext = "ok";
//		} else {
//			$errors = ActiveForm::validate($scanForm);
//		}

		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors,
//			'orderId' => $orderId,
			'runNext' => $runNext,
		];
	}
	/**
	 * Printing box content
	 * */
	public function actionPrintingBoxContent()
	{
		// wms20.local/wms/koton/outbound-common/printing-box-content?box_barcode=700000091896
		$client_id = Client::CLIENT_ERENRETAIL;
		$box_barcode = Yii::$app->request->get('box_barcode');
		$plIDs = Yii::$app->request->get('picking_list');
//         $plIDs = 6257;

		$qIDs = OutboundPickingLists::prepareIDsHelper($plIDs);
		$oolsID = array_shift($qIDs);
		$opl = OutboundPickingLists::findOne($oolsID);
		$toPoint = '';

		$stockItems = Stock::find()
						   ->select('id, product_barcode, count(product_barcode) as product_qty')
						   ->where([
							   'outbound_order_id' => $opl->outbound_order_id,
							   'client_id' => $client_id,
							   'status' => [
								   Stock::STATUS_OUTBOUND_SCANNED,
								   Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
								   Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
							   ],
							   'box_barcode' => $box_barcode,
						   ])
						   ->groupBy('product_barcode')
						   ->asArray()
						   ->all();
		if($opl){
			if($outboundOrder = OutboundOrder::findOne($opl->outbound_order_id)){
				if($point = $outboundOrder->toPoint){
					$toPoint = $point->getPointTitleByPattern('stock');
				}
			}
		}

		return $this->render('print/_box-content-pdf', [
			'items' => $stockItems,
			'box_barcode' => $box_barcode,
			'toPoint' => $toPoint,
			'clientID' => $client_id,
		]);
	}

	/**
	 *
	 * */
	public function actionSaveBoxKg()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$model = new ScanningForm();
		$model->scenario = 'sSaveBoxKg';

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {

			$qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);

			$outboundOrderIDs = OutboundPickingLists::find()
													->select('outbound_order_id')
													->andWhere(['id'=>$qIDs])
													->groupBy('outbound_order_id')
													->asArray()->column();

			Stock::updateAll([
				'box_kg'=>$model->box_kg,
			],[
				'box_barcode'=>$model->box_barcode,
				'outbound_order_id' => $outboundOrderIDs,
				'status' => Stock::STATUS_OUTBOUND_SCANNED,
			]);

			return [
				'success' => '1',
				'plids' => $model->picking_list_barcode_scanned,
			];
		}

		return [
			'success'=>'0',
			'errors' => ActiveForm::validate($model)
		];
	}
}