<?php

namespace app\modules\intermode\controllers\outbound\domain;

use app\modules\intermode\controllers\api\v1\outbound\dto\status_order\StatusOrderLogDTO;
use app\modules\intermode\controllers\api\v1\outbound\mapper\OutboundAPIMapper;
use app\modules\intermode\controllers\stock\domain\StockService;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;
use stdClass;

class OutboundService
{
	private $apiService;
	private $repository;
	/**
	 *
	 */
	public function __construct()
	{
		$this->apiService = new \app\modules\intermode\controllers\api\v1\outbound\service\OutboundAPIService();
		$this->repository = new \app\modules\intermode\controllers\outbound\domain\OutboundRepository();
	}

	/**
	 * @param integer $outboundId
	 */
	public function sendStatusInWork($outboundId) {
		$order = $this->repository->getOrder($outboundId);
		if ($order && empty($order->begin_datetime)) {
			$this->apiService->sendStatusInWork((new OutboundAPIMapper())->makeByOrderStatusOrderResponseDTO($order));
		}
	}

	public function reservation($outbound_order_id,$address = [])
	{
		if($oo = OutboundOrder::findOne($outbound_order_id)) {

			if (!in_array($oo->status,[Stock::STATUS_OUTBOUND_NEW,Stock::STATUS_OUTBOUND_PART_RESERVED])) {
				return;
			}

			$allocatedQty = 0;
			$inboundItemsGroupBySkuIDs = [];
			$expectedQty = 0;
			if($items = $oo->getOrderItems()->all()) {

				foreach($items as $itemLine) {
					$inboundItemsGroupBySkuIDs[$itemLine->product_id][] = $itemLine;
				}

				foreach($inboundItemsGroupBySkuIDs as $skuID=>$inboundItems) {
					$currentSkuIDToLines = [];
					foreach ($inboundItems as $i=>$item) {
						if($i == 0) {
							$expectedQty += $item->expected_qty;
							$currentSkuIDToLines['exp'] = $item->expected_qty;
							$currentSkuIDToLines['res'] = 0;
						}
						$currentSkuIDToLines['lines'][$item->id] = 0;

						$item->expected_qty -= $currentSkuIDToLines['res'];
						$item->allocated_qty = 0;
						$item->status = Stock::STATUS_OUTBOUND_RESERVING;

						$inStocksQuery = Stock::find()
											  ->andWhere([
//												  'product_barcode'=>$item->product_barcode,
												  'product_id'=>$skuID,
												  'client_id'=>$oo->client_id,
												  'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
											  ]);

						$inStocks = $inStocksQuery
							->orderBy('address_sort_order')
							->limit($item->expected_qty)
							->all();
						if ($inStocks) {
							foreach($inStocks as $stockLine) {
								// ORDER ITEM
								$item->allocated_qty += 1;
								$currentSkuIDToLines['res'] += 1;
								$currentSkuIDToLines['lines'][$item->id] += 1;
								$allocatedQty++;
								// STOCK
								$stockLine->outbound_order_id = $oo->id;
								$stockLine->outbound_order_item_id = $item->id;

								$stockLine->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
								$stockLine->status_availability = Stock::STATUS_AVAILABILITY_RESERVED;
								$stockLine->save(false);
							}
						}

						$item->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

						if( $item->allocated_qty == $item->expected_qty ) {
							$item->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
						}
						$item->save(false);
					}

					$linesCount = count($currentSkuIDToLines['lines']);
					$linesToDeleted = 0;
					foreach($currentSkuIDToLines['lines'] as $lineID=>$reservedByLine) {
						if(empty($reservedByLine) && $linesCount != 1 && $linesToDeleted != ($linesCount-1)) {
							$linesToDeleted += 1;
							OutboundOrderItem::deleteAll(['id'=>$lineID]);
						}
					}
				}

				$oo->expected_qty = $expectedQty;
				$oo->allocated_qty = $allocatedQty;
				$oo->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

				if( $oo->allocated_qty == $oo->expected_qty ) {
					$oo->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
				}
				$oo->save(false);
			}
			return true;
		}
	}

	/**
	 * @param integer $outboundId
	 * @return AddResponse $response
	 */
	public function sendStatusInCompleted($outboundId) {
		$order = $this->repository->getOrder($outboundId);
		//if ($order && $order->status == Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL) {
			$ss = new StockService();
			$items = $ss->getDataForOutboundAPI($order->id);
			$mapper = new OutboundAPIMapper();
			$dataForAPI = $mapper->makeFinishResponse($order,$items);
			return $this->apiService->sendStatusCompleted($dataForAPI);
		//}
		return new AddResponse();
	}
}