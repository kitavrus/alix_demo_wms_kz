<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:14
 */

namespace stockDepartment\modules\wms\models\miele\repository;


use common\modules\employees\models\Employees;
use common\modules\movement\models\Movement;
use common\modules\movement\models\MovementConstant;
use common\modules\movement\models\MovementItems;
use common\modules\movement\models\MovementPickList;
use common\modules\movement\models\MovementPickListStock;
use common\modules\outbound\models\OutboundOrder;
use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\product\models\Product;
use common\modules\stock\models\Stock;
use yii\data\ActiveDataProvider;

class MovementRepository
{
    public function getOrdersForPrintPickList()
    {
        $query = Movement::find()->andWhere([
            "client_id" => Client::CLIENT_MIELE,
            'status' => [
                MovementConstant::STATUS_NEW,
                MovementConstant::STATUS_PRINT_PICK_LIST,
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
        $order = Movement::find()->andWhere([
            "id" => $id,
            "client_id" => Client::CLIENT_MIELE,

        ])->one();
        $items = MovementItems::find()->andWhere(['movement_id' => $order->id])->all();

        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;

        return $result;
    }

//    public function qtyProductInBox($orderId, $boxBarcode)
//    {
//        return Stock::find()->andWhere([
//            'client_id' => Client::CLIENT_MIELE,
//            'movement_id' => $orderId,
//            'primary_address' => $boxBarcode,
//        ])->count();
//    }

    public function isProduct($barcode)
    {
        return Product::find()->andWhere([
            'client_id' => Client::CLIENT_MIELE,
            'barcode' => $barcode,
        ])->exists();
    }

    public function findOrderByPickList($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        $order = new Movement();
        if ($pikingList) {
            $order = Movement::find()->andWhere([
                'client_id' => Client::CLIENT_MIELE,
                'id' => $pikingList->order_id,
            ])->one();
        }

        return $order;
    }

    public function getEmployeeByBarcode($barcode)
    {
        return Employees::find()->andWhere([
            'barcode' => $barcode
        ])->one();
    }
    // STOCK
//    public function makeMovementProduct($dto)
//    {
//        $stock = Stock::find()->andWhere([
//            'product_barcode' => $dto->productBarcode,
//            'outbound_picking_list_id' => $dto->pickList->id,
//            'status' => [
//                Stock::STATUS_OUTBOUND_PICKED,
//                Stock::STATUS_OUTBOUND_SCANNING
//            ],
//            'client_id' => Client::CLIENT_MIELE,
//        ])->one();
//
//        if ($stock) {
//            $stock->status = Stock::STATUS_OUTBOUND_SCANNED;
//            $stock->box_barcode = $dto->boxBarcode;
//            $stock->scan_out_employee_id = $dto->elmployee->id;
//            $stock->scan_out_datetime = time();
//            $stock->save(false);
//        }
//    }


//    private function makeScannedItem($dto)
//    {
//        $outboundOrderItem = OutboundOrderItem::find()->where(['outbound_order_id' => $dto->order->id,
//            'product_barcode' => $dto->productBarcode,
//        ])->one();
//
//        if ($outboundOrderItem) {
//
//            if (intval($outboundOrderItem->accepted_qty) < 1) {
//                $outboundOrderItem->begin_datetime = time();
//                $outboundOrderItem->status = Stock::STATUS_OUTBOUND_SCANNING;
//            }
//
//            $outboundOrderItem->accepted_qty = $this->getQtyScannedProduct($dto->productBarcode,$dto->order->id);
//
//            if ($outboundOrderItem->accepted_qty == $outboundOrderItem->expected_qty || $outboundOrderItem->accepted_qty == $outboundOrderItem->allocated_qty ) {
//                $outboundOrderItem->status = Stock::STATUS_OUTBOUND_SCANNED;
//            }
//
//            $outboundOrderItem->end_datetime = time();
//            $outboundOrderItem->save(false);
//        }
//    }
//    private function makeScannedOrder($orderId)
//    {
//        $outboundOrder = OutboundOrder::find()
//                         ->andWhere([
//                             'id'=>$orderId,
//                             'client_id' => Client::CLIENT_MIELE
//                         ])->one();
//
//        if(intval($outboundOrder->accepted_qty) < 1) {
//            $outboundOrder->begin_datetime = time();
//            $outboundOrder->status = Stock::STATUS_OUTBOUND_SCANNING;
//        }
//
//        $outboundOrder->accepted_qty = $this->getQtyScanned($orderId);
//
//        if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty || $outboundOrder->accepted_qty == $outboundOrder->allocated_qty ) {
//            $outboundOrder->status = Stock::STATUS_OUTBOUND_SCANNED;
//        }
//
//        $outboundOrder->end_datetime = time();
//        $outboundOrder->save(false);
//    }

    // FUB NUMBER
//    public function makeScannedFab($dto)
//    {
//        $this->makeScannedFubOnStock($dto);
//        $this->makeScannedFubOnItem($dto);
//    }

//    private function makeScannedFubOnStock($dto)
//    {
//        $stock = Stock::find()->andWhere([
//            'product_barcode' => $dto->productBarcode,
//            'outbound_picking_list_id' => $dto->pickList->id,
//            'status' => [
//                Stock::STATUS_OUTBOUND_SCANNED,
//            ],
//            'client_id' => Client::CLIENT_MIELE
//        ])->one();
//
//        if ($stock) {
//            $stock->field_extra1 = $dto->fabBarcode;
//            $stock->save(false);
//        }
//
//    }
//    private function makeScannedFubOnItem($dto)
//    {
//        $outboundOrderItem = OutboundOrderItem::find()->andWhere(['outbound_order_id' => $dto->order->id,
//            'product_barcode' => $dto->productBarcode,
//        ])->one();
//
//        if ($outboundOrderItem) {
//            $outboundOrderItem->field_extra1 = $dto->fabBarcode;
//            $outboundOrderItem->save(false);
//        }
//    }

    // PRINT BOX LABEL
//    public function makePrintBoxLabel($dto) {
//        $this->makePrintBoxOnStock($dto);
//        $this->makePrintBoxOnItem($dto);
//        $this->makePrintBoxOnOrder($dto);
//    }

//    private function makePrintBoxOnStock($dto) {
//        $stocks = Stock::find()->andWhere([
//            'client_id' => Client::CLIENT_MIELE,
//            'outbound_picking_list_id' => $dto->pickList->id,
//            'status' => [
//                Stock::STATUS_OUTBOUND_SCANNED,
//                Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
//                Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
//            ]
//        ])->all();
//
//        if ($stocks) {
//           foreach($stocks as $stock) {
//               $stock->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
//               $stock->save(false);
//           }
//        }
//    }
//    private function makePrintBoxOnItem($dto)
//    {
//        $outboundOrderItems = OutboundOrderItem::find()
//            ->andWhere(['outbound_order_id' => $dto->order->id,])
//            ->andWhere('accepted_qty > 0')
//            ->all();
//
//        if ($outboundOrderItems) {
//            foreach ($outboundOrderItems as $item) {
//                $item->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
//                $item->save(false);
//            }
//        }
//    }
//    private function makePrintBoxOnOrder($dto)
//    {
//        $outboundOrder = Movement::find()
//            ->andWhere([
//                'id'=>$dto->order->id,
//                'client_id' => Client::CLIENT_MIELE
//            ])->one();
//        if($outboundOrder) {
//            $outboundOrder->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;;
//            $outboundOrder->save(false);
//        }
//    }

    // CLEAR BOX
//    public function clearBox($dto) {
//        $this->makeClearBoxStock($dto->productBarcode, $dto->boxBarcode, $dto->order->id);
//        $this->recalculateOrderItems($dto->productBarcode,$dto->order->id);
//        $this->makeScannedOrder($dto->order->id);
//    }

//    private function makeClearBoxStock($productBarcode,$boxBarcode,$orderId) {
//
//        $stocks = $this->getProductInBox($productBarcode,$boxBarcode,$orderId);
//
//        if ($stocks) {
//            foreach($stocks as $stock) {
//                $stock->box_barcode = '';
//                $stock->status = Stock::STATUS_OUTBOUND_PICKED;
//                $stock->save(false);
//            }
//        }
//    }
//    private function recalculateOrderItems($productBarcode,$orderId) {
//        $outboundOrderItems = OutboundOrderItem::find()
//            ->andWhere(['outbound_order_id' => $orderId])
//            ->andWhere(['product_barcode' => $productBarcode])
//            ->all();
//
//        if ($outboundOrderItems) {
//            foreach ($outboundOrderItems as $item) {
//                $item->accepted_qty = $this->getQtyScannedProduct($productBarcode,$orderId);
//                $item->save(false);
//            }
//        }
//    }
//    private function getProductInBox($productBarcode,$boxBarcode,$orderId ) {
//        return Stock::find()->andWhere([
//            'product_barcode'=>$productBarcode,
//            'box_barcode'=>$boxBarcode,
//            'outbound_order_id' => $orderId,
//            'status'=>Stock::STATUS_OUTBOUND_SCANNED,
//            'client_id' => Client::CLIENT_MIELE,
//        ])->all();
//    }


//    private function getQtyScannedProduct($productBarcode,$orderId) {
//        return Stock::find()->andWhere([
//            'product_barcode'=>$productBarcode,
//            'outbound_order_id' => $orderId,
//            'status'=>Stock::STATUS_OUTBOUND_SCANNED,
//            'client_id' => Client::CLIENT_MIELE,
//        ])->count();
//    }
//    private function getQtyScanned($orderId) {
//        return Stock::find()->andWhere([
//            'outbound_order_id' => $orderId,
//            'status'=>Stock::STATUS_OUTBOUND_SCANNED,
//            'client_id' => Client::CLIENT_MIELE,
//        ])->count();
//    }

    //---------------------------------- DONE
    public function getStockIDsByMovementID($orderID)
    {
        return MovementPickListStock::find()->select('stock_id')->andWhere([
            'movement_id' => $orderID,
//            'movement_pick_id' => $orderID,
        ])->column();
    }

    public function isPickListExist($pickListBarcode)
    {
        return MovementPickList::find()
            ->andWhere([
                'client_id' => Client::CLIENT_MIELE,
                'barcode' => $pickListBarcode,
            ])
            ->exists();
    }

    public function isProductInMovementOrder($pickListBarcode, $productBarcode)
    {

        $pickList = $this->getPickListByBarcode($pickListBarcode);

        return Stock::find()->andWhere([
            'id' => $this->getStockIDsByMovementID($pickList->id),
            'product_barcode' => $productBarcode,
            'client_id' => Client::CLIENT_MIELE,
        ])->exists();
    }

    public function getPickListByBarcode($pickListBarcode)
    {
        return MovementPickList::find()->andWhere([
            'client_id' => Client::CLIENT_MIELE,
            'barcode' => $pickListBarcode,
        ])->one();
    }

    public function isProductAndFabInMovementOrder($pickListBarcode, $productBarcode, $fabBarcode)
    {
        $pickList = $this->getPickListByBarcode($pickListBarcode);

        return Stock::find()->andWhere([
            'id' => $this->getStockIDsByMovementID($pickList->id),
            'product_barcode' => $productBarcode,
            'field_extra1' => $fabBarcode,
            'client_id' => Client::CLIENT_MIELE,
        ])->exists();
    }

    public function moveToAddress($dto)
    {
        if(empty($dto->fabBarcode)) {
            $stock = Stock::find()->andWhere([
                'id' => $this->getStockIDsByMovementID($dto->pickList->id),
                'product_barcode' => $dto->productBarcode,
                'client_id' => Client::CLIENT_MIELE,
                'field_extra1' => ""
            ])->one();
        } else {
            $stock = Stock::find()->andWhere([
                'id' => $this->getStockIDsByMovementID($dto->pickList->id),
                'product_barcode' => $dto->productBarcode,
                'client_id' => Client::CLIENT_MIELE,
                'field_extra1' => $dto->fabBarcode
            ])->one();
        }

        if ($stock) {
            $stock->primary_address = $dto->boxBarcode;
            $stock->secondary_address = $dto->addressBarcode;
            $stock->save(false);

            $this->setStatusScannedOnPickListStock($stock->id, $dto->order->id);
            $this->moveToAddressOnItem($dto);
            $this->moveToAddressOnOrder($dto);
        }
    }

    private function setStatusScannedOnPickListStock($stockId, $orderId)
    {
        $movementPickListStock = MovementPickListStock::find()->andWhere([
            'status' => MovementConstant::STATUS_PICK_LIST_STOCK_NEW,
            'stock_id' => $stockId,
            'movement_id' => $orderId,
        ])->one();

        if ($movementPickListStock) {
            $movementPickListStock->status = MovementConstant::STATUS_PICK_LIST_STOCK_SCANNED;
            $movementPickListStock->save(false);
        }
    }

    private function moveToAddressOnItem($dto)
    {
        $item = MovementItems::find()
            ->andWhere([
                'movement_id' => $dto->order->id,
                'product_barcode' => $dto->productBarcode
            ])->andFilterWhere(['field_extra1' => $dto->fabBarcode])
            ->one();

        if ($item) {
            $item->accepted_qty += 1;
            $item->status = MovementConstant::STATUS_IN_WORKING;
            $item->save(false);
        }
    }

    private function moveToAddressOnOrder($dto)
    {
        $order = Movement::find()
            ->andWhere([
                'id' => $dto->order->id,
                'client_id' => Client::CLIENT_MIELE
            ])->one();
        if ($order) {
            $order->status = MovementConstant::STATUS_IN_WORKING;
            $order->accepted_qty = $this->getScannedQtyInOrder($dto);
            $order->save(false);
        }
    }

    public function getScannedQtyInOrder($dto)
    {
        return MovementPickListStock::find()->andWhere([
            'product_barcode' => $dto->productBarcode,
            'movement_id' => $dto->order->id,
            'status' => MovementConstant::STATUS_PICK_LIST_STOCK_SCANNED,
        ])->count();
    }

    public function acceptedOrder($orderId)
    {
        //One function
        $orderInfo = $this->getOrderInfo($orderId);
        $orderInfo->order->status = MovementConstant::STATUS_COMPLETE;

        foreach ($orderInfo->items as $item) {
            $item->status = MovementConstant::STATUS_COMPLETE;
            $item->save(false);
        }
        $orderInfo->order->save(false);

        $pickListStocks = MovementPickListStock::find()->andWhere([
            "client_id" => Client::CLIENT_MIELE,
            "status" => MovementConstant::STATUS_PICK_LIST_STOCK_SCANNED,
            'movement_id' => $orderInfo->order->id
        ])->all();
        foreach ($pickListStocks as $stock) {
            $stock->status = MovementConstant::STATUS_PICK_LIST_STOCK_COMPLETE;
            $stock->save(false);
        }
    }

    public function getOrderItemsForDiffReport($orderId) {

        $orderInfo = $this->getOrderInfo($orderId);

        $result = new \stdClass();
        $result->items = [];
        foreach($orderInfo->items as $movementItem) {
                if($movementItem->expected_qty != $movementItem->accepted_qty) {
                    $resultItem = new \stdClass();
                    $resultItem->productBarcode = $movementItem->product_barcode;
                    $resultItem->expectedQty = $movementItem->expected_qty;
                    $resultItem->acceptedQty = $movementItem->accepted_qty;
                    $resultItem->fabBarcode = $movementItem->field_extra1;
                    $resultItem->scannedItems = [];

                    $pickListStocks = MovementPickListStock::find()->andWhere([
                        'product_barcode' => $resultItem->productBarcode,
                        'movement_id' => $orderInfo->order->id,
//                        "client_id" => Client::CLIENT_MIELE,
//                        "status" => MovementConstant::STATUS_PICK_LIST_STOCK_SCANNED,
                    ])->all();
                    foreach($pickListStocks as $pickListStock) {

                        if($resultItem->fabBarcode) {
                            $stock = Stock::find()->andWhere(['id'=>$pickListStock->stock_id])->andWhere(['field_extra2'=>$resultItem->fabBarcode])->one();
                        } else {
                            $stock = Stock::find()->andWhere(['id'=>$pickListStock->stock_id])->andWhere(['field_extra2'=>""])->one();
                        }

                        if($stock && $pickListStock->address == $stock->secondary_address) {
                            $item = new \stdClass();
                            $item->oldAddress = $pickListStock->address;
                            $item->newAddress = $stock->secondary_address;
                            $item->isScanned = $pickListStock->status == MovementConstant::STATUS_PICK_LIST_STOCK_SCANNED;
                            $resultItem->scannedItems [] = $item;
                        }
                    }
                    $result->items[] = $resultItem;
                }
        }

        return $result;
    }

    public function isExtraBarcodeInOrder($movementId,$productBarcode) {
        return MovementItems::find()->andWhere([
            'movement_id'=>$movementId,
            'product_barcode'=>$productBarcode,
        ])
            ->andWhere('expected_qty = accepted_qty AND field_extra1 = "" ')->exists();
    }

    public function isNextBarcodeWithFabInOrder($movementId,$productBarcode) {
        return MovementItems::find()->andWhere([
            'movement_id'=>$movementId,
            'product_barcode'=>$productBarcode,
        ])
            ->andWhere('expected_qty != accepted_qty AND field_extra1 != "" ')->exists();
    }
}