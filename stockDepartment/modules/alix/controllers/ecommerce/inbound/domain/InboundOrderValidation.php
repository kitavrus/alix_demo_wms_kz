<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\inbound\domain;

class InboundOrderValidation
{
    private $inboundRepository;
    private $stockService;

    public function __construct()
    {
        $this->inboundRepository = new InboundRepository();
        $this->stockService = new \stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\service\StockService();
    }

    public function isExtraBarcodeInOrder($inboundID, $barcode)
    {
        return $this->inboundRepository->isExtraBarcodeInOrder($inboundID, $barcode);
    }

    public function isNotAvailableDataMatrix($inboundId, $productBarcode, $dataMatrix)
    {
        return $this->inboundRepository->isNotAvailableDataMatrix($inboundId, $productBarcode, $dataMatrix);
    }

    public function checkExistDataMatrixByStockId($stockId, $datamatrixId, $datamatrix)
    {
        return $this->stockService->checkExistDataMatrixByStockId($stockId, $datamatrixId, $datamatrix);
    }
}
