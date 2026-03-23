<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 11.11.2017
 * Time: 9:52
 */

namespace common\clientObject\deliveryProposal;


use common\components\DeliveryProposalManager;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\transportLogistics\models\TlDeliveryRoutes;

class DeliveryProposalCarPartsService
{
    public function createTemplateEmptyOrder($outboundModelID)
    {
        $outboundModel = OutboundOrder::findOne($outboundModelID);
        //
        $dp = new TlDeliveryProposal();
        $dp->status = TlDeliveryProposal::STATUS_ROUTE_FORMED;
        $dp->client_id = $outboundModel->client_id;
        $dp->route_from = $outboundModel->from_point_id;
        $dp->route_to = $outboundModel->to_point_id;
        $dp->cash_no = TlDeliveryProposal::METHOD_CHAR;
        $dp->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_OUTBOUND;
        $dp->delivery_method = TlDeliveryProposal::DELIVERY_METHOD_WAREHOUSE_WAREHOUSE;
        $dp->agent_id = 30; // Частное лицо
        $dp->car_id = 40; // MAN 20000 Кг Частное лицо
        $dp->driver_name = '-';
        $dp->driver_phone = '-';
        $dp->driver_auto_number = '-';
        $dp->change_mckgnp = 1;
        $dp->change_price = 2;
        $dp->shipped_datetime = time();
        $dp->save(false);
        // Добавить заказы
        $dpOrder = new TlDeliveryProposalOrders();
        $dpOrder->client_id = $dp->client_id;
        $dpOrder->tl_delivery_proposal_id = $dp->id;
        $dpOrder->order_id = $outboundModel->id;
        $dpOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
        $dpOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
        $dpOrder->order_number = $outboundModel->parent_order_number . ' ' . $outboundModel->order_number;
        $dpOrder->kg = $outboundModel->kg;
        $dpOrder->kg_actual = $outboundModel->kg;
        $dpOrder->mc = $outboundModel->mc;
        $dpOrder->mc_actual = $outboundModel->mc;
        $dpOrder->number_places = $outboundModel->accepted_number_places_qty;
        $dpOrder->number_places_actual = $outboundModel->accepted_number_places_qty;
        $dpOrder->title = $outboundModel->title;
        $dpOrder->description = $outboundModel->description;
        $dpOrder->save(false);
        //
        $deliveryRoute = new TlDeliveryRoutes();
        $deliveryRoute->client_id = $outboundModel->client_id;
        $deliveryRoute->tl_delivery_proposal_id = $dp->id;
        $deliveryRoute->route_from = $outboundModel->from_point_id;
        $deliveryRoute->route_to = $outboundModel->to_point_id;
        $deliveryRoute->transportation_type = 1;
        $deliveryRoute->shipped_datetime = time();
        $deliveryRoute->save(false);

        //
        $dpManager = new DeliveryProposalManager(['id' => $dp->id]);
        $dpManager->onCreateProposal();


        $outboundModel->date_left_warehouse = time();
        $outboundModel->status = Stock::STATUS_OUTBOUND_PACKED;
        $outboundModel->save(false);

//        $dp->delivery_date = time();
//        $dp->status = TlDeliveryProposal::STATUS_DONE;
//        $dp->save(false);

        return $dp;
    }
}