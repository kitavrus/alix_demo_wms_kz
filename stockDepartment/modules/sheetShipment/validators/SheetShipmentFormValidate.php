<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 15.06.2017
 * Time: 15:26
 */

namespace stockDepartment\modules\sheetShipment\validators;


use stockDepartment\modules\sheetShipment\repositories\SheetShipmentRepository;

class SheetShipmentFormValidate
{
    public static function isPlaceExist($place) {
        return SheetShipmentRepository::isPlaceExist($place);
    }
    
    public static function isBoxBarcodeExist($boxBarcode) {
        return SheetShipmentRepository::isBoxBarcodeExist($boxBarcode);
    }
}