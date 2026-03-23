<?php

namespace app\modules\transportLogistics\controllers;

use clientDepartment\modules\transportLogistics\models\TlDeliveryProposalSearchExport;
use common\components\FailDeliveryStatus\StatusList;
use common\components\MailManager;
use common\helpers\DateHelper;
use common\modules\client\models\ClientEmployees;
use common\modules\codebook\models\Codebook;
use common\modules\crossDock\models\CrossDock;
use common\modules\outbound\models\OutboundOrder;
use common\modules\store\models\StoreReviews;
use Yii;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use clientDepartment\components\Controller;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use clientDepartment\modules\transportLogistics\models\TlDeliveryProposalSearch;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlCars;
use common\modules\user\models\User;
use common\modules\client\models\Client;
use clientDepartment\modules\client\components\ClientManager;
use common\components\DeliveryProposalManager;



/**
 * TlDeliveryProposalController implements the CRUD actions for TlDeliveryProposal model.
 */
class TlDeliveryProposalController extends Controller
{


    /**
     * Lists all TlDeliveryProposal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlDeliveryProposalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $client = ClientManager::getClientEmployeeByAuthUser();
//        $filterWidgetOptionDataRoute= TLHelper::getStoreArrayByClientID($client->client_id,true);
        $filterWidgetOptionDataRoute= TLHelper::getStoreArrayByClientID($client->client_id);

        return $this->render($this->getViewByType('index'), [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
        ]);
    }

    /**
     * Displays a single TlDeliveryProposal model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if(!ClientManager::canViewDeliveryProposal($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $dataProviderProposalOrders = new ActiveDataProvider([
            'query' => $model->getProposalOrders(),
        ]);

        $storeReviewButton1 = '';
        if(ClientManager::canUpdateStoreReview($model)) {
            $storeReviewButton1 = '{update}';
        }




        return $this->render($this->getViewByType('view'), [
            'model' => $model,
            'dataProviderProposalOrders' => $dataProviderProposalOrders,
            'storeReviewButton1' => $storeReviewButton1,
        ]);
    }

    /**
     * Creates a new TlDeliveryProposal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlDeliveryProposal(['scenario'=>'create-on-client']);
        $ce = ClientManager::getClientEmployeeByAuthUser();

        $model->route_from = $ce->store_id;


        if ($model->load(Yii::$app->request->post())) {

            $model->source = $model::SOURCE_CLIENT;

            $model->client_id = ClientManager::getClientEmployeeByAuthUser()->client_id;
            $model->status = TlDeliveryProposal::STATUS_NEW;
            $model->cash_no = TlDeliveryProposal::METHOD_CHAR;
//            $model->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_TRANSFER_MORE_ROUTE;
            $model->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_ONE_ROUTE;
            if ($model->save()) {
                $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
                $dpManager->onCreateProposal();

                $bm = new MailManager();
                $bm->sendMailToStockIfClientCreateNewDP($model);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $client = ClientManager::getClientEmployeeByAuthUser();
        $storeArray= TLHelper::getStoreArrayByClientID($client->client_id,true);

        return $this->render($this->getViewByType('create'), [
            'model' => $model,
            'storeArray' => $storeArray,
        ]);
    }

    /**
     * Updates an existing TlDeliveryProposal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->setScenario('create-on-client');

        if(!ClientManager::canUpdateDeliveryProposal($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
            $dpManager->onCreateProposal();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            $client = ClientManager::getClientEmployeeByAuthUser();
            $storeArray= TLHelper::getStoreArrayByClientID($client->client_id,true);

            return $this->render($this->getViewByType('update'), [
                'model' => $model,
                'storeArray' => $storeArray,
            ]);
        }
    }

    /**
     * Deletes an existing TlDeliveryProposal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if(!ClientManager::canDeleteDeliveryProposal($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        if($model) {
            $model->deleted = 1;
            $model->save(false);
        }


        return $this->redirect(['index']);
    }

    /**
     * Finds the TlDeliveryProposal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /*
     * The client confirms the correctness of the data entered by the Operator at the warehouse
     * */
    public function actionIsClientConfirm($id)
    {
//        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        $model->is_client_confirmed = $model::IS_CLIENT_CONFIRMED_YES;
        if ($model->save()) {
            $model->refresh();
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /*
     * Print TTN
     *
     * */
    public function actionPrintTtn()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if(!$this->canChangeShippedDatetime($model->shipped_datetime,DateHelper::getTimestamp())) {
            Yii::$app->session->setFlash("danger",'У вас нет доступа для повторной печати ТТН');
            return $this->redirect('index');
        }


        $ceID = '';
        $userName = '';
//        if(!Yii::$app->user->isGuest) {
//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {
                if($userClient = ClientManager::getClientEmployeeByAuthUser()) {
                    $ceID = $userClient->store_id;
                    $userName = $userClient->last_name.' '.$userClient->first_name;
                }
//            }
//        }

//        $storeTo = $model->routeTo;
        $storeFrom = $model->routeFrom;

        $managersNamesTo = 'Контакты получателей:<br />';
        if($routeTo = $model->routeTo) {
            // находим всех директоров магазина и отправляем им имейлы
            $clientEmployees = ClientEmployees::find()
                ->where([
                    'deleted'=>0,
                    'client_id'=>$model->client_id,
                    'store_id'=>$routeTo->id,
                    'manager_type'=>[
                        ClientEmployees::TYPE_BASE_ACCOUNT,
                        ClientEmployees::TYPE_DIRECTOR,
                        ClientEmployees::TYPE_DIRECTOR_INTERN,
                    ]
                ])
                ->all();

            foreach($clientEmployees as $item) {
                $managersNamesTo .= $item->first_name.' '.$item->last_name.' / '.$item->phone_mobile.' '.$item->phone."<br />";
            }
        }

//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($storeTo,10,true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($userModel,10,true);



        // если отправляем груз со склада из магазина, то печатаем 3 копии файла ТТН
        // 4 = DC - это наш склад
//        $to = 1;
        if(in_array($storeFrom->id,[$ceID])) {
            if($this->canChangeShippedDatetime($model->shipped_datetime,DateHelper::getTimestamp())) {
                $model->shipped_datetime = DateHelper::getTimestamp();
                $model->status = TlDeliveryProposal::STATUS_ON_ROUTE;
                $model->save(false);
            }
        }

        // Если груз получаем, по печатаем 1 экземпляр ТТН
//        if(in_array($storeTo->id,[$ceID])) {
//            $to = 1;
//            $model->delivery_date = Yii::$app->formatter->asDateTime(time(),'php:Y-m-d H:i:s');
//            $model->status = TlDeliveryProposal::STATUS_DELIVERED;
//        }



        $dpManager = new DeliveryProposalManager(['id' =>$model->id]);
        $dpManager->onPrintTtn();

        return $this->render('print-ttn-pdf',['model'=>$model,'userName'=>$userName,'managersNamesTo'=>$managersNamesTo]);
//        return $this->render('print-ttn-pdf',['model'=>$model,'to'=>$to,'userName'=>$userName]);
    }

    function canChangeShippedDatetime($aOldDateTime,$aCurrentDateTime) {

        if(empty($aOldDateTime)) {
            return true;
        }

        $old = new \DateTime();
        $old->setTimestamp($aOldDateTime);
        $oldYmd = (int)$old->format("Ymd");

        $current = new \DateTime();
        $current->setTimestamp($aCurrentDateTime);
        $currentYmd = (int)$current->format("Ymd");

        return $oldYmd == $currentYmd;
    }

    /*
     * Print box label
     *
     * */
    public function actionPrintBoxLabel()
    {
        $id = Yii::$app->request->get('id');
        $type = Yii::$app->request->get('type');

        $model = $this->findModel($id);
//
        $codeBookModel = Codebook::findOne(['base_type'=>Codebook::BASE_TYPE_BOX]); // Короб

//        VarDumper::dump($codeBookModel,10,true);
//        die('----STOP----');

        $view = ($type == 1 ? 'print-box-label-a4-pdf' : 'print-box-label-self-adhesive-pdf' );

        return $this->render($view,['model'=>$model,'codeBookModel'=>$codeBookModel]);
    }

    /*
   *
   * Export data to EXEL
   *
   * */
    public function actionExportToExcel()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

        $searchModel = new TlDeliveryProposalSearchExport();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dps = $dataProvider->getModels();

        $checkModel = isset($dps[0]) ? $dps[0] : '';
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));

        $activeSheet->setCellValue('A1', '');
        $activeSheet->setCellValue('A2', Yii::t('forms', 'Application to the act'));
        $activeSheet->setCellValue('A3', '');
        $activeSheet->setCellValue('A4', Yii::t('forms', 'Client'));
