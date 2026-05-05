<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\inbound\domain;

use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\constants\InboundAPIStatus;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\mapper\InboundAPIMapper;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\service\InboundAPIService;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\service\StockService;

class InboundScanningService
{
    private $inboundRepository;
    private $stockService;
    private $apiService;
    private $dto;

    /**
     * @param $dto array | \stdClass
     */
    public function __construct($dto = [])
    {
        $this->dto = $dto;
        $this->inboundRepository = new InboundRepository();
        $this->stockService = new StockService();
        $this->apiService = new InboundAPIService();
    }

    public function scanDataMatrix()
    {
        $dm = $this->inboundRepository->getAvailableDataMatrix(
            $this->dto->orderNumberId,
            $this->dto->productBarcode,
            $this->dto->datamatrix
        );
        $dm->status = InboundDataMatrix::SCANNED;
        $dm->save(false);
        $this->stockService->updateInboundDataMatrixId($this->dto->stockId, $dm->id, $this->dto->datamatrix);
    }

    public function sendStatusInWork($inboundId)
    {
        $inboundOrder = $this->getOrder($inboundId);
        if ($inboundOrder && empty($inboundOrder->begin_datetime)) {
            $this->apiService->sendStatusInWorkInbound(
                (new InboundAPIMapper())->makeByOrderStatusOrderResponseDTO($inboundOrder)
            );
        }
    }

    public function sendStatusCompleted($inboundId)
    {
        $inboundOrder = $this->getOrder($inboundId);

        $status = InboundAPIStatus::COMPLETED;
        if ($inboundOrder->expected_product_qty != $inboundOrder->accepted_product_qty) {
            $status = InboundAPIStatus::COMPLETED_WITH_DIFFERENCES;
        }

        $mapper = new InboundAPIMapper();
        $dto = $mapper->makeByOrderStatusOrderResponseDTO($inboundOrder);
        $this->apiService->sendStatusCompletedInbound($dto, $status);

        // Дополнительный отдельный вызов: проведение прихода в 1С с фактическими
        // количествами по строкам. ChangeDocumentStatus только меняет статус,
        // сам документ проводится через InboundComplete.
        $items = $this->inboundRepository->getOrderItems($inboundOrder->id);
        $completeDto = $mapper->makeInboundCompleteRequestDTO($inboundOrder, $items, $status);
        $this->apiService->sendInboundComplete($completeDto);
    }

    /**
     * @return array|\common\ecommerce\entities\EcommerceInbound|\yii\db\ActiveRecord
     */
    public function getOrder($id)
    {
        return $this->inboundRepository->getOrder($id);
    }
}
