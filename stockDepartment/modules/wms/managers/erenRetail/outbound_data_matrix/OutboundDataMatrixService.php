<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:02
 */

namespace stockDepartment\modules\wms\managers\erenRetail\outbound_data_matrix;

use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;

class OutboundDataMatrixService
{
	private $client_id = 103;
    /**
     * OutboundDataMatrixService constructor.
     */
    public function __construct() {
    }

    public function getOrderInfoByBoxBarcode($boxBarcode) {
    	$stock = Stock::find()
					->andWhere([
						"client_id"=>$this->client_id,
						"box_barcode"=>$boxBarcode,
					])
					->one();

		$order = OutboundOrder::find()
					  ->andWhere([
						  "id"=>$stock->outbound_order_id,
					  ])
					  ->one();


		$count = Stock::find()
					  ->andWhere([
						  "client_id"=>$this->client_id,
						  "box_barcode"=>$boxBarcode,
					  ])
					  ->count();

		$scanCount = Stock::find()
					  ->andWhere([
						  "client_id"=>$this->client_id,
						  "outbound_order_id"=>$stock->outbound_order_id,
					  ])
			->andWhere("product_qrcode IS NOT NULL or product_qrcode != :product_qrcode ",[":product_qrcode"=>""])
					  ->count();

		  $expCount= Stock::find()
					  ->andWhere([
						  "client_id"=>$this->client_id,
						  "outbound_order_id"=>$stock->outbound_order_id,
					  ])

			->andWhere("product_qrcode IS NULL or product_qrcode = :product_qrcode ",[":product_qrcode"=>""])
					  ->count();

		$scanCountBox = Stock::find()
						  ->andWhere([
							  "client_id"=>$this->client_id,
							  "outbound_order_id"=>$stock->outbound_order_id,
							  "box_barcode"=>$boxBarcode,
						  ])
						  ->andWhere("product_qrcode IS NOT NULL or product_qrcode != :product_qrcode ",[":product_qrcode"=>""])
						  ->count();

		$expCountBox = Stock::find()
						->andWhere([
							"client_id"=>$this->client_id,
							"outbound_order_id"=>$stock->outbound_order_id,
							"box_barcode"=>$boxBarcode,
						])

						->andWhere("product_qrcode IS NULL or product_qrcode = :product_qrcode ",[":product_qrcode"=>""])
						->count();


    	$out = new \stdClass();
		$out->order_number = $order->order_number;
		$out->countProductInBox = $count;
		$out->expCount = $expCount;
		$out->scanCount = $scanCount;

		$out->expCountBox = $expCountBox;
		$out->scanCountBox = $scanCountBox;

		return $out;
	}


	/*
	* Check exist product in box
	* @param string $productBarcode
	* @return boolean
	* */
	public function checkProductInBox($box_barcode,$productBarcode)
	{
		return Stock::find()
					->andWhere([
						"client_id"=>$this->client_id,
						'box_barcode'=>$box_barcode,
						'product_barcode'=>$productBarcode,
						'status'=>[
							Stock::STATUS_OUTBOUND_SCANNED,
							Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
							],
					])
					->andWhere("product_qrcode IS NULL or product_qrcode = :product_qrcode ",[":product_qrcode"=>""])
					->exists();
	}

	/*
	* */
	public function setProductDataMatrix($boxBarcode,$productBarcode,$productDataMatrix)
	{
		$stock = Stock::find()
			 ->andWhere([
				 "client_id"=>$this->client_id,
				 'box_barcode'=>$boxBarcode,
				 'product_barcode'=>$productBarcode,
						'status'=>[
							Stock::STATUS_OUTBOUND_SCANNED,
							Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
							],
			 ])
			 ->andWhere("product_qrcode IS NULL or product_qrcode = :product_qrcode ",[":product_qrcode"=>""])
			 ->one();

		Stock::getDb()->transaction(function($db) use ($stock,$productDataMatrix) {
			$stock->product_qrcode = $productDataMatrix;
			$stock->save(false);
		});
	}

	/*
	* */
	public function clearBox($boxBarcode)
	{
		Stock::updateAll([
			'product_qrcode' => null
		],
			[
				"client_id"=>$this->client_id,
				'box_barcode'=>$boxBarcode,
				'status'=>[
						Stock::STATUS_OUTBOUND_SCANNED,
						Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
					]
			]
		);
	}
}