<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.08.2017
 * Time: 15:32
 */
namespace common\ecommerce\defacto\outbound\service;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\constants\OutboundStatus;
use common\ecommerce\entities\EcommerceStock;

class OutboundReservationService
{
    public static function run($orderInfo)
    {
            $allocateQty = 0;
            foreach($orderInfo->items as $item) {

                $item->allocated_qty = 0;
                $item->status = OutboundStatus::RESERVING;

                $stocks = EcommerceStock::find()
                            ->andWhere([
                                'client_id' => $orderInfo->order->client_id,
//                                'product_barcode' =>$item->product_barcode,
                                'client_product_sku' =>$item->product_sku,
                                'status_availability' =>StockAvailability::YES,
                                'condition_type' =>StockConditionType::UNDAMAGED,
                            ])
                            ->orderBy('place_address_sort1')
                            ->limit($item->expected_qty)
                            ->all();

                if ($stocks) {
                    foreach($stocks as $stock) {
                        // ORDER ITEM
                        $item->allocated_qty +=1;
                        $allocateQty++;
                        //STOCK
                        $stock->outbound_id =  $orderInfo->order->id;
                        $stock->outbound_item_id = $item->id;
                        $stock->status_outbound = StockOutboundStatus::FULL_RESERVED;
                        $stock->status_availability = StockAvailability::RESERVED;
                        $stock->save(false);

                        $item->product_barcode = $stock->product_barcode;
                    }
                } else {
                   $stock =  EcommerceStock::find()->andWhere([
                        'client_id' => $orderInfo->order->client_id,
                        'client_product_sku' =>$item->product_sku,
                    ])->one();
                    if($stock) {
                        $item->product_barcode = $stock->product_barcode;
                    }
                }

                $item->status = OutboundStatus::PART_RESERVED;

                if( $item->allocated_qty == $item->expected_qty ) {
                    $item->status = OutboundStatus::FULL_RESERVED;
                }

                $item->save(false);
            }

            $orderInfo->order->allocated_qty = $allocateQty;
            $orderInfo->order->status = OutboundStatus::PART_RESERVED;

            if($orderInfo->order->allocated_qty ==  $orderInfo->order->expected_qty) {
                $orderInfo->order->status = OutboundStatus::FULL_RESERVED;
            }

            $orderInfo->order->save(false);
    }
}