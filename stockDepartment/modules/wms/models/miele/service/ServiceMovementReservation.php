<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.08.2017
 * Time: 15:32
 */

namespace stockDepartment\modules\wms\models\miele\service;


use common\modules\movement\models\MovementConstant;
use common\modules\movement\models\MovementPickListStock;
use common\modules\stock\models\Stock;

class ServiceMovementReservation
{
    public static function run($orderInfo)
    {
        $allocateQty = 0;
        foreach ($orderInfo->items as $item) {

            $fabAllocated = trim($item->field_extra1);

            $stocks = Stock::find()->andWhere([
                    'client_id' => $orderInfo->order->client_id,
                    'product_barcode' => $item->product_barcode,
                    'zone' => $orderInfo->order->from_zone,
                    'status_availability' => Stock::STATUS_AVAILABILITY_YES
                ])->filterWhere([
                    'field_extra1'=>$fabAllocated
                ])
                ->limit($item->expected_qty)
                ->all();

            if ($stocks) {
                foreach ($stocks as $stock) {
                    // ORDER ITEM
                    $item->allocated_qty += 1;
                    $allocateQty++;
                    //STOCK
                    $stock->zone = $orderInfo->order->to_zone;
                    $stock->field_extra2 = ($fabAllocated ? $fabAllocated : '');
//                    $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
                    $stock->save(false);
                    // MOVEMENT LIST
                    $movPickListToStock = new MovementPickListStock();
                    $movPickListToStock->movement_id = $orderInfo->order->id;
                    $movPickListToStock->stock_id = $stock->id;
                    $movPickListToStock->movement_pick_id = 0;

                    $movPickListToStock->product_name = $stock->product_name;
                    $movPickListToStock->product_barcode = $stock->product_barcode;
                    $movPickListToStock->product_model = $stock->product_model;
                    $movPickListToStock->product_sku = $stock->product_sku;

                    $movPickListToStock->box = $stock->primary_address;
                    $movPickListToStock->address = $stock->secondary_address;
                    $movPickListToStock->status = MovementConstant::STATUS_PICK_LIST_STOCK_NEW;
                    $movPickListToStock->save(false);
                }
            }

            $item->status = MovementConstant::STATUS_PRINT_PICK_LIST;
            if ($item->allocated_qty == $item->expected_qty) {
                $item->status = MovementConstant::STATUS_RESERVED;
            }

            $item->save(false);
        }

        $orderInfo->order->allocated_qty = $allocateQty;
        $orderInfo->order->status = MovementConstant::STATUS_PRINT_PICK_LIST;

        if ($orderInfo->order->allocated_qty == $orderInfo->order->expected_qty) {
            $orderInfo->order->status = MovementConstant::STATUS_RESERVED;
        }

        $orderInfo->order->save(false);
    }
}