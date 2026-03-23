<?php

namespace common\modules\returnOrder\models;

use Yii;

/**
 * This is the model class for table "return_order_items".
 *
 * @property integer $id
 * @property integer $return_order_id
 * @property integer $product_id
 * @property string $product_barcode
 * @property string $product_model
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $product_serialize_data
 * @property string $box_barcode
 * @property string $client_box_barcode
 * @property string $field_extra1
 * @property string $field_extra2
 * @property string $field_extra3
 * @property string $field_extra4
 * @property string $field_extra5
 * @property integer $from_point_id
 * @property integer $to_point_id
 * @property string $from_point_client_id
 * @property string $to_point_client_id
 *
 */
class ReturnOrderItems extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'return_order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['to_point_id','from_point_id','return_order_id', 'product_id', 'status', 'expected_qty', 'accepted_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['product_barcode'], 'string', 'max' => 54],
            [['product_model'], 'string', 'max' => 128],
            [['product_serialize_data','box_barcode','client_box_barcode'], 'string'],
            [['field_extra1','field_extra2','field_extra3','field_extra4','field_extra5'], 'string'],
            [['from_point_client_id','to_point_client_id'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('return/forms', 'ID'),
            'return_order_id' => Yii::t('return/forms', 'Return Order ID'),
            'product_id' => Yii::t('return/forms', 'Product ID'),
            'product_barcode' => Yii::t('return/forms', 'Product Barcode'),
            'product_model' => Yii::t('return/forms', 'Product model'),
            'box_barcode' => Yii::t('return/forms', 'Box barcode'),
            'client_box_barcode' => Yii::t('return/forms', 'Client box barcode'),
            'status' => Yii::t('return/forms', 'Status'),
            'expected_qty' => Yii::t('return/forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('return/forms', 'Accepted Qty'),
            'begin_datetime' => Yii::t('return/forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('return/forms', 'End Datetime'),
            'created_user_id' => Yii::t('return/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('return/forms', 'Updated User ID'),
            'created_at' => Yii::t('return/forms', 'Created At'),
            'updated_at' => Yii::t('return/forms', 'Updated At'),
            'deleted' => Yii::t('return/forms', 'Deleted'),
        ];
    }
}
