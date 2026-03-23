<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.10.2017
 * Time: 14:48
 */

namespace common\ecommerce\main\inbound\validation;


use common\modules\stock\models\Stock;

class BoxToBoxValidation
{
    public function isBoxNotEmpty($boxBarcode)
    {
        return Stock::find()
            ->andWhere(['primary_address'=>$boxBarcode])
            ->andWhere(['status_availability'=>Stock::STATUS_AVAILABILITY_YES])
            ->exists();
    }

    public function isProductExistInBox($productBarcode,$boxBarcode) {
        return Stock::find()
            ->andWhere([
                'product_barcode'=>$productBarcode,
                'primary_address'=>$boxBarcode,
                'status_availability'=>Stock::STATUS_AVAILABILITY_YES
            ])
            ->exists();
    }

    public function isBoxOnPlace($boxBarcode) {
        return Stock::find()
            ->andWhere([
                'primary_address'=>$boxBarcode,
            ])
            ->andWhere('secondary_address IS NOT NULL AND secondary_address != "" AND secondary_address != 0')
            ->exists();
    }
}