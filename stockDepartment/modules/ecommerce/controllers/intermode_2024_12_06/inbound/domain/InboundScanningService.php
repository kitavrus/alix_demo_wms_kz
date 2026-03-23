<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:02
 */

namespace app\modules\ecommerce\controllers\intermode\inbound\domain;

use app\modules\ecommerce\controllers\intermode\inbound\domain\constants\InboundAPIStatus;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInbound;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundDataMatrix;
use app\modules\ecommerce\controllers\intermode\inbound\domain\mapper\InboundAPIMapper;
use app\modules\ecommerce\controllers\intermode\inbound\domain\repository\InboundRepository;
use app\modules\ecommerce\controllers\intermode\stock\domain\StockService;
use common\modules\product\service\ProductService;
use common\overloads\ArrayHelper;
use yii\helpers\VarDumper;

class InboundScanningService
{
	private $inboundRepository;
	private $stockService;
	private $productService;
	private $apiService;
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
		$this->productService = new ProductService();
//        $this->apiService = new \app\modules\ecommerce\controllers\intermode\inbound\domain\InboundAPIService();
		$this->apiService = new \app\modules\ecommerce\controllers\intermode\inbound\domain\mock\InboundAPIService();
	}

	/**
	 * @return array|EcommerceInbound|\yii\db\ActiveRecord
	 */
	public function getOrder($id)
	{
		return $this->inboundRepository->getOrder($id);
	}

	/**
	 *
	 */
	public function getNewAndInProcessOrder()
	{
		return ArrayHelper::map($this->inboundRepository->getNewAndInProcessOrder(), 'id', 'order_number');
	}

	/**
	 *
	 */
	public function getQtyModelsInOrder()
	{
		return $this->inboundRepository->getQtyModelsInOrder($this->dto->orderNumberId, $this->dto->productBarcode);
	}

	/**
	 *
	 */
	public function isEmptyProductBarcodeByModel()
	{
		$productService = new ProductService();
		return $productService->isEmptyProductBarcodeByModel($this->inboundRepository->getClientID(), $this->dto->productModel);
	}

	/**
	 *
	 */
	public function getItemsForDiffReportByOrderId($orderNumberId)
	{
		$clientBoxBarcodeList = $this->inboundRepository->getItemsForDiffReportByOrderId($orderNumberId);
		if (!empty($clientBoxBarcodeList)) {
			return $this->inboundRepository->getItemsForDiffReportByClientBoxBarcodeList($clientBoxBarcodeList);
		}

		return [];
	}

	/**
	 *
	 */
	public function productSumQtyInClientBox($aInboundId)
	{
		return $this->inboundRepository->productSumQtyInClientBox($aInboundId);
	}

	/**
	 *
	 */
	public function productSumQtyInOurBox($aInboundId, $aOurBoxBarcode)
	{
		return $this->inboundRepository->productSumQtyInOurBox($aInboundId, $aOurBoxBarcode);
	}

	/**
	 *
	 */
	public function scanClientBoxBarcode()
	{
		$productSumQtyInClientBox = $this->productSumQtyInClientBox($this->dto->orderNumberId);

		$this->inboundRepository->updateScannedClientBoxInOrder($this->dto->orderNumberId);
		$this->inboundRepository->updateExpectedProductInOrder($this->dto->orderNumberId);

		return [
			'productAcceptedQty' => ArrayHelper::getValue($productSumQtyInClientBox, 'productAcceptedQty'),
			'productExpectedQty' => ArrayHelper::getValue($productSumQtyInClientBox, 'productExpectedQty'),
		];
	}

	/**
	 *
	 */
	public function scanOurBoxBarcode()
	{
		$productSumQtyInClientBox = $this->productSumQtyInOurBox($this->dto->orderNumberId, $this->dto->ourBoxBarcode);

		return [
			'productAcceptedQty' => $productSumQtyInClientBox,
		];
	}

	/**
	 *
	 */
	public function scanProductBarcode()
	{
		// TODO For this create mapper
		$dtoForCreateStock = new \stdClass();
		$dtoForCreateStock->clientId = $this->inboundRepository->getClientID();
		$dtoForCreateStock->inboundId = $this->dto->orderNumberId;
		$dtoForCreateStock->productBarcode = $this->dto->productBarcode;
		$dtoForCreateStock->conditionType = $this->dto->conditionType;
		$dtoForCreateStock->boxAddressBarcode = $this->dto->ourBoxBarcode;
		$dtoForCreateStock->statusInbound = $this->stockService->getStatusInboundScanned();
		$dtoForCreateStock->statusAvailability = $this->stockService->getStatusAvailabilityNO();
		$dtoForCreateStock->scanInDatetime = $this->stockService->makeScanInboundDatetime();

		$inboundItem = $this->inboundRepository->getItemByProductBarcode($this->dto->orderNumberId, $this->dto->productBarcode);
		if ($inboundItem) {
			$dtoForCreateStock->inboundItemId = $inboundItem->id;
			$dtoForCreateStock->productModel = $inboundItem->product_model;
		}

		$this->stockService->create($dtoForCreateStock);
		$this->dto->clientId = $this->inboundRepository->getClientID();

		$beginDatetime = $this->getOrder($this->dto->orderNumberId)->begin_datetime;
		$this->inboundRepository->updateAcceptedQtyItemByProductBarcode($this->dto->orderNumberId, $this->dto->productBarcode);
		$this->inboundRepository->updateQtyScannedInOrder($this->dto->orderNumberId, $this->stockService->getScannedQtyByOrderInStock($this->dto->orderNumberId));
		$this->inboundRepository->setOrderStatusInProcess($this->dto->orderNumberId);
		$this->inboundRepository->setOrderItemStatusInProcess($this->dto->orderNumberId, $this->dto->productBarcode);
		$this->inboundRepository->updateScannedClientBoxInOrder($this->dto->orderNumberId);

		$productSumQtyInOurBox = $this->productSumQtyInOurBox($this->dto->orderNumberId, $this->dto->ourBoxBarcode);

		$inboundOrder = $this->getOrder($this->dto->orderNumberId);
		if (empty($beginDatetime)) {
			$this->apiService->sendStatusInWork((new InboundAPIMapper())->makeByOrderStatusOrderResponseDTO($inboundOrder));
		}

		return [
			'InOurBoxProductAcceptedQty' => $productSumQtyInOurBox,
			'stockId' => $this->stockService->getStockId(),
			'expected_product_qty' => $inboundOrder->expected_product_qty,
			'accepted_product_qty' => $inboundOrder->accepted_product_qty,
		];
	}

	/**
	 *
	 */
	public function getOrderItems()
	{
		return $this->inboundRepository->getItemsByOrderId($this->dto->orderNumberId);
	}

	/**
	 *
	 */
	public function cleanOurBox()
	{
		$productBarcodeWithQtyInBox = $this->stockService->cleanOurBox($this->dto->ourBoxBarcode, $this->dto->orderNumberId);
		$this->inboundRepository->updateAcceptedQtyItems($this->dto->orderNumberId, $productBarcodeWithQtyInBox);
		$this->inboundRepository->updateQtyScannedInOrder($this->dto->orderNumberId, $this->stockService->getScannedQtyByOrderInStock($this->dto->orderNumberId));

		foreach ($productBarcodeWithQtyInBox as $item) {
			if (empty($item["datamatrixIds"])) {
				continue;
			}
			$dms = explode(",",$item["datamatrixIds"]);
			if (empty($dms) || !is_array($dms)) {
				continue;
			}
			$this->inboundRepository->setToNotScannedDataMatrix($dms);
		}

	}

	/**
	 *
	 */
	public function getQtyByBoxBarcodeInOrder()
	{
		return $this->stockService->getQtyByBoxBarcodeInOrder($this->dto->ourBoxBarcode, $this->dto->orderNumberId);
	}

	/**
	 *
	 */
	public function done()
	{
		$this->inboundRepository->setOrderStatusComplete($this->dto->orderNumberId);
		$this->inboundRepository->setDateConfirm($this->dto->orderNumberId);
		$this->stockService->setStatusNewAndAvailableYes($this->dto->orderNumberId);

		$scannedProducts = $this->stockService->getDataForInboundAPI($this->dto->orderNumberId);
		$inboundOrder = $this->inboundRepository->getOrder($this->dto->orderNumberId);

		$status = InboundAPIStatus::COMPLETED;
		if ($inboundOrder->expected_product_qty != $inboundOrder->accepted_product_qty) {
			$status = InboundAPIStatus::COMPLETED_WITH_DIFFERENCE;
		}

		$this->apiService->sendStatusCompletedWithStatus(
			(new InboundAPIMapper())->makeByOrderStatusWithDataOrderResponseDTO($inboundOrder, $scannedProducts),
			$status
		);
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
		$dm->status = EcommerceInboundDataMatrix::SCANNED;
		$dm->save(false);
		$this->stockService->updateInboundDataMatrixId($this->dto->stockId, $dm->id, $this->dto->datamatrix);
	}

	/**
	 *
	 */
	public function test($inboundID)
	{
//		$inboundOrder = $this->inboundRepository->getOrder($inboundID);
//		$scannedProducts = $this->stockService->getDataForInboundAPI($inboundID);
//		$map = (new InboundAPIMapper())->makeByOrderStatusWithDataOrderResponseDTO($inboundOrder, $scannedProducts);
//		VarDumper::dump($scannedProducts, 10, true);
//		VarDumper::dump($map, 10, true);

//		$productBarcodeWithQtyInBox = $this->stockService->cleanOurBox("100000000007",$inboundID);
//		foreach ($productBarcodeWithQtyInBox as $item) {
//			if (empty($item["datamatrixIds"])) {
//				continue;
//			}
//			$dms = explode(",",$item["datamatrixIds"]);
//			if (empty($dms) || !is_array($dms)) {
//				continue;
//			}
//			$this->inboundRepository->setToNotScannedDataMatrix($dms);
//		}
//		VarDumper::dump($productBarcodeWithQtyInBox, 10, true);
	}
}