<?php

namespace common\modules\agentBilling\models;

use Yii;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\city\models\Country;
use common\modules\city\models\City;
use common\modules\city\models\Region;
use common\modules\store\models\Store;
use yii\helpers\ArrayHelper;
use common\modules\agentBilling\models\TlAgentBillingConditions;

/**
 * This is the model class for table "tl_agents_billing".
 *
 * @property integer $id
 * @property integer $agent_id
 * @property integer $from_country_id
 * @property integer $from_region_id
 * @property integer $from_city_id
 * @property integer $to_country_id
 * @property integer $to_region_id
 * @property integer $to_city_id
 * @property integer $route_from
 * @property integer $route_to
 * @property integer $rule_type
 * @property string $mc
 * @property string $kg
 * @property integer $number_places
 * @property string $price_invoice
 * @property string $price_invoice_with_vat
 * @property string $price_invoice_kg
 * @property string $price_invoice_kg_with_vat
 * @property string $price_invoice_mc
 * @property string $price_invoice_mc_with_vat
 * @property string $formula_tariff
 * @property integer $status
 * @property string $delivery_term
 * @property integer $delivery_term_from
 * @property integer $delivery_term_to
 * @property integer $tariff_type
 * @property integer $cooperation_type
 * @property integer $delivery_type
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlAgentBilling extends \common\models\ActiveRecord
{

    /*
     * @var integer status
     * */
    const STATUS_UNDEFINED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /*
    * @var integer payment method
    * */
//    const METHOD_UNDEFINED = 0; //не указан
//    const METHOD_CASH = 1; //наличный
//    const METHOD_CHAR = 2; //безналичный


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_agents_billing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agent_id', 'status', 'cash_no', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
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
            'agent_id' => Yii::t('forms', 'Agent ID'),
            'cash_no' => Yii::t('forms', 'Method of payment'),
            'status' => Yii::t('forms', 'Status'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /*
     * Relation with Client table
     **/
    public function getAgent()
    {
        return $this->hasOne(TlAgents::className(), ['id' => 'agent_id']);
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
    * Relation has one with conditions
    * */
    public function getConditions()
    {
        return $this->hasMany(TlAgentBillingConditions::className(), ['tl_agents_billing_id' => 'id']);
    }

    /*
     * Get active conditions
     **/
    public function getActiveConditions()
    {
        return TlAgentBillingConditions::find()->andWhere(['tl_agents_billing_id' => $this->id, 'status' => TlAgentBillingConditions::STATUS_ACTIVE])->all();
    }

//
//    /**
//     * @return array Массив с формами оплаты.
//     */
//    public static function getPaymentMethodArray()
//    {
//        $data = [
//            self::METHOD_UNDEFINED => Yii::t('forms', 'Undefined'), //Не определен
//            self::METHOD_CASH => Yii::t('forms', 'Cash'), //наличный
//            self::METHOD_CHAR => Yii::t('forms', 'Charging to account') //безналичный
//        ];
//        return $data;
//    }
//
//    /**
//     * @return array Значение формы оплаты.
//     */
//    public function getPaymentMethod($cash_no=null)
//    {
//        if(is_null($cash_no)){
//            $cash_no = $this->cash_no;
//        }
//        return ArrayHelper::getValue(self::getPaymentMethodArray(), $cash_no);
//    }

//    /*
//   * Array with attribute values functions mapping
//   * @return array
//   **/
//    public function getAttributesValuesMap($attribute)
//    {
//        $data = [
//            'route_to'=>'getRouteTitle',
//            'route_from'=>'getRouteTitle',
//            'rule_type'=>'getRuleType',
//            'status'=>'getStatus',
//            'tariff_type'=>'getTariffType',
//            'cooperation_type'=>'getCooperationType',
//            'delivery_type'=>'getDeliveryType',
//            'from_country_id'=>'getCountryName',
//            'to_country_id'=>'getCountryName',
//            'from_city_id'=>'getCityName',
//            'to_city_id'=>'getCityName',
//            'from_region_id'=>'getRegionName',
//            'to_region_id'=>'getRegionName',
//        ];
//
//        return ArrayHelper::getValue($data, $attribute);
//    }
}
