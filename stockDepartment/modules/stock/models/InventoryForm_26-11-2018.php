<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\stock\models;

use common\components\BarcodeManager;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;
use common\modules\codebook\models\Codebook;

class InventoryForm extends Model {

    public $inventory_id; // Ряд
    public $secondary_address; // Ряд
    public $primary_address; // ШК короба
    public $product_barcode; // штрих код товара

    private $inventoryFileNameError = 'inventory20170105.csv';

    /*
     *
     * */
    public function attributeLabels()
    {
        return [
            'inventory_id' => Yii::t('stock/forms', 'Inventory title'),
            'secondary_address' => Yii::t('stock/forms', 'Secondary address'),
            'primary_address' => Yii::t('stock/forms',  'Primary address'),
            'product_barcode' => Yii::t('stock/forms', 'Product barcode'),
        ];
    }
    /*
     *
     * */
    public function rules()
    {
        return [
            [['inventory_id'], 'integer'],
            [['secondary_address','primary_address','product_barcode'], 'string'],

            [['secondary_address','inventory_id'], 'required','on'=>'SecondaryAddress'],
            [['secondary_address'], 'isRegimentValidate','on'=>['SecondaryAddress','PrimaryAddress','ProductBarcode']],
            [['secondary_address'], 'isModulusValidate','on'=>['SecondaryAddress']],

            [['primary_address','secondary_address','inventory_id'], 'required','on'=>'PrimaryAddress'],
            [['primary_address','secondary_address','inventory_id'], 'required','on'=>'ClearBox'],
            [['primary_address'], 'isBoxValidate','on'=>['SecondaryAddress','PrimaryAddress','ProductBarcode','ClearBox']],

            [['primary_address','secondary_address','product_barcode','inventory_id'], 'required','on'=>'ProductBarcode'],
            [['product_barcode'], 'isProductValidate','on'=>['ProductBarcode']],

            [['secondary_address','inventory_id'], 'required','on'=>'PrintInventoryDiffList'],
        ];
    }

    /*
    * Is Regiment
    * @param string barcode
    * @return boolean
    * */
    public function isRegimentValidate($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isRegiment($value)) {
            $this->addError($attribute,'Это не адрес полки '.'[ '.$value.']');
        }
    }

    /*
    * Is Modulus
    * @param string barcode
    * @return boolean
    * */
    public function isModulusValidate($attribute, $params)
    {
        $value = $this->$attribute;
        $sa = explode('-',trim($value));
        if(!is_array($sa)) {
            if($sa['1'] % 2) {
                $this->addError($attribute, 'Ряд должен быть не четный, Например: 1 или 3 или 5 и т.д');
            }
        }
    }

    /*
     * Is box
     * @param string barcode
     * @return boolean
     * */
    public function IsBoxValidate($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isBox($value)) {
            $this->addError($attribute,'Это не штрих код короба '.' [ '.$value.' ]');
        }
//        if(!BarcodeManager::isEmptyBox($value)) {
//            $this->addError($attribute,'Этот короб не пуст '.' [ '.$value.' ]');
//        }
    }

    /*
     * Is product
     * @param string barcode
     * @return boolean
     * */
    public function IsProductValidate($attribute, $params)
    {
        $productBarcode = $this->product_barcode = $this->preparedProductModel($this->$attribute);
        $boxBarcode = $this->primary_address;

        if (BarcodeManager::isReturnBoxBarcode($productBarcode) || BarcodeManager::isOneBoxOneProduct($productBarcode)) {
            $productBarcode = BarcodeManager::findProductInStockByReturnBarcodeBoxInventory($productBarcode);
        }

        if(!BarcodeManager::isFreeProduct($productBarcode)) {
            $this->addError($attribute,'Этот штрих код товара не найден на складе как доступный '.'[ '.$productBarcode.' ]'.' Короб: '.'[ '.$boxBarcode.' ]');

            $specialMessageToFile = '';
            $messageToFile = 'Этот штрих код товара не найден на складе как доступный ';
            $productBarcodeToFile = $productBarcode;
            $primaryAddressToFile = $boxBarcode;
            $secondaryAddressToFile = $this->secondary_address;
            $contentToFile = $messageToFile . ";" . $productBarcodeToFile . ";" . $primaryAddressToFile . ";" . $specialMessageToFile . ";" . $secondaryAddressToFile . ";";
            file_put_contents($this->inventoryFileNameError, $contentToFile."\n", FILE_APPEND);
        }
        // TODO доделать проверку чтобы этот товар был в этомряду
//        $this->addError($attribute,'qqqqq');
    }

    public function preparedProductModel($productModel) {
        file_put_contents('preparedProductModel-inventory-hyundai-auto.log',$productModel."\n",FILE_APPEND);
        $tmp = explode(' ',$productModel);
        if(isset($tmp['1'])) {
            $x = $tmp['0'];
        } else {
            $x = $productModel;
        }

        file_put_contents('preparedProductModel-inventory.log',$x."\n",FILE_APPEND);
        return $x;
    }
}