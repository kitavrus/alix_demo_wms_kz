<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:14
 */

namespace common\ecommerce\defacto\outbound\repository;


use common\ecommerce\constants\LamodaDefaultValue;
use common\ecommerce\constants\KaspiDefaultValue;
use common\ecommerce\constants\OutboundStatus;
use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use common\ecommerce\defacto\pickingList\repository\PickingListRepository;
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

class OutboundRepository
{
    public function getClientID()
    {
        return 2;
    }

    //
    public function isOrderExist($orderNumber)
    {
        return EcommerceOutbound::find()->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])->exists();
    }


    public function isOrderExistByAny($anyField)
    {

        $outboundId = EcommerceStock::find()->select('outbound_id')
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere(['outbound_box'=>$anyField])
            ->scalar();

        return EcommerceOutbound::find()
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere('client_ReferenceNumber = :client_ReferenceNumber OR order_number = :order_number OR id = :id',[':client_ReferenceNumber'=>$anyField,':order_number'=>$anyField,':id'=>$outboundId])
            ->exists();
    }

    //
    public function getOrderByAny($anyField)
    {
        $outboundId = EcommerceStock::find()->select('outbound_id')
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere(['outbound_box'=>$anyField])
            ->scalar();

        return EcommerceOutbound::find()
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere('client_ReferenceNumber = :client_ReferenceNumber OR order_number = :order_number OR id = :id',[':client_ReferenceNumber'=>$anyField,':order_number'=>$anyField,':id'=>$outboundId])
            ->one();
    }


    //
    public function getOrderByOrderNumber($orderNumber)
    {
        return EcommerceOutbound::find()->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])->one();
    }

    public function getOrdersForPrintPickList()
    {
        $query = EcommerceOutbound::find()->andWhere([
            "client_id" => $this->getClientID(),
            'status'=>OutboundStatus::getOrdersForPrintPickList()
        ]);


        return new ActiveDataProvider([
            'query' => $query,
//            'pagination' => false,
              'pagination' => [
                'pageSize' => 20,
              ],
            'sort' => ['defaultOrder' => ['created_at' => SORT_ASC]]
//            'sort' => ['defaultOrder' => ['expected_qty' => SORT_ASC,'created_at' => SORT_ASC]]
        ]);
    }

    public function getOrderInfo($id)
    {
        $order = EcommerceOutbound::find()->andWhere([
            "id" => $id,
            "client_id" => $this->getClientID(),
        ])->one();

        $items = EcommerceOutboundItem::find()->andWhere(['outbound_id' => $order->id])->all();
        $stocks = EcommerceStock::find()->select('product_barcode,product_qrcode')
								->andWhere(['outbound_id' => $order->id])
								->groupBy(['product_barcode','product_qrcode'])
								->asArray()
								->all();
								
        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;
		$result->stocks = $stocks;
        $result->outboundBoxBarcode = EcommerceStock::find()
													->select('outbound_box')
													->andWhere(['outbound_id'=>$id])
													->andWhere('outbound_box  != "" AND outbound_box  != 0')
													->scalar();

        $totalPrice = 0;
        $totalPriceTax = 0;
        $totalPriceAndDiscount = 0;
        $productsInOrder = $result->items;
        foreach($productsInOrder as $key=>$productRow) {
            if ($productRow->accepted_qty < 1) {
                continue;
            }

            $totalPriceTax += $productRow->price_tax;
            $rowPrice = ($productRow->product_price * $productRow->accepted_qty);
            $rowPriceAndDiscount = ($rowPrice - $productRow->price_discount);

            $totalPrice += $rowPrice;
            $totalPriceAndDiscount += $rowPriceAndDiscount;
        }

        $result->OrderTotalPrice = $totalPrice;
        $result->OrderTotalPriceAndDiscount = $totalPriceAndDiscount;
        $result->OrderTotalPriceTax = $totalPriceTax;

        return $result;
    }

