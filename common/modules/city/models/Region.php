<?php

namespace common\modules\city\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\ActiveRecord;
use dektrium\user\models\User;
use app\modules\transportLogistics\transportLogistics;
/**
 * This is the model class for table "region".
 *
 * @property integer $id
 * @property integer $country_id
 * @property string $name
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Region extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment'], 'string'],
            [['country_id','created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
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
            'country_id' => Yii::t('forms', 'Country name'),
            'name' => Yii::t('forms', 'Region name'),
            'comment' => Yii::t('forms', 'Comment'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /*
 * Relation has one with user
 * */
    public function getCreatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_user_id']);
    }

    /*
     * Relation has one with user
     * */
    public function getUpdatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_user_id']);
    }

    /*
     * Relation has one with Country
     * */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /*
    * Return array region ['id'=>'region name']
    * @return array Regions
    * */
    public static function getArrayData()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }
}
