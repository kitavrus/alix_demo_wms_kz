<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\checkBox\forms;

use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use yii\base\Model;
use Yii;

class CheckBoxForm extends Model
{
    public $inventoryKey;
    public $title;
    public $employeeBarcode;
    public $boxBarcode;
    public $productBarcode;
    public $placeAddress;

    private $validation;

    const SCENARIO_INVENTORY_KEY_BARCODE = 'INVENTORY-KEY-BARCODE';
    const SCENARIO_TITLE_BARCODE = 'TITLE-BARCODE';
    const SCENARIO_EMPLOYEE_BARCODE = 'EMPLOYEE-BARCODE';
    const SCENARIO_BOX_BARCODE = 'BOX-BARCODE';
    const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';
    const SCENARIO_PLACE_BARCODE = 'PLACE-BARCODE';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->validation = new \common\ecommerce\defacto\checkBox\validation\Validation();
    }
    /**
     *
     * */
    public function rules()
    {
        return [
            [['inventoryKey', 'title', 'boxBarcode', 'productBarcode', 'placeAddress'], 'trim', 'on' => [self::SCENARIO_EMPLOYEE_BARCODE,self::SCENARIO_BOX_BARCODE,self::SCENARIO_PRODUCT_BARCODE,self::SCENARIO_PLACE_BARCODE]],
            [['inventoryKey', 'title', 'boxBarcode', 'productBarcode', 'placeAddress'], 'string', 'on' => [self::SCENARIO_EMPLOYEE_BARCODE,self::SCENARIO_BOX_BARCODE,self::SCENARIO_PRODUCT_BARCODE,self::SCENARIO_PLACE_BARCODE]],
            // INVENTORY KEY
            [['inventoryKey'], 'string', 'on' => self::SCENARIO_INVENTORY_KEY_BARCODE],
            // EMPLOYEE BARCODE
            [['employeeBarcode'], 'EmployeeBarcode', 'on' => self::SCENARIO_EMPLOYEE_BARCODE],
            [['employeeBarcode'], 'required', 'on' => self::SCENARIO_EMPLOYEE_BARCODE],
            // PLACE BARCODE
            [['placeAddress'], 'PlaceAddress', 'on' => self::SCENARIO_PLACE_BARCODE],
            [['placeAddress'], 'required', 'on' => self::SCENARIO_PLACE_BARCODE],
            // BOX BARCODE
            [['boxBarcode'], 'BoxBarcode', 'on' => self::SCENARIO_BOX_BARCODE],
            [['boxBarcode'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],
            [['employeeBarcode'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],
            [['placeAddress'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],
            [['title'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],
            // PRODUCT BARCODE
            [['productBarcode'], 'ProductBarcode', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['boxBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['placeAddress'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['title'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['inventoryKey'], 'string',  'on' => self::SCENARIO_PRODUCT_BARCODE],
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

    /**
     * Validate barcode place
     * */
    public function PlaceAddress($attribute, $params)
    {
        $value = $this->$attribute;

        if(!$this->validation->palaceAddress($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('inbound/errors', 'Это не адрес  4-го этажа'));
            return;
        }

        if(!$this->validation->isExistPalaceAddress($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('inbound/errors', 'Это не адрес полки'));
        }
    }

    /**
    * Validate barcode box
    * */
    public function BoxBarcode($attribute, $params)
    {
        $boxBarcode = $this->$attribute;
        $placeAddress = $this->placeAddress;
        if(!$this->validation->ourBoxBarcode($boxBarcode)) {
            $this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Это не наш короб'));
            return;
        }

        if(!$this->validation->isBoxNotEmpty($boxBarcode)) {
            $this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст'));
        }

        if(!$this->validation->isBoxOnPlaceAddress($boxBarcode,$placeAddress)) {
            $placeAddress = $this->validation->getPlaceAddressByBoxBarcode($boxBarcode);
            $this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Этого короба нет в этом адресе. Есть в :').$placeAddress);
        }
    }

    /**
    * Validate barcode product
    * */
    public function ProductBarcode($attribute, $params)
    {
        $inventoryKey = $this->inventoryKey;
        $title = $this->title;
        $boxBarcode = $this->boxBarcode;
        $productBarcode = $this->productBarcode;
        $placeAddress = $this->placeAddress;

        if(!$this->validation->isProductBarcode($productBarcode)) {
            $this->addError($attribute, '<b>['.$productBarcode.']</b> '.Yii::t('outbound/errors','Это не шк товара'));
        }

        if(!$this->validation->isExistProductInBoxForScanning($productBarcode,$boxBarcode,$placeAddress,$title,$inventoryKey)) {
            $this->addError($attribute, '<b>['.$productBarcode.']</b> '.Yii::t('outbound/errors','Это лишний шк товара'));
        }
    }

    /**
    *
    * */
    public function attributeLabels()
    {
        return [
            'employeeBarcode' => Yii::t('outbound/forms', 'Шк сотридника'),
            'inventoryKey' => Yii::t('outbound/forms', 'Инвентори кей'),
            'title' => Yii::t('outbound/forms', 'Дата'),
            'boxBarcode' => Yii::t('outbound/forms', 'Шк короба'),
            'productBarcode' => Yii::t('outbound/forms', 'Шк товар'),
            'placeAddress' => Yii::t('outbound/forms', 'Шк полки'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->employeeBarcode = $this->employeeBarcode;
        $dto->inventoryKey = $this->inventoryKey;
        $dto->title = $this->title;
        $dto->boxBarcode = $this->boxBarcode;
        $dto->productBarcode = $this->productBarcode;
        $dto->placeAddress = $this->placeAddress;
        return $dto;
    }
}