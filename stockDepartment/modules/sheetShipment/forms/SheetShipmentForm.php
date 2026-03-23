<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.06.2017
 * Time: 16:08
 */

namespace stockDepartment\modules\sheetShipment\forms;

use stockDepartment\modules\sheetShipment\dto\SheepShipmentDTO;
use stockDepartment\modules\sheetShipment\validators\SheetShipmentFormValidate;
use yii\base\Model;
use yii;

class SheetShipmentForm  extends Model {

    public $placeAddress; // place address for any outbound orders
    public $boxBarcode; // any outbound box barcode. Outbound order or Cross-dock

    public function attributeLabels()
    {
        return [
            'boxBarcode' => Yii::t('sheet-shipment/forms', 'BOX-BARCODE'),
            'placeAddress' => Yii::t('sheet-shipment/forms', 'PLACE-ADDRESS'),
        ];
    }

    public function rules()
    {
        return [
            [['boxBarcode'], 'validateBoxBarcode'],
            [['placeAddress','boxBarcode'], 'string'],
            [['placeAddress','boxBarcode'], 'trim'],
            [['placeAddress'], 'validatePlaceAddress'],
        ];
    }

    public function validatePlaceAddress($attribute, $params)
    {
        $attribute = $this->$attribute;
        if(!SheetShipmentFormValidate::isPlaceExist($attribute)) {
            $this->addError($attribute, "Это не адрес для отгрузки");
        }
    }

    public function validateBoxBarcode($attribute, $params)
    {
        $attribute = $this->$attribute;
        if(!SheetShipmentFormValidate::isBoxBarcodeExist($attribute)) {
            $this->addError($attribute, "Это не адрес для отгрузки");
        }
    }

    public function getDto()
    {
        $dto = new SheepShipmentDTO();
        $dto->loadFromForm($this);
        return $dto;
    }
}