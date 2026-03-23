<?php

namespace stockDepartment\modules\wms\managers\erenRetail\checkBox\entities;

use Yii;

/**
 * This is the model class for table "check_box_stock".
 *
 * @property int $id
 * @property int $inventory_id
 * @property int $check_box_id Outbound id
 * @property int $stock_id Product id
 * @property int $client_id Client id
 * @property int $warehouse_id Warehouse id
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
 * @property string $stock_transfer_id
 * @property string $stock_status_transfer
 * @property string $stock_transfer_outbound_box
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
class CheckBoxStock extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'check_box_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inventory_id', 'check_box_id', 'stock_id', 'client_id', 'warehouse_id', 'stock_inbound_id', 'stock_inbound_item_id', 'stock_outbound_id', 'stock_outbound_item_id', 'stock_status_availability', 'stock_inbound_status', 'stock_outbound_status', 'stock_condition_type', 'status', 'scanned_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['serialized_data_stock'], 'string'],
            [['box_barcode', 'place_address'], 'string', 'max' => 15],
            [['stock_client_product_sku', 'product_barcode'], 'string', 'max' => 14],
            [['stock_transfer_id', 'stock_status_transfer', 'stock_transfer_outbound_box'], 'string', 'max' => 18],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'inventory_id' => Yii::t('app', 'Inventory ID'),
            'check_box_id' => Yii::t('app', 'Outbound id'),
            'stock_id' => Yii::t('app', 'Product id'),
            'client_id' => Yii::t('app', 'Client id'),
            'warehouse_id' => Yii::t('app', 'Warehouse id'),
            'box_barcode' => Yii::t('app', 'Box barcode'),
            'place_address' => Yii::t('app', 'Place address barcode'),
            'stock_inbound_id' => Yii::t('app', 'stock inbound id'),
            'stock_inbound_item_id' => Yii::t('app', 'stock inbound item id'),
            'stock_outbound_id' => Yii::t('app', 'stock outbound id'),
            'stock_outbound_item_id' => Yii::t('app', 'stock outbound item id'),
            'stock_status_availability' => Yii::t('app', 'stock status availability'),
            'stock_client_product_sku' => Yii::t('app', 'Stock client product sku'),
            'stock_inbound_status' => Yii::t('app', 'Status inbound'),
            'stock_outbound_status' => Yii::t('app', 'Status outbound'),
            'stock_condition_type' => Yii::t('app', 'Condition type'),
            'stock_transfer_id' => Yii::t('app', 'Stock Transfer ID'),
            'stock_status_transfer' => Yii::t('app', 'Stock Status Transfer'),
            'stock_transfer_outbound_box' => Yii::t('app', 'Stock Transfer Outbound Box'),
            'product_barcode' => Yii::t('app', 'Product Barcode'),
            'serialized_data_stock' => Yii::t('app', 'Serialized data stock'),
            'status' => Yii::t('app', 'Status'),
            'scanned_datetime' => Yii::t('app', 'Scanned datetime'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
