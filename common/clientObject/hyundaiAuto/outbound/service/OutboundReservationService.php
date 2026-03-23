<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.08.2017
 * Time: 15:32
 */

namespace common\clientObject\hyundaiAuto\outbound\service;


use common\modules\stock\models\Stock;

class OutboundReservationService
{
    public static function run($orderInfo)
    {
            $allocateQty = 0;
            foreach($orderInfo->items as $item) {

                $item->allocated_qty = 0;
                $item->status = Stock::STATUS_OUTBOUND_RESERVING;

                $stocks = Stock::find()
                            ->andWhere([
                                'client_id' => $orderInfo->order->client_id,
                                'product_barcode' =>$item->product_barcode,
                                'status_availability' =>Stock::STATUS_AVAILABILITY_YES,
                            ])
                            ->limit($item->expected_qty)
                            ->all();

                if ($stocks) {
                    foreach($stocks as $stock) {
                        // ORDER ITEM
                        $item->allocated_qty +=1;
                        $allocateQty++;
                        //STOCK
                        $stock->outbound_order_id =  $orderInfo->order->id;
                        $stock->outbound_order_item_id = $item->id;
                        $stock->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
                        $stock->status_availability = Stock::STATUS_AVAILABILITY_RESERVED;
                        $stock->save(false);
                    }
                }

                $item->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

                if( $item->allocated_qty == $item->expected_qty ) {
                    $item->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
                }

                $item->save(false);
            }

            $orderInfo->order->allocated_qty = $allocateQty;
            $orderInfo->order->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

            if($orderInfo->order->allocated_qty ==  $orderInfo->order->expected_qty) {
                $orderInfo->order->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
            }

            $orderInfo->order->save(false);
    }
}