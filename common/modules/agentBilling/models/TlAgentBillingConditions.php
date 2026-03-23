<?php

namespace common\modules\agentBilling\models;

use common\modules\billing\models\TlDeliveryProposalBilling;
use Yii;
use common\modules\billing\components\BillingManager;
use yii\helpers\ArrayHelper;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\store\models\Store;
/**
 * This is the model class for table "tl_agents_billing_conditions".
 *
 * @property integer $id
 * @property integer $tl_agents_billing_id
 * @property integer $agent_id
 * @property string $price_invoice
 * @property string $price_invoice_with_vat
 * @property string $formula_tariff
 * @property integer $status
 * @property integer $transport_type
 * @property string $comment
 * @property string $title
 * @property integer $delivery_type
 * @property integer $sort_order
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlAgentBillingConditions extends \common\models\ActiveRecord
{
    /*
     * @var integer status
     * */
    const STATUS_UNDEFINED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;


    const TRANSPORT_TYPE_AUTO = 1;
    const TRANSPORT_TYPE_RAIL = 2;
    const TRANSPORT_TYPE_AIR = 3;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_agents_billing_conditions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_agents_billing_id', 'agent_id', 'status', 'route_from', 'route_to', 'transport_type', 'rule_type', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['price_invoice', 'price_invoice_with_vat', 'price_kg', 'price_kg_with_vat', 'price_mc', 'price_mc_with_vat', 'price_pl', 'price_pl_with_vat'], 'number'],
            [['formula_tariff', 'comment', 'title'], 'string'],
            [['route_from', 'route_to'], 'required'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'tl_agents_billing_id' => Yii::t('forms', 'Tl Delivery Proposal Billing ID'),
            'agent_id' => Yii::t('forms', 'Client ID'),
            'route_from' => Yii::t('forms', 'Route From'),
            'route_to' => Yii::t('forms', 'Route To'),
            'price_invoice' => Yii::t('forms', 'Price Invoice'),
            'price_invoice_with_vat' => Yii::t('forms', 'Price Invoice With Vat'),
            'price_kg' => Yii::t('forms', 'Price Invoice kg'),
            'price_mc' => Yii::t('forms', 'Price Invoice mc'),
            'price_pl' => Yii::t('forms', 'Price Invoice pl'),
            'price_kg_with_vat' => Yii::t('forms', 'Price Invoice kg with vat'),
            'price_mc_with_vat' => Yii::t('forms', 'Price Invoice mc with vat'),
            'price_pl_with_vat' => Yii::t('forms', 'Price Invoice pl with vat'),
            'formula_tariff' => Yii::t('forms', 'Formula Tariff'),
            'rule_type' => Yii::t('forms', 'Rule type'),
            'transport_type' => Yii::t('forms', 'Transport Type'),
            'status' => Yii::t('forms', 'Status'),
            'comment' => Yii::t('forms', 'Comment'),
            'title' => Yii::t('forms', 'Name'),
            'delivery_type' => Yii::t('forms', 'Delivery Type'),
            'sort_order' => Yii::t('forms', 'Sort Order'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }

    /**
     * @return array .
     */
    public static function getTransportTypeArray()
    {
        return [
            self::TRANSPORT_TYPE_AUTO => Yii::t('titles', 'Auto'),
            self::TRANSPORT_TYPE_RAIL => Yii::t('titles', 'Train'),
            self::TRANSPORT_TYPE_AIR => Yii::t('titles', 'Air'),
        ];
    }

    /**
     * @return string .
     */
    public function getTransportTypeValue($value = null)
    {
        if(is_null($value)) {
            $value = $this->transport_type;
        }

        return ArrayHelper::getValue(self::getTransportTypeArray(),$value);
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray($key = null)
    {
        $data = [
            self::STATUS_UNDEFINED => Yii::t('forms', 'Undefined'),
            self::STATUS_ACTIVE => Yii::t('forms', 'Active'),
            self::STATUS_INACTIVE => Yii::t('forms', 'Not active'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatusValue($status=null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(),$status);
    }

    /*
    * Relation with Client table
    **/
    public function getAgent()
    {
        return $this->hasOne(TlAgents::className(), ['id' => 'agent_id']);
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getRouteFrom()
    {
        return $this->hasOne(Store::className(), ['id' => 'route_from']);
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getRouteTo()
    {
        return $this->hasOne(Store::className(), ['id' => 'route_to']);
    }

    /*
    * This method is called at the beginning of inserting or updating a record.
    *
    * */
//    public function beforeSave($insert)
//    {
//        if (parent::beforeSave($insert)) {
//
//            $b = new BillingManager();
//            $this->price_invoice = $b->calculateWithOutNDS( $this->price_invoice_with_vat);
//
//            return true;
//        } else {
//            return false;
//        }
//    }

    /**
     * @return array Массив с типами  подсчета тарифа.
     */
    public static function getRuleTypeArray()
    {
        return  [
            TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_MC => Yii::t('titles', 'point-point (per м³)'),
            TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_KG => Yii::t('titles', 'point-point (per kg)'),
//            TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_CONDITION_MC => Yii::t('titles', 'point-point (per м³ by condition)'),
//            TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_CONDITION_KG=> Yii::t('titles', 'point-point (per kg by condition)'),
            TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_WEIGHT_VOLUME_INDEX => Yii::t('titles', 'point-point (weight-volume index)'),
            TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_UNIT => Yii::t('titles', 'point-point (per auto)'),
        ];

    }

    /**
     * @return string Читабельный тип.
     */
    public function getRuleType($rule_type=null)
    {
        if(is_null($rule_type)){
            $rule_type = $this->rule_type;
        }
        return ArrayHelper::getValue($this->getRuleTypeArray(), $rule_type);
    }

}
