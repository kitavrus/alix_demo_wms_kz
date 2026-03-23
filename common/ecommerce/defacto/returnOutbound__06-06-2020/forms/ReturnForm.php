<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 9:33
 */

namespace common\ecommerce\defacto\returnOutbound\forms;

use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;
use common\ecommerce\defacto\returnOutbound\validation\ReturnValidation;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

class ReturnForm extends Model
{
    public $employeeBarcode; //
    public $orderNumber; // ExternalShipmentId
    public $boxBarcode; //
    public $productBarcode; // SkuBarcode
    public $returnProcess; // ReturnProcess (FirsQuality or Donation)

    private $validation;
    private $outboundRepository;

    const SCENARIO_EMPLOYEE_BARCODE = 'EMPLOYEE-BARCODE';
    const SCENARIO_ORDER_NUMBER = 'ORDER-NUMBER';
    const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';
    const SCENARIO_BOX_BARCODE = 'BOX-BARCODE';
    const SCENARIO_RETURN_PROCESS = 'RETURN-PROCESS';
    const SCENARIO_EMPTY_BOX = 'EMPTY-BOX';
    const SCENARIO_COMPLETE = 'COMPLETE';
    const SCENARIO_SHOW_ORDER_ITEMS = 'SHOW-ORDER-ITEMS';
    const SCENARIO_SHOW_BOX_ITEMS = 'SHOW-BOX-ITEMS';

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->validation = new ReturnValidation();
        $this->outboundRepository = new OutboundRepository();
    }

    /**
     * */
    public function rules()
    {
        return [
            [['employeeBarcode', 'orderNumber','boxBarcode', 'productBarcode'], 'trim'],
            [['employeeBarcode', 'orderNumber','boxBarcode', 'productBarcode'], 'string'],
            // Employee
            [['employeeBarcode'], 'EmployeeBarcode', 'on' => self::SCENARIO_EMPLOYEE_BARCODE],
            [['employeeBarcode'], 'required', 'on' => self::SCENARIO_EMPLOYEE_BARCODE],
            // Order Number
            [['orderNumber'], 'OrderNumber', 'on' => self::SCENARIO_ORDER_NUMBER],
            [['orderNumber'], 'required', 'on' => self::SCENARIO_ORDER_NUMBER],
            // BoxBarcode
            [['boxBarcode'], 'BoxBarcode', 'on' => self::SCENARIO_BOX_BARCODE],
            [['boxBarcode'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],
            [['employeeBarcode'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],
            [['orderNumber'], 'required', 'on' => self::SCENARIO_BOX_BARCODE],
            // Product
            [['productBarcode'], 'ProductBarcode', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['boxBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['employeeBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['orderNumber'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['productBarcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['returnProcess'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            // Return Process
            [['returnProcess'], 'ReturnProcess', 'on' => self::SCENARIO_RETURN_PROCESS],
            [['productBarcode'], 'required', 'on' => self::SCENARIO_RETURN_PROCESS],
            [['employeeBarcode'], 'required', 'on' => self::SCENARIO_RETURN_PROCESS],
            [['orderNumber'], 'required', 'on' => self::SCENARIO_RETURN_PROCESS],
            [['productBarcode'], 'required', 'on' => self::SCENARIO_RETURN_PROCESS],
            // Empty Box
            [['boxBarcode'], 'EmptyBox', 'on' => self::SCENARIO_EMPTY_BOX],
            [['boxBarcode'], 'required', 'on' => self::SCENARIO_EMPTY_BOX],
            [['employeeBarcode'], 'required', 'on' => self::SCENARIO_EMPTY_BOX],
            [['orderNumber'], 'required', 'on' => self::SCENARIO_EMPTY_BOX],
            [['boxBarcode'], 'required', 'on' => self::SCENARIO_EMPTY_BOX],
            // Show order items
            [['orderNumber'], 'ShowOrderItems', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],
            [['orderNumber'], 'required', 'on' => self::SCENARIO_SHOW_ORDER_ITEMS],

            // Show box items
            [['boxBarcode'], 'ShowBoxItems', 'on' => self::SCENARIO_SHOW_BOX_ITEMS],
            [['boxBarcode'], 'required', 'on' => self::SCENARIO_SHOW_BOX_ITEMS],

            // Complete
            [['orderNumber'], 'CompleteOrder', 'on' => self::SCENARIO_COMPLETE],
            [['orderNumber'], 'required', 'on' => self::SCENARIO_COMPLETE],
        ];
    }

    public function EmployeeBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if (!EmployeeRepository::isEmployee($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД СОТРУДНИКА'));
        }
    }

    public function OrderNumber($attribute, $params)
    {
        $value = $this->getOutboundOrderByAny($this->$attribute);
//        $value = $this->$attribute;
        if (!$this->validation->isOrderNumber($value)) {
            $this->addError($attribute, '<b>[' . $this->$attribute . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ номер накладной'));
        }
    }

    public function BoxBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if (!$this->validation->isOurInboundBoxBarcode($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД короба'));
        }
    }

    public function ProductBarcode($attribute, $params)
    {
        $productBarcode = $this->$attribute;
        if (!$this->validation->isDefactoProductBarcode($productBarcode)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели не шк товара дефакто'));
        }

        $orderNumber = $this->orderNumber;
        if ($this->validation->isProductNotExistInOrder($orderNumber,$productBarcode)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('outbound/errors', 'Этого товара нет в заказе'));
        }

        $orderNumber = $this->orderNumber;
        if ($this->validation->isExtraBarcodeInOrder($orderNumber,$productBarcode)) {
            $this->addError($attribute, '<b>[' . $productBarcode . ']</b> ' . Yii::t('outbound/errors', 'Этого товара лишний в заказе'));
        }
    }

    public function ReturnProcess($attribute, $params)
    {
        $value = $this->$attribute;
//        if (!EmployeeRepository::isEmployee($value)) {
//            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД СОТРУДНИКА'));
//        }
    }

    public function CompleteOrder($attribute, $params)
    {
        $value = $this->getOutboundOrderByAny($this->$attribute);
        if (!$this->validation->isOrderNumber($value)) {
            $this->addError($attribute, '<b>[' . $this->$attribute . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ номер накладной'));
        }

        //if ($this->validation->isOrderReturnWithDifferentScannedProduct($value)) {
          //  $this->addError($attribute, '<b>[' . $this->$attribute . ']</b> ' . Yii::t('outbound/errors', 'В заказе есть расхождения'));
        //}

    }

    public function EmptyBox($attribute, $params)
    {
        $value = $this->$attribute;
//        if (!EmployeeRepository::isEmployee($value)) {
//            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД СОТРУДНИКА'));
//        }
    }

    public function ShowOrderItems($attribute, $params)
    {
        $value = $this->$attribute;
//        if (!EmployeeRepository::isEmployee($value)) {
//            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД СОТРУДНИКА'));
//        }
    }

    public function ShowBoxItems($attribute, $params)
    {
        $value = $this->$attribute;
//        if (!EmployeeRepository::isEmployee($value)) {
//            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД СОТРУДНИКА'));
//        }
    }

    public function getOutboundOrderByAny($outboundOrderKey) {

        $outboundOrder = $this->outboundRepository->getOrderByAny($outboundOrderKey);
        return ArrayHelper::getValue($outboundOrder,'order_number');
    }

    public function getDTO()
    {
        $dto = new \stdClass();
//        $dto->employee = $this->validation->getEmployeeByBarcode($this->employeeBarcode);
        $dto->employeeBarcode = $this->employeeBarcode;
        $dto->orderNumber = $this->getOutboundOrderByAny($this->orderNumber);
//        $dto->orderNumber = $this->orderNumber;
        $dto->boxBarcode = BarcodeService::onlyDigital($this->boxBarcode);
        $dto->productBarcode = BarcodeService::onlyDigital($this->productBarcode);
        $dto->returnProcess = $this->returnProcess;
        return $dto;
    }
}