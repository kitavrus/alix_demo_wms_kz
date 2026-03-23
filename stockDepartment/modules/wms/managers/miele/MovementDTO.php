<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.07.2017
 * Time: 8:00
 */

namespace stockDepartment\modules\wms\managers\miele;


use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\movement\models\Movement;
use common\modules\movement\models\MovementItems;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\ConstantZone;

class MovementDTO
{
    public function prepareRequestSendOrder($request)
    {
        $returnOrderHeader = [
            'client_order_id' => $request->order->Идентификатор, // 8e45048d-6b8f-11e7-a28a-94de80bd5cf8
            'order_number' => $request->order->НомерДокументаПрообраза, // 00000000138
            'from_zone' => $request->order->ЗонаОтправки, // 0
            'to_zone' => $request->order->ЗонаПриемки, // 1
            'client_datetime' => $request->order->Дата, // 2017-07-18T14:47:20
            'status' => $request->order->Статус, // 1
            'comments' => $request->order->Комментарий, // Новый тест 3
        ];

        $extraFields = new \stdClass();
        $extraFields->Идентификатор =  $request->order->Идентификатор;
        $extraFields->НомерДокументаПрообраза =  $request->order->НомерДокументаПрообраза;
        $extraFields->СтрочноеПредставлениеДокументаПрообраза =  $request->order->СтрочноеПредставлениеДокументаПрообраза;
        $extraFields->ЗонаПриемки =  $request->order->ЗонаПриемки;
        $extraFields->ЗонаОтправки =  $request->order->ЗонаОтправки;
        $extraFields->Дата =  $request->order->Дата;
        $extraFields->Статус =  $request->order->Статус;
        $extraFields->Комментарий =  $request->order->Комментарий;
        $returnOrderHeader['extra_fields'] = serialize($extraFields);

        $returnOrderItems = [];
        if(!empty($request->order->Спецификация)) {
            file_put_contents('MovementDTO-makeResponseGetOrders-data-items.log',print_r($request->order->Спецификация,true),FILE_APPEND);

            if(isset($request->order->Спецификация->МатНомер)) {
                $returnOrderItems[] = [
                    'product_sku'=>  $request->order->Спецификация->МатНомер,
                    'product_model'=>  $request->order->Спецификация->Артикул,
                    'field_extra1'=>  $request->order->Спецификация->ФабНомер,
                    'field_extra2'=>  $request->order->Спецификация->КомНомер,
                    'field_extra3'=>  $request->order->Спецификация->НомерГТД,
                    'expected_qty'=>  $request->order->Спецификация->Количество,
                    'field_extra4'=>  $request->order->Спецификация->КоличествоНеадаптированное,
                    'field_extra5'=>  $request->order->Спецификация->КоличествоБрак,
                    'product_serialize_data'=>  serialize($request->order->Спецификация),
                ];
            } else {
                foreach($request->order->Спецификация as $key=>$item) {
                    file_put_contents('MovementDTO-prepareRequestSendOrder.log',print_r($item,true),FILE_APPEND);
                    $returnOrderItems[] = [
                        'product_sku'=>  $item->МатНомер,
                        'product_model'=>  $item->Артикул,
                        'field_extra1'=>  $item->ФабНомер,
                        'field_extra2'=>  $item->КомНомер,
                        'field_extra3'=>  $item->НомерГТД,
                        'expected_qty'=>  $item->Количество,
                        'field_extra4'=>  $item->КоличествоНеадаптированное,
                        'field_extra5'=>  $item->КоличествоБрак,
                        'product_serialize_data'=>  serialize($item),
                    ];
                }
            }
        }

        return ['order'=>$returnOrderHeader,'items'=>$returnOrderItems];
    }

    public function makeResponseSendOrder()
    {
        $std = [
            'SendMovementOrderResponse' => true // "что должно быть в ответе"
        ];
        return $std;
    }

    public function prepareRequestGetOrders($request)
    {
        $ids = [];
        if(isset($request->idList->Идентификатор) && is_array($request->idList->Идентификатор)) {
            foreach($request->idList->Идентификатор as $item) {
                $ids[] = $item;
            }
        } else {
            $ids[] = $request->idList->Идентификатор;
        }

        return [
            'ids' => $ids
        ];
    }

    public function makeResponseGetOrders($movements) {
        $result = [];
        foreach ($movements as $key=>$movement) {
            $result['GetMovementOrdersResult']['Запись'] = $this->makeResponseHeader($movement['order']);
            $result['GetMovementOrdersResult']['Запись']['Спецификация'] = $this->makeResponseSpecification($movement['items'],$movement['order']);
        }

        return $result;
    }

    public function makeResponseGetChangedOrders($movements) {
        $result = [];
        foreach ($movements as $key=>$movement) {
            $result['GetChangedMovementOrdersResult']['Запись'] = $this->makeResponseHeader($movement['order']);
            $result['GetChangedMovementOrdersResult']['Запись']['Спецификация'] = $this->makeResponseSpecification($movement['items'],$movement['order']);
        }
        return $result;
    }

