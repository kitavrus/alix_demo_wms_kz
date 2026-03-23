<?php

namespace app\modules\ecommerce\controllers\intermode\outbound\domain;

use app\modules\ecommerce\controllers\intermode\inbound\domain\mapper\InboundAPIMapper;
use app\modules\ecommerce\controllers\intermode\outbound\domain\repository\OutboundRepository;
use app\modules\ecommerce\controllers\intermode\outbound\domain\repository\OutboundReservationRepository;

class OutboundScanningService
{
	private $repository;
	private $repositoryReservation;
	private $apiService;

	public function __construct()
	{
		$this->repository = new OutboundRepository();
		$this->repositoryReservation = new OutboundReservationRepository();
		$this->apiService = new \app\modules\ecommerce\controllers\intermode\outbound\domain\mock\OutboundAPIService();
	}

	/**
	*
	 */
	public function makeScanned($dto)
	{
		 return	$this->repository->makeScannedProduct($dto);
	}

	public function makeScannedQRCode($dto)
	{
		$this->repository->makeScannedStockQRCode($dto);
	}


	public function packageBarcodeInfo($pickListBarcode,$packageBarcode)
	{
		$qtyProductInPackage = $this->repository->qtyProductInPackage($pickListBarcode,$packageBarcode);
		return [
			'qtyProductInPackage'=>$qtyProductInPackage,
		];
	}

	public function showOrderItems($pickListBarcode)
	{
		$items =  $this->repository->showOrderItems($pickListBarcode);
		return $items;
	}

	public function getOrderInfo($id) {
		return $this->repository->getOrderInfo($id);
	}

	public function emptyPackage($dto)
	{
		$this->repository->emptyPackage($dto);
	}

	public function package($orderNumber)
	{
		$order = $this->repository->getOrderByOrderNumber($orderNumber);
		$order = $this->repository->packageOrder($order->id);
		$response = (new InboundAPIMapper())->makeByStockAddOrderResponseDTO($this->repository->getOrderInfo($order->id));
//		VarDumper::dump($this->repository->getOrderInfo($order->id),10,true);
//		VarDumper::dump("<br />",10,true);
//		VarDumper::dump("<br />",10,true);
//		VarDumper::dump("<br />",10,true);
//		VarDumper::dump($response,10,true);
//		die;
		if ($order->expected_qty != $order->accepted_qty) {
			$apiStatus = $this->apiService->sendStatusPickedPartWarehouse($response);
		} else {
			$apiStatus = $this->apiService->sendStatusPickedWarehouse($response);
		}
		return $apiStatus;
	}

	/*
* Re Allocate outbound order
* @param integer $outbound_id
* */
	public function resetByOutboundOrderId($outbound_order_id)
	{
		$this->repositoryReservation->resetByOutboundOrderId($outbound_order_id);
	}

}