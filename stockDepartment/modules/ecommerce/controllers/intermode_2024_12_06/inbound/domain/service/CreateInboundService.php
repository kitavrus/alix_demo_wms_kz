<?php
namespace app\modules\ecommerce\controllers\intermode\inbound\domain\service;

use app\modules\ecommerce\controllers\intermode\inbound\domain\repository\InboundRepository;

class CreateInboundService
{
    private $repository;

    /**
     * InboundOrderUploadService constructor.
     */
    public function __construct($dto = [])
    {
        $this->repository = new InboundRepository($dto);
    }

    public function create($dto)
    {
        $this->repository->create($this->makeDTOForCreateInboundOrder($dto));
    }

    private function makeDTOForCreateInboundOrder($dto)
    {
        $dtoForCreateInboundOrder = new \stdClass();
        $dtoForCreateInboundOrder->orderNumber = trim($dto->orderNumber);
//        $dtoForCreateInboundOrder->expectedTotalProductQty = 0;
        $dtoForCreateInboundOrder->expectedBoxQty =  trim($dto->qtyBox);
        $dtoForCreateInboundOrder->items = [];

        return $dtoForCreateInboundOrder;
    }

}