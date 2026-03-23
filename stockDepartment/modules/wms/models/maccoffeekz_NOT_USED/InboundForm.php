<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\wms\models\maccoffeekz;

use common\components\BarcodeManager;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
//use common\modules\inbound\models\InboundOrderItemProcess;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;
//use common\modules\codebook\models\Codebook;
//use common\modules\product\models\ProductBarcodes;

class InboundForm extends Model {

    public $client_id;
    public $party_number;
    public $order_number;
    public $product_barcode;
    public $box_barcode;
    public $product_name;
    public $pallet_barcode;
    public $qty_box_on_pallet;

    public function rules()
    {
        return [
            [['client_id','order_number'], 'required'],
            [['qty_box_on_pallet','client_id'], 'integer'],
            [['pallet_barcode','order_number','box_barcode','product_barcode','product_name','party_number'], 'string'],
            [['pallet_barcode','order_number','box_barcode','product_barcode','product_name','party_number','qty_box_on_pallet'], 'trim'],
            [['pallet_barcode'], 'vPalletBarcode','on'=>'sPalletBarcode'],

            [['box_barcode'], 'vBoxBarcode','on'=>'sBoxBarcode'],
            [['box_barcode','pallet_barcode'], 'required','on'=>'sBoxBarcode'],

            [['product_barcode'], 'vProductBarcode','on'=>'sProductBarcode'],
            [['product_barcode','box_barcode'], 'required','on'=>'sProductBarcode'],

            [['product_barcode'], 'vLinkProductBarcodeToBox','on'=>'sLinkProductBarcodeToBox'],
            [['product_barcode','box_barcode','pallet_barcode'], 'required','on'=>'sLinkProductBarcodeToBox'],

            [['product_barcode'], 'vUnlinkProductBarcodeToBox','on'=>'sUnlinkProductBarcodeToBox'],
            [['product_barcode','box_barcode','pallet_barcode'], 'required','on'=>'sUnlinkProductBarcodeToBox'],

            [['product_name'], 'vProductName','on'=>'sProductName'],
            [['product_name','box_barcode'], 'required','on'=>'sProductName'],

            [['product_name'], 'vLinkProductNameToBox','on'=>'sLinkProductNameToBox'],
            [['product_name','box_barcode','pallet_barcode'], 'required','on'=>'sLinkProductNameToBox'],

            [['product_name'], 'vUnlinkProductNameToBox','on'=>'sUnlinkProductNameToBox'],
            [['product_name','box_barcode','pallet_barcode'], 'required','on'=>'sUnlinkProductNameToBox'],

            [['pallet_barcode'], 'vClearPalletBarcode','on'=>'sClearPalletBarcode'],
            [['pallet_barcode'], 'required','on'=>'sClearPalletBarcode'],

            [['qty_box_on_pallet'], 'vQtyBoxInPallet','on'=>'sQtyBoxInPallet'],
            [['qty_box_on_pallet','pallet_barcode','box_barcode'], 'required','on'=>'sQtyBoxInPallet'],

            [['client_id','order_number','party_number'], 'required','on'=>'sConfirmOrder'],
        ];
    }

    /*
    * Validate pallet_barcode
    * */
    public function vPalletBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isPallet($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','Вы ввели неправильный штрихкод паллеты'));
        }
    }

    /*
    * Validate box_barcode
    * */
    public function vBoxBarcode($attribute, $params)
    {
    }

    /*
    * Validate product_barcode
    * */
    public function vProductBarcode($attribute, $params)
    {
    }

    /*
    * Validate product_ame
    * */
    public function vProductName($attribute, $params)
    {
    }
    /*
    * Validate
    * */
    public function vLinkProductBarcodeToBox($attribute, $params)
    {
    }

    /*
    * Validate
    * */
    public function vUnlinkProductBarcodeToBox($attribute, $params)
    {
    }

    /*
    * Validate
    * */
    public function vLinkProductNameToBox($attribute, $params)
    {
    }

    /*
    * Validate
    * */
    public function vUnlinkProductNameToBox($attribute, $params)
    {
    }

    /*
    * Validate if qty box in pallet
    * */
    public function vQtyBoxInPallet($attribute, $params)
    {
    }

    /*
    * Validate if clear pallet
    * */
    public function vClearPalletBarcode($attribute, $params)
    {
    }

    /*
    * Validate box_barcode
    * */
