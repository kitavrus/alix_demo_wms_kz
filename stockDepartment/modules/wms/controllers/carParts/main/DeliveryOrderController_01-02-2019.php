<?php

namespace app\modules\wms\controllers\carParts\main;

use common\clientObject\constants\Constants;
use common\clientObject\main\inbound\service\InboundServiceReport;
use common\clientObject\deliveryProposal\service\DeliveryOrderService;
use common\clientObject\main\outbound\service\OutboundServiceReport;
use common\components\DeliveryProposalManager;
use common\helpers\DateHelper;
use common\modules\city\models\RouteDirections;
use common\modules\client\models\Client;
//use common\modules\outbound\models\OutboundOrderItem;
use common\modules\client\models\ClientEmployees;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use stockDepartment\components\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class DeliveryOrderController extends Controller
{
    /*
         * Print TTN
         *
         * */
    public function actionPrintTtn()
    {
        $id = Yii::$app->request->get('id');
        $model = TlDeliveryProposal::findOne($id);

        $userName = '';
        $storeFrom = $model->routeFrom;
        $managersNamesTo = 'Контакты получателей:<br />';
        if($routeTo = $model->routeTo) {
            // находим всех директоров магазина и отправляем им имейлы
            $clientEmployees = ClientEmployees::find()
                ->andWhere([
//                    'deleted'=>0,
                    'client_id'=>$model->client_id,
                    'store_id'=>$routeTo->id,
                    'manager_type'=>[
                        ClientEmployees::TYPE_BASE_ACCOUNT,
                        ClientEmployees::TYPE_DIRECTOR,
                        ClientEmployees::TYPE_DIRECTOR_INTERN,
                    ]
                ])
                ->all();

            foreach($clientEmployees as $item) {
                $managersNamesTo .= $item->first_name.' '.$item->last_name.' / '.$item->phone_mobile.' '.$item->phone."<br />";
            }
            $managersNamesTo .= $routeTo->phone_mobile.' / '.$routeTo->phone."<br />";
        }

        // если отправляем груз со склада, то печатаем 3 копии файла ТТН
        // 4 = DC - это наш склад
        if(in_array($storeFrom->id,[4])) {
            $model->shipped_datetime = DateHelper::getTimestamp();
            $model->delivery_date = DateHelper::getTimestamp();
            $model->status = TlDeliveryProposal::STATUS_ON_ROUTE;
            $model->save(false);

        }

        $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
        $dpManager->onPrintTtn();
        $dpManager->setCascadeDeliveryDate();
        $dpManager->setCascadedStatus();


        if ($relatedOrders = $model->proposalOrders) {
            foreach ($relatedOrders as $order) {
                if ($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
                    if ($oo = $order->outboundOrder) {
                        $oo->date_delivered = $model->delivery_date;
                        $oo->status = Stock::STATUS_OUTBOUND_COMPLETE;
                        $oo->save(false);
                    }
                }
            }
        }


        return $this->render('print-ttn-pdf',['model'=>$model,'userName'=>$userName,'managersNamesTo'=>$managersNamesTo]);
    }
}