<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:27
 */

namespace stockDepartment\modules\outbound\models;


use yii\base\Model;
use Yii;

class DeFactoAPIOutboundForm extends Model {

    /**
     * @var UploadedFile|Null file attribute
     */
    public $file;
    public $invoice_number;
    public $order_number;
    public $parent_order_number;
    public $client_id;

    public function attributeLabels()
    {
        return [
            'client_id' => Yii::t('outbound/forms', 'Client id'),
            'order_number' => Yii::t('outbound/forms', 'Order number'),
            'parent_order_number' => Yii::t('outbound/forms', 'Parent order number'),
            'file' => Yii::t('inbound/forms', 'File csv'),
        ];
    }

    public function rules()
    {
        return [
//            [['file'], 'required','on'=>'UploadFileForAPI'],
            [['invoice_number','client_id'], 'required','on'=>'DownloadFileForAPI'],
            [['order_number','parent_order_number'], 'string'],
            [['file'], 'file', 'extensions' => 'csv','on'=>'UploadFileForAPI'],
            [['client_id'], 'integer'],
        ];
    }
}