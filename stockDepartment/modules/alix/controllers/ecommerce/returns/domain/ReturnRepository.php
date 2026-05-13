<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\returns\domain;

use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use common\ecommerce\entities\EcommerceStock;
use yii\db\Expression;

class ReturnRepository
{
    private $returnID;
    private $clientId;

    public function __construct($dto = [])
    {
        $this->clientId = isset($dto->clientId) ? $dto->clientId : 0;
    }

    public function getClientID()
    {
        return $this->clientId;
    }

    /**
     * Сколько продуктов в данном коробе по возврату уже отсканировано.
     */
    public function getScannedProductInBox($boxBarcode, $returnId)
    {
        return (int) EcommerceStock::find()->where([
            'return_id' => $returnId,
            'box_address_barcode' => $boxBarcode,
            'status' => [
                EcommerceStock::STATUS_INBOUND_SCANNED,
                EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
            ],
        ])->count();
    }

    public function isOrderExist($orderNumber)
    {
        return EcommerceReturn::find()
            ->andWhere([
                'client_id' => $this->getClientID(),
                'order_number' => $orderNumber,
            ])
            ->andWhere(['deleted' => 0])
            ->exists();
    }

    public function create($data)
    {
        $now = time();

        // Если ручной возврат вводится по реальному номеру отгрузки — подтягиваем
        // исходный outbound, чтобы шапка не была пустой и сканирование могло
        // перезаписать те же физические ячейки EcommerceStock (см.
        // ScanningController::actionScanProductInBox).
        $sourceOutbound = $this->findSourceOutbound((string) $data->orderNumber);

        $return = new EcommerceReturn();
        $return->client_id     = $this->getClientID();
        $return->order_number  = $data->orderNumber;
        $return->status        = ReturnOutboundStatus::_NEW;
        $return->expected_qty  = (int) $data->expectedTotalProductQty;
        $return->accepted_qty  = 0;
        if ($sourceOutbound !== null) {
            $return->outbound_id      = (int) $sourceOutbound->id;
            $return->customer_name    = (string) $sourceOutbound->customer_name;
            $return->city             = (string) $sourceOutbound->city;
            $return->customer_address = self::composeAddress($sourceOutbound);
        } else {
            $return->customer_name    = '';
            $return->city             = '';
            $return->customer_address = '';
        }
        $return->created_at    = $now;
        $return->updated_at    = $now;
        $return->deleted       = 0;
        $return->save(false);

        foreach ($data->items as $item) {
            $rItem = new EcommerceReturnItem();
            $rItem->return_id       = (int) $return->id;
            $rItem->product_barcode = (string) $item->productModel;
            $rItem->expected_qty    = (int) $item->expectedProductQty;
            $rItem->accepted_qty    = 0;
            $rItem->status          = ReturnOutboundStatus::_NEW;
            $rItem->created_at      = $now;
            $rItem->updated_at      = $now;
            $rItem->deleted         = 0;
            $rItem->save(false);
        }

        // Pre-link сток исходной отгрузки на новый возврат: все ещё-непривязанные
        // ячейки этого outbound получают return_id и status_availability=NOT_SET.
        // Иначе после создания шапки сток остаётся в RESERVED (=3) от старого
        // outbound, и сканирование возврата плодит лишние записи вместо
        // перезаписи. Симметрично тому, как это делает Kaspi poll.
        if ($sourceOutbound !== null) {
            EcommerceStock::updateAll(
                [
                    'return_id' => (int) $return->id,
                    'status_availability' => EcommerceStock::STATUS_AVAILABILITY_NOT_SET,
                    'updated_at' => $now,
                ],
                [
                    'and',
                    ['outbound_id' => (int) $sourceOutbound->id],
                    ['or', ['return_id' => 0], ['return_id' => null]],
                    ['deleted' => 0],
                ]
            );
        }

        $this->returnID = (int) $return->id;
        return $this->returnID;
    }

    public function getItemsByReturnId($returnId)
    {
        return EcommerceReturnItem::find()
            ->select('*,(expected_qty - accepted_qty) as order_by')
            ->andWhere(['return_id' => $returnId])
            ->andWhere(['deleted' => 0])
            ->orderBy(new Expression('order_by != 0 DESC'))
            ->asArray()
            ->all();
    }

    /**
     * Найти исходную отгрузку, на которую ссылается ручной возврат.
     * Пользователь обычно вводит либо наш `order_number`, либо `external_order_number`
     * (например, kaspi orderId / маркетплейсовый номер) — пробуем оба варианта
     * в пределах клиента.
     */
    private function findSourceOutbound($orderNumber)
    {
        $orderNumber = trim((string) $orderNumber);
        if ($orderNumber === '') {
            return null;
        }

        return EcommerceOutbound::find()
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere(['deleted' => 0])
            ->andWhere([
                'or',
                ['order_number' => $orderNumber],
                ['external_order_number' => $orderNumber],
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();
    }

    /**
     * Склеить адрес доставки из частей `EcommerceOutbound` в одно строковое поле
     * `EcommerceReturn.customer_address` (там нет разложения по полям). Пустые
     * компоненты опускаем — иначе получим строку из запятых.
     */
    public static function composeAddress(EcommerceOutbound $outbound)
    {
        $parts = [
            (string) $outbound->region,
            (string) $outbound->city,
            (string) $outbound->street,
            (string) $outbound->house !== '' ? 'д. ' . $outbound->house : '',
            (string) $outbound->building !== '' ? 'к. ' . $outbound->building : '',
            (string) $outbound->entrance !== '' ? 'под. ' . $outbound->entrance : '',
            (string) $outbound->flat !== '' ? 'кв. ' . $outbound->flat : '',
            (string) $outbound->zip_code,
        ];
        $parts = array_values(array_filter(array_map('trim', $parts), function ($p) {
            return $p !== '';
        }));
        return implode(', ', $parts);
    }
}
