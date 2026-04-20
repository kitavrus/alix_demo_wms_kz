<?php

namespace stockDepartment\modules\kaspi\services;

use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceStock;
use common\modules\stock\models\Stock;
use stockDepartment\modules\alix\controllers\outbound\domain\constants\CourierCompany;
use stockDepartment\modules\kaspi\dto\OrderDto;
use stockDepartment\modules\kaspi\enums\OrderStatus;
use Yii;
use yii\base\Component;
use yii\db\Query;

/**
 * Импорт новых заказов из Kaspi (poll cron).
 *
 * Poll: GET /v2/orders с фильтром
 *   filter[orders][status]   = APPROVED_BY_BANK
 *   filter[orders][state]    = NEW
 *   creationDate[$ge/$le]    = окно последних N часов (по умолчанию 6)
 *
 * Для каждого нового заказа:
 *   1) Идемпотентно находит/создаёт EcommerceOutbound по external_order_number.
 *   2) Тянет позиции через getOrderEntries и пишет EcommerceOutboundItem.
 *   3) Резервирует нужное количество строк ecommerce_stock (status_availability=YES)
 *      по product_sku (merchantProductCode из Kaspi = product_sku в нашей БД, без маппинга).
 *   4) При достаточном стоке — POST acceptOrder в Kaspi; при нехватке —
 *      откат транзакции и cancelOrder(MERCHANT_OUT_OF_STOCK).
 */
class OrderImportService extends Component
{
    /** @var KaspiAPIService|null */
    public $api;

    /** @var int Окно poll по creationDate, в часах. */
    public $pollWindowHours = 6;

    /** @var int Размер страницы запроса к Kaspi. */
    public $pageSize = 100;

    /** @var int client_id по умолчанию для создаваемых EcommerceOutbound. */
    public $defaultClientId = 0;

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
     * Забрать новые заказы Kaspi и импортировать их в EcommerceOutbound.
     *
     * @return array
     */
    public function pollAndImportNew()
    {
        $nowMs     = (int) floor(microtime(true) * 1000);
        $fromMs    = (int) floor((time() - $this->pollWindowHours * 3600) * 1000);

        $params = [
            'filter[orders][status]'               => OrderStatus::ORDER_APPROVED_BY_BANK,
            'filter[orders][state]'                => 'NEW',
            'filter[orders][creationDate][$ge]'    => (string) $fromMs,
            'filter[orders][creationDate][$le]'    => (string) $nowMs,
            'page[number]'                         => 0,
            'page[size]'                           => max(1, (int) $this->pageSize),
        ];

        try {
            $page = $this->api->getOrdersPage($params);
        } catch (\Exception $e) {
            Yii::error('Kaspi order poll failed: ' . $e->getMessage(), 'kaspi.orders');
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }

        $orders = isset($page->orders) && is_array($page->orders) ? $page->orders : [];
        if (empty($orders)) {
            return [
                'status'           => 'OK',
                'fetched'          => 0,
                'imported'         => 0,
                'skipped_existing' => 0,
                'failed_no_stock'  => 0,
                'errors'           => 0,
            ];
        }

        $imported = 0;
        $skipped  = 0;
        $noStock  = 0;
        $errors   = 0;
        $details  = [];

        foreach ($orders as $order) {
            if (!$order instanceof OrderDto) {
                continue;
            }
            $kaspiOrderId = (string) $order->id;
            if ($kaspiOrderId === '') {
                continue;
            }

            $existing = EcommerceOutbound::find()
                ->andWhere(['external_order_number' => $kaspiOrderId])
                ->andWhere(['deleted' => 0])
                ->one();
            if ($existing !== null) {
                $skipped++;
                continue;
            }

            try {
                $entriesResponse = $this->api->getOrderEntries($kaspiOrderId);
            } catch (\Exception $e) {
                Yii::error('Kaspi getOrderEntries failed for ' . $kaspiOrderId . ': ' . $e->getMessage(), 'kaspi.orders');
                $errors++;
                continue;
            }

            $entries = $this->extractEntries($entriesResponse);
            if (empty($entries)) {
                Yii::warning('Kaspi order ' . $kaspiOrderId . ' has no entries — skipped', 'kaspi.orders');
                $errors++;
                continue;
            }

            $entries = self::resolveArticlesToGuids($entries);

            $importResult = $this->importSingleOrder($order, $entries);

            if ($importResult['status'] === 'OK') {
                $imported++;
                $details[] = [
                    'kaspi_order_id' => $kaspiOrderId,
                    'outbound_id'    => $importResult['outbound_id'],
                ];

                try {
                    $this->api->acceptOrder($kaspiOrderId);
                    EcommerceOutbound::updateAll(
                        [
                            'external_kaspi_status' => OrderStatus::ORDER_ACCEPTED_BY_MERCHANT,
                            'updated_at'            => time(),
                        ],
                        ['id' => (int) $importResult['outbound_id']]
                    );
                } catch (\Exception $e) {
                    Yii::error(
                        'Kaspi acceptOrder failed for ' . $kaspiOrderId . ': ' . $e->getMessage(),
                        'kaspi.orders'
                    );
                    $errors++;
                }
            } elseif ($importResult['status'] === 'no_stock') {
                $noStock++;
                $details[] = [
                    'kaspi_order_id' => $kaspiOrderId,
                    'missing'        => $importResult['missing'],
                ];
                try {
                    $this->api->cancelOrder($kaspiOrderId, 'MERCHANT_OUT_OF_STOCK');
                } catch (\Exception $e) {
                    Yii::error(
                        'Kaspi cancelOrder failed for ' . $kaspiOrderId . ': ' . $e->getMessage(),
                        'kaspi.orders'
                    );
                }
            } else {
                $errors++;
                Yii::error(
                    'Kaspi import failed for ' . $kaspiOrderId . ': ' . json_encode($importResult),
                    'kaspi.orders'
                );
            }
        }

        return [
            'status'           => 'OK',
            'fetched'          => count($orders),
            'imported'         => $imported,
            'skipped_existing' => $skipped,
            'failed_no_stock'  => $noStock,
            'errors'           => $errors,
            'items'            => $details,
        ];
    }

