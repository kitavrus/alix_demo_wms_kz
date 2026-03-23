<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace app\modules\returnOrder\models;

use common\components\BarcodeManager;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use yii\base\Model;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class ReturnForm extends Model {

    public $box_barcode;
    public $order_number;
    public $product_barcode;
    public $new_return_order_id;

    /*
    * Validate box_barcode
    * */
    public function validateBoxBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isBox($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('return/errors','Invalid box barcode. Box barcode first letter must be B or 70'));
        }
    }

    /*
    * Validate box_barcode
    * */
    public function validateOrderNumber($attribute, $params)
    {
        $client_id = 2;
        $value = $this->$attribute;
        if($r = ReturnOrder::find()->where(['order_number'=>$value,'status'=>ReturnOrder::STATUS_COMPLETE,'client_id'=>$client_id])->one() ) {
            $message = '';
            if(!empty($r->extra_fields)) {
                $jsonData = Json::decode($r->extra_fields);
                $boxBarcode = isset($jsonData['boxBarcode']) ? $jsonData['boxBarcode'] : '';
                $message = Yii::t('return/errors',' Прикрепите отсканированный короб к коробоу с ШК №')."<b style='font-size:25px;'>".$boxBarcode."</b>";
            }

            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('return/errors','Этот короб уже принят на склад,').$message);
        }
    }

    /*
     *
     *
     * */
    public function attributeLabels()
    {
        return [
            'box_barcode' => Yii::t('return/forms', 'Box barcode'),
            'order_number' => Yii::t('return/forms', 'Order number'),
            'new_return_order_id' => Yii::t('return/forms', 'New return order id'),
        ];
    }

    /*
     *
     *
     * */
    public function rules()
    {
        return [
              [['new_return_order_id'], 'integer'],
              [['box_barcode','order_number'], 'string'],
              [['box_barcode','order_number'], 'trim'],
              [['box_barcode'],'required', 'on'=>'BoxBarcode'],
              [['box_barcode'], 'validateBoxBarcode','on'=>'BoxBarcode'],
              [['order_number'], 'validateOrderNumber','on'=>'OrderNumber'],
        ];
    }
}