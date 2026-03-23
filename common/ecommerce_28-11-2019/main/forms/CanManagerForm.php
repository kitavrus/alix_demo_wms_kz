<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */

namespace common\ecommerce\main\forms;

use Yii;
use yii\base\Model;

class CanManagerForm extends Model
{
    public $code;
    //
    public function rules()
    {
        return [
            [['code'], 'required', 'on' => 'onCan'],
            [['code'], 'string', 'on' => 'onCan'],
            [['code'], 'trim', 'on' => 'onCan'],
            [['code'], 'validateCode', 'on' => 'onCan'],
        ];
    }
    //
    public function validateCode($attribute,$params)
    {
        $code = $this->code;
        if ($code != '8888') {
            $this->addError($attribute, '<b>[' . $code . ']</b> ' . Yii::t('inbound/errors', 'Неправильный код'));
        }
    }
    //
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('inbound/forms', 'Введите код'),
        ];
    }
}