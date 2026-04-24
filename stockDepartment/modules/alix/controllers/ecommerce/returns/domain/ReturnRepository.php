<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\returns\domain;

use common\ecommerce\constants\ReturnOutboundStatus;
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

        $return = new EcommerceReturn();
        $return->client_id     = $this->getClientID();
        $return->order_number  = $data->orderNumber;
        $return->status        = ReturnOutboundStatus::_NEW;
        $return->expected_qty  = (int) $data->expectedTotalProductQty;
        $return->accepted_qty  = 0;
        $return->customer_name = '';
        $return->city          = '';
        $return->customer_address = '';
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
}
