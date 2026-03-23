<?php

namespace app\modules\operatorDella\controllers;

use app\modules\operatorDella\models\ClientSearch;
use app\modules\operatorDella\models\DeliveryCalculatorForm;
use app\modules\operatorDella\models\DeliveryOrderSearch;
use app\modules\operatorDella\models\QuickMakeOrderFrom;
use app\modules\operatorDella\models\RouteOrderFormSearch;
use common\modules\city\models\City;
use common\modules\city\models\Region;
use common\modules\client\models\ClientEmployees;
use common\modules\leads\models\TransportationOrderLead;
use common\modules\transportLogistics\components\TLHelper;
use Yii;
use app\modules\order\models\PersonalOrderLead;
use app\modules\order\models\TransportationOrderLeadSearch;
use personalDepartment\components\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use app\components\ClientManager;
use common\modules\store\models\Store;
use common\modules\client\models\Client;
use common\modules\codebook\models\Codebook;
use app\modules\operatorDella\models\DeliveryProposalSearch;
use common\components\DeliveryProposalManager;
use yii\web\Response;
use app\modules\operatorDella\models\SenderRecipientForm;
use common\components\DeliveryProposalService;
use common\modules\user\models\User;
use app\modules\operatorDella\models\TlDeliveryProposal;


class RouteOrderController extends Controller
{
    /*
    *
    * */
    public function actionIndex()
    {
        $model = new RouteOrderFormSearch();
        $model->load(Yii::$app->request->get());

        $dpClientIDs = [];
        $dataProviderDeliveryProposalTTN = [];
        $dpId = [];
        $searchModelDeliveryProposal = new DeliveryProposalSearch();
        if (!empty($model->client_id)) {
            $dpClientIDs[] = $model->client_id;
        }


        if (!empty($model->ttn)) {
            if($dp = TlDeliveryProposal::findOne($model->ttn)) {
                $dpClientIDs[] = $dp->client_id;
                $dpId = $model->ttn;
            }
        }

        if(!empty($model->ttn) || !empty($model->client_id)) {
            $dataProviderDeliveryProposalTTN = $searchModelDeliveryProposal->search(['DeliveryProposalSearch' => ['id' => $dpId,'client_id'=>$model->client_id]]);
        }


        // если заполнен телефон показываем клиента
        $dataProviderClient = [];
        if (!empty($model->phone) || !empty($dpClientIDs)) {
//            $phone = str_replace("-","",$model->phone);
            $phone = $model->phone;
            $clientIDs =  ClientEmployees::find()->select('client_id')->andWhere(['like','phone',$phone])->orWhere(['like','phone_mobile',$phone])->column();
            $clientIDs = !empty($clientIDs) ? $clientIDs : ['-1'];

            $searchModelClient = new ClientSearch();

            $clientIDs = $dpClientIDs?:$clientIDs;

            $dataProviderClient = $searchModelClient->search([-1]);
            $dataProviderClient->query->andWhere(['id' =>$clientIDs]);

        }

        // Если заполнены города из в.
        $dataProviderDeliveryProposal = [];
        if (!empty($model->cityFrom) || !empty($model->cityTo)) {

            $searchModelDeliveryProposal = new DeliveryProposalSearch();
            $dataProviderDeliveryProposal = $searchModelDeliveryProposal->search(['DeliveryProposalSearch' => ['cityFrom' => $model->cityFrom, 'cityTo' => $model->cityTo,]]);
            $dataProviderDeliveryProposal->query->andWhere(['status' => [
                TlDeliveryProposal::STATUS_UNDEFINED,
                TlDeliveryProposal::STATUS_NEW,
                TlDeliveryProposal::STATUS_ADD_CAR,
                TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,
                TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,
                TlDeliveryProposal::STATUS_ROUTE_FORMED,
                TlDeliveryProposal::STATUS_NOT_ADDED_M3,
                TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE,
                TlDeliveryProposal::STATUS_IN_PROCESSING_AT_WAREHOUSE,
                TlDeliveryProposal::STATUS_IN_TRANSFER_FROM_POINT,
            ]]);
        }

        //  показываем стоимость
        $deliveryCost = '';
        $deliveryTerm = '';
        if (!empty($model->cityFrom) || !empty($model->cityTo)) {
            $dcForm = new DeliveryCalculatorForm();
            $dcForm->city_to = $model->cityTo;
            $dcForm->city_from = $model->cityFrom;
            $dcForm->weight = $model->kg;
            $dcForm->volume = $model->m3;
            $dcForm->places = $model->places;
            $deliveryCost = $dcForm->calculateDeliveryCost();
            $deliveryTerm = $dcForm->calculateDeliveryCost(true);
        }

        $clientArray = Client::getActiveTMSItems();
        $storeArray = TLHelper::getStockPointArray();

        return $this->render('index', [
            'model' => $model,
            'dataProviderDeliveryProposal' => $dataProviderDeliveryProposal,
            'dataProviderDeliveryProposalTTN' => $dataProviderDeliveryProposalTTN,
            'dataProviderClient' => $dataProviderClient,
            'deliveryCost' => $deliveryCost,
            'deliveryTerm' => $deliveryTerm,
            'clientArray' => $clientArray,
            'storeArray' => $storeArray,
        ]);
    }

