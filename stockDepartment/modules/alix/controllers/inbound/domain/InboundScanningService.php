<?php

namespace stockDepartment\modules\alix\controllers\inbound\domain;


use stockDepartment\modules\alix\controllers\api\v1\inbound\mapper\InboundAPIMapper;
use app\modules\inbound\inbound;
use stockDepartment\modules\alix\controllers\stock\domain\StockService;
use stockDepartment\modules\alix\controllers\api\v1\inbound\constants\InboundAPIStatus;

class InboundScanningService
{
	private $inboundRepository;
	private $stockService;
	private $dto;

	/**
	 * ServiceInbound constructor.
	 *
	 * @param $dto array | \stdClass
	 */
	public function __construct($dto = [])
	{
		$this->dto = $dto;

		$this->inboundRepository = new InboundRepository();
		$this->stockService = new StockService();
//		$this->productService = new ProductService();
        $this->apiService = new \stockDepartment\modules\alix\controllers\api\v1\inbound\service\InboundAPIService();
	}
	/**
	 *
	 */
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
	/**
	 * @param integer $inboundId
	 */
	public function sendStatusInWork($inboundId) {
		$inboundOrder = $this->getOrder($inboundId);
		if ($inboundOrder && empty($inboundOrder->begin_datetime)) {
			if ($inboundOrder->order_type == 1) {
				$this->apiService->sendStatusInWorkInbound((new InboundAPIMapper())->makeByOrderStatusOrderResponseDTO($inboundOrder));
			}
			if ($inboundOrder->order_type == 2) {
				$this->apiService->sendStatusInWorkReturn((new InboundAPIMapper())->makeByOrderStatusOrderResponseDTO($inboundOrder));
			}
		}
	}

	public function sendStatusCompleted($inboundId) {
		$inboundOrder = $this->getOrder($inboundId);

		$status = InboundAPIStatus::COMPLETED;
		if ($inboundOrder->expected_qty != $inboundOrder->accepted_qty) {
			$status = InboundAPIStatus::COMPLETED_WITH_DIFFERENCES;
		}

		$mapper = new InboundAPIMapper();
		$dto = $mapper->makeByOrderStatusOrderResponseDTO($inboundOrder);

		if ($inboundOrder->order_type == 1) {
			$this->apiService->sendStatusCompletedInbound($dto, $status);

			// Дополнительный отдельный вызов: проведение прихода в 1С с фактическими
			// количествами по строкам. ChangeDocumentStatus только меняет статус,
			// сам документ проводится через InboundComplete. Возвраты (order_type=2)
			// не трогаем — для них 1С пока ничего не ожидает.
			$items = $this->inboundRepository->getOrderItems($inboundOrder->id);
			$completeDto = $mapper->makeInboundCompleteRequestDTO($inboundOrder, $items, $status);
			$this->apiService->sendInboundComplete($completeDto);
		}
		if ($inboundOrder->order_type == 2) {
			$this->apiService->sendStatusCompletedReturn($dto, $status);
		}
	}

	/**
	 * @return array|Inbound|\yii\db\ActiveRecord
	 */
	public function getOrder($id)
	{
		return $this->inboundRepository->getOrder($id);
	}
}