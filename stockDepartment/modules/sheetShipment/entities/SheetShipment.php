<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.06.2017
 * Time: 16:20
 */

namespace stockDepartment\modules\sheetShipment\entites;


class SheetShipment
{
    private $placeAddress; // place address for any outbound orders
    private $boxBarcode;   // any outbound box barcode. Outbound order or Cross-dock
    private $status;   // status if outbound order shipped
}