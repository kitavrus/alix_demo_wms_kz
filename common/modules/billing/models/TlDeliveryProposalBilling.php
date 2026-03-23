<?php

namespace common\modules\billing\models;

use common\modules\billing\components\BillingManager;
use Yii;
use yii\helpers\ArrayHelper;
use common\modules\client\models\Client;
use common\modules\city\models\City;
use common\modules\city\models\Region;
use common\modules\city\models\Country;
use common\modules\store\models\Store;
use common\helpers\iHelper;

/**
 * This is the model class for table "tl_delivery_proposal_billing".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $from_country_id
 * @property integer $to_country_id
 * @property integer $from_region_id
 * @property integer $to_region_id
 * @property integer $from_city_id
 * @property integer $to_city_id
 * @property integer $route_from
 * @property integer $route_to
 * @property integer $rule_type
 * @property string $mc
 * @property string $kg
 * @property integer $number_places
 * @property string $price_invoice
 * @property string $price_invoice_with_vat
 * @property string $price_invoice_kg_with_vat
 * @property string $price_invoice_mc_with_vat
 * @property string $price_invoice_kg
 * @property string $price_invoice_mc
 * @property string $formula_tariff
 * @property integer $status
 * @property integer $delivery_term
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
 */
class TlDeliveryProposalBilling extends \common\models\ActiveRecord
{

    /*
    * @var integer status
    * */
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /*
     * Rule type
     * */
    //const RULE_TYPE_BY_MC = 0;
    //const RULE_TYPE_BY_KG = 1;
    //const RULE_TYPE_BY_CONDITION_MC = 2;
   // const RULE_TYPE_BY_CONDITION_KG = 3;
   // const RULE_TYPE_BY_POINT = 4;


    const RULE_TYPE_UNDEFINED = 0;
    const RULE_TYPE_BY_POINT = 1;
    const RULE_TYPE_POINT_BY_MC = 2;
    const RULE_TYPE_POINT_BY_KG = 3;
    const RULE_TYPE_POINT_BY_CONDITION_KG = 4;
    const RULE_TYPE_POINT_BY_CONDITION_MC = 5;
    const RULE_TYPE_CITY_BY_MC = 6;
    const RULE_TYPE_CITY_BY_KG = 7;
    const RULE_TYPE_CITY_BY_CONDITION_KG = 8;
    const RULE_TYPE_CITY_BY_CONDITION_MC = 9;
    const RULE_TYPE_POINT_BY_WEIGHT_VOLUME_INDEX = 10;
    const RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX = 11;
    const RULE_TYPE_BY_CONDITION = 12;
    const RULE_TYPE_POINT_BY_UNIT = 13;

    /*
    * Tariff type
    * */
    const TARIFF_TYPE_UNDEFINED = 0;
    const TARIFF_TYPE_PERSON_DEFAULT = 1;
    const TARIFF_TYPE_COMPANY_DEFAULT = 2;
    const TARIFF_TYPE_PERSON_INDIVIDUAL = 3;
    const TARIFF_TYPE_COMPANY_INDIVIDUAL = 4;

    /*
     * Cooperation type
     * */
    const COOPERATION_TYPE_UNDEFINED = 0;
    const COOPERATION_TYPE_ONE_TIME = 1;
    const COOPERATION_TYPE_FULL_FREIGHT = 2;
    const COOPERATION_TYPE_COMPOSITE = 3;

