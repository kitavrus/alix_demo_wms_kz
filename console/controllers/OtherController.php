<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 8/5/14
 * Time: 6:17 PM
 */

namespace console\controllers;

//use app\modules\inbound\inbound;
//use frontend\modules\inbound\models\InboundOrderItem;
use app\modules\warehouse\warehouse;
use Codeception\Module\Cli;
use common\components\BarcodeManager;
use common\components\DeliveryProposalManager;
use common\modules\audit\models\CrossDockAudit;
use common\modules\audit\models\CrossDockItemsAudit;
use common\modules\audit\models\InboundOrderAudit;
use common\modules\audit\models\InboundOrderItemAudit;
use common\modules\audit\models\OutboundOrderAudit;
use common\modules\audit\models\OutboundOrderItemAudit;
use common\modules\audit\models\OutboundPickingListsAudit;
use common\modules\audit\models\StockAudit;
use common\modules\audit\models\StoreAudit;
use common\modules\audit\models\StoreReviewsAudit;
use common\modules\audit\models\TlDeliveryProposalAudit;
use common\modules\audit\models\TlDeliveryProposalBillingAudit;
use common\modules\audit\models\TlDeliveryProposalBillingConditionsAudit;
use common\modules\audit\models\TlDeliveryProposalOrdersAudit;
use common\modules\audit\models\TlDeliveryProposalRouteUnforeseenExpensesAudit;
use common\modules\audit\models\TlDeliveryRoutesAudit;
use common\modules\billing\components\BillingManager;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\billing\models\TlDeliveryProposalBillingConditions;
use common\modules\client\models\Client;
use common\modules\client\models\ClientEmployees;
use common\modules\crossDock\models\ConsignmentCrossDock;
use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItemProducts;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\crossDock\models\CrossDockLog;
use common\modules\dataMatrix\models\InboundDataMatrix;
use common\modules\employees\models\Employees;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\inbound\models\InboundUploadLog;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\outbound\models\OutboundUploadItemsLog;
use common\modules\outbound\models\OutboundUploadLog;
use common\modules\product\models\Product;
use common\modules\product\models\ProductBarcodes;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\store\models\StoreReviews;
use common\modules\transportLogistics\components\TLManager;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\stock\models\RackAddress;
use common\modules\user\models\User;
use Yii;
use yii\base\Security;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\helpers\BaseFileHelper;



use common\components\OutboundManager;
use common\modules\transportLogistics\components\TLHelper;
use common\helpers\DateHelper;

class OtherController extends Controller
{

    public function actionSetProductType()
    {
        // other/one/set-product-type
        // php yii other/set-product-type
		// cd /home/www/vhosts/wms20/ && php yii other/set-product-type

        echo "other/one/qty-return-in-warehouse<br />";
//        die("--DIE--");
        $clientId = 2;
        $i = 1;
        $stockAll = Stock::find()->andWhere('is_product_type NOT IN (23)')->andWhere(['client_id' => $clientId, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])->orderBy(['id' => SORT_DESC])->all();
        foreach ($stockAll as $stock) {
//        foreach (Stock::find()->andWhere('is_product_type NOT IN (23)')->andWhere(['id' =>2395690,'client_id' => 2, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])->orderBy(['id' => SORT_DESC])->each(50) as $stock) {
            if (BarcodeManager::isReturnProductBarcode($stock->product_barcode,$clientId)) {
//            if (BarcodeManager::isReturnBoxBarcode($stock->primary_address)) {
                $stock->is_product_type = Stock::IS_PRODUCT_TYPE_RETURN;
                $stock->save(false);
                continue;
            } else if (BarcodeManager::isOneBoxOneProductInventory($stock->primary_address, 2)) {
                $stock->is_product_type = Stock::IS_PRODUCT_TYPE_LOT_BOX;
                $stock->save(false);
				continue;
            } else {
                $stock->is_product_type = Stock::IS_PRODUCT_TYPE_LOT;
                $stock->save(false);
            }
            echo $i++. "\n";

            //if($stock->inventory_primary_address == '700000804467') {
            //echo "---------------------------------------------". "\n";
            //}

        }

        return 0;
    }

    /*
     *
     * */
    public function actionRemoveOldData($id = 1)
    {
        // php yii other/remove-old-data 1
        // php yii other/remove-old-data 21
        // php yii other/remove-old-data 66
        // remove-old-data
        echo "return die";
        return 0;
        $oldClientIds = [
            '1',  // Colins
            '21', // Koton
            '66', // Akmaral modacenter.kz удалено
        ];

        if (in_array($id, $oldClientIds)) {
            $oldClientId[] = $id;
            echo "Y : " . $id . "\n";
        } else {
            echo "N : " . $id . "\n";;
            return 0;
        }

        foreach ($oldClientId as $clientId) {

            // CLIENT EMPLOYEE
            echo 'CLIENT EMPLOYEE' . '-' . $clientId . "\n";
            if ($clientEmployeesAll = ClientEmployees::findAll(['client_id' => $clientId])) {
                foreach ($clientEmployeesAll as $clientEmployees) {
                    User::deleteAll(['id' => $clientEmployees->user_id]);
                    echo 'ClientEmployees:' . $clientEmployees->id . '-' . $clientId . "\n";
                }
                ClientEmployees::deleteAll(['client_id' => $clientId]);
                unset($clientEmployeesAll);
            }


            // CROSS DOC
            echo 'CROSS DOC' . '-' . $clientId . "\n";
            ConsignmentCrossDock::deleteAll(['client_id' => $clientId]);
            if ($crossDockAll = CrossDock::findAll(['client_id' => $clientId])) {
                foreach ($crossDockAll as $crossDock) {
                    echo 'CrossDock:' . $crossDock->id . '-' . $clientId . "\n";
                    CrossDockAudit::deleteAll(['parent_id' => $crossDock->id]);
                    if ($crossDockItemsAll = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id])) {
                        foreach ($crossDockItemsAll as $crossDockItem) {
                            echo 'CrossDockItems:' . $crossDockItem->id . '-' . $clientId . "\n";
                            CrossDockItemsAudit::deleteAll(['parent_id' => $crossDockItem->id]);
                            CrossDockItemProducts::deleteAll(['cross_dock_item_id' => $crossDockItem->id]);
                        }
                    }
                    CrossDockItems::deleteAll(['cross_dock_id' => $crossDock->id]);
                    CrossDock::deleteAll(['id' => $crossDock->id]);
                    unset($crossDockItemsAll);
                }
                CrossDock::deleteAll(['client_id' => $clientId]);
                unset($crossDockAll);
            }
            CrossDockLog::deleteAll(['client_id' => $clientId]);


            // INBOUND
            echo 'INBOUND' . '-' . $clientId . "\n";
            ConsignmentInboundOrders::deleteAll(['client_id' => $clientId]);
            if ($inboundAll = InboundOrder::findAll(['client_id' => $clientId])) {
                foreach ($inboundAll as $inbound) {
                    echo 'InboundOrder:' . $inbound->id . '-' . $clientId . "\n";
                    InboundOrderAudit::deleteAll(['parent_id' => $inbound->id]);
                    if ($inboundOrderItemsAll = InboundOrderItem::findAll(['inbound_order_id' => $inbound->id])) {
                        foreach ($inboundOrderItemsAll as $inboundItem) {
                            echo 'InboundOrderItem:' . $inboundItem->id . '-' . $clientId . "\n";
                            InboundOrderItemAudit::deleteAll(['parent_id' => $inboundItem->id]);
                        }
                    }
                    InboundOrderItem::deleteAll(['inbound_order_id' => $inbound->id]);
                    InboundOrder::deleteAll(['id' => $inbound->id]);
                    unset($inboundOrderItemsAll);
                }
                InboundOrder::deleteAll(['client_id' => $clientId]);
                unset($inboundAll);
            }
            InboundUploadLog::deleteAll(['client_id' => $clientId]);


            // OUTBOUND
            echo 'OUTBOUND' . '-' . $clientId . "\n";
            ConsignmentOutboundOrder::deleteAll(['client_id' => $clientId]);
            if ($outboundAll = OutboundOrder::findAll(['client_id' => $clientId])) {
                foreach ($outboundAll as $outbound) {
                    echo 'OutboundOrder:' . $outbound->id . '-' . $clientId . "\n";
                    OutboundOrderAudit::deleteAll(['parent_id' => $outbound->id]);
                    if ($outboundOrderItemsAll = OutboundOrderItem::findAll(['outbound_order_id' => $outbound->id])) {
                        foreach ($outboundOrderItemsAll as $outboundItem) {
                            echo 'OutboundOrderItem:' . $outboundItem->id . '-' . $clientId . "\n";
                            OutboundOrderItemAudit::deleteAll(['parent_id' => $outboundItem->id]);
                        }
                    }
                    OutboundOrderItem::deleteAll(['outbound_order_id' => $outbound->id]);
                    unset($outboundOrderItemsAll);

                    if ($outboundPickingListsAll = OutboundPickingLists::findAll(['outbound_order_id' => $outbound->id])) {
                        foreach ($outboundPickingListsAll as $outboundPickingLists) {
                            echo 'OutboundPickingLists:' . $outboundPickingLists->id . '-' . $clientId . "\n";
                            OutboundPickingListsAudit::deleteAll(['parent_id' => $outboundPickingLists->id]);
                        }
                    }

                    OutboundPickingLists::deleteAll(['outbound_order_id' => $outbound->id]);
                    OutboundOrder::deleteAll(['id' => $outbound->id]);
                    unset($outboundPickingListsAll);
                }
                OutboundOrder::deleteAll(['client_id' => $clientId]);
                unset($outboundAll);
            }

            if ($outboundUploadLogAll = OutboundUploadLog::findAll(['client_id' => $clientId])) {
                foreach ($outboundUploadLogAll as $outboundUploadLog) {
                    echo 'OutboundUploadLog:' . $outboundUploadLog->id . '-' . $clientId . "\n";
                    OutboundUploadItemsLog::deleteAll(['outbound_upload_id' => $outboundUploadLog->id]);
                }
                unset($outboundUploadLogAll);
            }
            OutboundUploadLog::deleteAll(['client_id' => $clientId]);

            // PRODUCT
            echo 'PRODUCT' . '-' . $clientId . "\n";
            Product::deleteAll(['client_id' => $clientId]);
            echo 'ProductBarcodes' . '-' . $clientId . "\n";
            ProductBarcodes::deleteAll(['client_id' => $clientId]);

            // Return
            echo 'RETURN' . '-' . $clientId . "\n";
            if ($returnAll = ReturnOrder::findAll(['client_id' => $clientId])) {
                foreach ($returnAll as $return) {
                    echo 'ReturnOrder:' . $return->id . '-' . $clientId . "\n";
                    ReturnOrderItems::deleteAll(['return_order_id' => $return->id]);
                    ReturnOrder::deleteAll(['id' => $return->id]);
                }
                unset($returnAll);
            }
            ReturnOrder::deleteAll(['client_id' => $clientId]);

            // Stock
            echo 'STOCK' . '-' . $clientId . "\n";
            if ($stockAllQ = Stock::find()->select('id')->andWhere(['client_id' => $clientId])->indexBy('id')->asArray()) {
                foreach ($stockAllQ->batch(200) as $stock) {
                    $stockIDS = array_keys($stock);
                    echo 'Stock:' . implode(',', $stockIDS) . '-' . $clientId . "\n";
                    StockAudit::deleteAll(['parent_id' => $stockIDS]);
                    Stock::deleteAll(['id' => $stockIDS]);
                }
            }


            // Store
            echo 'STORE' . '-' . $clientId . "\n";
            if ($storeAll = Store::findAll(['client_id' => $clientId])) {
                foreach ($storeAll as $store) {
                    echo 'Store:' . $store->id . '-' . $clientId . "\n";
                    StoreAudit::deleteAll(['parent_id' => $store->id]);
                }
                unset($storeAll);
            }
            Store::deleteAll(['client_id' => $clientId]);

            echo 'STORE-REVIEWS' . '-' . $clientId . "\n";
            if ($storeReviewsAll = StoreReviews::findAll(['client_id' => $clientId])) {
                foreach ($storeReviewsAll as $storeReviews) {
                    echo 'StoreReviews:' . $storeReviews->id . '-' . $clientId . "\n";
                    StoreReviewsAudit::deleteAll(['parent_id' => $storeReviews->id]);
                }
                unset($storeReviewsAll);
            }
            StoreReviews::deleteAll(['client_id' => $clientId]);

