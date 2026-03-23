<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\tms\models;

use yii\base\Model;
use Yii;

class SelectSubRouteDefault extends Model
{

    public $sub_default_route_id;
    public $delivery_proposal_id;

    public function attributeLabels()
    {
        return [
            'sub_default_route_id' => Yii::t('titles', 'Выберите маршрут'),
        ];
    }

    public function rules()
    {
        return [
            [['sub_default_route_id'], 'required'],
            [['sub_default_route_id', 'delivery_proposal_id'], 'integer'],
        ];
    }
}