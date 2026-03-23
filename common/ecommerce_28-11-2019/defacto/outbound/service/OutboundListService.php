<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */
namespace common\ecommerce\defacto\outbound\service;

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

    public function scanPackageBarcode($dto) {

        $orderInfo = $this->outboundService->getOrderInfo($this->stockService->getOrderIdByPackageBarcode($dto->barcode));

        $list = new EcommerceOutboundList();
        $list->our_outbound_id = $orderInfo->order->id;
        $list->client_order_number = $orderInfo->order->order_number;
        $list->ttn_delivery_company = $orderInfo->order->client_ReferenceNumber;
        $list->list_title = $dto->title;
        $list->package_barcode = $dto->barcode;
        $list->save(false);
        return $this->countScannedInList($dto->title);
    }



    public function printList($dto) {

        EcommerceOutboundList::updateAll([
            'status'=>OutboundListStatus::PRINTED
        ], [
            'list_title'=>trim($dto->title)
        ]);

        $outboundList = $this->getDataForPrintList($dto->title);
        foreach($outboundList as $listRow) {
            $this->outboundService->acceptedOrder($listRow->our_outbound_id);
        }

        return $this->getDataForPrintList($dto->title);
    }

    public function getDataForPrintList($title) {
        return EcommerceOutboundList::find()->andWhere(['list_title'=>$title])->orderBy(['id'=>SORT_DESC])->all();
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

        return $result;
    }

    public function packedOrderButNotScannedToListOrder() {
        return EcommerceOutbound::find()->select('id,status, order_number, packing_date, client_ReferenceNumber')
            ->andWhere([
                'client_id'=>$this->outboundService->getClientID(),
                'status'=>OutboundStatus::PRINT_BOX_LABEL
            ])
            ->andWhere(['NOT IN','id',EcommerceOutboundList::find()->select('our_outbound_id')])
            ->asArray()
            ->all();
    }

    public function countScannedInList($title)
    {
        return EcommerceOutboundList::find()
            ->andWhere([
                'list_title'=>$title,
            ])
            ->count();
    }

    public function isExistPackageBarcode($title,$placeBarcode)
    {
        return EcommerceOutboundList::find()
            ->andWhere([
                'list_title'=>$title,
                'package_barcode'=>$placeBarcode,
            ])
            ->exists();
    }

    public function isListNotPrinted($title)
    {
        return EcommerceOutboundList::find()
            ->andWhere([
                'list_title'=>$title,
                'status'=>OutboundListStatus::PRINTED,
            ])
            ->exists();
    }

    public function isPackageBarcodeExistInOtherList($title,$placeBarcode)
    {
        return EcommerceOutboundList::find()
            ->andWhere(['package_barcode'=>$placeBarcode])
            ->andWhere('list_title != :title',[':title'=>$title])
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


    public function getListDataForPrinting($title)
    {
        return EcommerceOutboundList::find()
            ->andWhere(['list_title'=>$title])
            ->all();
    }

    public function showOrdersInList($dto) {
       return $this->getDataForPrintList($dto->title);
    }

    //1+ проверяем что текущий лист отгрузки не закрыт
    //2+ проверяем что отскинировали шк отсканированного и упакованного заказа
    //3 проверяем что этого заказа нет в других листах отгрузки
    //4 при печати ставим статус ок
}
