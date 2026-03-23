<?php

namespace app\modules\wms\controllers\colins;

use app\modules\inbound\inbound;
//use Codeception\Module\Cli;
use common\api\DeFactoSoapAPI;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundUploadLog;
use common\modules\stock\models\Stock;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\inbound\models\InboundOrderItemProcess;
use stockDepartment\modules\inbound\models\LoadFromDeFactoAPIForm;
use common\modules\client\models\Client;

//use common\modules\client\components\ClientManager;
//use common\modules\inbound\components\InboundManager;
use stockDepartment\modules\inbound\models\InboundForm;
use Yii;
use stockDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\web\UploadedFile;
use stockDepartment\modules\wms\models\ColinsInboundForm;
use common\modules\outbound\models\OutboundOrder;
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use stockDepartment\modules\outbound\models\AllocationListForm;
use common\modules\outbound\models\OutboundOrderItem;
use stockDepartment\modules\outbound\models\ScanningColinsForm;
use stockDepartment\modules\product\models\ProductSearch;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\helpers\DateHelper;
use app\modules\outbound\models\ColinsOutboundForm;
use common\modules\product\models\ProductBarcodes;


class InboundController extends Controller
{
    /*
        * Delete product by barcode  in box
        * @return JSON true or errors array
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

        $model = new ColinsInboundForm();
        $model->scenario = 'ClearProductInBox';
        $model->load(Yii::$app->request->post());
        $model->client_id = Client::CLIENT_COLINS;
        $inboundItemIDs = $model->getItemsByParentOrder();

        if ($model->validate()) {

//            Stock::findAndUpdate([
//                'status'=> Stock::STATUS_INBOUND_SCANNING
//                ,'primary_address'=>''
//            ],
//                [
//                    'primary_address'=>$model->box_barcode,
//                    'product_barcode'=>$model->product_barcode,
//                    'inbound_order_id'=>$inboundIdArray,
//                    'status'=>[
//                        Stock::STATUS_INBOUND_SCANNED,
//                        Stock::STATUS_INBOUND_OVER_SCANNED
//                    ]
//                ]
//            );

           $stockItem =  Stock::find()->andWhere([
                'primary_address'=>$model->box_barcode,
                'product_barcode'=>$model->product_barcode,
                'inbound_order_id'=>$inboundItemIDs,
                'status'=>[
                    Stock::STATUS_INBOUND_SCANNED,
                    Stock::STATUS_INBOUND_OVER_SCANNED
                ]
            ])->orderBy('status DESC')->one();

            if($stockItem){

//                $stockItem->status = Stock::STATUS_INBOUND_SCANNING;
                $stockItem->status = Stock::STATUS_INBOUND_SORTING;
                $stockItem->status_availability = Stock::STATUS_AVAILABILITY_NOT_SET;
                $stockItem->primary_address = '';
                $stockItem->save(false);

                if(InboundOrder::find()->andWhere(['id'=>$stockItem->inbound_order_id,'status'=>Stock::STATUS_INBOUND_OVER_SCANNED])->exists()) {
                    $stockItem->delete();
                }

                $countStockForItem =  Stock::find()->where([
                    'inbound_order_id' => $stockItem->inbound_order_id,
                    'product_barcode' => $stockItem->product_barcode,
                    'status' => Stock::STATUS_INBOUND_SCANNED,
//                    'inbound_order_id'=>$inboundItemIDs,
                ])->count();

                if($ioi =  InboundOrderItem::findOne(['product_barcode'=>$model->product_barcode,'inbound_order_id'=>$stockItem->inbound_order_id])) {

//                    $ioi->accepted_qty -= 1;
                    $ioi->accepted_qty = $countStockForItem;
                    $ioi->save(false);

                    $partyProductExpected = 0;
                    $partyProductAccepted = 0;
                    $productParty = InboundOrderItem::find()->andWhere(['product_barcode' => $model->product_barcode, 'inbound_order_id' => $inboundItemIDs])->asArray()->all();

                    foreach ($productParty as $item) {
                        $partyProductExpected += $item['expected_qty'];
                        $partyProductAccepted += $item['accepted_qty'];
                    }

                    $colorRowClass = 'alert-danger';
                    if( $partyProductAccepted ==  $partyProductExpected) {
                        $colorRowClass = 'alert-success';
                    }elseif($partyProductAccepted > $partyProductExpected) {
                        $colorRowClass = 'alert-warning';
                    }

                    $countValue = $partyProductAccepted;
                    $rowId = $model->product_barcode;
                };

                $countStockForOrder =  Stock::find()->where([
                    'inbound_order_id' => $stockItem->inbound_order_id,
                    'status' => Stock::STATUS_INBOUND_SCANNED,
                ])->count();

                if($inbound = InboundOrder::findOne($stockItem->inbound_order_id)) {
                    //S: Удаляем товар со стока елси он находится в коробе для лишних товаров (т.е больше чем по накладной)
                    if( $inbound->status != Stock::STATUS_INBOUND_OVER_SCANNED) {
                        $inbound->status = Stock::STATUS_INBOUND_SCANNING;
                    }
                    //E: Удаляем товар со стока елси он находится в коробе для лишних товаров (т.е больше чем по накладной)

//                   $inbound->accepted_qty -= 1;
                    $inbound->accepted_qty = $countStockForOrder;
                    $inbound->save(false);

                    //$expected_qty = $inbound->expected_qty;

                    //S: PARTY
                    if($coi = ConsignmentInboundOrders::findOne($inbound->consignment_inbound_order_id)) {

                        $inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inbound->consignment_inbound_order_id])->asArray()->column();

                        $countStockForConsignment =  Stock::find()->where([
                            'inbound_order_id' => $inboundIDs,
//                            'status' => Stock::STATUS_INBOUND_SCANNED,
                            'status' => [
                                Stock::STATUS_INBOUND_SCANNED,
                                Stock::STATUS_INBOUND_OVER_SCANNED,
                                Stock::STATUS_OUTBOUND_SCANNED,
                                Stock::STATUS_OUTBOUND_SHIPPING,
                                Stock::STATUS_OUTBOUND_SHIPPED,
                                Stock::STATUS_OUTBOUND_COMPLETE,
                                Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                                Stock::STATUS_OUTBOUND_ON_ROAD,
                                Stock::STATUS_OUTBOUND_DELIVERED,
                                Stock::STATUS_OUTBOUND_DONE,
                                Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                                Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,

                            ],
                        ])->count();

                        $coi->accepted_qty = $countStockForConsignment;

//                        $coi->accepted_qty -= 1;
                        $coi->save(false);

                        $expectedQtyParty = $coi->expected_qty;
                        $acceptedQtyParty = $coi->accepted_qty;
                    }
                    //E: PARTY
                }

                $countProductInBox = InboundOrderItem::getScannedProductInBox($model->box_barcode,$inboundItemIDs);

            }

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors'=>$errors,
            'messages'=>$messages,
            'countProductInBox'=>$countProductInBox,
            //'countScannedProductInOrder'=>InboundOrder::getCountItemByID($stockItem->inbound_order_id),
            'expectedQtyParty'=>$expectedQtyParty,
            'acceptedQtyParty'=>$acceptedQtyParty,
            //'expected_qty'=> $expected_qty,
            'dataScannedProductByBarcode'=> [
                'rowId'=>$rowId,
                'countValue'=> $countValue,
                'colorRowClass'=> $colorRowClass
            ],
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
        $dataScannedProductByBarcode = [];
        $product_barcode_count = 0;
        $expected_qty = 0;
        //S: PARTY
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;
        //E: PARTY

        $model = new ColinsInboundForm();
        $model->scenario = 'ClearBox';
        $model->load(Yii::$app->request->post());
        $model->client_id = Client::CLIENT_COLINS;
        $inboundIdArray = $model->getItemsByParentOrder();

        if ($model->validate()) {
            //Ищем все записи stock в коробе
            if($productsInBox = Stock::find()->where([
                                                'primary_address'=>$model->box_barcode,
                                                'inbound_order_id'=>$inboundIdArray,
                                                'status'=>[
                                                    Stock::STATUS_INBOUND_SCANNED,
                                                    Stock::STATUS_INBOUND_OVER_SCANNED
                                                ]
                                            ])
                                            ->all()) {

                foreach($productsInBox as $item) {

                    if ($ioi = InboundOrderItem::findOne(['product_barcode' => $item->product_barcode, 'inbound_order_id' => $item->inbound_order_id])) {
                   // if ($ioi = InboundOrderItem::findOne(['product_barcode' => $item->product_barcode, 'inbound_order_id' => $model->order_number])) {
                        $item->primary_address = '';
                        $item->secondary_address = '';
//                        $item->status = Stock::STATUS_INBOUND_SCANNING;
                        $item->status = Stock::STATUS_INBOUND_SORTING;
                        $item->status_availability = Stock::STATUS_AVAILABILITY_NOT_SET;
                        $item->save(false);

                        if(InboundOrder::find()->andWhere(['id'=>$item->inbound_order_id,'status'=>Stock::STATUS_INBOUND_OVER_SCANNED])->exists()) {
                            $item->delete();
                        }

                        $countStockForItem =  Stock::find()->where([
                            //'inbound_order_id' => $model->order_number,
                            'inbound_order_id' => $inboundIdArray,
                            'product_barcode' => $item->product_barcode,
                            'status' => Stock::STATUS_INBOUND_SCANNED,
                        ])->count();

                        $ioi->accepted_qty = $countStockForItem;
                        $ioi->save(false);


                        $colorRowClass = 'alert-danger';
                        if ($ioi->accepted_qty == $ioi->expected_qty) {
                            $colorRowClass = 'alert-success';
                        } elseif ($ioi->accepted_qty > $ioi->expected_qty) {
                            $colorRowClass = 'alert-warning';
                        }

                        $countValue = $ioi->accepted_qty;
                        $rowId = $item->product_barcode;

                        $dataScannedProductByBarcode [] = [
                            'rowId' => $rowId,
                            'countValue' => $countValue,
                            'colorRowClass' => $colorRowClass
                        ];
                    };

                    $countStockForOrder =  Stock::find()->where([
                        'inbound_order_id' => $item->inbound_order_id,
                        'status' => Stock::STATUS_INBOUND_SCANNED,
                    ])->count();

                    if($inbound = InboundOrder::findOne($item->inbound_order_id)) {
                        //S: Удаляем товар со стока елси он находится в коробе для лишних товаров (т.е больше чем по накладной)
                        if( $inbound->status != Stock::STATUS_INBOUND_OVER_SCANNED) {
                            $inbound->status = Stock::STATUS_INBOUND_SCANNING;
                        }
                        //E: Удаляем товар со стока елси он находится в коробе для лишних товаров (т.е больше чем по накладной)
//                        $inbound->status = Stock::STATUS_INBOUND_SCANNING;
                        $inbound->accepted_qty = $countStockForOrder;
                        $inbound->save(false);

                        $expected_qty = $inbound->expected_qty;

                        //S: PARTY
                        if($coi = ConsignmentInboundOrders::findOne($inbound->consignment_inbound_order_id)) {

                            $inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inbound->consignment_inbound_order_id])->asArray()->column();

                            $countStockForConsignment =  Stock::find()->where([
                                'inbound_order_id' => $inboundIDs,
//                                'status' => Stock::STATUS_INBOUND_SCANNED,
                                'status' => [
                                    Stock::STATUS_INBOUND_SCANNED,
                                    Stock::STATUS_INBOUND_OVER_SCANNED,
                                    Stock::STATUS_OUTBOUND_SCANNED,
                                    Stock::STATUS_OUTBOUND_SHIPPING,
                                    Stock::STATUS_OUTBOUND_SHIPPED,
                                    Stock::STATUS_OUTBOUND_COMPLETE,
                                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                                    Stock::STATUS_OUTBOUND_ON_ROAD,
                                    Stock::STATUS_OUTBOUND_DELIVERED,
                                    Stock::STATUS_OUTBOUND_DONE,
                                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                                ],
                            ])->count();

                            $coi->accepted_qty = $countStockForConsignment;


                            $coi->save(false);

                            $expectedQtyParty = $coi->expected_qty;
                            $acceptedQtyParty = $coi->accepted_qty;
                        }
                        //E: PARTY
                    }

                }
            }

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors'=>$errors,
            'messages'=>$messages,
            //'countScannedProductInOrder'=>InboundOrder::getCountItemByID($model->order_number),
            'expected_qty'=> $expected_qty,
            'dataScannedProductByBarcode'=> $dataScannedProductByBarcode,
            'expectedQtyParty'=>$expectedQtyParty,
            'acceptedQtyParty'=>$acceptedQtyParty,
        ];
    }


    /// NEW END//////////////////////
    public function actionInboundForm()
    {
        $inboundForm = new ColinsInboundForm();

        return $this->renderAjax('scanning-form',[
            'inboundForm' => $inboundForm,
        ]);
    }

    /*
     * Get inbound orders in status new and in process by client
     * @param integer client_id
     * @return JSON
     * */
    public function actionGetInProcessInboundOrdersByClientId()
    {
        $clientID = Yii::$app->request->post('client_id');
        $type = '';
        $data = ['' => ''];
        if($cio =  ConsignmentInboundOrders::getNewAndInProcessItemByClientID($clientID)) {
            $data += $cio;
            $type = 'party-inbound';
        } else {
            $data += InboundOrder::getNewAndInProcessItemByClientID($clientID);
            $type = 'inbound';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'Success',
            'type' => $type,
            'dataOptions' => $data,
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
        $data +=  ConsignmentInboundOrders::getNewAndInProcessOrdersByPartyID($party_id);

        if($cio  = ConsignmentInboundOrders::findOne($party_id)) {
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
     * Get inbound order in status complete by client
     * @param integer client_id
     * @return JSON
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

    /*
     * Get scanned products by Consignment Order id
     *
     * */
    public function actionGetScannedProductByPartyId()
    {
        $id = Yii::$app->request->post('party_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $items = [];
        $productBarcodeArray = [];
        $itemsProcessItems = [];
        $acceptedQtyItems = [];

        if( $cio = ConsignmentInboundOrders::find()->andWhere(['id' => $id, 'client_id'=> Client::CLIENT_COLINS])->one()) {
            $inboundID = InboundOrder::find()->select('id')
                ->andWhere(['consignment_inbound_order_id'=>$cio->id, 'client_id' => Client::CLIENT_COLINS])
                ->asArray()
                ->column();

            if($inboundID) {

                $acceptedQtyItems =  Stock::find()
                    ->select('count(id) as accepted_qty, product_barcode')
                    ->andWhere([
                        'inbound_order_id' => $inboundID,
                        'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET,
                        'status' => [
                            Stock::STATUS_INBOUND_SCANNED,
                            Stock::STATUS_INBOUND_OVER_SCANNED,
                        ],
                    ])
                    ->groupBy('product_barcode')
                    ->asArray()
                    ->all();

                $items =  Stock::find()
                    ->select('count(id) as expected_qty, status_availability , id, inbound_order_id, outbound_order_id, product_barcode, product_model, status, primary_address, secondary_address')
                    ->andWhere([
                        'inbound_order_id' => $inboundID,
                        'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET,
                        'status' => [
                            Stock::STATUS_INBOUND_SORTING,
//                            Stock::STATUS_INBOUND_SCANNED,
//                            Stock::STATUS_INBOUND_SCANNING,
                        ]
                    ])
                    ->groupBy('product_barcode')
                    ->orderBy('product_barcode')
                    ->asArray()
                    ->all();
            }
        }

        return [
            'message' => 'Success',
            'expectedQtyParty' => $cio->expected_qty,
            'acceptedQtyParty' => is_null($cio->accepted_qty) ? '0' : $cio->accepted_qty,
            'items' =>$this->renderPartial('_order_items',[
                                                            'items'=>$items,
                                                            'acceptedQtyItems'=>$acceptedQtyItems
            ]),
        ];
    }

    /*
    * Validate scanned box
    * @return JSON true or errors array
    * */
    public function actionValidateScannedBox()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $client_id = Client::CLIENT_COLINS;
        $model = new ColinsInboundForm();

       // $model->scenario = 'ScannedBox';
        $model->load(Yii::$app->request->post());
        $model->client_id = $client_id;
        if ($model->validate()) {

            $inboundIDs = InboundOrder::find()->select('id')
                ->andWhere(['consignment_inbound_order_id'=>$model->party_number, 'client_id' => $client_id])
                ->asArray()
                ->column();

            return [
                'success' => '1',
                'countProductInBox'=>InboundOrderItem::getScannedProductInBox($model->box_barcode, $inboundIDs),
            ];

        } else {
            $errors = ActiveForm::validate($model);
            return [
                'success'=>(empty($errors) ? '1' : '0'),
                'errors' => $errors
            ];
        }
    }

    /*
    * Scanned product in box
    * @return JSON true or errors array
    * */
    public function actionScanProductInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $client_id = Client::CLIENT_COLINS;
        $expected_qty = 0;

        $model = new ColinsInboundForm();
        $model->scenario = 'ScannedProduct';
        $model->load(Yii::$app->request->post());
        $model->client_id = $client_id;

        $inboundIDs = $model->getItemsByParentOrder();

        if ($model->validate()) {

            //Ищем на стоке  запись по ШК товара и партии, выставляем ему статус отсканирован
            $stock = Stock::setStatusInboundScannedValueByConsignmentOrder($client_id, $model->party_number, $model->product_barcode,$model->box_barcode);

            //считаем кол-во отсканированых товаров на стоке для Inbound Item
            $countStockForItem =  Stock::find()->andWhere([
                'inbound_order_id' => $stock->inbound_order_id,
                'product_barcode' => $model->product_barcode,
                'status' => [
                            Stock::STATUS_INBOUND_SCANNED,
                            Stock::STATUS_INBOUND_OVER_SCANNED,
                ],
                'client_id' => $model->client_id,
            ])->count();

            if ( $ioi = InboundOrderItem::find()->andWhere(['inbound_order_id' => $stock->inbound_order_id,
                'product_barcode' => $model->product_barcode,
            ])->one() ) {

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

            } else { }

            //считаем кол-во отсканированых товаров для Inbound Order
            $countStockForOrder =  Stock::find()->where([
                'inbound_order_id' => $stock->inbound_order_id,
                'status' => [
                    Stock::STATUS_INBOUND_SCANNED,
                    Stock::STATUS_INBOUND_OVER_SCANNED,
                ],
                'client_id' => $model->client_id,
            ])->count();

            if($inboundModel = InboundOrder::find()->andWhere(['id'=>$stock->inbound_order_id, 'consignment_inbound_order_id' => $model->party_number])->one()) {

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
            if($coi = ConsignmentInboundOrders::findOne($inboundModel->consignment_inbound_order_id)) {

                $inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inboundModel->consignment_inbound_order_id])->asArray()->column();

                $countStockForConsignment =  Stock::find()->where([
                    'inbound_order_id' => $inboundIDs,
                    'status' => [
                        Stock::STATUS_INBOUND_SCANNED,
                        Stock::STATUS_INBOUND_OVER_SCANNED,
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_SHIPPING,
                        Stock::STATUS_OUTBOUND_SHIPPED,
                        Stock::STATUS_OUTBOUND_COMPLETE,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_ON_ROAD,
                        Stock::STATUS_OUTBOUND_DELIVERED,
                        Stock::STATUS_OUTBOUND_DONE,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,

                    ],
                    'client_id' => $model->client_id,
                ])->count();

                $coi->accepted_qty = $countStockForConsignment;
                $coi->save(false);
//                $coi->accepted_qty += 1;
//               $coi->recalculateOrderItems();
//                $coi = ConsignmentInboundOrders::findOne($inboundModel->consignment_inbound_order_id);
                $expectedQtyParty = $coi->expected_qty;
                $acceptedQtyParty = $coi->accepted_qty;
            }
            $partyProductExpected = 0;
            $partyProductAccepted = 0;
            $productParty = InboundOrderItem::find()->andWhere(['product_barcode' => $model->product_barcode, 'inbound_order_id' => $inboundIDs])->asArray()->all();

            foreach ($productParty as $item) {
                   $partyProductExpected += $item['expected_qty'];
                   $partyProductAccepted += $item['accepted_qty'];
            }


            $colorRowClass = 'alert-danger';
            if( $partyProductExpected == $partyProductAccepted) {
                $colorRowClass = 'alert-success';
            } elseif( $partyProductAccepted > $partyProductExpected) {
                $colorRowClass = 'alert-warning';
            }
            return [
                'success' => (empty($errors) ? '1' : '0'),
                'countProductInBox'=>InboundOrderItem::getScannedProductInBox($model->box_barcode, $inboundIDs),
               //'countScannedProductInOrder'=>InboundOrder::getCountItemByID($model->order_number),
                'expectedQtyParty'=>$expectedQtyParty,
                'acceptedQtyParty'=>$acceptedQtyParty,
                'expected_qty'=> $expected_qty,
//                'xxxxx'=> $countStockForItem,
                'dataScannedProductByBarcode'=> [
                    'rowId'=> $model->product_barcode,
                    //'rowId'=>$ioi->id.'-'.$model->product_barcode,
                    'expected_qty'=> $partyProductExpected,
                    'countValue'=> $partyProductAccepted,
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



    /*
 * Show the list of differences
 * */
    public function actionPrintListDifferences()
    {

        $id = Yii::$app->request->get('party_id');
        $items = [];
        $productBarcodeArray = [];
        $acceptedQtyItems = [];
        $itemsProcessItems = [];

        if( $inboundID = InboundOrder::find()->select('id')
            ->andWhere(['consignment_inbound_order_id'=>$id, 'client_id' => Client::CLIENT_COLINS])
            ->asArray()
            ->column()) {

            $acceptedQtyItems =  Stock::find()
                ->select('count(id) as accepted_qty, product_barcode')
                ->andWhere([
                    'inbound_order_id' => $inboundID,
                    'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET,
                    'status' => [
                        Stock::STATUS_INBOUND_SCANNED,
                        Stock::STATUS_INBOUND_OVER_SCANNED,
                    ],
                ])
                ->groupBy('product_barcode')
//                ->orderBy('product_barcode')
                ->asArray()
                ->all();

            if(!empty($acceptedQtyItems)) {
                $acceptedQtyItems = ArrayHelper::map($acceptedQtyItems,'product_barcode','accepted_qty');
                $productBarcodeArray = array_keys($acceptedQtyItems);
            }

            $itemsProcessItems = Stock::find()
                ->select('product_barcode, primary_address, secondary_address, product_model, count(*) as items ')
                ->where([
                    'product_barcode' => $productBarcodeArray,
                    'status' => [
                        Stock::STATUS_INBOUND_SCANNED,
                        Stock::STATUS_INBOUND_OVER_SCANNED,
                    ]
                ])
                ->groupBy('product_barcode, primary_address')
                ->orderBy([
                    'secondary_address' => SORT_DESC,
                    'primary_address' => SORT_DESC,
                ])
                ->asArray()
                ->all();

            if(!empty($itemsProcessItems)) {
                $tmpArray = [];
                foreach($itemsProcessItems as $value) {
                    $tmpArray[$value['product_barcode']][] = $value;
                }
                $itemsProcessItems = $tmpArray;
            }

            $items =  Stock::find()
                ->select('count(id) as expected_qty, status_availability , id, inbound_order_id, outbound_order_id, product_barcode, product_model, status, primary_address, secondary_address')
                ->andWhere([
                    'inbound_order_id' => $inboundID,
                    'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET,
                    'status' => [
                        Stock::STATUS_INBOUND_SORTING,
                        Stock::STATUS_INBOUND_SCANNED,
                        Stock::STATUS_INBOUND_SCANNING,
                    ]
                ])
                ->groupBy('product_barcode')
                ->orderBy('product_barcode')
                ->asArray()
                ->all();


        }
//        VarDumper::dump($itemsProcessItems,10,true);
//        VarDumper::dump($inboundID,10,true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "inboundID : <br />";
//        VarDumper::dump($inboundID,10,true); // +
//        echo "acceptedQtyItems : <br />";
//        VarDumper::dump($acceptedQtyItems,10,true); // +
//
//        echo "itemsProcessItems : <br />";
//        VarDumper::dump($itemsProcessItems,10,true); // +
//
//        echo "items : <br />";
//        VarDumper::dump($items,10,true); // +

//        echo count($items);
//        die('-actionGetListDifferences-');

        //---------------------------------------------------------------------------------
        $csvStr = '';
//            $pdf->SetLineWidth(0.2);

        $structure_table = '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
            '   <tr align="center" valign="middle" >' .
            '      <th width="30%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Product Barcode') . '</strong></th>' .
            '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Product Model') . '</strong></th>' .
            '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
            '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Secondary address') . '</strong></th>' .
            '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Expected Qty') . '</strong></th>' .
            '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Accepted Qty') . '</strong></th>' .
            '   </tr>';
        $csvStr .= Yii::t('inbound/forms','Product Barcode').';'
            .Yii::t('inbound/forms','Product Model').';'
            .Yii::t('inbound/forms','Primary address').';'
            .Yii::t('inbound/forms','Secondary address').';'
            .Yii::t('inbound/forms','Expected Qty').';'
            .Yii::t('inbound/forms','Accepted Qty').';'
            ."\n";

        if (!empty($items)) {
            foreach ($items as $item) {
                $accepted_qty = (isset($acceptedQtyItems[$item['product_barcode']]) ? $acceptedQtyItems[$item['product_barcode']] : '0');
                if($item['expected_qty'] != $accepted_qty) {
                    $structure_table .= '<tr align="center" valign="middle" style="background-color:' . ($item['expected_qty'] == $accepted_qty ? '#FFFFF1' : 'lightgray') . '">
                <td align="left" valign="middle" border="1">' . $item['product_barcode'] . '</td>
                <td align="center" valign="middle" border="1">' . $item['product_model'] . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . '-' . '</td>
                <td align="center" valign="middle" border="1">' . $item['expected_qty'] . '</td>
                <td align="center" valign="middle" border="1">' . $accepted_qty. '</td>
            </tr>';
                    $csvStr .= ''.';'
                        .''.';'
                        .''.';'
                        .''.';'
                        .''.';'
                        .''.';'
                        ."\n";
                    $csvStr .= $item['product_barcode'].';'
                        .$item['product_model'].';'
                        .'-'.';'
                        .'-'.';'
                        . $item['expected_qty'].';'
                        . $accepted_qty.';'
                        ."\n";

                    if (isset($itemsProcessItems[$item['product_barcode']])) {
                        foreach ($itemsProcessItems[$item['product_barcode']] as $value) {
                            $structure_table .= '<tr align="center" valign="middle">
                    <td align="left" valign="middle" border="1">' . $value['product_barcode'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['product_model'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['primary_address'] . '</td>
                    <td align="center" valign="middle" border="1">' . $value['secondary_address'] . '</td>
                    <td align="center" valign="middle" border="1">' . '-' . '</td>
                    <td align="center" valign="middle" border="1">' . $value['items'] . '</td>
                </tr>';

                            $csvStr .=  $value['product_barcode'].';'
                                . $value['product_model'].';'
                                .$value['primary_address'].';'
                                .$value['secondary_address'].';'
                                . '-'.';'
                                .$value['items'].';'
                                ."\n";

                        }
                    }
                }
            }
        }
        $structure_table .= '</table>';
        if($this->printType == 'html'){
            Yii::$app->layout = 'print-html';
            return $this->render('print/list-differences-html',['html'=>$structure_table]);
        } else {
            $f = date("d-m-Y-H-i-s") . '-list-differences.csv';
            file_put_contents($f,$csvStr,FILE_APPEND);

            return Yii::$app->response->sendFile($f);
        }

    }

