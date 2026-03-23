<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\returnOrder\entities\TmpOrder;

use common\modules\client\models\Client;
use common\components\BarcodeManager;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItemProduct;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\returnOrder\models\ReturnTmpOrders;
use common\modules\stock\models\RackAddress;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use stockDepartment\modules\returnOrder\entities\TmpOrder\Status;
use stockDepartment\modules\returnOrder\entities\TmpOrder\Ttn;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use yii\base\Model;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class ReturnTmpOrder  {

    const PARTY_NUMBER = 'D10AA00005505';
    public $api;

    /**
     * ReturnTmpOrder constructor.
     */
    public function __construct()
    {
        $this->api = new DeFactoSoapAPIV2Manager();
    }


    public static function getQtyByTTN($ttn)
    {
        return Ttn::getQtyPlacesById($ttn);
    }

    public function makeBox($ttn,$ourBox,$clientBox)
    {
        // проверяем существут ли такой короб в незакрытых накладных
        // создаем строку (короб) в ReturnTmpOrder заполняя данными из ReturnOrderItems
        $rOrderOne = null;
        $rOrderItemRow = ReturnOrderItems::find()->andWhere(['client_box_barcode'=>$clientBox,'status'=>ReturnOrder::STATUS_NEW])->one();
        if($rOrderItemRow) {
            $rOrderOne = ReturnOrder::find()->andWhere(['id' => $rOrderItemRow->return_order_id])->one();
        }

        $rTmpOrderOne = new ReturnTmpOrders();

        $rTmpOrderOne->client_id = Client::CLIENT_DEFACTO;
        if($rOrderItemRow) {
            $rTmpOrderOne->from_point_id = $rOrderItemRow->from_point_id;
            $rTmpOrderOne->from_point_client_id = $rOrderItemRow->from_point_client_id;
            $rTmpOrderOne->to_point_id = $rOrderItemRow->to_point_id;
            $rTmpOrderOne->to_point_client_id = $rOrderItemRow->to_point_client_id;
        }
        if($rOrderOne) {
            $rTmpOrderOne->order_number = $rOrderOne->order_number;
        }

        $rTmpOrderOne->status = Status::SCANNED;

        $rTmpOrderOne->expected_qty = 1;
        $rTmpOrderOne->accepted_qty = 1;

        $rTmpOrderOne->ttn = $ttn;

        $rTmpOrderOne->party_number = self::PARTY_NUMBER;
        $rTmpOrderOne->our_box_inbound_barcode = "";
        $rTmpOrderOne->our_box_to_stock_barcode = $ourBox;
        $rTmpOrderOne->client_box_barcode = $clientBox;
        $rTmpOrderOne->primary_address = $ourBox;
        $rTmpOrderOne->save(false);
    }

    public function create() // TODO
    {

    }

    public static function getQtyScanned($ttn)
    {
        return ReturnTmpOrders::find()->andWhere(['ttn'=>$ttn,'status'=>Status::SCANNED])->count();
    }

    public static function getUnacceptedList($ttn)
    {
        return ReturnTmpOrders::find()->andWhere(['ttn'=>$ttn,'status'=>Status::SCANNED])->andWhere("secondary_address = ''")->all();
    }

    public static function getAcceptedList($ttn)
    {
        return ReturnTmpOrders::find()->andWhere(['ttn'=>$ttn,'status'=>Status::SCANNED])->andWhere("secondary_address != ''")->all();
    }

    public static function boxMoveTo($from,$to) {
        ReturnTmpOrders::updateAll(['secondary_address'=>$to,'status'=>Status::SCANNED],'primary_address = :pa',[':pa'=>$from]);
    }

    public static function boxIsPreparedForMoveTo($from) {
       return ReturnTmpOrders::find()->andWhere(['primary_address'=>$from,'status'=>Status::SCANNED])->exists();
    }

    public static function isDefactoBox($box)
    {
        return BarcodeManager::isDefactoBox($box);
    }

    public static function getScannedBoxes()
    {
       return ReturnTmpOrders::find()
           ->andWhere(['status'=>Status::SCANNED])
           ->andWhere("secondary_address != '' AND primary_address != '' ")
           ->limit(250)
           ->all();
    }

    private static function setStatusScannedReturnOrderItem($clientBoxBarcode,$boxBarcode)
    {
        $returnItemID = 0;
        $returnItem = ReturnOrderItems::find()->andWhere([
            'client_box_barcode' => $clientBoxBarcode,
            'status' => [
                ReturnOrder::STATUS_NEW
            ],
        ])->one();

        if ($returnItem) {
            $returnItem->status = ReturnOrder::STATUS_SCANNED;
            $returnItem->box_barcode = $boxBarcode;
            $returnItem->save(false);

            $returnItemID = $returnItem->id;
        }
        return $returnItemID;
    }

    /*
*
* */
    private static function createOrUpdateInboundOrder($clientBoxBarcode)
    {
        $inboundID = 0;
        $inbound = InboundOrder::find()->andWhere([
            'parent_order_number'=>self::PARTY_NUMBER,
            'order_number'=>$clientBoxBarcode,
            'client_id'=>Client::CLIENT_DEFACTO,
        ])->one();

        if(!$inbound) {
            $inbound = new InboundOrder();
            $inbound->parent_order_number =  self::PARTY_NUMBER;
            $inbound->order_number = $clientBoxBarcode;
            $inbound->status = Stock::STATUS_INBOUND_SCANNED;
            $inbound->order_type = InboundOrder::ORDER_TYPE_RETURN;
            $inbound->client_id = Client::CLIENT_DEFACTO;
            $inbound->expected_qty = 0;
            $inbound->accepted_qty = 0;
            $inbound->save(false);
            $inboundID = $inbound->id;
        } else {
            $inboundID = $inbound->id;
        }

        return $inboundID;
    }

    /*
     * */
    private static function createInboundOrderItemAndStock($inboundID,
                                                   $boxBarcode,
                                                   $secondaryAddress,
                                                   $productBarcode,
                                                   $productSerializeData,
                                                   $clientBoxBarcode,
                                                   $fromPointId,
                                                   $fromPointClientId,
                                                   $toPointId,
                                                   $toPointClientId
    )
    {


        $inboundOrderItem = new InboundOrderItem();
        $inboundOrderItem->inbound_order_id = $inboundID;
        $inboundOrderItem->product_barcode = $productBarcode;
        $inboundOrderItem->product_serialize_data = $productSerializeData;
        $inboundOrderItem->box_barcode = $clientBoxBarcode;
        $inboundOrderItem->expected_qty = 1;
        $inboundOrderItem->accepted_qty = 1;
        $inboundOrderItem->status = Stock::STATUS_INBOUND_SCANNED;
        $inboundOrderItem->save(false);

        $address_sort_order = 0;
        if($address = RackAddress::find()->where(['address'=>$secondaryAddress])->one()) {
            $address_sort_order = $address->sort_order;
        }

        $stock = new Stock();
        $stock->client_id =  Client::CLIENT_DEFACTO;;
        $stock->inbound_order_id = $inboundOrderItem->inbound_order_id;
        $stock->inbound_order_item_id = $inboundOrderItem->id;
        $stock->product_barcode = $inboundOrderItem->product_barcode;
        $stock->product_model = '';
        $stock->status = Stock::STATUS_INBOUND_SCANNED;
        $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
        $stock->inbound_client_box = $inboundOrderItem->box_barcode;
        $stock->primary_address = $boxBarcode;
        $stock->secondary_address = $secondaryAddress;
        $stock->address_sort_order = $address_sort_order;
        $stock->save(false);

        $inboundOrder = InboundOrder::findOne($inboundID);
        $inboundOrder->expected_qty = 1;
        $inboundOrder->accepted_qty = 1;
        $inboundOrder->from_point_id = $fromPointId;
        $inboundOrder->from_point_title = $fromPointClientId;
        $inboundOrder->to_point_id = $toPointId;
        $inboundOrder->to_point_title = $toPointClientId;
        $inboundOrder->save(false);
    }

    private static function setStatusAfterSentByAPI($inboundID)
    {
        $inbound = InboundOrder::findOne($inboundID);

        if($inbound) {
            $inbound->status = Stock::STATUS_INBOUND_CONFIRM;
            $inbound->date_confirm = time();
            $inbound->begin_datetime = time();
            $inbound->save(false);

            InboundOrderItem::updateAll(['status'=>Stock::STATUS_INBOUND_CONFIRM],['inbound_order_id'=> $inbound->id]);

            Stock::updateAll([
                'status'=>Stock::STATUS_INBOUND_CONFIRM,
                'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
            ],[
                'inbound_order_id'=>$inbound->id,
                'status'=>[
                    Stock::STATUS_INBOUND_SCANNED,
                    Stock::STATUS_INBOUND_OVER_SCANNED,
                ]
            ]);
        }
    }

    public function sendDataToAPI($returnOrderItemProducts)
    {
        if( YII_ENV == 'prod' || 1) {
            $toSendDataForAPI = [];
            $returnOrderItemProductPrepared[] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataReturn($returnOrderItemProducts);
            $toSendDataForAPI['InBoundFeedBackThreePLResponse'] = $returnOrderItemProductPrepared;
            file_put_contents("SendInBoundFeedBackDataReturn-sendDataToAPI-live.log",date("Ymd")."\n".print_r($toSendDataForAPI,true)."\n",FILE_APPEND);
            file_put_contents("SendInBoundFeedBackDataReturn-sendDataToAPI-serialize-live.log",serialize($toSendDataForAPI)."\n",FILE_APPEND);
//            $api = new DeFactoSoapAPIV2Manager();
            $responseAPI = $this->api->SendInBoundFeedBackDataReturn($toSendDataForAPI);
//            $responseAPI = [];
//            $responseAPI['HasError'] = false;
            if (!$responseAPI['HasError']) {
                return true;
            }
        }
        return false;
    }

    //TODO сделать нормально
    public function makeInboundAndStockForAPI()
    {
        if ($returnTmpOrderList = self::getScannedBoxes()) {

//            VarDumper::dump($returnTmpOrderList,10,true);
//            die;
            foreach ($returnTmpOrderList as $returnTmpOrder) {

                $returnOrderItems = ReturnOrderItems::find()
                    ->andWhere(['client_box_barcode' => $returnTmpOrder->client_box_barcode, 'status' => ReturnOrder::STATUS_NEW])
                    ->all();
//                VarDumper::dump($returnOrderItems,10,true);
//                die;

                if (empty($returnOrderItems)) {
                    continue;
                }

                $clientBoxBarcode = $returnTmpOrder->client_box_barcode;
                $boxBarcode = $returnTmpOrder->our_box_to_stock_barcode;
                $secondaryAddress = $returnTmpOrder->secondary_address;

                foreach ($returnOrderItems as $returnOrderItem) {

                    $productBarcode = $returnOrderItem->product_barcode;
                    $productSerializeData = $returnOrderItem->product_serialize_data;
                    $fromPointId = $returnOrderItem->from_point_id;
                    $fromPointClientId = $returnOrderItem->from_point_client_id;
                    $toPointId = $returnOrderItem->to_point_id;
                    $toPointClientId = $returnOrderItem->to_point_client_id;

                    $returnTmpOrder->from_point_id = $returnOrderItem->from_point_id;;
                    $returnTmpOrder->from_point_client_id = $returnOrderItem->from_point_client_id;
                    $returnTmpOrder->to_point_id = $returnOrderItem->to_point_id;
                    $returnTmpOrder->to_point_client_id = $returnOrderItem->to_point_client_id;
                    $returnTmpOrder->status = Status::SEND_TO_API;
                    $returnTmpOrder->save(false);

//                        VarDumper::dump($returnOrderItem,10,true);
//                        die;

                    self::setStatusScannedReturnOrderItem($clientBoxBarcode, $boxBarcode);
                    ReturnOrderItemProduct::updateAll(['status' => ReturnOrder::STATUS_SCANNED], ['return_order_item_id' => $returnOrderItem->id]);

//                        VarDumper::dump($clientBoxBarcode,10,true);
//                        VarDumper::dump($boxBarcode,10,true);
//                        die;

                    $inboundID = self::createOrUpdateInboundOrder($clientBoxBarcode);

//                        VarDumper::dump($clientBoxBarcode,10,true);
//                        VarDumper::dump($inboundID,10,true);
//                        die;

                    self::createInboundOrderItemAndStock($inboundID,
                        $boxBarcode,
                        $secondaryAddress,
                        $productBarcode,
                        $productSerializeData,
                        $clientBoxBarcode,
                        $fromPointId,
                        $fromPointClientId,
                        $toPointId,
                        $toPointClientId
                    );

//                        VarDumper::dump($clientBoxBarcode,10,true);
//                        VarDumper::dump($inboundID,10,true);
//                        die;

                    // Send data to APT
                    $returnOrderItemProducts = ReturnOrderItemProduct::find()
                        ->select('return_order_item_id, product_barcode, product_serialize_data, field_extra1, client_box_barcode, expected_qty')
                        ->andWhere(['return_order_item_id' => $returnOrderItem->id, 'status' => ReturnOrder::STATUS_SCANNED])
//                                                ->andWhere(['client_box_barcode' => $returnTmpOrder->client_box_barcode,'status'=>ReturnOrder::STATUS_NEW])
                        ->one();


//                        VarDumper::dump($returnOrderItemProducts,10,true);
//                        die;
                    if (!$this->sendDataToAPI($returnOrderItemProducts)) {
                        file_put_contents("makeInboundAndStockForAPI-ERROR.log", date("Ymd") . "\n" . print_r($returnOrderItemProducts, true) . "\n", FILE_APPEND);
                    }
                    self::setStatusAfterSentByAPI($inboundID);

                    ReturnOrderItemProduct::updateAll(['status' => Stock::STATUS_INBOUND_CONFIRM,'accepted_qty'=>1], ['return_order_item_id' => $returnOrderItem->id]);

                    $returnOrderItem->accepted_qty = 1;
                    $returnOrderItem->status = ReturnOrder::STATUS_COMPLETE;
                    $returnOrderItem->save(false);

                    $returnTmpOrder->status = Status::COMPLETE;
                    $returnTmpOrder->save(false);

                    //
                    if ($rOrder = ReturnOrder::findOne($returnOrderItem->return_order_id)) {
                        $rOrder->accepted_qty = ReturnOrderItems::find()->andWhere(['return_order_id'=>$rOrder->id,'status'=>ReturnOrder::STATUS_COMPLETE])->count();
                        $rOrder->save(false);
                    }
                }
            }
        }
    }

    public static function countWithoutSecondaryAddress()
    {
        return ReturnTmpOrders::find()
            ->andWhere(['status'=>Status::SCANNED])
            ->andWhere("secondary_address = ''")
            ->count();
    }

    public static function countWithSecondaryAddress()
    {
        return ReturnTmpOrders::find()
            ->andWhere(['status'=>Status::SCANNED])
            ->andWhere("secondary_address != ''")
            ->count();
    }

    public static function countSendByAPI()
    {
        return ReturnTmpOrders::find()
            ->andWhere(['status'=>Status::COMPLETE])
            ->count();
    }
}