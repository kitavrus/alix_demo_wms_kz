<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */
namespace common\ecommerce\defacto\outbound\service;

use common\ecommerce\constants\CourierCompany;
use common\ecommerce\constants\KaspiDefaultValue;
use common\ecommerce\constants\OutboundListStatus;
use common\ecommerce\constants\OutboundStatus;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceOutboundList;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\entities\EcommerceStock;
use yii\helpers\ArrayHelper;

class OutboundListService
{
    private $outboundService;
    private $stockService;

    public function __construct()
    {
        $this->outboundService = new OutboundService();
        $this->stockService = new Service();
    }

    public function selectCourierCompany($dto) {

        return $this->countScannedInList($dto->title,$dto->courierCompany);
    }

    public function scanPackageBarcode($dto) {

        $orderInfo = $this->outboundService->getOrderInfo($this->stockService->getOrderIdByPackageBarcode($dto->barcode));

        $list = new EcommerceOutboundList();
        $list->our_outbound_id = $orderInfo->order->id;
        $list->client_order_number = $orderInfo->order->order_number;
        $list->ttn_delivery_company = $orderInfo->order->client_ReferenceNumber;
        $list->list_title = $dto->title;
        $list->package_barcode = $dto->barcode;
        $list->courier_company = $dto->courierCompany;
        $list->cargo_company_ttn = $dto->orderNumber;
        $list->save(false);
        return $this->countScannedInList($dto->title,$dto->courierCompany);
    }


    public function printList($dto) {

        EcommerceOutboundList::updateAll([
            'status'=>OutboundListStatus::PRINTED
        ], [
            'list_title'=>trim($dto->title),
            'courier_company'=>$dto->courierCompany
        ]);

        $outboundList = $this->getDataForPrintList($dto->title,$dto->courierCompany);
        foreach($outboundList as $listRow) {
            $this->outboundService->acceptedOrder($listRow->our_outbound_id);
        }

        return $this->getDataForPrintList($dto->title,$dto->courierCompany);
    }

    public function getDataForPrintList($title,$courierCompany) {
        return EcommerceOutboundList::find()->andWhere([
            'list_title'=>$title,
            'courier_company'=>$courierCompany
        ])->orderBy(['id'=>SORT_DESC])->all();
    }

    public function allPackedOrderButNotScannedToList()
    {
        $orderList = $this->packedOrderButNotScannedToListOrder();

        $orderIdList = ArrayHelper::getColumn($orderList,'id');
        $outboundBoxList =  ArrayHelper::map(
                                EcommerceStock::find()->select('outbound_id, outbound_box')->andWhere(['outbound_id'=>$orderIdList])->asArray()->all()
                                ,'outbound_id'
                                ,'outbound_box'
                            );

        $result  = new \stdClass();
        $result->orderList = $orderList;
        $result->outboundBoxList = $outboundBoxList;
        $result->orderByCourier = $this->packedOrderButNotScannedToListOrderByCourier();

        return $result;
    }

    public function packedOrderButNotScannedToListOrder() {
        return EcommerceOutbound::find()->select('id, status, order_number, packing_date, client_ReferenceNumber, client_CargoCompany')
            ->andWhere([
                'client_id'=>$this->outboundService->getClientID(),
                'status'=>OutboundStatus::PRINT_BOX_LABEL
            ])
            ->andWhere(['NOT IN','id',EcommerceOutboundList::find()->select('our_outbound_id')])
            ->asArray()
            ->all();
    }

    public function packedOrderButNotScannedToListOrderByCourier() {
        return EcommerceOutbound::find()->select('client_CargoCompany, count(order_number) as orderQty ')
            ->andWhere([
                'client_id'=>$this->outboundService->getClientID(),
                'status'=>OutboundStatus::PRINT_BOX_LABEL
            ])
            ->andWhere(['NOT IN','id',EcommerceOutboundList::find()->select('our_outbound_id')])
            ->groupBy('client_CargoCompany')
            ->asArray()
            ->all();
    }

    public function allOrdersInAllOutboundList($title) {
        return EcommerceOutboundList::find()->select('list_title,courier_company, count(courier_company) as orderQty')->andWhere([
            'list_title'=>$title,
        ])
            ->groupBy('courier_company')
            ->orderBy(['courier_company'=>SORT_DESC])
            ->asArray()
            ->all();
    }

    public function showAllOrdersInAllOutboundList($title)
    {
        $orderList = $this->allOrdersInAllOutboundList($title);

//        $orderIdList = ArrayHelper::getColumn($orderList,'id');
//        $outboundBoxList =  ArrayHelper::map(
//            EcommerceStock::find()->select('outbound_id, outbound_box')->andWhere(['outbound_id'=>$orderIdList])->asArray()->all()
//            ,'outbound_id'
//            ,'outbound_box'
//        );

        $result  = new \stdClass();
        $result->orderList = $orderList;
//        $result->outboundBoxList = $outboundBoxList;
//        $result->kaspiOrders = $orderList;
//        $result->orderByCourier = $this->packedOrderButNotScannedToListOrderByCourier();

        return $result;
    }

