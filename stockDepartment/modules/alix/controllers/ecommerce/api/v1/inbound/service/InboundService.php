<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\service;

use common\ecommerce\entities\EcommerceInbound;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\add_order\AddOrderItemRequestDTO;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\add_order\AddOrderRequestDTO;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\add_order\ValidateDTO;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\repository\InboundRepository;

class InboundService
{
    private $repository;

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
    public function isNotValidAddOrderData($request)
    {
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
    public function requestToCreateDTO($request)
    {
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
            // Клиент иногда присылает "datamatrix": "" (строка) — нормализуем к массиву,
            // иначе array_merge ниже и foreach в InboundRepository::createOrderItems упадут.
            $dm = isset($product["datamatrix"]) && is_array($product["datamatrix"])
                ? $product["datamatrix"]
                : [];

            if (isset($migrateItems[$product["barcode"]])) {
                $migrateItems[$product["barcode"]]["quantity"] += $product["quantity"];
                $migrateItems[$product["barcode"]]["datamatrix"] = array_merge($migrateItems[$product["barcode"]]["datamatrix"], $dm);
            } else {
                $migrateItems[$product["barcode"]]["barcode"] = $product["barcode"];
                $migrateItems[$product["barcode"]]["quantity"] = $product["quantity"];
                $migrateItems[$product["barcode"]]["article"] = isset($product["article"]) ? $product["article"] : "";
                $migrateItems[$product["barcode"]]["guid"] = isset($product["guid"]) ? $product["guid"] : "";
                $migrateItems[$product["barcode"]]["datamatrix"] = $dm;
            }
        }

        foreach ($migrateItems as $barcode => $product) {
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
     * @return EcommerceInbound
     */
    public function getOrderByID($inboundID)
    {
        return $this->repository->getOrderByID($inboundID);
    }
}
