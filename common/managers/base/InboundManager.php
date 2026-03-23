<?php
namespace common\managers\base;
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 26.04.2016
 * Time: 10:26
 */
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;

class InboundManager
{
    /*
     * Get count product in box
     * @param string $boxBarcode
     * @param integer $inboundId
     * @return integer Count product in box
     * */
    public static function getScannedProductInBox($boxBarcode,$inboundId)
    {
       return InboundOrderItem::getScannedProductInBox($boxBarcode,$inboundId);
    }

    /*
    * Get count inbound items by id
    * @return integer $inboundId inbound order id
    * @return array
    * */
    public static function getCountItemByID($inboundId)
    {
        if($i = InboundOrder::findOne($inboundId)) {
            return $i->accepted_qty;
        }
        return 0;
    }

    /*
    * Get inbound items sorted by diff
    * @param integer $inboundId
    * @return array
    * */
    public function getAllOrderItemsSortedByDiff($inboundId)
    {
        if($i = InboundOrder::findOne($inboundId)) {
            return $i->getOrderItemsSortedByDiff();
        }
        return [];
    }

    /*
     * Get inbound order in status STATUS_INBOUND_CONFIRM and STATUS_INBOUND_PREPARED_DATA_FOR_API by client
     * @param integer $client_id Client id
     * @return array
     * */
    public function getCompleteInboundOrdersByClientId($clientId)
    {
        return InboundOrder::getCompleteOrderByClientID($clientId);
    }

    /*
     * @param integer $client_id Client id
     * @param string $type Type Get data from ConsignmentInboundOrders or InboundOrder
     * @return array
     * */
    public function getNewAndInProcessItemByClientId($clientId,$type = 'party-inbound')
    {
        $data = [];
        if($type == 'party-inbound') {
            $data = ConsignmentInboundOrders::getNewAndInProcessItemByClientID($clientId);
        } elseif($type == 'inbound') {
            $data = InboundOrder::getNewAndInProcessItemByClientID($clientId);
        }

        return $data;
    }
}