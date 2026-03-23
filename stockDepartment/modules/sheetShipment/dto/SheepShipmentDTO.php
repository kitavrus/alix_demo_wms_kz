<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 15.06.2017
 * Time: 16:14
 */

namespace stockDepartment\modules\sheetShipment\dto;


class SheepShipmentDTO
{
    public $placeAddress; // place address for any outbound orders
    public $boxBarcode; // any outbound box barcode. Outbound order or Cross-dock

    public function loadFromForm($form)
    {
        // add validate attribute exist on form
        $form = $this->prepared($form);

        $this->placeAddress = $form->placeAddress;
        $this->boxBarcode = $form->boxBarcode;
    }

    public function prepared($form)
    {
        $form->placeAddress = trim($form->placeAddress, '#');
        $form->boxBarcode = trim($form->boxBarcode, '#');
        return $form;
    }

    public function asArray()
    {
        return [
            'place_address'=> $this->placeAddress,
            'box_barcode'=> $this->boxBarcode
        ];
    }
}