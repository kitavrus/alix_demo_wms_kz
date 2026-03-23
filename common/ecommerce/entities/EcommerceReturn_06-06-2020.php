<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_return".
 *
 * @property int $id
 * @property int $outbound_id Outbound id
 * @property int $client_id Client id
 * @property string $order_number Order number
 * @property int $expected_qty Expected product qty
 * @property int $accepted_qty Accepted product qty
 * @property string $customer_name Customer full name
 * @property string $city city
 * @property string $customer_address Адрес
 * @property string $client_ReferenceNumber Cargo company ReferenceNumber
 * @property int $status Status
 * @property int $begin_datetime Begin scanning datetime
 * @property int $end_datetime End scanning datetime
 * @property int $date_confirm End scanning datetime
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceReturn extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_return';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outbound_id','client_id', 'expected_qty', 'accepted_qty', 'status', 'begin_datetime', 'end_datetime', 'date_confirm', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['order_number'], 'string', 'max' => 36],
            [['customer_name'], 'string', 'max' => 256],
            [['city', 'client_ReferenceNumber'], 'string', 'max' => 128],
            [['customer_address'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'outbound_id' => 'Outbound ID',
            'client_id' => 'Client ID',
            'order_number' => 'Order Number',
            'expected_qty' => 'Expected Qty',
            'accepted_qty' => 'Accepted Qty',
            'customer_name' => 'Customer Name',
            'city' => 'City',
            'customer_address' => 'Customer Address',
            'client_ReferenceNumber' => 'Client  Reference Number',
            'status' => 'Status',
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
