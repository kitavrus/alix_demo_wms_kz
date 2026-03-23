<?php

namespace common\modules\broker\models;

use Yii;
use common\models\ActiveRecord;

/**
 * This is the model class for table "brokers".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property string $title
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $phone
 * @property string $phone_mobile
 * @property string $email
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class Broker extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brokers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['username', 'title'], 'string', 'max' => 128],
            [['first_name', 'middle_name', 'last_name', 'phone', 'phone_mobile', 'email'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'username' => Yii::t('app', 'Username'),
            'title' => Yii::t('app', 'Title'),
            'first_name' => Yii::t('app', 'First name'),
            'middle_name' => Yii::t('app', 'Middle name'),
            'last_name' => Yii::t('app', 'Last name'),
            'phone' => Yii::t('app', 'Phone'),
            'phone_mobile' => Yii::t('app', 'Phone mobile'),
            'email' => Yii::t('app', 'email'),
            'status' => Yii::t('app', 'Status'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
