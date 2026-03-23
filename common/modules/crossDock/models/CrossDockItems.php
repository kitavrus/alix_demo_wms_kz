<?php

namespace common\modules\crossDock\models;

use Yii;

/**
 * This is the model class for table "cross_dock_items".
 *
 * @property integer $id
 * @property integer $cross_dock_id
 * @property string $box_barcode
 * @property integer $status
 * @property integer $expected_number_places_qty
 * @property integer $accepted_number_places_qty
 * @property string $box_m3
 * @property string $weight_net
 * @property string $weight_brut
 * @property string $product_serialize_data
 * @property string $field_extra1
 * @property string $field_extra2
 * @property string $field_extra3
 * @property string $field_extra4
 * @property string $field_extra5
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class CrossDockItems extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cross_dock_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cross_dock_id', 'status', 'expected_number_places_qty', 'accepted_number_places_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['box_barcode'], 'string', 'max' => 54],
            [['box_m3','weight_net','weight_brut'], 'string', 'max' => 32],
            [['product_serialize_data','field_extra1','field_extra2','field_extra3','field_extra4','field_extra5'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'cross_dock_id' => Yii::t('forms', 'Cross Dock ID'),
            'box_barcode' => Yii::t('stock/forms', 'Box Barcode'),
            'status' => Yii::t('forms', 'Status'),
            'expected_number_places_qty' => Yii::t('inbound/forms', 'Expected Number Places Qty'),
            'accepted_number_places_qty' => Yii::t('inbound/forms', 'Accepted Number Places Qty'),

            'box_m3' => Yii::t('stock/forms', 'Box volume'),
            'weight_brut' => Yii::t('stock/forms', 'Brut weight'),
            'weight_net' => Yii::t('stock/forms', 'Net weight'),

            'begin_datetime' => Yii::t('inbound/forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('inbound/forms', 'End Datetime'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }
}