    private function makeResponseHeader($order) {
        $orderExtraField = unserialize($order->extra_fields);
        return  [
            'Идентификатор' => $order->client_order_id, //"8e45048d-6b8f-11e7-a28a-94de80bd5cf8",
            'НомерДокументаПрообраза' => $order->order_number,  //"00000000138",
            'СтрочноеПредставлениеДокументаПрообраза' => $orderExtraField->СтрочноеПредставлениеДокументаПрообраза, "Реализация ТМЗ и услуг 00000000138 от 05.07.2017 12:03:56",
            'ЗонаПриемки' => $order->zone_in,//false,
            'ЗонаОтправки' => $order->zone_out,// false,
            'Дата' => $order->client_datetime,// "2017-07-05T12:03:56",
            'Статус' => $order->status,// 3,
            'Комментарий' => $order->comments,// "Тесть пройден",
        ];
    }

    private function makeResponseSpecification($items,$movementOrder)
    {
        $result = [];
        if($items) {
            foreach($items as $item) {
//                $specification = unserialize($item->product_serialize_data);
                $result [] = [
                    'МатНомер' => $item->product_sku, //  "06165000",
                    'Артикул' => $item->product_model, // $specification['Артикул'],// "62782410",
                    'ФабНомер' => $item->field_extra1,//  $specification['ФабНомер'],// "",
                    'КомНомер' => $item->field_extra2,//  $specification['КомНомер'],// "",
                    'НомерГТД' => $item->field_extra3,// $specification['НомерГТД'],// "",
                    'Количество' => ($movementOrder->status == ConstantZone::STATUS_COMPLETE ? $item->accepted_qty : $item->expected_qty), //$item->expected_qty,//  $specification['Количество'],// 8,
                    'КоличествоНеадаптированное' => $item->field_extra4,//  $specification['КоличествоНеадаптированное'],// 0,
                    'КоличествоБрак' => $item->field_extra5,//  $specification['КоличествоБрак'],// 0,
                ];
            }
        }
        return $result;
    }

    public function prepareRequestCancelOrder($request)
    {
        return [
            'id'=> (isset($request->id)? $request->id : 0)
        ];
    }

    public function makeResponseCancelOrder()
    {
        $std = [
            'CancelMovementOrderResponse' => true
        ];

        return $std;
    }

    public function parseMarkAsUnchangedOrder($request)
    {
        return [
            'id' => isset($request->id) ? $request->id : null, //'736beb9a-6158-11e7-9c01-94de80bd5cf8',
            'status' => isset($request->status) ? $request->status : null, // 3
        ];
    }

    public function makeResponseMarkAsUnchangedOrder() {

        $std = [
            'MarkAsUnchangedMovementOrderResult' => true
        ];

        return $std;
    }

    public function parseRequestGetProduct($request)
    {
        file_put_contents('MovementDTO-parseRequestGetProduct.log',print_r($request,true),FILE_APPEND);
        return [
            'beginDate' => isset($request->beginDate) ? $request->beginDate : null, //2017-07-01T00:00:00
            'endDate' => isset($request->endDate) ? $request->endDate : null, // 2017-07-06T00:00:00
            'materialNo' => isset($request->materialNo) ? $request->materialNo : null, //
            'articul' => isset($request->articul) ? $request->articul : null, //
        ];
    }

    public function makeResponseGetProducts($data)
    {
        $result = [];
        $result['GetProductMovementResult']['Запись'] = $this->makeResponseProductRecords($data);
        return $result;
    }

    private function makeResponseProductRecords($data) {
        $result = [];
        $this->testMakeMovementResponseGetProductMovement('2cd483dc-7133-11e7-8696-94de80bd5cf8',$result);
        $this->testMakeOutboundResponseGetProductMovement('2cd483db-7133-11e7-8696-94de80bd5cf8',$result);
        $this->testMakeInboundResponseGetProductMovement('2cd483da-7133-11e7-8696-94de80bd5cf8',$result);
        return $result;

//        return $this->testMakeMovementResponseGetProductMovement('2cd483dc-7133-11e7-8696-94de80bd5cf8',$result);
//        return $this->testMakeOutboundResponseGetProductMovement('2cd483db-7133-11e7-8696-94de80bd5cf8');
//        return $this->testMakeInboundResponseGetProductMovement('2cd483da-7133-11e7-8696-94de80bd5cf8');
    }


