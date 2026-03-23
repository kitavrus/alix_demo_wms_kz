<?php

namespace common\modules\inbound\models;

use Yii;

/**
 * This is the model class for table "outbound_order_sync_values".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $outbound_id
 * @property string $outbound_client_id
 * @property integer $status_our
 * @property integer $status_client
 * @property string $zone_our
 * @property string $zone_client
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class OutboundOrderSyncValue extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_order_sync_values';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'outbound_id', 'status_our', 'status_client', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['outbound_client_id'], 'string', 'max' => 128],
            [['zone_our', 'zone_client'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client id'),
            'outbound_id' => Yii::t('app', 'Outbound id'),
            'outbound_client_id' => Yii::t('app', 'Client outbound id'),
            'status_our' => Yii::t('app', 'Status our'),
            'status_client' => Yii::t('app', 'Status client'),
            'zone_our' => Yii::t('app', 'Zone our'),
            'zone_client' => Yii::t('app', 'Zone client'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return OutboundOrderSyncValueQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OutboundOrderSyncValueQuery(get_called_class());
    }
}