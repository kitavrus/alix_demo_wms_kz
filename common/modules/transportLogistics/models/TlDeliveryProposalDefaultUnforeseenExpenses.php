<?php

namespace common\modules\transportLogistics\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tl_delivery_proposal_default_unforeseen_expenses".
 *
 * @property integer $id
 * @property integer $tl_delivery_proposal_default_sub_route_id
 * @property integer $tl_delivery_proposal_default_route_id
 * @property integer $type_id
 * @property string $name
 * @property integer $who_pays
 * @property string $price_cache
 * @property integer $cash_no
 * @property string $price_with_vat
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlDeliveryProposalDefaultUnforeseenExpenses extends \yii\db\ActiveRecord
{
    /*
    * @var integer
    * */
    const WHO_PAY_THEY = 0; // платит компания пересозчик
    const WHO_PAY_WE = 1; // Платим мы сами (Номадекс)
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_default_unforeseen_expenses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_delivery_proposal_default_sub_route_id','tl_delivery_proposal_default_route_id', 'type_id', 'who_pays', 'cash_no', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['price_cache', 'price_with_vat'], 'number'],
            [['comment'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'client_id' => Yii::t('transportLogistics/forms', 'Internal client id'),
            'tl_delivery_proposal_default_route_id' => Yii::t('transportLogistics/forms', 'DP route id'),
            'tl_delivery_proposal_default_sub_route_id' => Yii::t('transportLogistics/forms', 'DP sub route id'),
            'type_id' => Yii::t('transportLogistics/forms', 'route-unforeseen-expenses-type-id'),
            'name' => Yii::t('transportLogistics/forms', 'Name'),
            'who_pays' => Yii::t('transportLogistics/forms', 'Who pays'),
            'delivery_date' => Yii::t('transportLogistics/forms', 'Data expenses'),
            'price_cache' => Yii::t('transportLogistics/forms', 'Price expenses'),
            'cash_no' => Yii::t('transportLogistics/forms', 'Cash No'),
            'price_with_vat' => Yii::t('transportLogistics/forms', 'Price with vat'),
            'status' => Yii::t('transportLogistics/forms', 'Status'),
            'comment' => Yii::t('transportLogistics/forms', 'Comment'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
        ];
    }

    /**
     * @return array With who pays.
     */
    public static function getWhoPaysArray()
    {
        return [
            self::WHO_PAY_THEY => Yii::t('transportLogistics/forms', 'Pay they'),
            self::WHO_PAY_WE => Yii::t('transportLogistics/forms', 'Pay we'),
        ];
    }

    /**
     * @return string Display value
     */
    public function getWhoPayValue()
    {
        return ArrayHelper::getValue($this->getWhoPaysArray(),$this->who_pays);
    }


    /**
     * @return array Type.
     */
    public static function getTypeArray()
    {
        return ArrayHelper::map(
            TlDeliveryProposalRouteUnforeseenExpensesType::find()->active()->all(),'id','name'
        );
    }
    /**
     * @return string Show value.
     */
    public function getTypeValue($type_id = null)
    {
        return ArrayHelper::getValue(self::getTypeArray(),is_null($type_id) ? $this->type_id : $type_id);
    }
}