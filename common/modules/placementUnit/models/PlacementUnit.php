<?php

namespace common\modules\placementUnit\models;

use Yii;
use common\models\ActiveRecord;

/**
 * This is the model class for table "placement_unit".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $zone_id
 * @property integer $count_unit
 * @property integer $type_inout
 * @property string $barcode
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class PlacementUnit extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'placement_unit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'zone_id', 'count_unit', 'type_inout', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['barcode'], 'string', 'max' => 23],
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
            'zone_id' => Yii::t('app', 'Zone id'),
            'count_unit' => Yii::t('app', 'Count unit'),
            'type_inout' => Yii::t('app', 'Type inbound or outbound, mix'),
            'barcode' => Yii::t('app', 'Placement unit barcode'),
            'status' => Yii::t('app', 'Status: free, work, close'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
