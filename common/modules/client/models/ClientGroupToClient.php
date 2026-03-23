<?php

namespace common\modules\client\models;

use Yii;

/**
 * This is the model class for table "client_group_to_client".
 *
 * @property integer $id
 * @property integer $client_group_id
 * @property integer $client_id
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ClientGroupToClient extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_group_to_client';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_group_id', 'client_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_group_id' => Yii::t('app', 'Client group id'),
            'client_id' => Yii::t('app', 'Client id'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return ClientGroupToClientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientGroupToClientQuery(get_called_class());
    }
}
