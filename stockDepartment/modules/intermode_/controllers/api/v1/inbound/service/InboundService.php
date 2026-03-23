<?php

namespace app\modules\intermode\controllers\api\v1\inbound\service;

//use app\modules\ecommerce\controllers\intermode\inbound\domain\constants\InboundStatus;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\dto\add_order\AddOrderItemRequestDTO;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\dto\add_order\AddOrderRequestDTO;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInbound;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundDataMatrix;
//use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundItem;
use app\modules\intermode\controllers\api\v1\inbound\constants\InboundStatus;
use app\modules\intermode\controllers\api\v1\inbound\dto\add_order\AddOrderItemRequestDTO;
use app\modules\intermode\controllers\api\v1\inbound\dto\add_order\AddOrderRequestDTO;
use app\modules\intermode\controllers\api\v1\inbound\repository\InboundRepository;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use stockDepartment\modules\intermode\controllers\api\v1\inbound\dto\add_order\ValidateDTO;

class InboundService
{
    private $repository;



    /**
     * InboundService constructor.
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
		$orderId = $request['order_id'];
		$clientOrderId = $request['1с_uuid'];
		$items = $request['items'];
		$comment = isset($request["comment"]) ? $request["comment"] : "";
		$dto = new AddOrderRequestDTO();
		$dto->clientId = $this->getClientID();
		$dto->orderNumber = $orderId;
		$dto->uuid_1c = $clientOrderId;
		$dto->comment = $comment;

		$migrateItems = [];
		foreach ($items as $product) {
			if (isset($migrateItems[$product["barcode"]])) {
				$dm =  isset($product["datamatrix"]) ? $product["datamatrix"] : [];
				$migrateItems[$product["barcode"]]["quantity"] += $product["quantity"];
				$migrateItems[$product["barcode"]]["datamatrix"] = array_merge($migrateItems[$product["barcode"]]["datamatrix"],$dm);
			} else {
				$migrateItems[$product["barcode"]]["barcode"] = $product["barcode"];
				$migrateItems[$product["barcode"]]["quantity"] = $product["quantity"];
				$migrateItems[$product["barcode"]]["article"] = $product["article"];
				$migrateItems[$product["barcode"]]["guid"] = $product["guid"];
				$migrateItems[$product["barcode"]]["datamatrix"] = isset($product["datamatrix"]) ? $product["datamatrix"] : [];
			}
		}

		foreach ($migrateItems as $barcode=>$product) {
			$itemDto = new AddOrderItemRequestDTO();
			$itemDto->barcode = $product["barcode"];
			$itemDto->article = $product["article"];
			$itemDto->quantity = $product["quantity"];
			$itemDto->guid = $product["guid"];
			$itemDto->datamatrix = isset($product["datamatrix"]) ? $product["datamatrix"] : [];

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


	/**
	 * @param integer $inboundID
	 * @return InboundOrder
	 */
	public function getOrderByID($inboundID)
	{
		return $this->repository->getOrderByID($inboundID);;
	}
}