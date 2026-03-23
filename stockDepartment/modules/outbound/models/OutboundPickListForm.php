<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\outbound\models;

use yii\base\Model;
use Yii;
use common\modules\codebook\models\Codebook;
use common\modules\stock\models\Stock;


class OutboundPickListForm extends Model {

    public $client_id;
    public $parent_order_number;
    public $order_number;
//    public $product_barcode;
//    public $box_barcode;

    /*
    * Validate box_barcode
    * */
//    public function validateBoxBarcode($attribute, $params)
//    {
//        $value = $this->$attribute;
//        if(!Codebook::isBox($value)) {
//            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','Invalid box barcode. Box barcode first letter must be b'));
//        }
//    }

    /*
     * Validate product_barcode
     * */
//    public function validateProductBarcode($attribute, $params)
//    {
//        $value = $this->$attribute;
//        if(!self::checkProductBarcode($value)) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Scanned product barcode not found in selected inbound'));
//        }
//    }

    /*
     * Remove product in box
     *
     * */
//    public function validateProductInBox($attribute, $params)
//    {
//        $value = $this->$attribute;
//        $box_barcode = $this->box_barcode;
//        if(!self::checkProductInBox($value,$box_barcode)) {
//            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> '.Yii::t('inbound/errors','Box is empty')); // Этого товара нет в укзанном коробе
//        }
//    }

    public function attributeLabels()
    {
        return [
            'client_id' => Yii::t('outbound/forms', 'Client ID'),
            'parent_order_number' => Yii::t('outbound/forms', 'Parent order number'),
            'order_number' => Yii::t('outbound/forms', 'Order number'),
        ];
    }

    public function rules()
    {
        return [
            [['client_id','parent_order_number'], 'required'],
            [['client_id'], 'integer'],
            [['parent_order_number'], 'string'],

//            [['order_number','box_barcode','product_barcode'], 'string'],
//            [['box_barcode'], 'validateBoxBarcode'],
//            [['product_barcode'], 'validateProductBarcode'],
//            [['product_barcode','box_barcode'], 'required','on'=>'ScannedBox'],
//            [['client_id','order_number'], 'required','on'=>'ConfirmOrder'],
//            [['client_id','order_number','box_barcode'], 'required','on'=>'ClearBox'],
//            [['client_id','order_number','box_barcode','product_barcode'], 'required','on'=>'ClearProductInBox'],
//            [['product_barcode'], 'validateProductInBox','on'=>'ClearProductInBox'],
//            [['product_barcode','box_barcode'], 'required','on'=>'ScannedBox'],
        ];
    }

    /*
     * Проверяет существует ли отсканированный товар в выбранном заказе
     * @param string $productBarcode
     * @return
     * */
//    public function checkProductBarcode($productBarcode)
//    {
//        return InboundOrderItem::find()->where(['product_barcode'=>$productBarcode])->exists();
//    }

    /*
    * Check exist product in box
    * @param string $productBarcode
    * @return boolean
    * */
//    public function checkProductInBox($productBarcode,$box_barcode)
//    {
//        return Stock::find()->where(['primary_address'=>$box_barcode,'product_barcode'=>$productBarcode,'status'=>Stock::STATUS_INBOUND_SCANNED])->exists();
////        return InboundOrderItemProcess::find()->where(['box_barcode'=>$box_barcode,'product_barcode'=>$productBarcode])->exists();
//    }

}