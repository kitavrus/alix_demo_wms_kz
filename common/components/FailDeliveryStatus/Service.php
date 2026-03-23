<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.01.2019
 * Time: 18:36
 */

namespace common\components\FailDeliveryStatus;


use common\modules\crossDock\models\CrossDock;
use common\modules\outbound\models\OutboundOrder;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class Service
{
    static function add($data) {

        $deliveryProposalId = ArrayHelper::getValue($data,'deliveryProposalId');

        if (($deliveryProposal = TlDeliveryProposal::findOne($deliveryProposalId)) == null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $orderList = $deliveryProposal->proposalOrders;

        if($orderList) {
            foreach ($orderList as $orderLine) {
                if($orderLine->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
                  $order = OutboundOrder::findOne($orderLine->order_id);
                }elseif($orderLine->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK) {
                  $order = CrossDock::findOne($orderLine->order_id);
                }

                if(isset($order)) {
                    $order->fail_delivery_status = self::makeStatus($data);
                    $order->save(false);

                    $deliveryProposal->fail_delivery_status = self::makeStatus($data);
                    $deliveryProposal->save(false);
                }
            }
        }


//        VarDumper::dump($orderList,10,true);
//        die;
        return  isset($order) && $deliveryProposal;
    }

    private static function makeStatus($data) {
        return Json::encode($data);
    }
}