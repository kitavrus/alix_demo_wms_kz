<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */

namespace common\modules\warehouseAddress\service;


class RackAddressService
{
    private $repository;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new \common\modules\warehouseAddress\repository\RackAddressRepository();
    }

    public function create($dto)
    {
       return $this->repository->create($dto);
    }

    public function isExists($rackAddressBarcode) {
        return $this->repository->isExists($rackAddressBarcode);
    }
}