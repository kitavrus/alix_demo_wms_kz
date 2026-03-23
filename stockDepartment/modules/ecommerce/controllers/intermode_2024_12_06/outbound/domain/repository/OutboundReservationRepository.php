<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:14
 */

namespace app\modules\ecommerce\controllers\intermode\outbound\domain\repository;


use app\modules\ecommerce\controllers\intermode\stock\domain\constants\StockAvailability;
use app\modules\ecommerce\controllers\intermode\stock\domain\constants\StockConditionType;
use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\OutboundPlaceAddressSorting;
use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\OutboundStatus;
use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\StockOutboundStatus;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutbound;
use app\modules\ecommerce\controllers\intermode\stock\domain\entities\EcommerceStock;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutboundItem;
use yii\helpers\ArrayHelper;

class OutboundReservationRepository
{
    public function getClientID()
    {
        return 103;
    }
	public function beforeReservationSorting($orderIdList)
	{
		$ids = EcommerceOutbound::find()->select('id')->andWhere(['id' => $orderIdList])->orderBy('expected_qty')->column();
		return $ids;
	}



	public static function makePlaceAddressSort1($addressBarcode) {
		$addressBarcodeList = explode('-',$addressBarcode);
		if(empty($addressBarcodeList) || !is_array($addressBarcodeList) || !isset($addressBarcodeList[1])) {
			return OutboundPlaceAddressSorting::INCORRECT_PLACE_ADDRESS_SORT1;
		}
		$addressBarcodeResult = $addressBarcodeList[1].$addressBarcodeList[2];
		return $addressBarcodeResult;
	}

	public function changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode) {
		EcommerceStock::updateAll([
			'place_address_sort1'=> self::makePlaceAddressSort1($PlaceBarcode)
		],[
			'box_address_barcode'=>$BoxBarcode,
			'place_address_barcode'=>$PlaceBarcode
		]);
	}
	public function getStocksByProductBarcode($clientId,$productBarcode,$expectedQty)
	{
		return EcommerceStock::find()
								->andWhere([
									'client_id' => $clientId,
									'product_barcode' => $productBarcode,
									'status_availability' => StockAvailability::YES,
									'condition_type' => StockConditionType::UNDAMAGED,
								])
								->orderBy('place_address_sort1')
								->limit($expectedQty)
								->all();
	}

	public function resetByOutboundOrderId($outbound_order_id)
	{
		if ($outboundOrder = EcommerceOutbound::findOne($outbound_order_id)) {
			EcommerceOutbound::updateAll(['accepted_qty' => '0', 'allocated_qty' => '0', 'status' => OutboundStatus::_NEW], ['id' => $outboundOrder->id]);
			EcommerceOutboundItem::updateAll(['accepted_qty' => '0','allocated_qty' => '0', 'status' => OutboundStatus::_NEW], ['outbound_id' => $outboundOrder->id]);
			EcommerceStock::updateAll([
				'outbound_box' => '',
				'outbound_id' => 0,
				'outbound_item_id' => 0,
				'scan_out_datetime' => 0,
				'scan_out_employee_id' => 0,
				'status_outbound' => StockOutboundStatus::NOT_SET,
				'status_availability' => StockAvailability::YES
			], ['outbound_id' => $outboundOrder->id]);
		}
	}
}