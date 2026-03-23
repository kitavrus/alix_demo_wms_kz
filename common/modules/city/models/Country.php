<?php

namespace common\modules\city\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\ActiveRecord;
use dektrium\user\models\User;
use app\modules\transportLogistics\transportLogistics;
/**
 * This is the model class for table "country".
 *
 * @property integer $id
 * @property string $name
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Country extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment'], 'string'],
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
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
            'name' => Yii::t('forms', 'Country name'),
            'comment' => Yii::t('forms', 'Comment'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /*
    * Return array country ['id'=>'country name']
    * @return array Counties
    * */
    public static function getArrayData()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }
}
