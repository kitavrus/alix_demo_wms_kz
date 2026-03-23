<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 22.09.2017
 * Time: 8:04
 */

namespace common\modules\transportLogistics\DeliveryProposalOrder\repository;


use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\transportLogistics\DeliveryProposalOrder\dto\Create;

class Repository
{
    public function create( Create $data) {

       $deliveryProposalOrder = new TlDeliveryProposalOrders();
       $deliveryProposalOrder->client_id = $data->clientId;
       $deliveryProposalOrder->tl_delivery_proposal_id = $data->deliveryProposalId;
       $deliveryProposalOrder->order_type = $data->orderType;
       $deliveryProposalOrder->order_id = $data->orderId;
       $deliveryProposalOrder->order_number = $data->orderNumber;
       $deliveryProposalOrder->number_places = $data->numberPlaces;
       $deliveryProposalOrder->number_places_actual = $data->numberPlacesActual;
       $deliveryProposalOrder->mc = $data->mc;
       $deliveryProposalOrder->mc_actual = $data->mcActual;
       $deliveryProposalOrder->kg = $data->kg;
       $deliveryProposalOrder->kg_actual = $data->kgActual;
       $deliveryProposalOrder->status = $data->status;
       $deliveryProposalOrder->title = $data->title;
       $deliveryProposalOrder->description = $data->description;
       $deliveryProposalOrder->save(false);

    }

    public function update(){}
    public function createOrUpdate(){}
    public function findOrCreate(){}
    public function delete(){}

    public function exist($condition) {
        return TlDeliveryProposalOrders::find()->andWhere($condition)->exists();
    }
}