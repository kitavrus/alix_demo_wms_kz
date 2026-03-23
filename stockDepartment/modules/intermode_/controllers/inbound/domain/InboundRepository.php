<?php

namespace app\modules\intermode\controllers\inbound\domain;

use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInbound;
use common\modules\inbound\models\InboundOrder;
use app\modules\intermode\controllers\inbound\domain\InboundDataMatrix;
use common\modules\inbound\models\InboundOrderItem;


class InboundRepository
{
	/**
	 *
	 */
	public function getClientID()
	{
		return 103;
	}

	//
	public function isExtraBarcodeInOrder($productBarcode, $inboundOrderID)
	{
		return InboundOrderItem::find()->andWhere([
			'inbound_order_id' => $inboundOrderID,
			'product_barcode' => $productBarcode,
		])->andWhere('expected_qty = accepted_qty AND expected_qty != 0')->exists();
	}
	/**
	 *
	 */
	public function isNotAvailableDataMatrix($inboundId, $productBarcode, $dataMatrix)
	{
		return !InboundDataMatrix::find()
										  ->andWhere([
											  "inbound_id" => $inboundId,
											  "product_barcode" => $productBarcode,
											  "data_matrix_code" => $dataMatrix,
											  "status" => InboundDataMatrix::NOT_SCANNED,
										  ])
										  ->exists();
	}

	/**
	 *
	 */
	public function getAvailableDataMatrix($inboundId, $productBarcode, $dataMatrix)
	{
		return InboundDataMatrix::find()
										 ->andWhere([
											 "inbound_id" => $inboundId,
											 "product_barcode" => $productBarcode,
											 "data_matrix_code" => $dataMatrix,
											 "status" => InboundDataMatrix::NOT_SCANNED,
										 ])
										 ->one();
	}

	/**
	 *
	 */
	public function setToNotScannedDataMatrix($dataMatrixIds)
	{
		return InboundDataMatrix::updateAll([
			"status" => InboundDataMatrix::NOT_SCANNED,
		],[
			"id"=>$dataMatrixIds
		]);
	}
	/**
	 * @return array|InboundOrder|\yii\db\ActiveRecord
	 */
	public function getOrder($id)
	{
		return InboundOrder::find()->andWhere([
			"id" => $id,
			"client_id" => $this->getClientID(),
		])->one();
	}
}