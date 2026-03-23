<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 14.04.2020
 * Time: 12:28
 */

namespace common\ecommerce\defacto\transfer\repository;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockTransferStatus;
use common\ecommerce\constants\TransferDefaultValue;
use common\ecommerce\constants\TransferStatus;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\entities\EcommerceTransfer;
use common\ecommerce\entities\EcommerceTransferB2cToB2b;
use common\ecommerce\entities\EcommerceTransferItems;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ZZZZTransferRepository
{
    private $barcodeService;

    /**
     * TransferRepository constructor.
     */
    public function __construct()
    {
        $this->barcodeService = new \common\ecommerce\defacto\barcodeManager\service\BarcodeService();
    }


    public function getOrdersForPrintPickList()
    {
        $query = EcommerceTransfer::find()->andWhere([
            'status'=>TransferStatus::getOrdersForPrintPickList(),
            'client_LcBarcode'=>TransferDefaultValue::MAIN_VIRTUAL_BOX,
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
//            'sort' => ['defaultOrder' => ['created_at' => SORT_ASC]]
        ]);
    }

    public function getOrderInfo($aTransferId)
    {
        $order = EcommerceTransfer::find()->andWhere(["id" => $aTransferId])->one();
        $items = EcommerceTransferItems::find()->andWhere(['transfer_id' => $aTransferId])->all();

        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;

        $boxItems = EcommerceTransfer::find()->select('client_LcBarcode')
                    ->andWhere(["client_BatchId" => $order->client_BatchId])
                    ->andWhere(['!=','client_LcBarcode',TransferDefaultValue::MAIN_VIRTUAL_BOX])
                    ->column();
        $result->boxItems = $boxItems;

        return $result;
    }

    public function canPrintPickingList($aTransferId) {
        return EcommerceTransfer::find()->andWhere([
            'id' =>$aTransferId,
            'status' => TransferStatus::getOrdersForPrintPickList(),
            'client_LcBarcode'=>TransferDefaultValue::MAIN_VIRTUAL_BOX,
//            'client_id' => $this->getClientID()
        ])->exists();
    }

    public function isReadyPrintPickListForScanning($aPikingListBarcode) {

       $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return  EcommerceTransfer::find()->andWhere([
            'id' =>$parsePikingList->transferId,
            'client_BatchId' =>$parsePikingList->batchId,
            'status' => TransferStatus::getReadyPrintPickListForScanning(),
            'client_LcBarcode'=>TransferDefaultValue::MAIN_VIRTUAL_BOX,
        ])->exists();
    }

    public function getPrintPickListInfo($aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return EcommerceTransfer::find()->andWhere([
            'id' =>$parsePikingList->transferId,
            'client_BatchId' =>$parsePikingList->batchId,
            'status' => TransferStatus::getReadyPrintPickListForScanning(),
            'client_LcBarcode'=>TransferDefaultValue::MAIN_VIRTUAL_BOX,
        ])->one();
    }

    public function showOrderItems($aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $items = EcommerceTransferItems::find()->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
        ])
        ->orderBy('accepted_qty')
        ->asArray()
        ->all();

        return $items;
    }

    public function showScannedItems($aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $items = EcommerceStock::find()->select('box_address_barcode, transfer_outbound_box, product_barcode, COUNT(product_barcode) as productQty')->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
        ])
        ->groupBy('box_address_barcode, transfer_outbound_box, product_barcode')
        ->orderBy('transfer_outbound_box, box_address_barcode')
        ->asArray()
        ->all();

        return $items;
    }

    public function showBoxItems($aOurBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $items = EcommerceStock::find()->select('product_barcode, box_address_barcode as anyBox, count(product_barcode) as qtyProduct')->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'box_address_barcode' =>  $aOurBarcode,
        ])
        ->groupBy('box_address_barcode, product_barcode')
        ->asArray()
        ->all();

        return $items;
    }

    public function showLcBoxItems($aLcBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $items = EcommerceStock::find()->select('product_barcode, transfer_outbound_box as anyBox, count(product_barcode) as qtyProduct')->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'transfer_outbound_box' =>  $aLcBarcode,
        ])
        ->groupBy('transfer_outbound_box, product_barcode')
        ->asArray()
        ->all();

        return $items;
    }

    public function getQtyProductsInBox($aLcBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return  EcommerceStock::find()->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'transfer_outbound_box' =>  $aLcBarcode,
        ])->count();
    }

    public function getQtyProductsInOurBox($aOurBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $expectedProductQtyInBox =  EcommerceStock::find()->select('product_barcode')->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'box_address_barcode' =>  $aOurBarcode,
        ])->count();

        $scannedProductQtyInBox =  EcommerceStock::find()->select('product_barcode')->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'box_address_barcode' =>  $aOurBarcode,
            'status_transfer' =>  StockTransferStatus::SCANNED,
        ])->count();

        $result = new \stdClass();
        $result->expectedProductQtyInBox = $expectedProductQtyInBox;
        $result->scannedProductQtyInBox = $scannedProductQtyInBox;

        return $result;
    }

    public function getProductInfoInOurBox($aProductBarcode,$aOurBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $skuId = $this->barcodeService->getSkuIdByProductBarcodeInItem($aProductBarcode);

       $expectedProductQtyInBox =  EcommerceStock::find()->select('product_barcode')->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'client_product_sku' =>  $skuId,
//            'product_barcode' =>  $aProductBarcode,
            'box_address_barcode' =>  $aOurBarcode,
        ])->count();

        $scannedProductQtyInBox =  EcommerceStock::find()->select('product_barcode')->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'client_product_sku' =>  $skuId,
