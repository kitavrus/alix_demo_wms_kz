<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\repository;

use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceInboundItem;
use common\modules\dataMatrix\models\InboundDataMatrix;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\constants\InboundStatus;
use stockDepartment\modules\alix\controllers\product\domains\ProductService;

class InboundRepository
{
    public function getClientID()
    {
        return 103;
    }

    public function createOrder($data)
    {
        $inboundOrder = new EcommerceInbound();
        $inboundOrder->client_id = $this->getClientID();
        $inboundOrder->client_order_id = $data->uuid_1c;
        $inboundOrder->from_point_id = $data->from_location;
        $inboundOrder->order_number = $data->orderNumber;
        $inboundOrder->supplier_id = 1;
        $inboundOrder->status = InboundStatus::_NEW;
        $inboundOrder->expected_product_qty = $data->expectedQty;
        $inboundOrder->accepted_product_qty = 0;
        $inboundOrder->expected_box_qty = 0;
        $inboundOrder->accepted_box_qty = 0;
        $inboundOrder->comments = $data->comment;
        $inboundOrder->save(false);

        return $inboundOrder->id;
    }

    public function createOrderItems($data, $orderId)
    {
        $ps = new ProductService();
        foreach ($data->items as $item) {
            $p = $ps->resolveByGuidOrBarcodeOrArticle($item->guid, $item->barcode, $item->article);

            $resolvedGuid = !empty($item->guid)
                ? $item->guid
                : (!empty($p->product->guid) ? $p->product->guid : '');

            $inboundOrderItem = new EcommerceInboundItem();
            $inboundOrderItem->inbound_id = $orderId;
            $inboundOrderItem->product_model = $item->article;
            $inboundOrderItem->product_barcode = $item->barcode;
            $inboundOrderItem->product_expected_qty = $item->quantity;
            $inboundOrderItem->product_accepted_qty = 0;
            $inboundOrderItem->product_sku = $resolvedGuid;
            $inboundOrderItem->product_id = $p->product->id;
            $inboundOrderItem->product_name = $p->product->name;
            $inboundOrderItem->product_brand = $p->product->brand;
            $inboundOrderItem->product_color = $p->product->color_name;
            $inboundOrderItem->save(false);

            if (count($item->datamatrix) != 0) {
                foreach ($item->datamatrix as $dm) {
                    if (empty($dm)) {
                        continue;
                    }
                    $inboundDataMatrix = new InboundDataMatrix();
                    $inboundDataMatrix->inbound_id = $orderId;
                    $inboundDataMatrix->inbound_item_id = $inboundOrderItem->id;
                    $inboundDataMatrix->product_barcode = $item->barcode;
                    $inboundDataMatrix->product_model = $item->article;
                    $inboundDataMatrix->data_matrix_code = base64_decode($dm);
                    $inboundDataMatrix->save(false);
                }
            }
        }
    }

    public function canChange($uuid_1c)
    {
        return EcommerceInbound::find()
            ->andWhere([
                "client_id" => $this->getClientID(),
                "client_order_id" => $uuid_1c,
            ])
            ->andWhere("status != :status", [":status" => InboundStatus::_NEW])
            ->exists();
    }

    public function getOrderBy1cUUID($uuid_1c)
    {
        return EcommerceInbound::find()
            ->andWhere([
                "client_id" => $this->getClientID(),
                "client_order_id" => $uuid_1c,
            ])
            ->one();
    }

    public function deleteInboundOrderItem($id)
    {
        return EcommerceInboundItem::deleteAll(["inbound_id" => $id]);
    }

    public function getOrderByID($inboundID)
    {
        return EcommerceInbound::find()
            ->andWhere(["id" => $inboundID])
            ->one();
    }
}