    /*
    * Delivery type
    * */
    const DELIVERY_TYPE_UNDEFINED = 0;
    const DELIVERY_TYPE_WAREHOUSE_WAREHOUSE = 1;
    const DELIVERY_TYPE_DOOR_DOOR = 2;
    const DELIVERY_TYPE_WAREHOUSE_DOOR = 3;
    const DELIVERY_TYPE_DOOR_WAREHOUSE = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_billing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_type','client_id', 'from_country_id', 'tariff_type', 'cooperation_type', 'delivery_type', 'from_region_id', 'from_city_id', 'to_region_id', 'to_city_id', 'to_country_id', 'route_from', 'route_to', 'number_places', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['mc', 'kg', 'price_invoice', 'price_invoice_kg', 'price_invoice_mc', 'price_invoice_with_vat','price_invoice_kg_with_vat','price_invoice_mc_with_vat', 'delivery_term_from', 'delivery_term_to'], 'number'],
            [['formula_tariff', 'comment', 'delivery_term'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'client_id' => Yii::t('forms', 'Client'),
            'from_country_id' => Yii::t('forms', 'From Country'),
            'from_region_id' => Yii::t('forms', 'From Region'),
            'from_city_id' => Yii::t('forms', 'From City'),
            'to_country_id' => Yii::t('forms', 'To Country'),
            'to_region_id' => Yii::t('forms', 'To Region'),
            'to_city_id' => Yii::t('forms', 'To City'),
            'route_from' => Yii::t('forms', 'Route From'),
            'route_to' => Yii::t('forms', 'Route To'),
            'rule_type' => Yii::t('forms', 'Rule type'), // Тип подсчета тарифа
            'mc' => Yii::t('forms', 'Mc'),
            'kg' => Yii::t('forms', 'Kg'),
            'number_places' => Yii::t('forms', 'Number of Places'),
            'price_invoice' => Yii::t('forms', 'Price Invoice (fixed)'),
            'price_invoice_kg' => Yii::t('forms', 'Price Invoice kg'),
            'price_invoice_mc' => Yii::t('forms', 'Price Invoice mc'),
            'price_invoice_kg_with_vat' => Yii::t('forms', 'Price Invoice kg with vat'),
            'price_invoice_mc_with_vat' => Yii::t('forms', 'Price Invoice mc with vat'),
            'price_invoice_with_vat' => Yii::t('forms', 'Price Invoice With Vat (fixed)'),
            'formula_tariff' => Yii::t('forms', 'Formula Tariff'),
            'status' => Yii::t('forms', 'Status'),
            'delivery_term' => Yii::t('forms', 'Delivery Term'),
            'delivery_term_from' => Yii::t('forms', 'Delivery Term From'),
            'delivery_term_to' => Yii::t('forms', 'Delivery Term To'),
            'tariff_type' => Yii::t('forms', 'Tariff Type'),
            'cooperation_type' => Yii::t('forms', 'Cooperation Type'),
            'delivery_type' => Yii::t('forms', 'Delivery Type'),
            'comment' => Yii::t('forms', 'Comment'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }



    /*
  * Relation with Client table
  * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
     * Relation has one with Country
     * */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'from_country_id']);
    }

    /*
    * Relation has one with Region
    * */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'from_region_id']);
    }

    /*
    * Relation has one with City
    * */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'from_city_id'])->orderBy('name');
    }

    /*
    * Relation has one with City
    * */
    public function getCityTo()
    {
        return $this->hasOne(City::className(), ['id' => 'to_city_id'])->orderBy('name');
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


    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray($key = null)
    {
        $data = [
            self::STATUS_ACTIVE => Yii::t('forms', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('forms', 'Not active'),
            self::STATUS_DELETED => Yii::t('forms', 'Deleted'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatus($status=null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(),$status);
    }


    /**
     * @return array Массив с типами  подсчета тарифа.
     */
    public static function getRuleTypeArray()
    {
        return  [
            self::RULE_TYPE_UNDEFINED => Yii::t('titles', 'Undefined'),
            self::RULE_TYPE_BY_POINT => Yii::t('titles', 'point-point'),
            self::RULE_TYPE_POINT_BY_MC => Yii::t('titles', 'point-point (per м³)'),
            self::RULE_TYPE_POINT_BY_KG => Yii::t('titles', 'point-point (per kg)'),
            self::RULE_TYPE_POINT_BY_CONDITION_MC => Yii::t('titles', 'point-point (per м³ by condition)'),
            self::RULE_TYPE_POINT_BY_CONDITION_KG=> Yii::t('titles', 'point-point (per kg by condition)'),
            self::RULE_TYPE_CITY_BY_MC => Yii::t('titles', 'city-city (per м³)'),
            self::RULE_TYPE_CITY_BY_KG => Yii::t('titles', 'city-city (per kg)'),
            self::RULE_TYPE_CITY_BY_CONDITION_MC => Yii::t('titles', 'city-city (per м³ acc to condition)'),
            self::RULE_TYPE_POINT_BY_CONDITION_KG => Yii::t('titles', 'city-city (per kg acc to condition)'),
            self::RULE_TYPE_POINT_BY_WEIGHT_VOLUME_INDEX => Yii::t('titles', 'point-point (weight-volume index)'),
            self::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX => Yii::t('titles', 'city-city (weight-volume index)'),
            self::RULE_TYPE_BY_CONDITION => Yii::t('titles', 'By condition'),
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

    /*
    * Relation has one with conditions
    * */
    public function getConditions()
    {
        return $this->hasMany(TlDeliveryProposalBillingConditions::className(), ['tl_delivery_proposal_billing_id' => 'id'])->orderBy('sort_order DESC');
    }

    /*
    * Get all related default conditions
    * */
    public function getDefaultConditions()
    {
        return TlDeliveryProposalBillingConditions::findAll(['tl_delivery_proposal_billing_id' => $this->id]);
    }


    /*
    * @return array with store id=>title
    */
    public static function getRouteFromTo($key = null)
    {
        $data = ArrayHelper::map(Store::find()->orderBy('title')->all(), 'id', 'title');

        return isset($data[$key]) ? $data[$key] : $data;
    }


    /*
    * This method is called at the beginning of inserting or updating a record.
    *
    * */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $b = new BillingManager();
//            $this->price_invoice_with_vat = $b->calculateNDS( $this->price_invoice);
            $this->price_invoice = $b->calculateWithOutNDS($this->price_invoice_with_vat);
            if($this->price_invoice_kg_with_vat){
                $this->price_invoice_kg = $b->calculateWithOutNDS($this->price_invoice_kg_with_vat);
            }
            if($this->price_invoice_mc_with_vat){
                $this->price_invoice_mc = $b->calculateWithOutNDS($this->price_invoice_mc_with_vat);
            }

            if(!empty($this->delivery_term_from) && !empty($this->delivery_term_to) && $this->delivery_term_from !== $this->delivery_term_to){
                $this->delivery_term = 'От '.$this->delivery_term_from.' до '.$this->delivery_term_to.' дней';
            } elseif (!empty($this->delivery_term_from) && !empty($this->delivery_term_to) && $this->delivery_term_from == $this->delivery_term_to){
                $this->delivery_term = iHelper::formatTextAfterNumber($this->delivery_term_from, 'день', 'дня', 'дней');
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array Массив с типами тарифа
     */
    public static function getTariffTypeArray($key = null)
    {
        $data = [
            self::TARIFF_TYPE_UNDEFINED => Yii::t('forms', 'Undefined'),
            self::TARIFF_TYPE_PERSON_DEFAULT => Yii::t('forms', 'Person default'),
            self::TARIFF_TYPE_COMPANY_DEFAULT => Yii::t('forms', 'Company default'),
            self::TARIFF_TYPE_PERSON_INDIVIDUAL => Yii::t('forms', 'Person individual'),
            self::TARIFF_TYPE_COMPANY_INDIVIDUAL => Yii::t('forms', 'Company individual'),

        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getTariffType($tariff_type=null)
    {
        if(is_null($tariff_type)){
            $tariff_type = $this->tariff_type;
        }
        return ArrayHelper::getValue($this->getTariffTypeArray(), $tariff_type);
    }

    /**
     * @return array Массив с типами сотрудничества
     */
    public static function getCooperationTypeArray($key = null)
    {
        $data = [
            self::COOPERATION_TYPE_UNDEFINED => Yii::t('forms', 'Undefined'),
            self::COOPERATION_TYPE_ONE_TIME => Yii::t('forms', 'One-time transportation'),
            self::COOPERATION_TYPE_FULL_FREIGHT => Yii::t('forms', 'Full freight on the basis of a contract'),
            self::COOPERATION_TYPE_COMPOSITE => Yii::t('forms', 'Composite cargo on the basis of a contract'),


        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getCooperationType($cooperation_type=null)
    {
        if(is_null($cooperation_type)){
            $cooperation_type = $this->cooperation_type;
        }
        return ArrayHelper::getValue($this->getCooperationTypeArray(), $cooperation_type);
    }

    /**
     * @return array Массив с типами доставки
     */
    public static function getDeliveryTypeArray($key = null)
    {
        $data = [
            self::DELIVERY_TYPE_UNDEFINED => Yii::t('forms', 'Undefined'),
            self::DELIVERY_TYPE_WAREHOUSE_WAREHOUSE => Yii::t('forms', 'Warehouse-warehouse'),
            self::DELIVERY_TYPE_WAREHOUSE_DOOR => Yii::t('forms', 'Warehouse-Door'),

            self::DELIVERY_TYPE_DOOR_DOOR => Yii::t('forms', 'Door-Door'),
            self::DELIVERY_TYPE_DOOR_WAREHOUSE => Yii::t('forms', 'Door-warehouse'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getDeliveryType($delivery_type=null)
    {
        if(is_null($delivery_type)){
            $delivery_type = $this->delivery_type;
        }
        return ArrayHelper::getValue($this->getDeliveryTypeArray(), $delivery_type);
    }

    /**
     * Return array with tariffs by specified
     * delivery_type and customer_type
     * @param deliveryType int
     * @param customerType int
     * @return array
     */
    public static function getTariffsByType($deliveryType, $customerType){
        return   TlDeliveryProposalBilling::findAll([
            'tariff_type'=>$customerType,
            'delivery_type'=>$deliveryType,
            'status'=>TlDeliveryProposalBilling::STATUS_ACTIVE
        ]);
    }

    /**
     * @return string route title
     */
    public function getRouteTitle($route_id=null){
        if(is_null($route_id)){
            return 'Не задан';
        }
        return ArrayHelper::getValue($this->getRouteFromTo(), $route_id);
    }

    /**
     * @return string country name
     */
    public function getCountryName($country_id=null){
        if(!is_null($country_id)){
            $country = Country::findOne($country_id);
            if(!empty($country))
            return $country->name;
        }
        return 'Не задан';
    }

    /**
     * @return string region name
     */
    public function getRegionName($region_id=null){
        if(!is_null($region_id)){
            $region = Region::findOne($region_id);
            if(!empty($region))
            return $region->name;
        }
        return 'Не задан';
    }

    /**
     * @return string city name
     */
    public function getCityName($city_id=null){
        if(!is_null($city_id)){
            $city = City::findOne($city_id);
            if(!empty($city))
            return $city->name;
        }
        return 'Не задан';
    }

    /*
   * Array with attribute values functions mapping
   * @return array
   **/
    public function getAttributesValuesMap($attribute)
    {
        $data = [
            'route_to'=>'getRouteTitle',
            'route_from'=>'getRouteTitle',
            'rule_type'=>'getRuleType',
            'status'=>'getStatus',
            'tariff_type'=>'getTariffType',
            'cooperation_type'=>'getCooperationType',
            'delivery_type'=>'getDeliveryType',
            'from_country_id'=>'getCountryName',
            'to_country_id'=>'getCountryName',
            'from_city_id'=>'getCityName',
            'to_city_id'=>'getCityName',
            'from_region_id'=>'getRegionName',
            'to_region_id'=>'getRegionName',
        ];

        return ArrayHelper::getValue($data, $attribute);
    }
}
