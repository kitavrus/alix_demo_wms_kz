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
use common\modules\billing\components\BillingManager;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\client\models\ClientEmployees;
use common\modules\store\models\Store;
use common\modules\store\models\StoreReviews;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use Yii;
use yii\console\Controller;
use yii\helpers\VarDumper;
//use Touki\FTP\Connection\Connection;
//use Touki\FTP\FTPWrapper;
//use yii\helpers\FileHelper;
//use frontend\modules\product\models\SyncProducts;
//use frontend\modules\product\models\ProductBarcodes;
//use frontend\modules\product\models\Product;
//use frontend\modules\inbound\models\InboundOrder;
//use frontend\modules\transportLogistics\models\TlDeliveryProposal;
//use frontend\modules\transportLogistics\components\TLHelper;
//use frontend\modules\transportLogistics\models\TlDeliveryRoutes;
//use League\Csv\Reader;

/* 1 - Находим все заказы в которых не заполнены метры кубические
   Для дифакто
   2 - Поверяем есть ли кг для Колинса
   3 - Проверяем для клиентов подсчитан ли тариф


*/



class ValidateReportController extends Controller
{
    /*
     * Recalculate all delivery proposal on dp route and car expenses
     *
     * */
    public function actionIndex()
    {
        return 0;

        echo 'index - Start' . "\n";

        if($dps = TlDeliveryProposal::find()->all()) {
            foreach($dps as $dpItem) {
                if($dpRoutes = $dpItem->getProposalRoutes()->all()) {
                    foreach($dpRoutes as $dprItem) {
                        echo 'DPR ID: '.$dprItem->id."\n";
//                      $dprItem->recalculateExpensesRoute();
                        TLManager::recalculateDpAndDpr($dprItem->tl_delivery_proposal_id,$dprItem->id);
                    }
                }
//              $dpItem->recalculateExpensesOrder();
                TLManager::recalculateDpAndDpr($dpItem->id,null);
                echo "DP ID : ".$dpItem->id."\n";
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
        echo 'copy-delivery-datetime - Start'."\n";

        if($dps = TlDeliveryProposal::find()->all()) {
            foreach($dps as $dpItem) {
                if ( !empty($dpItem->delivery_date)  && empty($dpItem->shipped_datetime) ) {
                    $dpItem->shipped_datetime = $dpItem->delivery_date;
//                    $dpItem->save(false);
                }
            }
        }

        echo 'copy-delivery-datetime - End'."\n";
        return 0;
    }


    /*
     * Находим все заказы для клиента дифакто и указываем им цену
     *
     * */
    public function actionUpdateInvoicePrice()
    {
        echo 'update-invoice-price - Start'."\n";

        $clientID = '1'; // DeFacto = 2, Colins = 1;
        if($dps = TlDeliveryProposal::findAll(['client_id'=>$clientID])) {
           $bm =  new BillingManager();
            foreach ($dps as $dp) {
//                echo "DP : ".$dp->id."\n";
//                if(doubleval($dp->kg_actual) == 0.000) {
//                if(doubleval($dp->mc_actual) == 0.000) {
//                    echo "DP  MCA : " . $dp->id . "\n";
//                    echo "DP  MCA : " . $dp->mc_actual . "\n";
//                }

                $price_invoice_with_vat = $bm->getInvoicePriceForDP($dp);
                $price_invoice = $bm->getInvoicePriceForDP($dp,false);
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

        echo 'update-invoice-price - End'."\n";
        return 0;
    }

    /*
     *
     * */
    public function actionFixDuplicate()
    {
        echo 'fix-duplicate - Start'."\n";
        echo 'fix-duplicate - RETURN'."\n";
        return 0;

        $findId = 84;
        $newId = 13;

        //S: TlDeliveryProposalBilling
        if($dps = TlDeliveryProposalBilling::findAll(['route_to'=>$findId])) {
            foreach($dps as $dp) {
                $dp->route_to = $newId;
                $dp->save(false);
                echo 'TlDeliveryProposalBilling:route_to'."\n";
            }
        }

        if($dps = TlDeliveryProposalBilling::findAll(['route_from'=>$findId]) ) {
            foreach($dps as $dp) {
                $dp->route_from = $newId;
                $dp->save(false);
                echo 'TlDeliveryProposalBilling:route_from'."\n";
            }
        }

        //S: TlDeliveryProposal
        if($dps = TlDeliveryProposal::findAll(['route_to'=>$findId])) {
            foreach($dps as $dp) {
                $dp->route_to = $newId;
                $dp->save(false);
                echo 'TlDeliveryProposal:route_to'."\n";
            }
        }

        if($dps = TlDeliveryProposal::findAll(['route_from'=>$findId])) {
            foreach($dps as $dp) {
                $dp->route_from = $newId;
                $dp->save(false);
                echo 'TlDeliveryProposal:route_from'."\n";
            }
        }

        //S: TlDeliveryRoutes
        if($dps = TlDeliveryRoutes::findAll(['route_to'=>$findId])) {
            foreach($dps as $dp) {
                $dp->route_to = $newId;
                $dp->save(false);
                echo 'TlDeliveryRoutes:route_to'."\n";
            }
        }

        if($dps = TlDeliveryRoutes::findAll(['route_from'=>$findId])) {
            foreach($dps as $dp) {
                $dp->route_from = $newId;
                $dp->save(false);
                echo 'TlDeliveryRoutes:route_from'."\n";
            }
        }

        //S: Store Reviews
        if($dps = StoreReviews::findAll(['store_id'=>$findId])) {
            foreach($dps as $dp) {
                $dp->store_id = $newId;
                $dp->save(false);
                echo 'StoreReviews:store_id'."\n";
            }
        }

        //S: Client Employees
        if($dps = ClientEmployees::findAll(['store_id'=>$findId])) {
            foreach($dps as $dp) {
                $dp->store_id = $newId;
                $dp->save(false);
                echo 'ClientEmployees:store_id'."\n";
            }
        }


        // Delete old Store id
        if($store = Store::findOne($findId)) {
            $store->deleted = 1;
            $store->save(false);
            echo 'Store:deleted:1'."\n";
        }


        echo 'fix-duplicate - End'."\n";

        return 0;
    }


    /*
     * Set status выполнен
     *
     * */
    public function actionUpdateStatus()
    {
        echo 'update-status - Start'."\n";
        echo 'update-status - RETURN'."\n";
        return 0;

        $clientID = '2'; // DeFacto;
        if($dps = TlDeliveryProposal::findAll(['client_id'=>$clientID,'status_invoice'=>TlDeliveryProposal::INVOICE_PAID])) {
            foreach ($dps as $dp) {

                $dp->status = TlDeliveryProposal::STATUS_DONE;

                $dp->save(false);
                echo  "DP SAVE : ".$dp->id."\n";

                $dp->recalculateExpensesOrder();
                $dp->setCascadedStatus();
            }
        }

        echo 'update-status - End'."\n";
        return 0;
    }


    /*
     * Set data all sub routes and cars
     * */
    public function actionSetDataToRouteAndCar()
    {
        echo 'set-data-to-route-and-car - Start'."\n";
        return 0;
//        $clientID = '2'; // DeFacto;
        if($dps = TlDeliveryProposal::find()->all()) {
//        if($dps = TlDeliveryProposal::findAll(['client_id'=>$clientID])) {
            foreach ($dps as $dp) {
                if($dpRoutes = $dp->getProposalRoutes()->all()) {
                    foreach($dpRoutes as $routeItem) {
                        $routeItem->shipped_datetime = $dp->shipped_datetime;
                        $routeItem->save(false);
                        if($cars = $routeItem->getCarItems()->all()) {
                            foreach($cars as $car) {
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

        echo 'set-data-to-route-and-car - RETURN'."\n";
        return 0;

    }


} 