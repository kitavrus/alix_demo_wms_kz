<?php

namespace app\modules\wms\controllers\defacto;

use app\modules\inbound\inbound;
use common\api\DeFactoSoapAPI;
use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundUploadLog;
use common\modules\kpiSettings\models\KpiSetting;
use common\modules\stock\models\Stock;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\inbound\models\InboundOrderItemProcess;
use stockDepartment\modules\inbound\models\LoadFromDeFactoAPIForm;
use common\modules\client\models\Client;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use stockDepartment\modules\wms\models\defacto\InboundForm;
use Yii;
use stockDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\web\UploadedFile;

//use yii\helpers\VarDumper;


class InboundV2Controller extends Controller
{
    public function actionIndex()
    {
        $inboundForm = new InboundForm();
        $clientsArray = Client::getActiveItems();

        return $this->renderAjax('index', [
            'inboundForm' => $inboundForm,
            'clientsArray' => $clientsArray,
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
     * Get scanned products by inbound order id
     *
     * */
    public function actionGetScannedProductById() // +
    {
        $id = Yii::$app->request->post('inbound_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $countScannedProductInOrder = InboundOrder::getCountItemByID($id);
//        $items = [];
//        $timer = '0';
        if( $io = InboundOrder::findOne($id)) {
//            $items = $io->getOrderItems()->select('*,(expected_qty - accepted_qty) as order_by')->orderBy(new Expression('box_barcode, order_by!=0 DESC'))->asArray()->all();

//            $timer = KpiSetting::getInboundScanningTime($io->client_id, $io->expected_qty - $countScannedProductInOrder);
        }

        return [
            'message' => 'Success',
            'countScannedProductInOrder' => $countScannedProductInOrder,
            'expected_qty' => $io->expected_qty,
            'cdTimer' => 0,//$timer,
            'items' =>'',//$this->renderPartial('_order_items',['items'=>$items]),
        ];
    }

    /*
    * Validate scanned box
    * @return JSON true or errors array
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

    /*
    * Validate scanned box
    * @return JSON true or errors array
    * */
    public function actionValidateClientBoxBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new InboundForm();
        $model->scenario = 'ClientScannedBox';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $lotInfoOnClientBox = $model->getLotInfoInClientBox();
            return array_merge(['success' => '1'],$lotInfoOnClientBox);
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
    public function actionScanProductInBox() // +
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $expected_qty = 0;
        $model = new InboundForm();
        $model->scenario = 'ScannedProduct';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->setScannedStatus();
            $inboundModel= $model->updateInboundOrder();
            $inboundOrderItemModel = $model->updateInboundOrderItem();
            $model->updateConsignmentInboundOrders();
            $expectedQtyParty = 0;
            $acceptedQtyParty = 0;

//            $colorRowClass = 'alert-danger';
//            if( $inboundOrderItemModel->accepted_qty == $inboundOrderItemModel->expected_qty) {
//                $colorRowClass = 'alert-success';
//            }elseif($inboundOrderItemModel->accepted_qty > $inboundOrderItemModel->expected_qty) {
//                $colorRowClass = 'alert-warning';
//            }

            return [
                'success' => (empty($errors) ? '1' : '0'),
                'countProductInBox'=>$model->getScannedProductInBox(),
                'countScannedProductInOrder'=>$model->getAllScannedProductInOrder(),
                'expectedQtyParty'=>$expectedQtyParty,
                'acceptedQtyParty'=>$acceptedQtyParty,
                'expected_qty'=> $inboundModel->expected_qty,
//                'dataScannedProductByBarcode'=> [
//                    'rowId'=>$inboundOrderItemModel->id.'-'.$model->product_barcode,
//                    'expected_qty'=> $inboundOrderItemModel->expected_qty,
//                    'countValue'=> $inboundOrderItemModel->accepted_qty,
//                    'colorRowClass'=> $colorRowClass
//                ],
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
     * Print the list of differences
     * */
    public function actionPrintListDifferences()
    {
        $id = Yii::$app->request->get('inbound_id');

        $items = [];
        if( $io = InboundOrder::findOne($id)) {
            $items = $io->getOrderItems()->select('*,(expected_qty - accepted_qty) as order_by')->orderBy(new Expression('box_barcode, order_by!=0 DESC'))->asArray()->all();
        }
        return $this->render('print/list-differences-pdf',['items'=>$items]);
    }



    /*
     * Delete product by barcode  in box
     * @return JSON true or errors array
     * */
    public function actionClearProductInBox() // +
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $countValue = 0;
        $rowId = '';
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;

        $expected_qty = 0;
        $colorRowClass = '';

        $model = new InboundForm();
        $model->scenario = 'ClearProductInBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->deleteProductFromBox();
            $inboundModel = $model->updateInboundOrder();
            $inboundOrderItemModel = $model->updateInboundOrderItem();
            $model->updateConsignmentInboundOrders();

            $expected_qty = $inboundModel->expected_qty;
//            $countValue = $inboundOrderItemModel->accepted_qty;
//            $rowId = $inboundOrderItemModel->id.'-'.$model->product_barcode;

//            $colorRowClass = 'alert-danger';
//            if ($inboundOrderItemModel->accepted_qty == $inboundOrderItemModel->expected_qty) {
//                $colorRowClass = 'alert-success';
//            } elseif ($inboundOrderItemModel->accepted_qty > $inboundOrderItemModel->expected_qty) {
//                $colorRowClass = 'alert-warning';
//            }

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors'=>$errors,
            'messages'=>$messages,
            'countProductInBox'=>$model->getScannedProductInBox(),
            'countScannedProductInOrder'=>$model->getAllScannedProductInOrder(),
            'expectedQtyParty'=>$expectedQtyParty,
            'acceptedQtyParty'=>$acceptedQtyParty,
            'expected_qty'=> $expected_qty,
//            'dataScannedProductByBarcode'=> [
//                'rowId'=>$rowId,
//                'countValue'=> $countValue,
//                'colorRowClass'=> $colorRowClass
//            ],
        ];
    }

    /*
     * Clear all product in box
     * @param string $box_barcode Box barcode
     * */
    public function actionClearBox() // +
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $dataScannedProductByBarcode = [];
        $expected_qty = 0;
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;

        $model = new InboundForm();
        $model->scenario = 'ClearBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $dataScannedProductByBarcode = $model->updateInboundOrderItems();
            $inboundModel = $model->updateInboundOrder();
            $model->updateConsignmentInboundOrders();

            $expected_qty = $inboundModel->expected_qty;

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors'=>$errors,
            'messages'=>$messages,
            'countScannedProductInOrder'=>$model->getAllScannedProductInOrder(),
            'expected_qty'=> $expected_qty,
            'dataScannedProductByBarcode'=> $dataScannedProductByBarcode,
            'expectedQtyParty'=>$expectedQtyParty,
            'acceptedQtyParty'=>$acceptedQtyParty,
        ];
    }

    /*
     * Upload inbound confirm
     *
     * */
    public function actionUploadFileConfirm()
    { // upload-file-confirm
        $unique_key = Yii::$app->request->post('unique_key');
        $client_id = Yii::$app->request->post('client_id');

        $arrayToSaveCSVFile = InboundUploadLog::find()->where(['delivery_type' => InboundOrder::DELIVERY_TYPE_RPT, 'client_id' => $client_id, 'unique_key' => $unique_key])->asArray()->all();

        if(!empty($arrayToSaveCSVFile) && is_array($arrayToSaveCSVFile)) {

            $client_id = 2;// DeFacto;
            $expectedQty = 0;
            $inboundModelID = 0;
            foreach ($arrayToSaveCSVFile as $key => $productBarcode) {

                if ($key < 1) {

                    if (!($inboundModel = InboundOrder::findOne(['client_id' => $client_id, 'order_number' => $productBarcode['order_number']]))) {
                        $inboundModel = new InboundOrder();
                    }

                    $inboundModel->client_id = $client_id;
                    $inboundModel->order_number = $productBarcode['order_number'];
                    $inboundModel->status = Stock::STATUS_INBOUND_NEW;
                    $inboundModel->expected_qty = '0';
                    $inboundModel->accepted_qty = '0';
                    $inboundModel->accepted_number_places_qty = '0';
                    $inboundModel->expected_number_places_qty = '0';
                    $inboundModel->order_type = InboundOrder::ORDER_TYPE_INBOUND;
                    $inboundModel->save(false);

                    $inboundModelID = $inboundModel->id;
                }

//                if (!($ioi = InboundOrderItem::findOne(['inbound_order_id' => $inboundModelID, 'product_barcode' => $productBarcode['product_barcode'], 'expected_qty' => $productBarcode['expected_qty']]))) {
                if (!($ioi = InboundOrderItem::findOne(['inbound_order_id' => $inboundModelID, 'product_barcode' => $productBarcode['product_barcode']]))) {
                    $ioi = new InboundOrderItem();
                }

                $ioi->inbound_order_id = $inboundModelID;
                $ioi->product_barcode = $productBarcode['product_barcode'];
                $ioi->product_model = $productBarcode['product_model'];
                $ioi->expected_qty = $productBarcode['expected_qty'];
                $ioi->status = Stock::STATUS_INBOUND_NEW;
                $ioi->save(false);

                $expectedQty += $ioi->expected_qty;

                Stock::deleteAll(['client_id' => $client_id, 'inbound_order_id' => $ioi->inbound_order_id, 'product_barcode' => $ioi->product_barcode, 'product_model' => $ioi->product_model]);

                for ($i = 1; $i <= $ioi->expected_qty; $i++) {

                    $stock = new Stock();
                    $stock->client_id = $client_id;
                    $stock->inbound_order_id = $ioi->inbound_order_id;
                    $stock->product_barcode = $ioi->product_barcode;
                    $stock->product_model = $ioi->product_model;
                    $stock->status = Stock::STATUS_INBOUND_NEW;
                    $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
                    $stock->save(false);
                }
            }

            InboundOrder::updateAll(['expected_qty' => $expectedQty], ['id' => $inboundModelID]);
        }
//        $inboundModel->expected_qty = $expectedQty;
//        $inboundModel->save(false);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'ok'=>'ok'
        ];
    }

