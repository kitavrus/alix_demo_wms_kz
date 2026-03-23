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

class OutboundForm extends Model
{
    public $employee_barcode;
    public $pick_list_barcode;
    public $package_barcode;
    public $product_barcode;
	public $product_qrcode;
    public $kg;
    public $packageType;

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
			
            // Clean box
            [['package_barcode'],'required', 'on'=>self::SCENARIO_EMPTY_PACKAGE],
            [['pick_list_barcode'],'required', 'on'=>self::SCENARIO_EMPTY_PACKAGE],
            // Print box label
            [['pick_list_barcode'], 'PickListBarcode', 'on' => self::SCENARIO_PRINT_BOX_LABEL],
            [['pick_list_barcode'], 'PrintPickListBarcode', 'on' => self::SCENARIO_PRINT_BOX_LABEL],
			[['pick_list_barcode'], 'PrintBoxLabel', 'on' => self::SCENARIO_PRINT_BOX_LABEL],
						
            [['kg'], 'Kg', 'on' => self::SCENARIO_PRINT_BOX_LABEL],
            [['packageType'], 'PackageType', 'on' => self::SCENARIO_PRINT_BOX_LABEL],
            [['packageType','pick_list_barcode','kg'], 'required', 'on' => self::SCENARIO_PRINT_BOX_LABEL],
            // Print diff list
            [['pick_list_barcode'], 'required', 'on' => self::SCENARIO_PRINT_DIFF_LIST],
            // Show picking list items
            [['pick_list_barcode'], 'required', 'on' => self::SCENARIO_SHOW_PICKING_LIST_ITEMS],
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
	
	
    /*
    * Validate print box label
    * */
    public function PrintBoxLabel($attribute, $params)
    {
        $value = $this->pick_list_barcode;

        if($this->validation->isKaspiOrder($value)) {
            if ($this->validation->isValidKaspiOrder($value)) {
                $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Это заказ каспи. В нем есть расхождения. Его нельзя отгружать/печатать. Его нужно отменить.'));
            }
        }
		
		        if($this->validation->isLamodaOrder($value)) {
            if ($this->validation->isValidLamodaOrder($value)) {
                $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Это заказ Лямода. В нем есть расхождения. Его нельзя отгружать/печатать. Его нужно отменить.'));
            }
        }
		
    }

    public function Kg($attribute, $params)
    {
        $value = $this->$attribute;
        if(!$this->validation->kg($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Укажите вес заказа'));
        }
    }

    public function PackageType($attribute, $params)
    {
        $value = $this->$attribute;
        if(!$this->validation->packageType($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Укажите тип упаковки'));
        }
    }

   /*
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

    /*
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

    /*
     * */
    public function validateClearBox($attribute, $params)
    {
        $value = $this->$attribute;

//        if (!EcommerceStock::find()->andWhere([
//            'status' => EcommerceStock::STATUS_OUTBOUND_SCANNED,
//        ])->count()
//        ) {
//            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', '"Этот короб пуст или для него уже распечатали этикетки'));
//        }
    }

    /*
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
        $dto->kg = $this->kg;
        $dto->packageType = $this->packageType;
		$dto->productQRCode = $this->product_qrcode;
        return $dto;
    }
}