    /*
    * Create new delivery order
    * */
    public function actionShowOrdersByClient()
    {
        $searchModel = new DeliveryProposalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['source'=>DeliveryProposalSearch::SOURCE_OUR_OPERATOR,'client_id'=>Yii::$app->request->get('client-id')])
            ->andWhere('status != :status',[':status'=>TlDeliveryProposal::STATUS_DELIVERED])->orderBy(['id'=>SORT_DESC]);

        return $this->renderAjax('delivery-orders-grid', [
            'searchModel' => $searchModel,
            'dataProvider' =>$dataProvider,
        ]);
    }

    /*
    * Create new delivery order
     * */
    public function actionAddOrder()
    {
        $model = new SenderRecipientForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

           $dpManager = new DeliveryProposalService();
           $proposal = $dpManager->addOrderFromSenderRecipientForm($model);
           Yii::$app->getSession()->setFlash('success', Yii::t('client/messages', 'Your order was successfully created'));
           return $this->redirect(['/operatorDella/order/view', 'id' => $proposal->id]);
        }

        return $this->refresh();
    }

    /**
     * Create TransportationOrderLead and delivery proposal
     * @return mixed
     */
    public function actionMakeOrder()
    {
        $model = new TransportationOrderLead();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->status = TransportationOrderLead::STATUS_WAIT_FOR_CONFIRM;
            $model->source = TransportationOrderLead::SOURCE_OPERATOR_POINT;
            $model->order_number = TransportationOrderLead::generateOrderNumber();
            //$model->delivery_method = $model->delivery_type;
            $model->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_TRANSFER;

            if ($model->save()) {
                //тут же создаем Delivery Proposal
                if ($proposal = $model->createProposalFromLeadOrder()) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('client/messages', 'Your order was successfully created'));
                }

                return $this->redirect(['/operatorDella/order/view', 'id' => $proposal]);
            }

        }

        return $this->render('transportation_order', ['model' => $model]);
    }
