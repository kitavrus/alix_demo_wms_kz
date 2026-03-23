<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_api_outbound_log".
 *
 * @property int $id
 * @property int $our_outbound_id Our outbound id
 * @property int $our_outbound_item_id Our outbound item id
 * @property string $method_name Method name
 * @property int $request_is_success Response is success
 * @property int $response_is_success Response is success
 * @property string $request_data Request data
 * @property string $response_data Response data
 * @property string $request_error_message Request error message
 * @property string $response_error_message Response error message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceApiOutboundLog extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_api_outbound_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['our_outbound_id', 'our_outbound_item_id', 'request_is_success', 'response_is_success', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['request_data', 'response_data', 'request_error_message', 'response_error_message'], 'string'],
            [['method_name'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'our_outbound_id' => 'Our Outbound ID',
            'our_outbound_item_id' => 'Our Outbound Item ID',
            'method_name' => 'Method Name',
            'request_is_success' => 'Request Is Success',
            'response_is_success' => 'Response Is Success',
            'request_data' => 'Request Data',
            'response_data' => 'Response Data',
            'request_error_message' => 'Request Error Message',
            'response_error_message' => 'Response Error Message',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
