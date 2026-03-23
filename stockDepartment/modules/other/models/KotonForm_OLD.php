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

class KotonForm extends Model {

    public $invoice;

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