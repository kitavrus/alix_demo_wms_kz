<?php

namespace clientDepartment\modules\report\services;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\entities\EcommerceStock;
use common\modules\client\models\Client;
use common\modules\stock\models\Stock;

class QtyBoxDefactoService
{
	private $clientId = Client::CLIENT_DEFACTO;

	public function  getB2BBoxCount() {
		$query = Stock::find()->select('primary_address');
		$query->andWhere([
			'client_id' => $this->clientId,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		]);
		$query->andWhere("(`secondary_address` LIKE '1-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '3-%')");
		$query->groupBy('primary_address');
		return $query->count();
	}

	public function getB2BLotCount() {
		$query = Stock::find();
		$query->andWhere([
			'client_id' => $this->clientId,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		]);
		$query->andWhere("(`secondary_address` LIKE '1-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '3-%')");
		return $query->count();
	}


	public function  getB2CBoxCount() {
		$query = EcommerceStock::find()->select('box_address_barcode');
		$query->andWhere([
			'client_id' => $this->clientId,
			'status_availability' => StockAvailability::YES,
		]);
		$query->andWhere("(`place_address_barcode` LIKE '3-%')");
		$query->groupBy('box_address_barcode');
		return $query->count();
	}

	public function  getB2CProductCount() {
		$query = EcommerceStock::find();
		$query->andWhere([
			'client_id' => $this->clientId,
			'status_availability' => StockAvailability::YES,
		]);
		$query->andWhere("(`place_address_barcode` LIKE '3-%')");
		return $query->count();
	}

	public function  getReturnLotBoxCount() {
		$query = Stock::find()->select('primary_address');
		$query->andWhere([
			'client_id' => $this->clientId,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		]);
		$query->andWhere("(`secondary_address` LIKE '4-%')");
		$query->groupBy('primary_address');
		return  $query->count();
	}

	public function  getReturnLotCount() {
		$query = Stock::find();
		$query->andWhere([
			'client_id' => $this->clientId,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		]);
		$query->andWhere("(`secondary_address` LIKE '4-%')");
		return   $query->count();
	}

	public function  getReturnPalletBoxCount() {
		$query = Stock::find()->select('primary_address');
		$query->andWhere([
			'client_id' => $this->clientId,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		]);
		$query->andWhere("(`secondary_address` LIKE '5-%' OR `secondary_address` LIKE '6-%' OR `secondary_address` LIKE '7-%'OR `secondary_address` LIKE '8-%')");
		$query->groupBy('primary_address');
		return $query->count();
	}

	public function  getReturnPalletCount() {
		$query = Stock::find();
		$query->andWhere([
			'client_id' => $this->clientId,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		]);
		$query->andWhere("(`secondary_address` LIKE '5-%' OR `secondary_address` LIKE '6-%' OR `secondary_address` LIKE '7-%'OR `secondary_address` LIKE '8-%')");
		return   $query->count();
	}


}