//        $activeSheet->setCellValue('B4', '');
        $activeSheet->setCellValue('A5', '');
        $activeSheet->setCellValue('A6', '');
        $activeSheet->setCellValue('H1', date('d/m/Y'));

        $i = 7;
        $activeSheet->setCellValue('A'.$i, Yii::t('transportLogistics/forms', 'Route From'));
        $activeSheet->setCellValue('B'.$i, Yii::t('transportLogistics/forms', 'Route To'));
        $activeSheet->setCellValue('C'.$i, Yii::t('transportLogistics/forms', 'Shipped date'));
        $activeSheet->setCellValue('D'.$i, Yii::t('transportLogistics/forms', 'Delivery Date'));
        $activeSheet->setCellValue('E'.$i, Yii::t('transportLogistics/forms', 'Number Places Actual'));

        if($checkModel){
            if(ClientManager::canViewAttribute($checkModel)){
                $activeSheet->setCellValue('F'.$i, Yii::t('transportLogistics/forms', 'Kg Actual'));
            } else {
                $activeSheet->setCellValue('F'.$i, '-');
            }
        }

        $activeSheet->setCellValue('G'.$i, Yii::t('transportLogistics/forms', 'Mc Actual'));
        $activeSheet->setCellValue('H'.$i, Yii::t('transportLogistics/forms', 'Price Invoice With Vat'));
        $activeSheet->setCellValue('I'.$i, Yii::t('transportLogistics/forms', 'ID'));
        $activeSheet->setCellValue('J'.$i, Yii::t('transportLogistics/forms', 'Diff number places'));
        $activeSheet->setCellValue('K'.$i, Yii::t('transportLogistics/forms', 'Status'));
        $activeSheet->setCellValue('M'.$i, Yii::t('transportLogistics/forms', 'Orders'));

        $value = [];
        $clientTitle = '';
        if($userClient = ClientManager::getClientEmployeeByAuthUser()) {
            $value = TLHelper::getStoreArrayByClientID($userClient->client_id);
            $client = \common\modules\client\models\Client::findOne($userClient->client_id);
            $clientTitle = $client->legal_company_name;
        }

        if(in_array($userClient->client_id,[95,96,97])) {
            $activeSheet->setCellValue('L' . $i, Yii::t('transportLogistics/forms', 'Client TTN'));
        }

        $priceInvoiceWithVatSum = 0;

        foreach($dps as $model) {
            $i++;

            $from =  isset ($value[$model->route_from]) ? $value[$model->route_from] : '-EMPTY-';
            $activeSheet->setCellValue('A' . $i, $from);

            $to =  isset ($value[$model->route_to]) ? $value[$model->route_to] : '-EMPTY-';
            $activeSheet->setCellValue('B' . $i, $to);

            $shippedDatetime = '';
            if(!empty($model->shipped_datetime)) {
                $shippedDatetime = Yii::$app->formatter->asDate($model->shipped_datetime,'php:d/m/Y');
            }
            $activeSheet->setCellValue('C' . $i, $shippedDatetime);

            $deliveryDatetime = '';
            if($model->delivery_date) {
                $deliveryDatetime = Yii::$app->formatter->asDate($model->delivery_date,'php:d/m/Y');
            }
            $activeSheet->setCellValue('D' . $i, $deliveryDatetime);

            $numberPlacesActual = $model->number_places_actual;
            $activeSheet->setCellValue('E' . $i, $numberPlacesActual);

            $kgActual = $model->kg_actual;
            if($checkModel){
                if(ClientManager::canViewAttribute($checkModel)){
                    $activeSheet->setCellValue('F' . $i, $kgActual);
                } else {
                    $activeSheet->setCellValue('F' . $i, '-');
                }
            }


            $mcActual = $model->mc_actual;
            $activeSheet->setCellValue('G' . $i, $mcActual);

            $priceInvoiceWithVat = $model->price_invoice_with_vat;
            $activeSheet->setCellValue('H' . $i, $priceInvoiceWithVat);

            $priceInvoiceWithVatSum += $priceInvoiceWithVat;

            $activeSheet->setCellValue('I' . $i, $model->id);

            $diffNumberPlaces = 0;
            if($soreReview =  StoreReviews::findOne(['tl_delivery_proposal_id'=>$model->id])) {
//                $diffNumberPlaces = $numberPlacesActual - $soreReview->number_of_places;
                $diffNumberPlaces = $soreReview->number_of_places;
            }
            $activeSheet->setCellValue('J' . $i, $diffNumberPlaces);

            $status = '';
            if(empty($model->delivery_date)) {
                $status = 'в пути';
            } else if(!empty($model->delivery_date) && $diffNumberPlaces == $numberPlacesActual) {
                $status = 'без ошибок';
            } else {
                $status = 'разница';
            }

            $activeSheet->setCellValue('K' . $i, $status);

            if(in_array($userClient->client_id,[95,96,97])) {
                $activeSheet->setCellValue('L' . $i,$model->client_ttn);
            }

            $activeSheet->setCellValue('M' . ($i+1), $model->getExtraFieldValueByName('orders'));

        }

        $activeSheet->setCellValue('B4', $clientTitle);
        $activeSheet->setCellValue('H' . ($i+1), $priceInvoiceWithVatSum);


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/DeFacto/delivery-proposal/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'delivery-proposal-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

    /*
   *
   * Export data to EXEL
   *
   * */
    public function actionExportToExcelWithOrder()
    {
        //if(Yii::$app->language == 'tr') {
            //Yii::$app->language = 'en';
            Yii::$app->language = 'tr';
        //}

        $searchModel = new TlDeliveryProposalSearchExport();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dps = $dataProvider->getModels();

        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A' . $i, Yii::t('outbound/forms', 'Parent order number')); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('outbound/forms', 'Order number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('outbound/forms', 'To point id')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('outbound/forms', 'Volume (м³)')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('outbound/forms', 'Expected Number Places Qty')); // + E
        $activeSheet->setCellValue('F' . $i, Yii::t('outbound/forms', 'Accepted Number Places Qty')); // + E
        $activeSheet->setCellValue('G' . $i, Yii::t('outbound/forms', 'Expected Qty')); // + F
        $activeSheet->setCellValue('H' . $i, Yii::t('outbound/forms', 'Allocate Qty')); // + G
        $activeSheet->setCellValue('I' . $i, Yii::t('outbound/forms', 'Accepted Qty')); // + H
        $activeSheet->setCellValue('J' . $i, Yii::t('outbound/forms', 'Data created on client')); // + I
        $activeSheet->setCellValue('K' . $i, Yii::t('outbound/forms', 'Created At')); // + J
        $activeSheet->setCellValue('L' . $i, Yii::t('outbound/forms', 'Packing date')); // + K
        $activeSheet->setCellValue('M' . $i, Yii::t('outbound/forms', 'Date left our warehouse')); // +L
        $activeSheet->setCellValue('N' . $i, Yii::t('outbound/forms', 'Date delivered')); // + M
        $activeSheet->setCellValue('O' . $i, Yii::t('inbound/forms', 'Cargo status')); // + M
        $activeSheet->setCellValue('P' . $i, 'WMS'); // + N
        $activeSheet->setCellValue('Q' . $i, 'TR'); // + O
        $activeSheet->setCellValue('R' . $i, 'FULL'); // + P
        $activeSheet->setCellValue('S' . $i, 'Delivery fail reason');
        $activeSheet->setCellValue('T' . $i, 'Delivery fail reason comment');
        $activeSheet->setCellValue('U' . $i, 'TTN');

        $i += 1;

        $client = '';
        if($userClient = ClientManager::getClientEmployeeByAuthUser()) {
            $client = \common\modules\client\models\Client::findOne($userClient->client_id);
        }

        $value = TLHelper::getStoreArrayByClientID($client->id);

        foreach($dps as $deliveryProposal) {

            $orderList = $deliveryProposal->proposalOrders;
            $ttn =  $deliveryProposal->id;

            if($orderList) {
                foreach ($orderList as $orderLine) {

                    $packingDate = '';
                    $cargoStatus = '';
                    $dataCreatedOnClient = '';
                    $acceptedQty =  '';
                    $allocateQty =  '';
                    $expectedQty =  '';
                    $expectedPlacesQty =  '';
                    $orderNumber =  '';
                    $volumeM3 =  '';
                    $parentOrderNumber =  '';
                    $acceptedPlacesQty =  '';
                    $order =  '';

                    if($orderLine->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {

                        $order = OutboundOrder::findOne($orderLine->order_id);
                        if($order) {
                            $cargoStatus = $order->getCargoStatusValue();
                            $packingDate = $this->formatDateTime($order->packing_date);
                            $dataCreatedOnClient = $this->formatDateTime($order->data_created_on_client);

                            $acceptedQty = $order->accepted_qty;
                            $allocateQty = $order->allocated_qty;
                            $expectedQty = $order->expected_qty;

                            $acceptedPlacesQty = $order->accepted_number_places_qty;
                            $volumeM3 =  $order->mc;
                            $orderNumber =  $order->order_number;
                            $parentOrderNumber =  $order->parent_order_number;
                        }
                    }

                    if($orderLine->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK) {

                        $order = CrossDock::findOne($orderLine->order_id);
                        if($order) {
                            $packingDate = $this->formatDateTime($order->accepted_datetime);
                            $acceptedPlacesQty = $order->accepted_number_places_qty;
                            $expectedPlacesQty = $order->expected_number_places_qty;
                            $volumeM3 = $order->box_m3;
                            $orderNumber = ltrim($order->internal_barcode, '2-');
                            $parentOrderNumber = $order->party_number;
                        }
                    }

                    if(!empty($order)) {

                        $wms = $order->calculateWMS();
                        $tr = $order->calculateTR();
                        $full = $order->calculateFULL();

                        $dateDelivered = $this->formatDateTime($order->date_delivered);
                        $dateLeftOurWarehouse = $this->formatDateTime($order->date_left_warehouse);
                        $createdAt = $this->formatDateTime($order->created_at);

                        $toPoint = ArrayHelper::getValue($value, $order->to_point_id);


                        $deliveryFailReason = '';
                        $deliveryFailReasonComment = '';
                        if ($fds = StatusList::getValue($deliveryProposal->fail_delivery_status)) {
                            //$deliveryFailReason =  iconv("UTF-8", "ISO-8859-3//IGNORE",$fds->statusText);
                            //$deliveryFailReasonComment = iconv("UTF-8", "ISO-8859-3//IGNORE", $fds->otherStatus);
                            $deliveryFailReason = $fds->statusText;
                            $deliveryFailReasonComment = $fds->otherStatus;
                        }

                        $activeSheet->setCellValue('A' . $i, $parentOrderNumber);
                        $activeSheet->setCellValue('B' . $i, $orderNumber);
                        $activeSheet->setCellValue('C' . $i, $toPoint);
                        $activeSheet->setCellValue('D' . $i, $volumeM3);
                        $activeSheet->setCellValue('E' . $i, $acceptedPlacesQty);
                        $activeSheet->setCellValue('F' . $i, $expectedPlacesQty);
                        $activeSheet->setCellValue('G' . $i, $expectedQty);
                        $activeSheet->setCellValue('H' . $i, $allocateQty);
                        $activeSheet->setCellValue('I' . $i, $acceptedQty);
                        $activeSheet->setCellValue('J' . $i, $dataCreatedOnClient);
                        $activeSheet->setCellValue('K' . $i, $createdAt);
                        $activeSheet->setCellValue('L' . $i, $packingDate);
                        $activeSheet->setCellValue('M' . $i, $dateLeftOurWarehouse);
                        $activeSheet->setCellValue('N' . $i, $dateDelivered);
                        $activeSheet->setCellValue('O' . $i, $cargoStatus);
                        $activeSheet->setCellValue('P' . $i, $wms);
                        $activeSheet->setCellValue('Q' . $i, $tr);
                        $activeSheet->setCellValue('R' . $i, $full);
                        $activeSheet->setCellValue('S' . $i, $deliveryFailReason);
                        $activeSheet->setCellValue('T' . $i, $deliveryFailReasonComment);
                        $activeSheet->setCellValue('U' . $i, $ttn);
                        $i++;
                    }
                }
            }
        }


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //$dirPath = 'uploads/DeFacto/delivery-proposal/export/'.date('Ymd').'/'.date('His');
        //BaseFileHelper::createDirectory($dirPath);
        $fileName = 'delivery-proposal-export-with-order-'.time().'.xlsx';
        //$fullPath = $dirPath.'/'.$fileName;
        //$objWriter->save($fullPath);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        Yii::$app->end();

        //return Yii::$app->response->xSendFile($fullPath);
        //return Yii::$app->response->sendFile($fullPath);
        //return Yii::$app->response->sendContentAsFile(file_get_contents($fullPath),$fileName);
        //return Yii::$app->response->xSendFile($fullPath,$fileName);
    }

    private function formatDateTime($datetime) {
        return !empty ($datetime) ? Yii::$app->formatter->asDatetime($datetime,'php:d.m.Y H:i:s') : '';
    }

    /*
     *
     *
     * */
    public function actionShowStoreReviewForm()
    {
        $storeRevID = Yii::$app->request->post('review-id');

        if($model = StoreReviews::findOne($storeRevID)) {

//            $proposal = $model->proposal;
//            $isAlmaty = false;
//            if (in_array($proposal->route_to, ArrayHelper::map(StoreReviews::isAlmatyStores($proposal->client_id),'id','id') )) {
//                $isAlmaty = true;
//            }

            return $this->renderAjax('_store-review-form', ['model'=>$model]);
//            return $this->renderAjax('_store-review-form', ['model'=>$model,'isAlmaty'=>$isAlmaty]);
        }


    }

    /*
     * Get view type
     * @param string $view Action name
     * @return string view name
     * */
//    protected function getViewByType__($view)
//    {
//        if(!Yii::$app->user->isGuest) {
//
//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {
//
//                if ($client = ClientManager::getClientEmployeeByAuthUser()) {
//                    switch ($client->manager_type) {
//                        case ClientEmployees::TYPE_BASE_ACCOUNT:
//                        case ClientEmployees::TYPE_LOGIST:
//                            $view .= '';
//                            break;
//
//                        case ClientEmployees::TYPE_DIRECTOR:
//                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
//                        case ClientEmployees::TYPE_MANAGER:
//                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $view = 'manager/'.$view;
//                            break;
//                        case ClientEmployees::TYPE_OBSERVER:
//                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
//                            $view = 'observer/'.$view;
//                            break;
//
//                        default:
//                            $view = 'empty-default';
//                            break;
//                    }
//                }
//            } else {
//                $view = 'empty-default';
//            }
//        } else {
//            $view = 'empty-default';
//        }
//        return $view;
//    }
}