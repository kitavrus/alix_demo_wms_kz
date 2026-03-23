<?php

namespace stockDepartment\modules\alix\controllers\api\v1\stock\service;

use stockDepartment\modules\alix\controllers\api\v1\stock\repository\StockRepository;

class StockService
{
    private $repository;

    /**
     * StockService constructor.
     */
    public function __construct()
    {
        $this->repository = new StockRepository();
    }

    public function getAllStock() {
        return $this->repository->getAvailableStock();
    }
}