<?php

namespace common\modules\outbound\models;

use Yii;

/**
 * This is the model class for table "outbound_boxes".
 *
 * @property int $id
 * @property string $our_box Our box
 * @property string $client_box Client box
 * @property string $client_extra_json Client extra json
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class OutboundBox extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_boxes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['our_box', 'client_box'], 'string', 'max' => 15],
            [['client_extra_json'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'our_box' => Yii::t('app', 'Our Box'),
            'client_box' => Yii::t('app', 'Client Box'),
            'client_extra_json' => Yii::t('app', 'Client extra Json'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}