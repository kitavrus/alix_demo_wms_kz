<?php

namespace app\modules\operatorDella\controllers;

use app\modules\operatorDella\models\QuickDeliveryOrderForm;
use common\helpers\DateHelper;
use common\modules\client\models\ClientEmployees;
use common\modules\leads\models\TransportationOrderLead;
use common\modules\transportLogistics\components\TLHelper;
use Yii;
use app\modules\order\models\PersonalOrderLead;
use app\modules\order\models\TransportationOrderLeadSearch;
use personalDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use app\components\ClientManager;
use  app\modules\operatorDella\models\TlDeliveryProposal;
use common\modules\store\models\Store;
use common\modules\client\models\Client;
use common\modules\codebook\models\Codebook;
use app\modules\operatorDella\models\DeliveryProposalSearch;
use common\events\DpEvent;
use common\components\DeliveryProposalManager;

class OrderController extends Controller
{
    /**
     * Create TransportationOrderLead and delivery proposal
     * @return mixed
     */
    public function actionMakeOrder()
    {
        $model = new TransportationOrderLead();

        if($model->load(Yii::$app->request->post()) && $model->validate()){
//            $model->status = TransportationOrderLead::STATUS_WAIT_FOR_CONFIRM;
            $model->source = TlDeliveryProposal::SOURCE_DELLA_OPERATOR;
            $model->order_number = TransportationOrderLead::generateOrderNumber();
            $model->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_TRANSFER;

            if($model->save()){
                //тут же создаем Delivery Proposal
                if($proposal = $model->createProposalFromLeadOrder()){
                    Yii::$app->getSession()->setFlash('success', Yii::t('client/messages', 'Your order was successfully created'));
                }

                return $this->redirect(['/operatorDella/order/view', 'id' => $proposal]);
            }

        }

        return $this->render('transportation_order', ['model'=>$model]);
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
            ->andWhere([
                'source'=>[
                    DeliveryProposalSearch::SOURCE_OUR_OPERATOR,
                    DeliveryProposalSearch::SOURCE_DELLA_OPERATOR
                ]
            ])
            ->andWhere('status != :status',[':status'=>TlDeliveryProposal::STATUS_DELIVERED]);

        $clientArray = Client::getActiveTMSItems();
        $storeArray = TLHelper::getStockPointArray();
        return $this->render('my-orders', [
            'searchModel' => $searchModel,
            'dataProvider' =>$dataProvider,
            'clientArray' =>$clientArray,
            'storeArray' =>$storeArray,
        ]);
    }

