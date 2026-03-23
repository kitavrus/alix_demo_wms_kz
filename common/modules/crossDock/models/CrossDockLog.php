<?php

namespace common\modules\crossDock\models;

use Yii;

/**
 * This is the model class for table "cross_dock_log".
 *
 * @property integer $id
 * @property string $unique_key
 * @property integer $client_id
 * @property string $box_barcode
 * @property integer $from_point_id
 * @property integer $to_point_id
 * @property string $to_point_title
 * @property string $from_point_title
 * @property string $party_number
 * @property string $order_number
 * @property integer $order_type
 * @property integer $status
 * @property integer $expected_number_places_qty
 * @property integer $expected_rpt_places_qty
 * @property string $box_m3
 * @property string $weight_net
 * @property string $weight_brut
 * @property string $field_extra
 * @property integer $expected_datetime
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class CrossDockLog extends \common\models\ActiveRecord
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
        return 'cross_dock_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expected_rpt_places_qty','client_id', 'from_point_id', 'to_point_id', 'order_type', 'status', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['unique_key'], 'string', 'max' => 34],
            [['box_barcode'], 'string', 'max' => 54],
            [['field_extra'], 'string'],
            [['to_point_title', 'from_point_title'], 'string', 'max' => 255],
            [['weight_brut','weight_net','box_m3','party_number', 'order_number'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'unique_key' => Yii::t('forms', 'Unique Key'),
            'client_id' => Yii::t('forms', 'Client ID'),
            'box_barcode' => Yii::t('forms', 'Box Barcode'),
            'from_point_id' => Yii::t('forms', 'From Point ID'),
            'to_point_id' => Yii::t('forms', 'To Point ID'),
            'to_point_title' => Yii::t('forms', 'To Point Title'),
            'from_point_title' => Yii::t('forms', 'From Point Title'),
            'party_number' => Yii::t('forms', 'Party Number'),
            'order_number' => Yii::t('forms', 'Order Number'),
            'order_type' => Yii::t('forms', 'Order Type'),
            'status' => Yii::t('forms', 'Status'),
            'expected_number_places_qty' => Yii::t('forms', 'Expected Number Places Qty'),
            'expected_rpt_places_qty' => Yii::t('forms', 'Expected rpt places Qty'),

            'box_m3' => Yii::t('forms', 'Box m3'),
            'weight_brut' => Yii::t('forms', 'Brut weight'),
            'weight_net' => Yii::t('forms', 'Net weight'),
            'field_extra' => Yii::t('forms', 'Field extra'),

            'expected_datetime' => Yii::t('forms', 'Expected Datetime'),
            'begin_datetime' => Yii::t('forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('forms', 'End Datetime'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }
}