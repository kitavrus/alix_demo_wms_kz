<?php

namespace common\modules\city\models;

use common\modules\city\RouteDirection\constant\Constant;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "route_directions".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 * @property integer $base_type
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class RouteDirections extends \common\models\ActiveRecord
{
    const BASE_TYPE_ROUTE = 1; // базовый
    const BASE_TYPE_CUSTOM = 2; // Для поиска

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'route_directions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status','base_type'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Название'),
            'base_type' => Yii::t('app', 'Тип'),
            'status' => Yii::t('app', 'Статус'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /*
     *
     * */
    public function getLinkedCity()
    {
        return $this->hasMany(RouteDirectionToCity::className(), ['route_direction_id' => 'id']);
    }
    /*
    * Return array data ['id'=>'name']
    * @return array Cities
    * */
    public static function getArrayData()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }


    /*
     *
     * */
    public function getCityIDs()
    {
        return self::getLinkedCity()->select('city_id')->asArray()->column();
    }

    /*
    * Return array data []
    * @return array BASE TYPE
    * */
    public static function getTypeArrayData()
    {
        return Constant::getTypeArrayData();
    }

    /*
     *
     * */
    public function getValueBaseType($base_type = null)
    {
        if(is_null($base_type)){
            $base_type = $this->base_type;
        }
        return ArrayHelper::getValue(Constant::getTypeArrayData(), $base_type);
    }
}