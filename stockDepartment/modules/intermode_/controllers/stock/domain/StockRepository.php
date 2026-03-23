<?php

namespace app\modules\intermode\controllers\stock\domain;

use common\modules\stock\models\Stock;

class StockRepository
{
    public function getClientID()
    {
        return 103;
    }
	//
	public function setInboundDataMatrixId($stockId,$datamatrixId,$datamatrix)
	{
		return Stock::updateAll([
			'inbound_datamatrix_id'=>$datamatrixId,
			'inbound_datamatrix_code'=>$datamatrix,
		],[
			'id'=>$stockId,
		]);
	}

	//
	public function checkExistDataMatrixByStockId($stockId,$datamatrixId,$datamatrix)
	{
//		$stock = EcommerceStock::find()->andWhere(['id'=>$stockId])->one();
		return Stock::find()->andWhere(['inbound_datamatrix_code'=>$datamatrix])->exists();
	}

	public function getDataForInboundAPI($inboundOrderId)
	{
		return  Stock::find()
							  ->select("product_barcode, product_model, product_sku, GROUP_CONCAT(inbound_datamatrix_code ORDER BY inbound_datamatrix_code SEPARATOR '|') as qrcode, count(product_barcode) as productQty")
							  ->andWhere([
								  'inbound_order_id'=>$inboundOrderId,
//				'status_inbound'=>StockInboundStatus::DONE,
//				'status_availability'=>StockAvailability::YES,
							  ])
							  ->groupBy("product_barcode")
							  ->asArray()
							  ->all();
	}
	
	public function getDataForInboundAPIV2($inboundOrderId)
	{
		return  Stock::find()
					 ->select(" product_sku, count(product_sku) as productQty")
					 ->andWhere([
						 'inbound_order_id'=>$inboundOrderId,
					 ])
					 ->groupBy("product_sku")
					 ->asArray()
					 ->all();
	}

	public function getDataForInboundReturnAPI($inboundOrderId)
	{
		return  Stock::find()
							  ->select("
							  product_sku, 
							  count(product_barcode) as productQty")
							  ->andWhere([
								  'inbound_order_id'=>$inboundOrderId,
							  ])
							  ->groupBy("product_sku")
							  ->asArray()
							  ->all();
	}

	public function getDataForOutboundAPI($outboundOrderId)
	{
		return  Stock::find()
							  ->select("
							  box_barcode, 
							  product_sku, 
							  count(product_barcode) as productQty
							  ")
							  ->andWhere([
								  'outbound_order_id'=>$outboundOrderId,
							  ])
							  ->groupBy("product_sku,box_barcode")
							  ->asArray()
							  ->all();
	}
}