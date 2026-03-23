<?php

namespace common\modules\crossDock\models;

use Yii;

/**
 * This is the model class for table "cross_dock_item_products".
 *
 * @property integer $id
 * @property integer $cross_dock_item_id
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
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class CrossDockItemProducts extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cross_dock_item_products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cross_dock_item_id'], 'required'],
            [['cross_dock_item_id', 'product_id', 'status', 'expected_qty', 'accepted_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
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
            'id' => Yii::t('app', 'ID'),
            'cross_dock_item_id' => Yii::t('app', 'Internal cross dock order id'),
            'product_id' => Yii::t('app', 'Internal product id'),
            'product_name' => Yii::t('app', 'Scanned product name'),
            'product_barcode' => Yii::t('app', 'Scanned product barcode'),
            'product_price' => Yii::t('app', 'Product price'),
            'product_model' => Yii::t('app', 'Product model'),
            'product_sku' => Yii::t('app', 'Product sku'),
            'product_madein' => Yii::t('app', 'Product made in'),
            'product_composition' => Yii::t('app', 'Product composition'),
            'product_exporter' => Yii::t('app', 'Product exporter'),
            'product_importer' => Yii::t('app', 'Product importer'),
            'product_description' => Yii::t('app', 'Product importer'),
            'product_serialize_data' => Yii::t('app', 'Product serialize data'),
            'box_barcode' => Yii::t('app', 'Box barcode'),
            'status' => Yii::t('app', 'Status new, scanned'),
            'expected_qty' => Yii::t('app', 'Expected product quantity in order'),
            'accepted_qty' => Yii::t('app', 'Accepted product quantity in order'),
            'begin_datetime' => Yii::t('app', 'The start time of the scan order'),
            'end_datetime' => Yii::t('app', 'The end time of the scan order'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
