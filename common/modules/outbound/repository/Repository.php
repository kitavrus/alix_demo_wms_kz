<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:18
 */

namespace common\modules\outbound\repository;


use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\overloads\ArrayHelper;

class Repository
{
    private $id;

    public function createOrder($dto)
    {
        $outbound = new OutboundOrder();
        $outbound->client_id =  ArrayHelper::getValue($dto,'clientId');
        $outbound->client_order_id =  ArrayHelper::getValue($dto,'clientOrderId');
        $outbound->supplier_id =  ArrayHelper::getValue($dto,'supplierId');
        $outbound->warehouse_id =  ArrayHelper::getValue($dto,'warehouseId');
        $outbound->from_point_id =  ArrayHelper::getValue($dto,'fromPointId');
        $outbound->to_point_id =  ArrayHelper::getValue($dto,'toPointId');
        $outbound->to_point_title =  ArrayHelper::getValue($dto,'toPointTitle');
        $outbound->from_point_title =  ArrayHelper::getValue($dto,'fromPointTitle');
        $outbound->order_number =  ArrayHelper::getValue($dto,'orderNumber');
        $outbound->parent_order_number =  ArrayHelper::getValue($dto,'parentOrderNumber');
        $outbound->zone =  ArrayHelper::getValue($dto,'zoneId');
        $outbound->consignment_outbound_order_id =  ArrayHelper::getValue($dto,'consignmentOutboundOrderId');
        $outbound->order_type =  ArrayHelper::getValue($dto,'orderType');
        $outbound->delivery_type =  ArrayHelper::getValue($dto,'deliveryType');
        $outbound->status =  ArrayHelper::getValue($dto,'status');
        $outbound->cargo_status =  ArrayHelper::getValue($dto,'cargoStatus');
        $outbound->extra_status =  ArrayHelper::getValue($dto,'extraStatus');
        $outbound->mc =  ArrayHelper::getValue($dto,'mc');
        $outbound->kg =  ArrayHelper::getValue($dto,'kg');
        $outbound->expected_qty =  ArrayHelper::getValue($dto,'expectedQty');
        $outbound->accepted_qty =  ArrayHelper::getValue($dto,'acceptedQty');
        $outbound->allocated_qty =  ArrayHelper::getValue($dto,'allocatedQty');
        $outbound->accepted_number_places_qty =  ArrayHelper::getValue($dto,'acceptedNumberPlacesQty');
        $outbound->expected_number_places_qty =  ArrayHelper::getValue($dto,'expectedNumberPlacesQty');
        $outbound->allocated_number_places_qty =  ArrayHelper::getValue($dto,'allocatedNumberPlacesQty');
        $outbound->expected_datetime =  ArrayHelper::getValue($dto,'expectedDatetime');
        $outbound->begin_datetime =  ArrayHelper::getValue($dto,'beginDatetime');
        $outbound->end_datetime =  ArrayHelper::getValue($dto,'endDatetime');
        $outbound->date_confirm =  ArrayHelper::getValue($dto,'dateConfirm');
        $outbound->data_created_on_client =  ArrayHelper::getValue($dto,'dataCreatedOnClient');
        $outbound->extra_fields =  ArrayHelper::getValue($dto,'extraFields');
        $outbound->title =  ArrayHelper::getValue($dto,'title');
        $outbound->description =  ArrayHelper::getValue($dto,'description');
        $outbound->packing_date =  ArrayHelper::getValue($dto,'packingDate');
        $outbound->date_left_warehouse =  ArrayHelper::getValue($dto,'dateLeftWarehouse');
        $outbound->date_delivered =  ArrayHelper::getValue($dto,'dateDelivered');
        $outbound->save(false);

        $this->setId($outbound->id);
    }

    //
    public function createOrderItems($data, $orderId)
    {
        foreach ($data->items as $item) {
            $outboundOrderItem = new OutboundOrderItem();
            $outboundOrderItem->outbound_order_id = $orderId;
            $outboundOrderItem->product_name = $item->productName;
            $outboundOrderItem->product_barcode = $item->productBarcode;
            $outboundOrderItem->product_model = $item->productModel;
            $outboundOrderItem->expected_qty = $item->expectedProductQty;
            $outboundOrderItem->expected_number_places_qty = $item->expectedPlaceQty;
            $outboundOrderItem->save(false);
        }
    }

    public function deleteOrder($orderId) {
        OutboundOrder::deleteAll(['id'=>$orderId]);
    }

    public function deleteOrderItems($orderId) {
        OutboundOrderItem::deleteAll(['outbound_order_id'=>$orderId]);
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