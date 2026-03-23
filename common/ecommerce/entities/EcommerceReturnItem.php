<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_return_items".
 *
 * @property int $id
 * @property int $return_id Return id
 * @property int $product_id Product id
 * @property string $product_barcode Шк товара
 * @property string $product_barcode1 Шк товара
 * @property string $product_barcode2 Шк товара
 * @property string $product_barcode3 Шк товара
 * @property string $product_barcode4 Шк товара
 * @property int $expected_qty Product Expected qty
 * @property int $accepted_qty Product Accepted qty
 * @property int $status Status
 * @property int $begin_datetime Begin scanning datetime
 * @property int $end_datetime End scanning datetime
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceReturnItem extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_return_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['return_id', 'product_id', 'expected_qty', 'accepted_qty', 'status', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['client_SkuId'], 'string', 'max' => 16],
            [['client_ImageUrl'], 'string'],
            [['client_UnitPrice'], 'string', 'max' => 16],
            [['client_UnitDiscount'], 'string', 'max' => 16],
            [['client_SalesQuantity'], 'integer'],
            [['client_ReturnedQuantity'], 'integer'],
            [['product_barcode'], 'string', 'max' => 18],
            [['product_barcode1'], 'string', 'max' => 18],
            [['product_barcode2'], 'string', 'max' => 18],
            [['product_barcode3'], 'string', 'max' => 18],
            [['product_barcode4'], 'string', 'max' => 18],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'return_id' => 'Return ID',
            'product_id' => 'Product ID',
            'product_barcode' => 'Product Barcode',
            'expected_qty' => 'Expected Qty',
            'accepted_qty' => 'Accepted Qty',
            'status' => 'Status',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
