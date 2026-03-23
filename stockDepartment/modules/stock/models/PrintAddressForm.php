<?php

namespace stockDepartment\modules\stock\models;

use common\modules\stock\models\RackAddress;
use Yii;

class PrintAddressForm extends RackAddress
{
    public $stageMin;
    public $rowMin;
    public $rackMin;
    public $levelMin;
    public $stageMax;
    public $rowMax;
    public $rackMax;
    public $levelMax;
    public $printSize;

    public $labelWidth = 75;
    public $labelHeight = 120;

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
            'printSize' => Yii::t('stock/forms', 'Print size'),
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
            [['printSize'], 'in', 'range' => ['A4', 'Этикетка']],
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

    public static function getPrintTypesValues()
    {
        return ['A4', 'Этикетка'];
    }
} 