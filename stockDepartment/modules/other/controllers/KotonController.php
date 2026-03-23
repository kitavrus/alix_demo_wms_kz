<?php

namespace stockDepartment\modules\other\controllers;

use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;
use Yii;
use common\modules\client\models\Client;
use common\modules\transportLogistics\components\TLHelper;
use stockDepartment\modules\other\models\OutboundOrderGridSearch;
use yii\data\ActiveDataProvider;
use stockDepartment\modules\other\models\StockRemains;
use yii\helpers\VarDumper;


class KotonController extends \stockDepartment\components\Controller
{
    public $transferKotonOrders = [
      '1640'=>'transfer-stock-3-5',
      '1641'=>'transfer-stock-3-6',
      '1642'=>'transfer-stock-3-7',
      '1643'=>'transfer-stock-3-8',
      '1644'=>'transfer-stock-3-9',
      '1645'=>'transfer-stock-3-10',
      '1646'=>'transfer-stock-3-11',
      '1647'=>'transfer-stock-3-12',
      '1648'=>'transfer-stock-3-13',
      '1649'=>'transfer-stock-3-14',
      '1650'=>'transfer-stock-3-15',
      '1651'=>'transfer-stock-3-16',
      '1652'=>'transfer-stock-3-17',
      '1653'=>'transfer-stock-3-18',
      '1654'=>'transfer-stock-3-19',
      '1655'=>'transfer-stock-3-20',
      '1656'=>'transfer-stock-3-21',
      '1657'=>'transfer-stock-3-22',
      '1658'=>'transfer-stock-3-23',
      '1659'=>'transfer-stock-3-24',
      '1660'=>'transfer-stock-3-25',
      '1661'=>'transfer-stock-3-26',
      '1662'=>'transfer-stock-3-27',
      '1663'=>'transfer-stock-3-28',
      '1664'=>'transfer-stock-3-29',
//      '1665'=>'transfer-stock-4',
    ];

    public function actionIndex2()
    {
        $np = 'common\modules\audit\models';
        $array = [
            'cross_dock_audit'=>'CrossDockAudit',
            'cross_dock_items_audit'=>'CrossDockItemsAudit',
            'cross_dock_log'=>'',
            'inbound_orders_audit'=>'InboundOrderAudit',
            'inbound_order_items_audit'=>'InboundOrderItemAudit',
            'inbound_upload_log'=>'',
            'outbound_orders_audit'=>'OutboundOrderAudit',
            'outbound_order_items_audit'=>'OutboundOrderItemAudit',
            'outbound_picking_lists_audit'=>'OutboundPickingListsAudit',
            'outbound_upload_items_log'=>'',
            'outbound_upload_log'=>'',
            'stock_audit'=>'StockAudit',
            'store_audit'=>'StoreAudit',
            'store_reviews_audit'=>'StoreReviewsAudit',
            'tl_agents_audit'=>'TlAgentsAudit',
            'tl_agents_billing_audit'=>'TlAgentBillingAudit',
            'tl_agents_billing_conditions_audit'=>'TlAgentBillingConditionsAudit',
            'tl_delivery_proposals_audit'=>'TlDeliveryProposalAudit',
            'tl_delivery_proposal_billing_audit'=>'TlDeliveryProposalBillingAudit',
            'tl_delivery_proposal_billing_conditions_audit'=>'TlDeliveryProposalBillingConditionsAudit',
            'tl_delivery_proposal_orders_audit'=>'TlDeliveryProposalOrdersAudit',
            'tl_delivery_proposal_routes_audit'=>'TlDeliveryRoutesAudit',
            'tl_delivery_proposal_routes_car_audit'=>'TlDeliveryProposalRouteTransportAudit',
            'tl_delivery_proposal_route_cars_audit'=>'TlDeliveryProposalRouteCarsAudit',
            'tl_delivery_proposal_route_unforeseen_expenses_audit'=>'TlDeliveryProposalRouteUnforeseenExpensesAudit',
            'tl_outbound_registry_audit'=>'TlOutboundRegistryAudit',
            'tl_outbound_registry_items_audit'=>'TlOutboundRegistryItemsAudit',
        ];

        foreach($array as $key=>$value) {

        }

    }


