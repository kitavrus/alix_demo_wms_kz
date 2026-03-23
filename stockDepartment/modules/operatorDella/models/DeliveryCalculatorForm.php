<?php

namespace app\modules\operatorDella\models;

use Yii;
use yii\base\Model;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\billing\components\BillingManager;


/**
 * This is the model class for table "transportation_tariff_company_lead".
 *
 * @property integer $id
 * @property string $customer_name
 * @property string $customer_company_name
 * @property string $customer_position
 * @property string $customer_phone
 * @property string $customer_email
 * @property integer $status
 * @property integer $cooperation_type_1
 * @property integer $cooperation_type_2
 * @property integer $cooperation_type_3
 * @property string $customer_comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class DeliveryCalculatorForm extends Model
{
    public $client_id;
    public $city_to;
    public $city_from;
    public $weight;
    public $volume;
    public $delivery_type;
    public $places;

    public function attributeLabels()
    {
        return [
            'city_from' => Yii::t('frontend/forms', 'From city'),
            'delivery_type' => Yii::t('frontend/forms', 'Delivery type'),
            'city_to' => Yii::t('frontend/forms', 'To city'),
            'weight' => Yii::t('frontend/forms', 'Weight(kg)'),
            'volume' => Yii::t('frontend/forms', 'Volume(м³)'),
            'places' => Yii::t('frontend/forms', 'Places'),

        ];
    }

    public function rules()
    {
        return [
            [['weight','volume', 'city_from', 'city_to', 'delivery_type'], 'required'],
            [['client_id', 'places'], 'integer'],
            [['weight','volume'], 'filter', 'filter' => function ($value) {
                $value = trim ($value);
                $value = str_replace(',','.', $value);
                return $value;
            }],
            //[['city_to'], 'compare', 'compareAttribute'=>'city_from', 'operator'=>'!='],
        ];
    }

    /**
     * @return array Массив с типами доставки
     */
    public static function getDeliveryTypeArray($key = null)
    {
        $data = [
           TlDeliveryProposalBilling::DELIVERY_TYPE_WAREHOUSE_WAREHOUSE => Yii::t('forms', 'Warehouse-warehouse'),
           TlDeliveryProposalBilling::DELIVERY_TYPE_DOOR_DOOR => Yii::t('forms', 'Door-Door'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * Массив с доступными по умолчанию городами
     * для поля "Откуда"
     * @param int $tariff_type
     * @return array
     * */
    public static function getDefaultRoutesFrom($tariff_type)
    {
        $tariffs = TlDeliveryProposalBilling::findAll([
            'tariff_type'=>$tariff_type,
            'status'=>TlDeliveryProposalBilling::STATUS_ACTIVE
        ]);

        $data=[];

        if(!empty($tariffs)){
            foreach($tariffs as $tariff){
                if(is_object($tariff->city)){
                    $data[$tariff->city->id] = $tariff->city->name;
                }

            }
        }
        return $data;
    }

    /**
     * Массив с доступными по умолчанию городами
     * для поля "Куда"
     * @param int $tariff_type
     * @return array
     * */
    public static function getDefaultRoutesTo($tariff_type)
    {
        $tariffs = TlDeliveryProposalBilling::findAll([
            'tariff_type'=>$tariff_type,
            'status'=>TlDeliveryProposalBilling::STATUS_ACTIVE,
        ]);

        $data=[];

        if(!empty($tariffs)){
            foreach($tariffs as $tariff){
                if(is_object($tariff->cityTo)) {
                    $data[$tariff->cityTo->id] = $tariff->cityTo->name;
                }
            }
        }
        return $data;
    }


    /**
     * Высчитывает стоимость доставки
     * @params boolean $deliveryTerm
     * @return string
     * */
    public function calculateDeliveryCost($deliveryTerm = false)
    {
        $price = 0;
        $nds = true;
        $deliveryDateStr = '';
        $kg = $this->weight;
        $mc = $this->volume;
        $pl = $this->places;
        $index = BillingManager::getWeightVolumeIndex();
        $weightVolumeIndex = $mc * $index;
        if($this->city_from == $this->city_to) {
            if($billing=TlDeliveryProposalBilling::findOne([
                'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                'from_city_id' => $this->city_from,
                'to_city_id' => $this->city_to,
                'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_BY_CONDITION,

            ])) {
                $deliveryDateStr = $billing->delivery_term;
                if($conditions = $billing->getConditions()->all()) {
                    foreach($conditions as $c) {
                        if($c->formula_tariff){
                            $r = '';
                            $e = '$r = ('.str_replace(['mc', 'kg', 'pl'],['$mc', '$kg', '$pl'], $c->formula_tariff).');';
                            eval($e);
                            if($r) {
                                if($nds) {
                                    $price = $c->price_invoice_with_vat;
                                } else {
                                    $price = $c->price_invoice;
                                }
                                break;
                            } else {
                                if($nds) {
                                    $price = $billing->price_invoice_with_vat;
                                } else {
                                    $price = $billing->price_invoice;
                                }
                            }
                        }
                    }
                }
            }
//            elseif($billing=TlDeliveryProposalBilling::findOne([
//                'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
//                'from_city_id' => $this->city_from,
//                'to_city_id' => $this->city_to,
//                'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_BY_POINT,
//            ])){
//                if($nds) {
//                    $price = $billing->price_invoice_with_vat;
//                } else {
//                    $price = $billing->price_invoice;
//                }
//            }
        } else {
            if($weightVolumeIndex >= $kg) {
                        if($billing=TlDeliveryProposalBilling::findOne([
                            'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                            'from_city_id' => $this->city_from,
                            'to_city_id' => $this->city_to,
                            'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,
                        ])){
                            $deliveryDateStr = $billing->delivery_term;
                            if($nds) {
                                $price = $weightVolumeIndex * $billing->price_invoice_kg_with_vat;
                            } else {
                                $price = $weightVolumeIndex * $billing->price_invoice_kg;
                            }
                }
            } elseif ($weightVolumeIndex < $kg){
                if($billing=TlDeliveryProposalBilling::findOne([
                    'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                    'from_city_id' => $this->city_from,
                    'to_city_id' => $this->city_to,
                    'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,
                ])){
                    $deliveryDateStr = $billing->delivery_term;
                    if($nds) {
                        $price = $kg * $billing->price_invoice_kg_with_vat;
                    } else {
                        $price = $kg * $billing->price_invoice_kg;
                    }
                }
            }
        }

        if($deliveryTerm) {
            return $deliveryDateStr;
        }

        return Yii::$app->formatter->asDecimal($price, 2);


//        if (!empty($billing)) {
//            $conditions = $billing->getDefaultConditions();
//            if (!empty($conditions)) {
//                foreach ($conditions as $c) {
//                    $r = '';
//                    $e = '$r = (' . str_replace('kg', '$kg', $c->formula_tariff) . ');';
//                    eval($e);
//                    if ($r) {
//                        $index = $c->price_invoice_with_vat;
//                    }
//                }
//
//                $price = $kg*$index;
//            }
//        }

    }
}
