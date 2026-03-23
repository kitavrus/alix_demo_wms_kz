<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */

namespace common\modules\stock\repository;


use common\modules\client\models\Client;
use common\modules\stock\models\Stock;
use common\overloads\ArrayHelper;

class Repository
{
    private $id;

    //
    public function isExistEmptyM3($outboundOrderID) {
        return Stock::find()
            ->andWhere(["box_size_m3"=>0,'outbound_order_id'=>$outboundOrderID])
            ->orWhere(['box_size_barcode'=>null,'outbound_order_id'=>$outboundOrderID])->exists();
    }
    //
    public function isExistEmptyKg($outboundOrderID) {
        return Stock::find()->andWhere("box_kg = ''")->andWhere(['outbound_order_id'=>$outboundOrderID])->exists();

    }

    public function IsNotEmptyPrimaryAddress($primaryAddress)
    { // TODO Сделать это одним запросом

        $qtyNOAvailable =  Stock::find()->andWhere([
            'primary_address'=>$primaryAddress,
            'status_availability'=>[
                $this->getStatusAvailabilityNO()
            ],
        ])->count();

        $qtyYesAvailable =  Stock::find()->andWhere([
            'primary_address'=>$primaryAddress,
            'status_availability'=>[
                $this->getStatusAvailabilityYES()
            ],
        ])->count();

        return $qtyNOAvailable != $qtyYesAvailable && $qtyYesAvailable > 0;
    }
    //
    public function create($dto) {
        $stock = new Stock();
        $stock->scan_in_employee_id = ArrayHelper::getValue($dto,'scanInEmployeeId');
        $stock->scan_out_employee_id = ArrayHelper::getValue($dto,'scanOutEmployeeId');
        $stock->client_id = ArrayHelper::getValue($dto,'clientId');
        $stock->inbound_order_id = ArrayHelper::getValue($dto,'inboundOrderId');
        $stock->consignment_inbound_id = ArrayHelper::getValue($dto,'consignmentInboundId');
        $stock->inbound_order_item_id = ArrayHelper::getValue($dto,'inboundOrderItemId');
        $stock->inbound_order_number = ArrayHelper::getValue($dto,'inboundOrderNumber');
        $stock->outbound_order_id = ArrayHelper::getValue($dto,'outboundOrderId');
        $stock->consignment_outbound_id = ArrayHelper::getValue($dto,'consignmentOutboundId');
        $stock->outbound_order_item_id = ArrayHelper::getValue($dto,'outboundOrderItemId');
        $stock->outbound_picking_list_id = ArrayHelper::getValue($dto,'outboundPickingListId');
        $stock->outbound_picking_list_barcode = ArrayHelper::getValue($dto,'outboundPickingListBarcode');
        $stock->outbound_order_number = ArrayHelper::getValue($dto,'outboundOrderNumber');
        $stock->warehouse_id = ArrayHelper::getValue($dto,'warehouseId');
        $stock->zone = ArrayHelper::getValue($dto,'zoneId');
        $stock->product_id = ArrayHelper::getValue($dto,'productId');
        $stock->product_name = ArrayHelper::getValue($dto,'productName');
        $stock->product_barcode = ArrayHelper::getValue($dto,'productBarcode');
        $stock->product_model = ArrayHelper::getValue($dto,'productModel');
        $stock->product_sku = ArrayHelper::getValue($dto,'productSku');
        $stock->box_barcode = ArrayHelper::getValue($dto,'boxBarcode');
        $stock->box_size_barcode = ArrayHelper::getValue($dto,'boxSizeBarcode');
        $stock->box_size_m3 = ArrayHelper::getValue($dto,'boxSizeM3');
        $stock->box_kg = ArrayHelper::getValue($dto,'boxKg');
        $stock->condition_type = ArrayHelper::getValue($dto,'conditionType');
        $stock->status = ArrayHelper::getValue($dto,'status');
        $stock->status_availability = ArrayHelper::getValue($dto,'statusAvailability');
        $stock->status_lost = ArrayHelper::getValue($dto,'statusLost');
        $stock->inventory_id = ArrayHelper::getValue($dto,'inventoryId');
        $stock->inventory_primary_address = ArrayHelper::getValue($dto,'inventoryPrimaryAddress');
        $stock->inventory_secondary_address = ArrayHelper::getValue($dto,'inventorySecondaryAddress');
        $stock->status_inventory = ArrayHelper::getValue($dto,'statusInventory');
        $stock->primary_address = ArrayHelper::getValue($dto,'primaryAddress');
        $stock->secondary_address = ArrayHelper::getValue($dto,'secondaryAddress');
        $stock->address_sort_order = ArrayHelper::getValue($dto,'addressSortOrder');
        $stock->kpi_value = ArrayHelper::getValue($dto,'kpiValue');
        $stock->scan_out_datetime = ArrayHelper::getValue($dto,'scanOutDatetime');
        $stock->scan_in_datetime = ArrayHelper::getValue($dto,'scanInDatetime');
        $stock->inbound_client_box = ArrayHelper::getValue($dto,'inboundClientBox');
        $stock->system_status = ArrayHelper::getValue($dto,'systemStatus');
        $stock->system_status_description = ArrayHelper::getValue($dto,'systemStatusDescription');
        $stock->field_extra1 = ArrayHelper::getValue($dto,'fieldExtra1');
        $stock->field_extra2 = ArrayHelper::getValue($dto,'fieldExtra2');
        $stock->field_extra3 = ArrayHelper::getValue($dto,'fieldExtra3');
        $stock->field_extra4 = ArrayHelper::getValue($dto,'fieldExtra4');
        $stock->field_extra5 = ArrayHelper::getValue($dto,'fieldExtra5');
		$stock->stock_adjustment_id = @ArrayHelper::getValue($dto,'stockAdjustmentId');
        $stock->stock_adjustment_status = @ArrayHelper::getValue($dto,'stockAdjustmentStatus');
        $stock->save(false);

        $this->setId($stock->id);

        return $stock;
    }
    //
    public function getScannedQtyByOrderInStock($inboundOrderId) {
        return Stock::find()->andWhere([
            'inbound_order_id'=>$inboundOrderId,
//            'status'=>Stock::STATUS_INBOUND_SCANNED,
        ])->count();
    }
    //
    public function removeByIDs($stockIDs) {
        Stock::deleteAll(['id'=>$stockIDs]);
    }
    //
    public function getStatusInboundScanned()
    {
        return Stock::STATUS_INBOUND_SCANNED;
    }

