<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:27
 */

namespace app\modules\inbound\models;
use Yii;

class KotonReturnForm extends \yii\base\Model {


    public $product_barcode;
    public $client_id;
    public $inbound_order_number;
    public $accepted_qty;


    public function attributeLabels()
    {
        return [
            'product_barcode' => Yii::t('inbound/forms', 'Product Barcode'),
            'client_id' => Yii::t('forms', 'Client ID'),
        ];
    }

    public function rules()
    {
        return [
            [['client_id', 'product_barcode'], 'required'],
            [['client_id', 'product_barcode', 'accepted_qty'], 'integer'],
            [['inbound_order_number'], 'safe'],
        ];
    }

}