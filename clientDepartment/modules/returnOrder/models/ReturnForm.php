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

    public $store_id;
    public $inbound_order_number;
    public $file;


    /*
     *
     *
     * */
    public function attributeLabels()
    {
        return [
            'store_id' => Yii::t('return/forms', 'Store'),
            'inbound_order_number' => Yii::t('return/forms', 'Inbound order number'),
            'file' => Yii::t('return/forms', 'File'),
        ];
    }

    /*
     *
     *
     * */
    public function rules()
    {
        return [
            [['file','store_id','inbound_order_number'], 'required'],
            [['store_id', 'inbound_order_number'], 'integer'],
            [['file'], 'file', 'maxFiles' => 400],
        ];
    }
}