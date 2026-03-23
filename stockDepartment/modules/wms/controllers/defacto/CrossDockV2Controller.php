<?php

namespace app\modules\wms\controllers\defacto;

use common\components\BarcodeManager;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use Yii;
use yii\db\Query;
use yii\helpers\BaseFileHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\Model;

use common\modules\crossDock\models\ConsignmentCrossDock;
use common\modules\crossDock\models\CrossDockItemProducts;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\client\models\Client;
use common\modules\crossDock\models\CrossDock;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;

use stockDepartment\modules\wms\models\defacto\ApplyExpAccQtyCrossDockForm;
use stockDepartment\modules\wms\models\defacto\CreateCrossDockForm;
use stockDepartment\modules\wms\models\defacto\OutboundCrossDockForm;
use stockDepartment\modules\wms\models\defacto\SearchByProductCrossDockForm;
use stockDepartment\modules\wms\models\defacto\GenerateCrossDockForm;
use stockDepartment\modules\wms\models\defacto\AddItemCrossDockForm;
use stockDepartment\modules\wms\models\defacto\ConfirmCrossDockForm;


class CrossDockV2Controller extends \stockDepartment\components\Controller
{
    /*
     * Index page
     * @return mixed
     * */
    public function actionIndex()
    {
        $clientsArray = Client::getActiveItems();
        return $this->render('index',[
            'clientsArray' => $clientsArray,
        ]);
    }

    /*
     * Generate Cross dock picking list
     * @return mixed
     * */
    public function actionGenerateCrossDock()
    {
        $formModel = new GenerateCrossDockForm();
        $clientsArray = Client::getActiveItems();

        return $this->renderAjax('generate-cross-dock', [
            'formModel' => $formModel,
            'clientsArray' => $clientsArray,
        ]);
    }

    /*
     * Show form for confirmation cross dock
     * items actual qty
     * @return mixed
     * */
    public function actionConfirmCrossDock()
    {
        $formModel = new ConfirmCrossDockForm();
        $applyQtyForm = new ApplyExpAccQtyCrossDockForm();

        $crossOrders = [];
        if ($formModel->load(Yii::$app->request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;

            if($formModel->validate()) {
                $crossOrders = CrossDock::find()
                    ->andWhere([
                        'internal_barcode' => $formModel->cross_dock_barcode,
                        'status' => [
                            Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST,
                            Stock::STATUS_CROSS_DOCK_SCANNING,
                            Stock::STATUS_CROSS_DOCK_SCANNED,
                            Stock::STATUS_CROSS_DOCK_SCANNED,
                           28,
                        ]
                    ])
                    ->all();

                $data = [
                    'success' => '1',
                    'errors' => '',
                    'subTable' => $this->renderAjax('_cross-dock-table', ['crossOrders' => $crossOrders,'applyQtyForm'=>$applyQtyForm])
//                    'subTable' => $this->renderPartial('_cross-dock-table', ['crossOrders' => $crossOrders,'applyQtyForm'=>$applyQtyForm])
                ];

                return $data;
                //VarDumper::dump($data, 10, true); die;

            } else {
                $errors = $formModel->getErrors('cross_dock_barcode');
                return [
                    'success' => (empty($errors) ? '1' : '0'),
                    'errors' => $errors
                ];
            }
        }
        return $this->renderAjax('confirm-cross-dock', ['formModel' => $formModel,'crossOrders' => $crossOrders]);
    }

//    /*
//     * Show form for confirmation cross dock
//     * items actual qty
//     * @return mixed
//     * */
//    public function actionCrossDockPickingListHandler()
//    {
//        $formModel = new ConfirmCrossDockForm();
//        $crossOrders = [];
//        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
//            $crossOrders = CrossDock::find()
//                ->andWhere([
//                    'internal_barcode' => $formModel->cross_dock_barcode,
//                    'status' => Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST
//                ])
//                ->all();
//
//        }
//        return $this->renderAjax('confirm-cross-dock', ['formModel' => $formModel,'crossOrders' => $crossOrders]);
//    }

    /*
     * Set actual item qty
     * @param array data
     * @return mixed
     **/
    public function actionApplyQty()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($data = Yii::$app->request->post('ApplyExpAccQtyCrossDockForm')) {
            foreach ($data as $cdID=>$value){
                if($record = CrossDock::findOne($cdID)) {

                    $record->accepted_number_places_qty = $value['accepted_number_places_qty'];
                    $record->expected_number_places_qty = $value['expected_number_places_qty'];
                    $record->box_m3 = $value['box_m3'];
                    $record->status = Stock::STATUS_CROSS_DOCK_COMPLETE;
                    $record->addAcceptedDateTime();

                    if($record->save(false)) {
                        $record->createDeliveryProposal();
                        $this->sendCompleteCrossDockAPI($record->id);
                    }

                    if($cCD = ConsignmentCrossDock::findOne($record->consignment_cross_dock_id)) {
                        $cCD->accepted_number_places_qty += $record->accepted_number_places_qty;
                        $cCD->expected_number_places_qty += $record->expected_number_places_qty;
                        $cCD->status = Stock::STATUS_CROSS_DOCK_COMPLETE;
                        $cCD->save(false);
                    }
                }
            }
            //Yii::$app->getSession()->setFlash('success', Yii::t('inbound/messages', 'Сборочный лист успешно подтвержден'));
        }

        return [
          'success' => '1',
          'message' => Yii::t('inbound/messages', 'Сборочный лист успешно подтвержден'),
        ];
    }

