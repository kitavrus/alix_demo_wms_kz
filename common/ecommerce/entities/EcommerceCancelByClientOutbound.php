<?php
namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_cancel_by_client_outbound".
 *
 * @property int $id
 * @property int $client_id
 * @property int $outbound_id
 * @property string $cancel_key
 * @property string $order_number
 * @property string $outbound_box
 * @property string $client_OrderSource
 * @property string $new_box_address
 * @property string $status Статус
 * @property string $api_status API cтатус
 * @property int $expected_qty Expected qty
 * @property int $accepted_qty Accepted qty
 * @property int $begin_datetime Begin scanning datetime
 * @property int $end_datetime End scanning datetime
 * @property int $date_confirm Confirm datetime
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceCancelByClientOutbound extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_cancel_by_client_outbound';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'outbound_id', 'expected_qty', 'accepted_qty', 'begin_datetime', 'end_datetime', 'date_confirm', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['cancel_key', 'order_number', 'outbound_box', 'client_OrderSource', 'new_box_address', 'status', 'api_status'], 'string', 'max' => 36],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'outbound_id' => 'Outbound ID',
            'cancel_key' => 'Cancel Key',
            'order_number' => 'Order Number',
            'outbound_box' => 'Outbound Box',
            'client_OrderSource' => 'Client  Order Source',
            'new_box_address' => 'New Box Address',
            'status' => 'Status',
            'api_status' => 'Api Status',
            'expected_qty' => 'Expected Qty',
            'accepted_qty' => 'Accepted Qty',
            'begin_datetime' => 'Begin Datetime',
            'end_datetime' => 'End Datetime',
            'date_confirm' => 'Date Confirm',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
