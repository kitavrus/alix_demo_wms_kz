<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_transfer_items".
 *
 * @property int $id
 * @property string $client_BatchId
 * @property string $client_OutboundId
 * @property string $client_SkuId SKU ID 
 * @property int $client_Quantity
 * @property string $client_Status
 * @property string $status
 * @property string $api_status API c
 * @property string $product_sku Product sku
 * @property string $product_name Product name
 * @property string $product_model Product model
 * @property string $product_barcode Product Barcode
 * @property int $begin_datetime Begin datetime
 * @property int $end_datetime End datetime
 * @property int $expected_qty Expected qty
 * @property int $allocated_qty Allocated qty
 * @property int $accepted_qty Accepted qty
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceTransferItems extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_transfer_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_Quantity', 'begin_datetime', 'end_datetime', 'expected_qty', 'allocated_qty', 'accepted_qty', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['client_BatchId', 'client_OutboundId', 'client_SkuId', 'product_barcode'], 'string', 'max' => 18],
            [['client_Status', 'status', 'api_status'], 'string', 'max' => 36],
            [['product_sku', 'product_name', 'product_model'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_BatchId' => 'Client  Batch ID',
            'client_OutboundId' => 'Client  Outbound ID',
            'client_SkuId' => 'Client  Sku ID',
            'client_Quantity' => 'Client  Quantity',
            'client_Status' => 'Client  Status',
            'status' => 'Status',
            'api_status' => 'Api Status',
            'product_sku' => 'Product Sku',
            'product_name' => 'Product Name',
            'product_model' => 'Product Model',
            'product_barcode' => 'Product Barcode',
            'begin_datetime' => 'Begin Datetime',
            'end_datetime' => 'End Datetime',
            'expected_qty' => 'Expected Qty',
            'allocated_qty' => 'Allocated Qty',
            'accepted_qty' => 'Accepted Qty',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
