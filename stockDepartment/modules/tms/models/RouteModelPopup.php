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

class RouteModelPopup extends Model {

    /*
     * @var string
     * */

    public $agent_id;
    public $car_id;
    public $driver_name;
    public $driver_phone;
    public $driver_auto_number;

    public function attributeLabels()
    {
        return [
            'agent_id' => Yii::t('titles', 'Subcontractor'),
            'car_id' => Yii::t('titles', 'Car'),
            'driver_name' => Yii::t('titles', 'Driver name'),
            'driver_phone' => Yii::t('titles', 'Driver phone'),
            'driver_auto_number' => Yii::t('titles', 'Driver auto number'),
        ];
    }

    public function rules()
    {
        return [
            [['agent_id','car_id','driver_name','driver_phone','driver_auto_number'], 'required'],
            [['agent_id','car_id'], 'integer'],
            [['driver_name','driver_phone','driver_auto_number'], 'string'],
        ];
    }
}