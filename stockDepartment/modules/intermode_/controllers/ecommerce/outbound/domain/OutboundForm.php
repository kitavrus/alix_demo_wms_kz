<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace app\modules\intermode\controllers\ecommerce\outbound\domain;

use app\modules\intermode\controllers\ecommerce\barcode\domain\service\BarcodeService;
use app\modules\intermode\controllers\ecommerce\employee\domain\repository\EmployeeRepository;
use app\modules\intermode\controllers\ecommerce\outbound\domain\validation\ValidationOutbound;
use yii\base\Model;
use Yii;

class OutboundForm extends Model
{
    public $employee_barcode;
    public $pick_list_barcode;
    public $package_barcode;
    public $product_barcode;
    public $product_qrcode;
    public $stockId = -1;

    private $validation;

    const SCENARIO_EMPLOYEE_BARCODE = 'EMPLOYEE-BARCODE';
    const SCENARIO_PICK_LIST_BARCODE = 'PICK-LIST-BARCODE';
    const SCENARIO_PACKAGE_BARCODE = 'PACKAGE-BARCODE';
    const SCENARIO_PRODUCT_BARCODE = 'PRODUCT-BARCODE';
    const SCENARIO_PRODUCT_QR_CODE = 'PRODUCT-QR-CODE';
    const SCENARIO_PRINT_BOX_LABEL = 'PRINT-BOX-LABEL';
    const SCENARIO_PRINT_DIFF_LIST = 'PRINT-DIFF-LIST';
    const SCENARIO_SHOW_PICKING_LIST_ITEMS = 'SHOW-PICKING-LIST-ITEMS';
    const SCENARIO_EMPTY_PACKAGE = 'EMPTY-PACKAGE';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->validation = new ValidationOutbound();
    }

    /**
	 *
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
            // Package barcode
            [['package_barcode'],'PackageBarcode', 'on'=>self::SCENARIO_PACKAGE_BARCODE],
            [['package_barcode'],'required', 'on'=>self::SCENARIO_PACKAGE_BARCODE],
            // Product barcode
            [['product_barcode'], 'ProductBarcode', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['product_barcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['package_barcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['pick_list_barcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
            [['employee_barcode'], 'required', 'on' => self::SCENARIO_PRODUCT_BARCODE],
			// Product QR Code
            [['product_qrcode'],  'required','on' => self::SCENARIO_PRODUCT_QR_CODE],
            [['product_barcode'],  'required','on' => self::SCENARIO_PRODUCT_QR_CODE],
            [['pick_list_barcode'],  'required','on' => self::SCENARIO_PRODUCT_QR_CODE],
			[['employee_barcode'], 'required', 'on' => self::SCENARIO_PRODUCT_QR_CODE],
			[['stockId'], 'required', 'on' => self::SCENARIO_PRODUCT_QR_CODE],
			[['employee_barcode'], 'isProductQRCode', 'on' => self::SCENARIO_PRODUCT_QR_CODE],
            // Clean box
            [['package_barcode'],'required', 'on'=>self::SCENARIO_EMPTY_PACKAGE],
            [['pick_list_barcode'],'required', 'on'=>self::SCENARIO_EMPTY_PACKAGE],
            // Print box label
            [['pick_list_barcode'], 'PickListBarcode', 'on' => self::SCENARIO_PRINT_BOX_LABEL],
            [['pick_list_barcode'], 'PrintPickListBarcode', 'on' => self::SCENARIO_PRINT_BOX_LABEL],
            // Print diff list
            [['pick_list_barcode'], 'required', 'on' => self::SCENARIO_PRINT_DIFF_LIST],
            // Show picking list items
            [['pick_list_barcode'], 'required', 'on' => self::SCENARIO_SHOW_PICKING_LIST_ITEMS],
        ];
    }

    /**
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
		
		if($this->validation->isCancelOrder($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Заказ отменен, положите товары по местам'));
        }
    }

    /**
    * Validate  package barcode on order
    * */
    public function PrintPickListBarcode($attribute, $params)
    {
        $value = $this->$attribute;

        if($this->validation->isEmptyPackageBarcodeInOrder($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','В заказе есть товары без пакета'));
        }

        if(!$this->validation->isOrderScanned($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Этот заказ не отсканирован'));
        }
    }

   /**
    * Validate barcode picking list
    * */
    public function PackageBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        $pickList = $this->pick_list_barcode;

        if(!$this->validation->isPackageBarcodeExist($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод пакета'));
        }

        if(!$this->validation->usePackageBarcodeInOtherOrder($pickList,$value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Этот шк уже используется в другом заказе'));
        }
    }

    /**
    * Validate barcode product
    * */
    public function ProductBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        $pickList = $this->pick_list_barcode;
        if (!$this->validation->isProduct($pickList, $value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'ЭТОГО ТАВАРА НЕТ В ЗАКАЗЕ'));
        }

        $product = $this->product_barcode;
        if ($this->validation->isExtraBarcodeInOrder($pickList, $product)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'ЭТОТ ТАВАР ЛИШНИЙ В ЗАКАЗЕ'));
        }
    }

    /**
    * Validate product qr
    * */
    public function isProductQRCode($attribute, $params)
    {
        $qrcode= $this->product_qrcode;
        if ($this->validation->isExistsProductQRCode($qrcode)) {
            $this->addError($attribute, '<b>[' . $qrcode . ']</b> ' . Yii::t('outbound/errors', 'Такой QR код уже используется'));
        }
    }

    /*
     * */
//    public function validateClearBox($attribute, $params)
//    {
//        $value = $this->$attribute;

//        if (!EcommerceStock::find()->andWhere([
//            'status' => EcommerceStock::STATUS_OUTBOUND_SCANNED,
//        ])->count()
//        ) {
//            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', '"Этот короб пуст или для него уже распечатали этикетки'));
//        }
//    }

    /**
    *
    * */
    public function attributeLabels()
    {
        return [
            'employee_barcode' => Yii::t('outbound/forms', 'Сотрудник'),
            'pick_list_barcode' => Yii::t('outbound/forms', 'Лист сборки'),
            'package_barcode' => Yii::t('outbound/forms', 'Пакет'),
            'product_barcode' => Yii::t('outbound/forms', 'Товар'),
            'product_qrcode' => Yii::t('outbound/forms', 'QR код'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->employee = $this->validation->getEmployeeByBarcode($this->employee_barcode);
        $dto->order = $this->validation->getOrderByPickList($this->pick_list_barcode);
        $dto->pickListBarcode = $this->pick_list_barcode;
        $dto->packageBarcode = BarcodeService::onlyDigital($this->package_barcode);
        $dto->productBarcode = BarcodeService::onlyDigital($this->product_barcode);
        $dto->productQRCode = BarcodeService::addGS($this->product_qrcode);
        $dto->stockId = $this->stockId;
        return $dto;
    }
}