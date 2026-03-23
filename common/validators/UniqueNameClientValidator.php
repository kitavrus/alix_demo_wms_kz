<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 27.10.14
 * Time: 12:16
 */

namespace common\validators;

use yii\validators\Validator;
use common\modules\user\models\User;

class UniqueNameClientValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;

        $q = User::find();
        $q->where('username = :username',[':username'=>$value]);

        if(!$model->isNewRecord && !empty($model->user_id) ) {
            $q->andWhere('id != :id',[':id'=>$model->user_id]);
        }

        if($q->count()) {
            $this->addError($model,$attribute, '['.$value.'] Пользователь с таким логином уже существует');
        }
    }
}