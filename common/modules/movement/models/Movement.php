<?php

namespace common\modules\movement\models;

use common\modules\stock\models\ConstantZone;
use Yii;

/**
 * This is the model class for table "movements".
 *
 * @property integer $id
 * @property integer $client_id
 * @property string $order_number
 * @property string $parent_order_number
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $allocated_qty
 * @property string $comments
 * @property string $extra_fields
 * @property integer $zone_in
 * @property integer $zone_out
 * @property integer $from_zone
 * @property integer $to_zone
 * @property string $client_order_id
 * @property string $client_datetime
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
class Movement extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movements';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expected_qty','accepted_qty','allocated_qty','client_id', 'status', 'zone_in', 'zone_out', 'from_zone', 'to_zone', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['comments', 'extra_fields', 'field_extra4', 'field_extra5'], 'string'],
            [['order_number', 'parent_order_number', 'client_order_id', 'client_datetime', 'field_extra2'], 'string', 'max' => 128],
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
            'client_id' => Yii::t('app', 'Client id'),
            'order_number' => Yii::t('app', 'Order number'),
            'parent_order_number' => Yii::t('app', 'Parent order number'),
            'status' => Yii::t('app', 'Status'),
            'comments' => Yii::t('app', 'Comments'),
            'extra_fields' => Yii::t('app', 'Extra fields'),
            'zone_in' => Yii::t('app', 'Zone inbound'),
            'zone_out' => Yii::t('app', 'Zone outbound'),
            'from_zone' => Yii::t('app', 'From zone'),
            'to_zone' => Yii::t('app', 'To zone'),
            'client_order_id' => Yii::t('app', 'Client order number id'),
            'client_datetime' => Yii::t('app', 'Client datetime'),
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
     * @return MovementQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MovementQuery(get_called_class());
    }

    public static function getZoneValue($zone = null) {
        return ConstantZone::getZoneValue($zone);
    }

    public function getStatusValue($status = null) {

        if(is_null($status)){
            $status = $this->status;
        }
        return MovementConstant::getStatusValue($status);
    }
}