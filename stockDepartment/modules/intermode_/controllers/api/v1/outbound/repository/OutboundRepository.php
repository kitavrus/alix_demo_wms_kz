<?php

namespace app\modules\intermode\controllers\api\v1\outbound\repository;

//use common\modules\dataMatrix\models\InboundDataMatrix;
use app\modules\intermode\controllers\api\v1\outbound\dto\add_order\AddOrderRequestDTO;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use app\modules\intermode\controllers\api\v1\outbound\constants\OutboundStatus;
use common\modules\stock\models\Stock;
use stockDepartment\modules\intermode\controllers\product\domains\ProductService;

class OutboundRepository
{
	private $id;

    public function getClientID()
    {
        return 103;
    }
	/**
	 * @param AddOrderRequestDTO $createDTO
	 * @return OutboundOrder
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function createOrder($createDTO)
	{
			$o = OutboundOrder::findOne([
				'client_id' =>$this->getClientID(),
				'order_number' => $createDTO->orderNumber]
			);
			if (!$o) {
				$o = new OutboundOrder();
				$o->status = Stock::STATUS_OUTBOUND_NEW;
			}
			$o->client_id = $this->getClientID();
			$o->order_number = $createDTO->orderNumber;
			$o->from_point_id = 745;
			$o->to_point_id = $createDTO->toLocation;
			$o->expected_qty =$createDTO->expectedQty;
			$o->accepted_qty = 0;
			$o->description = $createDTO->comment;
			$o->save(false);
			return $o;
	}

	public function createOrderItems($data, $orderId)
	{
		$ps = new ProductService();
		foreach ($data->items as $item) {
			$p = $ps->getByGuid($item->guid);
			$barcode = "";
			if (count($p->barcodes)  > 0) {
				$barcode = array_values($p->barcodes)[0];
			}
			$inboundOrderItem = new OutboundOrderItem();
			$inboundOrderItem->status = Stock::STATUS_OUTBOUND_NEW;
			$inboundOrderItem->outbound_order_id = $orderId;
			$inboundOrderItem->product_sku = $item->guid;
			$inboundOrderItem->expected_qty = $item->quantity;
			$inboundOrderItem->product_id = $p->product->id;
			$inboundOrderItem->product_model = $p->product->model;
			$inboundOrderItem->product_name = $p->product->name;
			$inboundOrderItem->product_barcode = $barcode;
			$inboundOrderItem->save(false);
		}
	}

	public function canChange($orderNumber)
	{
		return OutboundOrder::find()
						   ->andWhere([
							   "client_id"=>$this->getClientID(),
							   "order_number"=>$orderNumber,
							])
						   ->andWhere("status != :status",[":status"=>Stock::STATUS_OUTBOUND_NEW])
						   ->exists();
	}

	public function getOrderByOrderNumber($orderNumber)
	{
		return OutboundOrder::find()
						   ->andWhere([
							   "client_id"=>$this->getClientID(),
							   "order_number"=>$orderNumber,
							])
						   ->one();
	}
	public function deleteOutboundOrderItem($id)
	{
		return OutboundOrderItem::deleteAll(["outbound_order_id"=>$id]);
	}
	
	public function getOrderByID($outboundID)
	{
		return OutboundOrder::find()
						   ->andWhere([
							   "id"=>$outboundID,
						   ])
						   ->one();
	}
}