<?php

namespace stockDepartment\modules\kaspi\services;

use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceStock;
use stockDepartment\modules\kaspi\enums\OrderStatus;
use Yii;
use yii\base\Component;

/**
 * Синхронизация статусов активных Kaspi-заказов.
 *
 * По активным EcommerceOutbound (external_order_number != '' и не в финальных
 * статусах) читает актуальный статус из Kaspi и реагирует:
 *   - CANCELLING / CANCELLED  → вернуть зарезервированный сток в YES (returnToStock)
 *   - COMPLETED               → пометить заказ к передаче в 1С (one_c_status = PENDING)
 *
 * Не запускает финальные действия (передача в 1С — отдельный крон).
 */
class OrderStatusSyncService extends Component
{
    /** @var KaspiAPIService|null */
    public $api;

    /** @var OrderReturnService|null */
    public $returnService;

    public function init()
    {
        parent::init();
        $module = Yii::$app->getModule('kaspi');
        if ($this->api === null && $module !== null) {
            $this->api = $module->get('apiService');
        }
        if (!$this->api instanceof KaspiAPIService) {
            $this->api = new KaspiAPIService();
            $this->api->init();
        }
        if ($this->returnService === null && $module !== null) {
            $this->returnService = $module->get('orderReturnService');
        }
        if (!$this->returnService instanceof OrderReturnService) {
            $this->returnService = new OrderReturnService();
            $this->returnService->init();
        }
    }

    /**
     * Пройтись по активным Kaspi-заказам и синхронизировать их состояние.
     *
     * @return array
     */
    public function syncActiveOrders()
    {
        $activeKaspiStatuses = [
            OrderStatus::ORDER_APPROVED_BY_BANK,
            OrderStatus::ORDER_ACCEPTED_BY_MERCHANT,
            OrderStatus::ORDER_ASSEMBLE,
            OrderStatus::ORDER_KASPI_DELIVERY,
            OrderStatus::ORDER_CANCELLING,
        ];

        $candidates = EcommerceOutbound::find()
            ->andWhere(['not', ['external_order_number' => null]])
            ->andWhere(['not', ['external_order_number' => '']])
            ->andWhere(['deleted' => 0])
            ->andWhere([
                'or',
                ['external_kaspi_status' => null],
                ['external_kaspi_status' => ''],
                ['external_kaspi_status' => $activeKaspiStatuses],
            ])
            ->limit(500)
            ->all();

        $checked  = 0;
        $cancelled = 0;
        $completed = 0;
        $errors    = 0;

        foreach ($candidates as $outbound) {
            $kaspiOrderId = (string) $outbound->external_order_number;
            if ($kaspiOrderId === '') {
                continue;
            }

            try {
                $order = $this->api->getOrderById($kaspiOrderId);
            } catch (\Exception $e) {
                Yii::error('Kaspi getOrderById failed for ' . $kaspiOrderId . ': ' . $e->getMessage(), 'kaspi.orders');
                $errors++;
                continue;
            }
            if ($order === null) {
                $errors++;
                continue;
            }

            $checked++;
            $status = (string) $order->status;

            switch ($status) {
                case OrderStatus::ORDER_CANCELLING:
                case OrderStatus::ORDER_CANCELLED:
                    $this->handleCancelled($outbound, $kaspiOrderId);
                    $cancelled++;
                    break;

                case OrderStatus::ORDER_COMPLETED:
                    $this->markForOneCSync($outbound);
                    $completed++;
                    break;

                default:
                    break;
            }

            if ($outbound->external_kaspi_status !== $status) {
                EcommerceOutbound::updateAll(
                    [
                        'external_kaspi_status' => $status,
                        'updated_at'            => time(),
                    ],
                    ['id' => (int) $outbound->id]
                );
            }
        }

        return [
            'status'    => 'OK',
            'checked'   => $checked,
            'cancelled' => $cancelled,
            'completed' => $completed,
            'errors'    => $errors,
        ];
    }

    /**
     * Снять резерв стока и пометить заказ отменённым.
     * Делегирует returnService, но не вызывает cancelOrder ещё раз — Kaspi уже сам в CANCELLING/CANCELLED.
     */
    private function handleCancelled(EcommerceOutbound $outbound, $kaspiOrderId)
    {
        EcommerceStock::updateAll(
            [
                'status_availability' => EcommerceStock::STATUS_AVAILABILITY_YES,
                'outbound_id'         => 0,
                'kaspi_order_status'  => OrderStatus::ORDER_CANCELLED,
            ],
            [
                'and',
                ['outbound_id' => (int) $outbound->id],
                ['status_availability' => EcommerceStock::STATUS_AVAILABILITY_RESERVED],
                ['deleted' => 0],
            ]
        );

        Yii::info(
            'Kaspi order ' . $kaspiOrderId . ' cancelled on Kaspi side — stock released for outbound '
            . (int) $outbound->id,
            'kaspi.orders'
        );
    }

    /**
     * Пометить заказ для передачи в 1С. Реальная отправка — в отдельном кроне.
     */
    private function markForOneCSync(EcommerceOutbound $outbound)
    {
        if (!empty($outbound->sent_to_1c_at)) {
            return; // Уже отправлен
        }
        if ((string) $outbound->one_c_status === 'PENDING') {
            return;
        }

        $outbound->one_c_status = 'PENDING';
        $outbound->updated_at = time();
        $outbound->save(false);
    }
}
