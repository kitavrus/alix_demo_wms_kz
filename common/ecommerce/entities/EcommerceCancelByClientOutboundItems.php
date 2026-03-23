<?php
namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_cancel_by_client_outbound_items".
 *
 * @property int $id
 * @property int $cancel_by_client_outbound_id
 * @property int $outbound_id
 * @property int $outbound_item_id
 * @property int $stock_id
 * @property int $client_SkuId
 * @property string $product_barcode
 * @property string $old_box_address
 * @property string $old_place_address
 * @property string $new_box_address
 * @property string $status Статус
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceCancelByClientOutboundItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_cancel_by_client_outbound_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cancel_by_client_outbound_id', 'outbound_id', 'outbound_item_id', 'stock_id', 'client_SkuId', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_barcode', 'old_box_address', 'old_place_address', 'new_box_address', 'status'], 'string', 'max' => 36],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cancel_by_client_outbound_id' => 'Cancel By Client Outbound ID',
            'outbound_id' => 'Outbound ID',
            'outbound_item_id' => 'Outbound Item ID',
            'stock_id' => 'Stock ID',
            'client_SkuId' => 'Client  Sku ID',
            'product_barcode' => 'Product Barcode',
            'old_box_address' => 'Old Box Address',
            'old_place_address' => 'Old Place Address',
            'new_box_address' => 'New Box Address',
            'status' => 'Status',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
