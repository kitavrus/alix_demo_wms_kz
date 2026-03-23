<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.07.2017
 * Time: 8:00
 */

namespace stockDepartment\modules\wms\managers\miele;


class StockDTO
{
    //OK
    public function prepareGetStock($request)
    {
        return [
            'date' => isset($request->date) ? $request->date : '',
        ];
    }

    //
    public function makeResponseGetStock($dto)
    {
        $result = [
            'GetStockResult' => [
                'Запись' => []
            ]
        ];

        if($dto) {
            foreach($dto as $item) {
                //TODO Как тут группировать по зоне и по мат номеру?
                $result['GetStockResult']['Запись'][] = [
                        'МатНомер' => $item->product_sku, // "06165000",
                        'Артикул' => $item->product_model,// "62782410",
                        'Зона' =>  $item->zone, // 0
                        'Количество' => $item->product_barcode_count,  //$item->qty //5,
                ];
            }
        }

        return $result;
    }
    //---------------------------
    //OK
    public function parseRequestGetSerial($request)
    {
        return [
            'date' => isset($request->date) ? $request->date : '',
            'materialNo' => isset($request->materialNo) ? $request->materialNo : '',
            'articul' => isset($request->articul) ? $request->articul : '',
        ];
    }

    //
    public function makeResponseGetSerial($dto)
    {
        $result = [
            'GetSerialStockResult' => [
                'Запись' => []
            ]
        ];

        if($dto) {
            foreach($dto as $item) {
                $result['GetSerialStockResult']['Запись'][] = [
                    'МатНомер' => $item->product_sku, // "06165000",
                    'Артикул' => $item->product_model,// "62782410",
                    'Зона' =>  $item->zone, // 0
                    'ФабНомер' =>  $item->field_extra2 //zxzx2222,
                ];
            }
        }

        return $result;
    }

}