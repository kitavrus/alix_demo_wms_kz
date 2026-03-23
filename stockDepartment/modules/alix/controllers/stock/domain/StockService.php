<?php
namespace stockDepartment\modules\alix\controllers\stock\domain;


use app\modules\ecommerce\controllers\alix\stock\domain\constants\StockAPIStatus;
use app\modules\ecommerce\controllers\alix\inbound\domain\entities\EcommerceInboundItem;
use app\modules\ecommerce\controllers\alix\stock\domain\entities\EcommerceStock;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class StockService
{
    private $repository;
	private $api;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new \stockDepartment\modules\alix\controllers\stock\domain\StockRepository();
//		$this->api = new \app\modules\ecommerce\controllers\alix\stock\domain\StockAPIService();
    }

	public function updateInboundDataMatrixId($stockId,$datamatrixId,$datamatrix) {
		return $this->repository->setInboundDataMatrixId($stockId,$datamatrixId,$datamatrix);
	}

	public function checkExistDataMatrixByStockId($stockId,$datamatrixId,$datamatrix) {
		return $this->repository->checkExistDataMatrixByStockId($stockId,$datamatrixId,$datamatrix);
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