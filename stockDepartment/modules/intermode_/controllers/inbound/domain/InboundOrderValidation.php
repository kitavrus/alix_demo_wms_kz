<?php
namespace app\modules\intermode\controllers\inbound\domain;

use app\modules\intermode\controllers\inbound\domain\InboundRepository;

class InboundOrderValidation
{
	private $inboundRepository;
	private $stockService;

	public function __construct()
	{
		$this->inboundRepository = new InboundRepository();
		$this->stockService = new \app\modules\intermode\controllers\stock\domain\StockService();
	}
	//
	public function isExtraBarcodeInOrder($inboundID,$barcode) {
		return $this->inboundRepository->IsExtraBarcodeInOrder($inboundID,$barcode);
	}

	public function isNotAvailableDataMatrix($inboundId,$productBarcode,$dataMatrix) {
		return $this->inboundRepository->isNotAvailableDataMatrix($inboundId,$productBarcode,$dataMatrix);
	}
	public function checkExistDataMatrixByStockId($stockId,$datamatrixId,$datamatrix) {
		return $this->stockService->checkExistDataMatrixByStockId($stockId,$datamatrixId,$datamatrix);
	}
}