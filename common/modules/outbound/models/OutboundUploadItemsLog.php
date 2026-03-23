<?php

namespace common\modules\outbound\models;

use Yii;

/**
 * This is the model class for table "outbound_upload_items_log".
 *
 * @property integer $id
 * @property integer $outbound_upload_id
 * @property integer $product_id
 * @property string $product_name
 * @property string $product_barcode
 * @property string $product_price
 * @property string $product_model
 * @property string $product_sku
 * @property string $product_madein
 * @property string $product_composition
 * @property string $product_exporter
 * @property string $product_importer
 * @property string $product_description
 * @property string $product_serialize_data
 * @property string $box_barcode
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class OutboundUploadItemsLog extends \common\models\ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->dbAudit;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_upload_items_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outbound_upload_id'], 'required'],
            [['outbound_upload_id', 'product_id', 'status', 'expected_qty', 'accepted_qty', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_price'], 'number'],
            [['product_exporter', 'product_importer', 'product_description', 'product_serialize_data'], 'string'],
            [['product_name', 'product_model', 'product_sku', 'product_madein', 'product_composition'], 'string', 'max' => 128],
            [['product_barcode', 'box_barcode'], 'string', 'max' => 54]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'outbound_upload_id' => Yii::t('forms', 'Outbound Upload ID'),
            'product_id' => Yii::t('forms', 'Product ID'),
            'product_name' => Yii::t('forms', 'Product Name'),
            'product_barcode' => Yii::t('forms', 'Product Barcode'),
            'product_price' => Yii::t('forms', 'Product Price'),
            'product_model' => Yii::t('forms', 'Product Model'),
            'product_sku' => Yii::t('forms', 'Product Sku'),
            'product_madein' => Yii::t('forms', 'Product Madein'),
            'product_composition' => Yii::t('forms', 'Product Composition'),
            'product_exporter' => Yii::t('forms', 'Product Exporter'),
            'product_importer' => Yii::t('forms', 'Product Importer'),
            'product_description' => Yii::t('forms', 'Product Description'),
            'product_serialize_data' => Yii::t('forms', 'Product Serialize Data'),
            'box_barcode' => Yii::t('forms', 'Box Barcode'),
            'status' => Yii::t('forms', 'Status'),
            'expected_qty' => Yii::t('forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('forms', 'Accepted Qty'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }
}
