<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_check_box".
 *
 * @property int $id
 * @property int $client_id Client id
 * @property int $warehouse_id Warehouse id
 * @property int $employee_id Employee id
 * @property int $inventory_id
 * @property string $box_barcode Box barcode
 * @property string $place_address Place address barcode
 * @property string $place_address_part1 Place address floor
 * @property string $place_address_part2 Place address box
 * @property string $place_address_part3 Place address place
 * @property string $place_address_part4 Place address level
 * @property string $place_address_part5 Place address other
 * @property int $expected_qty Expected Quantity
 * @property int $scanned_qty Scanned Quantity
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceCheckBox extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_check_box';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'warehouse_id', 'employee_id', 'inventory_id', 'expected_qty', 'scanned_qty', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['box_barcode', 'place_address'], 'string', 'max' => 15],
            [['place_address_part1', 'place_address_part2', 'place_address_part3', 'place_address_part4', 'place_address_part5'], 'string', 'max' => 5],
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
            'warehouse_id' => Yii::t('app', 'Warehouse id'),
            'employee_id' => Yii::t('app', 'Employee id'),
            'inventory_id' => Yii::t('app', 'Inventory ID'),
            'box_barcode' => Yii::t('app', 'Box barcode'),
            'place_address' => Yii::t('app', 'Place address barcode'),
            'place_address_part1' => Yii::t('app', 'Place address floor'),
            'place_address_part2' => Yii::t('app', 'Place address box'),
            'place_address_part3' => Yii::t('app', 'Place address place'),
            'place_address_part4' => Yii::t('app', 'Place address level'),
            'place_address_part5' => Yii::t('app', 'Place address other'),
            'expected_qty' => Yii::t('app', 'Expected Quantity'),
            'scanned_qty' => Yii::t('app', 'Scanned Quantity'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
