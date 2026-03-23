<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_change_address_place".
 *
 * @property int $id
 * @property string $from_barcode Address/Box barcode
 * @property string $to_barcode Address/Box barcode
 * @property string $product_barcode Product barcode
 * @property int $product_qty Product qty 
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceChangeAddressPlace extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_change_address_place';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_qty', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['from_barcode', 'to_barcode', 'product_barcode'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_barcode' => 'From Barcode',
            'to_barcode' => 'To Barcode',
            'product_barcode' => 'Product Barcode',
            'product_qty' => 'Product Qty',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
