<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\stock\models;

use common\modules\stock\models\RackAddress;
use Yii;
use yii\base\Model;

class GenerateAddressForm extends Model {

    public $stageMin;
    public $rowMin;
    public $rackMin;
    public $levelMin;
    public $stageMax;
    public $rowMax;
    public $rackMax;
    public $levelMax;

    public function attributeLabels()
    {
        return [
            'stageMin' => Yii::t('stock/forms', 'Stage Min'),
            'rowMin' => Yii::t('stock/forms', 'Row Min'),
            'rackMin' => Yii::t('stock/forms', 'Rack Min'),
            'levelMin' => Yii::t('stock/forms', 'Level Min'),
            'stageMax' => Yii::t('stock/forms', 'Stage Max'),
            'rowMax' => Yii::t('stock/forms', 'Row Max'),
            'rackMax' => Yii::t('stock/forms', 'Rack Max'),
            'levelMax' => Yii::t('stock/forms', 'Level Max'),
        ];
    }

    public function rules()
    {
        return [
            [['rowMin', 'rowMax', 'rackMin', 'rackMax'], 'required'],
            [['stageMin', 'stageMax'], 'validateStage'],
            [['rowMin', 'rowMax'], 'validateRow'],
            [['rackMin', 'rackMax'], 'validateRack'],
            [['levelMin', 'levelMax'], 'validateLevel'],
            [['stageMax'], 'compare', 'compareAttribute' => 'stageMin', 'operator' => '>=', 'type' => 'number'],
            [['rowMax'], 'compare', 'compareAttribute' => 'rowMin', 'operator' => '>=', 'type' => 'number'],
            [['rackMax'], 'compare', 'compareAttribute' => 'rackMin', 'operator' => '>=', 'type' => 'number'],
            [['levelMax'], 'compare', 'compareAttribute' => 'levelMin', 'operator' => '>=', 'type' => 'number'],
        ];
    }

    private function validateRange($attribute, $minValue, $maxValue, $fieldName)
    {
        $value = $this->$attribute;

        if (!preg_match('/^(0|[1-9][0-9]*)$/', $value)) {
            $this->addError($attribute, Yii::t('stock/errors', 'Неверный формат {field}: {value}', [
                'field' => $fieldName,
                'value' => $value
            ]));
            return;
        }

        $numericValue = (int)$value;
        if ($numericValue < $minValue || $numericValue > $maxValue) {
            $this->addError($attribute, Yii::t('stock/errors', '{field} должен быть в пределах {min} - {max}', [
                'field' => $fieldName,
                'min' => $minValue,
                'max' => $maxValue
            ]));
        }
    }

    public function validateStage($attribute)
    {
        if ($this->$attribute == 0 || is_null($this->$attribute)) {
            return;
        }

        $fieldName = Yii::t('stock/forms', 'Этаж');
        $this->validateRange($attribute, RackAddress::STAGE_MIN, RackAddress::STAGE_MAX, $fieldName);
    }

    public function validateRow($attribute)
    {
        $fieldName = Yii::t('stock/forms', 'Ряд');
        $this->validateRange($attribute, RackAddress::ROW_MIN, RackAddress::ROW_MAX, $fieldName);
    }

    public function validateRack($attribute)
    {
        $fieldName = Yii::t('stock/forms', 'Полка');
        $this->validateRange($attribute, RackAddress::RACK_MIN, RackAddress::RACK_MAX, $fieldName);
    }

    public function validateLevel($attribute)
    {
        if (is_null($this->$attribute)) {
            return;
        }

        $fieldName = Yii::t('stock/forms', 'Уровень');
        $this->validateRange($attribute, RackAddress::LEVEL_MIN, RackAddress::LEVEL_MAX, $fieldName);
    }

    public static function createAddress($stage = null, $row, $rack, $level = null)
    {
        $rack = ($rack < 10 && $rack > 0) ? '0' . $rack : $rack;

        $addressParts = [];

        if (!is_null($stage) && $stage != 0) {
            $addressParts[] = $stage;
        }

        $addressParts[] = $row;
        $addressParts[] = $rack;

        if (!is_null($level) && $level != 0) {
            $addressParts[] = $level;
        }

        $address = implode('-', $addressParts);

        if (!RackAddress::checkForExist($address)) {
            $ra = new RackAddress();
            $ra->warehouse_id = 0;
            $ra->zone_id = 0;
            $ra->address = $address;
            $ra->address_unit1 = $stage;
            $ra->address_unit2 = $row;
            $ra->address_unit3 = trim($rack, 0);
            $ra->address_unit4 = $level;
            $ra->sort_order = (int)RackAddress::find()->max('sort_order') + 1;
            if ($ra->save(false)) {
                return $ra->address;
            }
        }

        return false;
    }
}