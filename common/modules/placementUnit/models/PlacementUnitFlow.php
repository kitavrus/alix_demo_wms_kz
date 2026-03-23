<?php

namespace common\modules\placementUnit\models;

use Yii;
use common\models\ActiveRecord;
/**
 * This is the model class for table "placement_unit_flow".
 *
 * @property integer $id
 * @property integer $count_unit
 * @property integer $client_id
 * @property integer $stock_id
 * @property integer $zone_id
 * @property integer $inbound_order_id
 * @property integer $inbound_order_item_id
 * @property integer $outbound_order_id
 * @property integer $outbound_order_item_id
 * @property integer $placement_unit_barcode_id
 * @property string $placement_unit_barcode
 * @property integer $product_id
 * @property string $product_barcode
 * @property string $product_model
 * @property string $product_name
 * @property string $product_sku
 * @property integer $product_qty
 * @property integer $status
 * @property string $to_rack_address
 * @property string $to_pallet_address
 * @property string $to_box_address
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class PlacementUnitFlow extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'placement_unit_flow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['count_unit', 'client_id', 'stock_id', 'zone_id', 'inbound_order_id', 'inbound_order_item_id', 'outbound_order_id', 'outbound_order_item_id', 'placement_unit_barcode_id', 'product_id', 'product_qty', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['placement_unit_barcode', 'product_barcode', 'to_rack_address', 'to_pallet_address', 'to_box_address'], 'string', 'max' => 23],
            [['product_model', 'product_sku'], 'string', 'max' => 64],
            [['product_name'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'count_unit' => Yii::t('app', 'Count unit'),
            'client_id' => Yii::t('app', 'client id'),
            'stock_id' => Yii::t('app', 'Stock id'),
            'zone_id' => Yii::t('app', 'Zone id'),
            'inbound_order_id' => Yii::t('app', 'Inbound order id'),
            'inbound_order_item_id' => Yii::t('app', 'Inbound order item id'),
            'outbound_order_id' => Yii::t('app', 'Outbound order id'),
            'outbound_order_item_id' => Yii::t('app', 'Outbound order item id'),
            'placement_unit_barcode_id' => Yii::t('app', 'Placement unit id'),
            'placement_unit_barcode' => Yii::t('app', 'Placement unit barcode'),
            'product_id' => Yii::t('app', 'Product id'),
            'product_barcode' => Yii::t('app', 'Product barcode'),
            'product_model' => Yii::t('app', 'Product model'),
            'product_name' => Yii::t('app', 'Product name'),
            'product_sku' => Yii::t('app', 'Product sku'),
            'product_qty' => Yii::t('app', 'Product quantity'),
            'status' => Yii::t('app', 'Status: free, work, close'),
            'to_rack_address' => Yii::t('app', 'To rack address barcode'),
            'to_pallet_address' => Yii::t('app', 'To pallet address barcode'),
            'to_box_address' => Yii::t('app', 'To box address barcode'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
