<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:27
 */

namespace app\modules\report\models;

use common\modules\inbound\models\InboundOrder;
use common\modules\stock\models\Stock;
use Yii;

class StockInventoryForm extends \yii\base\Model {

    /**
     * @var UploadedFile|Null file attribute
     */
    public $file;


    public function attributeLabels()
    {
        return [
            'file' => Yii::t('inbound/forms', 'Файл в формате CSV'),
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