    /*
    * Confirm inbound order data
    * @return JSON true or errors array
    * */
    public function actionConfirmOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];

        $model = new ColinsInboundForm();
        $model->scenario = 'ConfirmOrder';
        $model->load(Yii::$app->request->post());
        $model->client_id = Client::CLIENT_COLINS;
        if ($model->validate()) {
            if($inboundOrderID = $model->getItemsByParentOrder()){
                foreach ($inboundOrderID as $inboundOrder){
                    if($io = InboundOrder::findOne($inboundOrder)) {
                        if($io->status == Stock::STATUS_INBOUND_COMPLETE) {
                            $messages [] = Yii::t('inbound/errors','Накладная с номером ' . $io->order_number . ' уже принята');
                        } else {
                            $io->status = Stock::STATUS_INBOUND_COMPLETE;
                            $io->date_confirm = time();
                            $io->save(false);

                            Stock::updateAll([
                                'status'=>Stock::STATUS_INBOUND_COMPLETE,
                                'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
                            ],[
                                'inbound_order_id'=>$io->id,
                                'status'=>[
                                    Stock::STATUS_INBOUND_SCANNED,
                                    Stock::STATUS_INBOUND_OVER_SCANNED,
                                ]
                            ]);

                            Stock::deleteAll('inbound_order_id = :inbound_order_id AND status = :status AND status_availability = :status_availability',[':status_availability'=>Stock::STATUS_AVAILABILITY_NOT_SET,':inbound_order_id'=>$io->id,':status'=>Stock::STATUS_INBOUND_SORTING]);

                            $messages [] =  Yii::t('inbound/errors','Накладная с номером ' . $io->order_number . ' успешно принята');

                            if($coi = ConsignmentInboundOrders::findOne($io->consignment_inbound_order_id)) {
                                $coi->status = Stock::STATUS_INBOUND_COMPLETE;
                                $coi->save(false);
                                // находим все пакладные проверяем в каком они статусе, если выполнены
                                // ставим статус выполнена если нет то в процессе (принимается)
                                // почему тут сразу не посчитать общее количество принятый товаров?

                                //s: TODO Нужно подумать как это правильно сделать для колинся
//                                $coi->status = Stock::STATUS_INBOUND_SCANNING;
//                                if(!InboundOrder::find()->where('status != :status AND consignment_inbound_order_id = :consignment_inbound_order_id',[':status'=>Stock::STATUS_INBOUND_CONFIRM,':consignment_inbound_order_id'=>$io->consignment_inbound_order_id])->exists()) {
//                                    $coi->status = Stock::STATUS_INBOUND_CONFIRM;
//                                }
//                                $coi->save(false);
                                //E: TODO Нужно подумать как это правильно сделать для колинся
                            }
                        }
                    } else {
                        // TODO сделать уведомление на почту
                    }
                }
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



    /*
     * Set status complete
     * @param $id Order
     * @return JSON
     * */
    public function actionComplete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->get('id');

        if($model = InboundOrder::findOne($id)) {
            $model->status = Stock::STATUS_INBOUND_COMPLETE;
            $model->save(false);
        }

        return  [];
    }

    /*
     * Check order status fow show or not button complete order
     * @param $id Order
     * @return JSON
     * */
    public function actionCheckOrderStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $status = 'NO';

        if($model = InboundOrder::findOne($id)) {
            if($model->status == Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API) {
                $status = 'PREPARED-DATA-FOR-API';
            }
        }

        return  [
            'status'=> $status
        ];
    }

    /*
     *
     *
     * */
    public function actionPrintUnallocatedList()
    {
        $id = Yii::$app->request->get('party_id');
        $items = [];
        if($inboundID = InboundOrder::find()->select('id')
            ->andWhere(['consignment_inbound_order_id'=>$id, 'client_id' => Client::CLIENT_COLINS])
            ->asArray()
            ->column()) {
            $items = Stock::find()
                ->select('primary_address, secondary_address')
                ->where([
                    'inbound_order_id' => $inboundID,
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
}