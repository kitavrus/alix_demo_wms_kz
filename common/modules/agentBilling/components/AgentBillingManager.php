<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 07.11.14
 * Time: 12:39
 */
namespace common\modules\agentBilling\components;

use common\components\MathUtility;
use common\modules\city\models\City;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use Yii;
use common\modules\agentBilling\models\TlAgentBilling;
use common\modules\agentBilling\models\TlAgentBillingConditions;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\client\models\ClientSettings;
use common\modules\client\models\Client;
use yii\helpers\VarDumper;
use common\components\MailManager;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;

class AgentBillingManager {

    /*
     * @param model $dRouteCar Delivery Proposal Route car
     * @param model $dp Delivery Proposal
     * @return number Invoice Price
     * @return boolean $nds Get price data with nds
     * */
    public static function getInvoicePriceForCar(TlDeliveryProposalRouteCars $dRouteCar, $from_id, $to_id, $dpID ,$nds = true)
    {
        $price = 0;

        if(!$from_id || !$to_id || !$dRouteCar) {

            $agent_id = 0;
            if($dRouteCar) {
                $agent_id = $dRouteCar->agent_id;
            }

            $mManager = new MailManager();
            $mManager->sendAgentTariffMissingWarningMail($agent_id,$from_id,$to_id,$dpID);
            return $price;
        }

        if($agentBilling = TlAgentBillingConditions::findOne([
            'agent_id' => $dRouteCar->agent_id,
            'route_from' => $from_id,
            'route_to' => $to_id,
            'status' => TlAgentBillingConditions::STATUS_ACTIVE

        ])){

            switch($agentBilling->rule_type) {

                case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_KG:
                    if($nds) {
                        $price = $agentBilling->price_invoice_with_vat * $dRouteCar->kg_filled;
                    } else {
                        $price = $agentBilling->price_invoice * $dRouteCar->kg_filled;
                    }
                    break;

                case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_MC:
                    if($nds) {
                        $price = $agentBilling->price_invoice_with_vat * $dRouteCar->mc_filled;
                    } else {
                        $price = $agentBilling->price_invoice * $dRouteCar->mc_filled;
                    }
                    break;

                case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_UNIT:
                    if($nds) {
                        $price = $agentBilling->price_invoice_with_vat;
                    } else {
                        $price = $agentBilling->price_invoice;
                    }
                    break;

                case TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_WEIGHT_VOLUME_INDEX:
                    $index = self::getWeightVolumeIndex();
                    $weightVolumeIndex = $dRouteCar->mc_filled * $index;

                    if($weightVolumeIndex >= $dRouteCar->kg_filled){

                        if($nds) {
                            $price = $weightVolumeIndex * $agentBilling->price_invoice_with_vat;
                        } else {
                            $price = $weightVolumeIndex * $agentBilling->price_invoice;
                        }

                    } elseif ($weightVolumeIndex < $dRouteCar->kg_filled){
                        if($nds) {
                            $price = $dRouteCar->kg_filled * $agentBilling->price_invoice_with_vat;
                        } else {
                            $price = $dRouteCar->kg_filled * $agentBilling->price_invoice;
                        }
                    }

                    break;
            }
        } else {
            $mManager = new MailManager();
            $mManager->sendAgentTariffMissingWarningMail($dRouteCar->agent_id,$from_id,$to_id,$dpID);
        }

        return $price;
    }

    /*
     * Возвращает индекс обьема\веса
     * если указать ID клиента, поиск индекса будет
     * осуществлятся в ClientSettings
     * В противном случае берется значение из params
     *
     * @return int index
     * */
    public static function getWeightVolumeIndex()
    {
        return Yii::$app->params['weightVolumeIndex'];
    }


}