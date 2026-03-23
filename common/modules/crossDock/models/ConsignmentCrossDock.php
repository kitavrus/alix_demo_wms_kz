<?php

namespace common\modules\crossDock\models;

use Yii;

/**
 * This is the model class for table "consignment_cross_dock".
 *
 * @property integer $id
 * @property integer $client_id
 * @property string $party_number
 * @property integer $status
 * @property integer $accepted_number_places_qty
 * @property integer $expected_number_places_qty
 * @property integer $expected_rpt_places_qty
 * @property integer $accepted_rpt_places_qty
 * @property integer $expected_datetime
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ConsignmentCrossDock extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'consignment_cross_dock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accepted_rpt_places_qty','expected_rpt_places_qty','client_id', 'party_number', 'status', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['party_number'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'client_id' => Yii::t('forms', 'Client ID'),
            'party_number' => Yii::t('forms', 'Party Number'),
            'status' => Yii::t('forms', 'Status'),
            'accepted_number_places_qty' => Yii::t('forms', 'Accepted Number Places Qty'),
            'expected_number_places_qty' => Yii::t('forms', 'Expected Number Places Qty'),
            'accepted_rpt_places_qty' => Yii::t('forms', 'Accepted RPT places Qty'),
            'expected_rpt_places_qty' => Yii::t('forms', 'Expected RPT places Qty'),
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
