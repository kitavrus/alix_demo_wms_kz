<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_check_box_stock".
 *
 * @property int $id
 * @property int $check_box_id Outbound id
 * @property int $stock_id Product id
 * @property int $client_id Client id
 * @property int $warehouse_id Warehouse id
 * @property string $inventory_key Inventory key
 * @property string $title Title
 * @property string $box_barcode Box barcode
 * @property string $place_address Place address barcode
 * @property int $stock_inbound_id stock inbound id
 * @property int $stock_inbound_item_id stock inbound item id
 * @property int $stock_outbound_id stock outbound id
 * @property int $stock_outbound_item_id stock outbound item id
 * @property int $stock_status_availability stock status availability
 * @property string $stock_client_product_sku Stock client product sku
 * @property int $stock_inbound_status Status inbound
 * @property int $stock_outbound_status Status outbound
 * @property int $stock_condition_type Condition type
 * @property string $product_barcode Product Barcode
 * @property string $serialized_data_stock Serialized data stock
 * @property int $status Status
 * @property int $scanned_datetime Scanned datetime
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceCheckBoxStock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_check_box_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['check_box_id', 'stock_id', 'client_id', 'warehouse_id', 'stock_inbound_id', 'stock_inbound_item_id', 'stock_outbound_id', 'stock_outbound_item_id', 'stock_status_availability', 'stock_inbound_status', 'stock_outbound_status', 'stock_condition_type', 'status', 'scanned_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['serialized_data_stock'], 'string'],
            [['inventory_key', 'title'], 'string', 'max' => 36],
            [['box_barcode', 'place_address'], 'string', 'max' => 15],
            [['stock_client_product_sku', 'product_barcode'], 'string', 'max' => 14],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_box_id' => 'Check Box ID',
            'stock_id' => 'Stock ID',
            'client_id' => 'Client ID',
            'warehouse_id' => 'Warehouse ID',
            'inventory_key' => 'Inventory Key',
            'title' => 'Title',
            'box_barcode' => 'Box Barcode',
            'place_address' => 'Place Address',
            'stock_inbound_id' => 'Stock Inbound ID',
            'stock_inbound_item_id' => 'Stock Inbound Item ID',
            'stock_outbound_id' => 'Stock Outbound ID',
            'stock_outbound_item_id' => 'Stock Outbound Item ID',
            'stock_status_availability' => 'Stock Status Availability',
            'stock_client_product_sku' => 'Stock Client Product Sku',
            'stock_inbound_status' => 'Stock Inbound Status',
            'stock_outbound_status' => 'Stock Outbound Status',
            'stock_condition_type' => 'Stock Condition Type',
            'product_barcode' => 'Product Barcode',
            'serialized_data_stock' => 'Serialized Data Stock',
            'status' => 'Status',
            'scanned_datetime' => 'Scanned Datetime',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
