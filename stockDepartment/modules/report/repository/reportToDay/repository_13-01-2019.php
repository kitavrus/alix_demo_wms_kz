<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.09.2017
 * Time: 9:35
 */

namespace stockDepartment\modules\report\repository\reportToDay;


use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\client\models\Client;
use common\modules\crossDock\models\CrossDock;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\overloads\ArrayHelper;
use common\modules\store\repository\Repository as StoreRepository;
use yii\db\Query;
use yii\helpers\VarDumper;

class repository
{
    private $clientID;
    const CROSS_DOC_START_DATETIME = 1504224000;

    /**
     * repository constructor.
     * @param int $clientID
     */
    public function __construct($clientID = Client::CLIENT_DEFACTO)
    {
        $this->clientID = $clientID;
//        $this->clientID = Client::CLIENT_DEFACTO;
    }

    /**
     * Собрано лотов сегодня
     */
    public function qtyScannedOutboundLotToDay($fromDateTime, $toDateTime)
    {
        return OutboundOrder::find()
            ->select('count(id) as orderSum, sum(accepted_qty) as lotSum, sum(accepted_number_places_qty) as placeSum, sum(mc) as mcSum')
            ->andWhere(['client_id' => $this->clientID])
            ->andWhere(['between', 'packing_date', strtotime($fromDateTime), strtotime($toDateTime)])
            ->asArray()
            ->one();
    }

    /**
     * Отгузили сегодня кросс-док и со склада
     */
    public function qtyLeftOutboundAndCrossDockToDay($fromDateTime, $toDateTime)
    {
       $crossDockOrder = CrossDock::find()
            ->select('count(id) as orderSum, sum(accepted_number_places_qty) as placeSum, sum(box_m3) as mcSum')
            ->andWhere(['client_id' => $this->clientID])
            ->andWhere(['between', 'date_left_warehouse', strtotime($fromDateTime), strtotime($toDateTime)])
            ->asArray()
            ->one();

       $outboundOrder = OutboundOrder::find()
            ->select('count(id) as orderSum, sum(accepted_qty) as lotSum, sum(accepted_number_places_qty) as placeSum, sum(mc) as mcSum')
            ->andWhere(['client_id' => $this->clientID])
            ->andWhere(['between', 'date_left_warehouse', strtotime($fromDateTime), strtotime($toDateTime)])
            ->asArray()
            ->one();

        return [
            'lotSum' => $outboundOrder['lotSum'],
            'orderSum' => $crossDockOrder['orderSum']+$outboundOrder['orderSum'],
            'placeSum' => $crossDockOrder['placeSum']+$outboundOrder['placeSum'],
            'mcSum' => $crossDockOrder['mcSum']+$outboundOrder['mcSum'],
        ];
    }

    /**
     * В пути заказы
     */
    public function qtyOnRoadOutboundToDay()
    {
        return OutboundOrder::find()
            ->select('count(id) as orderSum, sum(accepted_qty) as lotSum, sum(accepted_number_places_qty) as placeSum, sum(mc) as mcSum')
            ->andWhere(['client_id' => $this->clientID,
                'status' => Stock::STATUS_OUTBOUND_ON_ROAD,
                'cargo_status' => OutboundOrder::CARGO_STATUS_ON_ROUTE
            ])
            ->asArray()
            ->one();
    }

    public function sumOutboundOrderInProcess()
    {
        return OutboundOrder::find()
//            ->select('count(id) as orderSum, sum(expected_qty) as lotSum, sum(expected_number_places_qty) as placeSum, sum(mc) as mcSum')
            ->select('count(id) as orderSum, sum(expected_qty) - sum(accepted_qty) as lotSum, sum(expected_number_places_qty) as placeSum, sum(mc) as mcSum')
            ->andWhere(['client_id' => $this->clientID])
            ->andWhere(['packing_date' => null])
            ->andWhere('allocated_qty != 0')
            ->asArray()
            ->one();
    }

    public function sumOutboundOrderInProcessByStoreIds($storeIDs)
    {
        return OutboundOrder::find()
//            ->select('count(id) as orderSum, sum(expected_qty) as lotSum, sum(expected_number_places_qty) as placeSum, sum(mc) as mcSum')
            ->select('count(id) as orderSum, sum(expected_qty) - sum(accepted_qty) as lotSum, sum(expected_number_places_qty) as placeSum, sum(mc) as mcSum')
            ->andWhere(['client_id' => $this->clientID,'to_point_id'=>$storeIDs])
            ->andWhere(['packing_date' => null])
            ->andWhere('allocated_qty != 0')
            ->asArray()
            ->one();
    }

