<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 07.11.14
 * Time: 12:39
 */
namespace common\modules\billing\components;

use common\components\MathUtility;
use common\modules\city\models\City;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\client\models\ClientSettings;
use common\modules\client\models\Client;
use yii\helpers\VarDumper;
use common\components\MailManager;

class BillingManager {

    /*
     * @var number
     *
     * */
    const NDS = 12; // 12%
    const CALCULATE_NDS = 1.12; // 50/1.12 = %

    /*
     * @param model $dp Delivery Proposal
     * @return number Invoice Price
     * @return boolean $nds Get price data with nds
     * */
    public function getInvoicePriceForDP($dp,$nds = true)
    {
        $price = 0;
        if(in_array($dp->company_transporter,self::getTransportCompanyOutTariff())) {
            return $price;
        }


        $client = $dp->client;
        if(!is_object($dp->routeFrom) || !is_object($dp->routeTo) ){
            return $price;
        }
        if($client->client_type==Client::CLIENT_TYPE_CORPORATE_CONTRACT){
            if($billing=TlDeliveryProposalBilling::findOne([
                'client_id'=>$client->id,
                'tariff_type'=>TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
                'route_from' =>$dp->routeFrom->id,
                'route_to' =>$dp->routeTo->id
            ])){
                switch($billing->rule_type) {
                    case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_KG:
                        if($nds) {
                            $price = $billing->price_invoice_with_vat*$dp->kg_actual;
                        } else {
                            $price = $billing->price_invoice*$dp->kg_actual;
                        }

                        break;
                    case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_MC:
                        if($nds) {
                            $price = $billing->price_invoice_with_vat*$dp->mc_actual;
                        } else {
                            $price = $billing->price_invoice*$dp->mc_actual;
                        }
                        break;
                    case TlDeliveryProposalBilling::RULE_TYPE_BY_POINT:
                        if($nds) {
                            $price = $billing->price_invoice_with_vat;
                        } else {
                            $price = $billing->price_invoice;
                        }
                        break;

                    case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_CONDITION_MC:
                    case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_CONDITION_KG:
                        if($conditions = $billing->getConditions()->all()) {
                            foreach($conditions as $c) {
                                $r = '';
                                $e = '$r = ('.str_replace(['mc'],['$dp->mc_actual'],$c->formula_tariff).');';
                                eval($e);
                                if($r) {
                                    if($nds) {
                                        $price = $c->price_invoice_with_vat;
                                    } else {
                                        $price = $c->price_invoice;
                                    }
                                    break;
                                }
                            }
                        }

                        break;
                }
            } elseif($billing=TlDeliveryProposalBilling::findOne([
                'client_id'=>$client->id,
                'tariff_type'=>TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
            ])){
               $mManager = new MailManager();
               $mManager->sendTariffMissingWarningMail($dp);
            }

        } elseif ($client->client_type==Client::CLIENT_TYPE_PERSONAL){
            $from = $dp->routeFrom;
            $to = $dp->routeTo;
            if($from->city_id == $to->city_id){
                    if($billing=TlDeliveryProposalBilling::findOne([
                        'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                        'from_city_id' => $from->city_id,
                        'to_city_id' => $to->city_id,
                        'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_BY_CONDITION,

                    ])){
                        if($conditions = $billing->getConditions()->all()) {
                            foreach($conditions as $c) {
                                if($c->formula_tariff){
                                    $r = '';
                                    $e = '$r = ('.str_replace(['mc', 'kg', 'pl'],['$dp->mc_actual', '$dp->kg_actual', '$dp->number_places_actual'], $c->formula_tariff).');';
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
//                    elseif($billing=TlDeliveryProposalBilling::findOne([
//                    'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
//                    'from_city_id' => $from->city_id,
//                    'to_city_id' => $to->city_id,
//                    'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_BY_POINT,
//
//                ])){
//                    if($nds) {
//                        $price =  $billing->price_invoice_with_vat;
//                    } else {
//                        $price = $billing->price_invoice;
//                    }
//
//                }

            } else {
                $index = self::getWeightVolumeIndex($dp->client_id);
                $weightVolumeIndex = $dp->mc_actual * $index;
                if($weightVolumeIndex >= $dp->kg_actual){
                    if($billing=TlDeliveryProposalBilling::findOne([
                        'client_id'=>$client->id,
                        'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_INDIVIDUAL,
                        'from_city_id' => $dp->routeFrom->city_id,
                        'to_city_id' => $dp->routeTo->city_id,
                        'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,

                    ])){
                        if($nds) {
                            $price = $weightVolumeIndex * $billing->price_invoice_kg_with_vat;
                        } else {
                            $price = $weightVolumeIndex * $billing->price_invoice_kg;
                        }

                    } elseif($billing=TlDeliveryProposalBilling::findOne([
                        'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                        'from_city_id' => $dp->routeFrom->city_id,
                        'to_city_id' => $dp->routeTo->city_id,
                        'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,
                    ])){
                        $dp->kg = $weightVolumeIndex;
                        $dp->kg_actual = $weightVolumeIndex;
                        if($nds) {
                            $price = $weightVolumeIndex * $billing->price_invoice_kg_with_vat;
                        } else {
                            $price = $weightVolumeIndex * $billing->price_invoice_kg;
                        }
                    }
                } elseif ($weightVolumeIndex < $dp->kg_actual){
                    if($billing=TlDeliveryProposalBilling::findOne([
                        'client_id'=>$client->id,
                        'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_INDIVIDUAL,
                        'from_city_id' => $dp->routeFrom->city_id,
                        'to_city_id' => $dp->routeTo->city_id,
                        'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,

                    ])){
                        if($nds) {
                            $price = $dp->kg_actual * $billing->price_invoice_kg_with_vat;
                        } else {
                            $price = $dp->kg_actual * $billing->price_invoice_kg;
                        }
                    } elseif($billing=TlDeliveryProposalBilling::findOne([
                        'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                        'from_city_id' => $dp->routeFrom->city_id,
                        'to_city_id' => $dp->routeTo->city_id,
                        'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,
                    ])){
                        if($nds) {
                            $price = $dp->kg_actual * $billing->price_invoice_kg_with_vat;
                        } else {
                            $price = $dp->kg_actual * $billing->price_invoice_kg;
                        }
                    }
                }
            }


        }


        return $price;
    }
    /*TODO: добавить опцию НДС
    * @param model $dp Delivery Proposal
    * @return number Invoice Price
    * */
    public function getInvoicePriceForDP_NEW($dp)
    {
        $price = 0;
        $client = $dp->client;
        $from = $dp->routeFrom;
        $to = $dp->routeTo;
        $tariff='';

        //Для юр. лиц по контракту (Colins, DeFacto)
       if ($client->client_type == Client::CLIENT_TYPE_CORPORATE_CONTRACT) {

           //ищем персональные тарифы
           $tariff = TlDeliveryProposalBilling::findOne([
               'client_id' => $client->id,
               'route_from' => $from->id,
               'route_to' => $to->id,
               'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
           ]);
           //если не нашли, пробуем найти тариф для этого же клиента, но без привязки к точкам для оповещения по email
           if(!$tariff){
              if( $tariff = TlDeliveryProposalBilling::findOne([
                   'client_id' => $client->id,
                   'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
               ])){

                  //если нашли - отправляем email
                  $mManager = new MailManager();
                  $mManager->sendTariffMissingWarningMail($dp);
              }
           }
            //для физ. лиц
       } elseif ($client->client_type == Client::CLIENT_TYPE_PERSONAL) {
           //ищем индивидуальный тариф для физ. лиц
           $tariff = TlDeliveryProposalBilling::findOne([
               'client_id' => $client->id,
               'route_city_from' => $from->city_id,
               'route_city_to' => $to->city->id,
               'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_INDIVIDUAL,
           ]);
           //если не нашли, то берем дефолтный для этих городов
           if(!$tariff){
               $tariff = TlDeliveryProposalBilling::findOne([
                   'route_city_from' => $from->city_id,
                   'route_city_to' => $to->city->id,
                   'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
               ]);
           }
       }

        //высчитываем стоимость доставки по найденному тарифу
        if ($tariff){
           $price = $this->calculatePriceByTariff($dp, $tariff);
        }

        return $price;
    }


    /*
     * Calculate NDS
     * @param number $price
     * @param integer $decimals the number of digits after the decimal point. If not given the number of digits is determined from the
     * @return
     * */
    public function calculateNDS($price,$decimals = 2)
    {
        if(!empty($price)) {
            $price = MathUtility::prepare($price, $decimals);
            $p = ($price+(($price/100) * self::NDS));
            return MathUtility::prepare($p,$decimals);
        }

        return 0;
    }
    /*
     * Calculate with out NDS
     * @param number $price
     * @param integer $decimals the number of digits after the decimal point. If not given the number of digits is determined from the
     * @return
     * */
    public function calculateWithOutNDS($price,$decimals = 2)
    {
        if(!empty($price)) {
            return (MathUtility::prepare($price, $decimals) / self::CALCULATE_NDS);
        }
        return 0;
    }

    /*
     * Get transport company not calculated tariff
     * @return array Ids Transport company
     * */
    public static function getTransportCompanyOutTariff()
    {
        return [
            TlDeliveryProposal::COMPANY_TRANSPORTER_RLC,
            TlDeliveryProposal::COMPANY_TRANSPORTER_APIS,
        ];
    }

    /*
     * Возвращает индекс обьема\веса
     * если указать ID клиента, поиск индекса будет
     * осуществлятся в ClientSettings
     * В противном случае берется значение из params
     *
     * @return int index
     * */
    public static function getWeightVolumeIndex($client_id=null)
    {
           $cSetting = ClientSettings::findOne(['client_id'=>$client_id, 'option_name'=>'weightVolumeIndex']);
            if(!empty($cSetting)){
                return $cSetting->default_value;
            }


        return Yii::$app->params['weightVolumeIndex'];
    }

    /*
     * Высчитывает стоимость доставки для переданной заявки
     * согласно переданному тарифу
     * @param TlDeliveryProposal $dp
     * @param TlDeliveryProposalBilling $tariff
     * @return int $price
     * */
    public static function calculatePriceByTariff(TlDeliveryProposal $dp, TlDeliveryProposalBilling $tariff)
    {
        $price = 0;
        if(in_array($dp->company_transporter,self::getTransportCompanyOutTariff())) {
            return $price;
        }
        //определяем индекс объемного веса
        $weightVolumeIndex = self::getWeightVolumeIndex($dp->client_id);

       switch ($tariff->rule_type){

           case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_WEIGHT_VOLUME_INDEX:
           case TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX:

               if($dp->kg_actual && $dp->mc_actual){
                   $dpIndex = $dp->kg_actual / $dp->mc_actual;

                   if ($dpIndex <=  $weightVolumeIndex){
                       $price = $dp->mc_actual * $tariff->price_invoice_mc_with_vat;
                   } elseif ($dpIndex >  $weightVolumeIndex) {
                       $price = $dp->kg_actual * $tariff->price_invoice_kg_with_vat;
                   }

               }
               break;
           case TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_KG:
           case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_KG:
               $price = $dp->kg_actual * $tariff->price_invoice_kg_with_vat;
               break;
           case TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_MC:
           case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_MC:
               $price = $dp->mc_actual * $tariff->price_invoice_mc_with_vat;
               break;
           case TlDeliveryProposalBilling::RULE_TYPE_BY_POINT:
               $price = $tariff->price_invoice_mc_with_vat;
               break;
           case TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_CONDITION_MC:
           case TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_CONDITION_KG:
           case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_CONDITION_MC:
           case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_CONDITION_KG:
               if($conditions = $tariff->getConditions()->all()) {
                   foreach($conditions as $c) {
                       $r = '';
                       $e = '$r = ('.str_replace(['mc'],['$dp->mc_actual'],$c->formula_tariff).');';
                       eval($e);
                       if($r) {
                         $price = $c->price_invoice_with_vat;
                       break;
                       }
                   }
               }

               break;
       }

        return $price;
    }
}