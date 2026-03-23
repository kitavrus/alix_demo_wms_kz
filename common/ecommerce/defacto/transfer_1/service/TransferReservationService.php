<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.08.2017
 * Time: 15:32
 */
namespace common\ecommerce\defacto\transfer\service;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\constants\StockTransferStatus;
use common\ecommerce\constants\TransferStatus;
use common\ecommerce\entities\EcommerceStock;

class TransferReservationService
{

    public static function run($orderInfo)
    {
        $allocateQty = 0;
        foreach($orderInfo->items as $item) {

            $item->allocated_qty = 0;
            $item->status = TransferStatus::RESERVING;

            $stocks = EcommerceStock::find()
                ->andWhere([
                    'client_id' => 2,
                    'client_product_sku' =>$item->product_sku,
                    'status_availability' =>StockAvailability::YES,
                    'condition_type' =>StockConditionType::UNDAMAGED,
                ])
                ->andWhere([
                    'transfer_box_check_step' => $orderInfo->order->id,
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
                    $stock->transfer_id =  $orderInfo->order->id;
                    $stock->transfer_item_id = $item->id;
                    $stock->status_transfer = StockTransferStatus::FULL_RESERVED;
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

            $item->status = TransferStatus::PART_RESERVED;

            if( $item->allocated_qty == $item->expected_qty ) {
                $item->status = TransferStatus::FULL_RESERVED;
            }

            $item->save(false);
        }

        $orderInfo->order->allocated_qty = $allocateQty;
        $orderInfo->order->status = TransferStatus::PART_RESERVED;

        if($orderInfo->order->allocated_qty ==  $orderInfo->order->expected_qty) {
            $orderInfo->order->status = TransferStatus::FULL_RESERVED;
        }

        $orderInfo->order->save(false);
    }

    public static function run_Serdar($orderInfo)
    {
            $allocateQty = 0;
            foreach($orderInfo->items as $item) {

                $item->allocated_qty = 0;
                $item->status = TransferStatus::RESERVING;

                $stocks = EcommerceStock::find()
                            ->andWhere([
                                'note_message2' => 'transfer2',
                                'client_product_sku' =>$item->product_sku,
                            ])
                             ->andWhere([
                                'transfer_box_check_step' => $orderInfo->order->id,
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
                        $stock->transfer_id =  $orderInfo->order->id;
                        $stock->transfer_item_id = $item->id;
                        $stock->status_transfer = StockTransferStatus::FULL_RESERVED;
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

                $item->status = TransferStatus::PART_RESERVED;

                if( $item->allocated_qty == $item->expected_qty ) {
                    $item->status = TransferStatus::FULL_RESERVED;
                }

                $item->save(false);
            }

            $orderInfo->order->allocated_qty = $allocateQty;
            $orderInfo->order->status = TransferStatus::PART_RESERVED;

            if($orderInfo->order->allocated_qty ==  $orderInfo->order->expected_qty) {
                $orderInfo->order->status = TransferStatus::FULL_RESERVED;
            }

            $orderInfo->order->save(false);
    }



}