    /**
     * Displays a single TransportationOrderLead model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findProposalModel($id);
        $storeArray = TLHelper::getStockPointArray();
        return $this->render('view', [
            'model' => $model,
            'storeArray' => $storeArray,
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
    { //edit-order
        $model = $this->findProposalModel($id);
        if($model->status == TlDeliveryProposal::STATUS_NEW || $model->status == TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP){
            $model->scenario='create-update-manager-warehouse';
            $model->source = TlDeliveryProposal::SOURCE_DELLA_OPERATOR;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $dpManager = new DeliveryProposalManager(['id' => $model->id]);
                $dpManager->onUpdateProposal();
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
        $model = TransportationOrderLead::findOne(['id'=>$id, 'created_user_id'=>\Yii::$app->user->identity->id]);
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

        $model = TlDeliveryProposal::findOne(['created_user_id'=> Yii::$app->user->identity->id, 'id'=>$id]);
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
        $model = $this->findProposalModel($id);
        if($model) {
            $model->deleted = 1;
            $model->save(false);
            Yii::$app->getSession()->setFlash('warning', Yii::t('client/messages', 'Order №{0} was deleted', [$model->id]));
        }
        return $this->redirect(['my-orders']);
    }

    /**
     * Displays a form needed for create Order
     * @return mixed
     */
    public function actionQuickOrder($sender=null, $recipient=null, $weight=null, $volume=null, $client_id=null, $cost=null, $from_city_id=null, $to_city_id=null, $delivery_type=null)
    {
        if(!is_null($sender)){
            $sender_point = Store::findOne(['id'=>$sender, 'client_id'=>$client_id]);
        }
        if(!is_null($recipient)){
            $recipient_point = Store::findOne(['id'=>$recipient, 'client_id'=>$client_id]);
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

        if(isset($sender_point) && !empty($sender_point)){
            $model->from_city_id = $sender_point->city_id;
            $model->customer_name = $sender_point->contact_full_name;
            $model->customer_phone = $sender_point->phone_mobile;
            $model->customer_street = $sender_point->street;
            $model->customer_house = $sender_point->house;
            $model->customer_floor = $sender_point->floor;
            $model->customer_apartment = $sender_point->flat;

        }

        if(isset($recipient_point) && !empty($recipient_point)){
            $model->to_city_id = $recipient_point->city_id;
            $model->recipient_name = $recipient_point->contact_full_name;
            $model->recipient_phone = $recipient_point->phone_mobile;
            $model->recipient_street = $recipient_point->street;
            $model->recipient_house = $recipient_point->house;
            $model->recipient_floor = $recipient_point->floor;
            $model->recipient_apartment = $recipient_point->flat;

        }

        if($model->load(Yii::$app->request->post()) && $model->save()){
            //тут же создаем Delivery Proposal
            if($proposal = $model->createProposalFromLeadOrder()){
                Yii::$app->getSession()->setFlash('success', Yii::t('client/messages', 'Your order was successfully created'));
                return $this->redirect(['/operatorDella/order/view', 'id' => $proposal]);
            }

        }

        return $this->render('transportation_order', ['model'=>$model]);
    }

    /*
    * Print box label
    *
    * */
    public function actionPrintBoxLabel()
    {
        $id = Yii::$app->request->get('id');
        $type = Yii::$app->request->get('type');

        $model = $this->findProposalModel($id);
        $codeBookModel = Codebook::findOne(['base_type'=>Codebook::BASE_TYPE_BOX]); // Короб

        $view = ($type == 1 ? 'print/print-box-label-a4-pdf' : 'print/print-box-label-self-adhesive-pdf' );

        return $this->render($view,['model'=>$model,'codeBookModel'=>$codeBookModel]);
    }

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

    public function actionPrintOrderInvoice($id)
    {
        if($order = TlDeliveryProposal::findOne($id)){
            return $this->render('print/order-invoice-pdf',['order'=>$order]);
        }
    }

    /*
 * Print TTN
 *
 * */
    public function actionPrintTtn()
    { // print-ttn
        $id = Yii::$app->request->get('id');
        $model = $this->findProposalModel($id);

        $userName = '';
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

        // если отправляем груз со склада, то печатаем 3 копии файла ТТН
        // 4 = DC - это наш склад
        if(in_array($storeFrom->id,[4])) {
            //Yii::$app->formatter->timeZone = 'Asia/Almaty';
            $model->shipped_datetime = DateHelper::getTimestamp();
            $model->status = TlDeliveryProposal::STATUS_ON_ROUTE;
            // Yii::$app->formatter->timeZone = '';
            $model->save(false);

        }
//        $model->recalculateExpensesOrder();
//        $model->setCascadedStatus();
        $event = new DpEvent();
        $event->deliveryProposalId = $model->id;
        $model->trigger(TlDeliveryProposal::EVENT_PRINT_TTN, $event);

//        if($this->printType == 'html'){
//            Yii::$app->layout = 'print-html';
//            return $this->render('print-ttn-html',['model'=>$model,'userName'=>$userName,'managersNamesTo'=>$managersNamesTo]);
//        }
        return $this->render('print/print-ttn-pdf',['model'=>$model,'userName'=>$userName,'managersNamesTo'=>$managersNamesTo]);
    }

    /**
     * Displays a form needed for create Quick Delivery Order
     * @return mixed
     */
    public function actionCreateQuickForm()
    { // create-quick-form
        $quickDeliveryOrder = new QuickDeliveryOrderForm();

        return $this->render('forms/quick-delivery-order',['quickDeliveryOrder'=>$quickDeliveryOrder]);
    }
}
