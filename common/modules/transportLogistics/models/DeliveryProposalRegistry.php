<?php

namespace common\modules\transportLogistics\models;

use Yii;
use common\models\ActiveRecord;
/**
 * This is the model class for table "delivery_proposal_registry".
 *
 * @property integer $id
 * @property string $dp_list
 * @property integer $registry_type
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class DeliveryProposalRegistry extends ActiveRecord
{
    const REGISTRY_TYPE_UNDEFINED = 0;
    const REGISTRY_TYPE_POINT_OUTBOUND = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delivery_proposal_registry';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dp_list'], 'required'],
            [['registry_type', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['dp_list'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'dp_list' => Yii::t('forms', 'Dp List'),
            'registry_type' => Yii::t('forms', 'Registry Type'),
            'status' => Yii::t('forms', 'Status'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }
}