//            'product_barcode' =>  $aProductBarcode,
            'box_address_barcode' =>  $aOurBarcode,
            'status_transfer' =>  StockTransferStatus::SCANNED,
        ])->count();

        $result = new \stdClass();
        $result->expectedProductQtyInBox = $expectedProductQtyInBox;
        $result->scannedProductQtyInBox = $scannedProductQtyInBox;

        return $result;
    }

    public function getProductListInOurBox($aOurBoxBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return EcommerceStock::find()->select('product_barcode')
                ->andWhere([
                    'box_address_barcode' => $aOurBoxBarcode,
                    'transfer_id' => $parsePikingList->transferId,
                    'client_id' => $this->getClientID(),
                ])
                ->asArray()
                ->column();
    }


    public function makeScannedProduct($dto)
    {
        $parsePikingList = self::parsePikingListBarcode($dto->pickingListBarcode);

        $this->makeScannedStock($dto);
        $outboundOrderItem = $this->makeScannedItem($dto);
        $this->makeScannedOrder($parsePikingList->transferId);

        return $outboundOrderItem;
    }

    private function makeScannedStock($dto)
    {
        $parsePikingList = self::parsePikingListBarcode($dto->pickingListBarcode);

        $skuId = $this->barcodeService->getSkuIdByProductBarcodeInItem($dto->productBarcode);

        $stock = EcommerceStock::find()->andWhere([
            'box_address_barcode' => $dto->ourBoxBarcode,// TODO Закоментировал для обработки старой версии
            'client_product_sku' =>  $skuId,
//            'product_barcode' => $dto->productBarcode,
            'transfer_id' => $parsePikingList->transferId,
            'status_transfer' => StockTransferStatus::getReadyForScanning(),
            'client_id' => $this->getClientID(),
        ])
        ->one();

        if ($stock) {
            $stock->status_transfer = StockTransferStatus::SCANNED;
            $stock->transfer_outbound_box = $dto->lcBarcode;
//            $stock->scan_out_employee_id = $dto->employee->id;
            $stock->scan_out_datetime = time();
            $stock->save(false);
        }
    }

    private function makeScannedItem($dto)
    {
        $parsePikingList = self::parsePikingListBarcode($dto->pickingListBarcode);

        $skuId = $this->barcodeService->getSkuIdByProductBarcodeInItem($dto->productBarcode);

        $outboundOrderItem = EcommerceTransferItems::find()->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'client_SkuId' =>$skuId,
//            'product_barcode' => $dto->productBarcode,
        ])->one();

        if ($outboundOrderItem) {

            if (intval($outboundOrderItem->accepted_qty) < 1) {
                $outboundOrderItem->begin_datetime = time();
                $outboundOrderItem->status = TransferStatus::SCANNING;
            }

//            $outboundOrderItem->accepted_qty = $this->getQtyScannedProduct($dto->productBarcode,$parsePikingList->transferId);
            $outboundOrderItem->accepted_qty = $this->getQtyScannedProductItem($dto->productBarcode,$parsePikingList->transferId,$outboundOrderItem->id);

            if ($outboundOrderItem->accepted_qty == $outboundOrderItem->expected_qty || $outboundOrderItem->accepted_qty == $outboundOrderItem->allocated_qty ) {
                $outboundOrderItem->status = TransferStatus::SCANNED;
            }

            $outboundOrderItem->end_datetime = time();
            $outboundOrderItem->save(false);
        }

        return $outboundOrderItem;
    }

    private function makeScannedOrder($orderId)
    {
        $outboundOrder = EcommerceTransfer::find()
            ->andWhere([
                'id'=>$orderId,
                'client_id' => $this->getClientID()
            ])->one();

        if(intval($outboundOrder->accepted_qty) < 1) {
            $outboundOrder->begin_datetime = time();
            $outboundOrder->status = TransferStatus::SCANNING;
        }

        $outboundOrder->accepted_qty = $this->getQtyScanned($orderId);

        if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty || $outboundOrder->accepted_qty == $outboundOrder->allocated_qty ) {
            $outboundOrder->status = TransferStatus::SCANNED;
        }

        $outboundOrder->end_datetime = time();
        $outboundOrder->save(false);
    }

    private function getQtyScannedProduct($productBarcode,$orderId) {

        $skuId = $this->barcodeService->getSkuIdByProductBarcodeInItem($productBarcode);

        return EcommerceStock::find()->andWhere([
            'client_product_sku' =>  $skuId,
//            'product_barcode'=>$productBarcode,
            'transfer_id' => $orderId,
            'status_transfer'=>StockTransferStatus::SCANNED,
            'client_id' => $this->getClientID(),
        ])->count();
    }

    private function getQtyScannedProductItem($productBarcode,$orderId,$transferItemId) {

        $skuId = $this->barcodeService->getSkuIdByProductBarcodeInItem($productBarcode);

        return EcommerceStock::find()->andWhere([
            'client_product_sku' =>  $skuId,
//            'product_barcode'=>$productBarcode,
            'transfer_id' => $orderId,
            'transfer_item_id' => $transferItemId,
            'status_transfer'=>StockTransferStatus::SCANNED,
            'client_id' => $this->getClientID(),
        ])->count();
    }

    private function getQtyScanned($orderId) {
        return EcommerceStock::find()->andWhere([
            'transfer_id' => $orderId,
            'status_transfer'=>StockTransferStatus::SCANNED,
            'client_id' => $this->getClientID(),
        ])->count();
    }

    public function emptyPackage($aLcBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $stocks = EcommerceStock::find()->andWhere([
            'client_id'=>$this->getClientID(),
            'transfer_id' => $parsePikingList->transferId,
//            'box_address_barcode'=>$aOurBoxBarcode,
            'transfer_outbound_box'=>$aLcBarcode,
            'status_transfer'=>StockTransferStatus::SCANNED,
        ])->all();

        foreach ($stocks as $stock) {

            $stock->transfer_outbound_box = '';
            $stock->status_transfer = StockTransferStatus::PRINTED_PICKING_LIST;
            $stock->save(false);

            $skuId = $this->barcodeService->getSkuIdByProductBarcodeInItem( $stock->product_barcode);

            $inboundItem = EcommerceTransferItems::find()->andWhere([
                'transfer_id' =>  $parsePikingList->transferId,
                'client_SkuId' =>$skuId,
//                'product_barcode' => $stock->product_barcode,
                'id' => $stock->transfer_item_id,
            ])->one();

            if ($inboundItem) {
//                $inboundItem->accepted_qty = $this->getQtyScannedProduct($stock->product_barcode, $parsePikingList->transferId);
                $inboundItem->accepted_qty = $this->getQtyScannedProductItem($stock->product_barcode, $parsePikingList->transferId,$inboundItem->id);
                $inboundItem->save(false);
            }
        }

        $this->makeScannedOrder($parsePikingList->transferId);
    }

