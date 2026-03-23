<?php

namespace common\modules\movement\models;

use Yii;

/**
 * This is the model class for table "movement_pick_list_stock".
 *
 * @property integer $id
 * @property integer $movement_id
 * @property integer $movement_pick_id
 * @property string $product_name
 * @property string $product_barcode
 * @property string $product_model
 * @property string $product_sku
 * @property integer $stock_id
 * @property integer $status
 * @property string $from_box
 * @property string $to_box
 * @property string $from_address
 * @property string $to_address
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class MovementPickListStock extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movement_pick_list_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['movement_id', 'movement_pick_id', 'stock_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_name'], 'string', 'max' => 128],
            [['product_barcode', 'product_model', 'product_sku', 'from_box', 'to_box', 'from_address', 'to_address'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'movement_id' => 'Movement ID',
            'movement_pick_id' => 'Movement Pick ID',
            'product_name' => 'Product Name',
            'product_barcode' => 'Product Barcode',
            'product_model' => 'Product Model',
            'product_sku' => 'Product Sku',
            'stock_id' => 'Stock ID',
            'status' => 'Status',
            'from_box' => 'From Box',
            'to_box' => 'To Box',
            'from_address' => 'From Address',
            'to_address' => 'To Address',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
