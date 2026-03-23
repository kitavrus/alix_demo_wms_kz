<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */

namespace common\clientObject\erenRetail\inbound\repository;

use common\modules\dataMatrix\models\InboundDataMatrix;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\stock\models\Stock;
use common\modules\stock\service\Service;
use yii\db\Expression;

class InboundRepository
{
    private $inboundOrderID;
    private $clientId;

    /**
     * InboundRepository constructor.
     * @param $inboundOrderID
     */
    public function __construct($dto = [])
    {
        $this->clientId = isset($dto->clientId) ? $dto->clientId : 0;
    }
    //
    public function getClientID()
    {
        return $this->clientId;
    }
    //
    public function create($data)
    {
        $orderID = $this->createOrder($data);
        $this->createOrderItems($data, $orderID);

        $this->setInboundOrderID($orderID);
        return $orderID;
    }
    //
    //
    public function isOrderExist($orderNumber)
    {
        return InboundOrder::find()->andWhere(['client_id' => $this->getClientID(),'order_number' => $orderNumber])
            //->andWhere(' created_at > 1546300861') // GMT: Tuesday, 1 January 2019 г., 0:01:01
            ->andWhere(' created_at > 1577836861')
            ->exists();
    }
    //
    private function createOrder($data)
    {
        $inboundOrder = new InboundOrder();
        $inboundOrder->client_id = $this->getClientID();
        $inboundOrder->order_number = $data->orderNumber;
        $inboundOrder->supplier_id = $data->supplierId;
		$inboundOrder->order_type = $data->order_type; //  InboundOrder::ORDER_TYPE_INBOUND;
        $inboundOrder->status = Stock::STATUS_INBOUND_NEW;
        $inboundOrder->cargo_status = InboundOrder::CARGO_STATUS_NEW;
        $inboundOrder->expected_qty = $data->expectedTotalProductQty;
        $inboundOrder->accepted_qty = 0;
        $inboundOrder->accepted_number_places_qty = $data->expectedTotalPlaceQty;
        $inboundOrder->expected_number_places_qty = 0;
        $inboundOrder->comments = $data->comment;
        $inboundOrder->save(false);

        return $inboundOrder->id;
    }
    //
    private function createOrderItems($data, $orderId)
    {
        foreach ($data->items as $item) {
            $inboundOrderItem = new InboundOrderItem();
            $inboundOrderItem->inbound_order_id = $orderId;
            $inboundOrderItem->product_name = $item->productName;
            $inboundOrderItem->product_model = $item->productModel;
            $inboundOrderItem->product_barcode = $item->productBarcode;
			$inboundOrderItem->product_size = $item->productSize;
            $inboundOrderItem->expected_qty = $item->expectedProductQty;
            $inboundOrderItem->expected_number_places_qty = $item->expectedPlaceQty;
            $inboundOrderItem->save(false);


            if ( count($item->dataMatrix) != 0) {
				foreach ($item->dataMatrix as $dm) {
					if (empty($dm)) {
						continue;
					}
					$inboundDataMatrix = new InboundDataMatrix();
					$inboundDataMatrix->inbound_id = $orderId;
					$inboundDataMatrix->inbound_item_id = $inboundOrderItem->id;
					$inboundDataMatrix->product_barcode = $item->productBarcode;
					$inboundDataMatrix->product_model = $item->productModel;
					$inboundDataMatrix->data_matrix_code = $dm;
					$inboundDataMatrix->save(false);
				}
			}
        }
    }
    //
    public function isProductModelBarcodeExistInOrder($productModelBarcode, $inboundOrderID)
    {
        return InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $inboundOrderID,
            'product_model' => $productModelBarcode,
        ])->exists();
    }
    //
    public function isProductBarcodeExistInOrder($productBarcode, $inboundOrderID)
    {
        return InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $inboundOrderID,
            'product_barcode' => $productBarcode,
        ])->exists();
    }
    //
    public function isExtraBarcodeInOrder($productBarcode, $inboundOrderID)
    {
        return InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $inboundOrderID,
            'product_barcode' => $productBarcode,
        ])->andWhere('expected_qty = accepted_qty AND expected_qty != 0')->exists();
    }
    //
    public function getNewAndInProcessOrder()
    {
        return InboundOrder::find()
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere('status != :status', [':status' => Stock::STATUS_INBOUND_COMPLETE])
            ->asArray()
            ->all();
    }
    //
    public function getQtyInOrder($id)
    {
        $inboundOrder = InboundOrder::find()->select('expected_qty, accepted_qty')
            ->andWhere(['id' => $id, 'client_id' => $this->getClientID()])
            ->one();

        $dto = new \stdClass();
        $dto->expected_qty = -1;
        $dto->accepted_qty = -1;

        if ($inboundOrder != null) {
            $dto->expected_qty = $inboundOrder->expected_qty;
            $dto->accepted_qty = $inboundOrder->accepted_qty;
        }
        return $dto;
    }
    //
    public function getQtyModelsInOrder($inboundOrderID, $productModel)
    {
        $inboundOrderItem = InboundOrderItem::find()->select('expected_qty, accepted_qty')
            ->andWhere(['product_model' => $productModel, 'inbound_order_id' => $inboundOrderID])
            ->one();

        $dto = new \stdClass();
        $dto->expected_qty = -1;
        $dto->accepted_qty = -1;

        if ($inboundOrderItem != null) {
            $dto->expected_qty = $inboundOrderItem->expected_qty;
            $dto->accepted_qty = $inboundOrderItem->accepted_qty;
        }
        return $dto;
    }
    //
    public function addScannedProductToStock($dto)
    {
        $stock = new Stock();
        $stock->client_id = $this->getClientID();
        $stock->inbound_order_id = $dto->orderNumberId;
        $stock->product_barcode = $dto->productBarcode;
        $stock->product_model = $dto->productModel;
        $stock->primary_address = $dto->transportedBoxBarcode;
        $stock->status = Stock::STATUS_INBOUND_SCANNED;
        $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
        $stock->scan_in_datetime = time();
        $stock->save(false);

        $inboundItemID = $this->updateAcceptedQtyItemByProductBarcode($dto->orderNumberId, $dto->productBarcode);
        $stock->inbound_order_item_id = $inboundItemID;
        $stock->save(false);

        return $stock->id;
    }

    //
    public function updateAcceptedQtyItemByProductBarcode($inboundId, $productBarcode)
    {
        $inboundItem = InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $inboundId,
            'product_barcode' => $productBarcode,
        ])->one();

        if ($inboundItem) {
            $inboundItem->accepted_qty = $this->getScannedProductQtyByOrderInStock($inboundId, $productBarcode);
            $inboundItem->save(false);
            return $inboundItem->id;
        }
        return -1;
    }

    //
    public function updateAcceptedQtyItemByProductModelBarcode($inboundId, $productModelBarcode)
    {
        $inboundItem = InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $inboundId,
            'product_model' => $productModelBarcode,
        ])->one();

        if ($inboundItem) {
            $inboundItem->accepted_qty = $this->getScannedProductQtyByModelOrderInStock($inboundId, $productModelBarcode);
            $inboundItem->save(false);
            return $inboundItem->id;
        }
        return -1;
    }

    //
    private function getScannedProductQtyByOrderInStock($inboundId, $productBarcode)
    {
        return Stock::find()->andWhere([
            'inbound_order_id' => $inboundId,
            'product_barcode' => $productBarcode,
            'status' => Stock::STATUS_INBOUND_SCANNED,
        ])->count();
    }

    //
    private function getScannedProductQtyByModelOrderInStock($inboundId, $productModelBarcode)
    {
        return Stock::find()->andWhere([
            'inbound_order_id' => $inboundId,
            'product_model' => $productModelBarcode,
            'status' => Stock::STATUS_INBOUND_SCANNED,
        ])->count();
    }

    //
    public function updateQtyScannedInOrder($orderId, $acceptedQty)
    {
        $inbound = InboundOrder::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
        if ($inbound) {
            $inbound->accepted_qty = $acceptedQty;
            $inbound->save(false);
        }
    }

    //
    public function setOrderStatusInProcess($orderId)
    {
        $inbound = InboundOrder::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
        if ($inbound) {
            $inbound->status = Stock::STATUS_INBOUND_SCANNING;
            $inbound->save(false);
        }
    }

    //
    public function setOrderStatusClose($orderId)
    {
        $inbound = InboundOrder::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
        if ($inbound) {
            $inbound->status = Stock::STATUS_INBOUND_CLOSE;
            $inbound->save(false);
        }
    }

    //
    public function setOrderItemStatusClose($orderId)
    {

        $inboundItem = InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $orderId,
        ])->one();

        if ($inboundItem) {
            $inboundItem->status = Stock::STATUS_INBOUND_CLOSE;
            $inboundItem->save(false);
        }
    }

    public function setDateConfirm($orderId)
    {
        $inbound = InboundOrder::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
        if ($inbound) {
            $inbound->date_confirm = time();
            $inbound->save(false);
        }
    }

    //
    public function setOrderItemStatusInProcess($orderId, $productBarcode)
    {

        $inboundItem = InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $orderId,
            'product_barcode' => $productBarcode,
        ])->one();

        if ($inboundItem) {
            $inboundItem->status = Stock::STATUS_INBOUND_SCANNING;
            $inboundItem->save(false);

        }
    }

    //
    public function getItemByProductBarcode($inboundId, $productBarcode)
    {
        $inboundItem = InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $inboundId,
            'product_barcode' => $productBarcode,
        ])->one();

        if ($inboundItem) {
            return $inboundItem->id;
        }
        return -1;
    }

    //
    public function getItemsByOrderId($inboundOrderId)
    {
        return InboundOrderItem::find()->select('*,(expected_qty - accepted_qty) as order_by')
            ->andWhere(['inbound_order_id' => $inboundOrderId])
            ->orderBy(new Expression('order_by != 0 DESC'))
            ->all();
    }

    //
    public function getItemsForDiffReportByOrderId($inboundOrderId)
    {
        return InboundOrderItem::find()->select('*,(expected_qty - accepted_qty) as order_by')
            ->andWhere(['inbound_order_id' => $inboundOrderId])
            ->orderBy(new Expression('box_barcode,order_by != 0 DESC'))
            ->asArray()
            ->all();
    }

    //
    public function setProductBarcodeToItemByProductModel($productBarcode, $inboundId, $productModel)
    {
        $inboundItem = InboundOrderItem::find()->andWhere([
            'inbound_order_id' => $inboundId,
            'product_model' => $productModel,
        ])->one();

        if ($inboundItem) {
            $inboundItem->product_barcode = $productBarcode;
            $inboundItem->save(false);
        }
    }

    //
    public function closeOrder($inboundOrderId)
    {

    }

    public function getOrderInfo($id)
    {
        $order = InboundOrder::find()->andWhere([
            "id" => $id,
        ])->one();
        $items = InboundOrderItem::find()->andWhere(['inbound_order_id' => $order->id])->all();

        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;

        return $result;
    }

    public function delete($orderId) {

        $inboundService = new \common\modules\inbound\service\Service();
        $inboundService->delete($orderId);

        $stockService = new Service();
        $stockService->deleteByInboundId($orderId);
    }

    /**
     * @return mixed
     */
    public function getInboundOrderID()
    {
        return $this->inboundOrderID;
    }

    /**
     * @param mixed $inboundOrderID
     */
    public function setInboundOrderID($inboundOrderID)
    {
        $this->inboundOrderID = $inboundOrderID;
    }
}