    /**
     * Приняли лотов сегодня
     */
    public function qtyScannedInboundLotToDay($fromDateTime, $toDateTime)
    {
        return Stock::find()
            ->select('count(id) as lotSum')
            ->andWhere(['client_id' => $this->clientID])
            ->andWhere(['between', 'scan_in_datetime', strtotime($fromDateTime), strtotime($toDateTime)])
            ->asArray()
            ->one();
    }

    /**
     * Приходные накладные которые принимаются в данный момен
     */
    public function inboundInProcessByOrders()
    {
        return InboundOrder::find()
            ->select('order_number, parent_order_number, expected_qty as expectedQtyLot, accepted_qty as acceptedQtyLot, (expected_qty-accepted_qty) as diffQtyLot, begin_datetime as beginDatetime')
            ->andWhere([
                'client_id' => $this->clientID,
                'order_type' => InboundOrder::ORDER_TYPE_INBOUND,
                'status' => [
                    Stock::STATUS_INBOUND_NEW,
                    Stock::STATUS_INBOUND_SCANNING,
                    Stock::STATUS_INBOUND_SCANNED,
                ]
            ])
            ->asArray()
            ->all();
    }

    /**
     * Постепления которые принимаются
     */
    public function inboundOrderInProcess()
    {
        return InboundOrder::find()
            ->select('count(id) as orderSum, (sum(expected_qty) - sum(accepted_qty)) as lotSum')
            ->andWhere([
                'client_id' => $this->clientID,
                'order_type' => InboundOrder::ORDER_TYPE_INBOUND,
                'status' => [
                    Stock::STATUS_INBOUND_NEW,
                    Stock::STATUS_INBOUND_SCANNING,
                    Stock::STATUS_INBOUND_SCANNED,
                ]
            ])
            ->asArray()
            ->one();
    }

    /*
     *
     * */
    public function readyForDelivery()
    {
        $crossDockOrder = CrossDock::find()
            ->select('count(id) as orderSum, sum(accepted_number_places_qty) as placeSum, sum(box_m3) as mcSum')
            ->andWhere([
                        'client_id' => $this->clientID,
                        'status' => Stock::STATUS_CROSS_DOCK_COMPLETE,
            ])
            ->andWhere('accepted_datetime IS NOT NULL')
            ->andWhere(['>','created_at',self::CROSS_DOC_START_DATETIME])
            ->asArray()
            ->one();


        $outboundOrder = OutboundOrder::find()
            ->select('count(id) as orderSum, sum(accepted_qty) as lotSum, sum(accepted_number_places_qty) as placeSum, sum(mc) as mcSum')
            ->andWhere([
                'client_id' => $this->clientID,
                'status' => Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
            ])
            ->asArray()
            ->one();

        return [
          'orderSum'=>$crossDockOrder['orderSum']+$outboundOrder['orderSum'],
          'lotSum'=>$outboundOrder['lotSum'],
          'placeSum'=>$crossDockOrder['placeSum']+$outboundOrder['placeSum'],
          'mcSum'=>$crossDockOrder['mcSum']+$outboundOrder['mcSum'],
        ];
    }

    /*
     *
     * */
    public function readyForDeliveryByRouteDirection($storeIDs)
    {
        $outboundOrders = OutboundOrder::find()
            ->select('to_point_id, count(id) as orderSum, sum(accepted_qty) as lotSum, sum(accepted_number_places_qty) as placeSum, sum(mc) as mcSum, GROUP_CONCAT(id) as ids')
            ->andWhere([
                'client_id' => $this->clientID,
                'to_point_id'=>$storeIDs,
                'status' => Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
            ])
            ->groupBy('to_point_id')
            ->orderBy(['mcSum' => SORT_DESC])
            ->asArray()
            ->all();

        $crossDockOrders = CrossDock::find()
            ->select('to_point_id, count(id) as orderSum, sum(accepted_number_places_qty) as placeSum, sum(box_m3) as mcSum, GROUP_CONCAT(id) as ids')
            ->andWhere([
                'client_id' => $this->clientID,
                'to_point_id'=>$storeIDs,
                'status' => Stock::STATUS_CROSS_DOCK_COMPLETE,
            ])
            ->andWhere('accepted_datetime IS NOT NULL')
            ->andWhere(['>','created_at',self::CROSS_DOC_START_DATETIME])
            ->groupBy('to_point_id')
            ->orderBy(['mcSum' => SORT_DESC])
            ->asArray()
            ->all();

        $orders = [];
        foreach($outboundOrders as $outboundOrder) {
            $orders [$outboundOrder['to_point_id']]['RPT'] = [
                'orderSum'=>$outboundOrder['orderSum'],
                'lotSum'=>$outboundOrder['lotSum'],
                'placeSum'=>$outboundOrder['placeSum'],
                'mcSum'=>$outboundOrder['mcSum'],
                'ids'=>$outboundOrder['ids'],
            ];
        }

        foreach($crossDockOrders as $crossDockOrder) {
            $orders[$crossDockOrder['to_point_id']]['CROSS-DOCK'] = [
                'orderSum'=>$crossDockOrder['orderSum'],
                'placeSum'=>$crossDockOrder['placeSum'],
                'mcSum'=>$crossDockOrder['mcSum'],
                'ids'=>$crossDockOrder['ids'],
            ];
        }

        return $orders;
    }

