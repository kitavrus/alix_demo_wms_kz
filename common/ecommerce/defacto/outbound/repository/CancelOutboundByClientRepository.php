<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:14
 */

namespace common\ecommerce\defacto\outbound\repository;


use common\ecommerce\constants\CancelByClientOutboundStatus;
use common\ecommerce\constants\KaspiDefaultValue;
use common\ecommerce\constants\OutboundStatus;
use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use common\ecommerce\defacto\pickingList\repository\PickingListRepository;
use common\ecommerce\entities\EcommerceCancelByClientOutbound;
use common\ecommerce\entities\EcommerceCancelByClientOutboundItems;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceStock;
use common\helpers\DateHelper;
//use common\modules\employees\models\Employees;
//use common\modules\outbound\models\OutboundOrder;
//use common\modules\client\models\Client;
//use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
//use common\modules\product\models\Product;
//use common\modules\stock\models\Stock;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class CancelOutboundByClientRepository
{
    public function getClientID()
    {
        return 2;
    }

    //
    public function isOrderExist($orderNumber)
    {
        return EcommerceCancelByClientOutbound::find()->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])->exists();
    }

    public function getOrderByOrderNumber($orderNumber)
    {
        return EcommerceCancelByClientOutbound::find()->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])->one();
    }

    public function getAllOrderByCancelKey($aCancelKey)
    {
        return EcommerceCancelByClientOutbound::find()->andWhere(['client_id' => $this->getClientID(), 'cancel_key' => $aCancelKey])->all();
    }

    public function addBoxAddressToOrder($aCancelOrderId,$aNewBoxAddress)
    {
        EcommerceCancelByClientOutbound::updateAll([
            'new_box_address'=>$aNewBoxAddress
        ],['id' => $aCancelOrderId]);

        EcommerceCancelByClientOutboundItems::updateAll([
            'new_box_address'=>$aNewBoxAddress
        ],['cancel_by_client_outbound_id' => $aCancelOrderId]);

        return;
    }

    public function getOrderItems($aOrderNumber)
    {
        $order = $this->getOrderByOrderNumber($aOrderNumber);
        $result = [];
        $listItems = EcommerceCancelByClientOutboundItems::find()->andWhere(['cancel_by_client_outbound_id' => $order->id])->all();
        foreach($listItems as $item) {
            $std = new \stdClass();
            $std->cancelKey = $order->cancel_key;
            $std->orderNumber = $order->order_number;
            $std->outboundBox = $order->outbound_box;
            $std->productBarcode = $item->product_barcode;
            $std->newBoxAddress = $item->new_box_address;
            $result[] = $std;
        }

        return $result;
    }

    public function getAllOrderItems($aCancelKey)
    {
         $orderList = $this->getAllOrderByCancelKey($aCancelKey);
         $result = [];
         $countTotalOrder = 0;
         $countTotalProduct = 0;
         $dataList = new \stdClass();
         foreach($orderList as $order) {
             $countTotalOrder++;
             $listItems = EcommerceCancelByClientOutboundItems::find()->andWhere(['cancel_by_client_outbound_id' => $order->id])->all();
             foreach($listItems as $item) {
                 $std = new \stdClass();
                 $std->cancelKey = $order->cancel_key;
                 $std->orderNumber = $order->order_number;
                 $std->outboundBox = $order->outbound_box;
                 $std->productBarcode = $item->product_barcode;
                 $std->newBoxAddress = $item->new_box_address;
                 $result[] = $std;
                 $countTotalProduct++;
             }
         }

         $dataList->countTotalOrders = $countTotalOrder;
         $dataList->countTotalProducts = $countTotalProduct;

         arsort($result);
         $dataList->items = $result;

         return $dataList;
    }

    public function emptyBox($aCancelOrderId,$boxAddress)
    {
        EcommerceCancelByClientOutbound::updateAll([
            'new_box_address'=>''
        ],['id' => $aCancelOrderId,'new_box_address'=>$boxAddress]);

        EcommerceCancelByClientOutboundItems::updateAll([
            'new_box_address'=>''
        ],['cancel_by_client_outbound_id' => $aCancelOrderId,'new_box_address'=>$boxAddress]);

        return;
    }

    public function isExistOrderWithoutBoxAddress($cancelKey) {
       return EcommerceCancelByClientOutbound::find()->andWhere('new_box_address = ""')->andWhere(['cancel_key'=>$cancelKey])->exists();
    }

    public function isCountScannedOrderNotZero($cancelKey) {
       return EcommerceCancelByClientOutbound::find()->andWhere(['cancel_key'=>$cancelKey])->count() < 1;
    }

    //
    public function isDoneOrder($orderNumber)
    {
        $orderNumber = $this->getOrderByOrderNumber($orderNumber);
        if(!empty($orderNumber) && $orderNumber->status == CancelByClientOutboundStatus::DONE) {
             return true;
        }
        return false;
    }

    public function getItemsById($Id){
        return  EcommerceCancelByClientOutboundItems::find()->andWhere(['cancel_by_client_outbound_id' => $Id])->all();
    }

}