<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.08.2017
 * Time: 15:32
 */

namespace common\clientObject\main\outbound\service;


use common\modules\stock\models\Stock;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class OutboundReservationService
{
    public static function run($orderInfo)
    {
            $allocateQty = 0;
            foreach($orderInfo->items as $item) {

                $item->allocated_qty = 0;
                $item->status = Stock::STATUS_OUTBOUND_RESERVING;

                $availableBoxList = self::strategy($orderInfo, $item);
                
				//$availableBoxList = [];
				
                if (!empty($availableBoxList)) {

//                    $productQty = ArrayHelper::getValue($availableBoxList,'0.productQty',null);
//                    $productBarcode = ArrayHelper::getValue($availableBoxList,'0.productBarcode',null);
                    $boxAddress = ArrayHelper::getValue($availableBoxList,'0.boxAddress',null);
                    $placeAddress = ArrayHelper::getValue($availableBoxList,'0.placeAddress',null);

                    $stocks = Stock::find()
                                ->andWhere([
                                    'client_id' => $orderInfo->order->client_id,
                                    'product_barcode' => $item->product_barcode,
                                    'status_availability' => Stock::STATUS_AVAILABILITY_YES,
                                    'primary_address' =>$boxAddress,
                                    'secondary_address' =>$placeAddress,
                                ])
								//->andWhere(['inbound_order_id'=>79609])
                                ->andWhere('condition_type IS NULL OR condition_type IN (0,1)')
                                ->orderBy( new Expression(' FIELD(inbound_order_id,59714,59715,59733,59734) DESC'))
                                ->limit($item->expected_qty)
                                ->all();
                } else {

                    $stocks = Stock::find()
                                ->andWhere([
                                    'client_id' => $orderInfo->order->client_id,
                                    'product_barcode' =>$item->product_barcode,
                                    'status_availability' =>Stock::STATUS_AVAILABILITY_YES,
                                ])
								//->andWhere(['inbound_order_id'=>79609])
                                ->andWhere('condition_type IS NULL OR condition_type IN (0,1)')
                                ->orderBy( new Expression(' FIELD(inbound_order_id,59714,59715,59733,59734) DESC'))
                                ->limit($item->expected_qty)
                                ->all();
                }

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


    private static function strategy($orderInfo,$item) {

        $productList = self::getProductsForReservation($orderInfo,$item->product_barcode);

        $expectedProductQty = $item->expected_qty;
        $result = [];

        if(empty($productList) || !is_array($productList)) {
            return $result;
        }

        foreach($productList as $productInBox) {

                $productQty = ArrayHelper::getValue($productInBox,'productQty',null);
                $productBarcode = ArrayHelper::getValue($productInBox,'product_barcode',null);
                $boxAddress = ArrayHelper::getValue($productInBox,'primary_address',null);
                $placeAddress = ArrayHelper::getValue($productInBox,'secondary_address',null);
                $addressSortOrder = ArrayHelper::getValue($productInBox,'address_sort_order',null);

                if($productQty >= $expectedProductQty) {
                    $result[] = [
                        'productQty'=>$productQty,
                        'productBarcode'=>$productBarcode,
                        'boxAddress'=>$boxAddress,
                        'placeAddress'=>$placeAddress,
                        'addressSortOrder'=>$addressSortOrder,
                    ];
                    break;
                }
        }

        return $result;
    }

    private static function getProductsForReservation($orderInfo,$productBarcode)
    {
      return Stock::find()
            ->select('SQL_CALC_FOUND_ROWS COUNT(`product_barcode`) as productQty, product_barcode, primary_address, secondary_address,address_sort_order')
            ->andWhere([
                'client_id' => $orderInfo->order->client_id,
                'product_barcode' => trim($productBarcode),
                'status_availability' =>Stock::STATUS_AVAILABILITY_YES,
            ])
			//->andWhere(['inbound_order_id'=>79609])
            ->andWhere('condition_type IS NULL OR condition_type IN (0,1)')
            ->groupBy('primary_address, secondary_address')
            ->orderBy('COUNT(`product_barcode`) DESC')
            ->asArray()
            ->all();
    }
}