    /*
     *
     * */
    public function actionApplyByOneShop()
    {
        // wms/defacto/cross-dock-v2/apply-by-one-shop?id=3785
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->get('id');
        $messageStatus = 'next';
        if ($data = Yii::$app->request->post('ApplyExpAccQtyCrossDockForm')) {
            foreach ($data as $cdID=>$value) {
                if($cdID == $id) {
                    if ($crossDock = CrossDock::findOne($cdID)) {

                        $crossDock->accepted_number_places_qty = $value['accepted_number_places_qty'];
                        $crossDock->expected_number_places_qty = $value['expected_number_places_qty'];
                        $crossDock->box_m3 = $value['box_m3'];
                        $crossDock->status = Stock::STATUS_CROSS_DOCK_COMPLETE;
                        $crossDock->addAcceptedDateTime();


                        if ($crossDock->save(false)) {
                            $crossDock->createDeliveryProposal();
                            $this->sendCompleteCrossDockAPI($crossDock->id);

                        }

                        if ($cCD = ConsignmentCrossDock::findOne($crossDock->consignment_cross_dock_id)) {
                            $cCD->accepted_number_places_qty += $crossDock->accepted_number_places_qty;
                            $cCD->expected_number_places_qty += $crossDock->expected_number_places_qty;

                            if( CrossDock::find()->andWhere(['consignment_cross_dock_id'=>$crossDock->consignment_cross_dock_id,'status'=>Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST])->count() < 1) {
                                $cCD->status = Stock::STATUS_CROSS_DOCK_COMPLETE;
                                $messageStatus = 'end';
                            }

                            $cCD->save(false);
                        }
                    }
                }
            }
        }


        return [
            'success' => '1',
            'messageStatus' => $messageStatus,
            'message' => Yii::t('inbound/messages', 'Сборочный лист успешно подтвержден'),
        ];
    }


    private function sendCompleteCrossDockAPI($crossDockID) {

        if(YII_ENV == 'prod') {
            $api = new DeFactoSoapAPIV2Manager();
            if ($crossDocks = CrossDock::findAll(['id' => $crossDockID, 'client_id' => Client::CLIENT_DEFACTO])) {
                foreach ($crossDocks as $crossDock) {
                    $row = [];
                    $rows = [];
                    if ($crossDockItems = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id])) {
                        foreach ($crossDockItems as $crossDockItem) {
                            $rowTmp = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataCrossDock($crossDockItem);
                            $rows[] = array_shift($rowTmp);
                        }
                    }
                    $row['InBoundFeedBackThreePLResponse'] = $rows;
                    if (!empty($rows)) {
                        $api->SendInBoundFeedBackData($row);
                        file_put_contents("SendInBoundFeedBackData-CRoss-dock-" . $crossDock->party_number . ".log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
                    } else {
                        file_put_contents("SendInBoundFeedBackData-CRoss-dock-ERROR.log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
                    }
                }
            }

            // второй шаг.
            if ($crossDocks = CrossDock::findAll(['id' => $crossDockID, 'client_id' => Client::CLIENT_DEFACTO])) {
                foreach ($crossDocks as $crossDock) {
                    $row = [];
                    $rows = [];
                    if ($items = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id, 'status' => Stock::STATUS_CROSS_DOCK_SCANNED])) {
                        foreach ($items as $item) {
                            $rowTmp = DeFactoSoapAPIV2Manager::preparedSendCrossDockOutBoundFeedBackDataOutbound($item, $crossDock);
                            $rows[] = array_shift($rowTmp);
                        }
                    }
                    $row['OutBoundFeedBackThreePLResponse'] = $rows;
                    if (!empty($rows)) {
                        $api->SendOutBoundCrossDockFeedBackData($row);
                        file_put_contents("SendOutBoundCrossDockFeedBackData-" . $crossDock->party_number . ".log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
                    } else {
                        file_put_contents("SendOutBoundCrossDockFeedBackData-CRoss-dock-ERROR.log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
                    }
                }
            }
        }
    }

