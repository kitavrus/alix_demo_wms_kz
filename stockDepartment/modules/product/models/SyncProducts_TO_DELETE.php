<?php

namespace stockDepartment\modules\product\models;

use Yii;
use common\models\ActiveRecord;

/**
 * This is the model class for table "sync_products".
 *
 * @property integer $id
 * @property string $price
 * @property integer $product_id
 * @property integer $client_id
 * @property integer $client_product_id
 * @property string $name
 * @property string $barcode
 * @property string $sku
 * @property string $article
 * @property integer $created_user_id
 * @property integer $modified_user_id
 * @property string $sync_file_datetime
 * @property integer $created_at
 * @property integer $updated_at
 */
class SyncProducts extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sync_products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['product_id', 'client_id', 'client_product_id', 'created_user_id', 'modified_user_id', 'created_at', 'updated_at'], 'integer'],
            [['client_id', 'client_product_id', 'name', 'barcode', 'sku', 'article', 'created_user_id', 'modified_user_id', 'created_at', 'updated_at'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['barcode'], 'string', 'max' => 24],
            [['sku', 'article','sync_file_datetime'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('titles', 'ID'),
            'price' => Yii::t('titles', 'Internal product id'),
            'product_id' => Yii::t('titles', 'Internal product id'),
            'client_id' => Yii::t('titles', 'Client ID'),
            'client_product_id' => Yii::t('titles', 'Client Product ID'),
            'name' => Yii::t('titles', 'Name'),
            'barcode' => Yii::t('titles', 'Barcode'),
            'sku' => Yii::t('titles', 'Sku'),
            'article' => Yii::t('titles', 'Article'),
            'created_user_id' => Yii::t('titles', 'Created User ID'),
            'modified_user_id' => Yii::t('titles', 'Modified User ID'),
            'sync_file_datetime' => Yii::t('titles', 'Datetime last update file'),
            'created_at' => Yii::t('titles', 'Created At'),
            'updated_at' => Yii::t('titles', 'Updated At'),
        ];
    }
}