    public function getMoreDeliveryTime($fromDateTime, $toDateTime)
    {
        $deliveryOrders = TlDeliveryProposal::find()
            ->select('id, client_id, route_from, route_to, TIMESTAMPDIFF(DAY, FROM_UNIXTIME(shipped_datetime) , NOW()) as day, shipped_datetime')
            ->andWhere([
                'client_id' => $this->clientID,
                'route_from' => 4,
                'status' => TlDeliveryProposal::STATUS_ON_ROUTE,
            ])
            ->andWhere(['between', 'shipped_datetime', strtotime($fromDateTime), strtotime($toDateTime)])
            ->asArray()
            ->all();

        $moreDeliveryTimeStores = StoreRepository::getStoreByIDs(ArrayHelper::getColumn($deliveryOrders, 'route_to'));

        foreach ($deliveryOrders as $key => $outbound) {
            $billing = TlDeliveryProposalBilling::find()
                ->select('delivery_term, delivery_term_from, delivery_term_to, id')
                ->andWhere(
                    [
                        'client_id' => $outbound['client_id'],
                        'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
                        'route_from' => $outbound['route_from'],
                        'route_to' => $outbound['route_to'],
                    ]
                )
                ->one();

            $deliveryOrders[$key]['store-name'] = '';
            $deliveryOrders[$key]['diff'] = '0';
            $deliveryOrders[$key]['day-term'] = '';

            if ($billing && $outbound['day'] >= (int)$billing->delivery_term_to) {
                $deliveryOrders[$key]['store-name'] = (isset($moreDeliveryTimeStores[$outbound['route_to']]) ? $moreDeliveryTimeStores[$outbound['route_to']] : ' Магазин не найден');
                $deliveryOrders[$key]['diff'] = $outbound['day'] - (int)$billing->delivery_term_to;
                $deliveryOrders[$key]['day-term'] = ' от ' . $billing->delivery_term_from . ' до ' . $billing->delivery_term_to;
            } else {
                unset($deliveryOrders[$key]);
            }
        }

        return $deliveryOrders;
    }

    public function getMoreDeliveryTimeBetweenStore($fromStore,$toStore, $fromDateTime, $toDateTime)
    {
        $deliveryOrders = TlDeliveryProposal::find()
            ->select('id, client_id, route_from, route_to, TIMESTAMPDIFF(DAY, FROM_UNIXTIME(shipped_datetime) , NOW()) as day, shipped_datetime')
            ->andWhere([
                'client_id' => $this->clientID,
                'status' => TlDeliveryProposal::STATUS_ON_ROUTE,
            ])
            ->andFilterWhere(['OR',
                ['route_from'=>$fromStore],
                ['route_to'=>$toStore],
            ])
            ->andWhere(['between', 'shipped_datetime', strtotime($fromDateTime), strtotime($toDateTime)])
            ->asArray()
            ->all();

        $fromToStores = (new \common\modules\store\repository\Repository())->getStoreCityNameByClientWithPattern($this->clientID);


//        VarDumper::dump($fromToStores,10,true);
//        die;

        foreach ($deliveryOrders as $key => $outbound) {
            $billing = TlDeliveryProposalBilling::find()
                ->select('delivery_term, delivery_term_from, delivery_term_to, id')
                ->andWhere(
                    [
                        'client_id' => $outbound['client_id'],
                        'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
                        'route_from' => $outbound['route_from'],
                        'route_to' => $outbound['route_to'],
                    ]
                )
                ->one();

            $deliveryOrders[$key]['store-from-name'] = '';
            $deliveryOrders[$key]['store-to-name'] = '';
            $deliveryOrders[$key]['diff'] = '0';
            $deliveryOrders[$key]['day-term'] = '';

            if ($billing && $outbound['day'] >= (int)$billing->delivery_term_to) {

                $storeFromName = '';
                $storeToName = '--';
                if(isset($fromToStores[$outbound['route_from']])) {
                    $storeFromName = $fromToStores[$outbound['route_from']];
                }

                if(isset($fromToStores[$outbound['route_to']])) {
                    $storeToName = $fromToStores[$outbound['route_to']];
                }


                $deliveryOrders[$key]['delivery-id'] = $outbound['id'];
                $deliveryOrders[$key]['store-from-name'] = $storeFromName;
                $deliveryOrders[$key]['store-to-name'] = $storeToName;
                $deliveryOrders[$key]['diff'] = $outbound['day'] - (int)$billing->delivery_term_to;
                $deliveryOrders[$key]['day-term'] = ' от ' . $billing->delivery_term_from . ' до ' . $billing->delivery_term_to;
            } else {
                unset($deliveryOrders[$key]);
            }
        }

        return $deliveryOrders;
    }