//
//    /**
//     * Create delivery proposal based on transportation order
//     * @param int $lead_order_id
//     * @return mixed
//     */
//    public function createProposalFromLeadOrder($lead_order_id)
//    {
//        if ($model = TransportationOrderLead::findOne($lead_order_id)) {
//
//            if ($model->source == TransportationOrderLead::SOURCE_OPERATOR_POINT && empty($model->client_id) && $model->status==TransportationOrderLead::STATUS_WAIT_FOR_CONFIRM) {
//                if ($new_client_id = CManager::createClientFromOrder($model->id)) {
//                    //перегружаем экшен, так как client_id у модели поменялся в процессе создания клиента
//                    $model->client_id = $new_client_id;
//                    Yii::$app->getSession()->setFlash('success', Yii::t('client/messages', 'User {0} was created', [$model->customer_phone]));
//                }
//            }
//            //VarDumper::dump($new_client_id, 10, true); die;
//            $dp = new TlDeliveryProposal();
//            $dp->scenario = 'confirm-frontend-order';
//            $dp->client_id = $model->client_id;
//            $dp->transportation_order_lead_id = $model->id;
//            $dp->status = TlDeliveryProposal::STATUS_NEW;
//            $dp->source = TlDeliveryProposal::SOURCE_POINT_OPERATOR;
//            $dp->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_ONE_ROUTE;
//            $dp->shipment_description = $model->package_description;
//            $dp->declared_value = $model->declared_value;
//            $dp->change_price = TlDeliveryProposal::CHANGE_AUTOMATIC_PRICE_YES;
//            $dp->change_mckgnp = TlDeliveryProposal::CHANGE_AUTOMATIC_MC_KG_NP_NO;
//            $dp->cash_no = TlDeliveryProposal::METHOD_CASH;
//            //$dp->price_invoice_with_vat = $model->cost_vat;
//            $dp->delivery_method = $model->delivery_type;
//            $dp->kg = $model->weight;
//            $dp->mc = $model->volume;
//            $dp->kg_actual =  $model->weight;
//            $dp->mc_actual = $model->volume;
//            $dp->number_places = $model->places;
//            $dp->number_places_actual = $model->places;
//            $dp->route_from = $model->createPointFrom();
//            $dp->route_to = $model->createPointTo();
//            $dp->save();
//            $e = new DpEvent();
//            $e->deliveryProposalId = $dp->id;
//            $dp->eventRecalculate($e);
//
//                if($model->recipient_name_2){
//                    $dp->saveExtraFieldValue('recipient_name_2', $model->recipient_name_2);
//                }
//                if($model->recipient_phone_2){
//                    $dp->saveExtraFieldValue('recipient_phone_2', $model->recipient_phone_2);
//                }
//                $model->status = TransportationOrderLead::STATUS_CONFIRMED;
//                $model->save();
//                Yii::$app->getSession()->setFlash('success',
//                    Yii::t('client/messages', 'Order №{0} was confirmed', [$model->order_number]));
//                //TODO: confirmation SMS
////                $mManager = new MailManager();
////                $mManager->sender = 'no-reply@nmdx.kz';
////                if($mManager->sendOrderConfirmMail($model)){
////                    Yii::$app->getSession()->setFlash('warning', Yii::t('client/messages', 'Client was notified by email'));
////                }
//
//                return $dp->id;
//
//        }
//        return false;
//    }

    /**
     * Displays a grid with client orders
     * @return mixed
     */
    public function actionMyOrders()
    {
        $searchModel = new DeliveryProposalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andFilterWhere([
                //'created_user_id'=>\Yii::$app->user->identity->id,
                'source' => DeliveryProposalSearch::SOURCE_POINT_OPERATOR])
            ->andWhere('status != :status', [':status' => TlDeliveryProposal::STATUS_DELIVERED]);
//            ->andWhere(['in', 'status', [
//                    TlDeliveryProposal::STATUS_NEW,
//                    TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP
//                ]
//            ])
        ;

        return $this->render('my-orders', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TransportationOrderLead model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findProposalModel($id),
        ]);
    }

    /**
     * Displays a single DeliveryOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewInfo($id)
    {
        return $this->render('view-info', [
            'model' => $this->findProposalModel($id),
        ]);
    }

    /**
     * Edit delivery proposal
     * Can edit only new proposal
     * @return mixed
     */
    public function actionEditOrder($id)
    {
        $model = $this->findProposalModel($id);
        if ($model->status == TlDeliveryProposal::STATUS_NEW || $model->status == TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP) {
            $model->scenario = 'create-update-manager-warehouse';
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $dpManager = new DeliveryProposalManager(['id' => $model->id]);
                $dpManager->onCreateProposal();
                Yii::$app->getSession()->setFlash('info', Yii::t('client/messages', 'Order №{0} was successfully edited', [$model->id]));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('edit', [
                    'model' => $model,
                ]);
            }
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the TransportationOrderLead model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PersonalOrderLead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findLeadModel($id)
    {
        $model = TransportationOrderLead::findOne(['id' => $id, 'created_user_id' => \Yii::$app->user->identity->id]);
        if (!empty($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the TransportationOrderLead model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PersonalOrderLead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findProposalModel($id)
    {

        $model = TlDeliveryProposal::findOne(['created_user_id' => Yii::$app->user->identity->id, 'id' => $id]);
        if (!empty($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Soft delete.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findLeadModel($id);
        if ($model) {
            $model->deleted = 1;
            $model->save(false);
            Yii::$app->getSession()->setFlash('warning', Yii::t('client/messages', 'Order №{0} was deleted', [$model->order_number]));
        }
        return $this->redirect(['my-orders']);
    }

    /**
     * Displays a form needed for create Order
     * @return mixed
     */
    public function actionQuickOrder($sender = null, $recipient = null, $weight = null, $volume = null, $client_id = null, $cost = null, $from_city_id = null, $to_city_id = null, $delivery_type = null)
    {
        if (!is_null($sender)) {
            $sender_point = Store::findOne(['id' => $sender, 'client_id' => $client_id]);
        }
        if (!is_null($recipient)) {
            $recipient_point = Store::findOne(['id' => $recipient, 'client_id' => $client_id]);
        }
        $client = Client::findOne($client_id);

        $model = new TransportationOrderLead();
        $model->client_id = !empty($client) ? $client->id : '';
        $model->weight = $weight;
        $model->volume = $volume;
        $model->cost_vat = !empty($cost) ? $cost : '';
        $model->status = TransportationOrderLead::STATUS_WAIT_FOR_CONFIRM;
        $model->source = TransportationOrderLead::SOURCE_OPERATOR_POINT;
        $model->order_number = TransportationOrderLead::generateOrderNumber();
        $model->from_city_id = $from_city_id;
        $model->to_city_id = $to_city_id;
        $model->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_TRANSFER;
        $model->delivery_method = $delivery_type;

        if (isset($sender_point) && !empty($sender_point)) {
            $model->from_city_id = $sender_point->city_id;
            $model->customer_name = $sender_point->contact_full_name;
            $model->customer_phone = $sender_point->phone_mobile;
            $model->customer_street = $sender_point->street;
            $model->customer_house = $sender_point->house;
            $model->customer_floor = $sender_point->floor;
            $model->customer_apartment = $sender_point->flat;

        }

        if (isset($recipient_point) && !empty($recipient_point)) {
            $model->to_city_id = $recipient_point->city_id;
            $model->recipient_name = $recipient_point->contact_full_name;
            $model->recipient_phone = $recipient_point->phone_mobile;
            $model->recipient_street = $recipient_point->street;
            $model->recipient_house = $recipient_point->house;
            $model->recipient_floor = $recipient_point->floor;
            $model->recipient_apartment = $recipient_point->flat;

        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //тут же создаем Delivery Proposal
            if ($proposal = $model->createProposalFromLeadOrder()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('client/messages', 'Your order was successfully created'));
                return $this->redirect(['/operatorDella/order/view', 'id' => $proposal]);
            }

        }

        return $this->render('transportation_order', ['model' => $model]);
    }

    /*
    * Print box label
    *
    * */
    /*    public function actionPrintBoxLabel()
        {
            $id = Yii::$app->request->get('id');
            $type = Yii::$app->request->get('type');

            $model = $this->findProposalModel($id);
            $codeBookModel = Codebook::findOne(['base_type'=>Codebook::BASE_TYPE_BOX]); // Короб

            $view = ($type == 1 ? 'print/print-box-label-a4-pdf' : 'print/print-box-label-self-adhesive-pdf' );

            return $this->render($view,['model'=>$model,'codeBookModel'=>$codeBookModel]);
        }*/

    /*
     * Print proposal registry
     * and move all printed proposals
     * to status 'transfer from point'
     *
     * */
    /*    public function actionCreateRegistry()
        {
            $data = Yii::$app->request->get('keys');
            if(!empty($data)){
                $registry = new DeliveryProposalRegistry();
                $registry->registry_type = DeliveryProposalRegistry::REGISTRY_TYPE_POINT_OUTBOUND;
                $registry->dp_list = $data;
                $registry->save();
                $keys = explode(',', $data);
                $proposals = TlDeliveryProposal::find()
                    ->where(['in', 'id', $keys])
                    ->orderBy('id')
                    ->all();
                if(!empty($proposals)){
                    foreach($proposals as $proposal){
                        $proposal->status = TlDeliveryProposal::STATUS_IN_TRANSFER_FROM_POINT;
                        $proposal->save(false);
                    }
                    return $this->render('print/registry-pdf',['items'=>$proposals, 'registry_id'=>$registry->id]);
                }
            }
            throw new ErrorException('Empty keys not allowed');
        }*/

    /*    public function actionPrintOrderInvoice($id)
        {
            if($order = TlDeliveryProposal::findOne($id)){
                return $this->render('print/order-invoice-pdf',['order'=>$order]);
            }
        }*/

    /*
    * Form. Add new store route
    *
    * */
    public function actionAddStore()
    {
        $model = new Store();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'Success',
                'data_options' => DeliveryOrderSearch::getPointsByClient($model->client_id),
            ];
        } else {
            $model->client_id = Yii::$app->request->get('client_id');
            $model->status = Store::STATUS_ACTIVE;
        }

        return $this->renderAjax('forms/_create-store-form', [
            'model' => $model,
        ]);
    }

    /*
    * Form. Edit store route
    *
    * */
    public function actionEditStore($id)
    {
        $model = Store::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'Success',
                'data_options' => DeliveryOrderSearch::getPointsByClient($model->client_id),
            ];
        }

        return $this->renderAjax('forms/_create-store-form', [
            'model' => $model,
        ]);
    }

    /*
    * Form. Add new employee
    * */
    public function actionAddEmployee()
    {
        $model = new ClientEmployees();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->save(false);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'Success',
                'data_options' =>  ArrayHelper::map(ClientEmployees::find()->andWhere(['client_id'=>$model->client_id])->all(),'id','full_name'),
            ];
        } else {
            $model->client_id = Yii::$app->request->get('client_id');
            $model->status = Store::STATUS_ACTIVE;
            $model->manager_type = ClientEmployees::TYPE_TRANSPORT_TMP_CLIENT;
        }

        return $this->renderAjax('forms/_create-employee-form', [
            'model' => $model,
        ]);
    }

    /*
    * Form. Edit employee
    * */
    public function actionEditEmployee($id)
    {
        $model = ClientEmployees::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->save(false);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'Success',
                'data_options' =>  ArrayHelper::map(ClientEmployees::find()->andWhere(['client_id'=>$model->client_id])->all(),'id','full_name'),
            ];
        }

        return $this->renderAjax('forms/_create-employee-form', [
            'model' => $model,
        ]);
    }

    /*
     *
     * */
    public function actionCreateClient()
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->password = 'p'.time();
            $model->save(false);

            $userModel = \Yii::createObject([
                'class'    => User::className(),
                'scenario' => 'create',
            ]);

            $userModel->username = $model->username;
            $userModel->email = empty($model->email) ? time().'-demo@nmdx.kz' : $model->email;
            $userModel->user_type = User::USER_TYPE_CLIENT;
            $userModel->password = 'p'.$model->password;

            if($userModel->create()) {
                $model->user_id = $userModel->id;
                $model->password = '';
                $model->save(false);
            }

            // Create base client employee account
            $clientEmployee = new ClientEmployees();
            $clientEmployee->store_id = 0;
            $clientEmployee->client_id = $model->id;
            $clientEmployee->user_id = $model->user_id;
            $clientEmployee->username = $model->username;
            $clientEmployee->first_name = $model->first_name;
            $clientEmployee->middle_name = $model->middle_name;
            $clientEmployee->last_name = $model->last_name;
            $clientEmployee->phone = $model->phone;
            $clientEmployee->phone_mobile = $model->phone_mobile;
            $clientEmployee->email = $model->email;
            $clientEmployee->password = $model->password;
            $clientEmployee->status = $model::STATUS_ACTIVE;

            $clientEmployee->manager_type = ClientEmployees::TYPE_TRANSPORT_TMP_CLIENT;
            $clientEmployee->save(false);

            return $this->redirect(['index', 'RouteOrderFormSearch[phone]' => $model->phone]);

        }

        $model->phone = Yii::$app->request->get('phone');
        $model->username = Yii::$app->request->get('phone');
        $model->legal_company_name = Yii::$app->request->get('phone');
        $model->title = Yii::$app->request->get('phone');
        $model->status = $model::STATUS_ACTIVE;
        $model->on_stock = $model::ON_STOCK_TMS;

        return $this->renderAjax('forms/_create-client-form', [
            'model' => $model,
        ]);
    }


    /*
    * Get List city by region id
    * */
    public function actionGetCityByRegion()
    {
        $currentRegionID = Yii::$app->request->post('region_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
//        $data = [''=> Yii::t('transportLogistics/titles', 'Select city')];
        $data = \yii\helpers\ArrayHelper::map(City::find()->where(['region_id'=>$currentRegionID])->orderBy('name')->all(),'id','name');
        return [
            'message' => 'Success',
            'data_options' => $data,
        ];
    }

    /*
   * Get List redion by country id
   * */
    public function actionGetRegionByCountry()
    {
        $currentCountryID = Yii::$app->request->post('country_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = [''=> 'Выберите регион'];
        $data += \yii\helpers\ArrayHelper::map(Region::find()->where(['country_id'=>$currentCountryID])->orderBy('name')->all(),'id','name');
//        $data += [''=>Yii::t('forms','Please select')];
        return [
            'message' => 'Success',
            'data_options' => $data,
//            'data_options' => array_unshift($data,[''=>Yii::t('forms','Please select')]),
        ];
    }

    /*
     *
     * */
    public function actionQuickMakeOrder()
    {
        $model = new QuickMakeOrderFrom();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->createUpdateClient()  // Create new client
                ->createUpdateClientAddressFrom()  // Create new Address From
                ->createUpdateClientAddressTo() // Create new Address To
                ->createUpdateClientContactFrom()  // Create new Contact From
                ->createUpdateClientContactTo() // Create new Contact To
                ->createUpdateDeliveryProposal(); // Create new Delivery Proposal Create new Delivery Proposal Order

                Yii::$app->getSession()->setFlash('success', Yii::t('client/messages', 'Your order was successfully created'));
                return $this->redirect(['/operatorDella/order/view', 'id' => $model->getDeliveryProposal()->id]);
        } else {
            // DEFAULT VALUE
            $model->typeLoading = TlDeliveryProposal::TRANSPORT_TYPE_LOADING_BACK;
        }

        // DATA ARRAY
        $cityArray = ArrayHelper::map(\common\modules\city\models\City::find()->all(),'id','name');

       return $this->render('quick-make-order',['model'=>$model,'cityArray'=>$cityArray]);
    }
}