      //
    public function getStatusOutboundNew()
    {
        return Stock::STATUS_OUTBOUND_NEW;
    }

    //
    public function getStatusAvailabilityNO() {
        return Stock::STATUS_AVAILABILITY_NO;
    }

    //
    public function getStatusAvailabilityYES() {
        return Stock::STATUS_AVAILABILITY_YES;
    }

    public function setStatusNewAndAvailableYes($inboundOrderId)
    {
        Stock::updateAll([
            'status'=>Stock::STATUS_INBOUND_CONFIRM,
            'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
        ],[
            'inbound_order_id'=>$inboundOrderId,
            'status_availability'=>Stock::STATUS_AVAILABILITY_NO,
            'status'=>[
                Stock::STATUS_INBOUND_SCANNED,
                Stock::STATUS_INBOUND_OVER_SCANNED,
            ]
        ]);
    }
    //
    public function setPrimaryAddressForIds($stockIds,$primaryAddress)
    {
        file_put_contents('setPrimaryAddressForIds.log',$primaryAddress.';'.implode(',',$stockIds).';'."\n",FILE_APPEND);
        Stock::updateAll([
            'primary_address'=>$primaryAddress
        ],[
            'id'=>$stockIds,
        ]);
    }
    //
    public function setSecondaryAddressForIds($stockIds,$secondaryAddress)
    {
        file_put_contents('setSecondaryAddressForIds.log',$secondaryAddress.';'.implode(',',$stockIds).';'."\n",FILE_APPEND);
        Stock::updateAll([
            'secondary_address'=>$secondaryAddress
        ],[
            'id'=>$stockIds,
        ]);
    }

    //
    public function getIdsByPrimaryAddress($primaryAddress)
    {
        return Stock::find()->select('id')->andWhere([
            'primary_address'=>$primaryAddress,
        ])->column();
    }

    //
    public function setSecondaryAddressByPrimaryAddress($primaryAddress,$secondaryAddress)
    {
        Stock::updateAll([
            'secondary_address'=>$secondaryAddress
        ],[
            'primary_address'=>$primaryAddress,
        ]);
    }

    public function deleteByInboundId($inboundOrderId)
    {
        Stock::deleteAll(['inbound_order_id'=>$inboundOrderId]);
    }

    public function changeConditionType($stockId,$conditionType) {
        if($stock = Stock::findOne($stockId)) {
            $stock->condition_type = $conditionType;
            $stock->system_status = "restored";
            $stock->system_status_description = "Восстановлен из поврежденного";
            $stock->save(false);
        }
    }

	
    public function inboundPutAway($aInboundId) {
        return $stock = Stock::find()
            ->select('SQL_CALC_FOUND_ROWS `secondary_address`, `primary_address`, `product_barcode`, COUNT(`product_barcode`) as qty')
            ->andWhere(['inbound_order_id'=>$aInboundId])
            ->groupBy('`secondary_address`, `primary_address`, `product_barcode`')
            ->asArray()
            ->all();

    }
	

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
	
	    public function getRemainsForSendInventorySnapshotDefacto() {
        return Stock::find()
            ->select('product_barcode, primary_address, secondary_address, field_extra1 as client_product_sku, count(product_barcode) as productQty')
            ->andWhere([
                'client_id' =>Client::CLIENT_DEFACTO,
                'status_availability' =>$this->getStatusAvailabilityYES(),
            ])
            ->groupBy('product_barcode, primary_address, secondary_address')
            ->orderBy('address_sort_order, primary_address')
            ->asArray();
    }
}