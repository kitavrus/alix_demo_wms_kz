<?php

namespace app\modules\intermode\controllers\common\apilogs\models;

use Yii;

/**
 * This is the model class for table "api_logs".
 *
 * @property int $id
 * @property int $our_order_id Our in/out/return id
 * @property string $their_order_number Their in/out/return number
 * @property string $method_name Method name
 * @property string $request_status Request Status
 * @property string $order_type in/out/return b2b or b2c
 * @property string $request_data Request data
 * @property string $response_data Response data
 * @property int $response_code Response code
 * @property string $response_message Response error message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class ApiLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['our_order_id', 'response_code', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['request_data', 'response_data', 'response_message'], 'string'],
            [['their_order_number', 'method_name'], 'string', 'max' => 256],
            [['order_type'], 'string', 'max' => 256],
            [['request_status'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'our_order_id' => 'Our Order ID',
            'their_order_number' => 'Their Order Number',
            'method_name' => 'Method Name',
            'order_type' => 'Order Type',
            'request_data' => 'Request Data',
            'response_data' => 'Response Data',
            'response_code' => 'Response Code',
            'response_message' => 'Response Message',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
