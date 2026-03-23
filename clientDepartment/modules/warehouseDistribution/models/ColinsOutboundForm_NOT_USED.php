<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:27
 */

namespace app\modules\warehouseDistribution\models;

use common\modules\inbound\models\InboundOrder;
use common\modules\stock\models\Stock;
use Yii;

class ColinsOutboundForm extends \yii\base\Model {


    public $file;


    public function attributeLabels()
    {
        return [
            'file' => Yii::t('inbound/titles', 'Colins distribution file CSV'),
        ];
    }

    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'checkExtensionByMimeType' => false, 'extensions' => 'csv'],
        ];
    }

}