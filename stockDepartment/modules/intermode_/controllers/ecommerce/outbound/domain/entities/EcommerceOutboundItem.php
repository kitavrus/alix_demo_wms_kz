<?php

namespace app\modules\intermode\controllers\ecommerce\outbound\domain\entities;

use Yii;
/**
 * This is the model class for table "ecommerce_outbound_items".
 *
 * @property int $id
 * @property int $outbound_id Outbound id
 * @property int $product_id Product id
 * @property string $product_sku Product sku
 * @property string $product_name Product name
 * @property string $product_model Product model
 * @property int $product_barcode Product model
 * @property string $product_brand Product brand
 * @property string $product_color Product color
 * @property float $product_price Product price
 * @property float $price_tax Product price tax
 * @property float $price_discount Product price discount
 * @property int $expected_qty Expected qty
 * @property int $allocated_qty Allocated qty
 * @property int $accepted_qty Accepted qty
 * @property int $cancel_qty Cancel qty
 * @property int $status Status
 * @property string $comment_message Comment message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceOutboundItem extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_outbound_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outbound_id', 'product_id', 'expected_qty', 'allocated_qty', 'accepted_qty', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['comment_message','product_sku', 'product_name', 'product_model', 'product_barcode', 'product_price','price_tax','price_discount','price_discount'], 'string'],
			[['cancel_qty'], 'integer'],
			[['product_brand','product_color',], 'string'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'outbound_id' => Yii::t('app', 'Outbound ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'product_sku' => Yii::t('app', 'Product Sku'),
            'product_name' => Yii::t('app', 'Product Name'),
            'product_model' => Yii::t('app', 'Product Model'),
            'product_barcode' => Yii::t('app', 'Product Barcode'),
            'product_price' => Yii::t('app', 'Product Price'),
            'expected_qty' => Yii::t('app', 'Expected Qty'),
            'allocated_qty' => Yii::t('app', 'Allocated Qty'),
            'accepted_qty' => Yii::t('app', 'Accepted Qty'),
            'status' => Yii::t('app', 'Status'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
            'cancel_qty' => Yii::t('app', 'Cancel qty'),
        ];
    }
}
