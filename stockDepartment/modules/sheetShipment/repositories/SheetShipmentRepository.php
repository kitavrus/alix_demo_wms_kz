<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.06.2017
 * Time: 16:08
 */

namespace stockDepartment\modules\sheetShipment\repositories;


use stockDepartment\modules\sheetShipment\entities\SheetShipmentId;
use stockDepartment\modules\sheetShipment\models\SheetShipmentAR;

class SheetShipmentRepository
{
    private $id;

    public static function isBoxBarcodeExist($boxBarcode)
    {
        return SheetShipmentAR::isBoxBarcodeExist($boxBarcode);
    }

    public static function isPlaceExist($place)
    {
        return PlaceAddressRepository::isPlaceExist($place);
    }

    public function create($dto)
    {
        $id = SheetShipmentActiveRecord::create($dto);
        if($id !== false) {
            $this->setId($id);
            return true;
        }
        return false;
    }

    private function setId($id)
    {
        $this->id = new SheetShipmentId($id);
        return $this->$id;
    }

    public function getId()
    {
        return $this->id;
    }
}