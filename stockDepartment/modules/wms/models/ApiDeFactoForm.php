<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\wms\models;

use common\components\BarcodeManager;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
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
     * */
    public function rules()
    {
        return [
            [['invoice'], 'string'],
            [['invoice'], 'trim'],

            [['invoice'],'required', 'on'=>'Inbound'],
            [['invoice'],'validateInbound', 'on'=>'Inbound'],

            [['invoice'],'required', 'on'=>'Outbound'],
            [['invoice'],'validateOutbound', 'on'=>'Outbound'],

            [['invoice'],'required', 'on'=>'CrossDock'],
        ];
    }

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
    * Validate outbound order
    * */
    public function validateOutbound($attribute, $params)
    {
        $value = $this->$attribute;
        $client_id = 2;
        if(OutboundOrder::find()->where(['client_id'=>$client_id,'parent_order_number'=>$value])->andWhere(['not in','status',[Stock::STATUS_OUTBOUND_NEW]])->exists()) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('other/api-de-facto/errors','The invoice is accepted, it cannot load in system be re-pour'));
        }
    }

    /*
     * */
    public function attributeLabels()
    {
        return [
            'invoice' => Yii::t('other/api-de-facto/forms', 'Invoice'),
        ];
    }
}