//    public function sendByAPI($aPikingListBarcode) {
//
//        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);
//
//        $transferItems = EcommerceTransferItems::find()->andWhere(['transfer_id' =>  $parsePikingList->transferId])->all();
//
//        $result = [];
//        foreach ($transferItems as $item) {
//

//
//            if(empty($productInBox) || !is_array($productInBox) ) {
//                continue;
//            }
//
//            foreach ($productInBox as $product) {
//                $productData = new \stdClass();
//                $productData->OutBoundId = $item->client_OutboundId;
//                $productData->LcBarcode = $product['outbound_box'];
//                $productData->LotOrSingleBarcode = $product['product_barcode'];
//                $productData->LotOrSingleQuantity = $product['qtyProductInBox'];
//                $productData->WaybillNumber =  $item->transfer_id;
//
//                $result [] = $productData;
//            }
//        }
//
//        return  $result;
//    }

    public function getProductInBoxForSendByAPI($aTransferId,$aTransferItemId) {
        return EcommerceStock::find()->select('transfer_outbound_box,product_barcode, count(product_barcode) as qtyProductInBox')->andWhere([
                    'client_id'=>$this->getClientID(),
                    'transfer_id' => $aTransferId,
                    'transfer_item_id' => $aTransferItemId,
                    'status_transfer'=>StockTransferStatus::SCANNED,
                ])
                ->groupBy('transfer_outbound_box, product_barcode')
                ->asArray()
                ->all();
    }

    public function isExtraBarcodeInOrder($aPikingListBarcode, $aProductBarcode)
    {
        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

//        $skuId = $this->getSkuIdByProductBarcode($aProductBarcode);
        $skuId = $this->barcodeService->getSkuIdByProductBarcodeInItem($aProductBarcode);

        return EcommerceTransferItems::find()->andWhere([
            'transfer_id' => $parsePikingList->transferId,
            'client_SkuId' => $skuId,
//            'product_barcode' => $aProductBarcode,
        ])->andWhere('expected_qty = accepted_qty AND expected_qty != 0')
         ->exists();
    }

    public function isExtraBarcodeInBox($aOurBoxBarcode,$aPikingListBarcode, $aProductBarcode)
    {
        $productInfoInBox = $this->getProductInfoInOurBox($aProductBarcode,$aOurBoxBarcode,$aPikingListBarcode);

        return $productInfoInBox->scannedProductQtyInBox ==  $productInfoInBox->expectedProductQtyInBox;
    }

    public function isProductBarcodeExist($aPikingListBarcode,$aProductBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

//        $skuId = $this->getSkuIdByProductBarcode($aProductBarcode);
        $skuId = $this->barcodeService->getSkuIdByProductBarcodeInItem($aProductBarcode);

        return EcommerceTransferItems::find()->andWhere([
            'transfer_id' => $parsePikingList->transferId,
            'client_SkuId' => $skuId,
//            'product_barcode' => $aProductBarcode,
        ])->exists();
    }


    public function isProductOurBoxBarcodeExist($aPikingListBarcode,$aOurBoxBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return EcommerceStock::find()->andWhere([
            'transfer_id' => $parsePikingList->transferId,
            'box_address_barcode' => $aOurBoxBarcode,
        ])->exists();
    }

    public function resetTransferOrder ($aTransferId) {
        EcommerceTransfer::updateAll([
            'status'=>TransferStatus::_NEW,
            'allocated_qty'=>0,
            'accepted_qty'=>0,
            'print_picking_list_date'=>'',
            'begin_datetime'=>null,
            'end_datetime'=>null,

        ],['id'=>$aTransferId]);

        EcommerceTransferItems::updateAll([
            'status'=>TransferStatus::_NEW,
            'allocated_qty'=>0,
            'accepted_qty'=>0,
            'begin_datetime'=>null,
            'end_datetime'=>null,
        ],['transfer_id'=>$aTransferId]);

        EcommerceStock::updateAll([
            'transfer_id'=>0,
            'transfer_item_id'=>0,
            'status_transfer'=>StockTransferStatus::_NEW,
            'transfer_box_check_step'=>'',
            'transfer_outbound_box'=>'',
            'client_id' => 2,
            'status_availability' => StockAvailability::YES,
        ],['transfer_id'=>$aTransferId]);
    }

    public static function parsePikingListBarcode($aPikingListBarcode) {
        $result = new \stdClass();
        $explodeBarcode = explode('-',$aPikingListBarcode);
        $result->transferId  = ArrayHelper::getValue($explodeBarcode,'0',0);
        $result->batchId  = ArrayHelper::getValue($explodeBarcode,'1',0);

        return $result;
    }

    public function getClientID()
    {
        return 2;
    }

    public function getCountScannedBox($aTransferId) {
        return EcommerceStock::find()->select('COUNT(DISTINCT `transfer_outbound_box`)')
            ->andWhere(['transfer_id' =>$aTransferId])
            ->andWhere('transfer_outbound_box != 0 && transfer_outbound_box != "" && transfer_outbound_box is NOT NULL')
            ->scalar();
    }

    public function getScannedBox($aTransferId) {
        return EcommerceStock::find()->select('transfer_outbound_box')
            ->andWhere(['transfer_id' =>$aTransferId])
            ->andWhere('transfer_outbound_box != 0 && transfer_outbound_box != "" && transfer_outbound_box is NOT NULL')
            ->groupBy('transfer_outbound_box')
            ->orderBy('transfer_outbound_box')
            ->asArray()
            ->column();
    }

    public function getScannedBoxWithProducts($aTransferId) {
        return EcommerceStock::find()->select('SQL_CALC_FOUND_ROWS `transfer_outbound_box` as transferOutboundBox, `product_barcode` as productBarcode, `client_product_sku` as productSku, COUNT(`product_barcode`) as productQty ')
            ->andWhere(['transfer_id' =>$aTransferId])
            ->andWhere('transfer_outbound_box != 0 && transfer_outbound_box != "" && transfer_outbound_box is NOT NULL')
            ->groupBy('transfer_outbound_box, product_barcode')
            ->asArray()
            ->all();
    }

    public function getExpectedBox($aBatchId)
    {
        return EcommerceTransfer::find()->select('client_LcBarcode')
            ->andWhere(["client_BatchId" => $aBatchId])
            ->andWhere(['!=','client_LcBarcode',TransferDefaultValue::MAIN_VIRTUAL_BOX])
            ->column();
    }
}