    /*
    *
    * */
    public function actionIndex()
    {
        die('NO RUN actionIndex');
        $searchModel = new OutboundOrderGridSearch();
        $outboundDataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $outboundDataProvider->query->andWhere(['client_id'=>Client::CLIENT_KOTON]);
        $outboundDataProvider->pagination->pageSize = 100;

        $clientsArray = Client::getActiveWMSItems();
        $clientStoreArray = TLHelper::getStoreArrayByClientID();

        $query = StockRemains::find();
        $query->select('box_barcode, outbound_order_id, client_id, id, product_barcode, status, count(id) as [[qty]]');

//      $query->andWhere(['outbound_order_id'=> $searchModel->productBarcode]);
        $flag = 0;
        if(!empty($searchModel->productBarcode)) {
            $query->andWhere(['product_barcode'=> $searchModel->productBarcode]);
            $flag = 1;
        }

        if(!empty($searchModel->boxBarcode)) {
            $query->andWhere(['box_barcode'=> $searchModel->boxBarcode]);
            $flag = 1;
        }

        if($outbound = OutboundOrder::find()->andWhere(['client_id'=>$searchModel->client_id,'order_number'=>$searchModel->order_number])->one()) {
            $query->andWhere(['outbound_order_id'=> $outbound->id]);
            $flag = 1;
        }

        if($flag == 0) {
            $query->andWhere(['outbound_order_id'=> '-1']);
        }

        $query->groupBy('box_barcode, product_barcode, status');
        $query->orderBy('box_barcode');

        $stockDataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort' => [
//                'defaultOrder' => [
//                    'id' => SORT_DESC
//                ]
//            ],
        ]);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'outboundDataProvider' => $outboundDataProvider,
            'clientsArray' => $clientsArray,
            'clientStoreArray' => $clientStoreArray,
            'stockDataProvider' => $stockDataProvider,
        ]);
    }

    /*
     * полуаем список всех ненайдиных товаров в отсканированных для котона
     * */
    public function actionShowDiff()
    {
        // show-diff
        die('NO RUN actionShowDiff');
        $files = [
            'tmp-file/koton/transfer/box1.txt',
        ];
        $oID = 2042;
        $boxBarcode = '70000099999';
        foreach ($files as $pathToCSVFile) {
            if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 5000, ",")) !== FALSE) {
                    $productBarcode = $data['0'];

                    if ($stock = Stock::find()->where([
                        'status' => [
                            Stock::STATUS_OUTBOUND_PICKED,
                            Stock::STATUS_OUTBOUND_SCANNING
                        ],
                        'product_barcode' => $productBarcode,
                        'outbound_order_id' => $oID,
                    ])->one()) {

                        echo $productBarcode."<br />";

                        $stock->status = Stock::STATUS_OUTBOUND_SCANNED;
                        $stock->box_barcode = $boxBarcode;
                        //$stock->save(false);

                        $countStockForOrderItem = Stock::find()->where([
                            'status'=>Stock::STATUS_OUTBOUND_SCANNED,
                            'product_barcode'=>$productBarcode,
                            'outbound_order_id' => $oID])->count();

                        if ($ioi = OutboundOrderItem::find()->where(['outbound_order_id' => $stock->outbound_order_id,
                            'product_barcode' =>$productBarcode,
                        ])->one()
                        ) {

                            if (intval($ioi->accepted_qty) < 1) {
                                $ioi->begin_datetime = time();
                                $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                            }

                            $ioi->accepted_qty = $countStockForOrderItem;

                            if ($ioi->accepted_qty == $ioi->expected_qty || $ioi->accepted_qty == $ioi->allocated_qty ) {
                                $ioi->status = Stock::STATUS_OUTBOUND_SCANNED;
                            }

                            $ioi->end_datetime = time();
                            //$ioi->save(false);

                        }

                        // TODO убрать этот говно код, по свободе сделать миграуию и все полям которые integer значение по умолчанию постать 0
                        $oModel = OutboundOrder::findOne($stock->outbound_order_id);

                        $countStockForOrder = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'outbound_order_id'=>$stock->outbound_order_id])->count();

                        if(intval($oModel->accepted_qty) < 1) {
                            $oModel->begin_datetime = time();
                            $oModel->status = Stock::STATUS_OUTBOUND_SCANNING;
                        }

                        $oModel->accepted_qty = $countStockForOrder;

                        if ($oModel->accepted_qty == $oModel->expected_qty || $oModel->accepted_qty == $oModel->allocated_qty ) {
                            $oModel->status = Stock::STATUS_OUTBOUND_SCANNED;
                        }

                        $oModel->end_datetime = time();
                        //$oModel->save(false);
                        file_put_contents('koton-stock-55-yes.csv', $productBarcode.","."\n", FILE_APPEND);
                    } else {
                        file_put_contents('koton-stock-55-no.csv', $productBarcode.","."\n", FILE_APPEND);
                    }
                }
            }
        }
        echo "Y<br />";
        die('NO RUN actionShowDiff');
        $outboundIds = array_keys($this->transferKotonOrders);
        $client_id = 21;

        $stockItems = Stock::find()
