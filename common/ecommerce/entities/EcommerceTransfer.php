<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_transfer".
 *
 * @property int $id
 * @property int $client_id
 * @property string $client_BatchId Номер партии клиента
 * @property string $client_Status Статус клиента
 * @property string $client_LcBarcode Короб клиента
 * @property int $expected_box_qty
 * @property string $status Статус
 * @property string $api_status API cтатус
 * @property int $expected_qty Expected qty
 * @property int $allocated_qty Allocated qty
 * @property int $accepted_qty Accepted qty
 * @property int $print_picking_list_date Print picking list date
 * @property int $begin_datetime Begin scanning datetime
 * @property int $end_datetime End scanning datetime
 * @property int $packing_date Packing date
 * @property int $date_left_warehouse Date left warehouse
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceTransfer extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_transfer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','expected_qty', 'allocated_qty', 'accepted_qty', 'print_picking_list_date', 'begin_datetime', 'end_datetime', 'packing_date', 'date_left_warehouse', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['client_BatchId', 'client_LcBarcode'], 'string', 'max' => 18],
            [['client_Status', 'status', 'api_status'], 'string', 'max' => 36],
            [['expected_box_qty'], 'integer'],
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
            'client_BatchId' => 'Client  Batch ID',
            'client_Status' => 'Client  Status',
            'client_LcBarcode' => 'Client  Lc Barcode',
            'expected_box_qty' => 'Expected box qty',
            'status' => 'Status',
            'api_status' => 'Api Status',
            'expected_qty' => 'Expected Qty',
            'allocated_qty' => 'Allocated Qty',
            'accepted_qty' => 'Accepted Qty',
            'print_picking_list_date' => 'Print Picking List Date',
            'begin_datetime' => 'Begin Datetime',
            'end_datetime' => 'End Datetime',
            'packing_date' => 'Packing Date',
            'date_left_warehouse' => 'Date Left Warehouse',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
