<?php

namespace common\modules\movement\models;

use Yii;

/**
 * This is the model class for table "movement_pick_lists".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $employee_id
 * @property string $barcode
 * @property string $employee_barcode
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class MovementPickList extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movement_pick_lists';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','employee_id', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['barcode'], 'string', 'max' => 128],
            [['employee_barcode'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'employee_id' => 'Employee ID',
            'barcode' => 'Barcode',
            'employee_barcode' => 'Employee Barcode',
            'begin_datetime' => 'Begin Datetime',
            'end_datetime' => 'End Datetime',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}