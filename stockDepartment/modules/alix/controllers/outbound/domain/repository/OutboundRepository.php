<?php

namespace stockDepartment\modules\alix\controllers\outbound\domain\repository;

use stockDepartment\modules\alix\controllers\ecommerce\employee\domain\repository\EmployeeRepository;
use stockDepartment\modules\alix\controllers\outbound\domain\constants\OutboundStatus;
use stockDepartment\modules\alix\controllers\outbound\domain\dto\OrderInfoDTO;
use stockDepartment\modules\alix\controllers\outbound\domain\entities\EcommerceOutbound;
use stockDepartment\modules\alix\controllers\outbound\domain\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceStock;
use common\helpers\DateHelper;
use common\modules\stock\models\Stock;
use stockDepartment\modules\alix\controllers\product\domains\ProductService;
use yii\data\ActiveDataProvider;
use stockDepartment\modules\alix\controllers\outbound\domain\constants\OutboundSource;

/**
 * Все чтения/записи склада выполняются через `ecommerce_stock`.
 * Класс `Stock` подключается только ради констант статусов (STATUS_OUTBOUND_*,
 * STATUS_AVAILABILITY_*) — они шарятся между legacy и новой таблицей.
 */
class OutboundRepository
{
    public function getClientID()
    {
        return 103;
    }

