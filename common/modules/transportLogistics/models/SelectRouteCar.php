<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\modules\transportLogistics\models;

use yii\base\Model;
use Yii;

class SelectRouteCar extends Model {

    public $route_car_id;

    public function attributeLabels()
    {
        return [
            'route_car_id' => Yii::t('titles', 'Выберите машину'),
        ];
    }

    public function rules()
    {
        return [
            [['route_car_id'], 'required'],
            [['route_car_id'], 'integer'],
        ];
    }
}