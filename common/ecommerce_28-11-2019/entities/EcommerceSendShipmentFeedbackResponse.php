<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_send_shipment_feedback_response".
 *
 * @property int $id
 * @property int $our_outbound_id
 * @property int $send_shipment_feedback_id
 * @property string $IsSuccess
 * @property string $error_message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceSendShipmentFeedbackResponse extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_send_shipment_feedback_response';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['our_outbound_id','send_shipment_feedback_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['IsSuccess'], 'string', 'max' => 64],
            [['error_message'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'our_outbound_id' => Yii::t('app', 'our outbound id'),
            'send_shipment_feedback_id' => Yii::t('app', 'Send Shipment Feedback ID'),
            'IsSuccess' => Yii::t('app', 'Is Success'),
            'error_message' => Yii::t('app', 'Error Message'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
