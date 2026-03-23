<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\inventory\forms;

//use common\modules\stock\models\Inventory;
//use common\modules\stock\models\Stock;
//use common\modules\codebook\models\Codebook;
use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\entities\EcommerceInventory;
use yii\base\Model;
use Yii;
use common\components\BarcodeManager;

class InventoryForm extends Model {

    public $inventory_id; // Ряд
    public $place_address_barcode; // Ряд
    public $box_address_barcode; // ШК короба
    public $product_barcode; // штрих код товара

    private $inventoryFileNameError = 'inventory20201212.csv';

    /*
     *
     * */
    public function attributeLabels()
    {
        return [
            'inventory_id' => Yii::t('stock/forms', 'Inventory title'),
            'place_address_barcode' => Yii::t('stock/forms', 'Secondary address'),
            'box_address_barcode' => Yii::t('stock/forms',  'Primary address'),
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
            [['place_address_barcode','box_address_barcode','product_barcode'], 'string'],

            [['place_address_barcode','inventory_id'], 'required','on'=>'SecondaryAddress'],
            [['place_address_barcode'], 'isRegimentValidate','on'=>['SecondaryAddress','PrimaryAddress','ProductBarcode']],
            [['place_address_barcode'], 'isModulusValidate','on'=>['SecondaryAddress']],

            [['box_address_barcode','place_address_barcode','inventory_id'], 'required','on'=>'PrimaryAddress'],
            [['box_address_barcode','place_address_barcode','inventory_id'], 'required','on'=>'ClearBox'],
            [['box_address_barcode'], 'isBoxValidate','on'=>['SecondaryAddress','PrimaryAddress','ProductBarcode','ClearBox']],

            [['box_address_barcode','place_address_barcode','product_barcode','inventory_id'], 'required','on'=>'ProductBarcode'],
            [['product_barcode'], 'isProductValidate','on'=>['ProductBarcode']],

            [['place_address_barcode','inventory_id'], 'required','on'=>'PrintInventoryDiffList'],
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
//        if(!BarcodeManager::isBox($value)) {
        if(!(new BarcodeService())->isOurInboundBoxBarcode($value)) {
            $this->addError($attribute,'Это не штрих код короба '.' [ '.$value.' ]');
        }
       // $val = new \common\ecommerce\defacto\changeAddressPlace\validation\Validation();


        if(!(new EcommerceInventory())->isBoxEmpty($value)) {
            $this->addError($attribute,'Этот короб пуст '.' [ '.$value.' ]');
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
        $boxBarcode = $this->box_address_barcode;

        $minMax = EcommerceInventory::getMinMaxSecondaryAddress($this->place_address_barcode);
//        if (BarcodeManager::isReturnBoxBarcode($productBarcode) || BarcodeManager::isOneBoxOneProduct($productBarcode)) {
//        if (BarcodeManager::isBoxLotOrReturnBox($productBarcode,$minMax,$boxBarcode)) {
//            $productBarcode = BarcodeManager::findProductInStockByReturnBarcodeBoxInventory($productBarcode);
//        }

        if(!BarcodeService::isFreeProduct($productBarcode)) {
            $this->addError($attribute,'Этот штрих код товара не найден на складе как доступный '.'[ '.$productBarcode.' ]'.' Короб: '.'[ '.$boxBarcode.' ]');

            $specialMessageToFile = '';
            $messageToFile = 'Этот штрих код товара не найден на складе как доступный ';
            $productBarcodeToFile = $productBarcode;
            $primaryAddressToFile = $boxBarcode;
            $secondaryAddressToFile = $this->place_address_barcode;
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