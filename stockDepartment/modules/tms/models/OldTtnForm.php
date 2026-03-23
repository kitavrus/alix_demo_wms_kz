<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 10.08.2019
 * Time: 13:27
 */

namespace stockDepartment\modules\tms\models;


use yii\base\Model;

class OldTtnForm extends Model
{
    public $ttn;

    public function attributeLabels()
    {
        return [
            'ttn' =>'TTN',
        ];
    }

    public function rules()
    {
        return [
            [['ttn'], 'required'],
            [['ttn'], 'integer'],
        ];
    }
}