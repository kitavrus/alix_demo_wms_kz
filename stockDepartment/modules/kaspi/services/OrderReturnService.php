<?php

namespace stockDepartment\modules\kaspi\services;

use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use common\ecommerce\entities\EcommerceStock;
use common\modules\stock\models\Stock;
use stockDepartment\modules\kaspi\dto\PartialReturnRequestDto;
use stockDepartment\modules\kaspi\enums\OrderStatus;
use Yii;
use yii\base\Component;
use yii\db\Query;

/**
 * Сервис для сценариев возврата заказа Kaspi.
 *
 * Стратегия:
 *  - Kaspi poll по статусу KASPI_DELIVERY_RETURN_REQUESTED.
 *  - Для каждого найденного заказа читаем payload возврата
 *    (refundCode + список возвращаемых SKU с их qty — может быть меньше отгруженного).
 *  - Создаём EcommerceReturn + EcommerceReturnItem только на возвращаемые позиции.
 *  - Привязываем ровно N строк ecommerce_stock (N = qty возврата) — остальные
 *    продолжают оставаться зарезервированными под outbound.
 *  - Оператор идёт на форму /alix/ecommerce/returns/scanning/index и сканирует.
 *
 * Идемпотентность: по паре (source_kaspi_order_id, source_kaspi_refund_code).
 * Клиент может сделать несколько частичных возвратов по одному orderId —
 * каждый со своим refundCode, каждый попадает в отдельный EcommerceReturn.
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
     * Снять резерв до доставки: cancelOrder в Kaspi + вернуть строки стока в YES.
     * Используется, если отмена происходит до отгрузки (не через возвратный flow).
     *
     * Освобождаются ТОЛЬКО строки до фазы упаковки (PART_RESERVED/FULL_RESERVED).
     * Уже отсканированные/упакованные (status=OUTBOUND_SCANNED, box_barcode заполнен)
     * не трогаем — иначе физически в коробке товар, а в БД он «свободен» = дабл-аллокация.
     * Для таких случаев нужен return-flow (`/alix/ecommerce/returns/scanning/*`).
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

        // Если хотя бы одна строка уже упакована/отгружена — отказываемся,
        // чтобы caller осознанно пошёл в return-flow вместо «тихого» сброса.
        $packedCount = (int) EcommerceStock::find()
            ->andWhere(['outbound_id' => (int) $outbound->id])
            ->andWhere(['deleted' => 0])
            ->andWhere(['or',
                ['status' => Stock::STATUS_OUTBOUND_SCANNED],
                ['and', ['not', ['box_barcode' => null]], ['!=', 'box_barcode', '']],
            ])
            ->count();

        if ($packedCount > 0) {
            return [
                'status'  => 'already_packed',
                'message' => sprintf(
                    'У отгрузки %d есть %d упакованных/отгруженных строк стока — '
                    . 'используйте возвратный флоу (/alix/ecommerce/returns/scanning/index) '
                    . 'или физически распакуйте товар перед повторной попыткой.',
                    (int) $outbound->id,
                    $packedCount
                ),
                'outbound_id'  => (int) $outbound->id,
                'packed_rows'  => $packedCount,
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
                ['status' => [
                    Stock::STATUS_OUTBOUND_PART_RESERVED,
                    Stock::STATUS_OUTBOUND_FULL_RESERVED,
                ]],
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
     * Опросить Kaspi на предмет запрошенных возвратов (KASPI_DELIVERY_RETURN_REQUESTED)
     * и идемпотентно создать EcommerceReturn. Поддерживает частичные возвраты.
     */
    public function pollKaspiReturnsAndCreateEcomReturns()
    {
        $params = [
            'filter[orders][status]' => OrderStatus::ORDER_KASPI_DELIVERY_RETURN_REQUESTED,
            // creationDate Kaspi требует обязательно (иначе HTTP 400).
            // Window остаётся дефолтный 13 дней (см. KaspiAPIService::getOrdersPage):
            // покрывает большинство возвратов, т.к. срок Kaspi-возврата ≤14 дней с доставки.
            // Защита от фантомов — order->status check ниже в loop'е.
        ];

        try {
            $page = $this->api->getOrdersPage($params);
        } catch (\Exception $e) {
            Yii::error(
                'Kaspi poll возвратов: запрос к Kaspi упал: ' . $e->getMessage(),
                'kaspi.return'
            );
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }

        $orders = isset($page->orders) && is_array($page->orders) ? $page->orders : [];
        if (empty($orders)) {
            return ['status' => 'OK', 'fetched' => 0, 'created' => 0, 'skipped' => 0, 'errors' => 0];
        }

        $created = 0;
        $skipped = 0;
        $errors  = 0;
        $results = [];

        foreach ($orders as $order) {
            $kaspiOrderId = is_object($order) && property_exists($order, 'id') && !empty($order->id)
                ? (string) $order->id
                : '';
            if ($kaspiOrderId === '') {
                continue;
            }

            // Защита: Kaspi может вернуть в выдаче заказы с другими статусами
            // (видели CANCELLED/ARCHIVE и ACCEPTED_BY_MERCHANT при фильтре
            // KASPI_DELIVERY_RETURN_REQUESTED). Создавать EcommerceReturn по таким —
            // фантомные записи. Фильтруем явно по статусу заказа.
            $orderStatus = is_object($order) && property_exists($order, 'status')
                ? (string) $order->status
                : '';
            if ($orderStatus !== OrderStatus::ORDER_KASPI_DELIVERY_RETURN_REQUESTED) {
                Yii::warning(
                    'Kaspi poll возвратов: у заказа ' . $kaspiOrderId . ' status='
                    . ($orderStatus !== '' ? $orderStatus : '<пусто>')
                    . ' (ожидался KASPI_DELIVERY_RETURN_REQUESTED) — пропущен',
                    'kaspi.return'
                );
                $skipped++;
                continue;
            }

            $returnRequest = $this->readReturnRequestFromKaspi($kaspiOrderId);
            if (empty($returnRequest['items'])) {
                Yii::warning(
                    'Kaspi poll возвратов: у заказа ' . $kaspiOrderId
                    . ' нет позиций к возврату — пропущен',
                    'kaspi.return'
                );
                $skipped++;
                continue;
            }

            if ($this->returnExists($kaspiOrderId, $returnRequest['refund_code'])) {
                $skipped++;
                continue;
            }

            $outbound = EcommerceOutbound::find()
                ->andWhere(['external_order_number' => $kaspiOrderId])
                ->andWhere(['deleted' => 0])
                ->one();
            if ($outbound === null) {
                Yii::warning(
                    'Kaspi poll возвратов: для заказа ' . $kaspiOrderId
                    . ' не найдена отгрузка (ecommerce_outbound.external_order_number) — пропущен',
                    'kaspi.return'
                );
                $skipped++;
                continue;
            }

            $result = $this->createEcomReturnFromOutbound($outbound, $kaspiOrderId, $returnRequest);
            if (isset($result['status']) && $result['status'] === 'OK') {
                $created++;
                $results[] = [
                    'kaspi_order_id' => $kaspiOrderId,
                    'refund_code'    => $returnRequest['refund_code'],
                    'return_id'      => $result['return_id'],
                    'linked_stock'   => $result['linked_stock'],
                ];
            } else {
                $errors++;
                Yii::error(
                    'Kaspi poll возвратов: не удалось создать EcommerceReturn для '
                    . $kaspiOrderId . ': ' . json_encode($result, JSON_UNESCAPED_UNICODE),
                    'kaspi.return'
                );
            }
        }

        return [
            'status'  => 'OK',
            'fetched' => count($orders),
            'created' => $created,
            'skipped' => $skipped,
            'errors'  => $errors,
            'items'   => $results,
        ];
    }

    /**
     * Ручное создание EcommerceReturn по Kaspi orderId — используется из UI-формы
     * "Создать возврат" и как fallback когда poll пропустил заказ.
     *
     * Для ручного режима читаем return payload так же, как poll. Идемпотентность
     * по паре (orderId, refundCode) — повторный вызов без изменений в Kaspi
     * вернёт 'exists'.
     *
     * @param string $kaspiOrderId
     * @return array ['status' => 'OK'|'exists'|'not_found'|'no_items'|'error', ...]
     */
    public function createEcomReturnByKaspiOrderId($kaspiOrderId)
    {
        $kaspiOrderId = trim((string) $kaspiOrderId);
        if ($kaspiOrderId === '') {
            return ['status' => 'error', 'message' => 'Empty kaspi order id'];
        }

        // Запрещаем заводить возврат по заказу, который Kaspi не пометил как RETURN_REQUESTED.
        // Иначе оператор может случайно создать «фантомный» возврат для CANCELLED / ACCEPTED_BY_MERCHANT.
        $kaspiOrder = $this->api->getOrderById($kaspiOrderId);
        if ($kaspiOrder === null) {
            return ['status' => 'not_found', 'message' => 'Kaspi order ' . $kaspiOrderId . ' не найден'];
        }
        $kaspiStatus = (string) $kaspiOrder->status;
        if ($kaspiStatus !== OrderStatus::ORDER_KASPI_DELIVERY_RETURN_REQUESTED) {
            return [
                'status'  => 'wrong_status',
                'message' => 'Kaspi order ' . $kaspiOrderId . ' имеет status=' . $kaspiStatus
                    . '. Возврат можно создать только в статусе KASPI_DELIVERY_RETURN_REQUESTED.',
            ];
        }

        $returnRequest = $this->readReturnRequestFromKaspi($kaspiOrderId);
        if (empty($returnRequest['items'])) {
            return ['status' => 'no_items', 'message' => 'Kaspi вернул пустой список позиций возврата'];
        }

        if ($this->returnExists($kaspiOrderId, $returnRequest['refund_code'])) {
            $existing = EcommerceReturn::find()
                ->andWhere([
                    'source_kaspi_order_id' => $kaspiOrderId,
                    'source_kaspi_refund_code' => $returnRequest['refund_code'],
                ])
                ->andWhere(['deleted' => 0])
                ->one();
            return [
                'status'    => 'exists',
                'return_id' => $existing ? (int) $existing->id : 0,
            ];
        }

        $outbound = EcommerceOutbound::find()
            ->andWhere(['external_order_number' => $kaspiOrderId])
            ->andWhere(['deleted' => 0])
            ->one();
        if ($outbound === null) {
            return [
                'status'  => 'not_found',
                'message' => 'Outbound with external_order_number=' . $kaspiOrderId . ' not found',
            ];
        }

        return $this->createEcomReturnFromOutbound($outbound, $kaspiOrderId, $returnRequest);
    }

    /**
     * Создать частичный возврат по orderId + явный список позиций (`{product_guid, qty}`)
     * из тела HTTP-запроса. Используется endpoint'ом `POST /kaspi/api/v1/orders/<id>/partial-return`.
     *
     * Отличие от `createEcomReturnByKaspiOrderId`: позиции и refund_code приходят
     * от клиента, а не из `getOrderEntries`. Status-guard и идемпотентность те же.
     *
     * @param string                    $kaspiOrderId
     * @param PartialReturnRequestDto   $dto
     * @return array ['status' => 'OK'|'exists'|'not_found'|'wrong_status'|'no_items'|'error', ...]
     */
    public function createPartialReturn($kaspiOrderId, PartialReturnRequestDto $dto)
    {
        $kaspiOrderId = trim((string) $kaspiOrderId);
        if ($kaspiOrderId === '') {
            return ['status' => 'error', 'message' => 'Empty kaspi order id'];
        }

        $kaspiOrder = $this->api->getOrderById($kaspiOrderId);
        if ($kaspiOrder === null) {
            return ['status' => 'not_found', 'message' => 'Kaspi order ' . $kaspiOrderId . ' не найден'];
        }
        $kaspiStatus = (string) $kaspiOrder->status;
        if ($kaspiStatus !== OrderStatus::ORDER_KASPI_DELIVERY_RETURN_REQUESTED) {
            return [
                'status'  => 'wrong_status',
                'message' => 'Kaspi order ' . $kaspiOrderId . ' имеет status=' . $kaspiStatus
                    . '. Возврат можно создать только в статусе KASPI_DELIVERY_RETURN_REQUESTED.',
            ];
        }

        // Конвертируем DTO-позиции в формат createEcomReturnFromOutbound.
        // В DTO product_guid должен быть уже product_v2.guid; resolveKaspiSkuToGuid
        // ниже примет и guid, и article — как и в poll-флоу.
        $items = [];
        foreach ((array) $dto->items as $row) {
            $guid = isset($row['product_guid']) ? (string) $row['product_guid'] : '';
            $qty  = isset($row['qty']) ? (int) $row['qty'] : 0;
            if ($guid === '' || $qty <= 0) {
                continue;
            }
            $items[] = ['sku' => $guid, 'qty' => $qty];
        }
        if (empty($items)) {
            return ['status' => 'no_items', 'message' => 'Список items пуст после нормализации'];
        }

        $refundCode = ($dto->refund_code !== null && $dto->refund_code !== '')
            ? (string) $dto->refund_code
            : 'RF-' . substr($kaspiOrderId, 0, 16);

        if ($this->returnExists($kaspiOrderId, $refundCode)) {
            $existing = EcommerceReturn::find()
                ->andWhere([
                    'source_kaspi_order_id'    => $kaspiOrderId,
                    'source_kaspi_refund_code' => $refundCode,
                ])
                ->andWhere(['deleted' => 0])
                ->one();
            return [
                'status'    => 'exists',
                'return_id' => $existing ? (int) $existing->id : 0,
            ];
        }

        $outbound = EcommerceOutbound::find()
            ->andWhere(['external_order_number' => $kaspiOrderId])
            ->andWhere(['deleted' => 0])
            ->one();
        if ($outbound === null) {
            return [
                'status'  => 'not_found',
                'message' => 'Outbound with external_order_number=' . $kaspiOrderId . ' not found',
            ];
        }

        return $this->createEcomReturnFromOutbound(
            $outbound,
            $kaspiOrderId,
            ['refund_code' => $refundCode, 'items' => $items]
        );
    }

    /**
     * @param EcommerceOutbound $outbound
     * @param string            $kaspiOrderId
     * @param array             $returnRequest ['refund_code' => '...', 'items' => [['sku' => '...', 'qty' => N], ...]]
     * @return array
     */
    private function createEcomReturnFromOutbound(EcommerceOutbound $outbound, $kaspiOrderId, array $returnRequest)
    {
        $now = time();
        $refundCode = (string) $returnRequest['refund_code'];
        $requestedItems = $returnRequest['items'];

        $expectedTotal = 0;
        foreach ($requestedItems as $it) {
            $expectedTotal += (int) $it['qty'];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $return = new EcommerceReturn();
            $return->client_id                = (int) $outbound->client_id;
            $return->outbound_id              = (int) $outbound->id;
            $return->order_number             = (string) $outbound->order_number;
            $return->customer_name            = (string) $outbound->customer_name;
            $return->city                     = '';
            $return->customer_address         = '';
            $return->expected_qty             = $expectedTotal;
            $return->accepted_qty             = 0;
            $return->status                   = ReturnOutboundStatus::_NEW;
            $return->source_kaspi_order_id    = $kaspiOrderId;
            $return->source_kaspi_refund_code = $refundCode;
            $return->created_at               = $now;
            $return->updated_at               = $now;
            $return->deleted                  = 0;

            if (!$return->save(false)) {
                throw new \RuntimeException('Failed to save EcommerceReturn for ' . $kaspiOrderId);
            }

            $linkedStock = 0;
            foreach ($requestedItems as $reqItem) {
                $sku = (string) $reqItem['sku'];
                $qty = (int) $reqItem['qty'];
                if ($sku === '' || $qty <= 0) {
                    continue;
                }

                // Kaspi даёт productCode (article), в ecommerce_outbound_items.product_sku
                // у нас лежит GUID из product_v2 — нужна резолюция.
                $guid = $this->resolveKaspiSkuToGuid($sku);
                if ($guid === '') {
                    Yii::warning(
                        "Kaspi poll возвратов: sku '{$sku}' не резолвится в product_v2.guid — позиция пропущена",
                        'kaspi.return'
                    );
                    continue;
                }

                $oItem = EcommerceOutboundItem::find()
                    ->andWhere(['outbound_id' => (int) $outbound->id])
                    ->andWhere(['product_sku' => $guid])
                    ->andWhere(['deleted' => 0])
                    ->one();
                if ($oItem === null) {
                    Yii::warning(
                        "Kaspi poll возвратов: в отгрузке {$outbound->id} нет позиции с product_sku={$guid} — пропущена",
                        'kaspi.return'
                    );
                    continue;
                }

                $rItem = new EcommerceReturnItem();
                $rItem->return_id       = (int) $return->id;
                $rItem->product_id      = (int) $oItem->product_id;
                $rItem->product_barcode = $this->resolveProductBarcode($oItem);
                $rItem->expected_qty    = $qty;
                $rItem->accepted_qty    = 0;
                $rItem->status          = ReturnOutboundStatus::_NEW;
                $rItem->created_at      = $now;
                $rItem->updated_at      = $now;
                $rItem->deleted         = 0;
                if (!$rItem->save(false)) {
                    throw new \RuntimeException('Failed to save EcommerceReturnItem for sku=' . $sku);
                }

                // Берём ровно $qty свободных (ещё не привязанных к какому-либо возврату)
                // сток-строк из того же outbound_item.
                $stockIds = EcommerceStock::find()
                    ->select('id')
                    ->andWhere(['outbound_id' => (int) $outbound->id])
                    ->andWhere(['outbound_item_id' => (int) $oItem->id])
                    ->andWhere(['or', ['return_id' => 0], ['return_id' => null]])
                    ->andWhere(['deleted' => 0])
                    ->orderBy(['id' => SORT_ASC])
                    ->limit($qty)
                    ->column();

                if (count($stockIds) < $qty) {
                    Yii::warning(
                        "Kaspi poll возвратов: по sku={$sku} найдено только "
                        . count($stockIds) . " свободных строк стока из {$qty} требуемых",
                        'kaspi.return'
                    );
                }

                if (!empty($stockIds)) {
                    $linkedStock += (int) EcommerceStock::updateAll(
                        [
                            'return_id'          => (int) $return->id,
                            'return_item_id'     => (int) $rItem->id,
                            'kaspi_order_status' => OrderStatus::ORDER_KASPI_DELIVERY_RETURN_REQUESTED,
                            'updated_at'         => $now,
                        ],
                        ['id' => $stockIds]
                    );
                }
            }

            $transaction->commit();

            return [
                'status'       => 'OK',
                'return_id'    => (int) $return->id,
                'linked_stock' => $linkedStock,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error(
                'Kaspi: не удалось создать EcommerceReturn: ' . $e->getMessage(),
                'kaspi.return'
            );
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Подтвердить в Kaspi завершение возврата — переводит заказ в RETURNED.
     * Вызывается после финального подтверждения возврата в Nomadex.
     *
     * Ответ Kaspi логируется с меткой 'kaspi.return.response' (runtime/logs/app.log).
     * Fiscal-чек возврата через API у Kaspi напрямую не отдаётся, но в будущем
     * в ответе могут появиться поля вроде refundReceiptUrl / refundedAt —
     * по логам увидим.
     */
    public function confirmReturnCompleted($kaspiOrderId)
    {
        $kaspiOrderId = (string) $kaspiOrderId;
        $response = $this->api->changeOrderStatus($kaspiOrderId, OrderStatus::ORDER_RETURNED);

        Yii::info(
            'Kaspi: ответ на перевод заказа ' . $kaspiOrderId . ' в RETURNED: '
            . json_encode($response, JSON_UNESCAPED_UNICODE),
            'kaspi.return.response'
        );

        return $response;
    }

    /**
     * Прочитать payload возврата из Kaspi. Возвращает нормализованную структуру:
     *  ['refund_code' => string, 'items' => [['sku' => string (productCode), 'qty' => int], ...]]
     *
     * TODO: когда появится реальный sample ответа Kaspi на partial-return
     * (поля refundCode, returnedQuantity на entries и/или отдельный /v2/returns),
     * подменить на парсер реального payload. Сейчас используем getOrderEntries:
     *   - qty = returnedQuantity, если есть (для partial);
     *   - иначе = quantity (полный возврат).
     * refund_code берётся из ответа, если присутствует, иначе синтезируется
     * из orderId (idempotency всё равно работает, просто повторный poll того же
     * orderId без refund_code будет "уже существует").
     *
     * @param string $kaspiOrderId
     * @return array
     */
    private function readReturnRequestFromKaspi($kaspiOrderId)
    {
        try {
            $response = $this->api->getOrderEntries($kaspiOrderId);
        } catch (\Exception $e) {
            Yii::error(
                'Kaspi: getOrderEntries для ' . $kaspiOrderId . ' упал: ' . $e->getMessage(),
                'kaspi.return'
            );
            return ['refund_code' => '', 'items' => []];
        }

        $entries = isset($response['data']) && is_array($response['data']) ? $response['data'] : [];

        $refundCode = '';
        if (isset($response['refundCode']) && $response['refundCode'] !== '') {
            $refundCode = (string) $response['refundCode'];
        } elseif (isset($response['meta']['refundCode']) && $response['meta']['refundCode'] !== '') {
            $refundCode = (string) $response['meta']['refundCode'];
        } else {
            // Синтетический fallback — чтобы (orderId, refundCode) уникально идентифицировали возврат.
            $refundCode = 'RF-' . substr((string) $kaspiOrderId, 0, 16);
        }

        $items = [];
        foreach ($entries as $entry) {
            $attrs = isset($entry['attributes']) && is_array($entry['attributes']) ? $entry['attributes'] : [];

            $sku = '';
            if (isset($attrs['productCode'])) {
                $sku = (string) $attrs['productCode'];
            } elseif (isset($attrs['merchantProductCode'])) {
                $sku = (string) $attrs['merchantProductCode'];
            } elseif (isset($attrs['offer']['code'])) {
                $sku = (string) $attrs['offer']['code'];
            }

            $qty = 0;
            if (isset($attrs['returnedQuantity']) && (int) $attrs['returnedQuantity'] > 0) {
                $qty = (int) $attrs['returnedQuantity'];
            } elseif (isset($attrs['quantity'])) {
                $qty = (int) $attrs['quantity'];
            }

            if ($sku === '' || $qty <= 0) {
                continue;
            }

            $items[] = ['sku' => $sku, 'qty' => $qty];
        }

        return ['refund_code' => $refundCode, 'items' => $items];
    }

    /**
     * @param string $kaspiOrderId
     * @param string $refundCode
     * @return bool
     */
    private function returnExists($kaspiOrderId, $refundCode)
    {
        return EcommerceReturn::find()
            ->andWhere([
                'source_kaspi_order_id'    => $kaspiOrderId,
                'source_kaspi_refund_code' => $refundCode,
            ])
            ->andWhere(['deleted' => 0])
            ->exists();
    }

    /**
     * Kaspi productCode (= article) → product_v2.guid, которым хранится product_sku
     * в ecommerce_outbound_items.
     */
    private function resolveKaspiSkuToGuid($kaspiSku)
    {
        if ($kaspiSku === '') {
            return '';
        }
        $row = (new Query())
            ->select(['guid'])
            ->from('product_v2')
            ->andWhere(['or', ['article' => $kaspiSku], ['guid' => $kaspiSku]])
            ->one();
        return $row && !empty($row['guid']) ? (string) $row['guid'] : '';
    }

    /**
     * OrderImportService пишет в ecommerce_outbound_items только product_sku (guid);
     * physical barcode берём либо из самого outbound-item, либо из product_v2 по guid.
     */
    private function resolveProductBarcode(EcommerceOutboundItem $oItem)
    {
        $fromItem = (string) $oItem->product_barcode;
        if ($fromItem !== '') {
            return $fromItem;
        }

        $sku = (string) $oItem->product_sku;
        if ($sku === '') {
            return '';
        }

        $row = (new Query())
            ->select(['barcode'])
            ->from('product_v2')
            ->andWhere(['guid' => $sku])
            ->one();

        return $row && !empty($row['barcode']) ? (string) $row['barcode'] : '';
    }
}
