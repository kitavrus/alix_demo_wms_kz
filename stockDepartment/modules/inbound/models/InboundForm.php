<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\inbound\models;

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
    public $order_number;
    public $product_barcode;
    public $box_barcode;
    public $party_number;


    /*
 * Validate box_barcode
 * */
    public function validateBoxBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isBox($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','Invalid box barcode. Box barcode first letter must be b'));
        }
    }

    /*
     * Validate product_barcode
     * */
    public function validateProductBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        $inbound_order_id = $this->order_number;
        if(!self::checkProductBarcode($value,$inbound_order_id)) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Scanned product barcode not found in selected inbound'));
        }
    }

    /*
     * Remove product in box
     *
     * */
    public function validateProductInBox($attribute, $params)
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

    }

    /*
     * Remove all product in box
     *
     * */
    public function validateClearBox($attribute, $params)
    {
        $value = $this->$attribute;
//        $box_barcode = $this->box_barcode;
//        if(!self::checkProductInBox($value,$box_barcode)) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Box is empty')); // Этого товара нет в укзанном коробе
//        }

        if(InboundOrder::find()->where(['status'=>Stock::STATUS_INBOUND_COMPLETE,'id'=> $this->order_number])->exists()) {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','This order is complete'));
        }

    }

    public function attributeLabels()
    {
        return [
            'party_number' => Yii::t('inbound/forms', 'Party number'),
            'client_id' => Yii::t('inbound/forms', 'Client'),
            'order_number' => Yii::t('inbound/forms', 'Order number'),
            'box_barcode' => Yii::t('inbound/forms', 'Box barcode'),
            'product_barcode' => Yii::t('inbound/forms', 'Product barcode'),
        ];
    }

    public function rules()
    {
        return [
            [['client_id','order_number'], 'required'],
            [['client_id'], 'integer'],
            [['order_number','box_barcode','product_barcode','party_number'], 'string'],
            [['box_barcode'], 'validateBoxBarcode'],

            [['client_id','order_number'], 'required','on'=>'ConfirmOrder'],
            [['client_id','order_number','box_barcode'], 'required','on'=>'ClearBox'],
            [['box_barcode'], 'validateClearBox','on'=>'ClearBox'],
            [['client_id','order_number','box_barcode','product_barcode'], 'required','on'=>'ClearProductInBox'],
            [['product_barcode'], 'validateProductInBox','on'=>'ClearProductInBox'],

            [['product_barcode'], 'validateProductBarcode','on'=>'ScannedProduct'],
            [['product_barcode','box_barcode'], 'required','on'=>'ScannedProduct'],
        ];
    }

    /*
     * Проверяет существует ли отсканированный товар в выбранном заказе
     * @param string $productBarcode
     * @param integer $inbound_order_id
     * @return
     * */
    public function checkProductBarcode($productBarcode,$inbound_order_id)
    {
        return InboundOrderItem::find()->where(['product_barcode'=>$productBarcode,'inbound_order_id'=>$inbound_order_id])->exists();
    }

    /*
    * Check exist product in box
    * @param string $productBarcode
    * @return boolean
    * */
    public function checkProductInBox($productBarcode,$box_barcode)
    {
        return Stock::find()->where(['primary_address'=>$box_barcode,'product_barcode'=>$productBarcode,'status'=>[Stock::STATUS_INBOUND_SCANNED,Stock::STATUS_INBOUND_OVER_SCANNED]])->exists();
//        return InboundOrderItemProcess::find()->where(['box_barcode'=>$box_barcode,'product_barcode'=>$productBarcode])->exists();
    }

}