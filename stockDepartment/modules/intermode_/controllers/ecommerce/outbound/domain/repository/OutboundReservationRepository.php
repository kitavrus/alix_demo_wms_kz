<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:14
 */

namespace app\modules\intermode\controllers\ecommerce\outbound\domain\repository;


use app\modules\intermode\controllers\ecommerce\stock\domain\constants\StockAvailability;
use app\modules\intermode\controllers\ecommerce\stock\domain\constants\StockConditionType;
use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundPlaceAddressSorting;
use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
//use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\StockOutboundStatus;
use app\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
//use app\modules\intermode\controllers\ecommerce\stock\domain\entities\EcommerceStock;
use app\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItem;
use common\modules\stock\models\Stock;
use stockDepartment\modules\intermode\controllers\product\domains\ProductService;
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
		Stock::updateAll([
			'address_sort_order'=> self::makePlaceAddressSort1($PlaceBarcode)
		],[
			'box_address_barcode'=>$BoxBarcode,
			'place_address_barcode'=>$PlaceBarcode
		]);
	}
	public function getStocksByProductBarcode($clientId,$productBarcode,$expectedQty)
	{
		return Stock::find()
								->andWhere([
									'client_id' => $clientId,
//									'product_barcode' => $productBarcode,
									'product_id' => (new ProductService())->getProductIdByBarcode($productBarcode),
									'status_availability' => Stock::STATUS_AVAILABILITY_YES,
									'condition_type' => [Stock::CONDITION_TYPE_NOT_SET,Stock::CONDITION_TYPE_UNDAMAGED],
								])
								->orderBy('address_sort_order')
								->limit($expectedQty)
								->all();
	}

	public function resetByOutboundOrderId($outbound_order_id)
	{
		if ($outboundOrder = EcommerceOutbound::findOne($outbound_order_id)) {
			EcommerceOutbound::updateAll(['accepted_qty' => '0', 'allocated_qty' => '0', 'status' => OutboundStatus::getNEW()], ['id' => $outboundOrder->id]);
			EcommerceOutboundItem::updateAll(['accepted_qty' => '0','allocated_qty' => '0', 'status' => OutboundStatus::getNEW()], ['outbound_id' => $outboundOrder->id]);
			Stock::updateAll([
				'outbound_box' => '',
				'ecom_outbound_id' => 0,
				'ecom_outbound_items_id' => 0,
				'scan_out_datetime' => 0,
				'scan_out_employee_id' => 0,
				'status' => Stock::STATUS_INBOUND_CONFIRM,
				'status_availability' => Stock::STATUS_AVAILABILITY_YES
			], ['ecom_outbound_id' => $outboundOrder->id]);
		}
	}
}