/*    public function validateBoxBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isBox($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','Invalid box barcode. Box barcode first letter must be b'));
        }
    }*/

    /*
     * Validate product_barcode
     * */
/*    public function validateProductBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        $inbound_order_id = $this->order_number;
        if(!self::checkProductBarcode($value,$inbound_order_id)) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Scanned product barcode not found in selected inbound'));
        }
    }*/

    /*
     * Remove product in box
     *
     * */
/*    public function validateProductInBox($attribute, $params)
    {
        $value = $this->$attribute;
        $box_barcode = $this->box_barcode;
        if(!self::checkProductInBox($value,$box_barcode)) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Box is empty')); // Этого товара нет в укзанном коробе
        }
//        if($product = self::checkProductInBox($value,$box_barcode)){
//            if($product->outbound_order_id){
//                $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This product already assigned to outbound order')); // Этот товар уже привязан к outbound order
//            }
//        }
        if(InboundOrder::find()->where(['status'=>Stock::STATUS_INBOUND_COMPLETE,'id'=> $this->order_number])->exists()) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This order is complete'));
        }

    }*/

    /*
     * Remove all product in box
     *
     * */
/*    public function validateClearBox($attribute, $params)
    {
        $value = $this->$attribute;
//        $box_barcode = $this->box_barcode;
//        if(!self::checkProductInBox($value,$box_barcode)) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Box is empty')); // Этого товара нет в укзанном коробе
//        }

        if(InboundOrder::find()->where(['status'=>Stock::STATUS_INBOUND_COMPLETE,'id'=> $this->order_number])->exists()) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This order is complete'));
        }

    }*/

    public function attributeLabels()
    {
        return [
            'party_number' => Yii::t('inbound/forms', 'Party number'),
            'client_id' => Yii::t('inbound/forms', 'Client'),
            'order_number' => Yii::t('inbound/forms', 'Order number'),
            'box_barcode' => Yii::t('inbound/forms', 'Box barcode'),
            'product_barcode' => Yii::t('inbound/forms', 'Product Barcode'),
            'pallet_barcode' => Yii::t('inbound/forms', 'PALLET_BARCODE'),
            'product_name' => Yii::t('inbound/forms', 'PRODUCT_NAME'),
            'qty_box_on_pallet' => Yii::t('inbound/forms', 'QTY_BOX_ON_PALLET'),
        ];
    }

    /*
    * Check exist client box barcode
    * */
    public function checkExistClientBoxBarcode()
    {
        $boxBarcode = $this->box_barcode;
        $orderNumberId = $this->order_number;
        return InboundOrderItem::find()->andWhere(['inbound_order_id'=>$orderNumberId,'box_barcode'=>$boxBarcode])->exists();
    }

    /*
     * Проверяет существует ли отсканированный товар в выбранном заказе
     * @param string $productBarcode
     * @param integer $inbound_order_id
     * @return
     * */
/*    public function checkProductBarcode($productBarcode,$inbound_order_id)
    {
        return InboundOrderItem::find()->where(['product_barcode'=>$productBarcode,'inbound_order_id'=>$inbound_order_id])->exists();
    }*/

    /*
    * Check exist product in box
    * @param string $productBarcode
    * @return boolean
    * */
/*    public function checkProductInBox($productBarcode,$box_barcode)
    {
        return Stock::find()->where(['primary_address'=>$box_barcode,'product_barcode'=>$productBarcode,'status'=>[Stock::STATUS_INBOUND_SCANNED,Stock::STATUS_INBOUND_OVER_SCANNED]])->exists();
//        return InboundOrderItemProcess::find()->where(['box_barcode'=>$box_barcode,'product_barcode'=>$productBarcode])->exists();
    }*/

}