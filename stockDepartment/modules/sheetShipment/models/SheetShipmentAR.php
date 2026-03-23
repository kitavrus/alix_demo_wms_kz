<?php

namespace stockDepartment\modules\sheetShipment\models;

use common\modules\codebook\models\BaseBarcode;
use yii;

/**
 * This is the model class for table "sheet_shipment".
 *
 * @property integer $id
 * @property string $place_address
 * @property string $box_barcode
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class SheetShipmentAR extends \common\models\ActiveRecord
{
    public static function isBoxBarcodeExist($boxBarcode)
    {
        return BaseBarcode::find()->andWhere(['box_barcode' => $boxBarcode])->exists();
    }

    public static function create($dto)
    {
        $ar = new self();
        $ar->setAttributes($dto);
        if ($ar->save(false)) {
            return $ar->id;
        }
        return false;
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sheet_shipment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['place_address', 'box_barcode'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'place_address' => Yii::t('app', 'Place address'),
            'box_barcode' => Yii::t('app', 'Box barcode'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return SheetShipmentARQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SheetShipmentARQuery(get_called_class());
    }
}
