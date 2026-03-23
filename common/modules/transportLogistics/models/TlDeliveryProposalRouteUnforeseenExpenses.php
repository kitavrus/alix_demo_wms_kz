<?php

namespace common\modules\transportLogistics\models;

use common\events\DpEvent;
use Yii;
use common\models\ActiveRecord;
use app\modules\transportLogistics\transportLogistics;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "tl_delivery_proposal_route_unforeseen_expenses".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $tl_delivery_proposal_id
 * @property integer $tl_delivery_route_id
 * @property integer $type_id
 * @property integer $who_pays
 * @property string $name
 * @property integer $delivery_date // TO DELETE
 * @property string $price_cache
 * @property integer $cash_no
 * @property string $price_with_vat
 * @property integer $status
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class TlDeliveryProposalRouteUnforeseenExpenses extends ActiveRecord
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
        return 'tl_delivery_proposal_route_unforeseen_expenses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['client_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'required'],
            [['type_id','who_pays','client_id', 'tl_delivery_proposal_id', 'tl_delivery_route_id', 'cash_no', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['price_cache', 'price_with_vat'], 'number'],
            [['comment'], 'string'],
            [['delivery_date'], 'string'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /*
    *
    * */
//    public function scenarios() {
//        return [
//            'default'=>['number_places',],
//            'create-update-manager-warehouse'=>[
//                'number_places',
//                'number_places_actual',
//
//            ],
//        ];
//    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'client_id' => Yii::t('transportLogistics/forms', 'Internal client id'),
            'tl_delivery_proposal_id' => Yii::t('transportLogistics/forms', 'DP id'),
            'tl_delivery_route_id' => Yii::t('transportLogistics/forms', 'DP route id'),
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

//    /*
//    * After save add order to route order
//    * */
//    public function afterSave($insert, $changedAttributes )
//    {
//        $e = new DpEvent();
//        $e->deliveryProposalId = $this->tl_delivery_proposal_id;
//        Event::trigger(TlDeliveryProposal::className(),TlDeliveryProposal::EVENT_RECALCULATE,$e);
//    }

    /*
     *
     * */
    public function afterDelete()
    {
//        TlDeliveryRoutes::recalculateExpensesRoute($this->tl_delivery_route_id);
//        TlDeliveryProposal::recalculateExpensesOrder($this->tl_delivery_proposal_id);
    }
}
