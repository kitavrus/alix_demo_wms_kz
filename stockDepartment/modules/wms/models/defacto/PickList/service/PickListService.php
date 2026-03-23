<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 30.08.2017
 * Time: 23:17
 */

namespace stockDepartment\modules\wms\models\defacto\PickList\service;


use stockDepartment\modules\wms\models\defacto\PickList\repository\PickListRepository;

class PickListService
{
    private $repository;
    private $dto;
    /**
     * PickListService constructor.
     */
    public function __construct($dto)
    {
        $this->dto = $dto;
        $this->repository = new PickListRepository();
    }

    public function setStatusScanned() {
        $this->repository->addStockStatusScanned($this->dto->pickListBarcode,$this->dto->lotBarcode);
        $this->repository->addPickListStatusScanInProcess($this->dto->pickListBarcode);
    }
}