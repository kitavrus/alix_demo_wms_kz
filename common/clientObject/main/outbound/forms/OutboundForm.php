<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\clientObject\main\outbound\forms;

use common\components\BarcodeManager;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use  common\clientObject\main\outbound\validation\ValidationOutbound;
use yii\base\Model;
use Yii;
use common\modules\codebook\models\Codebook;
use common\modules\stock\models\Stock;
use yii\helpers\VarDumper;


class OutboundForm extends Model {

    public $employee_barcode;
    public $pick_list_barcode;
    public $box_barcode;
    public $product_barcode;

    private $validation;

    public function __construct($config = []) {
        parent::__construct($config);

        $this->validation = new ValidationOutbound();
    }

    /*
     * */
    public function rules()
    {
        return [
            [['employee_barcode','pick_list_barcode','box_barcode','product_barcode'], 'trim'],
            [['employee_barcode','pick_list_barcode','box_barcode','product_barcode'], 'string'],
            // Employee
            [['employee_barcode'],'IsEmployeeBarcode', 'on'=>'onEmployeeBarcode'],
            [['employee_barcode'],'required', 'on'=>'onEmployeeBarcode'],
            // Pick list
            [['pick_list_barcode'],'IsPickListBarcode', 'on'=>'onPickListBarcode'],
            [['pick_list_barcode'],'required', 'on'=>'onPickListBarcode'],
            // Our box
            [['box_barcode'],'IsBoxBarcode', 'on'=>'onBoxBarcode'],
            [['box_barcode'],'required', 'on'=>'onBoxBarcode'],
             // Product
            [['product_barcode'],'IsProductBarcode', 'on'=>'onProductBarcode'],
            [['product_barcode'],'required', 'on'=>'onProductBarcode'],
            [['box_barcode'],'required', 'on'=>'onProductBarcode'],
            [['pick_list_barcode'],'required', 'on'=>'onProductBarcode'],
            [['employee_barcode'],'required', 'on'=>'onProductBarcode'],
            // Clean box
            [['box_barcode'],'required', 'on'=>'onCleanBox'],
            [['pick_list_barcode'],'required', 'on'=>'onCleanBox'],
            // Print box label
            [['pick_list_barcode'],'IsPickListBarcode', 'on'=>'onPrintBoxLabel'],
            [['pick_list_barcode'],'required', 'on'=>'onPrintBoxLabel'],
            // Print diff list
            [['pick_list_barcode'], 'required', 'on' => 'onPrintDiffList'],
            // Show picking list items
            [['pick_list_barcode'], 'required', 'on' => 'onShowPickingListItems'],
        ];
    }
    /*
    * Validate barcode employee
    * */
    public function IsEmployeeBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isEmployee($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД СОТРУДНИКА'));
        }
    }
    /*
    * Validate barcode picking list
    * */
    public function IsPickListBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isPickingList($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод сборочного листа'));
        }

        if(BarcodeManager::isPickingList($value,[OutboundPickingLists::STATUS_NOT_SET,OutboundPickingLists::STATUS_BEGIN,OutboundPickingLists::STATUS_PRINT])) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели еще не собраный штрихкод сборочного листа'));
        }
        if(BarcodeManager::isPickingList($value,OutboundPickingLists::STATUS_PRINT_BOX_LABEL)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Этот сборочный лист уже упакован'));
        }
    }
    /*
    * Validate barcode picking list
    * */
    public function IsBoxBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!$this->validation->isBox($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД КОРОБА'));
        }
    }
    /*
    * Validate barcode product
    * */
    public function IsProductBarcode($attribute, $params)
    {
        $value = $this->preparedProductModel($this->$attribute);

        if(!$this->validation->isProduct($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','Вы ввели НЕСУЩЕСТВУЮЩИЙ ШТРИХКОД ТОВАРА'));
        }

        $product = $this->preparedProductModel($this->product_barcode);
        $pickList = $this->pick_list_barcode;
        if($this->validation->isExtraBarcodeInOrder($pickList,$product)) {
//            if ($this->validation->isNextBarcodeWithFabInOrder($pickList, $product)) {
//                $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Этот тавар вы должны принять по фаб номеру'));
//            } else {
                $this->addError($attribute, '<b>[' . $value . ']</b> ' . Yii::t('outbound/errors', 'Этот тавар лишний в заказе'));
//            }
        }
    }

    /*
     * */
    public function validateClearBox($attribute, $params)
    {
        $value = $this->$attribute;

        if( !Stock::find()->andWhere([
            'status'=>Stock::STATUS_OUTBOUND_SCANNED,
        ])->count()) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('outbound/errors','"Этот короб пуст или для него уже распечатали этикетки'));
        }
    }

    /*
    *
    * */
    public function attributeLabels()
    {
        return [
            'employee_barcode' => Yii::t('outbound/forms', 'Employee barcode'),
            'pick_list_barcode' => Yii::t('outbound/forms', 'Picking list barcode'),
            'box_barcode' => Yii::t('outbound/forms', 'Box barcode'),
            'product_barcode' => Yii::t('outbound/forms', 'Product barcode'),
        ];
    }

    public function getDTO() {

        $dto = new \stdClass();
        $dto->order = $this->validation->getOrderByPickList($this->pick_list_barcode);
        $dto->pickList = $this->validation->getPickListByBarcode($this->pick_list_barcode);
        $dto->employee = $this->validation->getEmployeeByBarcode($this->employee_barcode);
        $dto->boxBarcode = $this->box_barcode;
        $dto->productBarcode = $this->preparedProductModel($this->product_barcode);
        return $dto;
    }

    public function preparedProductModel($productModel) {
        file_put_contents('preparedProductModel.log',$productModel."\n",FILE_APPEND);
        $tmp = explode(' ',$productModel);
        if(isset($tmp['1'])) {
            $x = $tmp['0'];
        } else {
            $x = $productModel;
        }

        file_put_contents('preparedProductModel.log',$x."\n",FILE_APPEND);
        return $x;
    }
}