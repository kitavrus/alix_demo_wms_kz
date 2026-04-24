<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\service;

class StockService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new StockRepository();
    }

    public function updateInboundDataMatrixId($stockId, $datamatrixId, $datamatrix)
    {
        return $this->repository->setInboundDataMatrixId($stockId, $datamatrixId, $datamatrix);
    }

    public function checkExistDataMatrixByStockId($stockId, $datamatrixId, $datamatrix)
    {
        return $this->repository->checkExistDataMatrixByStockId($stockId, $datamatrixId, $datamatrix);
    }

    public function getDataForInboundAPI($inboundOrderId)
    {
        return $this->repository->getDataForInboundAPI($inboundOrderId);
    }

    public function getDataForInboundReturnAPI($inboundOrderId)
    {
        return $this->repository->getDataForInboundReturnAPI($inboundOrderId);
    }

    public function getDataForOutboundAPI($outboundOrderId)
    {
        return $this->repository->getDataForOutboundAPI($outboundOrderId);
    }

    public function getDataForInboundAPIV2($inboundOrderId)
    {
        return $this->repository->getDataForInboundAPIV2($inboundOrderId);
    }
}
