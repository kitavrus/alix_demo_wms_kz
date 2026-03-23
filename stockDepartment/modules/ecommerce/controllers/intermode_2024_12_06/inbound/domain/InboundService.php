<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain;

//use common\ecommerce\intermode\outbound\constants\OutboundStatus;
//use common\ecommerce\intermode\outbound\dto\add_order\AddOrderItemRequestDTO;
//use common\ecommerce\intermode\outbound\dto\add_order\AddOrderRequestDTO;
//use common\ecommerce\intermode\outbound\entities\EcommerceOutbound;
//use common\ecommerce\intermode\outbound\entities\EcommerceOutboundItem;
//use common\ecommerce\intermode\outbound\repository\OutboundRepository;

use app\modules\ecommerce\controllers\intermode\inbound\domain\constants\InboundStatus;
use app\modules\ecommerce\controllers\intermode\inbound\domain\dto\add_order\AddOrderItemRequestDTO;
use app\modules\ecommerce\controllers\intermode\inbound\domain\dto\add_order\AddOrderRequestDTO;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInbound;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundDataMatrix;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundItem;
use app\modules\ecommerce\controllers\intermode\inbound\domain\repository\InboundRepository;
use yii\helpers\VarDumper;

class InboundService
{
	private $repository;
	public function __construct()
	{
		$this->repository = new InboundRepository();
	}
	public function getOrderInfo($id) {
		return $this->repository->getOrderInfo($id);
	}

	/**
	 * @param AddOrderRequestDTO $createDTO
	 * @return EcommerceInbound
	 */
	public function addOrder($createDTO)
	{
//		VarDumper::dump($createDTO,10,true);
//		die;
		$order = new EcommerceInbound();
		$order->client_id = $createDTO->clientId;
		$order->order_number = $createDTO->orderNumber;
		$order->expected_product_qty = $createDTO->expectedQty;
		$order->status = InboundStatus::_NEW;
		$order->save(false);

		foreach ($createDTO->items as $item) {
			$row = new EcommerceInboundItem();
			$row->inbound_id = $order->id;
			$row->status = InboundStatus::_NEW;
			$row->product_name = $item->name;
			$row->product_barcode = $item->barcode;
			$row->product_brand = $item->brand;
			$row->product_color = $item->color;
			$row->product_expected_qty = $item->quantity;
			$row->product_model = $item->article;
			$row->save(false);

			if (empty($item->datamatrix)) {
				continue;
			}

			foreach ($item->datamatrix as $dmCode) {
				$dm = new EcommerceInboundDataMatrix();
				$dm->inbound_id =  $order->id;
				$dm->inbound_item_id =  $row->id;
				$dm->product_barcode =  $item->barcode;
				$dm->product_model =  $item->article;
				$dm->data_matrix_code =  $dmCode;
				$dm->save(false);
			}
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
		// Добавить проверку, что все нужные поля заполнены
		//foreach ($request['items'] as $item) {}


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
			$itemDto->datamatrix = isset($product["datamatrix"]) ? $product["datamatrix"] : [];
			$dto->items[] = $itemDto;
			$dto->expectedQty += $itemDto->quantity;
		}

		return $dto;
	}

}