    /*
     * Get cross dock orders group by party number by client
     * @param integer client_id
     * @return JSON
     * */
    public function actionGetCrossDockOrdersByClientId()
    {
        $clientID = Yii::$app->request->post('client_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = ['' => ''];
        $data += CrossDock::getCrossDockListByClientID($clientID);
        return [
            'message' => 'Success',
            'dataOptions' => $data,
        ];
    }

    /*
     * Generate Cross dock list PDF file
     * @param integer client_id
     * @param string party_number
     * @return mixed
     *
     * */
    public function actionPrintCrossDockList()
    {
        $client_id = \Yii::$app->request->get('client_id');
        $party_number = \Yii::$app->request->get('party_number');
        $barcode = '';
        $rptQty = '';

        if($client_id && $party_number) {

            if($cCD = ConsignmentCrossDock::findOne(['client_id'=>$client_id,'party_number'=>$party_number])) {
                $rptQty = $cCD->expected_rpt_places_qty;
            }


            $client = Client::findOne($client_id);
            $crossOrders = CrossDock::find()
                ->andWhere([
                    'party_number'=>$party_number,
                    'client_id'=>$client_id,
                    'status'=>[
                            Stock::STATUS_CROSS_DOCK_NEW,
                            Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST,
                            Stock::STATUS_CROSS_DOCK_SCANNING,
                            Stock::STATUS_CROSS_DOCK_SCANNED,
                    ],
                ])
                ->all();
            if($crossOrders){
                foreach ($crossOrders as  $co){
                    $co->status = Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST;
                    $co->assignBarcode();
                    $co->save(false);
                    $barcode = $co->internal_barcode;
                }

                $cCD->status = Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST;
                $cCD->save(false);
            }
        }

//        if($this->printType == 'html'){
//            Yii::$app->layout = 'print-html';
//            return $this->render('print/print-crossdoc-list-html',['rptQty'=>$rptQty,'crossOrders' => $crossOrders, 'client' => $client, 'barcode'=> $barcode]);
//        }

        return $this->render('print/print-cross-doc-list-pdf', ['rptQty'=>$rptQty,'crossOrders' => $crossOrders, 'client' => $client, 'barcode'=> $barcode]);
    }

    /*
    * Add new item cross dock
    * @return mixed
    * */
    public function actionAddNewItemCrossDock()
    {
        $formModel = new AddItemCrossDockForm();
        $clientsArray = Client::getActiveItems();
        $route_to = TLHelper::getStockPointArray(\common\modules\client\models\Client::CLIENT_DEFACTO,true);

        return $this->renderAjax('add-new-item-cross-dock', [
            'formModel' => $formModel,
            'clientsArray' => $clientsArray,
            'route_to' => $route_to,
        ]);
    }

    /*
    * Create new cross dock
    * @return mixed
    * */
    public function actionCreateCrossDockForm()
    {
        $client_id = 2;
        $formModel = new CreateCrossDockForm();
        $clientsArray = Client::getActiveItems();

        $stores = Store::find()
            ->andWhere(['type_use'=>[Store::TYPE_USE_STORE],'client_id'=>[$client_id]])
            ->andWhere('shop_code != ""')
            ->orWhere('id=4')
//            ->andWhere('shop_code2 != "" AND shop_code2 != "-" AND shop_code2 != "0" ')
            ->all();

        return $this->renderAjax('create-cross-dock-form', [
            'formModel' => $formModel,
            'clientsArray' => $clientsArray,
            'stores' => $stores,
        ]);
    }

    public function actionSaveCreateCrossDockForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $message = '';
        if ($data = Yii::$app->request->post('CreateCrossDockForm')) {

            $party_number = $data['order_number'];
            $client_id = $data['client_id'];

            $cCD = new ConsignmentCrossDock();
            $cCD->client_id = $client_id;
            $cCD->party_number = $party_number;
            $cCD->expected_rpt_places_qty = 0;
            $cCD->expected_number_places_qty = 0;
            $cCD->status = Stock::STATUS_CROSS_DOCK_NEW;;
            $cCD->save(false);

            foreach ($data as $storeId=>$value) {
                if(!empty($value['expected_number_places_qty']) && !in_array($storeId,['client_id','order_number']) && $store = Store::findOne($storeId) ) {
                    $newCrossDock = new CrossDock();
                    $newCrossDock->client_id = $client_id;
                    $newCrossDock->party_number = $party_number;
                    $newCrossDock->consignment_cross_dock_id = $cCD->id;
                    $newCrossDock->from_point_id = 4;
                    $newCrossDock->to_point_id = $store->id;
                    $newCrossDock->to_point_title = $store->shop_code2;
                    $newCrossDock->from_point_title = 4;
                    $newCrossDock->internal_barcode = $client_id . '-' . $party_number;
                    $newCrossDock->status = Stock::STATUS_CROSS_DOCK_NEW;
                    $newCrossDock->expected_number_places_qty = $value['expected_number_places_qty'];
                    $newCrossDock->box_m3 = $value['box_m3'];
                    if ($newCrossDock->save(false)) {
                        for($i=1; $i<= $newCrossDock->expected_number_places_qty; $i++) {
                            $crossDockItem = new CrossDockItems();
                            $crossDockItem->cross_dock_id = $newCrossDock->id;
                            $crossDockItem->box_barcode = 'BOX0'.$i;
                            $crossDockItem->expected_number_places_qty = 1;
                            $crossDockItem->box_m3 = 0.096;
                            $crossDockItem->weight_net = 0;
                            $crossDockItem->weight_brut = 0;
                            $crossDockItem->save(false);
                        }
//                        $newCrossDock->createDeliveryProposal();
                    }

                    if($cCD = ConsignmentCrossDock::findOne($newCrossDock->consignment_cross_dock_id)) {
                        $cCD->expected_number_places_qty += $newCrossDock->expected_number_places_qty;
                        $cCD->status = Stock::STATUS_CROSS_DOCK_NEW;
                        $cCD->save(false);
                    }

                }
            }
            $message = 'Кросс-док успешно создан';
            Yii::$app->session->setFlash('success', $message);
        }

        return [
            'success' => '1',
            'message' => $message,
        ];
    }

