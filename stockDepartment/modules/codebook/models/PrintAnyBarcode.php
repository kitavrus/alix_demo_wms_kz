<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\codebook\models;

use yii\base\Model;
use Yii;

class PrintAnyBarcode extends Model {

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
            [['quantity'], 'integer','max'=>1000],
        ];
    }
}