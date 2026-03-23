<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\checkBox\forms;

use common\ecommerce\constants\CheckBoxStatus;
use common\ecommerce\defacto\checkBox\service\CheckBoxService;
use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use yii\base\Model;
use Yii;

class CheckBoxForm extends Model
{
    public $inventoryId;
    public $employeeBarcode;
    public $boxBarcode;
    public $productBarcode;
    public $placeAddress;

    private $validation;
    private $checkBoxService;

    const SCENARIO_INVENTORY_ID = 'INVENTORY-ID';
    const SCENARIO_EMPLOYEE_BARCODE = 'EMPLOYEE-BARCODE';
    const SCENARIO_BOX_BARCODE = 'BOX-BARCODE';
    const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';
    const SCENARIO_PLACE_BARCODE = 'PLACE-BARCODE';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->validation = new \common\ecommerce\defacto\checkBox\validation\Validation();
        $this->checkBoxService = new CheckBoxService();
    }

    public function getInventoryKeyList() {
        return $this->checkBoxService->getInventoryKeyList();
    }

    /**
     *
     * */
    public function rules()
    {
        return [
            [['inventoryId', 'boxBarcode', 'productBarcode', 'placeAddress'], 'trim', 'on' => [self::SCENARIO_EMPLOYEE_BARCODE,self::SCENARIO_BOX_BARCODE,self::SCENARIO_PRODUCT_BARCODE,self::SCENARIO_PLACE_BARCODE]],
            [['inventoryId', 'boxBarcode', 'productBarcode', 'placeAddress'], 'string', 'on' => [self::SCENARIO_EMPLOYEE_BARCODE,self::SCENARIO_BOX_BARCODE,self::SCENARIO_PRODUCT_BARCODE,self::SCENARIO_PLACE_BARCODE]],
            // INVENTORY KEY
            [['inventoryId'], 'integer', 'on' => self::SCENARIO_INVENTORY_ID],
            [['inventoryId'], 'InventoryId', 'on' => self::SCENARIO_INVENTORY_ID],
            [['inventoryId'], 'required', 'on' => self::SCENARIO_INVENTORY_ID],
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
            [['inventoryId'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],
            // PRODUCT BARCODE
            [['productBarcode'], 'ProductBarcode', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['boxBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['placeAddress'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['inventoryId'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['inventoryId'], 'string',  'on' => self::SCENARIO_PRODUCT_BARCODE],
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
    * Validate Inventory Id
    * */
    public function InventoryId($attribute, $params)
    {
        $value = $this->$attribute;
        $inventoryInfo = $this->checkBoxService->getInventoryInfo($value);

        if (CheckBoxStatus::isDone($inventoryInfo->status)) {
            $this->addError($attribute, '<b>[' . $inventoryInfo->inventory_key . ']</b> ' . Yii::t('outbound/errors', 'Эта инвентаризиция уже закрыта'));
        }
    }

    /**
     * Validate barcode place
     * */
    public function PlaceAddress($attribute, $params)
    {
        $value = $this->$attribute;

        $inventoryInfo = $this->checkBoxService->getInventoryInfo($this->inventoryId);

        if (CheckBoxStatus::isDone($inventoryInfo->status)) {
            $this->addError($attribute, '<b>[' . $inventoryInfo->inventory_key . ']</b> ' . Yii::t('outbound/errors', 'Эта инвентаризиция уже закрыта'));
        }

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

        if(!$this->validation->isExistProductInStockBoxForScanning($boxBarcode)) {
            $this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст'));
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
        $inventoryId = $this->inventoryId;
        $boxBarcode = $this->boxBarcode;
        $productBarcode = $this->productBarcode;
        $placeAddress = $this->placeAddress;

        if(!$this->validation->isProductBarcode($productBarcode)) {
            $this->addError($attribute, '<b>['.$productBarcode.']</b> '.Yii::t('outbound/errors','Это не шк товара'));
        }

        if(!$this->validation->isExistProductInBoxForScanning($inventoryId,$productBarcode,$boxBarcode,$placeAddress)) {
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
            'inventoryId' => Yii::t('outbound/forms', 'Инвентори кей'),
            'boxBarcode' => Yii::t('outbound/forms', 'Шк короба'),
            'productBarcode' => Yii::t('outbound/forms', 'Шк товар'),
            'placeAddress' => Yii::t('outbound/forms', 'Шк полки'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->employeeBarcode = $this->employeeBarcode;
        $dto->inventoryId = $this->inventoryId;
        $dto->boxBarcode = $this->boxBarcode;
        $dto->productBarcode = $this->productBarcode;
        $dto->placeAddress = $this->placeAddress;
        return $dto;
    }
}