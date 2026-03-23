<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 31.07.2017
 * Time: 21:19
 */

namespace stockDepartment\modules\wms\managers\miele;


use common\modules\inbound\models\InboundOrderItem;
use common\modules\movement\models\MovementHistory;
use common\modules\movement\models\MovementItems;
use common\modules\outbound\models\OutboundOrderItem;

class MovementHistoryService
{
    public function makeInboundOrder($inbound)
    {
        $items = InboundOrderItem::find()->andWhere(['inbound_order_id'=>$inbound->id])->all();
        foreach($items as $item) {
            $history = MovementHistory::find()->andWhere([
                'client_id'=>$inbound->client_id,
                'inbound_id'=>$inbound->id,
                'client_order_id'=>$inbound->client_order_id,
                'product_barcode'=>$item->product_barcode
            ])->one();

            if(!$history) {
                $history = new MovementHistory();
            }

            $history->client_id = $inbound->client_id;
            $history->stock_id  = 0;
            $history->inbound_id = $inbound->id;
            $history->outbound_id = 0;
            $history->from_zone_id = 0;
            $history->to_zone_id = $inbound->zone;
            $history->client_order_id = $inbound->client_order_id;
            $history->product_barcode = $item->product_barcode;
            $history->product_model = $item->product_model;
            $history->product_sku = $item->product_sku;
            $history->product_qty = $item->accepted_qty;
            $history->save(false);
        }
    }

    public function makeOutboundOrder($outbound) {
        $items = OutboundOrderItem::find()->andWhere(['outbound_order_id'=>$outbound->id])->all();
        foreach($items as $item) {
            $history = MovementHistory::find()->andWhere([
                'client_id'=>$outbound->client_id,
                'inbound_id'=>$outbound->id,
                'client_order_id'=>$outbound->client_order_id,
                'product_barcode'=>$item->product_barcode
            ])->one();

            if(!$history) {
                $history = new MovementHistory();
            }

            $history->client_id = $outbound->client_id;
            $history->stock_id  = 0;
            $history->inbound_id = 0;
            $history->outbound_id = $outbound->id;
            $history->from_zone_id = $outbound->zone;
            $history->to_zone_id = 0;
            $history->client_order_id = $outbound->client_order_id;
            $history->product_barcode = $item->product_barcode;
            $history->product_model = $item->product_model;
            $history->product_sku = $item->product_sku;
            $history->product_qty = $item->accepted_qty;
            $history->save(false);
        }
    }

    public function makeMovementOrder($movement) {
        $items = MovementItems::find()->andWhere(['movement_id'=>$movement->id])->all();
        foreach($items as $item) {
            $history = MovementHistory::find()->andWhere([
                'client_id'=>$movement->client_id,
                'movement_id'=>$movement->id,
                'client_order_id'=>$movement->client_order_id,
                'product_barcode'=>$item->product_barcode
            ])->one();

            if(!$history) {
                $history = new MovementHistory();
            }

            $history->client_id = $movement->client_id;
            $history->stock_id  = 0;
            $history->inbound_id = 0;
            $history->outbound_id = 0;
            $history->movement_id = $movement->id;
            $history->from_zone_id = $movement->from_zone;
            $history->to_zone_id = $movement->to_zone;
            $history->client_order_id = $movement->client_order_id;
            $history->product_barcode = $item->product_barcode;
            $history->product_model = $item->product_model;
            $history->product_sku = $item->product_sku;
            $history->product_qty = $item->accepted_qty;
            $history->save(false);
        }
    }
}