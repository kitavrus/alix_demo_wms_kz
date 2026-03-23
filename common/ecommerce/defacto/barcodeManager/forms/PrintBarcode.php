<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 11.09.2019
 * Time: 17:27
 */
namespace common\ecommerce\defacto\barcodeManager\forms;

use yii\base\Model;
use Yii;

class PrintBarcode extends Model
{
    public $codebook_id;
    public $quantity;

    public function attributeLabels()
    {
        return [
            'codebook_id' => Yii::t('forms', 'Code book type'),
            'quantity' => Yii::t('forms', 'Quantity'),
        ];
    }

    public function rules()
    {
        return [
            [['codebook_id','quantity'], 'required'],
            [['codebook_id','quantity'], 'integer'],
            [['quantity'], 'integer','max'=>100],
        ];
    }
}