<?php
namespace stockDepartment\modules\wms\models\miele\form;


use stockDepartment\modules\wms\models\miele\validation\ValidationMovement;
use Yii;
use yii\base\Model;
use common\components\BarcodeManager;

class MovementForm extends Model
{
    public $employee_barcode;
    public $pick_list_barcode;
    public $product_barcode;
    public $fub_barcode;
    public $to_box;
    public $to_address;

    private $validation;

    // Y
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->validation = new ValidationMovement();
    }

    // Y
    public function rules()
    {
        return [
            [['to_address', 'to_box', 'fub_barcode', 'employee_barcode', 'pick_list_barcode', 'product_barcode'], 'trim'],
            [['to_address', 'to_box', 'fub_barcode', 'employee_barcode', 'pick_list_barcode', 'product_barcode'], 'string'],

            [['employee_barcode'], 'IsEmployeeBarcode', 'on' => 'onEmployeeHandler'],
            [['employee_barcode'], 'required', 'on' => 'onEmployeeHandler'],

            [['pick_list_barcode'], 'IsPickListBarcode', 'on' => 'onPickListHandler'],
            [['pick_list_barcode'], 'required', 'on' => 'onPickListHandler'],

            [['product_barcode'], 'IsProductBarcode', 'on' => 'onProductHandler'],
            [['product_barcode'], 'required', 'on' => 'onProductHandler'],
            [['pick_list_barcode'], 'required', 'on' => 'onProductHandler'],
            [['employee_barcode'], 'required', 'on' => 'onProductHandler'],

            [['fub_barcode'], 'IsFubBarcode', 'on' => 'onFubHandler'],
            [['product_barcode'], 'IsProductBarcode', 'on' => 'onFubHandler'],
            [['product_barcode'], 'required', 'on' => 'onFubHandler'],
            [['pick_list_barcode'], 'required', 'on' => 'onFubHandler'],
            [['employee_barcode'], 'required', 'on' => 'onFubHandler'],

            [['to_box'], 'IsToBoxBarcode', 'on' => 'onToBoxHandler'],
            [['to_box'], 'required', 'on' => 'onToBoxHandler'],

            [['to_address'], 'IsToAddressBarcode', 'on' => 'onToAddressHandler'],
            [['to_address'], 'required', 'on' => 'onToAddressHandler'],

            [['pick_list_barcode'], 'required', 'on' => 'onPrintDiffList'],
        ];
    }

    //Y
    public function IsEmployeeBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if (!BarcodeManager::isEmployee($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД СОТРУДНИКА'));
        }
    }

    //
    public function IsPickListBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if (!$this->validation->isPickListExist($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели несуществующий штрихкод листа перемещения'));
        }
    }

    //
    public function IsProductBarcode($attribute, $params)
    {
        $value = $this->$attribute;

        if (!$this->validation->isProduct($value)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД ТОВАРА'));
        }
        $productBarcode = $this->product_barcode;
        $pickListBarcode = $this->pick_list_barcode;
        if (!$this->validation->isProductInMovementOrder($pickListBarcode, $productBarcode)) {
            $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Этого шк нет в этом листе перемещения'));
        }
    }

    //
    public function IsFubBarcode($attribute, $params)
    {
        $fabBarcode = $this->fub_barcode;
        $productBarcode = $this->product_barcode;
        $pickListBarcode = $this->pick_list_barcode;
        if (!$this->validation->isFub($pickListBarcode, $productBarcode, $fabBarcode)) {
            $this->addError($attribute, '<b>[' . $fabBarcode . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели не ФАБ. НОМЕР'));
        }
    }

    //
    public function IsToBoxBarcode($attribute, $params)
    {
        $toBoxBarcode = $this->to_box;
        if (!BarcodeManager::isBoxOnlyOur($toBoxBarcode)) {
            $this->addError($attribute, '<b>[' . $toBoxBarcode . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели не шк нашего короба'));
        }
    }

    //
    public function IsToAddressBarcode($attribute, $params)
    {
        $toAddressBarcode = $this->to_address;
        if (!BarcodeManager::isRegiment($toAddressBarcode)) {
            $this->addError($attribute, '<b>[' . $toAddressBarcode . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели не адрес полки'));
        }
        // проверяем какой зоне принадлежит адрес TODO
        $dto = $this->getDTO();
        if(!BarcodeManager::addressInZone($toAddressBarcode,$dto->order->to_zone) ) {
            $this->addError($attribute,Yii::t('stock/errors','Неверная зона для перемещения').' ['.$toAddressBarcode.']');
        }
        // Добавить проверку на перескан TODO

        if($this->validation->isExtraBarcodeInOrder($dto->order->id,$dto->productBarcode)) {
            if ($this->validation->isNextBarcodeWithFabInOrder($dto->order->id, $dto->productBarcode)) {
                $this->addError($attribute, '<b>[' . $dto->productBarcode . ']</b> ' . Yii::t('outbound/errors', 'Этот тавар вы должны перемещен по фаб номеру'));
            } else {
                $this->addError($attribute, '<b>[' . $dto->productBarcode . ']</b> ' . Yii::t('outbound/errors', 'Этот тавар лишний в заказе'));
            }
        }
    }

    //
    public function attributeLabels()
    {
        return [
            'employee_barcode' => Yii::t('outbound/forms', 'Employee barcode'),
            'picking_list_barcode' => Yii::t('outbound/forms', 'Picking list barcode'),
            'box_barcode' => Yii::t('outbound/forms', 'Box barcode'),
            'product_barcode' => Yii::t('outbound/forms', 'Product barcode'),
        ];
    }

    //
    public function getDTO()
    {

        $dto = new \stdClass();
        $dto->employee = $this->validation->getEmployeeByBarcode($this->employee_barcode);
        $dto->pickList = $this->validation->getPickListByBarcode($this->pick_list_barcode);
        $dto->order = $this->validation->getOrderByPickList($this->pick_list_barcode);
        $dto->productBarcode = $this->product_barcode;
        $dto->fabBarcode = $this->fub_barcode;
        $dto->boxBarcode = $this->to_box;
        $dto->addressBarcode = $this->to_address;
        return $dto;
    }
}