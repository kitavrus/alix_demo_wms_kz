<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:14
 */

namespace common\clientObject\main\outbound\repository;


use common\clientObject\constants\Constants;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;
use yii\data\ActiveDataProvider;
class OutboundRepository
{
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
    public function isOrderExist($orderNumber)
    {
        return OutboundOrder::find()
            ->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])
//            ->andWhere(' created_at > 1546300861')
            ->andWhere(' created_at > 1577836861')
            ->exists();
    }
//
//    public function getClientID()
//    {
//        return Constants::getCarPartClientIDs();
//    }

    public function getOrdersForPrintPickList()
    {
        $query = OutboundOrder::find()->andWhere([
            "client_id" => Constants::getCarPartClientIDs(),
            'status' => [
                Stock::STATUS_OUTBOUND_NEW,
                Stock::STATUS_OUTBOUND_FULL_RESERVED,
                Stock::STATUS_OUTBOUND_PART_RESERVED,
                Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
            ]
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
    }

    public function getOrderInfo($id)
    {
        $order = OutboundOrder::find()->andWhere([
            "id" => $id,
        ])->one();
        $items = OutboundOrderItem::find()->andWhere(['outbound_order_id' => $order->id])->all();

        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;

        return $result;
    }
}