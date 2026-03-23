<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\outbound\forms;

use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use  common\ecommerce\defacto\outbound\validation\ValidationOutbound;
use common\ecommerce\entities\EcommerceStock;
use yii\base\Model;
use Yii;

class OutboundChangeForm extends Model
{
    public $employee_barcode;
    public $pick_list_barcode;

    private $validation;

    const SCENARIO_EMPLOYEE_BARCODE = 'EMPLOYEE-BARCODE';
    const SCENARIO_PICK_LIST_BARCODE = 'PICK-LIST-BARCODE';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->validation = new ValidationOutbound();
    }

    /*
     * */
    public function rules()
    {
        return [
            [['employee_barcode', 'pick_list_barcode', 'product_barcode'], 'trim'],
            [['employee_barcode', 'pick_list_barcode', 'product_barcode'], 'string'],
            // Employee
            [['employee_barcode'], 'EmployeeBarcode', 'on' => self::SCENARIO_EMPLOYEE_BARCODE],
            [['employee_barcode'], 'required', 'on' => self::SCENARIO_EMPLOYEE_BARCODE],
            // Pick list
            [['pick_list_barcode'], 'PickListBarcode', 'on' => self::SCENARIO_PICK_LIST_BARCODE],
            [['pick_list_barcode'], 'required', 'on' => self::SCENARIO_PICK_LIST_BARCODE],
            [['employee_barcode'], 'required', 'on' => self::SCENARIO_PICK_LIST_BARCODE],
        ];
    }

    /*
    * Validate barcode employee
    * */
    public function EmployeeBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if (!EmployeeRepository::isEmployee($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД СОТРУДНИКА'));
        }
    }

    /*
    * Validate barcode picking list
    * */
    public function PickListBarcode($attribute, $params)
    {
        $value = $this->$attribute;

        if(!$this->validation->isValidPickingList($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод сборочного листа'));
        }

        if(!$this->validation->isOrderReserved($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Этот заказ не зарезервирован'));
        }

        if(!$this->validation->isNotDoneOrder($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Этот заказ уже упакован'));
        }
    }

    /*
    *
    * */
    public function attributeLabels()
    {
        return [
            'employee_barcode' => Yii::t('outbound/forms', 'Сотрудник'),
            'pick_list_barcode' => Yii::t('outbound/forms', 'Лист сборки'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->employee = $this->validation->getEmployeeByBarcode($this->employee_barcode);
        $dto->order = $this->validation->getOrderByPickList($this->pick_list_barcode);
        $dto->pickListBarcode = $this->pick_list_barcode;
        return $dto;
    }
}