//            ->select('id, product_barcode, outbound_order_id')
//            ->select('id, product_barcode, outbound_order_id, count(product_barcode) as product_qty')
            ->where([
                'outbound_order_id' => $outboundIds,
                'client_id' => $client_id,
                'status' => [
                    Stock::STATUS_OUTBOUND_PICKED,
//                    Stock::STATUS_OUTBOUND_SCANNED,
//                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
//                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
            ])
//            ->groupBy('outbound_order_id, product_barcode')
//            ->orderBy('outbound_order_id')
//            ->asArray()
            ->all();

        $stockItems2 = Stock::find()
//            ->select('id, status, product_barcode, outbound_order_id, count(product_barcode) as product_qty')
            ->where([
                'client_id' => $client_id,
                'status_availability' =>[Stock::STATUS_AVAILABILITY_YES,21],
            ])
//            ->groupBy('product_barcode')
//            ->orderBy('product_barcode')
//            ->asArray()
            ->all();

        $stockItems3 = Stock::find()
//            ->select('id, status, product_barcode, outbound_order_id, count(product_barcode) as product_qty')
            ->where([
                'client_id' => $client_id,
                'outbound_order_id' => [1665],
            ])
//            ->groupBy('product_barcode')
//            ->orderBy('product_barcode')
//            ->asArray()
            ->all();

//        die;
        $totalQty = 0;
        $productsToOutbound = [];
        foreach($stockItems as $keyProductBarcode=>$value) {
            $value->status_availability = 21;
            $value->status = 9;
            $value->outbound_order_id = 0;
            $value->consignment_outbound_id = 0;
            $value->outbound_order_item_id = 0;
            $value->outbound_picking_list_id = 0;
            $value->outbound_picking_list_barcode = 0;
            $value->secondary_address = '55-55-55-51';
            //$value->save(false);
//            if(isset($productsToOutbound[$keyProductBarcode])) {
//                $productsToOutbound[$keyProductBarcode] += $value['product_qty'];
//            } else {
//                $productsToOutbound[$keyProductBarcode] = $value['product_qty'];
//            }
//            $totalQty += $value['product_qty'];
        }

        foreach($stockItems2 as $keyProductBarcode=>$value) {
//            if(isset($productsToOutbound[$keyProductBarcode])) {
//                $productsToOutbound[$keyProductBarcode] += $value['product_qty'];
//            } else {
//                $productsToOutbound[$keyProductBarcode] = $value['product_qty'];
//            }
//            $totalQty += $value['product_qty'];
            $value->status_availability = 21;
            $value->status = 9;
            $value->outbound_order_id = 0;
            $value->consignment_outbound_id = 0;
            $value->outbound_order_item_id = 0;
            $value->outbound_picking_list_id = 0;
            $value->outbound_picking_list_barcode = 0;
            $value->secondary_address = '55-55-55-52';
            //$value->save(false);
        }

        foreach($stockItems3 as $keyProductBarcode=>$value) {
//            if(isset($productsToOutbound[$keyProductBarcode])) {
//                $productsToOutbound[$keyProductBarcode] += $value['product_qty'];
//            } else {
//                $productsToOutbound[$keyProductBarcode] = $value['product_qty'];
//            }
//            $totalQty += $value['product_qty'];
            $value->status_availability = 21;
            $value->status = 9;
            $value->outbound_order_id = 0;
            $value->consignment_outbound_id = 0;
            $value->outbound_order_item_id = 0;
            $value->outbound_picking_list_id = 0;
            $value->outbound_picking_list_barcode = 0;
            $value->secondary_address = '55-55-55-53';
           // $value->save(false);
        }

//        VarDumper::dump($totalQty,10,true);
        //VarDumper::dump($stockItems2,10,true);
//        VarDumper::dump($stockItems,10,true);
//        ConsignmentOutboundOrder::deleteAll(['id'=>246]);
//        OutboundOrder::deleteAll(['id'=>1665]);
//        OutboundOrderItem::deleteAll(['id'=>1665]);
        die('Y');

        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Заказ'); // +
        $activeSheet->setCellValue('B' . $i, 'Штрих-код товара'); // +
        $activeSheet->setCellValue('C' . $i, 'Количество'); // +


        foreach ($stockItems2 as $model) {
            $i++;

//            $activeSheet->setCellValue('A' . $i, $this->transferKotonOrders[$model['outbound_order_id']]);
            $activeSheet->setCellValue('B' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('C' . $i, $model['product_qty']);

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-orders-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();

//        return $this->render('show-diff',['stockItems'=>$stockItems]);
    }
}