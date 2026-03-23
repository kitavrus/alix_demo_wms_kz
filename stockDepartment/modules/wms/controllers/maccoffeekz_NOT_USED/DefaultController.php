<?php

namespace app\modules\wms\controllers\maccoffeekz;

use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\stock\models\Stock;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;

class DefaultController extends Controller
{
    const CLIENT_ID = \common\modules\client\models\Client::CLIENT_MACCOFFEEKZ;

    public function actionIndex()
    {

        return $this->render('index');
    }
    /*
     *
     * */
    public function actionAddTestData()
    {
        // add-test-data

        $pathToCSVFile = 'tmp-file/Akmaral/MC_005(2).csv';
//        $row = 0;
        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
            $parsedData = [];
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                if(isset($parsedData[$data['1']])) {
                    $parsedData[$data['1']] = [
                        'model' => $data['4'],
                        'name' => $data['3'],
                        'barcode' => $data['1'],
                        'qty' => $parsedData[$data['1']]['qty']+(int)preg_replace('/[^\d]+/', '', $data['7'])
                    ];
                } else {
                    $parsedData[$data['1']] = [
                        'model' => $data['4'],
                        'name' => $data['3'],
                        'barcode' => $data['1'],
                        'qty' =>  (int)preg_replace('/[^\d]+/', '', $data['7'])
                    ];
                }
            }
        }
//        VarDumper::dump($parsedData,10,true);
//        die;

        $client_id = self::CLIENT_ID;
        $order_number = 'TestMac-1';
        $consignmentInboundOrder = new ConsignmentInboundOrders();
        $consignmentInboundOrder->client_id = $client_id;
        $consignmentInboundOrder->party_number = $order_number;
        $consignmentInboundOrder->delivery_type = InboundOrder::DELIVERY_TYPE_RPT;
        $consignmentInboundOrder->order_type = InboundOrder::ORDER_TYPE_INBOUND;
        $consignmentInboundOrder->status = Stock::STATUS_INBOUND_NEW;
        $consignmentInboundOrder->save(false);

        $inboundModel = new InboundOrder();
        $inboundModel->client_id = $client_id;
        $inboundModel->consignment_inbound_order_id = $consignmentInboundOrder->id;
        $inboundModel->order_number = $order_number;
        $inboundModel->parent_order_number = $order_number;
        $inboundModel->status = Stock::STATUS_INBOUND_NEW;
        $inboundModel->expected_qty = '0';
        $inboundModel->accepted_qty = '0';
        $inboundModel->accepted_number_places_qty = '0';
        $inboundModel->expected_number_places_qty = '0';
        $inboundModel->order_type = InboundOrder::ORDER_TYPE_INBOUND;
        $inboundModel->save(false);

        $inboundModelID = $inboundModel->id;
        $expectedQty = 0;
        foreach ($parsedData as $productData) {

            $ioi = new InboundOrderItem();
            $ioi->inbound_order_id = $inboundModelID;
            $ioi->product_barcode = $productData['barcode'];
            $ioi->product_name = $productData['name'];
//            $ioi->product_model = $productData['model'];
            $ioi->expected_qty = $productData['qty'];
            $ioi->status = Stock::STATUS_INBOUND_NEW;
            $ioi->save(false);

            $expectedQty += $ioi->expected_qty;

            for ($i = 1; $i <= $ioi->expected_qty; $i++) {

                $stock = new Stock();
                $stock->client_id = $client_id;
                $stock->inbound_order_id = $ioi->inbound_order_id;
                $stock->product_barcode = $ioi->product_barcode;
//                $stock->product_model = $ioi->product_model;
                $stock->product_name = $ioi->product_name;
                $stock->status = Stock::STATUS_INBOUND_NEW;
                $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
                $stock->save(false);
            }
        }

        InboundOrder::updateAll(['expected_qty' => $expectedQty], ['id' => $inboundModelID]);
        echo "<br />" . "Приходная накладная успешно создана" . "<br />";

        return $this->render('index');
    }
}
