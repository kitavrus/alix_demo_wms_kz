<?php

namespace stockDepartment\modules\wms\managers\erenRetail\inventory;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "inventory_rows".
 *
 * @property integer $id
 * @property integer $inventory_id
 * @property integer $column_number
 * @property string $row_number
 * @property integer $floor_number
 * @property integer $level_number
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $expected_places_qty
 * @property integer $accepted_places_qty
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class InventoryRows extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inventory_rows';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level_number','floor_number','inventory_id', 'column_number', 'expected_qty', 'accepted_qty', 'expected_places_qty', 'accepted_places_qty', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['row_number'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
//            'id' => Yii::t('app', 'ID'),
//            'inventory_id' => Yii::t('app', 'Inventory ID'),
            'level_number' => Yii::t('inventory/forms', 'Level Number'),
            'column_number' => Yii::t('inventory/forms', 'Column Number'),
            'row_number' => Yii::t('inventory/forms', 'Row Number'),
            'floor_number' => Yii::t('inventory/forms', 'Floor Number'),
            'expected_qty' => Yii::t('inventory/forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('inventory/forms', 'Accepted Qty'),
//            'expected_places_qty' => Yii::t('app', 'Expected Places Qty'),
//            'accepted_places_qty' => Yii::t('app', 'Accepted Places Qty'),
            'status' => Yii::t('inventory/forms', 'Status'),
//            'created_user_id' => Yii::t('app', 'Created User ID'),
//            'updated_user_id' => Yii::t('app', 'Updated User ID'),
//            'created_at' => Yii::t('app', 'Created At'),
//            'updated_at' => Yii::t('app', 'Updated At'),
//            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public function getStatusArray()
    {
        return (new Inventory)->getStatusArray();
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatusValue($status = null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue((new Inventory)->getStatusArray(), $status);
    }
}
