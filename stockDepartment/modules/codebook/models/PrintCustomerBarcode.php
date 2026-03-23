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

class PrintCustomerBarcode extends Model {

    public $address;

    public function attributeLabels()
    {
        return [
            'address' => Yii::t('forms', 'Любой штрих-код'),
        ];
    }

    public function rules()
    {
        return [
            [['address'], 'required'],
            [['address'], 'string'],
        ];
    }
}