//    public function qtyProductInBox($orderId, $boxBarcode)
//    {
//        return EcommerceStock::find()->andWhere([
//            'client_id' => $this->getClientID(),
//            'outbound_id' => $orderId,
//            'outbound_box' => $boxBarcode,
//        ])->count();
//    }

    public function isProductExistInOrder($outboundOrderID,$productBarcode)
    {
        return EcommerceOutboundItem::find()->andWhere([
            'outbound_id' => $outboundOrderID,
            'product_sku' => $this->getProductSkuIdByBarcode($productBarcode),
//            'product_barcode' => $productBarcode,
        ])->exists();
    }

    public function findOrderByPickList($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);
        $outbound = new EcommerceOutbound();
        if ($pikingList) {
            $outbound = EcommerceOutbound::find()->andWhere([
                'client_id' => $this->getClientID(),
                'id' => $pikingList['id'],
                'order_number' => $pikingList['orderNumber'],
            ])->one();
        }

        return $outbound;
    }

    //
    public function isOrderExistByPickingBarcode($id,$orderNumber)
    {
        return EcommerceOutbound::find()->andWhere([
            'client_id' => $this->getClientID(),
            'id' => $id,
            'order_number' => $orderNumber,
        ])->exists();
    }
    //
    public function isNotDoneOrder($id,$orderNumber)
    {
        return EcommerceOutbound::find()->andWhere([
            'client_id' => $this->getClientID(),
            'id' => $id,
            'order_number' => $orderNumber,
            'status' => OutboundStatus::getNotDoneOrders(),
        ])->exists();
    }

    public function getPickListByBarcode($pickList)
    {
        $result = explode('-',$pickList);

//        VarDumper::dump($result,10,true);
//        die('-getPickListByBarcode-');
        return [
            'id'=>ArrayHelper::getValue($result,'0'),
            'orderNumber'=>ArrayHelper::getValue($result,'1').'-'.ArrayHelper::getValue($result,'2'),
        ];
    }

    public function getEmployeeByBarcode($barcode)
    {
        return EmployeeRepository::getEmployeeByBarcode($barcode);
//        return Employees::find()->andWhere([
//            'barcode' => $barcode
//        ])->one();
    }
    // STOCK
    public function makeScannedProduct($dto)
    {
        $this->makeScannedStock($dto);
        $this->makeScannedItem($dto);
        $this->makeScannedOrder($dto->order->id);
    }

    private function makeScannedStock($dto)
    {
        $stock = EcommerceStock::find()->andWhere([
//                    'product_barcode' => $dto->productBarcode,
					 'client_product_sku' => $this->getProductSkuIdByBarcode($dto->productBarcode),
                    'outbound_id' => $dto->order->id,
                    'status_outbound' => StockOutboundStatus::getReadyForScanning(),
                    'client_id' => $this->getClientID(),
                ])
                ->one();

        if ($stock) {
            $stock->status_outbound = StockOutboundStatus::SCANNED;
            $stock->outbound_box = $dto->packageBarcode;
            $stock->scan_out_employee_id = $dto->employee->id;
            $stock->scan_out_datetime = time();
            $stock->save(false);
        }
    }
	
	public function makeScannedStockQRCode($dto)
	{
		$stock = EcommerceStock::find()->andWhere([
			'client_product_sku' => $this->getProductSkuIdByBarcode($dto->productBarcode),
			'outbound_id' => $dto->order->id,
			'status_outbound' => StockOutboundStatus::SCANNED,
			'client_id' => $this->getClientID(),
			//'product_qrcode' => "",
		])
		->one();

		if ($stock) {
			$stock->product_qrcode = $dto->productQRCode;
			$stock->save(false);
		}
	}
	
	
	
    private function makeScannedItem($dto)
    {
        $outboundOrderItem = EcommerceOutboundItem::find()->andWhere([
            'outbound_id' => $dto->order->id,
//            'product_barcode' => $dto->productBarcode,
			'product_sku' => $this->getProductSkuIdByBarcode($dto->productBarcode),
        ])->one();

        if ($outboundOrderItem) {

            if (intval($outboundOrderItem->accepted_qty) < 1) {
                $outboundOrderItem->begin_datetime = time();
                $outboundOrderItem->status = OutboundStatus::SCANNING;
            }

            $outboundOrderItem->accepted_qty = $this->getQtyScannedProduct($dto->productBarcode,$dto->order->id);

            if ($outboundOrderItem->accepted_qty == $outboundOrderItem->expected_qty || $outboundOrderItem->accepted_qty == $outboundOrderItem->allocated_qty ) {
                $outboundOrderItem->status = OutboundStatus::SCANNED;
            }

            $outboundOrderItem->end_datetime = time();
            $outboundOrderItem->save(false);
        }
    }
    private function makeScannedOrder($orderId)
    {
        $outboundOrder = EcommerceOutbound::find()
                         ->andWhere([
                             'id'=>$orderId,
                             'client_id' => $this->getClientID()
                         ])->one();

        if(intval($outboundOrder->accepted_qty) < 1) {
            $outboundOrder->begin_datetime = time();
            $outboundOrder->status = OutboundStatus::SCANNING;
        }

        $outboundOrder->accepted_qty = $this->getQtyScanned($orderId);

        if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty || $outboundOrder->accepted_qty == $outboundOrder->allocated_qty ) {
            $outboundOrder->status = OutboundStatus::SCANNED;
        }

        $outboundOrder->end_datetime = time();
        $outboundOrder->save(false);
    }

    // PRINT BOX LABEL
    public function makePrintBoxLabel($dto) {

        $this->makePrintBoxPickingList($dto);
        $this->makePrintBoxOnStock($dto);
        $this->makePrintBoxOnItem($dto);
        $this->makePrintBoxOnOrder($dto);
        $this->makePrintBoxOnDeliveryProposal($dto);
    }

    private function makePrintBoxOnStock($dto) {
        $stocks = EcommerceStock::find()->andWhere([
            'client_id' => $this->getClientID(),
            'outbound_id' => $dto->pickList->outbound_id,
//            'outbound_picking_list_id' => $dto->pickList->id,
            'status_outbound' =>StockOutboundStatus::getPrintBoxOnStock()
//            'status' => [
//                EcommerceStock::STATUS_OUTBOUND_SCANNED,
//                EcommerceStock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
//                EcommerceStock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
//            ]
        ])->all();

        if ($stocks) {
           foreach($stocks as $stock) {
               $stock->status_outbound = StockOutboundStatus::PRINT_BOX_LABEL;
               $stock->save(false);
           }
        }
    }

    private function makePrintBoxPickingList($dto)
    {
        PickingListRepository::makePrintBoxPickingList($dto->order->id);
//      EcommercePickingList::updateAll(['status'=>EcommercePickingList::STATUS_PRINT_BOX_LABEL],['outbound_id'=>$dto->order->id]);
    }


    private function makePrintBoxOnItem($dto)
    {
        $outboundOrderItems = EcommerceOutboundItem::find()
            ->andWhere(['outbound_id' => $dto->order->id])
            ->andWhere('accepted_qty > 0')
            ->all();

        if ($outboundOrderItems) {
            foreach ($outboundOrderItems as $item) {
                $item->status = OutboundStatus::PRINT_BOX_LABEL;
                $item->save(false);
            }
        }
    }
    private function makePrintBoxOnOrder($dto)
    {
        $outboundOrder = EcommerceOutbound::find()
            ->andWhere([
                'id'=>$dto->order->id,
                'client_id' => $this->getClientID()
            ])->one();
        if($outboundOrder) {
            $outboundOrder->status = OutboundStatus::PRINT_BOX_LABEL;
            $outboundOrder->accepted_number_places_qty = $this->getQtyBoxesInOrder($dto->order->id);
            $outboundOrder->packing_date = DateHelper::getTimestamp();
            $outboundOrder->save(false);
        }
    }

    private function makePrintBoxOnDeliveryProposal($dto)
    {
        // TODO
    }



    private function getProductInBox($productBarcode,$boxBarcode,$orderId ) {
        return EcommerceStock::find()->andWhere([
            'product_barcode'=>$productBarcode,
            'outbound_box'=>$boxBarcode,
            'outbound_id' => $orderId,
            'status_outbound'=>StockOutboundStatus::SCANNED,
            'client_id' => $this->getClientID(),
        ])->all();
    }


    private function getQtyScannedProduct($productBarcode,$orderId) {
        return EcommerceStock::find()->andWhere([
            'product_barcode'=>$productBarcode,
            'outbound_id' => $orderId,
            'status_outbound'=>StockOutboundStatus::SCANNED,
            'client_id' => $this->getClientID(),
        ])->count();
    }

    private function getQtyScanned($orderId) {
        return EcommerceStock::find()->andWhere([
            'outbound_id' => $orderId,
            'status_outbound'=>StockOutboundStatus::SCANNED,
            'client_id' => $this->getClientID(),
        ])->count();
    }

    public function getOrderForComplete()
    {
        $query = EcommerceOutbound::find()->andWhere([
            "client_id" => $this->getClientID(),
//            'status' => [
//                EcommerceStock::STATUS_OUTBOUND_ACCEPTED,
//            ]
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
    }

    public function isExtraBarcodeInOrder($outboundId,$productBarcode) {
        return EcommerceOutboundItem::find()->andWhere([
            'outbound_id'=>$outboundId,
            //'product_barcode'=>$productBarcode,
			'product_sku' => $this->getProductSkuIdByBarcode($productBarcode),
        ])
        ->andWhere('expected_qty = accepted_qty')->exists();
//        ->andWhere('expected_qty = accepted_qty AND field_extra1 = "" ')->exists();
    }

//    public function getOrderItemsForDiffReport($orderID)
////    public function getOrderItemsForDiffReport($pickListID)
//    {
//        $subQuery = (new Query())
//            ->select('count(*)')
//            ->from('stock as stck')
//            ->andWhere(['stck.status' =>StockOutboundStatus::SCANNED, 'stck.outbound_id' => $orderID])
////            ->andWhere(['stck.status' => Ecommercestock::STATUS_OUTBOUND_SCANNED, 'stck.outbound_picking_list_id' => $pickListID])
//            ->andWhere('stck.product_barcode = stock.product_barcode')
//            ->andWhere(["client_id" => $this->getClientID()]);
//
//        return EcommerceStock::find()
//            ->select(['id', 'outbound_id', 'product_barcode', 'outbound_box', 'status', 'primary_address', 'secondary_address', 'product_model', 'field_extra1', 'count(*) as items', 'count_status_scanned' => $subQuery])
//            ->andWhere([
//                'outbound_id' => $orderID,
//                'status_outbound'=>StockOutboundStatus::getOrderItemsForDiffReport()
//            ])
//            ->andWhere(["client_id" => $this->getClientID()])
//            ->groupBy('product_barcode')
//            ->orderBy([
//                'product_barcode' => SORT_DESC,
//                'count_status_scanned' => SORT_DESC,
//            ])
//            ->asArray()
//            ->all();
//    }

    public function getQtyBoxesInOrder($orderId)
    {
        return EcommerceStock::find()
            ->andWhere([
                'outbound_id' => $orderId,
                'status'=>StockOutboundStatus::getPrintBoxOnStock()
            ])
            ->andWhere(["client_id" => $this->getClientID()])
            ->groupBy('outbound_box')
            ->orderBy('outbound_box')
            ->asArray()
            ->count();
    }

//    public function getBoxesInOrder($orderId)
//    {
//        return EcommerceStock::find()
//            ->andWhere([
//                'outbound_id' => $orderId,
//                'status_outbound'=>StockOutboundStatus::getPrintBoxOnStock()
//            ])
//            ->andWhere(["client_id" => $this->getClientID()])
//            ->groupBy('outbound_box')
//            ->orderBy('outbound_box')
//            ->asArray()
//            ->all();
//    }

    public function acceptedOrder($orderId)
    {
        //One function
        $orderInfo = $this->getOrderInfo($orderId);

        if(!empty($orderInfo->order->date_left_warehouse)) {
            return;
        }

        $orderInfo->order->status = OutboundStatus::DONE;
        $orderInfo->order->date_left_warehouse = time();
        $orderInfo->order->save(false);

        foreach ($orderInfo->items as $item) {
            $item->status = OutboundStatus::DONE;
            $item->save(false);
        }


        $stocks = EcommerceStock::find()->andWhere(["client_id" => $this->getClientID(), 'outbound_id' => $orderInfo->order->id])->all();
        foreach ($stocks as $stock) {
            $stock->status_outbound = StockOutboundStatus::DONE;
            $stock->save(false);
        }

    }

    public function showOrderItems($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        $items = EcommerceOutboundItem::find()->andWhere([
                'outbound_id' => $pikingList['id'],
            ])
            ->asArray()
            ->all();

        return $items;
    }

    public function qtyProductInPackage($pickList,$packageBarcode) {

       $pikingList = $this->getPickListByBarcode($pickList);

       return EcommerceStock::find()
            ->andWhere([
            "client_id" => $this->getClientID(),
            'outbound_id' => $pikingList['id'],
            'outbound_box' => $packageBarcode,
        ])
        ->count();
    }


    public function usePackageBarcodeInOtherOrder($pickList,$packageBarcode)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        return EcommerceStock::find()
            ->andWhere('outbound_id != :outboundId',[':outboundId'=>$pikingList['id']])
            ->andWhere(['outbound_box' => $packageBarcode])
            ->exists();
    }

    public function isOrderReserved($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        return EcommerceStock::find()
            ->andWhere(['outbound_id' => $pikingList['id']])
            ->exists();
    }

    public function isOrderScanned($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        return EcommerceOutbound::find()
            ->andWhere(['id' => $pikingList['id']])
            ->andWhere('accepted_qty > 0')
            ->exists();
    }

    public function emptyPackage($dto) {

        $stocks =  EcommerceStock::find()->andWhere([
            'client_id'=>$this->getClientID(),
            'outbound_id'=>$dto->order->id,
            'outbound_box'=>$dto->packageBarcode,
            'status_outbound'=>StockOutboundStatus::SCANNED,
        ])->all();

        foreach ($stocks as $stock) {

            $stock->outbound_box = '';
            $stock->status_outbound = StockOutboundStatus::PRINTED_PICKING_LIST;
            $stock->save(false);

            $inboundItem = EcommerceOutboundItem::find()->andWhere([
                'outbound_id' => $dto->order->id,
                'product_barcode' => $stock->product_barcode,
            ])->one();

            if ($inboundItem) {
                $inboundItem->accepted_qty = $this->getQtyScannedProduct($stock->product_barcode, $dto->order->id);
                $inboundItem->save(false);
            }
        }
        $this->makeScannedOrder($dto->order->id);
    }

    public function create($shipmentList) {

        $shipmentListOut = [];
        if(empty($shipmentList)) {
            return $shipmentListOut;
        }

        if(!is_array($shipmentList)) {
            $shipmentList = [$shipmentList];
        }

        foreach($shipmentList as $shipment) {

            $orderNumber = ArrayHelper::getValue($shipment,'ExternalShipmentId');
//            $orderNumber = ArrayHelper::getValue($shipment,'ShipmentId');
//            $externalOrderNumber = ArrayHelper::getValue($shipment,'ExternalShipmentNo');

            $orderNumber = trim($orderNumber);
            if(empty($orderNumber)) {
                continue;
            }



//            $order = EcommerceOutbound::find()->andWhere(['order_number' => $orderNumber,'external_order_number'=>$externalOrderNumber,'client_id' => $this->getClientID()])->one();
            $order = EcommerceOutbound::find()->andWhere(['order_number' => $orderNumber,'client_id' => $this->getClientID()])->one();
            if ($order) {
				continue;
			}

            $order =  new EcommerceOutbound();
            $order->order_number = $orderNumber;
            $order->external_order_number = @ArrayHelper::getValue($shipment,'MarketPlaceOrderNumber','');
            $order->customer_name = ArrayHelper::getValue($shipment,'CustomerName','');
            $order->customer_address = ArrayHelper::getValue($shipment,'ShippingAddress','');
            $order->city = ArrayHelper::getValue($shipment,'ShippingCity','');
            $order->email = ArrayHelper::getValue($shipment,'ShippingEmail','');
            $order->phone_mobile1 = ArrayHelper::getValue($shipment,'ShippingPhone','');
            $order->client_Priority = ArrayHelper::getValue($shipment,'Priority');
            $order->client_CargoCompany = ArrayHelper::getValue($shipment,'CourierCompany','');
            $order->client_StoreName = ArrayHelper::getValue($shipment,'StoreName','');
            $order->client_PackMessage = @ArrayHelper::getValue($shipment,'PackMessage','');
            $order->client_ShipmentSource = @ArrayHelper::getValue($shipment,'ShipmentSource','');
            $order->status = OutboundStatus::_NEW;
            $order->client_id = $this->getClientID();;
            $order->save(false);

            $B2CShipmentDetailList = ArrayHelper::getValue($shipment,'B2CShipmentDetailList');
            $expectedQty = 0;
            $totalPrice = 0;
            $totalPriceTax = 0;
            $totalPriceDiscount = 0;
            if(!empty($B2CShipmentDetailList)) {
//                $productInOrder = unserialize($B2CShipmentDetailList);
                $data = ArrayHelper::getValue($B2CShipmentDetailList,'B2CShipmentDetailDto');

                $productItems = count($data) <=1 ? [$data] : $data;

                foreach($productItems as $productItem) {
                    $item = new EcommerceOutboundItem();
                    $item->outbound_id = $order->id;
                    $item->product_sku = ArrayHelper::getValue($productItem, 'SkuId');
                    $item->expected_qty = ArrayHelper::getValue($productItem, 'Quantity',0);
                    $item->product_name = @ArrayHelper::getValue($productItem, 'ProductName','-');
                    $item->product_model = ArrayHelper::getValue($productItem, 'ProductCode');

                    $item->product_price = ArrayHelper::getValue($productItem, 'UnitPrice',0);
                    $item->price_tax = ArrayHelper::getValue($productItem, 'UnitTax',0);
                    $item->price_discount = ArrayHelper::getValue($productItem, 'UnitDiscount',0);
                    $item->comment_message = ArrayHelper::getValue($productItem, 'ItemMessage','');
                    $item->save(false);

                    $expectedQty += $item->expected_qty;

                    $totalPrice += $item->product_price;
                    $totalPriceTax += $item->price_tax;
                    $totalPriceDiscount += $item->price_discount;
                }
            }

            $order->expected_qty = $expectedQty;
            $order->total_price = $totalPrice;
            $order->total_price_tax = $totalPriceTax;
            $order->total_price_discount = $totalPriceDiscount;
            $order->save(false);
            $shipmentListOut [] = $orderNumber;
        }

        return $shipmentListOut;
    }

    public function setStatusCancelByCustomer($outboundID,$reason = '') {
        $order = EcommerceOutbound::find()->andWhere(['id' => $outboundID,'client_id' => $this->getClientID()])->one();
        $order->status = OutboundStatus::CANCEL;
        $order->client_CancelReason = $reason;
        $order->save(false);
        return $order;
    }

    public function setStatusCancelByAPI($outboundID,$reason = '') {
       return $this->setStatusCancelByCustomer($outboundID,$reason);
    }

    public function getDataForSendByApi() {
       return EcommerceOutbound::find()->andWhere([
           'api_status' => StockAPIStatus::NO,
           'status' => OutboundStatus::PRINT_BOX_LABEL,
           'client_id' => $this->getClientID()
       ])->all();
    }

    public function setApiStatus($ourOutboundId,$ApiStatus) {
        EcommerceOutbound::updateAll([
            'api_status'=>$ApiStatus,
        ],[
            'id'=>$ourOutboundId,
        ]);
    }

    public function isPacked($ourOutboundId) {
        return EcommerceOutbound::find()->andWhere([
            'id' =>$ourOutboundId,
            'status' => OutboundStatus::PRINT_BOX_LABEL,
            'client_id' => $this->getClientID()
        ])->exists();
    }

    public function canPrintPickingList($ourOutboundId) {
        return EcommerceOutbound::find()->andWhere([
            'id' =>$ourOutboundId,
            'status' => OutboundStatus::getOrdersForPrintPickList(),
            'client_id' => $this->getClientID()
        ])->exists();
    }


    public function isEmptyPackageBarcodeInOrder($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        return EcommerceStock::find()
            ->andWhere(['outbound_id'=>$pikingList['id'],'client_id' => $this->getClientID()])
            ->andWhere('outbound_box = "" OR outbound_box = 0 OR outbound_box IS NULL')
            ->andWhere('status_outbound != :status_outbound',[':status_outbound'=>StockOutboundStatus::PRINTED_PICKING_LIST])
            ->exists();
    }

    public function pickingList($outboundId) {
        $onStockAll = EcommerceStock::find()->andWhere([
            'outbound_id' => $outboundId,
            'client_id' => $this->getClientID(),
        ])
        ->orderBy('place_address_sort1')
        ->all();
        return $onStockAll;
    }

    public function findAvailableProductsInOtherAddress($productBarcode,$warehouseId = null)
    {
        return EcommerceStock::find()->andWhere([
            'client_id' => $this->getClientID(),
            'status_availability' => StockAvailability::YES,
            'product_barcode' => $productBarcode,
        ])
        ->andWhere(['order_re_reserved'=>''])
        ->andFilterWhere(['warehouse_id'=>$warehouseId])
        ->orderBy('place_address_sort1')
        ->all();

    }

    public function findStockById($stockId) {
        return EcommerceStock::find()->andWhere(['client_id' => $this->getClientID(),'id' => $stockId])->one();
    }

    public function getOrderByStockById($stockId) {
        $outboundId = EcommerceStock::find()->select('outbound_id')->andWhere(['client_id' => $this->getClientID(),'id' => $stockId])->scalar();
        return EcommerceOutbound::find()->andWhere([
            "id" => $outboundId,
            "client_id" => $this->getClientID(),
        ])->one();
    }
	
	

    public function isKaspiOrder($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);
        $order = EcommerceOutbound::find()->andWhere(['id' => $pikingList['id']])->one();

        return $order->client_ShipmentSource == KaspiDefaultValue::CLIENT_SHIPMENT_SOURCE;
    }

    public function isValidKaspiOrder($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);
        $order = EcommerceOutbound::find()->andWhere(['id' => $pikingList['id']])->one();

        return ($order->expected_qty != $order->allocated_qty) || ($order->allocated_qty != $order->accepted_qty);
    }
	
		public function isLamodaOrder($pickList)
	{
		$pikingList = $this->getPickListByBarcode($pickList);
		$order = EcommerceOutbound::find()->andWhere(['id' => $pikingList['id']])->one();

		return $order->client_ShipmentSource == LamodaDefaultValue::CLIENT_SHIPMENT_SOURCE;
	}

	public function isValidLamodaOrder($pickList)
	{
		$pikingList = $this->getPickListByBarcode($pickList);
		$order = EcommerceOutbound::find()->andWhere(['id' => $pikingList['id']])->one();

		return $order->expected_qty != $order->accepted_qty;
	}


	public function getProductSkuIdByBarcode($productBarcode)
	{
		return EcommerceStock::find()->select('client_product_sku')->andWhere(['product_barcode' => $productBarcode])->scalar();
	}
	
}