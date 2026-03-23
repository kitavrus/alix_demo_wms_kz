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
use yii\i18n\MessageFormatter;

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

        $managersNamesTo = '';
        if($routeTo = $model->routeTo) {
            $clientEmployees = ClientEmployees::find()
                ->andWhere([
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





        $storeFrom = $model->routeFrom;

        $endPointAddress = $model->routeTo->getPointTitleByPattern('{city_name} / {street} {house}');
        $endPointCompanyName = $model->routeTo->getPointTitleByPattern('{name}');

        $day = Yii::$app->formatter->asDatetime($model->shipped_datetime,'php:d');
        $monthYear = Yii::$app->formatter->asDatetime($model->shipped_datetime,'php:F Y');
        $dateTime = [];
        $dateTime['day'] = $day;
        $dateTime['monthYear'] = $monthYear;
        $ttnNumber = $model->id;
        $clientName = Client::findOne($model->client_id)->legal_company_name;
        $positionUser = 'начь. склада';
        $passedUserName = 'Ерболатов М.Е';


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


        $outboundOrderItems = [];
        $outboundOrderItems['products'] = [];
        $outboundOrderItems['totalProductQty'] = 0;
        $outboundOrderItems['totalBoxQty'] = 0;
        $totalBoxList = [];

        if ($relatedOrders = $model->proposalOrders) {
            foreach ($relatedOrders as $order) {
                if ($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
                    if ($oo = $order->outboundOrder) {

                        $oo->date_delivered = $model->delivery_date;
                        $oo->status = Stock::STATUS_OUTBOUND_COMPLETE;
                        $oo->save(false);

                        $mapPBarcodePName = ArrayHelper::map($oo->orderItems,'product_barcode','product_name');

                        $stockOrderItems = Stock::find()
                            ->select('product_barcode, box_barcode, count(product_barcode) as product_barcode_count')
                            ->andWhere([
                                        'outbound_order_id'=> $oo->id,
                                        'status'=>[Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL]
                            ])
                            ->groupBy('product_barcode, box_barcode')
                            ->orderBy('box_barcode,product_barcode')
                            ->all();

                        if($stockOrderItems) {
                            foreach($stockOrderItems as $orderItem) {
                                $outboundOrderItems['products'][] = [
                                    'productName'=>ArrayHelper::getValue($mapPBarcodePName,$orderItem->product_barcode),
                                    'productBarcode'=>$orderItem->product_barcode,
                                    'boxBarcode'=>$orderItem->box_barcode,
                                    'acceptedQty'=>$orderItem->product_barcode_count,
                                    'orderNumber'=>$oo->order_number,
                                ];

                                $outboundOrderItems['totalProductQty'] += $orderItem->product_barcode_count;
                                $totalBoxList[$orderItem->box_barcode] = $orderItem->box_barcode;
                            }
                        }

                    }
                }
            }
        }

        $outboundOrderItems['totalBoxQty'] = count($totalBoxList);
//        $outboundOrderItems['totalBoxQtyText'] =  Yii::t('app','{0,spellout}',[$outboundOrderItems['totalBoxQty']]);
        $outboundOrderItems['totalBoxQtyText'] = Yii::$app->formatter->asSpellout($outboundOrderItems['totalBoxQty']);

        return $this->render('new/print-ttn-pdf',[
            'model'=>$model,
            'outboundOrderItems'=>$outboundOrderItems,
            'endPointAddress'=>$endPointAddress,
            'endPointCompanyName'=>$endPointCompanyName,
            'dateTime'=>$dateTime,
            'ttnNumber'=>$ttnNumber,
            'clientName'=>$clientName,
            'passedUserName'=>$passedUserName,
            'positionUser'=>$positionUser,
            'managersNamesTo'=>$managersNamesTo,
        ]);

    }


     /*
         * Print TTN
         *
         * */
    public function actionPrintTtn_old()
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


        $outboundOrderItems = [];

        if ($relatedOrders = $model->proposalOrders) {
            foreach ($relatedOrders as $order) {
                if ($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
                    if ($oo = $order->outboundOrder) {
                        $oo->date_delivered = $model->delivery_date;
                        $oo->status = Stock::STATUS_OUTBOUND_COMPLETE;
                        $oo->save(false);

                        $orderItems = $oo->orderItems;

                        if($orderItems) {
                            foreach($orderItems as $orderItem) {
                                $outboundOrderItems[] = [
                                    'productName'=>$orderItem->product_name,
                                    'productBarcode'=>$orderItem->product_barcode,
                                    'acceptedQty'=>$orderItem->accepted_qty,
                                ];
                            }

                        }

                    }
                }
            }
        }

//        VarDumper::dump($orderItems,10,true);
//        VarDumper::dump($outboundOrderItems,10,true);
//        die;

        return $this->render('print-ttn-pdf',[
            'model'=>$model,
            'userName'=>$userName,
            'managersNamesTo'=>$managersNamesTo,
            'outboundOrderItems'=>$outboundOrderItems,
        ]);
    }
}