    /*
     * Confirm Data from API for only client "DeFacto"
     * @return JSON true or errors array
     * TODO NOT USED
     * */
    public function actionDownloadFromApi_NOT_SED_TO_REMOVE()
    { // download-from-api
        $model = new LoadFromDeFactoAPIForm();

        $clientsArray = Client::getActiveItems();

        $model->scenario = 'DownloadFileForAPI';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $dirPath = 'uploads/de-facto/inbound/download/'.date('Ymd').'/'.date('His');
            BaseFileHelper::createDirectory($dirPath);

            $rows[] = [
                'YurtDisiIrsaliyeNo',
                'Barkod',
                'CrossDockType',
                'Miktar',
            ];

            if($inbound = InboundOrder::findOne($model->invoice_number) ) {
                if($items = InboundOrderItem::findAll(['inbound_order_id'=>$model->invoice_number]) ) {
                    foreach($items as $item) {
                        if($item->accepted_qty >= 1) {
                            $rows[] = [
                                $inbound->order_number,
                                $item->product_barcode,
                                'P',
                                $item->accepted_qty,
                            ];
                        }
                    }
                }

//                $inbound->status = InboundOrder::STATUS_COMPLETE_BY_API;
                $inbound->status = Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API;
                $inbound->save(false);
            }

            $fileName = 'inbound-file-download-for-api-'.time().'.csv';

            $fp = fopen($dirPath.'/'.$fileName, 'w');

            foreach ($rows as $fields) {
                fputcsv($fp, $fields,';');
            }

            fclose($fp);

            return Yii::$app->response->sendFile($dirPath.'/'.$fileName);
        }

