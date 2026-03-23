<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 15.06.2017
 * Time: 15:02
 */

namespace stockDepartment\modules\sheetShipment\service;


use stockDepartment\modules\sheetShipment\repositories\SheetShipmentRepository;

class SheetShipmentService
{
    public static function create($dto)
    {
       $repository = new SheetShipmentRepository();
       return $repository->save($dto->asArray());
    }


    public function getErrors(){}
}