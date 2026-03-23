<?php

namespace app\modules\intermode\controllers\api\v1\inbound\service;

use app\modules\intermode\controllers\api\v1\inbound\constants\InboundStatus;
use app\modules\intermode\controllers\api\v1\inbound\dto\add_order\AddOrderItemRequestDTO;
use app\modules\intermode\controllers\api\v1\inbound\dto\add_order\AddOrderRequestDTO;
use app\modules\intermode\controllers\api\v1\inbound\repository\InboundRepository;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use stockDepartment\modules\intermode\controllers\api\v1\inbound\dto\add_order\ValidateDTO;

class InboundReturnService
{
    private $repository;

    /**
     * InboundReturnService constructor.
     */
    public function __construct()
    {
        $this->repository = new InboundRepository();
    }

    public function getClientID()
	{
		return 103;
	}

	/**
	 * @param array $request
	 * @return ValidateDTO
	 */
	public function isNotValidAddOrderData($request) {
		if (!isset($request['order_id']) || !isset($request['items'])) {
			return (new ValidateDTO())->withError("Не заполнен order_id или items");
		}

		if (empty(trim($request['order_id']))) {
			return (new ValidateDTO())->withError("Пустой order_id");
		}

		if (!isset($request['1с_uuid'])) {
			return (new ValidateDTO())->withError("Не заполнен 1с_uuid");
		}

		if (empty(trim($request['1с_uuid']))) {
			return (new ValidateDTO())->withError("Пустой 1с_uuid");
		}

		if (!isset($request['from_location'])) {
			return (new ValidateDTO())->withError("Не заполнен from_location");
		}

		if (empty(trim($request['from_location']))) {
			return (new ValidateDTO())->withError("Пустой from_location");
		}

		if (count($request['items']) < 1) {
			return (new ValidateDTO())->withError("Пустой items, нет товаров");
		}

		if ($this->repository->canChange($request['1с_uuid'])) {
			return (new ValidateDTO())->withError("Такая накладная уже есть, она в работе");
		}

		return (new ValidateDTO())->withOutError("");
	}

	/**
	 * @param array $request
	 * @return AddOrderRequestDTO
	 */
	public function requestToCreateDTO($request) {
		$dto = new AddOrderRequestDTO();
		$dto->clientId = $this->getClientID();
		$dto->orderNumber = $request['order_id'];
		$dto->uuid_1c =  $request['1с_uuid'];
		$dto->from_location =  $request['from_location'];
		$dto->type = InboundOrder::ORDER_TYPE_RETURN;

		foreach ($request['items'] as $product) {
			$itemDto = new AddOrderItemRequestDTO();
			$itemDto->barcode = $product["barcode"];
			$itemDto->article = $product["article"];
			$itemDto->guid = $product["guid"];
			$itemDto->quantity = $product["quantity"];

//			$itemDto->datamatrix = isset($product["datamatrix"]) ? $product["datamatrix"] : [];

			$dto->items[] = $itemDto;
			$dto->expectedQty += $itemDto->quantity;
		}

		return $dto;
	}

	/**
	 * @param AddOrderRequestDTO $createDTO
	 * @return int
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function addOrder($createDTO)
	{
//		VarDumper::dump($createDTO,10,true);
//		die;

		$order = $this->repository->getOrderBy1cUUID($createDTO->uuid_1c);
		if (!empty($order)) {
			$order->delete();
			$this->repository->deleteInboundOrderItem($order->id);
		}

		$orderID = $this->repository->createOrder($createDTO);
		$this->repository->createOrderItems($createDTO, $orderID);
		return $orderID;
	}
}