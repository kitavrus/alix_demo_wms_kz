<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\outbound\forms;

use common\ecommerce\defacto\outbound\validation\ValidationPartReReserved;
use yii\base\Model;
use Yii;

class PartReReservedForm extends Model
{
    public $employeeBarcode;
    public $pickListBarcode;

    private $validation;

    const SCENARIO_EMPLOYEE_BARCODE = 'EMPLOYEE-BARCODE';
    const SCENARIO_PICK_LIST_BARCODE = 'PICK-LIST-BARCODE';
    const SCENARIO_SHOW_OTHER_PRODUCT_ADDRESSES = 'SHOW-OTHER-PRODUCT-ADDRESSES';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->validation = new ValidationPartReReserved();
    }

    /**
     *
     * */
    public function rules()
    {
        return [
            [['pickListBarcode','employeeBarcode'], 'trim'],
            [['pickListBarcode','employeeBarcode'], 'string'],
            // Employee Barcode
            [['employeeBarcode'], 'EmployeeBarcode', 'on' => self::SCENARIO_EMPLOYEE_BARCODE],
            [['employeeBarcode'], 'required', 'on' => self::SCENARIO_EMPLOYEE_BARCODE],
            // Pick list barcode
            [['pickListBarcode'], 'PickListBarcode', 'on' => self::SCENARIO_PICK_LIST_BARCODE],
            [['pickListBarcode'], 'required', 'on' => self::SCENARIO_PICK_LIST_BARCODE],
        ];
    }

    /**
     * Validate employee barcode
     * */
    public function EmployeeBarcode($attribute, $params)
    {
        $value = $this->$attribute;

        if (!$this->validation->isEmployeeBarcode($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Нет сотрудника с таким шк'));
        }
    }

    /**
     * Validate barcode picking list
     * */
    public function PickListBarcode($attribute, $params)
    {
        $value = $this->$attribute;

        if (!$this->validation->isValidPickingList($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели несуществующий штрихкод сборочного листа'));
        }

        if (!$this->validation->isNotDoneOrder($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Этот заказ уже упакован'));
        }
    }

    /**
     *
     * */
    public function attributeLabels()
    {
        return [
            'employee_barcode' => Yii::t('outbound/forms', 'Сотрудник'),
            'pickListBarcode' => Yii::t('outbound/forms', 'Лист сборки'),
            'package_barcode' => Yii::t('outbound/forms', 'Пакет'),
            'product_barcode' => Yii::t('outbound/forms', 'Товар'),
        ];
    }

    /**
     *
     * */
    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->order = $this->validation->getOrderByPickList($this->pickListBarcode);
        $dto->employeeBarcode = $this->employeeBarcode;
        $dto->pickListBarcode = $this->pickListBarcode;
        return $dto;
    }
}