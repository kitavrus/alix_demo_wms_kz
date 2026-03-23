<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\other\models;

use common\components\BarcodeManager;
use common\modules\inbound\models\InboundOrder;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class ApiDeFactoForm extends Model {

    public $invoice;

    /*
    * Validate inbound order
    * */
    public function validateInbound($attribute, $params)
    {
        $value = $this->$attribute;
        $client_id = 2;
        if(InboundOrder::find()->where(['client_id'=>$client_id,'order_number'=>$value])->andWhere(['not in','status',[Stock::STATUS_INBOUND_NEW]])->exists()) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('other/api-de-facto/errors','The invoice is accepted, it cannot load in system be re-pour'));
        }
    }

    /*
    * Validate box_barcode
    * */
//    public function validateOrderNumber($attribute, $params)
//    {
//        $client_id = 2;
//        $value = $this->$attribute;
//        if($r = ReturnOrder::find()->where(['order_number'=>$value,'status'=>ReturnOrder::STATUS_COMPLETE,'client_id'=>$client_id])->one() ) {
//            $message = '';
//            if(!empty($r->extra_fields)) {
//                $jsonData = Json::decode($r->extra_fields);
//                $boxBarcode = isset($jsonData['boxBarcode']) ? $jsonData['boxBarcode'] : '';
//                $message = Yii::t('return/errors',' Прикрепите отсканированный короб к коробоу с ШК №')."<b style='font-size:25px;'>".$boxBarcode."</b>";
//            }
//
//            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('return/errors','Этот короб уже принят на склад,').$message);
//        }
//    }

    /*
     *
     *
     * */
    public function attributeLabels()
    {
        return [
            'invoice' => Yii::t('other/api-de-facto/forms', 'Invoice'),
        ];
    }

    /*
     *
     *
     * */
    public function rules()
    {
        return [
              [['invoice'], 'string'],
              [['invoice'], 'trim'],
              [['invoice'],'required', 'on'=>'Inbound'],
              [['invoice'],'validateInbound', 'on'=>'Inbound'],
              [['invoice'],'required', 'on'=>'Outbound'],

              [['invoice'],'required', 'on'=>'CrossDock'],
        ];
    }
}