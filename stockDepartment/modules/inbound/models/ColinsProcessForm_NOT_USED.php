<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:27
 */

namespace app\modules\inbound\models;

use common\modules\inbound\models\InboundOrder;
use common\modules\stock\models\Stock;
use Yii;

class ColinsProcessForm extends \yii\base\Model {

    /**
     * @var UploadedFile|Null file attribute
     */
    public $file_1;
    public $file_2;


    public function attributeLabels()
    {
        return [
            'file_1' => Yii::t('inbound/forms', 'Файл 1 в формате CSV'),
            'file_2' => Yii::t('inbound/forms', 'Файл 2 в формате CSV'),
        ];
    }

    public function rules()
    {
        return [
            [['file_1', 'file_2'], 'file', 'checkExtensionByMimeType' => false, 'extensions' => 'csv'],
        ];
    }

}