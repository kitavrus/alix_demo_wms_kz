<?php

namespace app\modules\outbound\controllers\repository;

use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;
use Yii;

class ReturnToOrderRepository
{
    /**
     * Снять резерв с конкретной записи stock и пересчитать связанный заказ.
     *
     * @throws \Throwable
     */
    public function unreserve($stockId)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $stock = Stock::findOne($stockId);

            if (!$stock) {
                throw new \Exception('Stock not found');
            }

            if ((int) $stock->outbound_order_id === 0) {
                throw new \Exception('Товар не зарезервирован');
            }

            $orderId = (int) $stock->outbound_order_id;
            $orderItemId = (int) $stock->outbound_order_item_id;

            // 1. Снимем снапшот текущего состояния перед изменениями
            $snapshot = [
                'outbound_order_id' => $stock->outbound_order_id,
                'outbound_order_item_id' => $stock->outbound_order_item_id,
                'outbound_picking_list_id' => $stock->outbound_picking_list_id,
                'outbound_picking_list_barcode' => $stock->outbound_picking_list_barcode,
                'box_barcode' => $stock->box_barcode,
                'box_size_barcode' => $stock->box_size_barcode,
                'box_size_m3' => $stock->box_size_m3,
                'status' => $stock->status,
                'status_availability' => $stock->status_availability,
                'primary_address' => $stock->primary_address,
                'inventory_primary_address' => $stock->inventory_primary_address,
                'unreserved_at' => date('Y-m-d H:i:s'),
                'unreserved_by' => Yii::$app->user->id ?: null,
            ];

            $stock->unreserve_snapshot = json_encode($snapshot);

            // 2. Пересчет Stock: снять резерв и вернуть в исходную ячейку
            $stock->outbound_order_id = 0;
            $stock->outbound_picking_list_id = 0;
            $stock->outbound_picking_list_barcode = '';
            $stock->box_barcode = '';
            $stock->box_size_barcode = '';
            $stock->box_size_m3 = '';
            $stock->box_kg = '';

            $stock->status = Stock::STATUS_INBOUND_NEW;
            $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;

            if (!$stock->save(false)) {
                throw new \Exception('Не удалось сохранить изменения stock');
            }

            // 2. Пересчёт Order items
            $this->recalculateOrderItem($orderItemId);

            // 3. Пересчёт Order
            $this->recalculateOrder($orderId);

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            // Для дебага
            // throw $e
            return false;
        }
    }

    /**
     * Вернуть товар в заказ по данным из unreserve_snapshot.
     *
     * @throws \Throwable
     */
    public function reserveBack($stockId)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $stock = Stock::findOne($stockId);
            if (!$stock) {
                throw new \Exception('Товар не найден');
            }
            if (empty($stock->unreserve_snapshot)) {
                throw new \Exception('Нет снапшота для возврата в заказ');
            }

            $snapshot = json_decode($stock->unreserve_snapshot, true);
            if (!is_array($snapshot) || empty($snapshot['outbound_order_id'])) {
                throw new \Exception('Неверный снапшот');
            }

            $orderId = (int) $snapshot['outbound_order_id'];
            $orderItemId = (int) (isset($snapshot['outbound_order_item_id']) ? $snapshot['outbound_order_item_id'] : 0);

            $stock->outbound_order_id = $orderId;
            $stock->outbound_picking_list_id = (int) (isset($snapshot['outbound_picking_list_id']) ? $snapshot['outbound_picking_list_id'] : 0);
            $stock->outbound_picking_list_barcode = (string) (isset($snapshot['outbound_picking_list_barcode']) ? $snapshot['outbound_picking_list_barcode'] : '');
            $stock->box_barcode = (string) (isset($snapshot['box_barcode']) ? $snapshot['box_barcode'] : '');
            $stock->box_size_barcode = (string) (isset($snapshot['box_size_barcode']) ? $snapshot['box_size_barcode'] : '');
            $stock->box_size_m3 = (string) (isset($snapshot['box_size_m3']) ? $snapshot['box_size_m3'] : '');
            $stock->status = (int)Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
            $stock->status_availability = (int)Stock::STATUS_AVAILABILITY_NO;

            $stock->unreserve_snapshot = null;

            if (!$stock->save(false)) {
                throw new \Exception('Не удалось сохранить stock');
            }

            $this->recalculateOrderItem($orderItemId);
            $this->recalculateOrder($orderId);

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    private function recalculateOrderItem($orderItemId)
    {
        if ($orderItemId === 0) {
            return;
        }

        $count = (int) Stock::find()
            ->where(['outbound_order_item_id' => $orderItemId])
            ->andWhere(['!=', 'outbound_order_id', 0])
            ->count();

        $item = OutboundOrderItem::findOne($orderItemId);
        if (!$item) {
            return;
        }

        $item->accepted_qty = $count;
        $item->save(false);
    }

    private function recalculateOrder($orderId)
    {
        if ($orderId === 0) {
            return;
        }

        $items = OutboundOrderItem::find()
            ->where(['outbound_order_id' => $orderId])
            ->all();

        $accepted = 0;
        $expected = 0;

        foreach ($items as $item) {
            $accepted += (int) $item->accepted_qty;
            $expected += (int) $item->expected_qty;
        }

        $order = OutboundOrder::findOne($orderId);
        if (!$order) {
            return;
        }

        $order->accepted_qty = $accepted;
        $order->expected_qty = $expected;
        $order->save(false);
    }
}