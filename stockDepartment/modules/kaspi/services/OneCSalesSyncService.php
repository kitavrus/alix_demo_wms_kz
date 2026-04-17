<?php

namespace stockDepartment\modules\kaspi\services;

use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceOutboundItem;
use Yii;
use yii\base\Component;

/**
 * Передача выполненных Kaspi-заказов в 1С.
 *
 * Источник — EcommerceOutbound с one_c_status='PENDING' (пометка ставится
 * OrderStatusSyncService при COMPLETED в Kaspi). Для каждого собираем payload
 * и вызываем Alix1CApiService::postSale().
 *
 * Сейчас 1С endpoint — заглушка (см. Alix1CApiService::postSale), поэтому
 * поведение: лог + перевод one_c_status → SENT, sent_to_1c_at = now.
 * При подключении реальной 1С поведение останется тем же, поменяется только
 * содержимое ответа.
 */
class OneCSalesSyncService extends Component
{
    /** @var Alix1CApiService|null */
    public $api;

    /** @var int максимум заказов за один запуск крона */
    public $batchLimit = 100;

    public function init()
    {
        parent::init();
        $module = Yii::$app->getModule('kaspi');
        if ($this->api === null && $module !== null) {
            $this->api = $module->get('alix1cApiService');
        }
        if (!$this->api instanceof Alix1CApiService) {
            $this->api = new Alix1CApiService();
            $this->api->init();
        }
    }

    /**
     * Пройтись по PENDING заказам и отправить в 1С.
     *
     * @return array
     */
    public function syncPendingSales()
    {
        $candidates = EcommerceOutbound::find()
            ->andWhere(['one_c_status' => 'PENDING'])
            ->andWhere(['sent_to_1c_at' => null])
            ->andWhere(['deleted' => 0])
            ->limit((int) $this->batchLimit)
            ->all();

        $sent   = 0;
        $errors = 0;

        foreach ($candidates as $outbound) {
            $payload = $this->buildSalePayload($outbound);

            try {
                $response = $this->api->postSale($payload);
            } catch (\Exception $e) {
                $outbound->one_c_status   = 'ERROR';
                $outbound->one_c_response = substr($e->getMessage(), 0, 5000);
                $outbound->updated_at     = time();
                $outbound->save(false);
                $errors++;
                Yii::error(
                    'Alix 1C postSale failed for outbound ' . $outbound->id . ': ' . $e->getMessage(),
                    'kaspi.orders'
                );
                continue;
            }

            $statusOk = isset($response['status']) && $response['status'] === 'OK';
            if ($statusOk) {
                $outbound->one_c_status   = 'SENT';
                $outbound->one_c_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                $outbound->sent_to_1c_at  = time();
                $outbound->updated_at     = time();
                $outbound->save(false);
                $sent++;
            } else {
                $outbound->one_c_status   = 'ERROR';
                $outbound->one_c_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                $outbound->updated_at     = time();
                $outbound->save(false);
                $errors++;
            }
        }

        return [
            'status' => 'OK',
            'picked' => count($candidates),
            'sent'   => $sent,
            'errors' => $errors,
        ];
    }

    private function buildSalePayload(EcommerceOutbound $outbound)
    {
        $items = EcommerceOutboundItem::find()
            ->andWhere(['outbound_id' => (int) $outbound->id])
            ->andWhere(['deleted' => 0])
            ->all();

        $itemsPayload = [];
        foreach ($items as $item) {
            $itemsPayload[] = [
                'sku'         => (string) $item->product_sku,
                'name'        => (string) $item->product_name,
                'barcode'     => (string) $item->product_barcode,
                'quantity'    => (int) $item->expected_qty,
                'price'       => (float) $item->product_price,
            ];
        }

        return [
            'order_number'    => (string) $outbound->order_number,
            'kaspi_order_id'  => (string) $outbound->external_order_number,
            'completed_at'    => (int) $outbound->updated_at,
            'customer'        => [
                'name'  => (string) $outbound->customer_name,
                'phone' => (string) $outbound->phone_mobile1,
            ],
            'items'           => $itemsPayload,
            'total_price'     => (float) $outbound->total_price,
        ];
    }
}
