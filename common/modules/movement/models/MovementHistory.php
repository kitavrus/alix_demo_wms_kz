<?php

namespace common\modules\movement\models;

use Yii;

/**
 * This is the model class for table "movement_history".
 *
 * @property integer $id
 * @property string $client_order_id
 * @property integer $client_id
 * @property integer $stock_id
 * @property integer $inbound_id
 * @property integer $movement_id
 * @property integer $outbound_id
 * @property integer $from_zone_id
 * @property integer $to_zone_id
 * @property string $product_barcode
 * @property string $product_model
 * @property string $product_sku
 * @property integer $product_qty
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class MovementHistory extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movement_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outbound_id','client_id', 'stock_id', 'inbound_id', 'movement_id', 'from_zone_id', 'to_zone_id', 'product_qty', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['client_order_id', 'product_barcode', 'product_model', 'product_sku'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_order_id' => 'Client Order ID',
            'client_id' => 'Client ID',
            'stock_id' => 'Stock ID',
            'inbound_id' => 'Inbound ID',
            'movement_id' => 'Movement ID',
            'from_zone_id' => 'From Zone ID',
            'to_zone_id' => 'To Zone ID',
            'product_barcode' => 'Product Barcode',
            'product_model' => 'Product Model',
            'product_sku' => 'Product Sku',
            'product_qty' => 'Product Qty',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
