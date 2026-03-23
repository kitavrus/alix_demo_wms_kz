<?php

namespace common\modules\movement\models;

use Yii;

/**
 * This is the model class for table "movement_items".
 *
 * @property integer $id
 * @property integer $movement_id
 * @property integer $product_id
 * @property string $product_name
 * @property string $product_model
 * @property string $product_sku
 * @property string $product_description
 * @property string $product_barcode
 * @property string $product_serialize_data
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property string $comments
 * @property integer $from_zone
 * @property integer $to_zone
 * @property string $field_extra1
 * @property string $field_extra2
 * @property string $field_extra3
 * @property string $field_extra4
 * @property string $field_extra5
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class MovementItems extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movement_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['movement_id', 'product_id', 'status', 'expected_qty', 'accepted_qty', 'from_zone', 'to_zone', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_description', 'product_serialize_data', 'comments', 'field_extra4', 'field_extra5'], 'string'],
            [['product_name', 'product_model', 'product_sku', 'product_barcode', 'field_extra2'], 'string', 'max' => 128],
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
            'movement_id' => Yii::t('app', 'Movement id'),
            'product_id' => Yii::t('app', 'Product id'),
            'product_name' => Yii::t('app', 'Product name'),
            'product_model' => Yii::t('app', 'Product model'),
            'product_sku' => Yii::t('app', 'Product sku'),
            'product_description' => Yii::t('app', 'Product description'),
            'product_barcode' => Yii::t('app', 'Product barcode'),
            'product_serialize_data' => Yii::t('app', 'Client Product data'),
            'status' => Yii::t('app', 'Status'),
            'expected_qty' => Yii::t('app', 'Expected qty'),
            'accepted_qty' => Yii::t('app', 'Accepted qty'),
            'comments' => Yii::t('app', 'comments'),
            'from_zone' => Yii::t('app', 'From zone'),
            'to_zone' => Yii::t('app', 'To zone'),
            'field_extra1' => Yii::t('app', 'Extra field 1'),
            'field_extra2' => Yii::t('app', 'Extra field 2'),
            'field_extra3' => Yii::t('app', 'Extra field 3'),
            'field_extra4' => Yii::t('app', 'Extra field 4'),
            'field_extra5' => Yii::t('app', 'Extra field 5'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return MovementItemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MovementItemsQuery(get_called_class());
    }
}
