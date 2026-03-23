<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_stock".
 *
 * @property int $id
 * @property int $client_id Client id
 * @property int $warehouse_id Warehouse id
 * @property int $scan_in_employee_id Scan inbound employee id
 * @property int $scan_out_employee_id Scan outbound employee id
 * @property int $inbound_id Inbound id
 * @property int $inbound_item_id Inbound item id
 * @property int $outbound_id Outbound id
 * @property int $outbound_item_id Outbound item id
 * @property string $client_box_barcode Шк приходного короба клиента
 * @property string $client_inbound_id inbound id client
 * @property string $client_lot_sku SKU лота клинта
 * @property string $lot_barcode Шк лота
 * @property string $product_barcode Шк товара
 * @property string $box_address_barcode Адрес короба
 * @property string $place_address_barcode Адрес полки
 * @property int $place_address_sort1 Адрес полки для сортировки
 * @property int $place_address_sort2 Адрес полки для сортировки
 * @property int $place_address_sort3 Адрес полки для сортировки
 * @property int $place_address_sort4 Адрес полки для сортировки
 * @property string $outbound_box Шк короба в котором отгружаем
 * @property int $status Status
 * @property int $status_inbound Status inbound
 * @property int $status_outbound Status outbound
 * @property int $api_status  API Status
 * @property int $status_availability Доступен для резервировани или нет
 * @property int $condition_type Состояние товара: норм, брак, частичный брак
 * @property int $product_id Product id
 * @property string $product_sku Product sku
 * @property string $product_name Product name
 * @property string $product_model Product model
 * @property string $product_price Product price
 * @property int $scan_out_datetime Scan outbound datetime
 * @property int $scan_in_datetime Scan inbound datetime
 * @property int $scan_reserved_datetime Reserved inbound datetime
 * @property int $address_sort_order
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceStock extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_status','address_sort_order','client_id', 'warehouse_id', 'scan_in_employee_id', 'scan_out_employee_id', 'inbound_id', 'inbound_item_id', 'outbound_id', 'outbound_item_id', 'status', 'status_inbound', 'status_outbound', 'status_availability', 'condition_type', 'product_id', 'scan_out_datetime', 'scan_in_datetime', 'scan_reserved_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['client_box_barcode', 'client_inbound_id', 'client_lot_sku', 'lot_barcode', 'box_address_barcode', 'place_address_barcode', 'outbound_box'], 'string', 'max' => 18],
            [['product_barcode', 'product_sku', 'product_name', 'product_model'], 'string', 'max' => 64],
            [['product_price'], 'string', 'max' => 11],
            [['place_address_sort1','place_address_sort2','place_address_sort3','place_address_sort4'], 'integer'],
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
            'warehouse_id' => 'Warehouse ID',
            'scan_in_employee_id' => 'Scan In Employee ID',
            'scan_out_employee_id' => 'Scan Out Employee ID',
            'inbound_id' => 'Inbound ID',
            'inbound_item_id' => 'Inbound Item ID',
            'outbound_id' => 'Outbound ID',
            'outbound_item_id' => 'Outbound Item ID',
            'client_box_barcode' => 'Client Box Barcode',
            'client_inbound_id' => 'Client Inbound ID',
            'client_lot_sku' => 'Client Lot Sku',
            'lot_barcode' => 'Lot Barcode',
            'product_barcode' => 'Product Barcode',
            'box_address_barcode' => 'Box Address Barcode',
            'place_address_barcode' => 'Place Address Barcode',
            'outbound_box' => 'Outbound Box',
            'status' => 'Status',
            'status_inbound' => 'Status Inbound',
            'status_outbound' => 'Status Outbound',
            'api_status' => 'API Status',
            'status_availability' => 'Status Availability',
            'condition_type' => 'Condition Type',
            'product_id' => 'Product ID',
            'product_sku' => 'Product Sku',
            'product_name' => 'Product Name',
            'product_model' => 'Product Model',
            'product_price' => 'Product Price',
            'scan_out_datetime' => 'Scan Out Datetime',
            'scan_in_datetime' => 'Scan In Datetime',
            'scan_reserved_datetime' => 'Scan Reserved Datetime',
            'address_sort_order' => 'Address sort order',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
