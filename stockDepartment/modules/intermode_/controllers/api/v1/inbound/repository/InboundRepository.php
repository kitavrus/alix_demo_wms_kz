<?php

namespace app\modules\intermode\controllers\api\v1\inbound\repository;

use common\modules\dataMatrix\models\InboundDataMatrix;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use app\modules\intermode\controllers\api\v1\inbound\constants\InboundStatus;
use stockDepartment\modules\intermode\controllers\product\domains\ProductService;

class InboundRepository
{
	private $id;

    public function getClientID()
    {
        return 103;
    }

	public function createOrder($data)
	{
		$inboundOrder = new InboundOrder();
		$inboundOrder->client_id = $this->getClientID();
		$inboundOrder->client_order_id = $data->uuid_1c;
		$inboundOrder->from_point_id = $data->from_location;
		$inboundOrder->order_number = $data->orderNumber;
		$inboundOrder->supplier_id = 1;
		$inboundOrder->order_type = $data->type;
		$inboundOrder->status = InboundStatus::_NEW;
		$inboundOrder->cargo_status = InboundOrder::CARGO_STATUS_NEW;
		$inboundOrder->expected_qty = $data->expectedQty;
		$inboundOrder->accepted_qty = 0;
		$inboundOrder->accepted_number_places_qty = 0;
		$inboundOrder->expected_number_places_qty = 0;
		$inboundOrder->comments = $data->comment;
		$inboundOrder->save(false);

		return $inboundOrder->id;
	}

	public function createOrderItems($data, $orderId)
	{
		$ps = new ProductService();
		foreach ($data->items as $item) {
			$p = $ps->getByGuid($item->guid);

			$inboundOrderItem = new InboundOrderItem();
			$inboundOrderItem->inbound_order_id = $orderId;
			$inboundOrderItem->product_model = $item->article;
			$inboundOrderItem->product_barcode = $item->barcode;
			$inboundOrderItem->expected_qty = $item->quantity;
			$inboundOrderItem->product_sku = $item->guid;
			$inboundOrderItem->product_id = $p->product->id;
			$inboundOrderItem->product_name = $p->product->name;
			$inboundOrderItem->product_brand = $p->product->field_extra1;
			$inboundOrderItem->product_color = $p->product->color;
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
		return InboundOrder::find()
						   ->andWhere([
							   "client_id"=>$this->getClientID(),
							   "client_order_id"=>$uuid_1c,
							])
						   ->andWhere("status != :status",[":status"=>InboundStatus::_NEW])
						   ->exists();
	}

	public function getOrderBy1cUUID($uuid_1c)
	{
		return InboundOrder::find()
						   ->andWhere([
							   "client_id"=>$this->getClientID(),
							   "client_order_id"=>$uuid_1c,
							])
						   ->one();
	}
	public function deleteInboundOrderItem($id)
	{
		return InboundOrderItem::deleteAll(["inbound_order_id"=>$id]);
	}

	public function getOrderByID($inboundID)
	{
		return InboundOrder::find()
						   ->andWhere([
							   "id"=>$inboundID,
						   ])
						   ->one();
	}
}