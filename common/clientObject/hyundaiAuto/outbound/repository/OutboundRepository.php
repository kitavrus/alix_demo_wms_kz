<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:14
 */

namespace common\clientObject\hyundaiAuto\outbound\repository;


use common\helpers\DateHelper;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\product\models\Product;
use common\modules\stock\models\Stock;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class OutboundRepository
{
    public function getClientID()
    {
        return 95;
    }

    //
    public function isOrderExist($orderNumber)
    {
        return OutboundOrder::find()->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])->exists();
    }

    public function getOrdersForPrintPickList()
    {
        $query = OutboundOrder::find()->andWhere([
            "client_id" => $this->getClientID(),
            'status' => [
                Stock::STATUS_OUTBOUND_NEW,
                Stock::STATUS_OUTBOUND_FULL_RESERVED,
                Stock::STATUS_OUTBOUND_PART_RESERVED,
                Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
            ]
        ]);


        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
    }

    public function getOrderInfo($id)
    {
        $order = OutboundOrder::find()->andWhere([
            "id" => $id,
            "client_id" => $this->getClientID(),

        ])->one();
        $items = OutboundOrderItem::find()->andWhere(['outbound_order_id' => $order->id])->all();

        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;

        return $result;
    }

    public function qtyProductInBox($orderId, $boxBarcode)
    {
        return Stock::find()->andWhere([
            'client_id' => $this->getClientID(),
            'outbound_order_id' => $orderId,
            'box_barcode' => $boxBarcode,
        ])->count();
    }

    public function isProductExistInOrder($outboundOrderID,$productBarcode)
    {
        return OutboundOrderItem::find()->andWhere([
            'outbound_order_id' => $outboundOrderID,
            'product_barcode' => $productBarcode,
        ])->exists();
    }

    public function findOrderByPickList($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        $outbound = new OutboundOrder();
        if ($pikingList) {
            $outbound = OutboundOrder::find()->andWhere([
                'client_id' => $this->getClientID(),
                'id' => $pikingList->outbound_order_id,
            ])->one();
        }

        return $outbound;
    }

    public function getPickListByBarcode($pickList)
    {
        return OutboundPickingLists::find()->andWhere([
            'client_id' => $this->getClientID(),
            'barcode' => $pickList,
        ])->one();
    }

    public function getEmployeeByBarcode($barcode)
    {
        return Employees::find()->andWhere([
            'barcode' => $barcode
        ])->one();
    }
    // STOCK
    public function makeScannedProduct($dto)
    {
        $this->makeScannedStock($dto);
        $this->makeScannedItem($dto);
        $this->makeScannedOrder($dto->order->id);
    }
	
	
   // STOCK
    public function makeScannedProductQty($dto)
    {
        if ($dto->productQty > 0) {
            for ($i = 1; $i <= $dto->productQty; $i++) {
                $this->makeScannedStock($dto);
            }
			$this->makeScannedItem($dto);
        }
        $this->makeScannedOrder($dto->order->id);
    }
	

    private function makeScannedStock($dto)
    {
        $stock = Stock::find()->andWhere([
                    'product_barcode' => $dto->productBarcode,
                    'outbound_order_id' => $dto->pickList->outbound_order_id,
//                    'outbound_picking_list_id' => $dto->pickList->id,
                    'status' => [
                        Stock::STATUS_OUTBOUND_PICKED,
                        Stock::STATUS_OUTBOUND_SCANNING
                    ],
                    'client_id' => $this->getClientID(),
                ])
//                ->andWhere('field_extra2 = "" ')
//                ->filterWhere(['field_extra2'=>$dto->fabBarcode])
                ->one();

        if ($stock) {
            $stock->status = Stock::STATUS_OUTBOUND_SCANNED;
            $stock->box_barcode = $dto->boxBarcode;
            $stock->scan_out_employee_id = $dto->employee->id;
            $stock->scan_out_datetime = time();
            $stock->save(false);
        }
    }
    private function makeScannedItem($dto)
    {
        $outboundOrderItem = OutboundOrderItem::find()->andWhere([
            'outbound_order_id' => $dto->order->id,
            'product_barcode' => $dto->productBarcode,
//            'field_extra1' => "",
        ])->one();

        if ($outboundOrderItem) {

            if (intval($outboundOrderItem->accepted_qty) < 1) {
                $outboundOrderItem->begin_datetime = time();
                $outboundOrderItem->status = Stock::STATUS_OUTBOUND_SCANNING;
            }

            $outboundOrderItem->accepted_qty = $this->getQtyScannedProduct($dto->productBarcode,$dto->order->id);

            if ($outboundOrderItem->accepted_qty == $outboundOrderItem->expected_qty || $outboundOrderItem->accepted_qty == $outboundOrderItem->allocated_qty ) {
                $outboundOrderItem->status = Stock::STATUS_OUTBOUND_SCANNED;
            }

            $outboundOrderItem->end_datetime = time();
            $outboundOrderItem->save(false);
        }
    }
    private function makeScannedOrder($orderId)
    {
        $outboundOrder = OutboundOrder::find()
                         ->andWhere([
                             'id'=>$orderId,
                             'client_id' => $this->getClientID()
                         ])->one();

        if(intval($outboundOrder->accepted_qty) < 1) {
            $outboundOrder->begin_datetime = time();
            $outboundOrder->status = Stock::STATUS_OUTBOUND_SCANNING;
        }

        $outboundOrder->accepted_qty = $this->getQtyScanned($orderId);

        if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty || $outboundOrder->accepted_qty == $outboundOrder->allocated_qty ) {
            $outboundOrder->status = Stock::STATUS_OUTBOUND_SCANNED;
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
        $stocks = Stock::find()->andWhere([
            'client_id' => $this->getClientID(),
            'outbound_order_id' => $dto->pickList->outbound_order_id,
//            'outbound_picking_list_id' => $dto->pickList->id,
            'status' => [
                Stock::STATUS_OUTBOUND_SCANNED,
                Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
            ]
        ])->all();

        if ($stocks) {
           foreach($stocks as $stock) {
               $stock->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
               $stock->save(false);
           }
        }
    }

    private function makePrintBoxPickingList($dto)
    {
      OutboundPickingLists::updateAll(['status'=>OutboundPickingLists::STATUS_PRINT_BOX_LABEL],['outbound_order_id'=>$dto->order->id]);
    }


    private function makePrintBoxOnItem($dto)
    {
        $outboundOrderItems = OutboundOrderItem::find()
            ->andWhere(['outbound_order_id' => $dto->order->id])
            ->andWhere('accepted_qty > 0')
            ->all();

        if ($outboundOrderItems) {
            foreach ($outboundOrderItems as $item) {
                $item->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
                $item->save(false);
            }
        }
    }
    private function makePrintBoxOnOrder($dto)
    {
        $outboundOrder = OutboundOrder::find()
            ->andWhere([
                'id'=>$dto->order->id,
                'client_id' => $this->getClientID()
            ])->one();
        if($outboundOrder) {
            $outboundOrder->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
            $outboundOrder->accepted_number_places_qty = $this->getQtyBoxesInOrder($dto->order->id);
            $outboundOrder->packing_date = DateHelper::getTimestamp();
            $outboundOrder->save(false);
        }
    }

    private function makePrintBoxOnDeliveryProposal($dto)
    {
        // TODO
    }

    // CLEAR BOX
    public function cleanBox($dto) {

        $stocks =  Stock::find()->andWhere([
            'client_id'=>$this->getClientID(),
            'outbound_order_id'=>$dto->order->id,
            'box_barcode'=>$dto->boxBarcode,
            'status'=>Stock::STATUS_OUTBOUND_SCANNED,
        ])->all();

        foreach ($stocks as $stock) {
            $stock->box_barcode = '';
            $stock->status = Stock::STATUS_OUTBOUND_PICKED;
            $stock->save(false);

            $inboundItem = OutboundOrderItem::find()->andWhere([
                'outbound_order_id' => $dto->order->id,
                'product_barcode' => $stock->product_barcode,
            ])->one();

            if ($inboundItem) {
                $inboundItem->accepted_qty = $this->getQtyScannedProduct($stock->product_barcode, $dto->order->id);
                $inboundItem->save(false);
            }
        }
        $this->makeScannedOrder($dto->order->id);
//        $this->updateQtyScannedInOrder($inboundId);


//        $this->makeCleanBoxStock($dto->productBarcode, $dto->boxBarcode, $dto->order->id);
//        $this->recalculateOrderItems($dto->productBarcode,$dto->order->id);
//        $this->makeScannedOrder($dto->order->id);
    }

    private function getProductInBox($productBarcode,$boxBarcode,$orderId ) {
        return Stock::find()->andWhere([
            'product_barcode'=>$productBarcode,
            'box_barcode'=>$boxBarcode,
            'outbound_order_id' => $orderId,
            'status'=>Stock::STATUS_OUTBOUND_SCANNED,
            'client_id' => $this->getClientID(),
        ])->all();
    }


    private function getQtyScannedProduct($productBarcode,$orderId) {
        return Stock::find()->andWhere([
            'product_barcode'=>$productBarcode,
            'outbound_order_id' => $orderId,
            'status'=>Stock::STATUS_OUTBOUND_SCANNED,
            'client_id' => $this->getClientID(),
        ])->count();
    }

    private function getQtyScanned($orderId) {
        return Stock::find()->andWhere([
            'outbound_order_id' => $orderId,
            'status'=>Stock::STATUS_OUTBOUND_SCANNED,
            'client_id' => $this->getClientID(),
        ])->count();
    }

    public function getOrderForComplete()
    {
        $query = OutboundOrder::find()->andWhere([
            "client_id" => $this->getClientID(),
//            'status' => [
//                Stock::STATUS_OUTBOUND_ACCEPTED,
//            ]
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
    }

    public function isExtraBarcodeInOrder($outboundId,$productBarcode) {
        return OutboundOrderItem::find()->andWhere([
            'outbound_order_id'=>$outboundId,
            'product_barcode'=>$productBarcode,
        ])
        ->andWhere('expected_qty = accepted_qty AND field_extra1 = "" ')->exists();
    }

    public function getOrderItemsForDiffReport($orderID)
//    public function getOrderItemsForDiffReport($pickListID)
    {
        $subQuery = (new Query())
            ->select('count(*)')
            ->from('stock as stck')
            ->andWhere(['stck.status' => Stock::STATUS_OUTBOUND_SCANNED, 'stck.outbound_order_id' => $orderID])
//            ->andWhere(['stck.status' => Stock::STATUS_OUTBOUND_SCANNED, 'stck.outbound_picking_list_id' => $pickListID])
            ->andWhere('stck.product_barcode = stock.product_barcode')
            ->andWhere(["client_id" => $this->getClientID()]);

        return Stock::find()
            ->select(['id', 'outbound_order_id', 'product_barcode', 'box_barcode', 'status', 'primary_address', 'secondary_address', 'product_model', 'field_extra1', 'count(*) as items', 'count_status_scanned' => $subQuery])
            ->andWhere([
                'outbound_order_id' => $orderID,
//                'outbound_picking_list_id' => $pickListID,
                'status' => [
                    Stock::STATUS_OUTBOUND_PICKED,
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_SCANNING
                ],
            ])
            ->andWhere(["client_id" => $this->getClientID()])
            ->groupBy('product_barcode')
            ->orderBy([
                'product_barcode' => SORT_DESC,
                'count_status_scanned' => SORT_DESC,
            ])
            ->asArray()
            ->all();
    }

    public function getQtyBoxesInOrder($orderId)
    {
        return Stock::find()
            ->andWhere([
                'outbound_order_id' => $orderId,
                'status' => [
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
            ])
            ->andWhere(["client_id" => $this->getClientID()])
            ->groupBy('box_barcode')
            ->orderBy('box_barcode')
            ->asArray()
            ->count();
    }

    public function getBoxesInOrder($orderId)
    {
        return Stock::find()
            ->andWhere([
                'outbound_order_id' => $orderId,
                'status' => [
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
            ])
            ->andWhere(["client_id" => $this->getClientID()])
            ->groupBy('box_barcode')
            ->orderBy('box_barcode')
            ->asArray()
            ->all();
    }

    public function acceptedOrder($orderId)
    {
        //One function
        $orderInfo = $this->getOrderInfo($orderId);
        $orderInfo->order->status = Stock::STATUS_OUTBOUND_COMPLETE;

        foreach ($orderInfo->items as $item) {
            $item->status = Stock::STATUS_OUTBOUND_COMPLETE;
            $item->save(false);
        }
        $orderInfo->order->save(false);

        $stocks = Stock::find()->andWhere(["client_id" => $this->getClientID(), 'outbound_order_id' => $orderInfo->order->id])->all();
        foreach ($stocks as $stock) {
            $stock->status = Stock::STATUS_OUTBOUND_COMPLETE;
            $stock->save(false);
        }
    }
	
	

    public function getCountNotScannedProduct($aProductBarcode,$aOutboundOrderId) {
        return Stock::find()->andWhere([
            'product_barcode' => $aProductBarcode,
            'outbound_order_id' => $aOutboundOrderId,
            'status' => [
                Stock::STATUS_OUTBOUND_PICKED,
                Stock::STATUS_OUTBOUND_SCANNING
            ],
            'client_id' => $this->getClientID(),
        ])
            ->count();
    }
	
}