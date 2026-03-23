<?php

namespace app\modules\ecommerce\controllers\intermode\outbound\domain;

use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\OutboundStatus;
use app\modules\ecommerce\controllers\intermode\outbound\domain\dto\add_order\AddOrderItemRequestDTO;
use app\modules\ecommerce\controllers\intermode\outbound\domain\dto\add_order\AddOrderRequestDTO;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutbound;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutboundItem;
use app\modules\ecommerce\controllers\intermode\outbound\domain\repository\OutboundRepository;

class OutboundService
{
	private $repository;
	public function __construct()
	{
		$this->repository = new OutboundRepository();
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
		$order->status = OutboundStatus::_NEW;
		$order->save(false);

		foreach ($createDTO->items as $item) {
			$row = new EcommerceOutboundItem();
			$row->outbound_id = $order->id;
			$row->status = OutboundStatus::_NEW;
			$row->product_name = $item->name;
			$row->product_barcode = $item->barcode;
			$row->product_brand = $item->brand;
			$row->product_color = $item->color;
			$row->expected_qty = $item->quantity;
			$row->product_model = $item->article;
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
		$dto = new AddOrderRequestDTO();
		$dto->clientId = 103;
		$dto->orderNumber = $orderId;

		foreach ($items as $product) {
			$itemDto = new AddOrderItemRequestDTO();
			$itemDto->name = $product["name"];
			$itemDto->barcode = $product["barcode"];
			$itemDto->brand = $product["brand"];
			$itemDto->color = $product["color"];
			$itemDto->quantity = $product["quantity"];
			$itemDto->article = $product["article"];
			$dto->items[] = $itemDto;
			$dto->expectedQty += $itemDto->quantity;
		}

		return $dto;
	}

}