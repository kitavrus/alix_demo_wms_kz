<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:18
 */

namespace common\modules\inbound\repository;


use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\overloads\ArrayHelper;

class Repository
{
    private $id;

    public function createOrder($dto)
    {
        $inbound = new InboundOrder();
        $inbound->client_id =  ArrayHelper::getValue($dto,'clientId');
        $inbound->order_number =  ArrayHelper::getValue($dto,'orderNumber');
        $inbound->save(false);

        $this->setId($inbound->id);
    }

    //
    public function createOrderItems($data, $orderId)
    {
        foreach ($data->items as $item) {
            $inboundOrderItem = new InboundOrderItem();
            $inboundOrderItem->inbound_order_id = $orderId;
            $inboundOrderItem->product_name = $item->productName;
            $inboundOrderItem->product_barcode = $item->productBarcode;
            $inboundOrderItem->product_model = $item->productModel;
            $inboundOrderItem->expected_qty = $item->expectedProductQty;
            $inboundOrderItem->expected_number_places_qty = $item->expectedPlaceQty;
            $inboundOrderItem->save(false);
        }
    }

    public function deleteOrder($orderId) {
        InboundOrder::deleteAll(['id'=>$orderId]);
    }

    public function deleteOrderItems($orderId) {
       InboundOrderItem::deleteAll(['inbound_order_id'=>$orderId]);
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}