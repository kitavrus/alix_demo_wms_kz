<?php

namespace common\modules\placementUnit\repository;

use common\modules\placementUnit\models\InboundUnitAddress;
use common\overloads\ArrayHelper;
use Yii;
/**
*
 */
class InboundUnitAddressRepository
{
    public function create($dto) {
        $inboundUnitAddress = new InboundUnitAddress();
        $inboundUnitAddress->client_id = ArrayHelper::getValue($dto,'clientID',0);
        $inboundUnitAddress->warehouse_id = ArrayHelper::getValue($dto,'warehouseId',0);
        $inboundUnitAddress->zone_id = ArrayHelper::getValue($dto,'zoneID',0);
        $inboundUnitAddress->inbound_order_id = ArrayHelper::getValue($dto,'inboundOrderID',0);
        $inboundUnitAddress->code_book_id =  ArrayHelper::getValue($dto,'codeBookID',0);

        $inboundUnitAddress->to_rack_address = ArrayHelper::getValue($dto,'toRackAddress',0);
        $inboundUnitAddress->to_pallet_address = ArrayHelper::getValue($dto,'toPalletAddress',0);
        $inboundUnitAddress->to_box_address = ArrayHelper::getValue($dto,'toBoxAddress',0);

        $inboundUnitAddress->transfer_rack_address = ArrayHelper::getValue($dto,'transferRackAddress',0);
        $inboundUnitAddress->transfer_pallet_address = ArrayHelper::getValue($dto,'transferPalletAddress',0);
        $inboundUnitAddress->transfer_box_address = ArrayHelper::getValue($dto,'transferBoxAddress',0);

        $inboundUnitAddress->our_barcode = ArrayHelper::getValue($dto,'ourBarcode',0);
        $inboundUnitAddress->client_barcode = ArrayHelper::getValue($dto,'clientBarcode',0);
        $inboundUnitAddress->status = ArrayHelper::getValue($dto,'status',0);
        $inboundUnitAddress->save(false);
    }

    public function isExist($barcode) {
        return InboundUnitAddress::find()->andWhere(['our_barcode'=>$barcode])->exists();
    }
}