    // CROSS-DOCK
    public function getAcceptedCrossDockBoxToDay($fromDateTime, $toDateTime) {
        return CrossDock::find()
            ->select('count(id) as orderSum, sum(accepted_number_places_qty) as boxSum, sum(box_m3) as mcSum')
            ->andWhere(['client_id' => $this->clientID])
            ->andWhere(['between', 'accepted_datetime', strtotime($fromDateTime), strtotime($toDateTime)])
            ->asArray()
            ->one();
    }

    // CROSS-DOCK
    public function getInProcessCrossDockBoxToDay() {

        $acceptedOrderSumQuery = (new Query())->select('COUNT(id)')
            ->andWhere([
                'client_id' => $this->clientID,
                'status' => Stock::STATUS_CROSS_DOCK_COMPLETE,
                'deleted' => 0,
            ])
            ->andWhere('topCrossDock.party_number = acceptedOrderSumQuery.party_number')
            ->from(CrossDock::tableName().' as acceptedOrderSumQuery');

        $acceptedBoxM3SumQuery = (new Query())->select('sum(box_m3)')
            ->andWhere([
                'client_id' => $this->clientID,
                'status' => Stock::STATUS_CROSS_DOCK_COMPLETE,
                'deleted' => 0,
            ])
            ->andWhere('topCrossDock.party_number = acceptedBoxM3SumQuery.party_number')
            ->from(CrossDock::tableName().' as acceptedBoxM3SumQuery');

        $acceptedBoxSumQuery = (new Query())->select('sum(accepted_number_places_qty)')
            ->andWhere([
                'client_id' => $this->clientID,
                'status' => Stock::STATUS_CROSS_DOCK_COMPLETE,
                'deleted' => 0,
            ])
            ->andWhere('topCrossDock.party_number = acceptedBoxSumQuery.party_number')
            ->from(CrossDock::tableName().' as acceptedBoxSumQuery');


        return (new Query())
            ->select([
                'internal_barcode',
                'party_number',
                'created_at',
                'count(id) expectedOrderSum',
                'sum(expected_number_places_qty) as expectedBoxSum',
                'acceptedOrderSum'=>$acceptedOrderSumQuery,
                'acceptedBoxM3Sum'=>$acceptedBoxM3SumQuery,
                'acceptedBoxSum'=>$acceptedBoxSumQuery,

            ])
            ->andWhere([
                'client_id' => $this->clientID,
                'status' => [
                    Stock::STATUS_CROSS_DOCK_NEW,
                    Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST,
                    Stock::STATUS_CROSS_DOCK_SCANNING,
                    Stock::STATUS_CROSS_DOCK_SCANNED,
                ],
                'deleted' => 0,
            ])
            ->andWhere(['>','created_at',self::CROSS_DOC_START_DATETIME])
            ->from(CrossDock::tableName().' as topCrossDock')
            ->groupBy('party_number')
            ->all();

//        return CrossDock::find()
//            ->select('party_number, count(id) as orderSum, sum(expected_number_places_qty) as expectedBoxSum, sum(accepted_number_places_qty) as acceptedBoxSum, sum(box_m3) as mcSum')
//            ->andWhere([
//                'client_id' => $this->clientID,
//                'status' => [
//                    Stock::STATUS_CROSS_DOCK_NEW,
//                    Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST,
//                    Stock::STATUS_CROSS_DOCK_SCANNING,
//                    Stock::STATUS_CROSS_DOCK_SCANNED,
//                ],
//                'accepted_number_places_qty'=> null,
//                'box_m3'=> 0,
//            ])
//            ->groupBy('party_number')
//            ->asArray()
//            ->all();
    }

    /*
 *
 * */
//    public function readyForDeliveryByStore()
//    {
//        return OutboundOrder::find()
//            ->select('to_point_id, count(id) as orderSum, sum(accepted_qty) as lotSum, sum(accepted_number_places_qty) as placeSum, sum(mc) as mcSum')
//            ->andWhere([
//                'client_id' => $this->clientID,
//                'status' => Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
//            ])
//            ->groupBy('to_point_id')
//            ->orderBy(['mcSum' => SORT_DESC])
//            ->asArray()
//            ->all();
//    }

}