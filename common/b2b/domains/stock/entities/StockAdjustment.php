<?php

namespace common\b2b\domains\stock\entities;

use Yii;

/**
 * This is the model class for table "stock_adjustment".
 *
 * @property int $id
 * @property string $product_barcode Шк товара
 * @property int $product_quantity Количество
 * @property string $product_operator Оператор +-
 * @property string $reason Причина
 * @property string $address_box_barcode Причина
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class StockAdjustment extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_adjustment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_quantity', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['reason'], 'string'],
            [['address_box_barcode'], 'string'],
            [['product_barcode'], 'string', 'max' => 16],
            [['product_operator'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_box_barcode' => 'Шк короба',
            'product_barcode' => 'Product Barcode',
            'product_quantity' => 'Product Quantity',
            'product_operator' => 'Product Operator',
            'reason' => 'Reason',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}