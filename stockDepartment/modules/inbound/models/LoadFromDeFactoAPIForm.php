<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:27
 */

namespace stockDepartment\modules\inbound\models;

use common\modules\inbound\models\InboundOrder;
use common\modules\stock\models\Stock;
use Yii;

class LoadFromDeFactoAPIForm extends \yii\base\Model {

    /**
     * @var UploadedFile|Null file attribute
     */
    public $file;
    public $invoice_number;
    public $client_id;

    public function attributeLabels()
    {
        return [
            'client_id' => Yii::t('inbound/forms', 'Client id'),
            'invoice_number' => Yii::t('inbound/forms', 'Invoice number'),
            'file' => Yii::t('inbound/forms', 'File csv'),
        ];
    }

    public function rules()
    {
        return [
            [['invoice_number'], 'required','on'=>'UploadFileForAPI'],
            [['invoice_number','client_id'], 'required','on'=>'DownloadFileForAPI'],
            [['invoice_number'], 'string'],
            [['client_id'], 'integer'],
//            [['invoice_number'], 'unique','targetAttribute'=>['client_id']],
            [['invoice_number'], 'validateInvoiceNumber'],
            [['file'], 'file', 'extensions' => 'csv'],
        ];
    }

    /*
    * Validate exist and unique
    *
    * */
    public function validateInvoiceNumber($attribute, $params)
    {
        $value = $this->$attribute;

        if(InboundOrder::find()->where(['order_number'=>$value,'client_id'=>2])->andWhere(['not in','status',Stock::STATUS_INBOUND_NEW])->exists()) {
            $this->addError($attribute, ' [ ' . $value . ' ] '.Yii::t('inbound/errors','Эта накладная уже добавлена в систему')); // Этого товара нет в укзанном коробе
        }
    }
}