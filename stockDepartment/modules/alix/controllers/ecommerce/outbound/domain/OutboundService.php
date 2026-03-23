<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain;

use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundSource;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderItemRequestDTO;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderRequestDTO;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItem;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\repository\OutboundRepository;
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
		
		$order->first_name = $createDTO->firstName;
		$order->last_name = $createDTO->lastName;
		$order->customer_name = $createDTO->customerName;
		$order->email = $createDTO->email;
		$order->phone_mobile1 = $createDTO->phoneMobile;
		$order->country = $createDTO->country;
		$order->region = $createDTO->region;
		$order->city = $createDTO->city;
		$order->zip_code = $createDTO->zipCode;
		$order->street = $createDTO->street;
		$order->save(false);
		
		$clientPackMessage = [];

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
			
			$clientPackMessage['items'][] = [
				'lamoda_sku' => $item->lamoda_sku,
				'item_name' => $item->item_name,
				'quantity' => $item->quantity,
				'unit_price' => $item->paidPrice,
			];
		}
		
		$order->client_PackMessage = json_encode($clientPackMessage, JSON_UNESCAPED_UNICODE);
		$order->save(false);

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
		$dto->firstName = isset($request['firstName']) ? $request['firstName'] : "";
		$dto->lastName = isset($request['lastName']) ? $request['lastName'] : "";
		$dto->customerName = isset($request['customerName']) ? $request['customerName'] : "";
		$dto->email = isset($request['email']) ? $request['email'] : "";
		$dto->phoneMobile = isset($request['phoneMobile']) ? $request['phoneMobile'] : "";
		$dto->country = isset($request['country']) ? $request['country'] : "";
		$dto->region = isset($request['region']) ? $request['region'] : "";
		$dto->city = isset($request['city']) ? $request['city'] : "";
		$dto->zipCode = isset($request['zipCode']) ? $request['zipCode'] : "";
		$dto->street = isset($request['street']) ? $request['street'] : "";

		foreach ($items as $product) {
			$itemDto = new AddOrderItemRequestDTO();
			$itemDto->guid = $product["guid"];
			$itemDto->quantity = $product["quantity"];
			$itemDto->lamoda_sku = isset($product["lamoda_sku"]) ? $product['lamoda_sku'] : "";
			$itemDto->item_name = isset($product["item_name"]) ? $product['item_name'] : "";
			$itemDto->paidPrice = isset($product["paidPrice"]) ? $product['paidPrice'] : "";

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