    /*
     *
     * */
    public function actionPreviewCrossDockForm()
    {
        $rptQty = 0;
        $barcode = '';
        $crossOrders = [];
        $client = '';
        if ($data = Yii::$app->request->post('CreateCrossDockForm')) {
            $party_number = $data['order_number'];
            $client_id = $data['client_id'];
            $client = Client::findOne($client_id);
            $barcode = $party_number;

            foreach ($data as $storeId=>$value) {
                if(!empty($value['expected_number_places_qty']) && !in_array($storeId,['client_id','order_number']) && $store = Store::findOne($storeId) ) {
                    $crossOrders[] = [
                        'expected_number_places_qty'=>$value['expected_number_places_qty'],
                        'box_m3'=>$value['box_m3'],
                        'store'=> $store->getPointTitleByPattern('{shopping_center_name} / {city_name}'),
                    ];
                }
            }

        }

        return $this->render('print/preview-crossdoc-pdf', [
            'rptQty'=>$rptQty,
            'crossOrders' => $crossOrders,
            'client' => $client,
            'barcode'=> $barcode]
        );
    }

    /*
    * Save new item cross dock
    * @return mixed
    * */
    public function actionSaveNewItemCrossDock()
    {
        $formModel = new AddItemCrossDockForm();
        $message = '';
        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            if($cd = CrossDock::find()->where(['client_id'=>$formModel->client_id,'party_number'=>$formModel->order_number])->one()) {
                $from_point_id = 4;
                $to_point_title = '';
                $to_point_id = $from_point_title = $formModel->route_to;

                if($s = Store::find()->where(['id'=>$formModel->route_to])->one()) {
                    $to_point_title = $s->shop_code2;
                }

                $newCrossDock = new CrossDock();
                $newCrossDock->client_id = $cd->client_id;
                $newCrossDock->party_number = $cd->party_number;
                $newCrossDock->consignment_cross_dock_id = $cd->consignment_cross_dock_id;
                $newCrossDock->from_point_id = $from_point_id;
                $newCrossDock->to_point_id = $to_point_id;
                $newCrossDock->to_point_title = $to_point_title;
                $newCrossDock->from_point_title = $from_point_title;
                $newCrossDock->internal_barcode = $cd->client_id.'-'.$cd->party_number;
                $newCrossDock->status = Stock::STATUS_CROSS_DOCK_NEW;
                $newCrossDock->expected_number_places_qty = $formModel->number_places_qty;
                $newCrossDock->box_m3 = $formModel->box_m3;
                if ($newCrossDock->save(false)) {
                    for($i=1; $i<= $newCrossDock->expected_number_places_qty; $i++) {
                        $crossDockItem = new CrossDockItems();
                        $crossDockItem->cross_dock_id = $newCrossDock->id;
                        $crossDockItem->box_barcode = 'BOX0'.$i;
                        $crossDockItem->expected_number_places_qty = 1;
                        $crossDockItem->box_m3 = 0.096;
                        $crossDockItem->weight_net = 0;
                        $crossDockItem->weight_brut = 0;
                        $crossDockItem->save(false);
                    }
//                        $newCrossDock->createDeliveryProposal();
                }

                if($cCD = ConsignmentCrossDock::findOne($newCrossDock->consignment_cross_dock_id)) {
                    $cCD->expected_number_places_qty += $newCrossDock->expected_number_places_qty;
                    $cCD->status = Stock::STATUS_CROSS_DOCK_NEW;
                    $cCD->save(false);
                }

                $message = 'Магазин успешно дабавлен. Можите распечатать CROSS-DOCK сборочный лист';
                Yii::$app->session->setFlash('success', $message);
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = $formModel->getErrors();
        return [
            'message' => $message,
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }

    /*
    * Get list for print box barcode
    * @return mixed
    * */
    public function actionGetCrossDocOrders()
    {
        $formModel = new GenerateCrossDockForm();
        $clientID = Yii::$app->request->post('client_id');
//        $orderListData = CrossDock::getCrossDockListByClientID($clientID);
        $orderListData = CrossDock::getCrossDockCompleteListByClientID($clientID);

        return $this->renderAjax('get-cross-dock-orders', [
            'formModel' => $formModel,
            'orderListData' => $orderListData,
        ]);
    }

    /*
    * Get list for print box barcode
    * @return mixed
    * */
    public function actionGetCrossDockOrderList()
    {
        $clientID = Yii::$app->request->post('client_id');
        $parentOrderNumber = Yii::$app->request->post('parent_order_number');

        $query = CrossDock::find()
            ->andWhere([
                'client_id' => $clientID,
                'party_number' => $parentOrderNumber,
//                'status' => Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST
            ]);

        $dataProvider = new ActiveDataProvider(
            [
                'query'=>$query,
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]
        );

        return $this->renderAjax('_get-cross-dock-order-list', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
     *
     * */
    public function actionPrintLabelBoxBarcode()
    {
        $id = Yii::$app->request->get('id');
        $model = new \stdClass();
        if($orderModel = CrossDock::findOne($id)) {
            $deliveryOrder = TlDeliveryProposalOrders::find()->andWhere([
                'client_id' => $orderModel->client_id,
                'order_id' => $orderModel->id,
                'order_type' => TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK,
            ])->one();

            $model = TlDeliveryProposal::findOne($deliveryOrder->tl_delivery_proposal_id);

//            if($crossDockItems = CrossDockItems::find()->where(['cross_dock_id'=>$orderModel->id])->all()) {
//              foreach($crossDockItems as $itemBox) {
//                  $boxes[] = [
//                        'order_number' => $itemBox['box_barcode']
//                    ];
//              }
//            } else {
            for($i = 1;$i <= $orderModel->accepted_number_places_qty; $i++ ) {
                $boxes[] = [
                    'order_number' => 'BOX-0'.$i
                ];
            }
//            }
        }

        return $this->render('print/box-label-pdf',['model'=>$model,'orderModel'=>$orderModel,'boxes'=>$boxes]);
    }

    /*
     *
     * */
    public function actionOutboundForm()
    {
       $formModel =  new OutboundCrossDockForm();

        return $this->renderAjax('outbound-form',['formModel'=>$formModel]);
    }

    /*
    *
    * */
    public function actionInternalBarcodeOutboundForm()
    {
        // 2-559004-05-06-07-08
        $message = '';
        $formModel = new OutboundCrossDockForm();
        $formModel->scenario = 'validateInternalBarcode';

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {

        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = $formModel->getErrors();
        return [
            'message' => $message,
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }

    /*
    *
    * */
    public function actionToStoreOutboundForm()
    {
        // 2-559004-05-06-07-08
        $storeName = '';
        $formModel = new OutboundCrossDockForm();
        $scenario = 'validateToPoint';
        $boxQty = 0;
        $formModel->scenario = $scenario;

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            $storeCode = $formModel->to_point;
            $barcode = $formModel->internal_barcode;

            $storeId = Store::find()->select('id')->andWhere(['client_id'=>2,'type_use'=>Store::TYPE_USE_STORE, 'shop_code' => $storeCode])->scalar();

            $storeName = Store::getPointTitle($storeId);
            $cd = CrossDock::find()->andWhere(['internal_barcode'=>$barcode, 'to_point_id' => $storeId])->one();
            $boxQty = $cd->accepted_number_places_qty.' / '.$cd->expected_number_places_qty;

        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = $formModel->getErrors();

        return [
            'storeName' => $storeName,
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'boxQty' => $boxQty,
        ];
    }

    /*
    *
    * */
    public function actionScanningOutboundForm()
    {
        // 2-559004-05-06-07-08
        $message = '';
        $formModel = new OutboundCrossDockForm();
        $scenario = 'validateBoxBarcode';
        $boxQty = 0;
        $formModel->scenario = $scenario;
        $client_id = 2;
        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            $storeCode = $formModel->to_point;
            $barcode = $formModel->internal_barcode;
            $box_barcode = $formModel->box_barcode;

            $storeId = Store::find()->select('id')->andWhere(['client_id'=>$client_id,'type_use'=>Store::TYPE_USE_STORE, 'shop_code' => $storeCode])->scalar();
            $cd = CrossDock::find()->andWhere(['internal_barcode'=>$barcode, 'to_point_id' => $storeId])->one();

            $cdItems = CrossDockItems::find()->andWhere(['cross_dock_id'=>$cd->id,'box_barcode'=>$box_barcode])->all();
            foreach($cdItems as $cdItem) {
                $cdItem->status = Stock::STATUS_CROSS_DOCK_SCANNED;
                $cdItem->accepted_number_places_qty = 1;
                $cdItem->save(false);
            }

            $cd->status = Stock::STATUS_CROSS_DOCK_SCANNING;
            $cd->accepted_number_places_qty += 1;

            if($cd->accepted_number_places_qty == $cd->expected_number_places_qty) {
                $cd->status = Stock::STATUS_CROSS_DOCK_SCANNED;
            }
            $cd->save(false);

            $boxQty = $cd->accepted_number_places_qty.' / '.$cd->expected_number_places_qty;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = $formModel->getErrors();
        return [
            'message' => $message,
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'boxQty' => $boxQty,
        ];
    }

    /*
     *
     * */
    public function actionPrintListDifferences()
    {
        $id = Yii::$app->request->get('id');

        $items = [];
        if( $ccd =  CrossDock::findOne($id)) {
            $items = CrossDockItems::find()
                ->andWhere(['cross_dock_id'=>$id])
                ->andWhere('status != :status',[':status'=>Stock::STATUS_CROSS_DOCK_SCANNED])
                ->groupBy('box_barcode')
                ->asArray()
                ->all();
        }

        return $this->render('print/list-differences-pdf',['items'=>$items]);
    }

    /*
     *
     * */
    public function actionScanningBoxSizeCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';

        $model = new OutboundCrossDockForm();
        $model->scenario = 'vScanningBoxSizeCode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $clientId = 2;
            $boxBarcode = $model->box_barcode;
            $internalBarcode = $model->internal_barcode;
            $toPoint = $model->to_point;
            $boxSizeCode = $model->box_size_code;

            $storeId = Store::find()->select('id')->andWhere(['client_id'=>$clientId,'type_use'=>Store::TYPE_USE_STORE, 'shop_code' => $toPoint])->scalar();
            $cd = CrossDock::find()->andWhere(['internal_barcode'=>$internalBarcode, 'to_point_id' => $storeId])->one();

            // Box barcode
            $dirPath = 'log/cross-dock/' . date('Ymd');
            BaseFileHelper::createDirectory($dirPath);
            file_put_contents($dirPath . '/actionScanningBoxSizeCode.log', $boxBarcode.';'.$internalBarcode.';'.$toPoint . ';' . date('Ymd-H:i:s') . "\n", FILE_APPEND);
            file_put_contents($dirPath . '/actionScanningBoxSizeCode-full.log', date('Ymd-H:i:s') . "\n" . "\n", FILE_APPEND);
            file_put_contents($dirPath . '/actionScanningBoxSizeCode-full.log', print_r($model, true) . "\n" . "\n", FILE_APPEND);

            if (BarcodeManager::isM3BoxBorder($boxSizeCode)) {
                $m3BoxValue = BarcodeManager::getBoxM3($boxSizeCode);

                if (empty($m3BoxValue)) {
                    $m3BoxValue = 0.096;
                }

                CrossDockItems::updateAll(['box_m3'=>$m3BoxValue],['cross_dock_id'=>$cd->id,'box_barcode'=>$boxBarcode]);

                $subQuery = (new Query())
                                ->select('id')
                                ->from(CrossDockItems::tableName())
                                ->andWhere(['cross_dock_id'=>$cd->id])
                                ->groupBy('box_barcode')
                                ->column();
                $box_m3 = CrossDockItems::find()->select('sum(box_m3)')->andWhere(['id'=>$subQuery])->scalar();
                if($box_m3) {
                    $cd->box_m3 = round($box_m3, 3);
                    $cd->save(false);
                }

                return [
                    'change_box' => 'ok',
                    'box_m3' => $box_m3,
                    'm3BoxValue' => $m3BoxValue,
                ];
            }
        }
        $errors = $model->getErrors();

        return [
            'change_box' => 'no',
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }


    /*
    *
    *
    * */
    public function actionGetId()
    {
        $formModel = new OutboundCrossDockForm();
        $href = '';

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            $storeCode = $formModel->to_point;
            $barcode = $formModel->internal_barcode;

            $storeId = Store::find()->select('id')->andWhere(['client_id'=>2,'type_use'=>Store::TYPE_USE_STORE, 'shop_code' => $storeCode])->scalar();
            $cd = CrossDock::find()->andWhere(['internal_barcode'=>$barcode, 'to_point_id' => $storeId])->one();
            $href = Url::toRoute(['print-list-differences','id'=>$cd->id]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'href' => $href,
        ];
    }


    /*
    *
    * */
    public function actionInternalBarcodeSearchByProduct()
    {
        // 2-559004-05-06-07-08
        $message = '';
        $formModel = new SearchByProductCrossDockForm();
        $formModel->scenario = 'validateInternalBarcode';

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {

        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = $formModel->getErrors();
        return [
            'message' => $message,
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }

    /*
     *
     * */
    public function actionSearchByProductForm()
    {
        $formModel =  new SearchByProductCrossDockForm();

        return $this->renderAjax('search-by-product-form',['formModel'=>$formModel]);
    }

    /*
    *
    * */
    public function actionSearchBarcodeByProduct()
    {
        $formModel =  new SearchByProductCrossDockForm();
        $formModel->scenario = 'validateProductBarcode';
        $html = '';
        $scannedProducts = '';
        $scannedProductsWhere = [];
        $spwProdBarcodeIDs = [];
        $qtyShops = 0;
        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            $scannedProducts = (empty($formModel->scanned_product_barcodes) ? '' : $formModel->scanned_product_barcodes . ',') .$formModel->product_barcode;
            if (!empty($scannedProducts)) {
                $scannedProducts = trim($scannedProducts, ',');
                $tmp = explode(',', $scannedProducts);
                $scannedProducts = implode(',', $tmp);
                foreach($tmp as $t) {
                    if(isset($scannedProductsWhere[$t])) {
                        $scannedProductsWhere[$t]++;
                    } else {
                        $scannedProductsWhere[$t] = 1;
                    }
                }
            }

            $cdIDs = CrossDock::find()->select('id')->andWhere(['internal_barcode' => $formModel->internal_barcode, 'status' => [
                Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST,
                Stock::STATUS_CROSS_DOCK_SCANNING,
                Stock::STATUS_CROSS_DOCK_SCANNED,
            ]])->column();

            $cdIDsItems = CrossDockItems::find()->select('id')->andWhere(['cross_dock_id' =>$cdIDs])->column();


            foreach($scannedProductsWhere as $spwProdBarcode=>$spwProdBarcodeQty) {

                $qtyProducts = CrossDockItemProducts::find()->select('cross_dock_item_id')->andWhere([

                    'cross_dock_item_id' => $cdIDsItems,
                    'product_barcode' => $spwProdBarcode,
                ])->andWhere('expected_qty >= :expected_qty',[':expected_qty'=>$spwProdBarcodeQty])
                    ->column();

                if( $qtyProducts) {
                    $spwProdBarcodeIDs = empty($spwProdBarcodeIDs) ? $qtyProducts : $spwProdBarcodeIDs;
                    $spwProdBarcodeIDs = array_intersect($qtyProducts, $spwProdBarcodeIDs);
                } else {
                    $spwProdBarcodeIDs = [-1];
                    break;
                }

            }

            $data = CrossDockItems::find()->andWhere([
                'id' =>$spwProdBarcodeIDs
            ])->all();

            $qtyShops = CrossDockItems::find()->andWhere([
                'id' =>$spwProdBarcodeIDs
            ])->count();

            $html = $this->renderPartial('search-barcode-by-product-list',['data'=>$data,'scannedProductsWhere' => $scannedProductsWhere,]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = $formModel->getErrors();
        $scannedProductsShowInGrid = '';
        $scannedProductsShowInGrid .= '<h2>Количество магазинов: '.  $qtyShops."</h2>";
        if(!empty($scannedProductsWhere) && is_array($scannedProductsWhere)) {
            foreach($scannedProductsWhere as $keyProduct=>$valueQty) {
                $scannedProductsShowInGrid .= "<h4>".$keyProduct.' / '.$valueQty."</h4>";
            }
        }


        return [
            'html' => $html,
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'scannedProducts' => $scannedProducts,
            'scannedProductsShowInGrid' => $scannedProductsShowInGrid,
        ];
    }

    /*
     * TODO NOT USED. TODO REMOVE JS HANDLER!
     * */
    public function actionPrintSearchByProductPdf()
    {
        $formModel =  new SearchByProductCrossDockForm();
        $formModel->scenario = 'validateProductBarcode';

        $productBarcode = '';
        $data = [];
        if ($formModel->load(Yii::$app->request->get()) && $formModel->validate()) {
            $html = '';
            $scannedProducts = '';
            $scannedProductsWhere = [];
            $spwProdBarcodeIDs = [];
            $qtyShops = 0;
            if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
                $scannedProducts = (empty($formModel->scanned_product_barcodes) ? '' : $formModel->scanned_product_barcodes . ',') .$formModel->product_barcode;
                if (!empty($scannedProducts)) {
                    $scannedProducts = trim($scannedProducts, ',');
                    $tmp = explode(',', $scannedProducts);
                    $scannedProducts = implode(',', $tmp);
                    foreach($tmp as $t) {
                        if(isset($scannedProductsWhere[$t])) {
                            $scannedProductsWhere[$t]++;
                        } else {
                            $scannedProductsWhere[$t] = 1;
                        }
                    }
                }

                $cdIDs = CrossDock::find()->select('id')->andWhere(['internal_barcode' => $formModel->internal_barcode, 'status' => [
                    Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST,
                    Stock::STATUS_CROSS_DOCK_SCANNING,
                    Stock::STATUS_CROSS_DOCK_SCANNED,
                ]])->column();

                $cdIDsItems = CrossDockItems::find()->select('id')->andWhere(['cross_dock_id' =>$cdIDs])->column();


                foreach($scannedProductsWhere as $spwProdBarcode=>$spwProdBarcodeQty) {

                    $qtyProducts = CrossDockItemProducts::find()->select('cross_dock_item_id')->andWhere([

                        'cross_dock_item_id' => $cdIDsItems,
                        'product_barcode' => $spwProdBarcode,
                    ])->andWhere('expected_qty >= :expected_qty',[':expected_qty'=>$spwProdBarcodeQty])
                        ->column();

                    if( $qtyProducts) {
                        $spwProdBarcodeIDs = empty($spwProdBarcodeIDs) ? $qtyProducts : $spwProdBarcodeIDs;
                        $spwProdBarcodeIDs = array_intersect($qtyProducts, $spwProdBarcodeIDs);
                    } else {
                        $spwProdBarcodeIDs = [-1];
                        break;
                    }

                }

                $data = CrossDockItems::find()->andWhere([
                    'id' =>$spwProdBarcodeIDs
                ])->all();

//                $qtyShops = CrossDockItems::find()->andWhere([
//                    'id' =>$spwProdBarcodeIDs
//                ])->count();

//                $html = $this->renderPartial('search-barcode-by-product-list',['data'=>$data,'scannedProductsWhere' => $scannedProductsWhere,]);
            }

        }

        VarDumper::dump($data,10,true);
        die;

        return $this->renderPartial('print/product-by-box-pdf',['data'=>$data,'productBarcode'=>$productBarcode]);
    }
}