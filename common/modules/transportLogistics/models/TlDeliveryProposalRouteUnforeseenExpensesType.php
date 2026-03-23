<?php

namespace common\modules\transportLogistics\models;

use Yii;
use common\models\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tl_delivery_proposal_route_unforeseen_expenses_type".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlDeliveryProposalRouteUnforeseenExpensesType extends ActiveRecord
{
    /*
 * @var integer status
 * */
//    const STATUS_NOT_DEFINED = 0;
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
//    const STATUS_DELETED = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_route_unforeseen_expenses_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['name'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('transportLogistics/forms', 'Name'),
            'status' => Yii::t('transportLogistics/forms', 'Status'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray()
    {
        return  [
            self::STATUS_ACTIVE => Yii::t('forms', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('forms', 'Not active'),
        ];
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatusValue($status = null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue(self::getStatusArray(),$status);
    }

    /**
     * @inheritdoc
     * @return TlDeliveryProposalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TlDeliveryProposalQuery(get_called_class());
    }
}