    public function getOrdersForPrintPickList()
    {
        $query = EcommerceOutbound::find()->andWhere([
            'client_id' => $this->getClientID(),
            'status'    => OutboundStatus::getOrdersForPrintPickList(),
        ]);

        return new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 20],
            'sort'       => ['defaultOrder' => ['created_at' => SORT_ASC]],
        ]);
    }

    public function canPrintPickingList($ourOutboundId)
    {
        return EcommerceOutbound::find()->andWhere([
            'id'        => $ourOutboundId,
            'status'    => OutboundStatus::getOrdersForPrintPickList(),
            'client_id' => $this->getClientID(),
        ])->exists();
    }

    public function getOrderInfo($id)
    {
        $order = EcommerceOutbound::find()->andWhere([
            'id'        => $id,
            'client_id' => $this->getClientID(),
        ])->one();

        $items = EcommerceOutboundItem::find()->andWhere(['outbound_id' => $order->id])->all();
        $stocks = EcommerceStock::find()
            ->andWhere(['outbound_id' => $order->id])
            ->andWhere(['deleted' => 0])
            ->all();

        $result = new OrderInfoDTO();
        $result->order = $order;
        $result->items = $items;
        $result->stocks = $stocks;
        $result->outboundBoxBarcode = EcommerceStock::find()
            ->select('box_barcode')
            ->andWhere(['outbound_id' => $id])
            ->andWhere(['deleted' => 0])
            ->andWhere(['not', ['box_barcode' => null]])
            ->andWhere(['not', ['box_barcode' => '']])
            ->scalar();

        return $result;
    }

    public function getStockBeforePrintPickingList($orderIdList)
    {
        return EcommerceStock::find()
            ->select("created_at,
                outbound_id AS ecom_outbound_id,
                place_address_sort1 AS address_sort_order,
                product_barcode,
                product_id,
                place_address_barcode AS primary_address,
                box_address_barcode AS secondary_address,
                product_model,
                product_name,
                count(*) as productQty")
            ->andWhere(['outbound_id' => $orderIdList])
            ->andWhere(['deleted' => 0])
            ->groupBy('product_barcode, place_address_barcode, outbound_id')
            ->asArray()
            ->all();
    }

    public function getOrderByID($id)
    {
        return EcommerceOutbound::find()->andWhere([
            'id'        => $id,
            'client_id' => $this->getClientID(),
        ])->one();
    }

    public function getPickListByBarcode($pickList)
    {
        $pickList = trim($pickList);
        $order = EcommerceOutbound::find()->andWhere([
            'order_number' => $pickList,
            'client_id'    => $this->getClientID(),
        ])->one();
        return [
            'id'          => $order->id,
            'orderNumber' => $order->order_number,
        ];
    }

    public function changeStatusOfOutOfStock($orderId)
    {
        return EcommerceOutbound::updateAll([
            'status' => OutboundStatus::getOutOfStock(),
        ], [
            'id' => $orderId,
        ]);
    }

    public function isOrderExistByPickingBarcode($id, $orderNumber = '')
    {
        return EcommerceOutbound::find()->andWhere([
            'id' => $id,
        ])->exists();
    }

    public function isOrderReserved($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        return EcommerceStock::find()
            ->andWhere(['outbound_id' => $pikingList['id']])
            ->andWhere(['deleted' => 0])
            ->exists();
    }

    public function isNotDoneOrder($id, $orderNumber = '')
    {
        return EcommerceOutbound::find()->andWhere([
            'id'     => $id,
            'status' => OutboundStatus::getNotDoneOrders(),
        ])->exists();
    }

    public function isCancelOrder($id)
    {
        return EcommerceOutbound::find()->andWhere([
            'id'                  => $id,
            'client_CancelReason' => OutboundStatus::getCANCEL(),
        ])->exists();
    }

    public function canSendByAPI($id)
    {
        return EcommerceOutbound::find()->andWhere([
            'id'                    => $id,
            'client_ShipmentSource' => OutboundSource::getCRM(),
        ])->exists();
    }

    public function getEmployeeByBarcode($barcode)
    {
        return EmployeeRepository::getEmployeeByBarcode($barcode);
    }

    public function findOrderByPickList($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);
        $outbound = new EcommerceOutbound();
        if ($pikingList) {
            $outbound = EcommerceOutbound::find()->andWhere([
                'id' => $pikingList['id'],
            ])->one();
        }
        return $outbound;
    }

    public function usePackageBarcodeInOtherOrder($pickList, $packageBarcode)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        return EcommerceStock::find()
            ->andWhere(['not', ['outbound_id' => (int) $pikingList['id']]])
            ->andWhere(['box_barcode' => $packageBarcode])
            ->andWhere(['deleted' => 0])
            ->exists();
    }

    public function qtyProductInPackage($pickList, $packageBarcode)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        return EcommerceStock::find()
            ->andWhere([
                'outbound_id' => $pikingList['id'],
                'box_barcode' => $packageBarcode,
                'deleted'     => 0,
            ])
            ->count();
    }

    public function showOrderItems($pickList)
    {
        $pikingList = $this->getPickListByBarcode($pickList);

        return EcommerceOutboundItem::find()
            ->andWhere(['outbound_id' => $pikingList['id']])
            ->asArray()
            ->all();
    }

    public function isProductExistInOrder($outboundOrderID, $productBarcode)
    {
        return EcommerceOutboundItem::find()->andWhere([
            'outbound_id' => $outboundOrderID,
            'product_sku' => (new ProductService())->getGuidIdByBarcode($productBarcode),
        ])->exists();
    }

    public function isExtraBarcodeInOrder($outboundId, $productBarcode)
    {
        return EcommerceOutboundItem::find()->andWhere([
            'outbound_id' => $outboundId,
            'product_sku' => (new ProductService())->getGuidIdByBarcode($productBarcode),
        ])->andWhere('expected_qty = accepted_qty')->exists();
    }

    public function makeScannedProduct($dto)
    {
        $stock = $this->makeScannedStock($dto);
        $this->makeScannedItem($dto);
        $this->makeScannedOrder($dto->order->id);
        return $stock;
    }

    private function makeScannedStock($dto)
    {
        $stock = EcommerceStock::find()->andWhere([
            'product_sku'  => (new ProductService())->getGuidIdByBarcode($dto->productBarcode),
            'outbound_id'  => $dto->order->id,
            'status'       => OutboundStatus::getReadyForScanning(),
            'client_id'    => $this->getClientID(),
            'deleted'      => 0,
        ])->one();

        if ($stock) {
            $stock->status               = Stock::STATUS_OUTBOUND_SCANNED;
            $stock->box_barcode          = $dto->packageBarcode;
            $stock->scan_out_employee_id = $dto->employee->id;
            $stock->scan_out_datetime    = time();
            $stock->save(false);
        }
        return $stock;
    }

    public function makeScannedStockQRCode($dto)
    {
        $stock = EcommerceStock::findOne((int) $dto->stockId);
        if ($stock) {
            $stock->product_qrcode = $dto->productQRCode;
            $stock->save(false);
        }
    }

    private function makeScannedItem($dto)
    {
        $outboundOrderItem = $this->getOrderItemByProductBarcode($dto->order->id, $dto->productBarcode);
        if ($outboundOrderItem) {
            if (intval($outboundOrderItem->accepted_qty) < 1) {
                $outboundOrderItem->begin_datetime = time();
                $outboundOrderItem->status         = OutboundStatus::getSCANNING();
            }

            $outboundOrderItem->accepted_qty = $this->getQtyScannedProduct($dto->productBarcode, $dto->order->id);

            if ($outboundOrderItem->accepted_qty == $outboundOrderItem->expected_qty
                || $outboundOrderItem->accepted_qty == $outboundOrderItem->allocated_qty) {
                $outboundOrderItem->status = OutboundStatus::getSCANNED();
            }

            $outboundOrderItem->end_datetime = time();
            $outboundOrderItem->save(false);
        }
    }

    private function makeScannedOrder($orderId)
    {
        $outboundOrder = EcommerceOutbound::find()->andWhere([
            'id'        => $orderId,
            'client_id' => $this->getClientID(),
        ])->one();

        if (intval($outboundOrder->accepted_qty) < 1) {
            $outboundOrder->begin_datetime = time();
            $outboundOrder->status         = OutboundStatus::getSCANNING();
        }

        $outboundOrder->accepted_qty = $this->getQtyScanned($orderId);

        if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty
            || $outboundOrder->accepted_qty == $outboundOrder->allocated_qty) {
            $outboundOrder->status = OutboundStatus::getSCANNED();
        }

        $outboundOrder->end_datetime = time();
        $outboundOrder->save(false);
    }

    private function getQtyScannedProduct($productBarcode, $orderId)
    {
        return EcommerceStock::find()->andWhere([
            'product_sku' => (new ProductService())->getGuidIdByBarcode($productBarcode),
            'outbound_id' => $orderId,
            'status'      => OutboundStatus::getSCANNED(),
            'deleted'     => 0,
        ])->count();
    }

    private function getQtyScanned($orderId)
    {
        return EcommerceStock::find()->andWhere([
            'outbound_id' => $orderId,
            'status'      => OutboundStatus::getSCANNED(),
            'deleted'     => 0,
        ])->count();
    }

    public function emptyPackage($dto)
    {
        $stocks = EcommerceStock::find()->andWhere([
            'outbound_id' => $dto->order->id,
            'box_barcode' => $dto->packageBarcode,
            'status'      => OutboundStatus::getSCANNED(),
            'deleted'     => 0,
        ])->all();

        foreach ($stocks as $stock) {
            $stock->box_barcode    = '';
            $stock->product_qrcode = '';
            $stock->status         = OutboundStatus::getPRINTED_PICKING_LIST();
            $stock->save(false);

            $outboundItem = $this->getOrderItemByProductBarcode($dto->order->id, $stock->product_barcode);
            if ($outboundItem) {
                $outboundItem->accepted_qty = $this->getQtyScannedProduct($stock->product_barcode, $dto->order->id);
                $outboundItem->save(false);
            }
        }
        $this->makeScannedOrder($dto->order->id);
    }

    public function getOrderItemByProductBarcode($outboundId, $productBarcode)
    {
        return EcommerceOutboundItem::find()->andWhere([
            'outbound_id' => $outboundId,
            'product_sku' => (new ProductService())->getGuidIdByBarcode($productBarcode),
        ])->one();
    }

    public function getEcommerceStockByOutboundAndBarcode($outboundId, $productBarcode)
    {
        return EcommerceStock::find()->andWhere([
            'outbound_id'     => $outboundId,
            'product_barcode' => $productBarcode,
            'deleted'         => 0,
        ])->one();
    }

    public function isExistsProductQRCode($productQRCode)
    {
        return EcommerceStock::find()
            ->andWhere(['product_qrcode' => $productQRCode])
            ->andWhere(['deleted' => 0])
            ->exists();
    }

    public function packageOrder($orderId)
    {
        $outboundOrder = EcommerceOutbound::find()->andWhere([
            'id'        => $orderId,
            'client_id' => $this->getClientID(),
        ])->one();
        if ($outboundOrder) {
            $outboundOrder->status             = OutboundStatus::getPACKED();
            $outboundOrder->place_accepted_qty = $this->getQtyBoxesInOrder($orderId);
            $outboundOrder->packing_date       = DateHelper::getTimestamp();
            $outboundOrder->save(false);
        }

        return $outboundOrder;
    }

    public function getQtyBoxesInOrder($orderId)
    {
        return EcommerceStock::find()
            ->andWhere([
                'outbound_id' => $orderId,
                'status'      => OutboundStatus::getPrintBoxOnStock(),
                'client_id'   => $this->getClientID(),
                'deleted'     => 0,
            ])
            ->groupBy('box_barcode')
            ->orderBy('box_barcode')
            ->asArray()
            ->count();
    }

    public function getOrderByOrderNumber($orderNumber)
    {
        return EcommerceOutbound::find()->andWhere([
            'order_number' => trim($orderNumber),
            'client_id'    => $this->getClientID(),
        ])->one();
    }
}
