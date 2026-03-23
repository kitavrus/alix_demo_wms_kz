<?php

namespace stockDepartment\modules\wms\managers\erenRetail\checkBox\entities;

use Yii;

/**
 * This is the model class for table "check_box_inventory".
 *
 * @property int $id
 * @property string $inventory_key Inventory key
 * @property string $status Статус
 * @property string $description Описание
 * @property int $expected_product_qty Expected product qty
 * @property int $scanned_product_qty Scanned product qty
 * @property int $expected_box_qty Expected box qty
 * @property int $scanned_box_qty Scanned box qty
 * @property int $begin_datetime Begin scanning datetime
 * @property int $end_datetime End scanning datetime
 * @property int $complete_date Packing date
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class CheckBoxInventory extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'check_box_inventory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expected_product_qty', 'scanned_product_qty', 'expected_box_qty', 'scanned_box_qty', 'begin_datetime', 'end_datetime', 'complete_date', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['inventory_key', 'status'], 'string', 'max' => 36],
            [['description'], 'string'],
            [['inventory_key'], 'required'],
            [['inventory_key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'inventory_key' => Yii::t('app', 'Inventory key'),
            'status' => Yii::t('app', 'Статус'),
            'expected_product_qty' => Yii::t('app', 'Expected product qty'),
            'scanned_product_qty' => Yii::t('app', 'Scanned product qty'),
            'expected_box_qty' => Yii::t('app', 'Expected box qty'),
            'scanned_box_qty' => Yii::t('app', 'Scanned box qty'),
            'begin_datetime' => Yii::t('app', 'Begin scanning datetime'),
            'end_datetime' => Yii::t('app', 'End scanning datetime'),
            'complete_date' => Yii::t('app', 'Complete date'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
            'description' => Yii::t('app', 'Описание'),
        ];
    }
}
