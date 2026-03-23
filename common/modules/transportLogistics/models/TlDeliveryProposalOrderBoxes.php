<?php

namespace common\modules\transportLogistics\models;

use Yii;

/**
 * This is the model class for table "tl_delivery_proposal_order_boxes".
 *
 * @property int $id
 * @property int $tl_delivery_proposal_id DP id
 * @property string $box_barcode Шк короба клиента
 * @property string $employee_name Имя сканирующего
 * @property int $created_user_id
 * @property int $updated_user_id
 * @property int $created_at
 * @property int $updated_at
 */
class TlDeliveryProposalOrderBoxes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_order_boxes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_delivery_proposal_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'required'],
            [['box_barcode'], 'string', 'max' => 255],
            [['employee_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tl_delivery_proposal_id' => Yii::t('app', 'DP id'),
            'box_barcode' => Yii::t('app', 'Шк короба клиента'),
            'employee_name' => Yii::t('app', 'Имя сканирующего'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
