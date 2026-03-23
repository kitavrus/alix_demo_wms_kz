<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_inbound_items".
 *
 * @property int $id
 * @property int $inbound_id Inbound id
 * @property int $product_id Product id
 * @property string $client_box_barcode Короб клиента
 * @property string $client_inbound_id inbound id client
 * @property string $client_lot_sku SKU лота клинта
 * @property string $our_box_barcode Наш короб
 * @property string $lot_barcode Шк лота
 * @property string $product_barcode Шк товара
 * @property int $product_expected_qty Product Expected qty
 * @property int $product_accepted_qty Product Accepted qty
 * @property int $status Status
 * @property int $api_status API Status
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceInboundItem extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_inbound_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_status','inbound_id', 'product_id', 'product_expected_qty', 'product_accepted_qty', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['client_box_barcode', 'client_inbound_id', 'client_lot_sku', 'our_box_barcode', 'lot_barcode', 'product_barcode'], 'string', 'max' => 18],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inbound_id' => 'Inbound ID',
            'product_id' => 'Product ID',
            'client_box_barcode' => 'Client Box Barcode',
            'client_inbound_id' => 'Client Inbound ID',
            'client_lot_sku' => 'Client Lot Sku',
            'our_box_barcode' => 'Our Box Barcode',
            'lot_barcode' => 'Lot Barcode',
            'product_barcode' => 'Product Barcode',
            'product_expected_qty' => 'Product Expected Qty',
            'product_accepted_qty' => 'Product Accepted Qty',
            'status' => 'Status',
            'api_status' => 'API Status',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
