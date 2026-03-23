<?php

namespace common\modules\placementUnit\repository;

use common\modules\placementUnit\models\OutboundUnitAddress;
use common\overloads\ArrayHelper;
use Yii;
/**
 *
 */
class OutboundUnitAddressRepository
{
    public function create($dto) {
        $outboundUnitAddress = new OutboundUnitAddress();
        $outboundUnitAddress->client_id = ArrayHelper::getValue($dto,'clientID',0);
        $outboundUnitAddress->warehouse_id = ArrayHelper::getValue($dto,'warehouseId',0);
        $outboundUnitAddress->zone_id = ArrayHelper::getValue($dto,'zoneID',0);
        $outboundUnitAddress->outbound_order_id = ArrayHelper::getValue($dto,'outboundOrderID',0);
        $outboundUnitAddress->code_book_id =  ArrayHelper::getValue($dto,'codeBookID',0);
        $outboundUnitAddress->from_rack_address = ArrayHelper::getValue($dto,'fromRackAddress',0);
        $outboundUnitAddress->from_pallet_address = ArrayHelper::getValue($dto,'fromPalletAddress',0);
        $outboundUnitAddress->from_box_address = ArrayHelper::getValue($dto,'fromBoxAddress',0);
        $outboundUnitAddress->our_barcode = ArrayHelper::getValue($dto,'ourBarcode',0);
        $outboundUnitAddress->client_barcode = ArrayHelper::getValue($dto,'clientBarcode',0);
        $outboundUnitAddress->status = ArrayHelper::getValue($dto,'status',0);
        $outboundUnitAddress->save(false);
    }

    public function isExist() {

    }
}