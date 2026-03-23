<?php

namespace common\modules\city\models;


use Yii;
use yii\helpers\ArrayHelper;
use common\models\ActiveRecord;
//use dektrium\user\models\User;
use app\modules\transportLogistics\transportLogistics;
use common\modules\city\models\Region;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property string $name
 * @property integer $region_id
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class City extends ActiveRecord
{
    const DEFAULT_CITY = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['comment'], 'string'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'name' => Yii::t('forms', 'City name'),
            'region_id' => Yii::t('forms', 'Region ID'),
            'comment' => Yii::t('forms', 'Comment'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }


    /*
    * Relation has one with region
    * */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    /*
    * Return array cities ['id'=>'city name']
    * @return array Cities
    * */
    public static function getArrayData()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }
}