    /**
     * Создать EcommerceOutbound + позиции + зарезервировать сток под один заказ.
     * Вся операция — в транзакции. При нехватке стока — откат.
     *
     * @param OrderDto $order
     * @param array    $entries нормализованные позиции [{sku, qty, price}, ...]
     * @return array ['status' => 'OK'|'no_stock'|'error', 'outbound_id'?, 'missing'?]
     */
    private function importSingleOrder(OrderDto $order, array $entries)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $outbound = $this->createOutbound($order, $entries);
            $this->createOutboundItems($outbound, $entries);

            $missing = $this->reserveStock($outbound, $entries);
            if (!empty($missing)) {
                $transaction->rollBack();
                return [
                    'status'  => 'no_stock',
                    'missing' => $missing,
                ];
            }

            $transaction->commit();
            return [
                'status'      => 'OK',
                'outbound_id' => (int) $outbound->id,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Kaspi import transaction failed: ' . $e->getMessage(), 'kaspi.orders');
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    private function createOutbound(OrderDto $order, array $entries)
    {
        $now = time();
        $expectedQty = 0;
        foreach ($entries as $e) {
            $expectedQty += (int) $e['qty'];
        }

        $customerName = '';
        $cellPhone    = '';
        if ($order->customer !== null) {
            $customerName = trim(((string) $order->customer->firstName) . ' ' . ((string) $order->customer->lastName));
            $cellPhone    = (string) $order->customer->cellPhone;
        }

        $outbound = new EcommerceOutbound();
        $outbound->client_id              = (int) $this->defaultClientId;
        $outbound->order_number           = 'KASPI-' . substr((string) $order->id, 0, 30);
        $outbound->external_order_number  = (string) $order->id;
        $outbound->expected_qty           = $expectedQty;
        $outbound->customer_name          = $customerName;
        $outbound->phone_mobile1          = $cellPhone;
        $outbound->total_price            = (string) (float) $order->totalPrice;
        $outbound->status                 = Stock::STATUS_OUTBOUND_NEW;
        $outbound->api_status             = 0;
        $outbound->external_kaspi_status  = OrderStatus::ORDER_APPROVED_BY_BANK;
        // OutboundListService::isOrderFromOtherCourierCompany сравнивает это поле
        // с выбранной в UI курьеркой; без него лист отгрузки отвергнет коробку.
        $outbound->client_ShipmentSource  = CourierCompany::PONY_EXPRESS_KASPI;
        // Гейт для ScanningController::actionPackage: transferToCourier (ASSEMBLE)
        // вызываем только для Kaspi-доставки, иначе Kaspi API ответит 400.
        $outbound->is_kaspi_delivery      = $order->isKaspiDelivery === true ? 1 : 0;
        $outbound->data_created_on_client = $order->creationDate > 0 ? (int) floor($order->creationDate / 1000) : $now;
        $outbound->created_at             = $now;
        $outbound->updated_at             = $now;
        $outbound->deleted                = 0;

        if (!$outbound->save(false)) {
            throw new \RuntimeException('Failed to save EcommerceOutbound for Kaspi order ' . $order->id);
        }

        return $outbound;
    }

    private function createOutboundItems(EcommerceOutbound $outbound, array $entries)
    {
        $now = time();
        foreach ($entries as $e) {
            $item = new EcommerceOutboundItem();
            $item->outbound_id    = (int) $outbound->id;
            $item->product_sku    = (string) $e['sku'];
            $item->product_name   = isset($e['product_name'])  ? (string) $e['product_name']  : '';
            $item->product_brand  = isset($e['product_brand']) ? (string) $e['product_brand'] : '';
            $item->product_color  = isset($e['product_color']) ? (string) $e['product_color'] : '';
            $item->product_model  = isset($e['product_model']) ? (string) $e['product_model'] : '';
            $item->expected_qty   = (int) $e['qty'];
            $item->allocated_qty  = 0;
            $item->accepted_qty   = 0;
            $item->status         = 0;
            $item->product_price  = (string) (float) $e['price'];
            $item->created_at     = $now;
            $item->updated_at     = $now;
            $item->deleted        = 0;
            if (!$item->save(false)) {
                throw new \RuntimeException('Failed to save EcommerceOutboundItem for sku ' . $e['sku']);
            }
        }
    }

    /**
     * Резервирует строки ecommerce_stock под каждый sku из заказа и фиксирует
     * allocated_qty/status на outbound+items (чтобы pick-flow видел заказ
     * как FULL_RESERVED и не пытался повторно резервировать).
     *
     * Возвращает массив недостающих позиций [{sku, needed, available}], пустой если ок.
     */
    private function reserveStock(EcommerceOutbound $outbound, array $entries)
    {
        $missing = [];
        $itemIdBySku = [];
        foreach (EcommerceOutboundItem::find()->andWhere(['outbound_id' => (int) $outbound->id])->all() as $it) {
            $itemIdBySku[(string) $it->product_sku] = (int) $it->id;
        }

        $totalReserved = 0;
        foreach ($entries as $e) {
            $sku    = (string) $e['sku'];
            $needed = (int) $e['qty'];
            if ($sku === '' || $needed <= 0) {
                continue;
            }

            $ids = EcommerceStock::find()
                ->select('id')
                ->andWhere(['product_sku' => $sku])
                ->andWhere(['status_availability' => EcommerceStock::STATUS_AVAILABILITY_YES])
                ->andWhere(['deleted' => 0])
                ->limit($needed)
                ->column();

            if (count($ids) < $needed) {
                $missing[] = [
                    'sku'       => $sku,
                    'needed'    => $needed,
                    'available' => count($ids),
                ];
                continue;
            }

            $itemId = isset($itemIdBySku[$sku]) ? (int) $itemIdBySku[$sku] : 0;

            $updates = [
                'outbound_id'         => (int) $outbound->id,
                'outbound_item_id'    => $itemId,
                'status_availability' => EcommerceStock::STATUS_AVAILABILITY_RESERVED,
                'status'              => Stock::STATUS_OUTBOUND_FULL_RESERVED,
                'kaspi_order_status'  => OrderStatus::ORDER_APPROVED_BY_BANK,
                'updated_at'          => time(),
            ];
            if (!empty($e['product_name']))  { $updates['product_name']  = (string) $e['product_name']; }
            if (!empty($e['product_brand'])) { $updates['product_brand'] = (string) $e['product_brand']; }
            if (!empty($e['product_color'])) { $updates['product_color'] = (string) $e['product_color']; }
            if (!empty($e['product_model'])) { $updates['product_model'] = (string) $e['product_model']; }

            EcommerceStock::updateAll($updates, ['id' => $ids]);

            $totalReserved += count($ids);

            if ($itemId > 0) {
                EcommerceOutboundItem::updateAll(
                    [
                        'allocated_qty' => $needed,
                        'status'        => Stock::STATUS_OUTBOUND_FULL_RESERVED,
                        'updated_at'    => time(),
                    ],
                    ['id' => $itemId]
                );
            }
        }

        if (empty($missing) && $totalReserved > 0) {
            EcommerceOutbound::updateAll(
                [
                    'allocated_qty' => $totalReserved,
                    'status'        => Stock::STATUS_OUTBOUND_FULL_RESERVED,
                    'updated_at'    => time(),
                ],
                ['id' => (int) $outbound->id]
            );
        }

        return $missing;
    }

    /**
     * Подменить sku-артикулы из Kaspi на GUID товара из product_v2 и обогатить
     * entry полями товара (product_name/brand/color/model) — чтобы pick-list PDF
     * и EcommerceOutboundItem получили корректные данные, а не пустоту.
     *
     * Если товара нет в product_v2 — sku остаётся как есть, поля остаются пустыми.
     *
     * @param array $entries [{sku, qty, price}, ...]
     * @return array каждый entry: {sku, qty, price, product_name, product_brand, product_color, product_model}
     */
    public static function resolveArticlesToGuids(array $entries)
    {
        if (empty($entries)) {
            return $entries;
        }

        $skus = [];
        foreach ($entries as $e) {
            $skus[] = (string) $e['sku'];
        }
        $skus = array_values(array_unique(array_filter($skus, function ($v) {
            return $v !== '';
        })));
        if (empty($skus)) {
            return $entries;
        }

        $rows = (new Query())
            ->select(['article', 'guid', 'name', 'brand', 'color_name'])
            ->from('product_v2')
            ->andWhere(['or', ['article' => $skus], ['guid' => $skus]])
            ->andWhere(['deleted' => 0])
            ->all();

        $byArticle = [];
        $byGuid = [];
        foreach ($rows as $row) {
            $byArticle[(string) $row['article']] = $row;
            $byGuid[(string) $row['guid']] = $row;
        }

        foreach ($entries as &$e) {
            $raw  = (string) $e['sku'];
            $info = isset($byArticle[$raw]) ? $byArticle[$raw] : (isset($byGuid[$raw]) ? $byGuid[$raw] : null);
            if ($info !== null) {
                $e['sku']           = (string) $info['guid'];
                $e['product_name']  = (string) $info['name'];
                $e['product_brand'] = (string) $info['brand'];
                $e['product_color'] = (string) $info['color_name'];
                $e['product_model'] = (string) $info['article'];
            } else {
                $e['product_name']  = isset($e['product_name'])  ? $e['product_name']  : '';
                $e['product_brand'] = isset($e['product_brand']) ? $e['product_brand'] : '';
                $e['product_color'] = isset($e['product_color']) ? $e['product_color'] : '';
                $e['product_model'] = isset($e['product_model']) ? $e['product_model'] : '';
            }
        }
        unset($e);

        return $entries;
    }

    /**
     * Нормализовать ответ getOrderEntries в массив [{sku, qty, price}, ...].
     */
    private function extractEntries($response)
    {
        $data = isset($response['data']) && is_array($response['data']) ? $response['data'] : [];
        $entries = [];
        foreach ($data as $row) {
            $attrs = isset($row['attributes']) && is_array($row['attributes']) ? $row['attributes'] : [];

            $sku = '';
            if (isset($attrs['offer']['code'])) {
                $sku = (string) $attrs['offer']['code'];
            } elseif (isset($attrs['merchantProductCode'])) {
                $sku = (string) $attrs['merchantProductCode'];
            } elseif (isset($attrs['productCode'])) {
                $sku = (string) $attrs['productCode'];
            }

            $qty = isset($attrs['quantity']) ? (int) $attrs['quantity'] : 0;
            if ($sku === '' || $qty <= 0) {
                continue;
            }

            $price = 0.0;
            if (isset($attrs['basePrice'])) {
                $price = (float) $attrs['basePrice'];
            } elseif (isset($attrs['totalPrice']) && $qty > 0) {
                $price = (float) $attrs['totalPrice'] / $qty;
            }

            $entries[] = [
                'sku'   => $sku,
                'qty'   => $qty,
                'price' => $price,
            ];
        }

        return $entries;
    }
}
