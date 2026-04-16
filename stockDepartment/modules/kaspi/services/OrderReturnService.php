<?php

namespace stockDepartment\modules\kaspi\services;

use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceInboundItem;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceStock;
use stockDepartment\modules\kaspi\dto\PartialReturnRequestDto;
use stockDepartment\modules\kaspi\enums\OrderStatus;
use Yii;
use yii\base\Component;

/**
 * Сервис для сценариев возврата заказа Kaspi.
 *
 * Сценарий A — отмена до доставки: товар не покидал склад, снимаем резерв.
 * Сценарий B — частичный возврат после доставки: оператор заводит Inbound return,
 * физическая приёмка идёт через существующий inbound-флоу.
 */
class OrderReturnService extends Component
{
    /** @var KaspiAPIService|null */
    public $api;

    public function init()
    {
        parent::init();
        if ($this->api === null) {
            $module = Yii::$app->getModule('kaspi');
            if ($module !== null) {
                $this->api = $module->get('apiService');
            }
        }
        if (!$this->api instanceof KaspiAPIService) {
            $this->api = new KaspiAPIService();
            $this->api->init();
        }
    }

    /**
     * Сценарий A: отменить заказ в Kaspi и вернуть зарезервированные строки стока в YES.
     *
     * @param string      $kaspiOrderId Kaspi order id (external_order_number в EcommerceOutbound)
     * @param string|null $reason       Причина отмены для Kaspi
     * @return array
     */
    public function returnToStock($kaspiOrderId, $reason = null)
    {
        $outbound = EcommerceOutbound::find()
            ->andWhere(['external_order_number' => $kaspiOrderId])
            ->andWhere(['deleted' => 0])
            ->one();

        if ($outbound === null) {
            return [
                'status'  => 'not_found',
                'message' => 'Outbound order with external_order_number=' . $kaspiOrderId . ' not found',
            ];
        }

        $kaspiResponse = $this->api->cancelOrder(
            $kaspiOrderId,
            $reason !== null && $reason !== '' ? $reason : 'MERCHANT_CANCELLED_BEFORE_SHIPMENT'
        );

        $released = EcommerceStock::updateAll(
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

        return [
            'status'         => 'OK',
            'order_id'       => $kaspiOrderId,
            'outbound_id'    => (int) $outbound->id,
            'released_rows'  => (int) $released,
            'kaspi_response' => $kaspiResponse,
        ];
    }

    /**
     * Сценарий B: создать Inbound return с привязкой к Kaspi-заказу.
     * Физическая приёмка идёт дальше в обычном порядке.
     */
    public function createPartialReturn($kaspiOrderId, PartialReturnRequestDto $dto)
    {
        $outbound = EcommerceOutbound::find()
            ->andWhere(['external_order_number' => $kaspiOrderId])
            ->andWhere(['deleted' => 0])
            ->one();

        $now = time();
        $userId = Yii::$app->has('user') && !Yii::$app->user->isGuest
            ? (int) Yii::$app->user->id
            : null;

        $inbound = new EcommerceInbound();
        $inbound->client_id                = $outbound !== null ? (int) $outbound->client_id : 0;
        $inbound->order_number             = 'KASPI-RET-' . substr((string) $kaspiOrderId, 0, 20);
        $inbound->expected_product_qty     = array_sum(array_column($dto->items, 'qty'));
        $inbound->status                   = 0;
        $inbound->source_kaspi_order_id    = (string) $kaspiOrderId;
        $inbound->source_kaspi_refund_code = $dto->refund_code !== null ? (string) $dto->refund_code : '';
        $inbound->created_user_id          = $userId;
        $inbound->created_at               = $now;

        if (!$inbound->save(false)) {
            Yii::error('Failed to create Kaspi inbound return for order ' . $kaspiOrderId, 'kaspi.return');
            return [
                'status'  => 'error',
                'message' => 'Failed to create inbound return',
            ];
        }

        $itemsCount = 0;
        foreach ($dto->items as $item) {
            $guid = isset($item['product_guid']) ? (string) $item['product_guid'] : '';
            $qty  = isset($item['qty']) ? (int) $item['qty'] : 0;
            if ($guid === '' || $qty <= 0) {
                continue;
            }

            $row = new EcommerceInboundItem();
            $row->inbound_id           = (int) $inbound->id;
            $row->client_lot_sku       = $guid;
            $row->product_expected_qty = $qty;
            $row->product_accepted_qty = 0;
            $row->status               = 0;
            $row->created_user_id      = $userId;
            $row->created_at           = $now;
            $row->save(false);
            $itemsCount++;
        }

        return [
            'status'           => 'OK',
            'inbound_order_id' => (int) $inbound->id,
            'kaspi_order_id'   => (string) $kaspiOrderId,
            'items_count'      => $itemsCount,
        ];
    }

    /**
     * Опросить Kaspi на предмет возвратов и идемпотентно создать Inbound return'ы.
     *
     * По диаграмме flow: Kaspi подтверждает возврат на своей стороне → Nomadex
     * подхватывает его через API (наш polling) → автоматически создаёт Inbound.
     *
     * Вызывается cron-экшеном (например, раз в 30 минут).
     * Идемпотентность: пропускаем Kaspi-заказы, для которых уже есть Inbound
     * с тем же source_kaspi_order_id.
     *
     * @return array
     */
    public function pollKaspiReturnsAndCreateInbounds()
    {
        // Kaspi фильтр по статусу заказа — забираем запрошенные возвраты.
        $params = [
            'filter[orders][status]' => OrderStatus::ORDER_KASPI_DELIVERY_RETURN_REQUESTED,
        ];

        try {
            $page = $this->api->getOrdersPage($params);
        } catch (\Exception $e) {
            Yii::error('Kaspi return polling failed: ' . $e->getMessage(), 'kaspi.return');
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }

        $orders = isset($page->orders) && is_array($page->orders) ? $page->orders : [];
        if (empty($orders)) {
            return ['status' => 'OK', 'fetched' => 0, 'created' => 0, 'skipped' => 0];
        }

        $created = 0;
        $skipped = 0;
        $results = [];

        foreach ($orders as $order) {
            $kaspiOrderId = is_object($order) && property_exists($order, 'id') && !empty($order->id)
                ? (string) $order->id
                : '';
            if ($kaspiOrderId === '') {
                continue;
            }

            $exists = EcommerceInbound::find()
                ->andWhere(['source_kaspi_order_id' => $kaspiOrderId])
                ->andWhere(['deleted' => 0])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $dto = $this->buildPartialReturnDtoFromKaspi($kaspiOrderId);
            if (empty($dto->items)) {
                Yii::warning('Kaspi return poll: order ' . $kaspiOrderId . ' has no returnable entries', 'kaspi.return');
                $skipped++;
                continue;
            }

            $result = $this->createPartialReturn($kaspiOrderId, $dto);
            if (isset($result['status']) && $result['status'] === 'OK') {
                $created++;
                $results[] = [
                    'kaspi_order_id'   => $kaspiOrderId,
                    'inbound_order_id' => $result['inbound_order_id'],
                ];
            } else {
                Yii::error('Kaspi return poll: failed to create inbound for ' . $kaspiOrderId . ': ' . json_encode($result), 'kaspi.return');
            }
        }

        return [
            'status'  => 'OK',
            'fetched' => count($orders),
            'created' => $created,
            'skipped' => $skipped,
            'items'   => $results,
        ];
    }

    /**
     * Собрать PartialReturnRequestDto по данным Kaspi: отдельно подтянуть позиции
     * заказа через KaspiAPIService::getOrderEntries() и смапить в items.
     *
     * Ожидаемая структура ответа Kaspi — JSON:API: data[].attributes с полями
     * { offer: {code, name}, quantity, entryNumber, totalPrice } и т.п.
     *
     * При ошибке или пустом ответе возвращает DTO с пустым items — caller должен это проверить.
     */
    private function buildPartialReturnDtoFromKaspi($kaspiOrderId)
    {
        $dto = new PartialReturnRequestDto();
        $dto->note = 'Auto-created from Kaspi poll';

        $items = [];
        try {
            $response = $this->api->getOrderEntries($kaspiOrderId);
        } catch (\Exception $e) {
            Yii::error('Kaspi getOrderEntries failed for ' . $kaspiOrderId . ': ' . $e->getMessage(), 'kaspi.return');
            $dto->items = [];
            return $dto;
        }

        $entries = isset($response['data']) && is_array($response['data']) ? $response['data'] : [];
        foreach ($entries as $entry) {
            $attrs = isset($entry['attributes']) && is_array($entry['attributes']) ? $entry['attributes'] : [];
            $sku = null;
            if (isset($attrs['offer']) && is_array($attrs['offer']) && isset($attrs['offer']['code'])) {
                $sku = (string) $attrs['offer']['code'];
            } elseif (isset($attrs['merchantProductCode'])) {
                $sku = (string) $attrs['merchantProductCode'];
            }
            $qty = isset($attrs['quantity']) ? (int) $attrs['quantity'] : 0;

            if ($sku !== null && $sku !== '' && $qty > 0) {
                $items[] = ['product_guid' => $sku, 'qty' => $qty];
            }
        }
        $dto->items = $items;

        return $dto;
    }

    /**
     * Подтвердить в Kaspi завершение возврата (вызывается после Окончательного
     * подтверждения возврата в Nomadex).
     *
     * Переводит Kaspi-заказ в статус RETURNED — это триггерит на стороне Kaspi
     * регистрацию возврата на кассе и перевод денег покупателю.
     */
    public function confirmReturnCompleted($kaspiOrderId)
    {
        return $this->api->changeOrderStatus((string) $kaspiOrderId, OrderStatus::ORDER_RETURNED);
    }
}
