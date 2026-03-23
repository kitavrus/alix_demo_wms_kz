<?php

namespace common\modules\stock\models;

use Yii;

/**
 * This is the model class for table "stock_zone".
 *
 * @property integer $id
 * @property string $name
 * @property string $address_begin
 * @property string $address_end
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class StockZone extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_zone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['name', 'address_begin', 'address_end'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'address_begin' => Yii::t('app', 'Address begin'),
            'address_end' => Yii::t('app', 'Address end'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return StockZoneQuery the active query used by this AR class.
     */
//    public static function find()
//    {
//        return new ZoneQuery(get_called_class());
//    }
}