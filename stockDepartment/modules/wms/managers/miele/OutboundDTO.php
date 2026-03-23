<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.07.2017
 * Time: 8:00
 */

namespace stockDepartment\modules\wms\managers\miele;


use common\modules\stock\models\Stock;

class OutboundDTO
{
    // OK
    public function prepareSendOrder($request)
    {
        $returnOrderHeader = [
            'client_order_id' => $request->order->Идентификатор,
            'parent_order_number' => $request->order->Идентификатор, // 736beb9c-6158-11e7-9c01-94de80bd5cf8
            'order_number' => $request->order->НомерДокументаПрообраза, // 00000001888
            'zone' => $request->order->ЗонаОтгрузки, // 0
            'data_created_on_client' => $request->order->Дата, // 2017-07-05T12:03:56
            'description' => $request->order->Комментарий, // любой текст
            'to_point_title' => $request->order->Получатель.'///'.$request->order->Адрес, // адрес получателя
//            'extra_fields'=>serialize($request->order)
        ];

        $extraFields = new \stdClass();
        $extraFields->Идентификатор =  $request->order->Идентификатор;
        $extraFields->НомерДокументаПрообраза =  $request->order->НомерДокументаПрообраза;
        $extraFields->СтрочноеПредставлениеДокументаПрообраза =  $request->order->СтрочноеПредставлениеДокументаПрообраза;
        $extraFields->ЗонаОтгрузки =  $request->order->ЗонаОтгрузки;
        $extraFields->Дата =  $request->order->Дата;
        $extraFields->Статус =  $request->order->Статус;
        $extraFields->Комментарий =  $request->order->Комментарий;
        $extraFields->СписаниеПодПеремещение =  $request->order->СписаниеПодПеремещение;
        $extraFields->Получатель =  $request->order->Получатель;
        $extraFields->Адрес =  $request->order->Адрес;
        $extraFields->ВидОтгрузки =  $request->order->ВидОтгрузки;

        $returnOrderHeader['extra_fields'] = serialize($extraFields);

        $returnOrderItems = [];
        if(!empty($request->order->Спецификация)) {

            if(isset($request->order->Спецификация->МатНомер)) {
                $returnOrderItems[] = [
                    // TODO ТУТ жолжен быть EAN11!!!!
                    'product_sku'=> $request->order->Спецификация->МатНомер, // 06165000
                    'product_model'=>  $request->order->Спецификация->Артикул, // Артикул
                    'expected_qty'=>  $request->order->Спецификация->Количество, // 5
                    'field_extra1'=>  $request->order->Спецификация->ФабНомер, // 00000000000000000001
                    'product_serialize_data'=>  serialize($request->order->Спецификация), // 0
                ];
            } else {
                foreach($request->order->Спецификация as $key=>$item) {
                    $returnOrderItems[] = [
                        // TODO ТУТ жолжен быть EAN11!!!!
                        'product_sku'=> $item->МатНомер, // 06165000
                        'product_model'=>  $item->Артикул, // Артикул
                        'expected_qty'=>  $item->Количество, // 5
                        'field_extra1'=>  $item->ФабНомер, // 00000000000000000001
                        'product_serialize_data'=>  serialize($item), // 0
                    ];
                }
            }
        }

        return ['order'=>$returnOrderHeader,'items'=>$returnOrderItems];
    }
    // OK
    public function makeResponseSendOrder()
    {
        $std = [
            'SendOutboundOrderResponse' => true
        ];

        return $std;
    }
    // GET OUTBOUND ORDER
    // T
    public function prepareRequestGetOrders($request)
    {
        file_put_contents('OutboundDTO-prepareRequestGetOrders.log',print_r($request,true)."\n"."\n",FILE_APPEND);
        file_put_contents('OutboundDTO-prepareRequestGetOrders.log',print_r($request->idList,true)."\n"."\n",FILE_APPEND);
        file_put_contents('OutboundDTO-prepareRequestGetOrders.log',print_r($request->idList->Идентификатор,true)."\n"."\n",FILE_APPEND);

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
    // OK
    public function makeResponseGetOrders($outbounds)
    {
        $result = [];
        foreach ($outbounds as $key=>$outbound) {
            $result['GetOutboundOrdersResult']['Запись'][$key] = $this->makeResponseHeaderOutbound($outbound['order']);
            $result['GetOutboundOrdersResult']['Запись'][$key]['Спецификация'] = $this->makeResponseSpecificationOutbound($outbound['items'],$outbound['order']);
        }

        file_put_contents('outboundDTO-makeResponseGetOrders-data-items.log',print_r($outbounds,true),FILE_APPEND);
        file_put_contents('outboundDTO-makeResponseGetOrders.log',print_r($result,true),FILE_APPEND);

//        $result['GetOutboundOrdersResult']['Запись'] = $this->makeResponseHeaderOutbound($data['order']);
//        $result['GetOutboundOrdersResult']['Запись']['Спецификация'] = $this->makeResponseSpecificationOutbound($data['items']);

        return $result;
    }

    // OK
    public function makeResponseGetChangedOrders($outbounds)
    {
        $result = [];
        foreach ($outbounds as $key=>$outbound) {
            $result['GetChangedOutboundOrdersResult']['Запись'][$key] = $this->makeResponseHeaderOutbound($outbound['order']);
            $result['GetChangedOutboundOrdersResult']['Запись'][$key]['Спецификация'] = $this->makeResponseSpecificationOutbound($outbound['items'],$outbound['order']);
        }

        file_put_contents('outboundDTO-GetChangedOutboundOrdersResult-data-items.log',print_r($outbounds,true),FILE_APPEND);
        file_put_contents('outboundDTO-GetChangedOutboundOrdersResult.log',print_r($result,true),FILE_APPEND);

        return $result;
    }

    private function makeResponseHeaderOutbound($header) {
        $extraField = unserialize($header->extra_fields);

//        if($header->expected_qty != $header->accepted_qty) {
//            $header->status = 0;
//        }

        return  [
            'Идентификатор' => $header->client_order_id, // "736beb9c-6158-11e7-9c01-94de80bd5cf8",
//            'Идентификатор' => $header->parent_order_number, // "736beb9c-6158-11e7-9c01-94de80bd5cf8",
            'НомерДокументаПрообраза' =>$header->order_number , // "00000001888",
            'СтрочноеПредставлениеДокументаПрообраза' => $extraField->СтрочноеПредставлениеДокументаПрообраза, // "Реализация ТМЗ и услуг 00000001888 от 05.07.2017 12:03:56",
            'ЗонаОтгрузки' => $extraField->ЗонаОтгрузки, // 2,
            'Дата' => $extraField->Дата, // "2017-07-05T12:03:56",
            'Статус' => DTO::mapOutboundOurStatusToClient($header->status), // 3
            'Комментарий' => $header->description, // "Тесть пройден",
            'СписаниеПодПеремещение' => $extraField->СписаниеПодПеремещение, //12, // TODO Как это должно работать
            'Получатель' => $extraField->Получатель, // "APIS ТОО",
            'Адрес' => $extraField->Адрес, // "ул. Пушкина",
            'ВидОтгрузки' =>  $extraField->ВидОтгрузки, // 4,
        ];
    }

    private function makeResponseSpecificationOutbound($items,$header) {
        $result = [];
        if($items) {
            foreach ($items as $key=>$item) {
                $masterDataSpecification = unserialize($item->product_serialize_data);
                file_put_contents('outboundDTO-makeResponseSpecificationOutbound-item.log',print_r($item,true),FILE_APPEND);
                file_put_contents('outboundDTO-makeResponseSpecificationOutbound-product_serialize_data.log',print_r($masterDataSpecification,true),FILE_APPEND);
                $this->makeResponseItem($item,$header,$result);
            }
        }

        return $result;
    }

    private function makeResponseItem($item,$outboundOrder,&$result)
    {
        $masterDataSpecification = unserialize($item->product_serialize_data);
        if($item->field_extra1 == Constants::ALLOCATION_BY_FAB_KEY) {
              $stocks = Stock::find()->andWhere([
                    'outbound_order_id'=> $outboundOrder->id
                ])->all();
                foreach ($stocks as $stock) {
                     $result [] = [
                        'МатНомер' => $item->product_sku, // 06165000
                        'Артикул' =>  $item->product_model,//62782410,
                        'ФабНомер' => $stock->field_extra1,  //TODO Если это поле заполено send Outbound то резервируем по нему. но если она не заполнено но в мастер дате тип по фаб номеру мы должны его заполнить.
                        'КомНомер' => $masterDataSpecification->КомНомер,
                        'НомерГТД' => $masterDataSpecification->НомерГТД,
                        'Количество' => 1, // 8,
                        'КоличествоНеадаптированное' => $masterDataSpecification->КоличествоНеадаптированное,
                        'КоличествоБрак' => $masterDataSpecification->КоличествоБрак,
                    ];
                }
        } else {
            $result [] = [
                'МатНомер' => $item->product_sku, // 06165000
                'Артикул' => $item->product_model,//62782410,
                'ФабНомер' => $masterDataSpecification->ФабНомер,  //TODO Если это поле заполено send Outbound то резервируем по нему. но если она не заполнено но в мастер дате тип по фаб номеру мы должны его заполнить.
                'КомНомер' => $masterDataSpecification->КомНомер,
                'НомерГТД' => $masterDataSpecification->НомерГТД,
                'Количество' => ($outboundOrder->status == Stock::STATUS_OUTBOUND_COMPLETE ? $item->accepted_qty : $item->expected_qty), // 8,
                'КоличествоНеадаптированное' => $masterDataSpecification->КоличествоНеадаптированное,
                'КоличествоБрак' => $masterDataSpecification->КоличествоБрак,
            ];
        }
    }

    // OK
    public function prepareCancelOrder($request)
    {
        file_put_contents('outboundDTO-prepareCancelOrder-request.log',print_r($request,true),FILE_APPEND);
        file_put_contents('outboundDTO-prepareCancelOrder-id.log',print_r($request->id,true),FILE_APPEND);
        return [
            'id'=> (isset($request->id) ? $request->id : 0)
        ];
    }

    // OK
    public function makeResponseCancelOrder()
    {
        $std = [
            'CancelOutboundOrderResponse' => true
        ];

        return $std;
    }

    //OK
    public function prepareMarkAsUnchangedOrder($request)
    {
        return [
            'id' => isset($request->id) ? $request->id : null, //'736beb9a-6158-11e7-9c01-94de80bd5cf8',
            'status' => isset($request->status) ? $request->status : null, // 3
        ];
    }

    public function makeResponseMarkAsUnchangedOrder() {

        $std = [
            'MarkAsUnchangedOutboundOrderResult' => true
        ];

        return $std;
    }
}