    public function deleteList($title,$courierCompany)
    {
       $isExist = EcommerceOutboundList::find()->andWhere(['list_title'=>$title,'courier_company'=>$courierCompany,'status'=>OutboundListStatus::PRINTED])->exists();

        if($isExist) {
            return false;
        }

        EcommerceOutboundList::deleteAll([
            'list_title'=>$title,
            'courier_company'=>$courierCompany,
        ]);

        return true;
    }

//    public function showKaspiOrders()
//    {
//        $orderList = $this->findKaspiOrders();
//
//        $orderIdList = ArrayHelper::getColumn($orderList,'id');
//        $outboundBoxList =  ArrayHelper::map(
//            EcommerceStock::find()->select('outbound_id, outbound_box')->andWhere(['outbound_id'=>$orderIdList])->asArray()->all()
//            ,'outbound_id'
//            ,'outbound_box'
//        );
//
//        $result  = new \stdClass();
//        $result->orderList = $orderList;
//        $result->outboundBoxList = $outboundBoxList;
////        $result->kaspiOrders = $orderList;
////        $result->orderByCourier = $this->packedOrderButNotScannedToListOrderByCourier();
//
//        return $result;
//    }
//
//    public function findKaspiOrders() {
//        return EcommerceOutbound::find()->select('id, status, order_number, packing_date, client_ReferenceNumber, client_CargoCompany')
//            ->andWhere([
//                'client_id'=>$this->outboundService->getClientID(),
//                'status'=>OutboundStatus::PRINT_BOX_LABEL,
//                'client_CargoCompany'=>KaspiDefaultValue::CLIENT_CARGO_COMPANY
//            ])
////            ->andWhere(['NOT IN','id',EcommerceOutboundList::find()->select('our_outbound_id')])
//            ->asArray()
//            ->all();
//    }

    public function countScannedInList($title,$courierCompany)
    {
        return EcommerceOutboundList::find()
            ->andWhere([
                'list_title'=>$title,
                'courier_company'=>$courierCompany,
            ])
            ->count();
    }

    public function isExistPackageBarcode($title,$placeBarcode,$courierCompany)
    {
        return EcommerceOutboundList::find()
            ->andWhere([
                'list_title'=>$title,
                'package_barcode'=>$placeBarcode,
//                'courier_company'=>$courierCompany,
            ])
            ->exists();
    }

    public function isListNotPrinted($title,$courierCompany)
    {
        return EcommerceOutboundList::find()
            ->andWhere([
                'list_title'=>$title,
                'courier_company'=>$courierCompany,
                'status'=>OutboundListStatus::PRINTED,
            ])
            ->exists();
    }

    public function isPackageBarcodeExistInOtherList($title,$placeBarcode,$courierCompany)
    {
        return EcommerceOutboundList::find()
            ->andWhere(['package_barcode'=>$placeBarcode])
            ->andWhere('list_title != :listTitle',[':listTitle'=>$title])
            ->andWhere('courier_company != :courierCompany',[':courierCompany'=>$courierCompany])
            ->exists();
    }

    public function isOrderPackaged($placeBarcode)
    {
        $orderId = $this->stockService->getOrderIdByPackageBarcode($placeBarcode);
        if(empty($orderId)) {
            return false;
        }

        return $this->outboundService->isPacked($orderId);
    }


    public function getListDataForPrinting($title,$courierCompany)
    {
        return EcommerceOutboundList::find()
            ->andWhere([
                'list_title'=>$title,
                'courier_company'=>$courierCompany,
            ])
            ->all();
    }

    public function showOrdersInList($dto) {
       return $this->getDataForPrintList($dto->title,$dto->courierCompany);
    }


    public function isOrderFromOtherCourierCompany($dto) {

       $orderInfo = $this->outboundService->getOrderInfo($this->stockService->getOrderIdByPackageBarcode($dto->barcode));

        $result = false;

        switch($dto->courierCompany) {
            case CourierCompany::LAMODA :
                $result = $orderInfo && $orderInfo->order->client_CargoCompany == $dto->courierCompany;
            break;
            case CourierCompany::PONY_EXPRESS :
                $result = $orderInfo && $orderInfo->order->client_CargoCompany == $dto->courierCompany;
            break;
            case CourierCompany::PONY_EXPRESS_KASPI :
            case CourierCompany::DPD :
            case CourierCompany::DHL :
            case CourierCompany::GALLOP :
            case CourierCompany::EXLINE :
            case CourierCompany::PARTNER :
            case CourierCompany::KAZPOST :
                $result = $orderInfo && $orderInfo->order->client_CargoCompany == KaspiDefaultValue::CLIENT_CARGO_COMPANY;
            break;
        }

       return $result;
    }
}