    protected function testMakeMovementResponseGetProductMovement($id,&$result)
    {
        $order = Movement::find()->andWhere(['client_order_id'=>$id])->one();
        $items = MovementItems::find()->andWhere(['movement_id'=>$order->id])->all();
        $rows = [];
        foreach($items as $item) {

            $row = new \stdClass();
            $row->client_id = $order->client_id;
            $row->stock_id  = 0;
            $row->inbound_id = 0;
            $row->outbound_id = 0;
            $row->movement_id = $order->id;
            $row->from_zone_id = ConstantZone::CATEGORY_A;
            $row->to_zone_id = ConstantZone::CATEGORY_B;
            $row->client_order_id = $order->client_order_id;
            $row->barcode = $item->product_barcode;
            $row->product_model = $item->product_model;
            $row->product_sku = $item->product_sku;
            $row->qty = $item->accepted_qty;
            $row->datetime = date('c',$item->created_at);

            $rows[] = $row;
        }

        foreach($rows as $row) {
//            if(!empty($row->zone_from_id) && !empty($row->zone_to_id)) {
                $result[] = [
                    'ДатаВремя' => $row->datetime,// "2017-07-04T13:50:07",
                    'МатНомер' => $row->product_sku,// "010101",
                    'Артикул' => $row->product_model, //"020202",
                    'Зона' => $row->from_zone_id,//"A",//0,
                    'IDдокумента' => $row->client_order_id,// "736beb9c-6158-11e7-9c01-94de80bd5cf8",
                    'Приход' => 0,
                    'Расход' => $row->qty,
                ];

                $result[] = [
                    'ДатаВремя' => $row->datetime,// "2017-07-04T13:50:07",
                    'МатНомер' => $row->product_sku,// "010101",
                    'Артикул' => $row->product_model, //"020202",
                    'Зона' => $row->to_zone_id,//"A",//0,
                    'IDдокумента' => $row->client_order_id,// "736beb9c-6158-11e7-9c01-94de80bd5cf8",
                    'Приход' => $row->qty,
                    'Расход' => 0,
                ];

//            } else {
//            }
        }

        file_put_contents('MovementDTO-testMakeMovementResponseGetProductMovement-row.log',print_r($rows,true),FILE_APPEND);
        file_put_contents('MovementDTO-testMakeMovementResponseGetProductMovement-result.log',print_r($result,true),FILE_APPEND);


        return $result;
    }

    protected function testMakeOutboundResponseGetProductMovement($id,&$result)
    {
        $order = OutboundOrder::find()->andWhere(['client_order_id'=>$id])->one();
        $items = OutboundOrderItem::find()->andWhere(['outbound_order_id'=>$order->id])->all();
//        $result = [];
        $rows = [];
        foreach($items as $item) {
            $row = new \stdClass();
            $row->client_id = $order->client_id;
            $row->stock_id  = 0;
            $row->inbound_id = 0;
            $row->outbound_id = $order->id;
            $row->zone_id = ConstantZone::CATEGORY_A;
            $row->clinet_order_id = $order->client_order_id;
            $row->barcode = $item->product_barcode;
            $row->product_model = $item->product_model;
            $row->product_sku = $item->product_sku;
            $row->qty = $item->accepted_qty;
            $row->datetime = date('c',$item->created_at);

            $rows[] = $row;
        }

        foreach($rows as $row) {
            $result[] = [
                'ДатаВремя' => $row->datetime,// "2017-07-04T13:50:07",
                'МатНомер' =>  $row->product_sku,// "010101",
                'Артикул' => $row->product_model, //"020202",
                'Зона' => $row->zone_id ,//"A",//0,
                'IDдокумента' =>  $row->clinet_order_id,// "736beb9c-6158-11e7-9c01-94de80bd5cf8",
                'Приход' => 0,
                'Расход' =>$row->qty,
            ];
        }

        file_put_contents('MovementDTO-testMakeOutboundResponseGetProductMovement-row.log',print_r($rows,true),FILE_APPEND);
        file_put_contents('MovementDTO-testMakeOutboundResponseGetProductMovement-result.log',print_r($result,true),FILE_APPEND);


        return $result;
    }

    protected function testMakeInboundResponseGetProductMovement($id,&$result)
    {
        $order = InboundOrder::find()->andWhere(['client_order_id'=>$id])->one();
        $items = InboundOrderItem::find()->andWhere(['inbound_order_id'=>$order->id])->all();
//        $result = [];
        $rows = [];
        foreach($items as $item) {
            $row = new \stdClass();
            $row->client_id = $order->client_id;
            $row->stock_id  = 0;
            $row->inbound_id = $order->id;
            $row->outbound_id = 0;
            $row->zone_id = ConstantZone::CATEGORY_A;
            $row->clinet_order_id = $order->client_order_id;
            $row->barcode = $item->product_barcode;
            $row->product_model = $item->product_model;
            $row->product_sku = $item->product_sku;
            $row->qty = $item->accepted_qty;
            $row->datetime = date('c',$item->created_at);

            $rows[] = $row;
        }

        foreach($rows as $row) {
            $result[] = [
                'ДатаВремя' => $row->datetime,// "2017-07-04T13:50:07",
                'МатНомер' =>  $row->product_sku,// "010101",
                'Артикул' => $row->product_model, //"020202",
                'Зона' => $row->zone_id ,//"A",//0,
                'IDдокумента' =>  $row->clinet_order_id,// "736beb9c-6158-11e7-9c01-94de80bd5cf8",
                'Приход' => $row->qty,
                'Расход' =>0,
            ];
        }

        file_put_contents('MovementDTO-testMakeInboundResponseGetProductMovement-row.log',print_r($rows,true),FILE_APPEND);
        file_put_contents('MovementDTO-testMakeInboundResponseGetProductMovement-result.log',print_r($result,true),FILE_APPEND);


        return $result;
    }
}