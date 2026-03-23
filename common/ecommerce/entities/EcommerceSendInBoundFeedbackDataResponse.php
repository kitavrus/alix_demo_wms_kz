<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_send_in_bound_feedback_data_response".
 *
 * @property int $id
 * @property int $our_inbound_id
 * @property int $send_inbound_feedback_data_id
 * @property string $IsSuccess
 * @property string $error_message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceSendInBoundFeedbackDataResponse extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_send_inbound_feedback_data_response';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['our_inbound_id', 'send_inbound_feedback_data_id','error_message', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['IsSuccess'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'our_inbound_id' => 'Our Inbound ID',
            'send_inbound_feedback_data_id' => 'Send Inbound Feedback Data ID',
            'IsSuccess' => 'Is Success',
            'error_message' => 'error message',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
