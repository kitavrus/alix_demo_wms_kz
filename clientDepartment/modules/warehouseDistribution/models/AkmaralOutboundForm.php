<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace app\modules\warehouseDistribution\models;

use common\components\BarcodeManager;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use yii\base\Model;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class AkmaralOutboundForm extends Model {

    public $from_point;
    public $to_point;
    public $inbound_order_number;
    public $file;

    const FILE_COLUMN_QTY = 11;

    /*
     *
     *
     * */
    public function attributeLabels()
    {
        return [
            'from_point' => Yii::t('forms', 'From Point'),
            'to_point' => Yii::t('forms', 'To Point'),
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
            [['from_point', 'to_point'], 'required'],
            [['from_point', 'to_point'], 'integer'],
            [['file'], 'file', 'checkExtensionByMimeType' => false, 'extensions' => 'csv'],
        ];
    }
}