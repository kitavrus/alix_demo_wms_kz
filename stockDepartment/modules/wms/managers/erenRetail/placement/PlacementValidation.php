<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;


//use common\modules\warehouseAddress\service\RackAddressService;
use common\modules\stock\models\RackAddress;
use common\modules\stock\models\Stock;

class PlacementValidation
{
	public function isPlace($placeBarcode)
    {
        return RackAddress::find()->andWhere(['address'=>$placeBarcode])->exists();
    }

	public function isBox($boxBarcode) {
        return substr(trim($boxBarcode), 0, 4) == "5000";
    }

	public function isBoxNotEmpty($boxBarcode)
	{
		return Stock::find()
					->andWhere(['primary_address'=>$boxBarcode])
					->andWhere(['status_availability'=>[
						Stock::STATUS_AVAILABILITY_YES,
						Stock::STATUS_AVAILABILITY_NOT_SET,
					]])
					->exists();
	}

	public function isProductExistInBox($productBarcode,$boxBarcode) {
		return Stock::find()
					->andWhere([
						'product_barcode'=>$productBarcode,
						'primary_address'=>$boxBarcode,
						'status_availability'=> [
							Stock::STATUS_AVAILABILITY_YES,
							Stock::STATUS_AVAILABILITY_NOT_SET,
						],
						'status'=>[
							Stock::STATUS_INBOUND_NEW,
							Stock::STATUS_INBOUND_SCANNED,
							Stock::STATUS_INBOUND_OVER_SCANNED,
							Stock::STATUS_INBOUND_CONFIRM,
							Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API,
							Stock::STATUS_INBOUND_PLACED,
						]
					])
					->exists();
	}

	public function isBoxOnPlace($boxBarcode) {
		return Stock::find()
					->andWhere([
						'primary_address'=>$boxBarcode,
					])
					->andWhere('secondary_address IS NOT NULL AND secondary_address != "" AND secondary_address != "0"')
					->exists();
	}

	public function getPlaceByBox($boxBarcode) {
		return Stock::find()->select("secondary_address")
					->andWhere([
						'primary_address'=>$boxBarcode,
					])
					->andWhere('secondary_address IS NOT NULL AND secondary_address != "" AND secondary_address != "0"')
					->one();
	}

	public function getNotEmptyBoxIdsQuery($boxBarcode)
	{
		return Stock::find()->select('id')
					->andWhere([
						'primary_address'=>$boxBarcode,
						'status_availability'=>[
							Stock::STATUS_AVAILABILITY_NOT_SET,
							Stock::STATUS_AVAILABILITY_YES,
//					Stock::STATUS_AVAILABILITY_NO
						],
						'status'=>[
							Stock::STATUS_INBOUND_NEW,
							Stock::STATUS_INBOUND_SCANNED,
							Stock::STATUS_INBOUND_OVER_SCANNED,
							Stock::STATUS_INBOUND_CONFIRM,
							Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API,
							Stock::STATUS_INBOUND_PLACED,
						]]);
	}
}