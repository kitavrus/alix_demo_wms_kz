<?php

namespace common\modules\stock\models;

use Yii;

/**
 * This is the model class for table "stock_extra_fields".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $field_name
 * @property string $field_value
 * @property integer $date_created
 * @property integer $created_by
 */
class StockExtraField extends \yii\db\ActiveRecord
{
    const PARENT_TYPE_STOCK = 1;
    const PARENT_TYPE_OUTBOUND = 2;
    const PARENT_TYPE_OUTBOUND_ITEM = 3;


    const OUTBOUND_BOX_FIELD_NAME_DEFACTO = 'OUTBOUND_BOX_FIELD_NAME_DEFACTO';
    const OUTBOUND_LC_BARCODE_FIELD_NAME_DEFACTO = 'OUTBOUND_LC_BARCODE_FIELD_NAME_DEFACTO';
    const OUTBOUND_WAYBILL_NUMBER_FIELD_NAME_DEFACTO = 'OUTBOUND_WAYBILL_NUMBER_FIELD_NAME_DEFACTO';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_extra_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'date_created', 'created_by'], 'integer'],
            [['field_name'], 'string', 'max' => 128],
            [['field_value'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Stock id'),
            'field_name' => Yii::t('app', 'Field name'),
            'field_value' => Yii::t('app', 'Field value'),
            'date_created' => Yii::t('app', 'Date created'),
            'created_by' => Yii::t('app', 'Created by'),
        ];
    }

    public static function saveBoxDefacto($ids,$data = [])
    {
        if(empty($data) || !is_array($data) || empty($ids)) {
            return false;
        }
        if(!is_array($ids)) {
            $ids = [$ids];
        }

        foreach ($ids as $id) {
            foreach ($data as $key => $value) {
                $e = StockExtraField::find()
                    ->andWhere(['parent_id'=>$id,'field_value'=>$value])
                    ->exists();
                if(!$e) {
                    $m = new StockExtraField();
                    $m->parent_id = $id;
                    $m->field_name = $key;
                    $m->field_value = $value;
                    $m->save(false);
                }
            }
        }

        return true;
    }

    public static function deleteAllById($id)
    {
        if(empty($id)) {
            return false;
        }

        return StockExtraField::deleteAll(['parent_id'=>$id]);
    }
}