        return $this->render('download-from-api', [
            'model' => $model,
            'clientsArray' => $clientsArray,
        ]);
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
    { // check-order-status
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


    /*
     *
     *
     * */
    public function actionPrintAcceptedBox()
    {
        $id = Yii::$app->request->get('inbound_id');

        $items = [];
        if($io = InboundOrder::findOne($id)) {
            $items = Stock::find()
                ->select('primary_address, secondary_address')
                ->where([
                    'inbound_order_id' => $io->id,
//                    'secondary_address' => '',
                ])
                ->andWhere([
                    'not', ['primary_address'=>'']
                ])
                ->groupBy('primary_address, secondary_address')
                ->orderBy([
                    'secondary_address' => SORT_DESC,
                    'primary_address' => SORT_DESC,
                ])
                ->asArray()
                ->all();

        }
/*        if($this->printType == 'html'){
            Yii::$app->layout = 'print-html';
            return $this->render('print/print-unallocated-box-html',['items'=>$items]);
        }*/

        return $this->render('print/print-accepted-box-pdf',['items'=>$items]);
    }

    /*
    * Confirm inbound order data
    * @return JSON true or errors array
    * */
    public function actionConfirmOrder() // + Ok
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];

        $model = new InboundForm();
        $model->scenario = 'ConfirmOrder';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($io = InboundOrder::findOne($model->order_number)) {

                if($io->status == Stock::STATUS_INBOUND_CONFIRM || $io->status == Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API) {
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

                    if($coi = ConsignmentInboundOrders::findOne($io->consignment_inbound_order_id)) {
                        $coi->status = Stock::STATUS_INBOUND_SCANNING;
                        if(!InboundOrder::find()->andWhere('status != :status AND consignment_inbound_order_id = :consignment_inbound_order_id',[':status'=>Stock::STATUS_INBOUND_CONFIRM,':consignment_inbound_order_id'=>$io->consignment_inbound_order_id])->exists()) {
                            $coi->status = Stock::STATUS_INBOUND_CONFIRM;
                        }
                        $coi->save(false);
                    }


                    //S: отпарвляем данные INBOUND накладной по API для DeFacto
                    // с 10,02,2020 заказы будут отправлятся по апи через консоль php yii defacto/send-in-bound-feed-back-data
                    if(false) { // id 2 = Defacto
                    //if($io->client_id == 2 && YII_ENV == 'prod' && 0) { // id 2 = Defacto

                        if($items = InboundOrderItem::findAll(['inbound_order_id'=>$io->id]) ) {
                            $row = [];
                            $api = new DeFactoSoapAPIV2Manager();
                            foreach($items as $item) {
                                $rows['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackData($item,$io->order_number);
                                if(!empty($rows['InBoundFeedBackThreePLResponse'])) {
                                    //$api->SendInBoundFeedBackData($rows);
                                }
                            }
                        }

                        //START Принимаем сразу и CROSS-DOCK если он есть
//                        if ($crossDocks = CrossDock::findAll(['internal_barcode' => $io->client_id.'-'.$io->parent_order_number,'party_number'=>$io->order_number])) {
//                            foreach($crossDocks as $crossDock) {
//                                if ($crossDockItems = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id])) {
//                                    foreach ($crossDockItems as $crossDockItem) {
//                                        $row['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataCrossDock($crossDockItem);
//                                        if (!empty($row['InBoundFeedBackThreePLResponse'])) {
//                                            $api = new DeFactoSoapAPIV2Manager();
//                                            $api->SendInBoundFeedBackData($row);
//                                        } else {
//                                            file_put_contents("InBoundFeedBackThreePLResponse-CRoss-dock-ERROR.txt",print_r($row,true)."\n"."\n",FILE_APPEND);
//                                        }
//                                    }
//                                }
//                            }
//                        }
                        // END

                        if(!empty($rows)) {

                            $extraFields = [];
                            $extraFields['SendInBoundFeedBackData'] = $rows;
                            $io->extra_fields = Json::encode($extraFields);
                            $io->status = Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API;
                            $io->save(false);
                        }
                    }
                    //E: отпарвляем данные приходной накладной по API для DeFacto

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



    /*
    * Confirm inbound order data
    * @return JSON true or errors array
    * */
    public function actionConfirmOrder_OLD() // + Ok Это уже не используется
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];

        $model = new InboundForm();
        $model->scenario = 'ConfirmOrder';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($io = InboundOrder::findOne($model->order_number)) {

                if($io->status == Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API) {
                    $messages [] = Yii::t('inbound/errors','Накладная с номером ' . $io->order_number . ' уже принята');
                } else {
                    $io->status = Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API;
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

                    if($coi = ConsignmentInboundOrders::findOne($io->consignment_inbound_order_id)) {
                        $coi->status = Stock::STATUS_INBOUND_SCANNING;
                        if(!InboundOrder::find()->andWhere('status != :status AND consignment_inbound_order_id = :consignment_inbound_order_id',[':status'=>Stock::STATUS_INBOUND_CONFIRM,':consignment_inbound_order_id'=>$io->consignment_inbound_order_id])->exists()) {
                            $coi->status = Stock::STATUS_INBOUND_CONFIRM;
                        }
                        $coi->save(false);
                    }


                    //S: отпарвляем данные INBOUND накладной по API для DeFacto
                    if($io->client_id == 2 && YII_ENV == 'prod' && false) { // id 2 = Defacto

                        if($items = InboundOrderItem::findAll(['inbound_order_id'=>$io->id]) ) {
                            $row = [];
                            $api = new DeFactoSoapAPIV2Manager();
                            foreach($items as $item) {
                                $rows['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackData($item,$io->order_number);
                                if(!empty($rows['InBoundFeedBackThreePLResponse'])) {
                                    //$api->SendInBoundFeedBackData($rows);
                                }
                            }
                        }

                        //START Принимаем сразу и CROSS-DOCK если он есть
//                        if ($crossDocks = CrossDock::findAll(['internal_barcode' => $io->client_id.'-'.$io->parent_order_number,'party_number'=>$io->order_number])) {
//                            foreach($crossDocks as $crossDock) {
//                                if ($crossDockItems = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id])) {
//                                    foreach ($crossDockItems as $crossDockItem) {
//                                        $row['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataCrossDock($crossDockItem);
//                                        if (!empty($row['InBoundFeedBackThreePLResponse'])) {
//                                            $api = new DeFactoSoapAPIV2Manager();
//                                            $api->SendInBoundFeedBackData($row);
//                                        } else {
//                                            file_put_contents("InBoundFeedBackThreePLResponse-CRoss-dock-ERROR.txt",print_r($row,true)."\n"."\n",FILE_APPEND);
//                                        }
//                                    }
//                                }
//                            }
//                        }
                        // END

                        if(!empty($rows)) {

                            $extraFields = [];
                            $extraFields['SendInBoundFeedBackData'] = $rows;
                            $io->extra_fields = Json::encode($extraFields);
                            //$io->status = Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API;
                            $io->save(false);
                        }
                    }
                    //E: отпарвляем данные приходной накладной по API для DeFacto

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

    public function actionClearZeroQty()
    { // clear-zero-qty
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';
        $actionLogic = '';

        $id = Yii::$app->request->post('inbound_id');

        if($inbound = InboundOrder::findOne($id)) {
            InboundOrderItem::deleteAll(['inbound_order_id'=>$inbound->id,'expected_qty'=>'0','accepted_qty'=>'0']);
        }
       // sleep(10);

        return [
            'success'=>'OK',
            'actionLogic'=>$actionLogic,
            'errors'=>$errors,
            'messages'=>$messages,
        ];

    }

    public function actionQtyAcceptedBox() {
        // /qty-accepted-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new InboundForm();

        $items = 0;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if($io = InboundOrder::findOne($model->order_number)) {
                $items = Stock::find()
                    ->select('primary_address, secondary_address')
                    ->where([
                        'inbound_order_id' => $io->id,
                    ])
                    ->andWhere([
                        'not', ['primary_address'=>'']
                    ])
                    ->groupBy('primary_address, secondary_address')
                    ->orderBy([
                        'secondary_address' => SORT_DESC,
                        'primary_address' => SORT_DESC,
                    ])
//                ->asArray()
                    ->count();

            }
        }

        return [
            'success'=> 1,
            'qtyAcceptedBox'=> $items,
        ];
    }

    public function actionItemsInOrder() {
        // /items-in-order

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new InboundForm();

        $items = [];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if($io = InboundOrder::findOne($model->order_number)) {

//                $timer = '0';
//                if( $io = InboundOrder::findOne($id)) {
                    $items = $io->getOrderItems()->select('*,(expected_qty - accepted_qty) as order_by')->orderBy(new Expression('box_barcode, order_by!=0 DESC'))->asArray()->all();

//                    $timer = KpiSetting::getInboundScanningTime($io->client_id, $io->expected_qty - $countScannedProductInOrder);
//                }

//                return [
//                    'message' => 'Success',
//                    'countScannedProductInOrder' => $countScannedProductInOrder,
//                    'expected_qty' => $io->expected_qty,
//                    'cdTimer' => $timer,

//                ];

            }
        }

        return [
            'success'=> 1,
            'items' =>$this->renderPartial('_order_items',['items'=>$items]),
        ];
    }
}