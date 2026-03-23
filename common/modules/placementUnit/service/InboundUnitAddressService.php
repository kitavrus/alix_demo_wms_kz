<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.10.2017
 * Time: 17:19
 */

namespace common\modules\placementUnit\service;



class InboundUnitAddressService
{
    private $repository;
    private $dto;

    /**
     * Service constructor.
     * @param $dto array | \stdClass
     */
    public function __construct($dto = [])
    {
        $this->repository = new \common\modules\placementUnit\repository\InboundUnitAddressRepository();
        $this->dto = $dto;
    }
    //
    public function create($dto) {
        $this->repository->create($dto);
    }
    //
    public function isExist($barcode) {
        return  $this->repository->isExist($barcode);
    }
}