            // DELIVERY PROPOSALS
//            echo 'DELIVERY PROPOSALS'.'-'.$clientId."\n";
//            if($deliveryProposalAll = TlDeliveryProposal::findAll(['client_id'=>$clientId])) {
//                foreach($deliveryProposalAll as $deliveryProposal) {
//                    TlDeliveryProposalAudit::deleteAll(['parent_id'=>$deliveryProposal->id]);
//                    echo 'TlDeliveryProposal:'.$deliveryProposal->id.'-'.$clientId."\n";
//                    // DELIVERY-PROPOSAL-ORDERS
//                    if($deliveryProposalOrdersAll = TlDeliveryProposalOrders::findAll(['client_id'=>$clientId])) {
//                        foreach($deliveryProposalOrdersAll as $deliveryProposalOrders) {
//                            TlDeliveryProposalOrdersAudit::deleteAll(['parent_id'=>$deliveryProposalOrders->id]);
//                            echo 'TlDeliveryProposalOrders:'.$deliveryProposalOrders->id.'-'.$clientId."\n";
//                        }
//                    }
//                    TlDeliveryProposalOrders::deleteAll(['tl_delivery_proposal_id'=>$deliveryProposal->id]);
//                    unset($deliveryProposalOrdersAll);
//
//                    // DELIVERY-ROUTES
//                    if($deliveryRoutesAll = TlDeliveryRoutes::findAll(['client_id'=>$clientId,'tl_delivery_proposal_id'=>$deliveryProposal->id])) {
//                        foreach($deliveryRoutesAll as $deliveryRoutes) {
//                            TlDeliveryRoutesAudit::deleteAll(['parent_id'=>$deliveryRoutes->id]);
//                            echo 'TlDeliveryRoutes:'.$deliveryRoutes->id.'-'.$clientId."\n";
//                        }
//                    }
//                    TlDeliveryRoutes::deleteAll(['client_id'=>$clientId,'tl_delivery_proposal_id'=>$deliveryProposal->id]);
//                    unset($deliveryRoutesAll);
//
//                    // DELIVERY-TRANSPORT
//                    if($deliveryProposalRouteTransportAll = TlDeliveryProposalRouteTransport::findAll(['tl_delivery_proposal_id'=>$deliveryProposal->id])) {
//                        foreach($deliveryProposalRouteTransportAll as $deliveryRoutes) {
//                        }
//                    }
//                    TlDeliveryProposalRouteTransport::deleteAll(['tl_delivery_proposal_id'=>$deliveryProposal->id]);
//                    unset($deliveryProposalRouteTransportAll);
//
//
//                    if($deliveryProposalRouteUnforeseenExpensesAll = TlDeliveryProposalRouteUnforeseenExpenses::findAll(['client_id'=>$clientId,'tl_delivery_proposal_id'=>$deliveryProposal->id]))  {
//                        foreach($deliveryProposalRouteUnforeseenExpensesAll as $deliveryProposalRouteUnforeseenExpenses) {
//                            TlDeliveryProposalRouteUnforeseenExpensesAudit::deleteAll(['parent_id'=>$deliveryProposalRouteUnforeseenExpenses->id]);
//                            echo 'TlDeliveryProposalRouteUnforeseenExpenses:'.$deliveryProposalRouteUnforeseenExpenses->id.'-'.$clientId."\n";
//                        }
//                    }
//                    TlDeliveryProposalRouteUnforeseenExpenses::deleteAll(['client_id'=>$clientId,'tl_delivery_proposal_id'=>$deliveryProposal->id]);
//                    unset($deliveryProposalRouteUnforeseenExpensesAll);
//                }
//                TlDeliveryProposal::deleteAll(['client_id'=>$clientId]);
//                unset($deliveryProposalAll);
//            }

//            echo 'DELIVERY PROPOSALS BILLING'.'-'.$clientId."\n";
//            if($deliveryProposalBillingAll = TlDeliveryProposalBilling::findAll(['client_id'=>$clientId])) {
//                foreach($deliveryProposalBillingAll as $deliveryProposalBilling) {
//                    echo 'TlDeliveryProposalBilling:'.$deliveryProposalBilling->id.'-'.$clientId."\n";
//                    TlDeliveryProposalBillingAudit::deleteAll(['parent_id'=>$deliveryProposalBilling->id]);
//                    if($deliveryProposalBillingConditionAll = TlDeliveryProposalBillingConditions::findAll(['tl_delivery_proposal_billing_id'=>$deliveryProposalBilling->id])) {
//                        foreach($deliveryProposalBillingConditionAll as $deliveryProposalBillingCondition) {
//                            echo 'TlDeliveryProposalBillingConditions:'.$deliveryProposalBillingCondition->id.'-'.$clientId."\n";
//                            TlDeliveryProposalBillingConditionsAudit::deleteAll(['parent_id'=>$deliveryProposalBillingCondition->id]);
//                        }
//                    }
//                    TlDeliveryProposalBillingConditions::deleteAll(['tl_delivery_proposal_billing_id'=>$deliveryProposalBilling->id]);
//                    unset($deliveryProposalBillingConditionAll);
//                }
//                TlDeliveryProposalBilling::deleteAll(['client_id'=>$clientId]);
//                unset($deliveryProposalBillingAll);
//            }
        }
        return 0;
    }

    /*
 *
 * */
    public function actionKotonAddDp()
    {
        // koton-add-dp
        echo 'koton-add-dp';
        return 0;
        $client_id = 21;
        $orders = [
            246 => 'transfer-stock-4',
            245 => 'transfer-stock-3-29',
            244 => 'transfer-stock-3-28',
            243 => 'transfer-stock-3-27',
            242 => 'transfer-stock-3-26',
            241 => 'transfer-stock-3-25',
            240 => 'transfer-stock-3-24',
            239 => 'transfer-stock-3-23',
            238 => 'transfer-stock-3-22',
            237 => 'transfer-stock-3-21',
            236 => 'transfer-stock-3-20',
            235 => 'transfer-stock-3-19',
            234 => 'transfer-stock-3-18',
            233 => 'transfer-stock-3-17',
            232 => 'transfer-stock-3-16',
            231 => 'transfer-stock-3-15',
            230 => 'transfer-stock-3-14',
            229 => 'transfer-stock-3-13',
            228 => 'transfer-stock-3-12',
            227 => 'transfer-stock-3-11',
            226 => 'transfer-stock-3-10',
            225 => 'transfer-stock-3-9',
            224 => 'transfer-stock-3-8',
            223 => 'transfer-stock-3-7',
            222 => 'transfer-stock-3-6',
            221 => 'transfer-stock-3-5',
        ];

        foreach ($orders as $id => $orderNumber) {
            $coOrderAll = ConsignmentOutboundOrder::find()->andWhere(['id' => $id])->all();
            if ($coOrderAll) {
                foreach ($coOrderAll as $coOrderNumber) {

//                    $newCoo  = new ConsignmentOutboundOrder();
//                    $newCoo->setAttributes([
//                        'client_id'=> $coOrderNumber->client_id,
//                        'party_number'=> $coOrderNumber->party_number.'-1',
//                        'status'=> Stock::STATUS_OUTBOUND_NEW,
//                        'expected_qty'=> 0,
//                        'allocated_qty'=> 0,
//                    ],false);
//                    $newCoo->save(false);

//                    $data = [];
                    $partyNumber = $coOrderNumber->party_number;
                    $orderNumber = $coOrderNumber->party_number;

                    $oManager = new OutboundManager();
                    $oManager->initBaseData($client_id, $partyNumber, $orderNumber);
                    $oManager->setConsignmentID($coOrderNumber->id);

                    $ooAll = OutboundOrder::find()->andWhere(['consignment_outbound_order_id' => $coOrderNumber->id])->all();
                    if ($ooAll) {
                        foreach ($ooAll as $oOrderNumber) {
//                            $xQty = 0;
//                            $newOutboundOrder  = new OutboundOrder();
//                            $newOutboundOrder->setAttributes([
//                                'client_id'=> $coOrderNumber->client_id,
//                                'from_point_id'=> $oOrderNumber->from_point_id,
//                                'to_point_id'=> $oOrderNumber->to_point_id,
//                                'order_number'=> $newCoo->party_number,
//                                'parent_order_number'=> $newCoo->party_number,
//                                'consignment_outbound_order_id'=> $newCoo->id,
//                                'status'=> Stock::STATUS_OUTBOUND_NEW,
//                                'cargo_status'=> 2,
//                                'expected_qty'=> 0,
//                                'allocated_qty'=> 0,
//                                'accepted_qty'=> 0,
//                            ],false);
//                            $newOutboundOrder->save(false);

                            $oManager->setOutboundID($oOrderNumber->id);

//                            $expectedQtyConsignmentNew = 0;
//                            $expectedQtyOutboundNew = 0;


//                            $ooIAll = OutboundOrderItem::find()->andWhere(['outbound_order_id' => $oOrderNumber->id])->all();
//                            if ($ooIAll) {
//                                foreach ($ooIAll as $oIOrderNumber) {
//                                    $expected_qtyNew = ($oIOrderNumber->expected_qty - $oIOrderNumber->accepted_qty);
//                                    if($expected_qtyNew) {
//                                        $newOutboundOrderItem = new OutboundOrderItem();
//                                        $newOutboundOrderItem->setAttributes([
//                                            'outbound_order_id' => $newOutboundOrder->id,
//                                            'product_barcode' => $oIOrderNumber->product_barcode,
//                                            'status' => Stock::STATUS_OUTBOUND_NEW,
//                                            'expected_qty' => $expected_qtyNew,
//                                            'allocated_qty' => 0,
//                                            'accepted_qty' => 0,
//                                        ], false);
//                                        $newOutboundOrderItem->save(false);
//
//                                        $expectedQtyConsignmentNew += $expected_qtyNew;
//                                        $expectedQtyOutboundNew += $expected_qtyNew;
//                                        echo  $newOutboundOrderItem->id."\n";
//                                    }
//                                }
//
//                                $newOutboundOrder->expected_qty = $expectedQtyOutboundNew;
//                                $newOutboundOrder->save(false);
//
//                                $oOrderNumber->expected_qty = ($newOutboundOrder->expected_qty + $oOrderNumber->accepted_qty);
//                                $oOrderNumber->save(false);
//                            }
                        }

//                        $newCoo->expected_qty = $expectedQtyConsignmentNew;
//                        $newCoo->save(false);

//                        $coOrderNumber->expected_qty = $oOrderNumber->expected_qty;
//                        $coOrderNumber->save(false);

                        $oManager->createUpdateDeliveryProposalAndOrder();
//                        $oManager->reservationOnStockByPartyNumber();
                        echo $oOrderNumber->id . ' ' . $oOrderNumber->expected_qty . "\n";
                    }
                }
            }
        }

        echo "-end-";
        return '0';

    }

    /*
    *
    * */
    public function actionKotonOutOfStock()
    {
        return 0; //dont run on live server
        // koton-out-of-stock
        $client_id = 21;
        $stockAllQuery = Stock::find()
            ->andWhere(['client_id' => $client_id, 'status_availability' => [Stock::STATUS_AVAILABILITY_YES, 21]])
            ->andWhere('secondary_address != ""')
            ->orderBy([
                'address_sort_order' => SORT_ASC,
                'primary_address' => SORT_DESC,
            ]);
//            ->limit(100);
        //->all();
        $addresses = [];
        foreach ($stockAllQuery->batch() as $stocks) {
            foreach ($stocks as $stock) {
                $sa = explode('-', trim($stock->secondary_address));
                $stage = preg_replace('/[^0-9]/', '', $sa['0']); // этаж
                $row = preg_replace('/[^0-9]/', '', $sa['1']); // ряд
                //$rack = preg_replace('/[^0-9]/', '',$sa['2']); // полка
                //$level = preg_replace('/[^0-9]/', '',$sa['3']); // уровень
                if ($stage == 3 && 0) {
                    $addresses[$stage . '-' . $row][$stock->secondary_address] = $stock->secondary_address;
                    echo $stage . '-' . $row . "\n";
                }
                if ($stage == 4) {
                    $addresses[$stage][$stock->secondary_address] = $stock->secondary_address;
                    echo $stage . '-' . $row . "\n";
                }
                if ($stage == 55) {
                    $addresses[$stage][$stock->secondary_address] = $stock->secondary_address;
                    echo $stage . '-' . $row . "\n";
                }


                //echo $stock->secondary_address . "<br />";

                //file_put_contents('KOTON-NEW-OUTBOUND-1.CSV', $stock->secondary_address . "\n", FILE_APPEND);
            }
        }

//        VarDumper::dump(Inventory::getMinMaxSecondaryAddress('3-11-01-0'),10,true);
//        VarDumper::dump($addresses,10,true);
//        die;
        foreach ($addresses as $key => $address) {

            $newCoo = new ConsignmentOutboundOrder();
            $newCoo->setAttributes([
                'client_id' => $client_id,
                'party_number' => 'transfer-stock' . '-' . $key,
                'status' => Stock::STATUS_OUTBOUND_NEW,
                'expected_qty' => 0,
                'allocated_qty' => 0,
            ], false);
            $newCoo->save(false);


            $partyNumber = $newCoo->party_number;
            $orderNumber = $newCoo->party_number;

            $oManager = new OutboundManager();
            $oManager->initBaseData($client_id, $partyNumber, $orderNumber);
            $oManager->setConsignmentID($newCoo->id);

            $newOutboundOrder = new OutboundOrder();
            $newOutboundOrder->setAttributes([
                'client_id' => $client_id,
                'from_point_id' => 4,
                'to_point_id' => 4,
                'order_number' => $orderNumber,
                'parent_order_number' => $partyNumber,
                'consignment_outbound_order_id' => $newCoo->id,
                'status' => Stock::STATUS_OUTBOUND_NEW,
                'cargo_status' => 2,
                'expected_qty' => 0,
                'allocated_qty' => 0,
                'accepted_qty' => 0,
            ], false);
            $newOutboundOrder->save(false);
            $oManager->setOutboundID($newOutboundOrder->id);


            $stockAll = Stock::find()
                ->select('id, product_barcode, count(*) as qty')
                ->andWhere(['client_id' => $client_id, 'secondary_address' => $address, 'status_availability' => [Stock::STATUS_AVAILABILITY_YES, 21]])
                ->groupBy('product_barcode')
                ->asArray()
                ->all();

            $expectedQtyConsignmentNew = 0;
            $expectedQtyOutboundNew = 0;
            echo $key . "\n";
            foreach ($stockAll as $stock) {
                $newOutboundOrderItem = new OutboundOrderItem();
                $newOutboundOrderItem->setAttributes([
                    'outbound_order_id' => $newOutboundOrder->id,
                    'product_barcode' => $stock['product_barcode'],
                    'status' => Stock::STATUS_OUTBOUND_NEW,
                    'expected_qty' => $stock['qty'],
                    'allocated_qty' => 0,
                    'accepted_qty' => 0,
                ], false);
                $newOutboundOrderItem->save(false);
                echo $stock['product_barcode'] . ' ' . $stock['qty'] . "\n";

                $expectedQtyConsignmentNew += $stock['qty'];
                $expectedQtyOutboundNew += $stock['qty'];
            }

            $newOutboundOrder->expected_qty = $expectedQtyOutboundNew;
            $newOutboundOrder->save(false);

            $newCoo->expected_qty = $expectedQtyOutboundNew;
            $newCoo->save(false);

            $oManager->createUpdateDeliveryProposalAndOrder();
            $oManager->reservationOnStockByPartyNumber($address);
            echo "NEXT>" . "\n";
        }

        return 0;
    }

    /*
    *
    * */
    public function actionKotonInboundConfirm()
    {
        // koton-inbound-confirm
        return 0;
        $client_id = 21;
        $conInboundOrderAll = ConsignmentInboundOrders::find()->andWhere(['client_id' => $client_id])->all();
        $boxs = [];
        foreach ($conInboundOrderAll as $conInboundOrder) {
            $inboundOrderAll = InboundOrder::find()->andWhere(['consignment_inbound_order_id' => $conInboundOrder->id, 'status' => [
                Stock::STATUS_INBOUND_SCANNED,
                Stock::STATUS_INBOUND_OVER_SCANNED,
                Stock::STATUS_INBOUND_SCANNING,
//                Stock::STATUS_INBOUND_NEW,
            ]])->all();

            if ($inboundOrderAll) {
                foreach ($inboundOrderAll as $inboundOrder) {

                    ////// BEGIN
                    $inboundOrder->status = Stock::STATUS_INBOUND_CONFIRM;
                    $inboundOrder->date_confirm = time();
                    $inboundOrder->save(false);

                    Stock::updateAll([
                        'status' => Stock::STATUS_INBOUND_CONFIRM,
                        'status_availability' => Stock::STATUS_AVAILABILITY_YES,
                    ], [
                        'inbound_order_id' => $inboundOrder->id,
                        'status' => [
                            Stock::STATUS_INBOUND_SCANNED,
                            Stock::STATUS_INBOUND_OVER_SCANNED,
                        ]
                    ]);

                    Stock::deleteAll('inbound_order_id = :inbound_order_id AND status != :status', [':inbound_order_id' => $inboundOrder->id, ':status' => Stock::STATUS_INBOUND_CONFIRM]);

                    if ($coi = ConsignmentInboundOrders::findOne($inboundOrder->consignment_inbound_order_id)) {
                        $coi->status = Stock::STATUS_INBOUND_SCANNING;
                        if (!InboundOrder::find()->andWhere('status != :status AND consignment_inbound_order_id = :consignment_inbound_order_id', [':status' => Stock::STATUS_INBOUND_CONFIRM, ':consignment_inbound_order_id' => $inboundOrder->consignment_inbound_order_id])->exists()) {
                            $coi->status = Stock::STATUS_INBOUND_CONFIRM;
                        }
                        $coi->save(false);
                    }
                    echo $inboundOrder->id . "\n";
                    ////// END

                    $inboundStockAll = [];
//                    $inboundStockAll =   Stock::find()->andWhere([
//                        'inbound_order_id'=>$inboundOrder->id,
//                        'status'=>[
//                            Stock::STATUS_INBOUND_SCANNED,
//                            Stock::STATUS_INBOUND_OVER_SCANNED,
//                        ]
//                    ])->all();

                    if ($inboundStockAll) {
                        foreach ($inboundStockAll as $inboundStock) {
                            //echo $inboundStock->product_barcode.' '.$inboundStock->primary_address.' '.$inboundStock->secondary_address."<br />";
                            file_put_contents('inTEXT1.csv', $inboundStock->product_barcode . ';' . $inboundStock->primary_address . ';' . $inboundStock->secondary_address . ';' . "\n", FILE_APPEND);

                            if (empty($inboundStock->secondary_address)) {
                                $boxs [$inboundStock->primary_address] = $inboundStock->primary_address;
                                file_put_contents('inTEXT1-empty.csv', $inboundStock->product_barcode . ';' . $inboundStock->primary_address . ';' . $inboundStock->secondary_address . ';' . "\n", FILE_APPEND);
                            }
                        }
                    }
                }
            }
        }

        foreach ($boxs as $box) {
            file_put_contents('inTEXT1-empty-uni.csv', $box . "\n", FILE_APPEND);
        }
        echo "-end-";
        return '0';
    }

    /*
    *
    * */
    public function actionReCreateKotonOutbound()
    {
        // re-create-koton-outbound
        $client_id = 21;
        $orders = [
            '182' => '20160114-21-40',
            '183' => '20160114-21-41',
            '184' => '20160114-21-42',
            '185' => '20160114-21-43',
            '186' => '20160114-21-44',
            '187' => '20160114-21-45',
            '188' => '20160114-21-46',
            '189' => '20160114-21-47',
            '190' => '20160114-21-48',
            '191' => '20160114-21-49',
            '192' => '20160114-21-50',
            '193' => '20160114-21-51',
        ];

        foreach ($orders as $id => $orderNumber) {
            $coOrderAll = ConsignmentOutboundOrder::find()->andWhere(['id' => $id])->all();
            if ($coOrderAll) {
                foreach ($coOrderAll as $coOrderNumber) {

                    $newCoo = new ConsignmentOutboundOrder();
                    $newCoo->setAttributes([
                        'client_id' => $coOrderNumber->client_id,
                        'party_number' => $coOrderNumber->party_number . '-1',
                        'status' => Stock::STATUS_OUTBOUND_NEW,
                        'expected_qty' => 0,
                        'allocated_qty' => 0,
                    ], false);
                    $newCoo->save(false);

//                    $data = [];
                    $partyNumber = $newCoo->party_number;
                    $orderNumber = $newCoo->party_number;

                    $oManager = new OutboundManager();
                    $oManager->initBaseData($client_id, $partyNumber, $orderNumber);
                    $oManager->setConsignmentID($newCoo->id);

                    $ooAll = OutboundOrder::find()->andWhere(['consignment_outbound_order_id' => $coOrderNumber->id])->all();
                    if ($ooAll) {
                        foreach ($ooAll as $oOrderNumber) {
                            $xQty = 0;
                            $newOutboundOrder = new OutboundOrder();
                            $newOutboundOrder->setAttributes([
                                'client_id' => $coOrderNumber->client_id,
                                'from_point_id' => $oOrderNumber->from_point_id,
                                'to_point_id' => $oOrderNumber->to_point_id,
                                'order_number' => $newCoo->party_number,
                                'parent_order_number' => $newCoo->party_number,
                                'consignment_outbound_order_id' => $newCoo->id,
                                'status' => Stock::STATUS_OUTBOUND_NEW,
                                'cargo_status' => 2,
                                'expected_qty' => 0,
                                'allocated_qty' => 0,
                                'accepted_qty' => 0,
                            ], false);
                            $newOutboundOrder->save(false);

                            $oManager->setOutboundID($newOutboundOrder->id);

                            $expectedQtyConsignmentNew = 0;
                            $expectedQtyOutboundNew = 0;


                            $ooIAll = OutboundOrderItem::find()->andWhere(['outbound_order_id' => $oOrderNumber->id])->all();
                            if ($ooIAll) {
                                foreach ($ooIAll as $oIOrderNumber) {
                                    $expected_qtyNew = ($oIOrderNumber->expected_qty - $oIOrderNumber->accepted_qty);
                                    if ($expected_qtyNew) {
                                        $newOutboundOrderItem = new OutboundOrderItem();
                                        $newOutboundOrderItem->setAttributes([
                                            'outbound_order_id' => $newOutboundOrder->id,
                                            'product_barcode' => $oIOrderNumber->product_barcode,
                                            'status' => Stock::STATUS_OUTBOUND_NEW,
                                            'expected_qty' => $expected_qtyNew,
                                            'allocated_qty' => 0,
                                            'accepted_qty' => 0,
                                        ], false);
                                        $newOutboundOrderItem->save(false);

                                        $expectedQtyConsignmentNew += $expected_qtyNew;
                                        $expectedQtyOutboundNew += $expected_qtyNew;
                                        echo $newOutboundOrderItem->id . "\n";
                                    }
                                }

                                $newOutboundOrder->expected_qty = $expectedQtyOutboundNew;
                                $newOutboundOrder->save(false);

                                $oOrderNumber->expected_qty = ($newOutboundOrder->expected_qty + $oOrderNumber->accepted_qty);
                                $oOrderNumber->save(false);
                            }
                        }

                        $newCoo->expected_qty = $expectedQtyConsignmentNew;
                        $newCoo->save(false);

                        $coOrderNumber->expected_qty = $oOrderNumber->expected_qty;
                        $coOrderNumber->save(false);

                        $oManager->createUpdateDeliveryProposalAndOrder();
                        $oManager->reservationOnStockByPartyNumber();
                        echo $newCoo->id . ' ' . $newCoo->expected_qty . "\n";
                    }
                }
            }
        }

        echo "-end-";
        return '0';
    }

    /*
*
* */
    public function actionIndex()
    {
        return 0;
        $client_id = 2;
        $ids = [1, 2, 96, 7, 8, 9, 93, 308];
        $limit = 1000;

        $dps = TlDeliveryProposal::find()
            ->andWhere(['client_id' => $client_id])
            ->orderBy(['id' => SORT_DESC])
            ->limit($limit)
            ->all();

        foreach ($dps as $value) {
            /*
                        if ($billing = TlDeliveryProposalBilling::find()
                            ->andWhere(
                                [
                                    'client_id' => $client_id,
                                    'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
                                    'route_from' => $value->route_from,
                                    'route_to' => $value->route_to,
                                ]
                            )
                            ->one()) {
            //                echo $billing->id."\n";
                        } else {

                            $billing =  new  TlDeliveryProposalBilling();
                            $billing->client_id = $client_id;
                            $billing->tariff_type = TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL;
                            $billing->route_from = $value->route_from;
                            $billing->route_to = $value->route_to;
                            $billing->price_invoice_with_vat = 8300;
                            $billing->price_invoice = 7410.714;
                            $billing->rule_type = 2;


                            $billing->from_city_id = isset($value->routeFrom->city) ? $value->routeFrom->city->id : 0;
                            $billing->to_city_id = isset($value->routeTo->city) ? $value->routeTo->city->id : 0;
            //                $billing->save(false);
                            echo "+"."\n";
                        }

                        $billing->status = 1;
                        $billing->price_invoice_with_vat = 8300;
                        $billing->price_invoice = 7410.714;
                        $billing->rule_type = 2;

                        $billing->from_city_id = isset($value->routeFrom->city) ? $value->routeFrom->city->id : 0;
                        $billing->to_city_id = isset($value->routeTo->city) ? $value->routeTo->city->id : 0;
            //            $billing->save(false);

                        echo  $billing->id." - "."\n";
            */
        }

//        die('-END-');


        $billingIds = TlDeliveryProposalBilling::find()
            ->andWhere(
                [
                    'client_id' => $client_id,
                    'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
                    'route_from' => 4,
                    'route_to' => $ids,
                ]
            )
            ->column();


        $billingAll = TlDeliveryProposalBilling::find()
            ->andWhere(
                [
                    'client_id' => $client_id,
                    'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
//                    'route_from' =>4,
//                    'route_to' =>$ids,
                ]
            )
            ->andWhere(['NOT IN', 'id', $billingIds])
            //->andWhere(['not in','route_from',$ids])
            ->all();

        foreach ($billingAll as $billing) {
//            $billing->price_invoice_with_vat = 1500;
//            $billing->price_invoice = 1339.286;
//            $billing->rule_type = 2;

            $billing->price_invoice_with_vat = 8300;
            $billing->price_invoice = 7410.714;
            $billing->rule_type = 2;

//            $billing->save(false);

            echo $billing->id . "\n";
        }

        $dpsIDS = TlDeliveryProposal::find()->andWhere([
            'client_id' => $client_id,
            'route_from' => 4,
            'route_to' => $ids
        ])
//                                          ->andWhere(['not in','route_from',4])
//                                          ->andWhere(['not in','route_from',$ids])
            ->orderBy(['id' => SORT_DESC])
            ->limit($limit)
            ->column();

        echo "TlDeliveryProposal" . "\n";
        $dps = TlDeliveryProposal::find()->andWhere([
            'client_id' => $client_id,
//                                                    'route_from'=>4,
//                                                    'route_to'=>$ids
        ])
            ->andWhere(['not in', 'id', $dpsIDS])
//                                          ->andWhere(['not in','route_from',$ids])
            ->orderBy(['id' => SORT_DESC])
            ->limit($limit)
            ->all();

        $count = 0;
        foreach ($dps as $dp) {
            $count++;
            $dp->change_price = 1;
            $dp->save(false);
            $dpManager = new DeliveryProposalManager(['id' => $dp->id]);
            $dpManager->onUpdateProposal();
            echo $dp->id . "\n";
            file_put_contents('dpm.log', $dp->id . "\n", FILE_APPEND);
            echo "-" . $count . "\n";
        }

        return 0;
//        return $this->render('index');
    }


    /*
     * Recalculate all delivery proposal on dp route and car expenses
     *
     * */
    public function actionIndexNo()
    {
        return 0;

        echo 'index - Start' . "\n";

        if ($dps = TlDeliveryProposal::find()->all()) {
            foreach ($dps as $dpItem) {
                if ($dpRoutes = $dpItem->getProposalRoutes()->all()) {
                    foreach ($dpRoutes as $dprItem) {
                        echo 'DPR ID: ' . $dprItem->id . "\n";
//                      $dprItem->recalculateExpensesRoute();
                        TLManager::recalculateDpAndDpr($dprItem->tl_delivery_proposal_id, $dprItem->id);
                    }
                }
//              $dpItem->recalculateExpensesOrder();
                TLManager::recalculateDpAndDpr($dpItem->id, null);
                echo "DP ID : " . $dpItem->id . "\n";
            }
        }

        echo 'index - End' . "\n";

        return 0;
    }

    /*
     *  copy delivery_date to sipped_datetime
     *
     * */
    public function actionCopyDeliveryDatetime()
    {
        return 0;
        echo 'copy-delivery-datetime - Start' . "\n";

        if ($dps = TlDeliveryProposal::find()->all()) {
            foreach ($dps as $dpItem) {
                if (!empty($dpItem->delivery_date) && empty($dpItem->shipped_datetime)) {
                    $dpItem->shipped_datetime = $dpItem->delivery_date;
//                    $dpItem->save(false);
                }
            }
        }

        echo 'copy-delivery-datetime - End' . "\n";
        return 0;
    }


    /*
     * Находим все заказы для клиента дифакто и указываем им цену
     *
     * */
    public function actionUpdateInvoicePrice()
    {
        echo 'update-invoice-price - Start' . "\n";
        return 0;
        $clientID = '2'; // DeFacto = 2, Colins = 1;
        if ($dps = TlDeliveryProposal::findAll(['client_id' => $clientID])) {
            $bm = new BillingManager();
            foreach ($dps as $dp) {
//                echo "DP : ".$dp->id."\n";
//                if(doubleval($dp->kg_actual) == 0.000) {
//                if(doubleval($dp->mc_actual) == 0.000) {
//                    echo "DP  MCA : " . $dp->id . "\n";
//                    echo "DP  MCA : " . $dp->mc_actual . "\n";
//                }

                $price_invoice_with_vat = $bm->getInvoicePriceForDP($dp);
                $price_invoice = $bm->getInvoicePriceForDP($dp, false);
//                $price_invoice_with_vat = $bm->calculateNDS($price_invoice);

//                if(!$price_invoice_with_vat) {
//                    echo  "DP NO PRICE INVOICE : ".$dp->id."\n";
//                    //echo  "DP PRICE INVOICE : ".$price_invoice."\n";
//                }

                echo "DP PRICE INVOICE : " . $dp->id . ' ' . $price_invoice_with_vat . ' ' . $price_invoice . "\n";

                $dp->price_invoice = $price_invoice;
                $dp->price_invoice_with_vat = $price_invoice_with_vat;
                $dp->save(false);

//
                $dp->recalculateExpensesOrder();
//                $dp->setCascadedStatus();

            }

        }

        echo 'update-invoice-price - End' . "\n";
        return 0;
    }

    /*
     *
     * */
    public function actionFixDuplicate()
    {
        echo 'fix-duplicate - Start' . "\n";
//        echo 'fix-duplicate - RETURN'."\n";
        return 0;

        $findId = 139;
        $newId = 141;
        /*
            24 - 28 Меняем
            133 - 154 Меняем
            76 - 82 Меняем
            139 - 141 НЕ меняем ( Уточнить у Турсуна )
         * */

        // 4 - DC Алматы
        // 123 - AIR Алматы

        $oldNew = [
            //DeFacto
            '28' => '24',
            '154' => '133',
            '82' => '76',
            '20' => '123', // OLD => NEW AIR Алматы
            '88' => '99',
            //Colins
            '81' => '123', // OLD => NEW AIR Алматы
            //Sharuakaz
            '69' => '123', // OLD => NEW AIR Алматы
            '70' => '4', // OLD => NEW DC Алматы
            // Integra
            '34' => '123', // OLD => NEW AIR Алматы
            //Al hilal Bank
            '117' => '4', // OLD => NEW DC Алматы
            // GG
            '176' => '4', // OLD => NEW DC Алматы
            '175' => '123', // OLD => NEW AIR Алматы

        ];

        foreach ($oldNew as $findId => $newId) {

            //S: TlDeliveryProposalBilling
            if ($dps = TlDeliveryProposalBilling::findAll(['route_to' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->route_to = $newId;
                    $dp->save(false);
                    echo 'TlDeliveryProposalBilling:route_to' . "\n";
                }
            }

            if ($dps = TlDeliveryProposalBilling::findAll(['route_from' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->route_from = $newId;
                    $dp->save(false);
                    echo 'TlDeliveryProposalBilling:route_from' . "\n";
                }
            }

            //S: TlDeliveryProposal
            if ($dps = TlDeliveryProposal::findAll(['route_to' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->route_to = $newId;
                    $dp->save(false);
                    echo 'TlDeliveryProposal:route_to' . "\n";
                }
            }

            if ($dps = TlDeliveryProposal::findAll(['route_from' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->route_from = $newId;
                    $dp->save(false);
                    echo 'TlDeliveryProposal:route_from' . "\n";
                }
            }

            //S: TlDeliveryRoutes
            if ($dps = TlDeliveryRoutes::findAll(['route_to' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->route_to = $newId;
                    $dp->save(false);
                    echo 'TlDeliveryRoutes:route_to' . "\n";
                }
            }

            if ($dps = TlDeliveryRoutes::findAll(['route_from' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->route_from = $newId;
                    $dp->save(false);
                    echo 'TlDeliveryRoutes:route_from' . "\n";
                }
            }

            //S: Store Reviews
            if ($dps = StoreReviews::findAll(['store_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->store_id = $newId;
                    $dp->save(false);
                    echo 'StoreReviews:store_id' . "\n";
                }
            }

            //S: Client Employees
            if ($dps = ClientEmployees::findAll(['store_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->store_id = $newId;
                    $dp->save(false);
                    echo 'ClientEmployees:store_id' . "\n";
                }
            }

            //S: Cross Dock
            if ($dps = CrossDock::findAll(['from_point_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->from_point_id = $newId;
                    $dp->save(false);
                    echo 'Cross Dock:from_point_id' . "\n";
                }
            }

            //S: Cross Dock
            if ($dps = CrossDock::findAll(['to_point_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->to_point_id = $newId;
                    $dp->save(false);
                    echo 'Cross Dock:to_point_id' . "\n";
                }
            }

            //S: Inbound Orders
            if ($dps = InboundOrder::findAll(['from_point_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->from_point_id = $newId;
                    $dp->save(false);
                    echo 'Inbound Order:from_point_id' . "\n";
                }
            }

            //S: Inbound Orders
            if ($dps = InboundOrder::findAll(['to_point_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->to_point_id = $newId;
                    $dp->save(false);
                    echo 'Inbound Order:to_point_id' . "\n";
                }
            }

            //S: Outbound Orders
            if ($dps = OutboundOrder::findAll(['from_point_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->from_point_id = $newId;
                    $dp->save(false);
                    echo 'Outbound Order:from_point_id' . "\n";
                }
            }

            //S: Outbound Orders
            if ($dps = OutboundOrder::findAll(['to_point_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->to_point_id = $newId;
                    $dp->save(false);
                    echo 'Outbound Order:to_point_id' . "\n";
                }
            }


            //S: ConsignmentInboundOrder
            if ($dps = ConsignmentInboundOrders::findAll(['from_point_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->from_point_id = $newId;
                    $dp->save(false);
                    echo 'Consignment Inbound Order:from_point_id' . "\n";
                }
            }

            //S: ConsignmentInboundOrder
            if ($dps = ConsignmentInboundOrders::findAll(['to_point_id' => $findId])) {
                foreach ($dps as $dp) {
                    $dp->to_point_id = $newId;
                    $dp->save(false);
                    echo 'Consignment Inbound Order:to_point_id' . "\n";
                }
            }


            // Delete old Store id
            if ($store = Store::findOne($findId)) {
                $store->deleted = 1;
                $store->save(false);
                echo 'Store:deleted:1' . "\n";
            }


        }

        echo 'fix-duplicate - End' . "\n";

        return 0;
    }


    /*
     * Set status выполнен
     *
     * */
    public function actionUpdateStatus()
    {
        echo 'update-status - Start' . "\n";
        echo 'update-status - RETURN' . "\n";
        return 0;

        $clientID = '2'; // DeFacto;
        if ($dps = TlDeliveryProposal::findAll(['client_id' => $clientID, 'status_invoice' => TlDeliveryProposal::INVOICE_PAID])) {
            foreach ($dps as $dp) {

                $dp->status = TlDeliveryProposal::STATUS_DONE;

                $dp->save(false);
                echo "DP SAVE : " . $dp->id . "\n";

                $dp->recalculateExpensesOrder();
                $dp->setCascadedStatus();
            }
        }

        echo 'update-status - End' . "\n";
        return 0;
    }


    /*
     * Set data all sub routes and cars
     * */
    public function actionSetDataToRouteAndCar()
    {
        echo 'set-data-to-route-and-car - Start' . "\n";
        return 0;

//        $clientID = '2'; // DeFacto;
        if ($dps = TlDeliveryProposal::find()->all()) {
//        if($dps = TlDeliveryProposal::findAll(['client_id'=>$clientID])) {
            foreach ($dps as $dp) {
                if ($dpRoutes = $dp->getProposalRoutes()->all()) {
                    foreach ($dpRoutes as $routeItem) {
                        $routeItem->shipped_datetime = $dp->shipped_datetime;
                        $routeItem->save(false);
                        if ($cars = $routeItem->getCarItems()->all()) {
                            foreach ($cars as $car) {
//                                VarDumper::dump($car);
                                $car->shipped_datetime = $dp->shipped_datetime;
                                $car->save(false);
                                echo ".";
                            }
                        }
                    }
                }
            }
        }

        echo 'set-data-to-route-and-car - RETURN' . "\n";
        return 0;

    }

    /*
     *
     *
     * */
    public function actionUpdateMcKgFilled()
    {
        echo 'update-mc-kg-filled - start' . "\n";
        if ($dprcs = TlDeliveryProposalRouteCars::find()->all()) {
            foreach ($dprcs as $dprc) {

                $mc = $kg = 0;

                if ($car = TlDeliveryProposalRouteCars::findOne($dprc->id)) {
                    if ($routes = $car->getTransportItems()->all()) {
                        foreach ($routes as $route) {
//                    echo "mc_actual : ".$route->mc_actual."\n";
//                    echo "kg_actual : ".$route->kg_actual."\n";
                            $mc += $route->mc_actual;
                            $kg += $route->kg_actual;
//                    $c++;

//                    VarDumper::dump($route,10,true);
                            echo "." . "\n";
                        }
                    }

                    $car->mc_filled = $mc;
                    $car->kg_filled = $kg;
                    $car->save(false);

//            echo $mc . "<br />";
//            echo $kg . "<br />";
//            echo $c . "<br />";
//            die('TlDeliveryProposalRouteTransport ----> afterSave');
                }
            }
        }


        echo 'update-mc-kg-filled - RETURN' . "\n";
        return 0;
    }

    public function actionImportOldStock()
    {
        echo 'import-old-stock - BEGIN' . "\n";
        return 0;
        //S: Start test load demo data
//        $pathToCSVFile = Yii::getAlias('@stockDepartment/tests/_data/client/de-facto/2/in-stock-import3.csv');
        $pathToCSVFile = Yii::getAlias('@stockDepartment/tests/_data/client/de-facto/3/all-stock-import.csv');
        $row = 1;
        $arrayToSaveCSVFile = [];
        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//          $num = count($data);
//            echo "<p> $num fields in line $row: <br /></p>\n";
                $row++;
                if ($row > 2) {
//                    \yii\helpers\VarDumper::dump($data,10,true);

                    $stock = new Stock();
                    $stock->client_id = 2;
                    $stock->inbound_order_id = 0;
                    $stock->outbound_order_id = 0;
                    $stock->product_barcode = $data['0'];
                    $stock->product_model = $data['8'];
                    $stock->primary_address = $data['4'];
                    $stock->secondary_address = $data['17'];
                    $stock->status = Stock::STATUS_INBOUND_NEW;
                    $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
                    $stock->created_user_id = 2;
//                    $stock->save(false);
                    echo ".";
                }
            }

            fclose($handle);
        }

        echo 'import-old-stock - RETURN' . "\n";
        return 0;
    }

    public function actionRemapBillingRuleType()
    {
        echo 'remap-billing-rule-type start' . "\n";
        return 0;
        $mapArray = [
            0 => TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_MC,
            1 => TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_KG,
            2 => TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_CONDITION_MC,
            3 => TlDeliveryProposalBilling::RULE_TYPE_POINT_BY_CONDITION_KG,
            4 => TlDeliveryProposalBilling::RULE_TYPE_BY_POINT,
        ];
        $alreadyMapped = [];
        foreach ($mapArray as $oldValue => $newValue) {
            $records = TlDeliveryProposalBilling::find()
                ->where(['rule_type' => $oldValue])
                ->andWhere(['not in', 'id', $alreadyMapped])
                ->all();

            if (!empty($records)) {
                foreach ($records as $record) {
                    $record->rule_type = $newValue;
                    if ($record->save(false)) {
                        $alreadyMapped[] = $record->id;
                        echo 'remap-record ' . $record->id . "\n";
                    }
                }
            }
        }

        echo 'remap-billing-rule-type end' . "\n";
        return 0;
    }

    /*
     *
     *
     * */
    public function actionAddExternalShopIdDefacto()
    {
        echo 'add-external-shop-id-defacto start' . "\n";
        return 0;

        $client_id = '2'; // Defacto

        $oos = OutboundOrder::find()->select('to_point_title')->where(['client_id' => $client_id])->groupBy('to_point_title')->column();

        if ($stores = Store::find()->where(['shop_code' => '', 'client_id' => $client_id, 'type_use' => Store::TYPE_USE_STORE])->all()) {
            foreach ($stores as $k => $store) {

                echo $k . "->" . $store->id . "\n";

                if (isset($oos[$k])) {
                    $store->shop_code = $oos[$k];
//                    $store->save(false);
                }
            }
        }

        echo 'add-external-shop-id-defacto end' . "\n";
        return 0;
    }

    public function actionChangeOldBillingType()
    {
        echo 'change-old-billing-type start' . "\n";
        return 0;
        $i = 0;
        $clientsIDs = [1, 2]; // Colins, DeFacto

        if ($oldTariffs = TlDeliveryProposalBilling::findAll(['tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_UNDEFINED, 'client_id' => $clientsIDs])) {

            foreach ($oldTariffs as $tariff) {
                $tariff->tariff_type = TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL;
//                $tariff->save(false);
                $i++;
            }
        }
        echo $i . ' old tariffs was changed' . "\n";
        echo 'change-old-billing-type end' . "\n";
        return 0;
    }

    public function actionChangeOldClientType()
    {
        echo 'change-old-client-type start' . "\n";
        return 0;
        $i = 0;
        if ($oldClients = Client::findAll(['client_type' => NULL])) {
            foreach ($oldClients as $client) {
                $client->client_type = Client::CLIENT_TYPE_CORPORATE_CONTRACT;
//                $client->save(false);
                $i++;
            }
        }
        echo $i . ' clients was changed' . "\n";
        echo 'change-old-client-type end' . "\n";
        return 0;
    }

    public function actionRemapProposalDeliveryType()
    {
        echo 'remap-proposal-delivery-type start' . "\n";
        return 0;
        $mapArray = [
            3 => TlDeliveryProposal::DELIVERY_TYPE_ONE_ROUTE,
            4 => TlDeliveryProposal::DELIVERY_TYPE_MORE_ROUTE,
        ];
        $i = 0;
        $alreadyMapped = [];
        foreach ($mapArray as $oldValue => $newValue) {
            $records = TlDeliveryProposal::find()
                ->where(['delivery_type' => $oldValue])
                ->andWhere(['not in', 'id', $alreadyMapped])
                ->all();

            if (!empty($records)) {
                foreach ($records as $record) {
                    $record->delivery_type = $newValue;
                    if ($record->save(false)) {
                        $i++;
                        $alreadyMapped[] = $record->id;
                        echo 'remap-record ' . $record->id . "\n";
                    }
                }
            }
        }

        echo $i . ' records was remapped' . "\n";
        echo 'remap-proposal-delivery-type end' . "\n";
        return 0;
    }

    public function actionRemapStockAvailabilityStatus()
    {
        echo 'remap-stock-availability-status start' . "\n";
        return 0;
        $i = 0;
        $records = Stock::find()->all();

        foreach ($records as $record) {
            echo '--------------Current remap start record ID ' . $record->id . '-----------------' . "\n";
            echo 'Old value = ' . $record->status_availability . "\n";
            $record->status_availability++;
//            $record->save(false);
            $i++;
            echo 'New value = ' . $record->status_availability . "\n";
            echo '--------------Current remap end record ID ' . $record->id . '-----------------' . "\n";
            echo "\n";
            echo "\n";
            echo "\n";
        }


        echo $i . ' records was remapped' . "\n";
        echo 'remap-stock-availability-status end' . "\n";
        return 0;
    }

    public function actionChangeClientDateInOutboundOrders()
    {
        echo 'change-client-date-in-outbound-order start' . "\n";
        return 0;
        $i = 0;
        $records = OutboundOrder::find()->all();

        foreach ($records as $record) {
            echo '--------------Current date change start record ID ' . $record->id . '-----------------' . "\n";
            echo 'Old value = ' . $record->data_created_on_client . "\n";
            $record->data_created_on_client = DateHelper::formatToISO($record->data_created_on_client, 4);
//            $record->save(false);
            $i++;
            echo 'New value = ' . $record->data_created_on_client . "\n";
            echo '--------------Current date change end record ID ' . $record->id . '-----------------' . "\n";
            echo "\n";
            echo "\n";
            echo "\n";
        }


        echo $i . ' records was changed' . "\n";
        echo 'change-client-date-in-outbound-orders end' . "\n";
        return 0;
    }


    /* Скрипт выбирает адрес полки из записей Stock
     * и сохраняет их в таблицу RackAddress
     *
     * */
    public function actionSaveRackAddress()
    {
        echo 'save-rack-address start' . "\n";
        $records = Stock::find()->all();
        $i = 0;
        foreach ($records as $record) {
            if (!$exist = RackAddress::findOne(['address' => $record->secondary_address])) {
                echo '--------------Current save record address record ID ' . $record->id . '-----------------' . "\n";
                $ra = new RackAddress();
                $ra->address = $record->secondary_address;
                $ra->save(false);
                echo 'New address was saved: ' . $ra->address . "\n";

                echo '--------------Current save record address record ID ' . $record->id . '-----------------' . "\n";
                echo "\n";
                $i++;
            }
        }
        echo $i . ' records was saved' . "\n";
        echo 'save-rack-address end' . "\n";
        return 0;
    }

    /* Скрипт для заполнения отсутствующих дат
     * доставки и упаковки в Outbound Orders
     * Даты берутся из связанных записей DeliveryProposal
     **/
    public function actionSyncOrdersDate()
    {
        echo 'sync-orders-date start' . "\n";
        $records = TlDeliveryProposal::findAll(['client_id' => Client::CLIENT_DEFACTO]);
        foreach ($records as $record) {
            if ($proposalOrder = $record->getProposalOrders()->all()) {
                foreach ($proposalOrder as $po) {
                    if ($oo = $po->outboundOrder) {
                        if (empty($oo->date_delivered)) {
                            $oo->date_delivered = $record->delivery_date;
                            $oo->save(false);
                            echo '*****Date delivered ' . $oo->date_delivered . ' was saved*****' . "\n";
                        }

                        if (empty($oo->packing_date)) {
                            $oo->packing_date = $record->shipped_datetime;
                            $oo->save(false);
                            echo '*****Packing date  ' . $oo->packing_date . ' was saved*****' . "\n";
                        }
                    }
                }

            }


        }


        echo 'sync-orders-date end' . "\n";
        return 0;
    }

    /* Скрипт для заполнения отсутствующей даты окончания
     * сканирования в Outbound Orders
     * Даты берутся из Outbound Order Items
     **/
    public function actionSyncEndDateTime()
    {
        echo 'sync-end-datetime start' . "\n";
        $records = OutboundOrder::findAll(['client_id' => Client::CLIENT_DEFACTO]);
        foreach ($records as $record) {
            if (empty($record->end_datetime)) {
                if ($items = $record->orderItems) {
                    $end = 0;
                    echo '*****BREAK*****' . "\n";
                    foreach ($items as $item) {
                        if ($item->end_datetime > $end) {
                            $end = $item->end_datetime;
                        }

                        echo '*****End date  ' . Yii::$app->formatter->asDatetime($item->end_datetime) . '*****' . "\n";
                    }

                    if ($end > 0) {
                        $record->end_datetime = $end;
                        $record->save(false);
                        echo '*****Latest end date  ' . Yii::$app->formatter->asDatetime($record->end_datetime) . ' was saved*****' . "\n";
                    }
                }
            }


        }


        echo 'sync-end-datetime end' . "\n";
        return 0;
    }


    /* Скрипт для заполнения отсутствующих дат
     * доставки и упаковки в Outbound Orders
     * Даты берутся из связанных записей DeliveryProposal
     **/
    public function actionCascadeProposalDelete()
    {
        echo 'cascade-proposal-delete start' . "\n";
        return 0;
//        if($dp = TlDeliveryProposal::findOne(2142)){
        if ($dp = TlDeliveryProposal::find()->where(['id' => '2142'])->one()) {
            if ($dpOrders = $dp->proposalOrders) {
                foreach ($dpOrders as $dpOrder) {
                    if ($outboundOrder = $dpOrder->outboundOrder) {
                        if ($ooi = $outboundOrder->orderItems) {
                            foreach ($ooi as $item) {
                                $item->delete();
                                echo '****delete outbound order item : ' . $item->id . "\n";

                            }
                        }

                        if ($stockItems = $outboundOrder->orderItemInStock) {
                            foreach ($stockItems as $stock) {
                                $stock->outbound_order_id = 0;
                                $stock->status = Stock::STATUS_OUTBOUND_NEW;
                                $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
                                $stock->save(false);
                                echo '****Stock item unallocate : ' . $stock->id . "\n";
                            }
                        }

                        $outboundOrder->delete();
                        echo '***delete outbound order : ' . $outboundOrder->id . "\n";
                    }

                    $dpOrder->delete();
                    echo '**delete delivery order : ' . $dpOrder->id . "\n";
                }
            }

            $dp->delete();
            echo '*delete delivery proposal : ' . $dp->id . "\n";
        }
        echo 'cascade-delete-proposal end' . "\n";
    }

    /* issue #182
     * Всем записями с order_type == null
     * выставляем order_type inbound
     **/
    public function actionSetInboundOrderType()
    {
        return 0;
        echo 'set-inbound-order-type start' . "\n";
        if ($inboundOrders = InboundOrder::findAll(['order_type' => null])) {
            foreach ($inboundOrders as $io) {
                if ($io->order_type === null) {
                    echo 'null value found, set to proper value...' . "\n";
                    $io->order_type = InboundOrder::ORDER_TYPE_INBOUND;
                    $io->save(false);
                    echo 'done!' . "\n";
                    echo "\n";
                    echo "\n";
                }
            }
        }
        echo 'set-inbound-order-type end' . "\n";

    }

    /*
   *
   * */
//    public function actionSetAddressSortOrder()
//    {
//        echo 'set address sort order start' . "\n";
//
//        $data = RackAddress::find()
//            ->orderBy('address ASC')
//            ->limit(1000)
//            ->all();
//        $sortArray = [];
//        foreach ($data as $d) {
//            $sortArray[] = [
//                'id'=>$d->id,
//                'rack'=>$d->getRowValue(),
//                'sort_order'=>0,
//            ];
//        }
//
//       usort($sortArray, function($a, $b){
//          if($a['row'] >$b['row']){
//              return true;
//          }
//           return false;
//       });
//
//        var_dump($sortArray);
//        echo 'set address sort order end' . "\n";
//
//    }

    /*
     *
     *
     * */
    public function actionReCalculateRouteCar()
    {
        return 0;
        echo 're-calculate-route-car start' . "\n";

        $dpAll = TlDeliveryProposal::find()
            ->andWhere('TO_DAYS(NOW()) - TO_DAYS(FROM_UNIXTIME(created_at)) <= 22')
//            ->andWhere('delivery_date IS NULL AND TO_DAYS(NOW()) - TO_DAYS(FROM_UNIXTIME(created_at)) <= 10')
            ->all();

        if ($dpAll) {
            foreach ($dpAll as $dpModel) {
                if ($dpRouteAll = $dpModel->getProposalRoutes()->all()) {
                    foreach ($dpRouteAll as $dpRouteModel) {
                        if ($routeCars = $dpRouteModel->getCarItems()->all()) {
//                        if ($routeCars = TlDeliveryProposalRouteCars::find()->all()) {
                            foreach ($routeCars as $car) {
                                //S:
//                                if ($car = TlDeliveryProposalRouteCars::findOne($model->id)) {
                                if ($r = $car->getRoutes()->all()) {
                                    foreach ($r as $rItem) {
                                        if ($tlDp = TlDeliveryProposal::findOne($rItem->tl_delivery_proposal_id)) {
                                            if ($routes = $tlDp->getProposalRoutes()->all()) {
                                                foreach ($routes as $route) {
                                                    $route->recalculateExpensesRoute();
                                                }
                                            }
                                            TLManager::recalculateDpAndDpr($tlDp->id);
                                            echo $tlDp->id . " " . date('Y-m-d', $tlDp->created_at) . "\n";
                                        }
                                    }
//                                    }
                                }
                                //E:
                            }
                        }
                    }
                }
            }
        }

        echo 're-calculate-route-car end' . "\n";
        return 0;
    }


    /*
     *
     * */
    public function actionConsignmentOutboundOrder()
    {
        echo 'other/consignment-outbound-order begin' . "\n";

        $outboundOrders = OutboundOrder::find()->all();
        foreach ($outboundOrders as $outbound) {

            if (!($consignment = ConsignmentOutboundOrder::findOne(['client_id' => $outbound->client_id, 'party_number' => $outbound->parent_order_number]))) {
                $consignment = new ConsignmentOutboundOrder();
            }

//            $consignment->status = Stock::STATUS_OUTBOUND_COMPLETE;
            $consignment->client_id = $outbound->client_id;
            $consignment->party_number = $outbound->parent_order_number;

            $consignment->allocated_qty += $outbound->allocated_qty;
            $consignment->expected_qty += $outbound->expected_qty;
            $consignment->accepted_qty += $outbound->accepted_qty;

            $consignment->save(false);

            $outbound->consignment_outbound_order_id = $consignment->id;
            $outbound->save(false);

        }

        echo 'other/consignment-outbound-order end' . "\n";
        return 0;
    }

    /*
    *
    * */
    public function actionConfirmationDateFill()
    {
        return 0;
        echo 'other/confirmation-date-fill begin' . "\n";

        if ($InboundOrders = InboundOrder::find()->all()) {
            foreach ($InboundOrders as $io) {
                if ($io->end_datetime) {
                    $io->date_confirm = $io->end_datetime;
                    $io->save(false);
                    echo 'Confirm date was saved' . "\n";
                }
            }
        }


        return 0;
    }

    /*
    *
    * */
    public function actionSetInternalCode()
    {
        // TODO  выполнен на живом
        echo 'other/set-internal-code begin' . "\n";
        echo 'return 0' . "\n";
        return 0;

        Store::updateAll(['internal_code' => 0], 'type_use = :type_use', [':type_use' => Store::TYPE_USE_STORE]);
        Client::updateAll(['internal_code_count' => 0], 'id != :id', [':id' => '-1']);

        if ($storeAll = Store::find()->all()) {
            foreach ($storeAll as $store) {
//                if ($store->type_use == Store::TYPE_USE_STORE) {
                if (empty($store->internal_code) && $store->type_use == Store::TYPE_USE_STORE && $store->shop_code != '-' && $store->shop_code != 'CA1 (ССС)') {
                    if ($client = Client::findOne($store->client_id)) {

                        $client->internal_code_count += 1;
//                        $client->internal_code_count = 0;

                        $store->internal_code = $client->internal_code_count;
                        $store->save(false);

                        $client->save(false);

                        echo 'Saved: ' . $store->internal_code . "\n";
                    }
                }
            }
        }

        echo 'other/set-internal-code end' . "\n";
        return 0;
    }

    /*
    *
    * */
    public function actionFillNullClientId()
    {
        return 0;
        // выполнен на живом
        echo 'other/fill-null-client-id begin' . "\n";

        if ($orders = TlDeliveryProposalOrders::find()->all()) {
            foreach ($orders as $order) {
                if (is_null($order->client_id) && $dp = $order->deliveryProposal) {

                    $order->client_id = $dp->client_id;
                    $order->save(false);
                    echo 'Saved: ' . $order->order_number . "\n";
                }
            }
        }

        echo 'other/fill-null-client-id end' . "\n";
        return 0;
    }

    /*
    *
    * */
    public function actionSetCargoStatus()
    {
        return 0;
        // выполнен на живом
        echo 'other/set-cargo-status begin' . "\n";

        if ($outboundOrders = OutboundOrder::find()->all()) {
            foreach ($outboundOrders as $order) {
                switch ($order->status) {
                    case Stock::STATUS_OUTBOUND_NEW :
                        $order->cargo_status = OutboundOrder::CARGO_STATUS_NEW;
                        $order->save(false);
                        break;

                    case Stock::STATUS_OUTBOUND_COMPLETE :
                    case Stock::STATUS_OUTBOUND_DELIVERED :
                    case Stock::STATUS_OUTBOUND_DONE :
                        $order->cargo_status = OutboundOrder::CARGO_STATUS_DELIVERED;
                        $order->save(false);
                        break;
                    case Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API :
                        if ($outboundAudit = OutboundOrderAudit::find()->andWhere([
                            'parent_id' => $order->id,
                            'field_name' => 'status',
                            'after_value_text' => [
                                'Доставлен',
                                'Полностью выполнен',
                                'Выполнен (отгрузка)',
                            ]])->one()
                        ) {
                            $order->cargo_status = OutboundOrder::CARGO_STATUS_DELIVERED;
                            $order->save(false);
                        }
                        break;

                    case Stock::STATUS_OUTBOUND_ON_ROAD :
                        $order->cargo_status = OutboundOrder::CARGO_STATUS_ON_ROUTE;
                        $order->save(false);
                        break;

                    default :
                        $order->cargo_status = OutboundOrder::CARGO_STATUS_IN_PROCESSING;
                        $order->save(false);
                        break;
                }
            }
        }

        echo 'other/set-cargo-status end' . "\n";
        return 0;
    }


    /*
     *
     *
     * */
    public function actionColinsInboundTestAllocated()
    {
        echo 'other/colins-inbound-test-allocated begin' . "\n";
        return 0;
        $client_id = Client::CLIENT_COLINS;
        //Берем весь приход этого тира
        if ($inboundAll = InboundOrder::findAll(['consignment_inbound_order_id' => 12, 'status' => Stock::STATUS_INBOUND_NEW])) {
            foreach ($inboundAll as $inboundRowOne) {
                if ($order = InboundOrder::find()->andWhere(['client_box_barcode' => $inboundRowOne->order_number, 'client_id' => $client_id])->one()) {
                    //шк и кол-во товаров в приходном заказе
                    $productBarcodes = InboundOrderItem::find()
                        ->select('product_barcode, expected_qty')
                        ->where(['inbound_order_id' => $order->id])
                        ->asArray()
                        ->all();
                    //все Items приходного заказа
                    $InboundOrderItems = InboundOrderItem::find()
                        ->where(['inbound_order_id' => $order->id])
                        ->all();
                    //для каждого Items приходного заказа создаем записи в Stock
                    if (!empty($InboundOrderItems) && is_array($InboundOrderItems)) {
                        foreach ($InboundOrderItems as $inbound) {
                            if (!(Stock::find()->where([
                                'client_id' => $order->client_id,
                                'inbound_order_id' => $order->id,
                                'product_barcode' => $inbound->product_barcode,
                            ])->exists())
                            ) {
                                for ($i = 0; $i < $inbound->expected_qty; $i++) {
                                    // STOCK
                                    $stock = new Stock();
                                    $stock->client_id = $order->client_id;
                                    $stock->inbound_order_id = $order->id;
                                    $stock->product_barcode = $inbound->product_barcode;
                                    $stock->product_model = $inbound->product_model;
//                                   $stock->status = Stock::STATUS_INBOUND_SORTED;
                                    $stock->status = Stock::STATUS_INBOUND_SORTING;
                                    $stock->status_availability = Stock::STATUS_AVAILABILITY_NOT_SET;
                                    $stock->save(false);
                                }
                            }
                        }
                    }
                    //шк и кол-во товаров в приходном заказе
                    $boxProductBarcodes = ArrayHelper::map($productBarcodes, 'product_barcode', 'expected_qty');

                    //ID всех outbound orders этого тира
                    $outboundIds = OutboundOrder::find()->select('id')->where(['client_id' => $client_id, 'status' => Stock::STATUS_OUTBOUND_NEW])->orderBy('to_point_id ASC')->column();
                    file_put_contents('colins-allocate.log', "\n" . "\n" . "\n" . "--NEW--" . "\n" . "\n" . "\n", FILE_APPEND);

                    if (!empty($boxProductBarcodes)) {
                        //для каждой позиции приходного заказа как ШК товара => кол-во
                        foreach ($boxProductBarcodes as $productBarcode => $inBoxQty) {
                            //кол-во
                            $inBoxDiffAllocated = $inBoxQty;
                            while ($inBoxDiffAllocated) {
                                //ищем запись в Outbound Items c таким же ШК товара и номером партии
                                $outboundOrderItems = OutboundOrderItem::find()
                                    ->where(['product_barcode' => $productBarcode, 'outbound_order_id' => $outboundIds])
                                    ->andWhere('expected_qty != allocated_qty')
                                    ->orderBy('outbound_order_id ASC')
                                    ->limit(1)
                                    ->all();

                                if (!empty($outboundOrderItems)) {
                                    foreach ($outboundOrderItems as $outboundOrderItem) {

                                        $expectedQtyItem = intval($outboundOrderItem->expected_qty);
                                        $allocatedQtyItem = intval($outboundOrderItem->allocated_qty);
                                        $diffInOrder = $expectedQtyItem - $allocatedQtyItem;
                                        $diffWithBox = $diffInOrder - $inBoxDiffAllocated;

                                        file_put_contents('colins-allocate.log', "\n" . "\n", FILE_APPEND);
                                        file_put_contents('colins-allocate.log', "outboundOrderItem ID = " . $outboundOrderItem->id . "\n", FILE_APPEND);
                                        file_put_contents('colins-allocate.log', "productBarcode = " . $productBarcode . "\n", FILE_APPEND);
                                        file_put_contents('colins-allocate.log', "expectedQtyItem = " . $expectedQtyItem . "\n", FILE_APPEND);
                                        file_put_contents('colins-allocate.log', "allocatedQtyItem = " . $allocatedQtyItem . "\n", FILE_APPEND);
                                        file_put_contents('colins-allocate.log', "diffInOrder = " . $diffInOrder . "\n", FILE_APPEND);
                                        file_put_contents('colins-allocate.log', "diffWithBox = " . $diffWithBox . "\n", FILE_APPEND);
                                        file_put_contents('colins-allocate.log', "inBoxDiffAllocated = " . $inBoxDiffAllocated . "\n", FILE_APPEND);
                                        file_put_contents('colins-allocate.log', "inBoxQty = " . $inBoxQty . "\n", FILE_APPEND);


                                        if ($diffWithBox == 0) {
                                            if ($oo = $outboundOrderItem->outboundOrder) {
                                                $outputData[$outboundOrderItem->product_barcode][] = [
                                                    'outbound_order_id' => $oo->id,
                                                    'shop_id' => $oo->to_point_id,
                                                    'product_barcode' => $outboundOrderItem->product_barcode,
                                                    'product_model' => $outboundOrderItem->product_model,
                                                    'expected_qty' => $inBoxDiffAllocated,
                                                ];

                                                $outboundOrderItem->allocated_qty += $inBoxDiffAllocated;
                                                $outboundOrderItem->status = Stock::STATUS_INBOUND_SORTING;
                                                $outboundOrderItem->save(false);

                                                // STOCK
                                                if ($inStocks = Stock::find()->where([
                                                        'client_id' => $client_id,
                                                        'inbound_order_id' => $order->id,
                                                        'product_barcode' => $outboundOrderItem->product_barcode,
                                                        'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET]
                                                )->limit($inBoxDiffAllocated)->all()
                                                ) {
                                                    foreach ($inStocks as $stockLine) {
                                                        $stockLine->outbound_order_id = $oo->id;
                                                        $stockLine->status = Stock::STATUS_INBOUND_SORTED;
                                                        $stockLine->status_availability = Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED;
                                                        $stockLine->save(false);
                                                    }
                                                }


                                                $inBoxDiffAllocated = 0;
                                                $oo->recalculateOrderItems(); // TODO Тут должен ставится статус!!!
                                                file_put_contents('colins-allocate.log', "outbound_order_id = " . $oo->id . "\n", FILE_APPEND);
                                                continue;
                                            }
                                        }

                                        //Если одижаем 4 в коробе 3
                                        // 4 - 0 = 4
                                        // 4 - 3 = 1
                                        if ($diffWithBox > 0) {
                                            if ($oo = $outboundOrderItem->outboundOrder) {
                                                $outputData[$outboundOrderItem->product_barcode][] = [
                                                    'outbound_order_id' => $oo->id,
                                                    'shop_id' => $oo->to_point_id,
                                                    'product_barcode' => $outboundOrderItem->product_barcode,
                                                    'product_model' => $outboundOrderItem->product_model,
                                                    'expected_qty' => $inBoxDiffAllocated,
                                                ];

                                                $outboundOrderItem->allocated_qty += $inBoxDiffAllocated;
                                                $outboundOrderItem->status = Stock::STATUS_INBOUND_SORTING;
                                                $outboundOrderItem->save(false);

                                                // STOCK
                                                if ($inStocks = Stock::find()->where([
                                                        'client_id' => $client_id,
                                                        'inbound_order_id' => $order->id,
                                                        'product_barcode' => $outboundOrderItem->product_barcode,
                                                        'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET]
                                                )->limit($inBoxDiffAllocated)->all()
                                                ) {

                                                    foreach ($inStocks as $stockLine) {
                                                        $stockLine->outbound_order_id = $oo->id;
                                                        $stockLine->status = Stock::STATUS_INBOUND_SORTED;
                                                        $stockLine->status_availability = Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED;
                                                        $stockLine->save(false);
                                                    }
                                                }

                                                $inBoxDiffAllocated = 0;
                                                $oo->recalculateOrderItems(); // TODO Тут должен ставится статус!!!
                                                file_put_contents('colins-allocate.log', "outbound_order_id = " . $oo->id . "\n", FILE_APPEND);
                                                continue;
                                            }
                                        }

                                        //Если одижаем 4 в коробе 7
                                        // 4 - 0 = 4
                                        // 4 - 7 = -3
                                        if ($diffWithBox < 0) {
                                            if ($oo = $outboundOrderItem->outboundOrder) {
                                                $outputData[$outboundOrderItem->product_barcode][] = [
                                                    'outbound_order_id' => $oo->id,
                                                    'shop_id' => $oo->to_point_id,
                                                    'product_barcode' => $outboundOrderItem->product_barcode,
                                                    'product_model' => $outboundOrderItem->product_model,
                                                    'expected_qty' => $diffInOrder,
                                                ];

                                                $outboundOrderItem->allocated_qty += $diffInOrder;
                                                $outboundOrderItem->status = Stock::STATUS_INBOUND_SORTING;
                                                $outboundOrderItem->save(false);

                                                // STOCK
                                                if ($inStocks = Stock::find()->where([
                                                        'client_id' => $client_id,
                                                        'inbound_order_id' => $order->id,
                                                        'product_barcode' => $outboundOrderItem->product_barcode,
                                                        'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET]
                                                )->limit($outboundOrderItem->expected_qty)->all()
                                                ) {
                                                    foreach ($inStocks as $stockLine) {
                                                        $stockLine->outbound_order_id = $oo->id;
                                                        $stockLine->status = Stock::STATUS_INBOUND_SORTED;
                                                        $stockLine->status_availability = Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED;
                                                        $stockLine->save(false);
                                                    }
                                                }

                                                $oo->recalculateOrderItems(); // TODO Тут должен ставится статус!!!
                                                $inBoxDiffAllocated = $diffWithBox * -1;
                                                file_put_contents('colins-allocate.log', "outbound_order_id = " . $oo->id . "\n", FILE_APPEND);
                                                continue;
                                            }
                                        }
                                    }
                                } else {
                                    $remnantInBox[$productBarcode] = $inBoxDiffAllocated;
                                    $inBoxDiffAllocated = 0;
                                }
//                            }
                            } // end While
                        } // end foreach boxProductBarcodes
                    }
                    $order->status = Stock::STATUS_INBOUND_SORTED;
                    $order->save(false);
                    echo '.';
                }
            }
        }

        echo "\n" . 'other/colins-inbound-test-allocated end' . "\n";
        return 0;
    }

    /*
//   *
//   * */
//    public function actionFindProblemOutboundOrder()
//    {
//
//        // выполнен на живом
//        echo 'other/find-problem-outbound-order begin' . "\n";
//
//        $outboundOrders = OutboundOrder::find()->all();
//        $i=0;
//        foreach($outboundOrders as $oo){
//            if($oo->date_left_warehouse > $oo->date_delivered){
//                $i++;
//                echo $i.' Date print TTN > Delivery Date => id '.$oo->id ."\n";
//            }
//
//        }
//
//
//        echo 'Total: ' .$i .' records'."\n";
//        echo 'other/find-problem-outbound-order end' . "\n";
//        return 0;
//    }

    /*
     * Free allocated product in stock
     *
     * */
    public function actionFreeAllocatedProductInStockColins()
    {
        echo 'other/free-allocated-product-in-stock-colins end' . "\n";
        return 0;
        Stock::updateAll([
            'status' => Stock::STATUS_INBOUND_CONFIRM,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES
        ], [
//            'client_id'=>Client::CLIENT_COLINS
            'client_id' => -1
        ]);


        echo 'other/free-allocated-product-in-stock-colins end' . "\n";
        return 0;
    }

    public function actionInboundLeftProductCount()
    {
        echo 'other/inbound-left-product-count start' . "\n";
        return 0;
        $stockLeft = Stock::find()->select('id')->andWhere([
            'client_id' => Client::CLIENT_COLINS,
            'status' => Stock::STATUS_INBOUND_SORTED,
            'status_availability' => Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED,
        ])->asArray()->column();

        $box_barcode = '701_';

        foreach ($stockLeft as $scanningProduct) {

            if ($stock = Stock::find()->where([
                'status' => [
                    Stock::STATUS_INBOUND_SORTED,
                ],
                'id' => $scanningProduct,
                'status_availability' => Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED,
            ])->one()
            ) {
                $box = $box_barcode . $stock->inbound_order_id;
                $stock->status = Stock::STATUS_OUTBOUND_SCANNED;
                $stock->status_availability = Stock::STATUS_AVAILABILITY_RESERVED;
                $stock->box_barcode = $box;
                $stock->save(false);

                $countStockForOrderItem = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'product_barcode' => $stock->product_barcode, 'outbound_order_id' => $stock->outbound_order_id])->count();
                $countStockForInboundOrderItem = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'product_barcode' => $stock->product_barcode, 'inbound_order_id' => $stock->inbound_order_id])->count();
                $outboundOrder = OutboundOrder::findOne($stock->outbound_order_id);
                $inboundOrder = InboundOrder::findOne($stock->inbound_order_id);

                //Пересчет количества товаров в OutboundOrderItems
                if ($ioi = OutboundOrderItem::find()->where([
                    'outbound_order_id' => $stock->outbound_order_id,
                    'product_barcode' => $stock->product_barcode,
                ])->one()
                ) {
                    if (intval($ioi->accepted_qty) < 1) {
                        $ioi->begin_datetime = time();
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                    }

                    $ioi->accepted_qty = $countStockForOrderItem;
                    //$ioi->allocated_qty = $countStockForOrderItem;

                    if ($ioi->accepted_qty == $ioi->expected_qty || $ioi->accepted_qty == $ioi->allocated_qty) {
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNED;
                    }

                    $ioi->end_datetime = time();
                    $ioi->save(false);
                }

                //Пересчет количества товаров в OutboundOrders
                if ($outboundOrder) {
                    $countStockForOrder = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'outbound_order_id' => $outboundOrder->id])->count();

                    if (intval($outboundOrder->accepted_qty) < 1) {
                        $outboundOrder->begin_datetime = time();
                        $outboundOrder->status = Stock::STATUS_OUTBOUND_SCANNING;
                    }

                    $outboundOrder->accepted_qty = $countStockForOrder;
                    //$outboundOrder->allocated_qty = $countStockForOrder;

                    if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty || $outboundOrder->expected_qty == $outboundOrder->allocated_qty) {
                        $outboundOrder->status = Stock::STATUS_OUTBOUND_SCANNED;
                    }

                    $outboundOrder->end_datetime = time();
                    $outboundOrder->save(false);

                    //Пересчет количества товаров в ConsignmentOutboundOrders
                    if ($consignmentOutboundOrder = $outboundOrder->parentOrder) {

                        if (intval($consignmentOutboundOrder->accepted_qty) < 1) {
                            $consignmentOutboundOrder->begin_datetime = time();
                            $consignmentOutboundOrder->status = Stock::STATUS_OUTBOUND_SCANNING;
                        }

                        $consignmentOutboundOrder->recalculateOrderItems();
                    }
                }

                //Пересчет количества товаров в InboundOrderItems
                if ($inboundItem = InboundOrderItem::find()->where([
                    'inbound_order_id' => $stock->inbound_order_id,
                    'product_barcode' => $stock->product_barcode,
                ])->one()
                ) {
                    if (intval($inboundItem->accepted_qty) < 1) {
                        $inboundItem->begin_datetime = time();
                        $inboundItem->status = Stock::STATUS_INBOUND_SCANNING;
                    }

                    $inboundItem->accepted_qty = $countStockForInboundOrderItem;

                    if ($inboundItem->accepted_qty == $inboundItem->expected_qty) {
                        $inboundItem->status = Stock::STATUS_INBOUND_SCANNED;
                    }

                    $inboundItem->end_datetime = time();
                    $inboundItem->save(false);
                }

                //Пересчет количества товаров в InboundOrders
                if ($inboundOrder) {
                    $countStockForInboundOrder = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'inbound_order_id' => $inboundOrder->id])->count();

                    if (intval($inboundOrder->accepted_qty) < 1) {
                        $inboundOrder->begin_datetime = time();
                        $inboundOrder->status = Stock::STATUS_INBOUND_SCANNING;
                    }

                    $inboundOrder->accepted_qty = $countStockForInboundOrder;

                    if ($inboundOrder->accepted_qty == $inboundOrder->expected_qty) {
                        $inboundOrder->status = Stock::STATUS_INBOUND_SCANNED;
                    }

                    $inboundOrder->end_datetime = time();
                    $inboundOrder->save(false);

                    //Пересчет количества товаров в ConsignmentInboundOrders
                    if ($consignmentInboundOrder = $inboundOrder->parentOrder) {

                        if (intval($consignmentInboundOrder->accepted_qty) < 1) {
                            $consignmentInboundOrder->begin_datetime = time();
                            $consignmentInboundOrder->status = Stock::STATUS_INBOUND_SCANNING;
                        }

                        $consignmentInboundOrder->recalculateOrderItems();
                    }
                }
            }

            echo '.';
        }

        echo 'other/inbound-left-product-count start' . "\n";
        return 0;
    }


    /* Меням номер TIR для Colins
     *
     */
    public function actionChangePartyNumber()
    {
        echo "Выполнен на живом, повторно выполнять не нужно";
        return 0;
        echo 'other/change-party-number begin' . "\n";

        $validOrderNumber = 'Tir-8-2015';


        if ($consignmentInboundOrder = ConsignmentInboundOrders::find()->andWhere(['client_id' => Client::CLIENT_COLINS, 'party_number' => 'Tir-1'])->one()) {
            InboundOrder::updateAll(['parent_order_number' => $validOrderNumber], ['client_id' => Client::CLIENT_COLINS, 'consignment_inbound_order_id' => $consignmentInboundOrder->id]);
            $consignmentInboundOrder->party_number = $validOrderNumber;
            $consignmentInboundOrder->save(false);

            echo 'Inbound done' . "\n";
        }

        if ($consignmentOutboundOrder = ConsignmentOutboundOrder::find()->andWhere(['client_id' => Client::CLIENT_COLINS, 'party_number' => 'Tir-1'])->one()) {
            if ($outboundOrders = $consignmentOutboundOrder->orders) {
                foreach ($outboundOrders as $oo) {
                    $oo->parent_order_number = $validOrderNumber;
                    $oo->order_number = str_replace('Tir-1', $validOrderNumber, $oo->order_number);
                    $oo->save(false);

                    if ($dpo = TlDeliveryProposalOrders::find()
                        ->andWhere([
                            'client_id' => Client::CLIENT_COLINS,
                            'order_type' => TlDeliveryProposalOrders::ORDER_TYPE_RPT,
                            'order_id' => $oo->id])
                        ->one()
                    ) {
                        if ($dp = $dpo->deliveryProposal) {
                            $dp->extra_fields = str_replace('Tir-1', $validOrderNumber, $dp->extra_fields);
                            $dp->save(false);
                            echo 'Delivery Proposal done' . "\n";
                        }

                        $dpo->order_number = str_replace('Tir-1', $validOrderNumber, $dpo->order_number);
                        $dpo->save(false);
                        echo 'Delivery Proposal Order done' . "\n";
                    }

                }
            }

            $consignmentOutboundOrder->party_number = $validOrderNumber;
            $consignmentOutboundOrder->save(false);
            echo 'Outbound done' . "\n";
        }


        echo 'other/change-party-number end' . "\n";
        return 0;
    }


    /*
     *
     * */
    public function actionAddLostProductToStock()
    {
        echo 'other/add-lost-product-to-stock start' . "\n";
        echo "Выполнен на живом, повторно выполнять не нужно";
        return 0;
        //1 - Добавляем недостающие товары на сток УРА!
        /*        $inboundOrderAll =  InboundOrder::findAll(['client_id'=>Client::CLIENT_COLINS]);

                foreach($inboundOrderAll as $orderModel) {
                    $inboundOrderItemAll = InboundOrderItem::find()->where(['inbound_order_id'=>$orderModel->id])->all();
                    $str = '';
                    foreach($inboundOrderItemAll as $orderItemModel) {
                        if(($count = Stock::find()->andWhere(['inbound_order_id'=>$orderItemModel->inbound_order_id,'client_id'=>Client::CLIENT_COLINS,'product_barcode'=>$orderItemModel->product_barcode])->count()) != $orderItemModel->expected_qty) {
                            echo $str.= $orderItemModel->product_barcode." ".$orderModel->order_number." ".$count." ".$orderItemModel->expected_qty."\n";
                            $end = $orderItemModel->expected_qty - $count;
                            for($i=0; $i<$end; $i++) {
                                $stock = new Stock();
                                $stock->client_id = Client::CLIENT_COLINS;
                                $stock->inbound_order_id = $orderModel->id;
                                $stock->product_barcode = $orderItemModel->product_barcode;
                                $stock->product_model = $orderItemModel->product_model;
                                $stock->status = Stock::STATUS_INBOUND_SORTING;
                                $stock->status_availability = Stock::STATUS_AVAILABILITY_NOT_SET;
        //                        $stock->detachBehavior('auditBehavior');
                                $stock->save(false);
                            }
                            file_put_contents('add-lost-product-to-stock-inbound.log',$str,FILE_APPEND);
                        }
                    }
                }*/

        // 2 -  Убераем сверх зарезервированные товары УРА!
//        $inboundIDs = InboundOrder::find()->select('id')->where(['client_id'=>Client::CLIENT_COLINS,'status'=>Stock::STATUS_INBOUND_SORTED])->column();

        $outboundOrderAll = OutboundOrder::findAll(['client_id' => Client::CLIENT_COLINS, 'id' => 343, 344, 342, 341, 324]); //,'id'=>343,344,342,341,324
        foreach ($outboundOrderAll as $orderModel) {
            $outboundOrderItemAll = OutboundOrderItem::find()->where(['outbound_order_id' => $orderModel->id])->all();
            $str = '';
            foreach ($outboundOrderItemAll as $orderItemModel) {
                if (($count = Stock::find()->where(['outbound_order_id' => $orderItemModel->outbound_order_id, 'client_id' => Client::CLIENT_COLINS, 'product_barcode' => $orderItemModel->product_barcode])->count()) > $orderItemModel->allocated_qty) {
                    $end = $count - $orderItemModel->allocated_qty;
                    echo $str .= $orderItemModel->product_barcode . " " . $count . " " . $orderItemModel->allocated_qty . " " . $end . "\n";

                    for ($i = 0; $i < $end; $i++) {
                        if ($stock = Stock::find()->where(['outbound_order_id' => $orderItemModel->outbound_order_id, 'client_id' => Client::CLIENT_COLINS, 'product_barcode' => $orderItemModel->product_barcode])->one()) {
                            $stock->client_id = Client::CLIENT_COLINS;
                            $stock->outbound_order_id = 0;
                            $stock->status = Stock::STATUS_INBOUND_SORTING;
                            $stock->status_availability = Stock::STATUS_AVAILABILITY_NOT_SET;
//                            $stock->detachBehavior('auditBehavior');
                            $stock->save(false);
                        }
                    }
                    file_put_contents('add-lost-product-to-stock-outbound-big-allocated.log', $str, FILE_APPEND);
                }
            }
        }

        // Добавляем в заказ недостающие зарезервированные товары
        /*        $inboundIDs = InboundOrder::find()->select('id')->where(['client_id'=>Client::CLIENT_COLINS,'status'=>Stock::STATUS_INBOUND_SORTED])->column();
                $outboundOrderAll =  OutboundOrder::findAll(['client_id'=>Client::CLIENT_COLINS]);

                foreach($outboundOrderAll as $orderModel) {
                    $outboundOrderItemAll =  OutboundOrderItem::find()->where(['outbound_order_id'=>$orderModel->id])->all();
                    foreach($outboundOrderItemAll as $orderItemModel) {
                        $str = '';
                        if($orderItemModel->allocated_qty > 0) {
                            if(($count = Stock::find()->where(['inbound_order_id'=>$inboundIDs,'outbound_order_id'=>$orderItemModel->outbound_order_id,'client_id'=>Client::CLIENT_COLINS,'product_barcode'=>$orderItemModel->product_barcode])->count()) < $orderItemModel->allocated_qty) {
                                echo $str .=  $orderItemModel->product_barcode . " " . $count . " " . $orderItemModel->allocated_qty . " " . $orderItemModel->outbound_order_id . "\n";
                                $end = $orderItemModel->allocated_qty - $count;
                                for($i=0; $i<$end; $i++) {
                                    if($stock = Stock::find()->where(['inbound_order_id'=>$inboundIDs,'status_availability'=>Stock::STATUS_AVAILABILITY_NOT_SET,'client_id'=>Client::CLIENT_COLINS,'product_barcode'=>$orderItemModel->product_barcode])->one()){
                                        $stock->client_id = Client::CLIENT_COLINS;
                                        $stock->outbound_order_id = $orderItemModel->outbound_order_id;
                                        $stock->status = Stock::STATUS_INBOUND_SORTED;
                                        $stock->status_availability = Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED;
        //                                $stock->detachBehavior('auditBehavior');
                                        $stock->save(false);
                                    } else {
                                        //$orderItemModel->allocated_qty -= 1;
                                        //$orderItemModel->save(false);

                                        //$orderModel->allocated_qty -= 1;
                                        //$orderModel->save(false);
                                        echo $str .=  $orderItemModel->product_barcode . " " . $count . " " . $orderItemModel->allocated_qty. " " . $orderItemModel->outbound_order_id . " Stock empty"."\n";
                                    }
                                }
                            } else {
                                echo $str .=  $orderItemModel->product_barcode . " " . $count . " " . $orderItemModel->allocated_qty. " " . $orderItemModel->outbound_order_id . " count == allocate"."\n";
                            }

                            file_put_contents('add-lost-product-to-stock-outbound.log',$str,FILE_APPEND);
                        }
                    }
                }*/
        // 8680594288750 6 4

        //Выводим все заказы у которых количество зарезервированных неравно колличеству товаров на складе
        $outboundOrderAll = OutboundOrder::findAll(['client_id' => Client::CLIENT_COLINS]);
        foreach ($outboundOrderAll as $orderModel) {
            $str = '';
            if (($count = Stock::find()->where(['outbound_order_id' => $orderModel->id, 'client_id' => Client::CLIENT_COLINS])->count()) != $orderModel->allocated_qty) {
                echo $str .= $orderModel->expected_qty . " " . $count . " " . $orderModel->allocated_qty . " " . $orderModel->id . "\n";
            }
            file_put_contents('add-lost-product-to-stock-outbound-big-allocated.log', $str, FILE_APPEND);
        }

//        if($party = ConsignmentInboundOrders::find()->andWhere(['party_number'=>'Tir-8-2015'])->one()){
//            $party->recalculateOrderItems();
//        }
//
//        if($party = ConsignmentOutboundOrder::find()->andWhere(['party_number'=>'Tir-8-2015'])->one()){
//            $party->recalculateOrderItems();
//        }

        echo 'other/add-lost-product-to-stock end' . "\n";
        return 0;
    }

    /*
     *
     * */
    public function actionRemoveNoExistsInboundOrderItems()
    {
        echo 'other/remove-no-exists-inbound-order-items start' . "\n";
//        echo "Выполнен на живом, повторно выполнять не нужно";
//        return 0;
        $ioItemAll = InboundOrderItem::find()->all();
        foreach ($ioItemAll as $item) {
            if (!InboundOrder::find()->where(['id' => $item->inbound_order_id])->exists()) {
                $item->delete();
                echo $item->inbound_order_id . "\n";
            }
        }

        echo 'other/remove-no-exists-inbound-order-items end' . "\n";
        return 0;
    }

    public function actionFindProblemItems()
    {
        return 0;
        $path = 'console/input-files/outbound-compare.csv';
        $outboundOrderID = 338;
        $overQty = 0;
        $underQty = 0;

        if (($handle = fopen($path, "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $row++;
                if ($row > 1) {
                    //только заполненные строки
                    if (isset($data[0]) && !empty($data[0])) {
                        $product_qty = '';
                        $product_barcode = $data[0];
                        if (isset($data[1]) && !empty($data[1])) {
                            $product_qty = $data[1];
                        }
                        $item = OutboundOrderItem::find()
                            ->andWhere([
                                'outbound_order_id' => $outboundOrderID,
                                'product_barcode' => $product_barcode])
                            ->one();
                        //если в файле нету этого товара, а в заказе есть - пишем в лог
                        if (!$product_qty && $item) {
                            file_put_contents('console/compare.log', " LISHNIY TOVAR : product barcode = " . $item->product_barcode . '----' . $item->expected_qty . "\n", FILE_APPEND);
                            $underQty++;

                        } //если в файле есть, а в заказе нету
                        elseif ($product_qty && !$item) {
                            file_put_contents('console/compare.log', " NEHVATAET : product barcode = " . $product_barcode . '----' . $product_qty . "\n", FILE_APPEND);
                            $overQty += $product_qty;
                        }

                    }
                }
            }
            file_put_contents('console/compare.log', 'Over = ' . $overQty . "\n", FILE_APPEND);
            file_put_contents('console/compare.log', 'Under = ' . $underQty . "\n", FILE_APPEND);
            fclose($handle);
        }
    }

    public function actionUpdateProductPrice()
    {
        //Выполнен на живом
        return 0;
        echo 'other/update-product-price start' . "\n";
        $path = 'console/input-files/inbound-tir8.csv';
        $client_id = Client::CLIENT_COLINS;

        //Всего строк в таблице
        $rowAll = 0;

        $fileDump = [];
        $preparedData = [];

        if (($handle = fopen($path, "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $row++;
                if ($row > 1) {
                    $fileDump[$row]['box_barcode'] = $data[0];
                    $fileDump[$row]['product_barcode'] = $data[1];
                    $fileDump[$row]['product_model'] = $data[2];
                    $fileDump[$row]['product_sku'] = $data[3];
                    $fileDump[$row]['product_color'] = $data[4];
                    $fileDump[$row]['product_size'] = $data[5];
                    $fileDump[$row]['product_season'] = $data[6];
                    $fileDump[$row]['product_made_in'] = $data[8];
                    $fileDump[$row]['product_composition'] = $data[9];
                    $fileDump[$row]['product_category'] = $data[10];
                    $fileDump[$row]['product_gender'] = $data[11];
                    $fileDump[$row]['product_price'] = $data[12];
                }
            }

            fclose($handle);
            $rowAll = count($fileDump);
        }

        if ($fileDump) {
            foreach ($fileDump as $key => $value) {
                if (!empty($value['box_barcode'])) {
                    $price = trim($value['product_price']);
                    $price = str_replace(" ", "", $price);
                    $price = str_replace(",", ".", $price);
                    $preparedData[$value['box_barcode']]['items'][$key]['product_barcode'] = $value['product_barcode'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_model'] = $value['product_model'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_sku'] = $value['product_sku'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_color'] = $value['product_color'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_size'] = $value['product_size'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_season'] = $value['product_season'];
                    // $preparedData[$value['box_barcode']]['items'][$key]['product_qty'] = $value['product_qty'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_made_in'] = $value['product_made_in'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_composition'] = $value['product_composition'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_category'] = $value['product_category'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_gender'] = $value['product_gender'];
                    $preparedData[$value['box_barcode']]['items'][$key]['product_price'] = $price;
                }
            }

        }

        if ($preparedData) {
            foreach ($preparedData as $data) {
                if (isset($data['items']) && is_array($data['items'])) {
                    foreach ($data['items'] as $productRow) {
                        $product = ProductBarcodes::getProductByBarcode($client_id, $productRow['product_barcode']);
                        if (!$product) {
                            $product = new Product();
                        }
                        $product->client_id = $client_id;
                        $product->sku = $productRow['product_sku'];
                        $product->price = $productRow['product_price'];
                        $product->model = $productRow['product_model'];
                        $product->color = $productRow['product_color'];
                        $product->size = $productRow['product_size'];
                        $product->season = $productRow['product_season'];
                        $product->made_in = $productRow['product_made_in'];
                        $product->composition = $productRow['product_composition'];
                        $product->category = $productRow['product_category'];
                        $product->gender = $productRow['product_gender'];
                        $product->status = Product::STATUS_ACTIVE;
                        $product->save(false);
                        echo $productRow['product_price'] . "\n";
                    }
                }
            }
        }
        echo "\n" . 'Row in table: ' . $rowAll . "\n";

        echo 'other/update-product-price end' . "\n";
        return 0;
    }


    /*
     *
     *
     * */
    public function actionInboundAcceptedToStock()
    {
        echo 'other/inbound-accepted-to-stock begin' . "\n";
        return '0';
        $client_id = Client::CLIENT_COLINS;
        $model = new \stdClass();
        $model->party_number = '13';

        $inboundID = InboundOrder::find()->select('id')
            ->andWhere(['consignment_inbound_order_id' => $model->party_number, 'client_id' => Client::CLIENT_COLINS])
            ->asArray()
            ->column();


        $items = Stock::find()
            ->select('status_availability , id, inbound_order_id, outbound_order_id, product_barcode, product_model, status, primary_address, secondary_address')
            ->andWhere([
                'inbound_order_id' => $inboundID,
                'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET,
                'status' => [
                    Stock::STATUS_INBOUND_SORTING,
//                            Stock::STATUS_INBOUND_SCANNED,
//                            Stock::STATUS_INBOUND_SCANNING,
                ]
            ])
//            ->groupBy('product_barcode')
            ->orderBy('product_barcode')
            ->asArray()
            ->all();

//        VarDumper::dump($items);

        if ($items) {
            $i = 0;
            $box_barcode = '701_IN_START';
            foreach ($items as $item) {

                $product_barcode = $item['product_barcode'];
                if ($i == 0) {
                    $box_barcode = '70_in' . rand(10000, 999999);
                }
                if ($i == 10) {
                    $i = 0;
                } else {
                    $i++;
                }
                echo $box_barcode . "\n";
                $stock = Stock::setStatusInboundScannedValueByConsignmentOrder($client_id, $model->party_number, $product_barcode, $box_barcode);

            }
        }

        echo 'other/inbound-accepted-to-stock end' . "\n";
        return 0;
    }

    /*
     * Для пересчета колинс ускаман
     * */
    public function actionColinsUskaman()
    {
        echo 'other/colins-uskaman start' . "\n";

        $outboundOne = OutboundOrder::findOne(['id' => '343']);
        $stockAll = Stock::find()->where(['outbound_order_id' => $outboundOne->id])->groupBy('inbound_order_id')->all();
        $sum = 0;
        if ($stockAll) {
            foreach ($stockAll as $stock) {
                $inboundOne = InboundOrder::findOne(['id' => $stock->inbound_order_id]);
                if ($inboundOne) {
                    $countInStockAccepted = Stock::find()->where(['inbound_order_id' => $inboundOne->id, 'client_id' => Client::CLIENT_COLINS, 'status' => [34, 19]])->andWhere('status_availability = 3')->count();
                    if ($countInStockAccepted != $inboundOne->accepted_qty) {
//                        echo $inboundOne->id.' = '.$countInStockAccepted." ".$inboundOne->accepted_qty."\n";
                        $sum += $countInStockAccepted - $inboundOne->accepted_qty;

                        $inboundItemAll = InboundOrderItem::find()
                            ->where([
                                'inbound_order_id' => $inboundOne->id,
                            ])
                            ->all();
                        if ($inboundItemAll) {
                            foreach ($inboundItemAll as $inboundItem) {
                                $countInStockInboundItem = Stock::find()->where(['product_barcode' => $inboundItem->product_barcode, 'inbound_order_id' => $inboundOne->id, 'client_id' => Client::CLIENT_COLINS, 'status' => [34, 19]])->andWhere('status_availability = 3')->count();
                                if ($countInStockInboundItem != $inboundItem->accepted_qty) {
                                    echo $inboundOne->id . ' = ' . $countInStockInboundItem . " " . $inboundItem->accepted_qty . "\n";
                                    $inboundItem->accepted_qty = $countInStockInboundItem;
                                    $inboundItem->save(false);
                                }
                            }
                        }
                    }
                }
            }
        }


        echo 'Sum : ' . $sum . "\n";
        echo 'other/colins-uskaman end' . "\n";
        return 0;
    }

    public function actionResave()
    {
        echo "Выполнен на живом";
        return 0;
        $orders = OutboundOrder::find()->andWhere('created_at > 1431464400')->all();

        foreach ($orders as $order) {
            $order->save(false);
        }
    }

    public function actionUpdateColinsBarcodes()
    {
        echo "Выполнен на живом, повторно выполнять не нужно";
        return 0;
        echo 'other/update-colins-barcodes start' . "\n";
        $counter = 0;
        $barcodesMap = [
            '8680594450522' => '8680594268271',
            '8680594450492' => '8680594268240',
            '8680594450508' => '8680594268257',
            '8680594450515' => '8680594268264',
            '8680594450539' => '8680594268325',
            '8680594450553' => '8680594268349',
            '8680594450560' => '8680594268356',
            '8680594450546' => '8680594268332',
            '8680594450768' => '8680594271127',
            '8680594450799' => '8680594271158',
            '8680594450744' => '8680594271103',
            '8680594450713' => '8680594271073',
            '8680594450751' => '8680594271110',
            '8680594450690' => '8680594271059',
            '8680594450676' => '8680594271035',
            '8680594450782' => '8680594271141',
            '8680594450720' => '8680594271080',
            '8680594450669' => '8680594271028',
            '8680594450683' => '8680594271042',
            '8680594450737' => '8680594271097',
            '8680594450706' => '8680594271066',
            '8680594450775' => '8680594271134',
            '8680594450652' => '8680594271011',
        ];

        foreach ($barcodesMap as $oldBarcode => $newBarcode) {
            if ($inboundOrderItems = InboundOrderItem::find()->andWhere(['product_barcode' => $oldBarcode])->all()) {

                foreach ($inboundOrderItems as $ioi) {
                    if ($inboundOrder = InboundOrder::findOne($ioi->inbound_order_id)) {
                        if ($inboundOrder->client_id == Client::CLIENT_COLINS) {
                            $ioi->product_barcode = $newBarcode;
                            $ioi->save(false);
                            $counter++;
                        }
                    }

                }
            }

            if ($outboundOrderItems = OutboundOrderItem::find()->andWhere(['product_barcode' => $oldBarcode])->all()) {
                foreach ($outboundOrderItems as $ooi) {
                    if ($outboundOrder = OutboundOrder::findOne($ooi->outbound_order_id)) {
                        if ($outboundOrder->client_id == Client::CLIENT_COLINS) {
                            $ooi->product_barcode = $newBarcode;
                            $ooi->save(false);
                            $counter++;
                        }
                    }
                }
            }

            if ($stockItems = Stock::find()->andWhere(['client_id' => Client::CLIENT_COLINS, 'product_barcode' => $oldBarcode])->all()) {
                foreach ($stockItems as $si) {
                    $si->product_barcode = $newBarcode;
                    $si->save(false);
                    $counter++;
                }
            }

            if ($barcodeItems = ProductBarcodes::find()->andWhere(['client_id' => Client::CLIENT_COLINS, 'barcode' => $oldBarcode])->all()) {
                foreach ($barcodeItems as $bi) {
                    $bi->barcode = $newBarcode;
                    $bi->save(false);
                    $counter++;
                }
            }
            echo '.';
        }
        echo $counter . ' records was updated ' . "\n";
        echo 'other/update-colins-barcodes end' . "\n";
        return 0;
    }

    public function actionFixColinsStatus()
    {
        echo "Выполнен на живом";
        return 0;
        //выполнен на живом
        $coo = ConsignmentOutboundOrder::findOne(22);
        $counter = 0;
        if ($outboundOrders = $coo->orders) {
            foreach ($outboundOrders as $oo) {
                if ($oo->date_left_warehouse) {
                    $oo->status = Stock::STATUS_OUTBOUND_ON_ROAD;
                    $oo->save(false);
                    $counter++;
                }
            }
        }

        echo $counter . ' records was updated ' . "\n";
    }

    public function actionFindOutStockProduct()
    {
        return 0;
        echo 'other/find-out-stock-product begin' . "\n";
        $outPath = 'tmp/';
        $coo = ConsignmentOutboundOrder::findOne(28);
        //$data =[];

        BaseFileHelper::createDirectory($outPath);
        $fileName = 'colins-out-of-stock.csv';
        $fh = fopen($outPath . $fileName, 'w');

//        foreach ($parsedData as $rec) {
//            fputcsv($fh, $rec, ';');
//        }

        $outboundOrders = $coo->orders;

        foreach ($outboundOrders as $order) {
            $orderItems = $order->orderItems;

            foreach ($orderItems as $ioo) {
//                if(!ProductBarcodes::find()->andWhere(['client_id'=> Client::CLIENT_COLINS, 'barcode'=>$ioo->product_barcode])->exists()){
//                    fputcsv($fh, [$order->toPoint->shop_code, $ioo->product_barcode, $ioo->expected_qty], ';');
//                    echo '.';
//                }

                if (!Stock::find()->andWhere(['client_id' => Client::CLIENT_COLINS, 'product_barcode' => $ioo->product_barcode])->exists()) {
                    fputcsv($fh, [$order->toPoint->shop_code, $ioo->product_barcode, $ioo->expected_qty], ';');
                    echo '.';
                }
            }

        }
        fclose($fh);
        echo 'other/find-out-stock-product end' . "\n";
    }


    /*
     * Remove problem data in ctock colins
     * */
    public function actionProblemProductInStockColins()
    {
        echo 'other/problem-product-in-stock-colins start' . "\n";
        echo 'уже выполнен на живом';
        return 0;
//        echo "Уже вополнялся на живом, повторно выполнять на живом не нужно";
//        return 0;
        $inboundOrderAll = InboundOrder::findAll(['client_id' => Client::CLIENT_COLINS]);
        $str = '';
        foreach ($inboundOrderAll as $orderModel) {
            $itemQty = 0;
            $inboundOrderItemAll = InboundOrderItem::find()->where(['inbound_order_id' => $orderModel->id])->all();

            foreach ($inboundOrderItemAll as $orderItemModel) {
                $count = Stock::find()
                    ->andWhere(['inbound_order_id' => $orderItemModel->inbound_order_id, 'client_id' => Client::CLIENT_COLINS, 'product_barcode' => $orderItemModel->product_barcode])
                    ->andWhere(['status_availability' => [2]])
                    ->count();

                if ($count != $orderItemModel->accepted_qty) {
                    echo $str .= $orderItemModel->product_barcode . " " . $orderItemModel->id . " " . $count . " " . $orderItemModel->accepted_qty . "   " . $orderModel->order_number . "\n";
                    $orderItemModel->accepted_qty = $count;
//                    $orderItemModel->save(false);
                }
                $itemQty += $orderItemModel->accepted_qty;
            }

            $orderModel->accepted_qty = $itemQty;
//            $orderModel->save(false);

        }
        file_put_contents('add-lost-product-to-stock-inbound-' . date("d-m-Y-H-i-s") . '.log', $str, FILE_APPEND);

        /*       $stockAll =  Stock::find()->where("client_id = '1' AND box_barcode IS NULL AND primary_address = ''")->all();
                $str = '';
                if($stockAll) {
                    foreach ($stockAll as $stock) {
                        $inStockOverInboundScanned = Stock::find()->where(
                            [
                                'inbound_order_id' => 4928,
                                'product_barcode' => $stock->product_barcode,
                            ]
                        )->one();
                        if ($inStockOverInboundScanned) {
        //                    $stock->inbound_order_id = $inStockOverInboundScanned->inbound_order_id;
                            $stock->primary_address = $inStockOverInboundScanned->primary_address;
                            $stock->outbound_order_id = 0;
        //                    $stock->status = $inStockOverInboundScanned->status;
                            $stock->status = Stock::STATUS_INBOUND_SCANNED;
                            $stock->status_availability = $inStockOverInboundScanned->status_availability;
                            $stock->save(false);



                            $inStockOverInboundScanned->delete();
                            $str .= $stock->id.' '.$stock->product_barcode."\n";
                            echo $stock->id.' '.$stock->product_barcode."\n";
                        }
                    }
                }
                file_put_contents('problem-product-in-stock-colins-'.date("d-m-Y-H-i-s").'.log',$str,FILE_APPEND);*/

        /*        $inboundOverOne =  InboundOrder::findOne(4928);
                $inboundItemOverAll = InboundOrderItem::find()->where(['inbound_order_id'=>$inboundOverOne->id])->all();
                $str = '';
                foreach($inboundItemOverAll as $orderItemModel) {
                    if(($count = Stock::find()->andWhere(['inbound_order_id'=>$orderItemModel->inbound_order_id,'client_id'=>Client::CLIENT_COLINS,'product_barcode'=>$orderItemModel->product_barcode])->count()) != $orderItemModel->accepted_qty) {
                        $str .= $count.' '.$orderItemModel->product_barcode."\n";
                        echo $count.' '.$orderItemModel->product_barcode."\n";
                        $orderItemModel->accepted_qty = $count;

                        if($orderItemModel->accepted_qty < 1) {
                            $orderItemModel->delete();
                        }
                    }
                }
                $inboundOverOne->accepted_qty = InboundOrderItem::find()->where('inbound_order_id = 4928')->sum('accepted_qty');
                $inboundOverOne->save(false);

                file_put_contents('InboundOrder-problem-product-in-stock-colins-'.date("d-m-Y-H-i-s").'.log',$str,FILE_APPEND);*/

//        Stock::deleteAll("client_id = '1' AND box_barcode IS NULL AND primary_address = ''");
//

        echo 'other/problem-product-in-stock-colins end' . "\n";
        return 0;
    }

    /*
     * Add consignment inbound order to stock for reports
     *
     * */
    public function actionAddConsignmentInboundOrderIdToStock()
    {
        echo 'other/add-consignment-inbound-order-id-to-stock start' . "\n";
        echo 'уже выполнен на живом';
        return 0;

        $inboundOrderAll = InboundOrder::findAll(['client_id' => Client::CLIENT_COLINS]);
        foreach ($inboundOrderAll as $orderModel) {
            $inboundOrderItemAll = InboundOrderItem::find()->where(['inbound_order_id' => $orderModel->id])->all();
            foreach ($inboundOrderItemAll as $orderItemModel) {
                $stockAll = Stock::find()->where(['inbound_order_id' => $orderItemModel->inbound_order_id])->all();
                if ($stockAll) {
                    foreach ($stockAll as $stock) {
                        $stock->consignment_inbound_id = $orderModel->consignment_inbound_order_id;
                        $stock->save(false);
                        echo $stock->id . "\n";
                    }
                }
            }
        }
        echo 'other/add-consignment-inbound-order-id-to-stock end' . "\n";
        return 0;
    }


    public function actionAddColinsStockItems()
    {
        echo 'other/add-colins-stock-items start' . "\n";
        echo 'уже выполнен на живом, повторно выполнять нельзя';
//        Stock::updateAll(['primary_address'=>'700000063583','secondary_address'=>'2-6-15-1'],['inbound_order_id'=>'4931']);
        return 0;

        $path = 'console/input-files/colins-tir-8-dop.csv';
        $client_id = Client::CLIENT_COLINS;
        $consignmentInboundOrder = ConsignmentInboundOrders::find()->andWhere(['client_id' => $client_id, 'party_number' => 'Tir-8-2015'])->one();
        $io = new InboundOrder();
        $io->client_id = Client::CLIENT_COLINS;
        $io->parent_order_number = $consignmentInboundOrder->party_number;
        $io->client_box_barcode = '';
        $io->order_number = $consignmentInboundOrder->id . '-' . date('dmy');
        $io->consignment_inbound_order_id = $consignmentInboundOrder->id;
        $io->order_type = InboundOrder::ORDER_TYPE_INBOUND;
        $io->delivery_type = InboundOrder::DELIVERY_TYPE_CROSS_DOCK_A;
        $io->status = Stock::STATUS_INBOUND_COMPLETE;
//        $io->save(false);

        $stockCounter = 0;
        $itemsCounter = 0;
        $pbCounter = 0;
        $productCounter = 0;

        $fileDump = [];

        if (($handle = fopen($path, "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $row++;
                if ($row > 1) {
                    $price = trim($data[12]);
                    $price = str_replace(" ", "", $price);
                    $price = str_replace(",", ".", $price);
                    $fileDump[$row]['box_barcode'] = $data[0];
                    $fileDump[$row]['product_barcode'] = $data[1];
                    $fileDump[$row]['product_model'] = $data[2];
                    $fileDump[$row]['product_sku'] = $data[3];
                    $fileDump[$row]['product_color'] = $data[4];
                    $fileDump[$row]['product_size'] = $data[5];
                    $fileDump[$row]['product_season'] = $data[6];
                    $fileDump[$row]['product_qty'] = $data[7];
                    $fileDump[$row]['product_made_in'] = $data[8];
                    $fileDump[$row]['product_composition'] = $data[9];
                    $fileDump[$row]['product_category'] = $data[10];
                    $fileDump[$row]['product_gender'] = $data[11];
                    $fileDump[$row]['product_price'] = $price;
                }
            }

            fclose($handle);
        }
        //var_dump($fileDump);die;
        if ($fileDump) {
            foreach ($fileDump as $item) {
                $ioi = new InboundOrderItem();
                $ioi->inbound_order_id = $io->id;
                $ioi->product_barcode = $item['product_barcode'];
                $ioi->product_model = $item['product_model'];
                $ioi->box_barcode = '';
                $ioi->expected_qty = $item['product_qty'];
                $ioi->status = Stock::STATUS_INBOUND_COMPLETE;
//                $ioi->save(false);
                $itemsCounter++;

                $product = ProductBarcodes::getProductByBarcode($client_id, $item['product_barcode']);
                if (!$product) {
                    $product = new Product();
                    $product->detachBehaviors();
                    $product->created_user_id = 2;
                    $product->save(false);
                    $pb = new ProductBarcodes();
                    $pb->client_id = $client_id;
                    $pb->product_id = $product->id;
                    $pb->barcode = $item['product_barcode'];
                    $pb->detachBehaviors();
                    $pb->created_user_id = 2;
//                    $pb->save(false);
                    $pbCounter++;
                }
                $product->client_id = $client_id;
                $product->sku = $item['product_sku'];
                $product->price = $item['product_price'];
                $product->model = $item['product_model'];
                $product->color = $item['product_color'];
                $product->size = $item['product_size'];
                $product->season = $item['product_season'];
                $product->made_in = $item['product_made_in'];
                $product->composition = $item['product_composition'];
                $product->category = $item['product_category'];
                $product->gender = $item['product_gender'];
                $product->status = Product::STATUS_ACTIVE;
//                $product->save(false);
                $productCounter++;


                for ($i = 0; $i < $item['product_qty']; $i++) {
                    $stock = new Stock();
                    $stock->client_id = $client_id;
                    $stock->inbound_order_id = $io->id;
                    $stock->inbound_order_item_id = $ioi->id;
                    $stock->inbound_order_number = $io->order_number;
                    $stock->consignment_inbound_id = $consignmentInboundOrder->id;
                    $stock->product_barcode = $item['product_barcode'];
                    $stock->product_model = $item['product_model'];
                    $stock->status = Stock::STATUS_INBOUND_COMPLETE;
                    $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
                    $stock->secondary_address = '';
                    $stock->primary_address = '70_1';
                    $stock->product_id = $product->id;
//                    $stock->save(false);
                    $ioi->accepted_qty++;
                    $stockCounter++;
                }
//                $ioi->save(false);

            }
//            $io->recalculateOrderItems();
            //$consignmentInboundOrder->recalculateOrderItems();
            echo 'InboundOrderItem: ' . $itemsCounter . "\n";
            echo 'Stock: ' . $stockCounter . "\n";
            echo 'Product Barcodes: ' . $pbCounter . "\n";
            echo 'Product: ' . $productCounter . "\n";


        }
        echo 'other/add-colins-stock-items end' . "\n";
        return 0;
    }

    /*
     *
     * */
    public function actionGetStatusFromAudit()
    {
        echo 'other/get-status-from-audit start' . "\n";
        echo 'уже выполнен на живом, повторно выполнять нельзя';
        return 0;
        $o = OutboundOrder::findOne(495);
        $o->status = Stock::STATUS_OUTBOUND_SCANNING;
        $o->save(false);

        $stockAll = Stock::find()->where(['outbound_order_id' => 495])->all();

        foreach ($stockAll as $stock) {
            if ($audit = StockAudit::find()->where([
                'parent_id' => $stock->id,
                'field_name' => 'status',
                'before_value_text' => '17',
                'after_value_text' => '19',
            ])->one()
            ) {
                $stock->status = Stock::STATUS_OUTBOUND_SCANNED;
                $stock->save(false);
                echo $stock->id . "\n";
                $o->accepted_qty = $o->accepted_qty + 1;
                $o->save(false);
            } else {
                $stock->status = Stock::STATUS_OUTBOUND_PICKED;
                $stock->save(false);
            }
        }

        echo 'other/get-status-from-audit end' . "\n";
        return 0;
    }

    /*
    *
    * */
    public function actionCopyDp()
    {
        echo "на живом не выполнять";
        return 0;
        // 1945, 1937, 1936, 1925
        // 1929, 1930, 1931, 1932, 1933, 1934, 2278, 2279, 2280
        // 2277, 2251

        echo 'other/copy-dp start' . "\n";
//        $id = 2616;
        $id = 1561;
        $id = 1561;
        $dp = TlDeliveryProposal::findOne($id);

        //создаем DP
        $newDp = new TlDeliveryProposal();
        $parentDpAttributes = $dp->getAttributes(null, ['id']);
        $newDp->setAttributes($parentDpAttributes, false);
//        $newDp->shipped_datetime = '1433143093'; // 2616
        $newDp->shipped_datetime = '1427882409'; // 1561
//        $newDp->save(false);
        $newDp->shipped_datetime = '1425885493';
        $newDp->save(false);
        //ищем все заказы для этой DP
        if ($deliveryOrders = $dp->proposalOrders) {
            foreach ($deliveryOrders as $do) {
                //копируем каждый заказ
                $doAttributes = $do->getAttributes(null, ['id']);
                $newDo = new TlDeliveryProposalOrders();
                $newDo->setAttributes($doAttributes, false);
                //устанавливаем для каждого созданного заказа ID созданной DP
                $newDo->tl_delivery_proposal_id = $newDp->id;
//                $newDo->save(false);

                //для каждого заказа ищем связанный заказ на отгрузку или кросс-док в зависимости от типа
                /*                if ($do->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK) {
                                    $orderClass = CrossDock::className();
                                } else {
                                    $orderClass = OutboundOrder::className();
                                }

                                if ($orderClass) {
                                    if ($order = $orderClass::find()->andWhere(['id' => $do->order_id])->one()) {
                                        //создаем копию заказа на отгрузку
                                        $newOrderAttributes = $order->getAttributes(null, ['id']);
                                        $newOrder = new $orderClass();
                                        $newOrder->setAttributes($newOrderAttributes,false);
                                        $newOrder->save(false);

                                        //в новом заказе для DP прописываем ID нового заказа на отгрузку
                                        $newDo->order_id = $newOrder->id;
                                        $newDo->save(false);
                                    }
                                }*/
            }
        }

        //ищем все маршруты заявки
        if ($proposalRoutes = $dp->proposalRoutes) {
            foreach ($proposalRoutes as $pr) {
                //копируем каждый
                $proposalRoutesAttributes = $pr->getAttributes(null, ['id']);
                $newProposalRoute = new TlDeliveryRoutes();
                $newProposalRoute->setAttributes($proposalRoutesAttributes, false);
                //перезаписываем DP ID копии
                $newProposalRoute->tl_delivery_proposal_id = $newDp->id;
//                $newProposalRoute->save(false);


                if ($carsByRoute = $pr->carsByRoute) {
                    foreach ($carsByRoute as $cbr) {
                        $cbrAttributes = $cbr->getAttributes(null, ['id']);
                        $newCbr = new TlDeliveryProposalRouteTransport();
                        $newCbr->setAttributes($cbrAttributes, false);
                        $newCbr->tl_delivery_proposal_id = $newDp->id;
                        $newCbr->tl_delivery_proposal_route_id = $newProposalRoute->id;
//                        $newCbr->save(false);

                        if ($routeCar = $cbr->routeCar) {
                            $routeCarAttributes = $routeCar->getAttributes(null, ['id']);
                            $newRouteCar = new TlDeliveryProposalRouteCars();
                            $newRouteCar->detachBehaviors();
                            $newRouteCar->setAttributes($routeCarAttributes, false);
                            $newRouteCar->created_user_id = 2;
                            $newRouteCar->save(false);
                            $newCbr->tl_delivery_proposal_route_cars_id = $newRouteCar->id;
//                            $newCbr->save(false);
                        }
                    }
                }

//                $newProposalRoute->save(false);
            }

        }
//        $newDp->save(false);

        echo 'other/copy-dp end' . "\n";
        return 0;
    }

    public function actionRestoreProposalFromBillingRecalculate()
    {
        return 0;
        //billing ID 1: from 20 => to 4
        //billing ID 30: from 98 => to 4
        $to = strtotime('30.06.2015 23:59:59');
        //$year = strtotime('01.01.2015 00:00:00');
        $client_id = 2;
        //$route_from = 20;
        $route_to = 4;

        if ($proposals = TlDeliveryProposal::find()
            ->andWhere(
                'client_id=:client_id AND status_invoice!=:status_invoice AND route_from  IN (20, 98) AND route_to = :route_to AND shipped_datetime < :to',
                [
                    ':client_id' => $client_id,
                    ':status_invoice' => TlDeliveryProposal::INVOICE_PAID,
                    //':route_from'=>$route_from,
                    ':route_to' => $route_to,
                    ':to' => $to,
                    //':year'=>$year,
                ])
            ->all()
        ) {
            foreach ($proposals as $proposal) {
                $bm = new BillingManager();
                if ($price_invoice_with_vat = $bm->getInvoicePriceForDP($proposal)) {
                    //$price_invoice = $bm->getInvoicePriceForDP($proposal, false);

                    if ($proposal->price_invoice_with_vat != $price_invoice_with_vat) {
                        echo '******************' . "\n";
                        echo $proposal->id . "\n";
                        echo $proposal->price_invoice_with_vat . "=>" . $price_invoice_with_vat . "\n";
                        echo '******************' . "\n";

                    }
                    // echo $proposal->price_invoice_with_vat. "=>" .$price_invoice_with_vat . "\n";

                }
            }
            //2222, 2260, 2369, 2413

        }


    }

    public function actionRestoreProposalFromBillingRecalculateA()
    {
        return 0;
        //billing ID 1: from 20 => to 4
        //billing ID 30: from 98 => to 4
        $from = strtotime('01.07.2015 00:00:00');
        $to = strtotime('31.07.2015 23:59:59');
        $client_id = 2;
        $route_to = 4;

        if ($proposals = TlDeliveryProposal::find()
            ->andWhere(
                'client_id=:client_id AND status_invoice!=:status_invoice AND route_from  IN (20, 98) AND route_to = :route_to AND shipped_datetime BETWEEN :from AND :to AND mc_actual >= 128 AND mc_actual <= 160',
                [
                    ':client_id' => $client_id,
                    ':status_invoice' => TlDeliveryProposal::INVOICE_PAID,
                    ':route_to' => $route_to,
                    ':to' => $to,
                    ':from' => $from,
                ])
            ->all()
        ) {
            foreach ($proposals as $proposal) {
                echo $proposal->id . "\n";

            }
            //2222, 2260, 2369, 2413

        }


    }


/* Скрипт генерирует адреса для полок из
 * заданых диапазонов
 **/
    public function actionGenerateRackAddress()
    {
        echo 'start other/generate-rack-address end' . "\n";
		// cd /home/www/vhosts/wms_kz/ && php yii other/generate-rack-address >/dev/null 2>&1

        $lvlMin = 100; //RackAddress::STAGE_MIN; //Этаж минимальное значение
        $lvlMax = 190; //RackAddress::STAGE_MAX*2; //Этаж максимальное значение

        $rowMin = 1; //RackAddress::ROW_MIN; //Ряд минимальное значение
        $rowMax = 1;//RackAddress::ROW_MAX*2; //Ряд максимальное значение

        $rackInRowMin = 1;  // RackAddress::RACK_MIN; //Полка(по горизонтали) в ряду минимальное значение
        $rackInRowMax = 10; // RackAddress::RACK_MAX * 5; //Полка(по горизонтали) в ряду максимальное значение

        $upperMin = null; // RackAddress::LEVEL_MIN; //Полка(по вертикале) в ряду минимальное значение
        $upperMax = 0; ///RackAddress::LEVEL_MAX; //Полка(по вертикале) в ряду максимальное значение
		$warehouseId = 404;

		// 400 - интермод новый склад, напольное хранение
		// 401 - интермод новый склад, хранение на стеллаже
		// 402 - интермод новый склад, хранение на мезонин
		// 403 - интермод новый склад, хранение на мезонин, 3 и 4 этаж
        for ($i1 = $lvlMin; $i1 <= $lvlMax; $i1++) {
            if ($a = RackAddress::createAddress($i1, $rowMin, $rackInRowMin, $upperMin,$warehouseId)) {
                echo 'address ' . $a . ' was generated' . "\n";
            } else {
                echo 'address was not created' . "\n";
            }
			unset($a);

            for ($i2 = $rowMin; $i2 <= $rowMax; $i2++) {
                if ($a = RackAddress::createAddress($i1, $i2, $rackInRowMin, $upperMin,$warehouseId)) {
                    echo 'address ' . $a . ' was generated' . "\n";
                } else {
                    echo 'address was not created' . "\n";
                }
				unset($a);

                for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {

                    if ($a = RackAddress::createAddress($i1, $i2, $i3, $upperMin,$warehouseId)) {
                        echo 'address ' . $a . ' was generated' . "\n";
                    } else {
                        echo 'address was not created' . "\n";
                    }
					//unset($a);
                   // for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
                   //     if ($a = RackAddress::createAddress($i1, $i2, $i3, $i4,$warehouseId)) {
                   //         echo 'address ' . $a . ' was generated' . "\n";
                     //   } else {
                   //         echo 'address was not created' . "\n";
                   //     }
                   //     unset($a);
                   // }
                }
            }
        }

//        $i = 0;
//        foreach (Stock::find()->each(50) as $stock) {
//            if ($address = RackAddress::find()->where(['address' => $stock->secondary_address])->one()) {
//                $stock->address_sort_order = $address->sort_order;
//                $stock->save(false);
//                echo $i++ . " " . $stock->secondary_address . ' ' . $address->sort_order . "\n";
//            }
//        }

        echo ' end other/generate-rack-address end' . "\n";
        return 0;
    }

    /*
    * Скрипт генерирует адреса для полок из
    * заданых диапазонов
    **/
    public function actionGenerateRackAddressWarehouse2()
    {
        echo 'start other/generate-rack-address-warehouse2 begin' . "\n";

        $lvlMin = 1; //Этаж минимальное значение
        $lvlMax = 8; //Этаж максимальное значение

        $rowMin = 1; //Ряд минимальное значение
        $rowMax = 60; //Ряд максимальное значение

        $rackInRowMin = 1; //Полка в ряду минимальное значение
        $rackInRowMax = 6; //Полка в ряду максимальное значение

        $upperMin = null; //Полка в ряду минимальное значение
//        $upperMax = 0; //Полка в ряду максимальное значение

        $warehouseID = 2; // Номер склада

        for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {

            for ($i1 = $lvlMin; $i1 <= $lvlMax; $i1++) {

                if ($a = RackAddress::createAddress($i1, $rowMin, $i3, $upperMin, $warehouseID)) {
                    echo 'address ' . $a . ' was generated' . "\n";
                } else {
                    echo 'address was not created' . "\n";
                }

                for ($i2 = $rowMin; $i2 <= $rowMax; $i2++) {

                    if ($a = RackAddress::createAddress($i1, $i2, $i3, $upperMin, $warehouseID)) {
                        echo 'address ' . $a . ' was generated' . "\n";
                    } else {
                        echo 'address was not created' . "\n";
                    }
                }
            }
        }
        echo ' end other/generate-rack-address end' . "\n";
        return 0;
    }

    /*
    * Скрипт генерирует адреса для полок из
    * заданых диапазонов
    **/
    public function actionGenerateRackAddressWarehouse1()
    {
        echo 'start other/generate-rack-address-warehouse1 begin' . "\n";

        $lvlMin = 1; //Этаж минимальное значение
        $lvlMax = 8; //Этаж максимальное значение

        $rowMin = 1; //Ряд минимальное значение
        $rowMax = 40; //Ряд максимальное значение

        $rackInRowMin = 1; //Полка в ряду минимальное значение
        $rackInRowMax = 6; //Полка в ряду максимальное значение

        $upperMin = null; //Полка в ряду минимальное значение
//        $upperMax = 0; //Полка в ряду максимальное значение

        $warehouseID = 2; // Номер склада

        for ($i1 = $lvlMin; $i1 <= $lvlMax; $i1++) {
            if ($a = RackAddress::createAddress($i1, $rowMin, $rackInRowMin, $upperMin, $warehouseID)) {
                echo 'address ' . $a . ' was generated' . "\n";
            } else {
                echo 'address was not created' . "\n";
            }

            for ($i2 = $rowMin; $i2 <= $rowMax; $i2++) {

                if ($a = RackAddress::createAddress($i1, $i2, $rackInRowMin, $upperMin, $warehouseID)) {
                    echo 'address ' . $a . ' was generated' . "\n";
                } else {
                    echo 'address was not created' . "\n";
                }

                for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {

                    if ($a = RackAddress::createAddress($i1, $i2, $i3, $upperMin, $warehouseID)) {
                        echo 'address ' . $a . ' was generated' . "\n";
                    } else {
                        echo 'address was not created' . "\n";
                    }
//                    for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
//                        if ($a = RackAddress::createAddress($i1, $i2, $i3, $i4, $warehouseID)) {
//                            echo 'address ' . $a . ' was generated' . "\n";
//                        } else {
//                            echo 'address was not created' . "\n";
//                        }
//                    }
                }
            }
        }

//        $i = 0;
//        foreach(Stock::find()->each(100) as $stock) {
//            if($address = RackAddress::find()->andWhere(['address'=>$stock->secondary_address])->one()) {
//                $stock->address_sort_order = $address->sort_order;
//                $stock->save(false);
//                echo $i++." ".$stock->secondary_address.' '.$address->sort_order."\n";
//            }
//        }

        echo ' end other/generate-rack-address end' . "\n";
        return 0;
    }


    /* Скрипт генерирует адреса для полок из
    * заданых диапазонов
    **/
    public function actionGenerateRackAddressTmp()
    {
        echo 'start other/generate-rack-address end' . "\n";

        $lvlMin = 7; //Этаж минимальное значение
        $lvlMax = 7; //Этаж максимальное значение

        $rowMin = 1; //Ряд минимальное значение
        $rowMax = 1; //Ряд максимальное значение

        $rackInRowMin = 1; //Полка в ряду минимальное значение
        $rackInRowMax = 41; //Полка в ряду максимальное значение

        $upperMin = 1; //Полка в ряду минимальное значение
        $upperMax = 1; //Полка в ряду максимальное значение
        $warehouseId = 3;

        for ($i1 = $lvlMin; $i1 <= $lvlMax; $i1++) {
            if ($a = RackAddress::createAddress($i1, $rowMin, $rackInRowMin, $upperMin, $warehouseId)) {
                echo 'address ' . $a . ' was generated' . "\n";
            } else {
                echo 'address was not created' . "\n";
            }

            for ($i2 = $rowMin; $i2 <= $rowMax; $i2++) {
                if ($a = RackAddress::createAddress($i1, $i2, $rackInRowMin, $upperMin, $warehouseId)) {
                    echo 'address ' . $a . ' was generated' . "\n";
                } else {
                    echo 'address was not created' . "\n";
                }

                for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {

                    if ($a = RackAddress::createAddress($i1, $i2, $i3, $upperMin, $warehouseId)) {
                        echo 'address ' . $a . ' was generated' . "\n";
                    } else {
                        echo 'address was not created' . "\n";
                    }
                    for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
                        if ($a = RackAddress::createAddress($i1, $i2, $i3, $i4, $warehouseId)) {
                            echo 'address ' . $a . ' was generated' . "\n";
                        } else {
                            echo 'address was not created' . "\n";
                        }
                    }
                }
            }
        }

        $i = 0;
        foreach (Stock::find()->each(100) as $stock) {
            if ($address = RackAddress::find()->where(['address' => $stock->secondary_address])->one()) {
                $stock->address_sort_order = $address->sort_order;
                $stock->save(false);
                echo $i++ . " " . $stock->secondary_address . ' ' . $address->sort_order . "\n";
            }
        }

        echo ' end other/generate-rack-address end' . "\n";
        return 0;
    }

    /*
    *
    *
    * */
    public function actionSetUserType()
    {
        echo 'other/set-user-type start' . "\n";

        $employeesAll = Employees::find()->all();
        if ($employeesAll) {
            foreach ($employeesAll as $employee) {
                if ($user = User::findOne($employee->user_id)) {
                    if ($user->id == 69) {
                        $user->user_type = User::USER_TYPE_POINT_WORKER;
                    } else {
                        $user->user_type = User::USER_TYPE_STOCK_WORKER;
                    }

                    $user->save(false);
                    echo $user->id . " " . "\n";
                }
            }
        }

        $employeesClientAll = ClientEmployees::find()->all();
        if ($employeesClientAll) {
            foreach ($employeesClientAll as $employee) {
                if ($user = User::findOne($employee->user_id)) {
                    $user->user_type = User::USER_TYPE_CLIENT;
                    $user->save(false);
                    echo $user->id . " " . "\n";
                }
            }
        }

        if ($user = User::findOne(30)) {
            $user->scenario = 'update';
            $user->user_type = User::USER_TYPE_BOSS;
            $user->username = 'Eomurzakov';
            $user->password = '1EOmurzakov1';
            $user->save(false);
            echo $user->password . ' ' . $user->id . " " . "\n";
        }

        if ($user = User::findOne(2)) {
            $user->scenario = 'update';
            $user->user_type = User::USER_TYPE_STOCK_WORKER;
            $user->save(false);
            echo $user->password . ' ' . $user->id . " " . "\n";

            $e = new Employees();
            $e->user_id = $user->id;
            $e->password = '';
            $e->username = $user->username;
            $e->email = $user->email;
            $e->first_name = $user->username;
            $e->manager_type = 4;
            $e->status = 1;
            $e->barcode = 13;
            $e->save(false);
        }

        echo 'other/set-user-type end' . "\n";
        return 0;
    }

    /*
     *
     * */
    public function actionSetExtraStatusInOutbound()
    {
        echo "На живом уже запускали. Повторно запускать не нужно";
        return 0;
        echo 'other/set-extra-status-in-outbound start' . "\n";

        $outboundAll = OutboundOrder::find()->where(['client_id' => 2])->all();

        foreach ($outboundAll as $outbound) {
//            if($outbound->client_id == 2) { // DeFacto
            $m = 'İşleminiz Başarı ile gerçekleştirildi.';
            if ($outbound->extra_fields) {
                $extra = \yii\helpers\Json::decode($outbound->extra_fields);
                if (isset($extra['RezerveDagitimResult'])) {
                    $m = $extra['RezerveDagitimResult'];
                    if (empty($m)) {
                        $m = '';
                    }
                }
                $outbound->extra_status = $m;
//                    $outbound->save(false);
                echo '.';
            }

            $outbound->extra_status = $m;
//                $outbound->save(false);
//            }
        }

        echo 'other/set-extra-status-in-outbound end' . "\n";
        return 0;
    }

    //
    public function actionResaveDp()
    {
        echo "На живом уже запускали. Повторно запускать не нужно";
        return 0;
        echo 'other/resave-dp start' . "\n";
        $query = TlDeliveryProposal::find();
        $fromData = '2015-09-01';
        $toData = '2015-09-30';
        $query->andWhere(['between', 'shipped_datetime', strtotime($fromData), strtotime($toData)]);
        $query->andWhere(['client_id' => 2]);
        $all = $query->all();
        $i = 0;
        foreach ($all as $model) {
            echo $model->id . "\n";
            $i++;
            echo $i . "\n";
            $dpManager = new DeliveryProposalManager(['id' => $model->id]);
            $dpManager->onUpdateProposal();
        }

        echo 'other/resave-dp end' . "\n";
        return 0;
    }

    //
    public function actionSetAcceptedNumberPlaces()
    {
        echo "На живом уже запускали. Повторно запускать не нужно";
        return 0;
        echo 'other/set-accepted-number-places start' . "\n";

        $oOrders = OutboundOrder::find()->andWhere(['cargo_status' => [OutboundOrder::CARGO_STATUS_ON_ROUTE, OutboundOrder::CARGO_STATUS_DELIVERED]])->all();
        $i = 0;
        foreach ($oOrders as $order) {
            $dpOrder = TlDeliveryProposalOrders::find()->andWhere(['order_id' => $order->id, 'order_type' => TlDeliveryProposalOrders::ORDER_TYPE_RPT])->one();
            if ($dpOrder) {
//                if($order->accepted_number_places_qty < 1 ) {
                if (empty($order->accepted_number_places_qty)) {
                    if ($dpOrder->number_places_actual < 1) {
                        $order->accepted_number_places_qty = $dpOrder->number_places;
                    } else {
                        $order->accepted_number_places_qty = $dpOrder->number_places_actual;
                    }
//                    $order->save(false);
                    echo $order->id . ' ' . $order->accepted_number_places_qty . "\n";
                    $i++;
                }
            }
        }

        echo $i;
        echo 'other/set-accepted-number-places end' . "\n";
        return 0;
    }

    //
    public function actionSetPaidInvoiceStatus()
    {
        echo "На живом уже запускали. Повторно запускать не нужно";
        return 0;
        echo 'other/set-paid-invoice-status start' . "\n";
        $from = strtotime('01.10.2014 00:00:00');
        $to = strtotime('30.09.2015 23:59:59');
        $client_id = 2;
        $i = 0;
        if ($proposals = TlDeliveryProposal::find()
            ->andWhere(
                'client_id=:client_id AND status_invoice!=:status_invoice  AND shipped_datetime BETWEEN :from AND :to',
                [
                    ':client_id' => $client_id,
                    ':status_invoice' => TlDeliveryProposal::INVOICE_PAID,
                    ':to' => $to,
                    ':from' => $from,
                ])
            ->all()
        ) {
            foreach ($proposals as $proposal) {
                echo $proposal->id . "\n";
                $proposal->status_invoice = TlDeliveryProposal::INVOICE_PAID;
//                $proposal->save(false);
                $i++;
            }
        }
        echo "\n" . $i . "\n";
        echo 'other/set-paid-invoice-status end' . "\n";
        return 0;
    }

    //
    public function actionSetCargoStatusDp()
    {
        echo 'other/set-cargo-status-dp end' . "\n";
        return 0;
        $all = CrossDock::find()->andWhere('cargo_status = ""')->all();
        foreach ($all as $cd) {
//           if($dpOrder = TlDeliveryProposalOrders::find()->andWhere(['order_id'=>$cd->id,'order_type'=>TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK])->one()) {
//               if($dp =  TlDeliveryProposal::find()->where(['id'=>$dpOrder->tl_delivery_proposal_id])->one()) {
//                if($dp->status == TlDeliveryProposal::STATUS_DELIVERED) {
            $cd->cargo_status = CrossDock::CARGO_STATUS_DELIVERED;
            $cd->status = Stock::STATUS_OUTBOUND_DELIVERED;
//                    $cd->save(false);
//                }
//              }
//               echo $dpOrder->tl_delivery_proposal_id."\n";
//           }

        }
        echo 'other/set-cargo-status-dp end' . "\n";
        return 0;
    }

    //
    public function actionSetZeroSecondary()
    {
        echo 'other/set-zero-secondary start' . "\n";
        echo "На живом уже запускали. Повторно запускать не нужно";
//        echo "TEST". "\n";
        return 0;
        $all = Stock::find()
//            ->select('count(product_barcode) as product_qty, product_barcode, product_model')
            ->andWhere(['client_id' => 2])
            ->andWhere(['status_availability' => Stock::STATUS_AVAILABILITY_YES])
//            ->andWhere('primary_address != :primary_address',[':primary_address'=>'0-inventory-0'])
//            ->andWhere('inventory_primary_address != ""')
//            ->groupBy('product_barcode')
//            ->orderBy([
//                'secondary_address'=>SORT_DESC,
//            ])
//            ->asArray()
            ->all();
        $i = 0;
        foreach ($all as $stock) {
            echo $stock->secondary_address . "\n";
            $stock->secondary_address = '';
            $stock->save(false);
            $i++;

        }
        echo $i . "\n";
        echo 'other/set-zero-secondary end' . "\n";
        return 0;
    }


    /**
     * @return int
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function actionReOpenExcel()
    {
        //

        $productsOnStock = Stock::find()
            ->select('stock.product_barcode as productBarcode, count(stock.product_barcode) as productQty, (select inbound_order_items.product_name from inbound_order_items where inbound_order_items.product_barcode = stock.product_barcode limit 1) as productName ')
//            ->select('stock.product_barcode as productBarcode, count(stock.product_barcode) as productQty, stock.product_name as productName ')
//            ->select('stock.product_barcode as productBarcode, count(stock.product_barcode) as productQty, inbound_order_items.product_name as productName ')
            ->andWhere([
                'client_id' => 95,
                'status_availability' => Stock::STATUS_AVAILABILITY_YES,
            ])
//            ->innerJoin(InboundOrderItem::tableName(), 'inbound_order_items.product_barcode = stock.product_barcode')
            ->groupBy('stock.product_barcode')
            //->limit(100)
            ->asArray();
            //->all();

        $excel = \PHPExcel_IOFactory::load('stockDepartment/web/tmp-file/car-parts/hak/stock-template.xlsx');
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $orderNumber = '000001'; // <Номер документа>
        $createdAt = '2019-01-23'; // <Дата составления>
        // Header
        $excelActive->setCellValue('AR13', $orderNumber);
        $excelActive->setCellValue('AW13', $createdAt);

        // Rows
        $row = 46;
        $i = 0;

        $rowQty = $productsOnStock->count();

        $excelActive->duplicateStyle($excelActive->getStyle('B47'),'B47:B'.$rowQty);
        $excelActive->duplicateStyle($excelActive->getStyle('E47'),'E47:E'.$rowQty);
        $excelActive->duplicateStyle($excelActive->getStyle('Q47'),'Q47:Q'.$rowQty);
        $excelActive->duplicateStyle($excelActive->getStyle('V47'),'V47:V'.$rowQty);
        $excelActive->insertNewRowBefore($row+1,$rowQty);

        foreach ($productsOnStock->each() as $product) {
            $i++;
            $row++;
            echo "$i"."\n";
//            $excelActive->insertNewRowBefore($row, 1);
            $excelActive->mergeCells("B" . $row . ":D" . $row);
            $excelActive->setCellValue('B' . $row, $i);
//
            $excelActive->mergeCells("E" . $row . ":P" . $row);
            $excelActive->setCellValue('E' . $row,$product['productName']);
//
            $excelActive->mergeCells("Q" . $row . ":U" . $row);
//            // Номенклатурный номер
            $excelActive->mergeCells("V" . $row . ":AA" . $row);
            $excelActive->setCellValue('V' . $row, $product['productBarcode']);
//
            $excelActive->mergeCells("AB" . $row . ":AE" . $row);
            $excelActive->setCellValue('AB' . $row, "ШТ");
//
            $excelActive->mergeCells("AF" . $row . ":AJ" . $row);
//            // количество
            $excelActive->mergeCells("AK" . $row . ":AN" . $row);
            $excelActive->setCellValue('AK' . $row, $product['productQty']);
//
            $excelActive->mergeCells("AO" . $row . ":AS" . $row);
//
//            // количество
            $excelActive->mergeCells("AT" . $row . ":AW" . $row);
            $excelActive->setCellValue('AT' . $row, $product['productQty']);
//
            $excelActive->mergeCells("AX" . $row . ":BB" . $row);
            $rowStr = $i."; ".$product['productName']."; ".$product['productQty']."; "."\n";
            file_put_contents('00010.xlsx',$rowStr,FILE_APPEND);
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('re-open-excelX1.xlsx');

        return 0;
    }

	public function actionResetRackAddress()
	{
		echo 'start other/reset-rack-address end' . "\n";

		//foreach (Stock::find()->andWhere(['client_id' => 2])->each(500) as $stock) {
		foreach (Stock::find()->andWhere(['client_id' => 2, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])->each(500) as $stock) {
			// foreach (Stock::find()->andWhere(['client_id' => 2, 'outbound_order_id' =>[74120]])->each(500) as $stock) {
			//if ($address = RackAddress::find()->where(['address' => $stock->secondary_address])->one()) {
				//$stock->address_sort_order = (int)$address->address_unit1 + (int)$address->address_unit2 + (int)$address->address_unit3 + (int)$address->address_unit4;
//				$sort1 = ((int)$address->address_unit1) * 1000;
//				$sort2 = ((int)$address->address_unit2) * 100;
//				$sort3 = ((int)$address->address_unit3) * 10;
//				$sort4 = ((int)$address->address_unit4) * 1;

//				$sa = explode('-', trim("3-52-10-0"));
//				$sa = explode('-', trim("3-52-01-0"));
					

				echo trim($stock->secondary_address) ." \n";
				$sa = explode('-', trim($stock->secondary_address));
				$stage = preg_replace('/[^0-9]/', '', $sa['0']); // этаж
				
				$row = "";
				if (isset($sa['1'])) {
					$row = preg_replace('/[^0-9]/', '', $sa['1']); // ряд
				}
				
				$rack = "";
				if (isset($sa['2'])) {
					$rack = preg_replace('/[^0-9]/', '',$sa['2']); // полка
				}
				
				$level = "";
				if (isset($sa['3'])) {
					$level = preg_replace('/[^0-9]/', '',$sa['3']); // уровень
				}
				//$level = preg_replace('/[^0-9]/', '',$sa['3']); // уровень
//	echo $stage  ." + ".$row ." + " .$rack ." + " .$level ." +  \n";


//				$sort1 = ((int)$stage) * 100000;
//				$sort2 = ((int)$row) * 100;
//				$sort3 = ((int)$rack) * 10;
//				$sort4 = ((int)$level) * 1;
//				echo $sort1 ." + ".$sort2 ." + " .$sort3 ." + " .$sort4 ." + \n";
//				$rr1 = $sort1.$sort2.$sort3.$sort4;
				$rr2 = $stage.$row.$rack.$level;
				echo intval($rr2) ." + \n";
//die("-end-");
				$stock->address_sort_order =  intval($rr2);
//				$stock->address_sort_order = $address->address_unit1 + $address->address_unit2 + $address->address_unit3 + $address->address_unit4;
//				$stock->address_sort_order = $sort1 + $sort2 + $sort3 + $sort4;
				// $stock->address_sort_order = $address->sort_order;
				$stock->save(false);
//				file_put_contents('actionResetRackAddress-OK-c.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "\n", FILE_APPEND);
			//} else {
//				file_put_contents('actionResetRackAddress-NO-c.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "\n", FILE_APPEND);
			//}
		}


		echo ' end other/reset-rack-address end' . "\n";
		return 0;
	}

	public function actionResetRackAddressIntermod()
	{
		echo 'start other/reset-rack-address-intermod end' . "\n";
		foreach (Stock::find()
					  //->andWhere(['client_id' => 103])
					  ->andWhere(['client_id' => 103, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])
					 ->orderBy([
						 'address_sort_order'=>SORT_DESC,
						 'primary_address'=>SORT_DESC,
					 ])
					  ->each(500) as $stock) {

			$secondaryAddress = trim($stock->secondary_address);
				if (empty($secondaryAddress)) {
					echo $secondaryAddress."-"."\n";
					continue;
				}

//				echo $stock->secondary_address."\n";
//				echo rtrim(ltrim($stock->secondary_address,"0-"),"-0");
//				die;
				$sa = explode('-', trim($stock->secondary_address));
				$stage = preg_replace('/[^0-9]/', '', $sa['0']); // этаж
				$row = preg_replace('/[^0-9]/', '', $sa['1']); // ряд
				$rack = preg_replace('/[^0-9]/', '',$sa['2']); // полка
//				$level = preg_replace('/[^0-9]/', '',$sa['3']); // уровень
				if (strlen($row) == 1) {
					$row = "0".$row;
				}

				$rr2 = $stage.$row.$rack;//.$level;
				echo intval($rr2) . " ".$rr2." ".trim($stock->secondary_address)." + \n";
				$stock->address_sort_order =  intval($rr2);
				$stock->save(false);
		}
		echo ' end other/reset-rack-address-intermod end' . "\n";
		return 0;
	}


	public function actionFixDataMatrix()
	{
		echo 'other/fix-data-matrix start' . "\n";
//		$path = 'console/input-files/00TK-000143_10_04_2024_1KhanShatyr143.csv'; // +
//		$path = 'console/input-files/00TK-000143_10_04_2024_2MegaCenter191.csv'; // +
//		$path = 'console/input-files/00TK-000143_10_04_2024_3MegaSilkway-191.csv'; // +
//		$path = 'console/input-files/00TK-000143_10_04_2024_4DostykPlaza191.csv'; // +
//		$path = 'console/input-files/00TK-000143_10_04_2024_5Keruen143.csv';
//		$paths = [
//			"1"=>'00TK-000143_10_04_2024_1KhanShatyr143.csv',
//			"2"=>'00TK-000143_10_04_2024_2MegaCenter191.csv',
//			"3"=>'00TK-000143_10_04_2024_3MegaSilkway-191.csv',
//			"4"=>'00TK-000143_10_04_2024_4DostykPlaza191.csv',
//			"5"=>'00TK-000143_10_04_2024_5Keruen143.csv',
//		];

		$paths = [
			"1"=>'00TK-000157_17_04_2024_1-Khan-Shatyr-296.csv',
			"2"=>'00TK-000157_17_04_2024_2-Mega-Center-419.csv',
			"3"=>'00TK-000157_17_04_2024_3-Mega-Silkway-419.csv',
			"4"=>'00TK-000157_17_04_2024_4-Dostyk-Plaza-419.csv',
			"5"=>'00TK-000157_17_04_2024_5-Keruen-296.csv',
		];

		foreach ($paths as $index=>$path) {
			$p = 'console/input-files/2/';
			if (($handle = fopen($p.$path, "r")) !== FALSE) {
				$row = 0;
				while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) {
					$row++;
					$dm = trim($data[0]);
					$dmPart = mb_substr($dm, 0, 27);
					//echo $dmPart."\n";

					$data_matrix_code =	InboundDataMatrix::find()
															->select("data_matrix_code")
															->andWhere(new \yii\db\Expression('data_matrix_code LIKE :term', [':term' => $dmPart . '%']))
															->scalar();
//				echo $data_matrix_code."\n";
					if ($data_matrix_code) {
						file_put_contents("00TK-ozz-2-result.csv",$data_matrix_code."\n",FILE_APPEND);
//						file_put_contents("00TK-000143_10_04_2024-result"."-fixed.csv",$data_matrix_code."-key=".$dmPart."-index=".$index."-dm=".$dm."\n",FILE_APPEND);
					} else {
						echo $data_matrix_code."-error-".$dmPart."\n";
					}
				}

				fclose($handle);
			}

		}



		echo 'other/fix-data-matrix end' . "\n";
		return 0;
	}
}