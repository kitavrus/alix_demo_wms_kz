<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 17.02.2016
 * Time: 11:51
 */

namespace common\components;


use app\modules\delivery\models\SenderRecipientForm;
use common\components\DeliveryProposalManager;
use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use yii\helpers\VarDumper;

class DeliveryProposalService
{
    /*
    * Создаем заявку для клиента на основе сузествующего клиента
     * @param SenderRecipientForm $formData
     * @return array
    * */
    public function addOrderFromSenderRecipientForm($formData)
    {
       $dpModel = [];
       if($attributes =  $this->prepareDataFromSenderRecipientForm($formData)) {
           $dpModel = $this->createDeliveryOrderForExistClient($attributes);
       }

       return $dpModel;
    }
    /*
     * Создаем заявку для клиента на основе сузествующего клиента
     * @param array $data
     * */
    public function createDeliveryOrderForExistClient(array $data)
    {
        $dp = new TlDeliveryProposal();
        $dp->client_id = $data['clientId'];
        $dp->kg = $data['kg'];
        $dp->mc = $data['m3'];
        $dp->kg_actual =  $data['kg'];
        $dp->mc_actual = $data['m3'];
        $dp->number_places = $data['places'];
        $dp->number_places_actual = $data['places'];
        $dp->route_from = $data['sender'];
        $dp->route_to = $data['recipient'];
        $dp->sender_contact = $this->prepareContactToStr($data['senderContact']);;
        $dp->sender_contact_id = $data['senderContact'];
        $dp->recipient_contact = $this->prepareContactToStr($data['recipientContact']);
        $dp->recipient_contact_id = $data['recipientContact'];
        $dp->declared_value = $data['declaredValue'];
        $dp->delivery_method = $data['deliveryType'];
        $dp->comment = $data['description'];
        $dp->transport_type_loading = $data['typeLoading'];
        $dp->transport_who_pays = $data['whoPays'];
        $dp->price_invoice = $data['price'];

        $dp->status = TlDeliveryProposal::STATUS_NEW;
        $dp->source = TlDeliveryProposal::SOURCE_DELLA_OPERATOR;
        $dp->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_TRANSFER;
        $dp->change_price = TlDeliveryProposal::CHANGE_AUTOMATIC_PRICE_NO;
        $dp->change_mckgnp = TlDeliveryProposal::CHANGE_AUTOMATIC_MC_KG_NP_NO;
        $dp->cash_no = TlDeliveryProposal::METHOD_CASH;
        $dp->save(false);

        $deliveryOrder = new  TlDeliveryProposalOrders();
        $deliveryOrder->client_id = $dp->client_id;
        $deliveryOrder->tl_delivery_proposal_id = $dp->id;
        $deliveryOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_UNDEFINED;
        $deliveryOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK;
        $deliveryOrder->number_places = $dp->number_places;
        $deliveryOrder->number_places_actual = $dp->number_places;
        $deliveryOrder->mc_actual = $dp->mc;
        $deliveryOrder->mc = $dp->mc;
        $deliveryOrder->kg_actual = $dp->kg;
        $deliveryOrder->kg = $dp->kg;
        $deliveryOrder->order_number = '[OPERATOR-'.$dp->id.'-'.date('Ymd').']';
        $deliveryOrder->save(false);

        $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
        $dpManager->onCreateProposal();

        return $dp;
    }

    /*
     * Подготавливаем данные полученые из формы для создания новой заявки для существующего клиента
     * @param SenderRecipientForm $formData
     * @return array
     * */
    public function prepareDataFromSenderRecipientForm($formData)
    {
        $dataOut = [];
        if($attributes = $formData->getAttributes()) {
            return $attributes;
        }
        return $dataOut;
    }

    /*
     *
     * @param integer $id
     * @return string
     * */
    public function prepareContactToStr($id)
    {
        $out = '';
        if($ce = ClientEmployees::findOne($id)) {
            $out = $ce->full_name.' Тел: '.$ce->phone.' Тел2: '.$ce->phone_mobile;
        }

        return $out;
    }
}