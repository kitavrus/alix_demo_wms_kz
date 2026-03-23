<?php

namespace common\modules\returnOrder\models;

use Yii;

/**
 * This is the model class for table "return_order_item_products".
 *
 * @property integer $id
 * @property integer $return_order_id
 * @property integer $return_order_item_id
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
 * @property string $field_extra1
 * @property string $field_extra2
 * @property string $field_extra3
 * @property string $field_extra4
 * @property string $field_extra5
 * @property string $box_barcode
 * @property string $client_box_barcode
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ReturnOrderItemProduct extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'return_order_item_products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['return_order_id', 'return_order_item_id'], 'required'],
            [['return_order_id', 'return_order_item_id', 'product_id', 'status', 'expected_qty', 'accepted_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_price'], 'number'],
            [['product_exporter', 'product_importer', 'product_description', 'product_serialize_data', 'field_extra4', 'field_extra5'], 'string'],
            [['product_name', 'product_model', 'product_sku', 'product_madein', 'product_composition', 'field_extra2'], 'string', 'max' => 128],
            [['product_barcode', 'box_barcode', 'client_box_barcode'], 'string', 'max' => 54],
            [['field_extra1'], 'string', 'max' => 64],
            [['field_extra3'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'return_order_id' => Yii::t('app', 'Return Order ID'),
            'return_order_item_id' => Yii::t('app', 'Return Order Item ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'product_name' => Yii::t('app', 'Product Name'),
            'product_barcode' => Yii::t('app', 'Product Barcode'),
            'product_price' => Yii::t('app', 'Product Price'),
            'product_model' => Yii::t('app', 'Product Model'),
            'product_sku' => Yii::t('app', 'Product Sku'),
            'product_madein' => Yii::t('app', 'Product Madein'),
            'product_composition' => Yii::t('app', 'Product Composition'),
            'product_exporter' => Yii::t('app', 'Product Exporter'),
            'product_importer' => Yii::t('app', 'Product Importer'),
            'product_description' => Yii::t('app', 'Product Description'),
            'product_serialize_data' => Yii::t('app', 'Product Serialize Data'),
            'field_extra1' => Yii::t('app', 'Field Extra1'),
            'field_extra2' => Yii::t('app', 'Field Extra2'),
            'field_extra3' => Yii::t('app', 'Field Extra3'),
            'field_extra4' => Yii::t('app', 'Field Extra4'),
            'field_extra5' => Yii::t('app', 'Field Extra5'),
            'box_barcode' => Yii::t('app', 'Box Barcode'),
            'client_box_barcode' => Yii::t('app', 'Client Box Barcode'),
            'status' => Yii::t('app', 'Status'),
            'expected_qty' => Yii::t('app', 'Expected Qty'),
            'accepted_qty' => Yii::t('app', 'Accepted Qty'),
            'begin_datetime' => Yii::t('app', 'Begin Datetime'),
            'end_datetime' => Yii::t('app', 'End Datetime'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return ReturnOrderItemProductsQuery the active query used by this AR class.
     */
//    public static function find()
//    {
//        return new ReturnOrderItemProductsQuery(get_called_class());
//    }
}