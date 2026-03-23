<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.06.2017
 * Time: 16:08
 */

namespace stockDepartment\modules\sheetShipment\repositories;


use stockDepartment\modules\sheetShipment\models\SheepShipmentPlaceAddressAR;

class PlaceAddressRepository
{
    public static function isPlaceExist($place)
    {
        return SheepShipmentPlaceAddressAR::find()->andWhere(['place_barcode'=>$place])->exists();
    }
}