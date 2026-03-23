<?php

namespace stockDepartment\modules\sheetShipment\models;

use Yii;

/**
 * This is the model class for table "sheep_shipment_place_address".
 *
 * @property integer $id
 * @property string $address
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class SheepShipmentPlaceAddressAR extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sheep_shipment_place_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['address'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'address' => Yii::t('app', 'Place address'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('employees/forms', 'Created At'),
            'updated_at' => Yii::t('employees/forms', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return SheepShipmentPlaceAddressARQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SheepShipmentPlaceAddressARQuery(get_called_class());
    }
}
