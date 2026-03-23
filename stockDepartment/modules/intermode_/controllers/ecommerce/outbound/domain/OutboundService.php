<?php

namespace app\modules\intermode\controllers\ecommerce\outbound\domain;

use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundSource;
use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
use app\modules\intermode\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderItemRequestDTO;
use app\modules\intermode\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderRequestDTO;
use app\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
use app\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItem;
use app\modules\intermode\controllers\ecommerce\outbound\domain\repository\OutboundRepository;
use stockDepartment\modules\intermode\controllers\product\domains\ProductService;

class OutboundService
{
	private $repository;
	private $productService;
	public function __construct()
	{
		$this->repository = new OutboundRepository();
		$this->productService = new ProductService();
	}
	public function getOrderInfo($id) {
		return $this->repository->getOrderInfo($id);
	}

	/**
	 * @param AddOrderRequestDTO $createDTO
	 * @return EcommerceOutbound
	 */
	public function addOrder($createDTO)
	{
		$order = new EcommerceOutbound();
		$order->client_id = $createDTO->clientId;
		$order->order_number = $createDTO->orderNumber;
		$order->expected_qty = $createDTO->expectedQty;
		$order->client_ShipmentSource = $createDTO->shipmentSource;
		$order->status = OutboundStatus::getNEW();
		$order->save(false);

		foreach ($createDTO->items as $item) {
			$p = $this->productService->getByGuid($item->guid);
			$barcode = "";
			if (count($p->barcodes)  > 0) {
				$barcode = array_values($p->barcodes)[0];
			}

			$row = new EcommerceOutboundItem();
			$row->outbound_id = $order->id;
			$row->status = OutboundStatus::getNEW();
			$row->product_sku = $item->guid;
			$row->expected_qty = $item->quantity;
			$row->product_id = $p->product->id;
			$row->product_name = $p->product->name;
			$row->product_barcode = $barcode;
			$row->product_brand = $p->product->field_extra1;
			$row->product_color = $p->product->color;
			$row->product_model = $p->product->model;
			$row->save(false);
		}

		return $order;
	}

	/**
	 * @param array $request
	 * @return false
	 */
	public function isNotValidAddOrderData($request) {
		if (!isset($request['order_id']) || !isset($request['items'])) {
			return true;
		}

		if (count($request['items']) < 1) {
			return true;
		}
		return false;
	}

	/**
	 * @param array $request
	 * @return AddOrderRequestDTO
	 */
	public function requestToCreateDTO($request) {
		$orderId = $request['order_id'];
		$items = $request['items'];
		$shipmentSource = $this->getShipmentSource($request);
		$dto = new AddOrderRequestDTO();
		$dto->clientId = 103;
		$dto->orderNumber = $orderId;
		$dto->shipmentSource = $shipmentSource;

		foreach ($items as $product) {
			$itemDto = new AddOrderItemRequestDTO();
			$itemDto->guid = $product["guid"];
			$itemDto->quantity = $product["quantity"];
//			$itemDto->name = $product["name"];
//			$itemDto->barcode = $product["barcode"];
//			$itemDto->brand = $product["brand"];
//			$itemDto->color = $product["color"];
//			$itemDto->article = $product["article"];
			$dto->items[] = $itemDto;
			$dto->expectedQty += $itemDto->quantity;
		}

		return $dto;
	}

	public function getShipmentSource($request) {
		$shipmentSource = OutboundSource::getCRM();
		if (isset($request['shipmentSource']) && !empty(trim($request['shipmentSource']))) {
			$shipmentSource = $request['shipmentSource'];
		} else if (strlen($request['order_id']) == 9) {
			$shipmentSource = OutboundSource::getKASPI();
		}
		return $shipmentSource;
	}
	
	/**
	 * @param array $request
	 * @return false
	 */
	public function isNotValidCancelOrderData($request) {
		if (!isset($request['order_id'])) {
			return true;
		}
		if (empty($request['order_id'])) {
			return true;
		}
		return false;
	}

	/**
	 * @param string $orderNumber
	 * @return EcommerceOutbound
	 */
	public function cancelOrder($orderNumber)
	{
		$order = EcommerceOutbound::find()->andWhere(["order_number"=>$orderNumber])->one();
		if ($order) {
			$order->client_CancelReason = OutboundStatus::getCANCEL();
			$order->save(false);
		}
		return $order;
	}
	
}