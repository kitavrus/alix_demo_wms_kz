<?php

namespace common\modules\city\models;

use Yii;

/**
 * This is the model class for table "route_direction_to_city".
 *
 * @property integer $id
 * @property integer $route_direction_id
 * @property integer $city_id
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class RouteDirectionToCity extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'route_direction_to_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['route_direction_id', 'city_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'route_direction_id' => Yii::t('app', 'Direction id'),
            'city_id' => Yii::t('app', 'City id'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}