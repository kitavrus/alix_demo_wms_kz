<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_product".
 *
 * @property int $id
 * @property int $client_id Inbound id
 * @property string $product_on_client_id Product on client id
 * @property string $product_sku Product sku
 * @property string $product_name Product name
 * @property string $product_model Product model
 * @property string $product_barcode Product model
 * @property int $status Status
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceProduct extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_on_client_id', 'product_sku', 'product_name', 'product_model', 'product_barcode'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'product_on_client_id' => Yii::t('app', 'Product On Client ID'),
            'product_sku' => Yii::t('app', 'Product Sku'),
            'product_name' => Yii::t('app', 'Product Name'),
            'product_model' => Yii::t('app', 'Product Model'),
            'product_barcode' => Yii::t('app', 'Product Barcode'),
            'status' => Yii::t('app', 'Status'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
