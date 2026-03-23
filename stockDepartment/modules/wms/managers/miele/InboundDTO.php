<?php
namespace stockDepartment\modules\wms\managers\miele;

use common\modules\stock\models\Stock;

class InboundDTO
{
    // +
    public function prepareSendOrder($order)
    {
        $returnOrderHeader = [
            'client_order_id' => $order->Идентификатор, // 736beb9a-6158-11e7-9c01-94de80bd5cf8
            'order_number' => $order->НомерДокументаПрообраза, // 00000000474
//            '3' => $order->СтрочноеПредставлениеДокументаПрообраза, // Поступление ТМЗ и услуг 00000000474 от 05.07.2017 11:27:19
            'zone' => DTO::mapClientZoneToOur($order->ЗонаПриемки), //- // 0
//            '5' => $order->ТребуетсяЭтикетирование, // ????
            'data_created_on_client' => $order->Дата, // 2017-07-05T11:27:19
            'status' => DTO::mapClientStatusToOur($order->Статус), // 1
//            '8' => $order->ОтОсновногоПоставщика, // ????
            'comments' => $order->Комментарий, //- // Тест
//            'extra_fields' => serialize([
//                    'Идентификатор'=> $order->Идентификатор,
//                    'НомерДокументаПрообраза'=> $order->НомерДокументаПрообраза,
//                    'СтрочноеПредставлениеДокументаПрообраза'=> $order->СтрочноеПредставлениеДокументаПрообраза,
//                    'ЗонаПриемки'=> $order->ЗонаПриемки,
//                    'ТребуетсяЭтикетирование'=> $order->ТребуетсяЭтикетирование,
//                    'Дата'=> $order->Дата,
//                    'Статус'=> $order->Статус,
//                    'ОтОсновногоПоставщика'=> $order->ОтОсновногоПоставщика,
//                    'Комментарий'=> $order->Комментарий,
//                ])
//            'extra_fields' => serialize($order)
//            'extra_fields' => [
//                'header'=>[
//                    'Идентификатор'=> $order->Идентификатор,
//                    'НомерДокументаПрообраза'=> $order->НомерДокументаПрообраза,
//                    'СтрочноеПредставлениеДокументаПрообраза'=> $order->СтрочноеПредставлениеДокументаПрообраза,
//                    'ЗонаПриемки'=> $order->ЗонаПриемки,
//                    'ТребуетсяЭтикетирование'=> $order->ТребуетсяЭтикетирование,
//                    'Дата'=> $order->Дата,
//                    'Статус'=> $order->Статус,
//                    'ОтОсновногоПоставщика'=> $order->ОтОсновногоПоставщика,
//                    'Комментарий'=> $order->Комментарий,
//                ]
//            ],
        ];

        $extraFields = new \stdClass();
        $extraFields->Идентификатор = $order->Идентификатор;
        $extraFields->НомерДокументаПрообраза= $order->НомерДокументаПрообраза;
        $extraFields->СтрочноеПредставлениеДокументаПрообраза= $order->СтрочноеПредставлениеДокументаПрообраза;
        $extraFields->ЗонаПриемки= $order->ЗонаПриемки;
        $extraFields->ТребуетсяЭтикетирование= $order->ТребуетсяЭтикетирование;
        $extraFields->Дата= $order->Дата;
        $extraFields->Статус= $order->Статус;
        $extraFields->ОтОсновногоПоставщика= $order->ОтОсновногоПоставщика;
        $extraFields->Комментарий= $order->Комментарий;

        $returnOrderHeader['extra_fields'] = serialize($extraFields);

        $returnOrderItems = [];
        if (!empty($order->МастерДанныеНоменклатура) && !empty($order->Спецификация)) {

            if (isset($order->МастерДанныеНоменклатура->МатНомер) && isset($order->Спецификация->МатНомер)) {

                $returnOrderItems[] = [
                    'product_sku' => $order->МастерДанныеНоменклатура->МатНомер, // 06165000
                    'product_model' => $order->МастерДанныеНоменклатура->Артикул, // 62782410
                    'product_name' => $order->МастерДанныеНоменклатура->Наименование, // Дезинфекционно-моечный автомат G 7824
                    'status' => DTO::mapClientStatusToOur($order->Статус),
                    'product_barcode' => $order->МастерДанныеНоменклатура->EAN11, // 4002513672886
                    'expected_qty' => $order->Спецификация->Количество, // 10
//                    '11' => $item->УровеньШтабелирования, // 1 // ???
//                    '12' => $item->УчетПоФабричнымНомерам, // ???
//                    '13' => $item->УчетПоКоммерческимНомерам, // ???
//                    '14' => $item->УчетПоСрокамГодности, // ???
//                    '15' => $item->ТребуетсяЭтикетирование, // ???
//                    'specification' => [
//                        '1' => $order->Спецификация[$key]->МатНомер, // 06165000
//                        '2' => $order->Спецификация[$key]->Артикул, // 62782410
//                        '3' => $order->Спецификация[$key]->ФабНомер,
//                        '4' => $order->Спецификация[$key]->КомНомер,
//                        '5' => $order->Спецификация[$key]->НомерГТД,
//                        '6' => $order->Спецификация[$key]->Количество, // 10
//                        '7' => $order->Спецификация[$key]->КоличествоНеадаптированное, // 0
//                        '8' => $order->Спецификация[$key]->КоличествоБрак, // 0
//                    ],
                    'product_serialize_data' => serialize([
                        'МатНомер' => $order->МастерДанныеНоменклатура->МатНомер, // 06165000
                        'Артикул' => $order->МастерДанныеНоменклатура->Артикул, // 62782410
                        'Наименование' => $order->МастерДанныеНоменклатура->Наименование, // Дезинфекционно-моечный автомат G 7824
                        'ВесБрутто' => $order->МастерДанныеНоменклатура->ВесБрутто, // 272
                        'ВесНетто' => $order->МастерДанныеНоменклатура->ВесНетто, // 264
                        'Объем' => $order->МастерДанныеНоменклатура->Объем, // 0
                        'Длина' => $order->МастерДанныеНоменклатура->Длина, // 850
                        'Ширина' => $order->МастерДанныеНоменклатура->Ширина, // 1200
                        'Высота' => $order->МастерДанныеНоменклатура->Высота, // 1600
                        'EAN11' => $order->МастерДанныеНоменклатура->EAN11, // 4002513672886  TODO Ошибка если такой уже есть
                        'УровеньШтабелирования' => $order->МастерДанныеНоменклатура->УровеньШтабелирования, // 1 // ???
                        'УчетПоФабричнымНомерам' => $order->МастерДанныеНоменклатура->УчетПоФабричнымНомерам, // ???
                        'УчетПоКоммерческимНомерам' => $order->МастерДанныеНоменклатура->УчетПоКоммерческимНомерам, // ???
                        'УчетПоСрокамГодности' => $order->МастерДанныеНоменклатура->УчетПоСрокамГодности, // ???
                        'ТребуетсяЭтикетирование' => $order->МастерДанныеНоменклатура->ТребуетсяЭтикетирование, // ???
                        'specification' => [
                            'МатНомер' => $order->Спецификация->МатНомер, // 06165000
                            'Артикул' => $order->Спецификация->Артикул, // 62782410
                            'ФабНомер' => $order->Спецификация->ФабНомер,
                            'КомНомер' => $order->Спецификация->КомНомер,
                            'НомерГТД' => $order->Спецификация->НомерГТД,
                            'Количество' => $order->Спецификация->Количество, // 10
                            //'EAN11' => $order->Спецификация->EAN11, // 10
                            'КоличествоНеадаптированное' => $order->Спецификация->КоличествоНеадаптированное, // 0
                            'КоличествоБрак' => $order->Спецификация->КоличествоБрак, // 0
                        ],
                    ])
                ];
            } else {


                foreach ($order->МастерДанныеНоменклатура as $key => $item) {
                    $returnOrderItems[] = [
                        'product_sku' => $item->МатНомер, // 06165000
                        'product_model' => $item->Артикул, // 62782410
                        'product_name' => $item->Наименование, // Дезинфекционно-моечный автомат G 7824
                        'status' => DTO::mapClientStatusToOur($order->Статус),
                        'product_barcode' => $item->EAN11, // 4002513672886
                        'expected_qty' => $order->Спецификация[$key]->Количество, // 10
//                    '4' => $item->ВесБрутто, // 272
//                    '5' => $item->ВесНетто, // 264
//                    '6' => $item->Объем, // 0
//                    '7' => $item->Длина, // 850
//                    '8' => $item->Ширина, // 1200
//                    '9' => $item->Высота, // 1600

//                    '11' => $item->УровеньШтабелирования, // 1 // ???
//                    '12' => $item->УчетПоФабричнымНомерам, // ???
//                    '13' => $item->УчетПоКоммерческимНомерам, // ???
//                    '14' => $item->УчетПоСрокамГодности, // ???
//                    '15' => $item->ТребуетсяЭтикетирование, // ???
//                    'specification' => [
//                        '1' => $order->Спецификация[$key]->МатНомер, // 06165000
//                        '2' => $order->Спецификация[$key]->Артикул, // 62782410
//                        '3' => $order->Спецификация[$key]->ФабНомер,
//                        '4' => $order->Спецификация[$key]->КомНомер,
//                        '5' => $order->Спецификация[$key]->НомерГТД,
//                        '6' => $order->Спецификация[$key]->Количество, // 10
//                        '7' => $order->Спецификация[$key]->КоличествоНеадаптированное, // 0
//                        '8' => $order->Спецификация[$key]->КоличествоБрак, // 0
//                    ],
                        'product_serialize_data' => serialize([
                            'МатНомер' => $item->МатНомер, // 06165000
                            'Артикул' => $item->Артикул, // 62782410
                            'Наименование' => $item->Наименование, // Дезинфекционно-моечный автомат G 7824
                            'ВесБрутто' => $item->ВесБрутто, // 272
                            'ВесНетто' => $item->ВесНетто, // 264
                            'Объем' => $item->Объем, // 0
                            'Длина' => $item->Длина, // 850
                            'Ширина' => $item->Ширина, // 1200
                            'Высота' => $item->Высота, // 1600
                            'EAN11' => $item->EAN11, // 4002513672886  TODO Ошибка если такой уже есть
                            'УровеньШтабелирования' => $item->УровеньШтабелирования, // 1 // ???
                            'УчетПоФабричнымНомерам' => $item->УчетПоФабричнымНомерам, // ???
                            'УчетПоКоммерческимНомерам' => $item->УчетПоКоммерческимНомерам, // ???
                            'УчетПоСрокамГодности' => $item->УчетПоСрокамГодности, // ???
                            'ТребуетсяЭтикетирование' => $item->ТребуетсяЭтикетирование, // ???
                            'specification' => [
                                'МатНомер' => $order->Спецификация[$key]->МатНомер, // 06165000
                                'Артикул' => $order->Спецификация[$key]->Артикул, // 62782410
                                'ФабНомер' => $order->Спецификация[$key]->ФабНомер,
                                'КомНомер' => $order->Спецификация[$key]->КомНомер,
                                'НомерГТД' => $order->Спецификация[$key]->НомерГТД,
                                'Количество' => $order->Спецификация[$key]->Количество, // 10
                                //'EAN11' => $order->Спецификация[$key]->EAN11, // 10
                                'КоличествоНеадаптированное' => $order->Спецификация[$key]->КоличествоНеадаптированное, // 0
                                'КоличествоБрак' => $order->Спецификация[$key]->КоличествоБрак, // 0
                            ],
                        ])
                    ];
                }
            }
        }
        return ['order' => $returnOrderHeader, 'items' => $returnOrderItems];
    }
    public function makeItemNomenclature($item)
    {
        $masterDataNomenclature = unserialize($item->product_serialize_data);
        $result = new \stdClass();
        $result->МатНомер = $masterDataNomenclature['МатНомер']; //  "06165000",
        $result->Артикул = $masterDataNomenclature['Артикул']; //  "06165000",
        $result->Наименование = $masterDataNomenclature['Наименование']; //  "Резинка переходник FIRAT 50",
        $result->ВесБрутто = $masterDataNomenclature['ВесБрутто']; //  0,
        $result->ВесНетто = $masterDataNomenclature['ВесНетто']; //  0,
        $result->Объем = $masterDataNomenclature['Объем']; //  0,
        $result->Длина = $masterDataNomenclature['Длина']; //  0,
        $result->Ширина = $masterDataNomenclature['Ширина']; //  0,
        $result->Высота = $masterDataNomenclature['Высота']; //  0,
        $result->EAN11 = $masterDataNomenclature['EAN11']; //  0,
        $result->УровеньШтабелирования = $masterDataNomenclature['УровеньШтабелирования']; //  0,
        $result->УчетПоФабричнымНомерам = $masterDataNomenclature['УчетПоФабричнымНомерам']; //  0,
        $result->УчетПоКоммерческимНомерам = $masterDataNomenclature['УчетПоКоммерческимНомерам']; //  0,
        $result->УчетПоСрокамГодности = $masterDataNomenclature['УчетПоСрокамГодности']; //  0,
        $result->ТребуетсяЭтикетирование = $masterDataNomenclature['ТребуетсяЭтикетирование']; //  0,
        return $result;
    }

    // +
    public function makeResponseSendOrder()
    {
        $response = [
            'SendInboundOrderResponse' => true
        ];
        return $response;
    }
    // OK
    public function prepareCancel($request)
    {
        return [
            'id'=>(isset($request->id) ? $request->id : 0)
        ];
    }
    // OK
    public function makeResponseCancelOrder()
    {
        $std = [
            'CancelInboundOrderResponse' => true
        ];

        return $std;
    }
    // +
    public function prepareGetOrders($request)
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
    // +
    public function makeGetInboundOrders($inbounds)
    {
        $result = [];
        foreach ($inbounds as $key=>$inbound) {
            $result['GetInboundOrdersResult']['Запись'][$key] = $this->makeHeaderGetOrder($inbound['order']);
            $result['GetInboundOrdersResult']['Запись'][$key]['МастерДанныеНоменклатура'] = $this->makeMasterDataNomenclatureGetOrder($inbound['items']);
            $result['GetInboundOrdersResult']['Запись'][$key]['Спецификация'] = $this->makeSpecificationGetOrder($inbound['items'],$inbound['order']);
        }

        file_put_contents('makeGetInboundOrders-InboundDTO.log',print_r($result,true)."\n"."\n",FILE_APPEND);

        return $result;
    }
    // +
    private function makeHeaderGetOrder($header)
    {
        $extraField = unserialize($header->extra_fields);
        return [
            'Идентификатор' => $header->client_order_id,// "4f7d991c-5a46-11e7-80f4-00155d654809",
            'НомерДокументаПрообраза' => $header->order_number,//"00000000472",
            'СтрочноеПредставлениеДокументаПрообраза' =>$extraField->СтрочноеПредставлениеДокументаПрообраза, //"Поступление ТМЗ и услуг 00000000472 от 15.06.2017 20:09:13",
            'ЗонаПриемки' => DTO::mapOurZoneToClient($header->zone),
            'ТребуетсяЭтикетирование' => $extraField->ТребуетсяЭтикетирование,
            'Дата' => $header->data_created_on_client,
            'Статус' => DTO::mapOurStatusToClient($header->status),
            'ОтОсновногоПоставщика' => $extraField->ОтОсновногоПоставщика,
            'Комментарий' => $header->comments, //"Тесть пройден",
        ];
    }


    // +
    private function makeMasterDataNomenclatureGetOrder($items)
    {
        $result = [];
        if($items) {
            foreach($items as $item) {
                $masterDataNomenclature = unserialize($item->product_serialize_data);
                $result [] = [
                    'МатНомер' => $masterDataNomenclature['МатНомер'], //  "06165000",
                    'Артикул' =>  $masterDataNomenclature['Артикул'],// "62782410",
                    'Наименование' => $masterDataNomenclature['Наименование'],// "Резинка переходник FIRAT 50",
                    'ВесБрутто' =>$masterDataNomenclature['ВесБрутто'],// 0,
                    'ВесНетто' =>$masterDataNomenclature['ВесНетто'],// 0,
                    'Объем' =>$masterDataNomenclature['Объем'],// 0,
                    'Длина' =>$masterDataNomenclature['Длина'],// 0,
                    'Ширина' =>$masterDataNomenclature['Ширина'],// 0,
                    'Высота' =>$masterDataNomenclature['Высота'],// 0,
                    'EAN11' =>$masterDataNomenclature['EAN11'],// 0,
                    'УровеньШтабелирования' =>$masterDataNomenclature['УровеньШтабелирования'],// 0,
                    'УчетПоФабричнымНомерам' =>$masterDataNomenclature['УчетПоФабричнымНомерам'],// 0,
                    'УчетПоКоммерческимНомерам' =>$masterDataNomenclature['УчетПоКоммерческимНомерам'],// false,
                    'УчетПоСрокамГодности' =>$masterDataNomenclature['УчетПоСрокамГодности'],// 0,
                    'ТребуетсяЭтикетирование' =>$masterDataNomenclature['ТребуетсяЭтикетирование'],// 0,
                ];
            }
        }

        return $result;
    }
    // +
    private function makeSpecificationGetOrder($items,$inboundHeader)
    {
        $result = [];
        if($items) {
            foreach($items as $item) {
                $value = unserialize($item->product_serialize_data);
                $specification = $value['specification'];
                $result [] = [
                    'МатНомер' => $specification['МатНомер'], //  "06165000",
                    'Артикул' =>  $specification['Артикул'],// "62782410",
                    'ФабНомер' =>  $specification['ФабНомер'],// "",
                    'КомНомер' =>  $specification['КомНомер'],// "",
                    'НомерГТД' =>  $specification['НомерГТД'],// "",
                    //'EAN11' =>  $specification['EAN11'],// "",
//                    'Количество' =>  $item->expected_qty,// 8,
                    'Количество' => ($inboundHeader->status == Stock::STATUS_INBOUND_COMPLETE ? $item->accepted_qty : $item->expected_qty),// 8,
//                    'Количество' =>  $specification['Количество'],// 8,
                    'КоличествоНеадаптированное' =>  $specification['КоличествоНеадаптированное'],// 0,
                    'КоличествоБрак' =>  0,//$specification['КоличествоБрак'],// 0,
                ];

//                $result [] = [
//                    'МатНомер' => $specification['МатНомер'], //  "06165000",
//                    'Артикул' =>  $specification['Артикул'],// "62782410",
//                    'ФабНомер' =>  $specification['ФабНомер'],// "",
//                    'КомНомер' =>  $specification['КомНомер'],// "",
//                    'НомерГТД' =>  $specification['НомерГТД'],// "",
//                    'Количество' =>0,
////                    'Количество' =>  $specification['Количество'],// 8,
//                    'КоличествоНеадаптированное' =>  $specification['КоличествоНеадаптированное'],// 0,
//                    'КоличествоБрак' =>  1,//$specification['КоличествоБрак'],// 0,
//                ];
            }
        }

        return $result;
    }
// ======================== ONLY FOR TEST BEGIN======================================
    // +
    public function makeObjectGetInboundOrders($inbounds)
    {
        $result = [];
        foreach ($inbounds as $key=>$inbound) {
            $obj = new \stdClass();
            $obj->order = $this->makeObjHeaderGetOrder($inbound['order']);
            $obj->order->МастерДанныеНоменклатура = $this->makeObjMasterDataNomenclatureGetOrder($inbound['items']);
            $obj->order->Спецификация = $this->makeObjSpecificationGetOrder($inbound['items'],$inbound['order']);

//            $result['GetInboundOrdersResult']['Запись'][$key] = $this->makeObjHeaderGetOrder($inbound['order']);
//            $result['GetInboundOrdersResult']['Запись'][$key]['МастерДанныеНоменклатура'] = $this->makeObjMasterDataNomenclatureGetOrder($inbound['items']);
//            $result['GetInboundOrdersResult']['Запись'][$key]['Спецификация'] = $this->makeObjSpecificationGetOrder($inbound['items'],$inbound['order']);
        }

        file_put_contents('makeGetInboundOrders-InboundDTO.log',print_r($result,true)."\n"."\n",FILE_APPEND);

        return $obj;
    }
    private function makeObjHeaderGetOrder($header)
    {
        $extraField = unserialize($header->extra_fields);
        $r = new \stdClass();
        $r->Идентификатор = $header->client_order_id;
        $r->НомерДокументаПрообраза = $header->order_number;
        $r->СтрочноеПредставлениеДокументаПрообраза = $extraField->СтрочноеПредставлениеДокументаПрообраза;
        $r->ЗонаПриемки =DTO::mapOurZoneToClient($header->zone);
        $r->ТребуетсяЭтикетирование =$extraField->ТребуетсяЭтикетирование;
        $r->Дата = $header->data_created_on_client;
        $r->Статус = DTO::mapOurStatusToClient($header->status);
        $r->ОтОсновногоПоставщика = $extraField->ОтОсновногоПоставщика;
        $r->Комментарий = $header->comments;
        return $r;
    }


    private function makeObjMasterDataNomenclatureGetOrder($items)
    {
        $result = [];
        if($items) {
            foreach($items as $item) {
                $masterDataNomenclature = unserialize($item->product_serialize_data);

                $obj = new \stdClass();
                $obj->МатНомер = $masterDataNomenclature['МатНомер'];// , //  "06165000",
                $obj->Артикул =  $masterDataNomenclature['Артикул'];// ,// "62782410",
                    $obj->Наименование = $masterDataNomenclature['Наименование'];// ,// "Резинка переходник FIRAT 50",
                    $obj->ВесБрутто =$masterDataNomenclature['ВесБрутто'];// ,// 0,
                    $obj->ВесНетто =$masterDataNomenclature['ВесНетто'];// ,// 0,
                    $obj->Объем =$masterDataNomenclature['Объем'];// ,// 0,
                    $obj->Длина =$masterDataNomenclature['Длина'];// ,// 0,
                    $obj->Ширина =$masterDataNomenclature['Ширина'];// ,// 0,
                    $obj->Высота =$masterDataNomenclature['Высота'];// ,// 0,
                    $obj->EAN11 =$masterDataNomenclature['EAN11'];// ,// 0,
                    $obj->УровеньШтабелирования =$masterDataNomenclature['УровеньШтабелирования'];// ,// 0,
                    $obj->УчетПоФабричнымНомерам =$masterDataNomenclature['УчетПоФабричнымНомерам'];// ,// 0,
                    $obj->УчетПоКоммерческимНомерам =$masterDataNomenclature['УчетПоКоммерческимНомерам'];// ,// false,
                    $obj->УчетПоСрокамГодности =$masterDataNomenclature['УчетПоСрокамГодности'];// ,// 0,
                    $obj->ТребуетсяЭтикетирование =$masterDataNomenclature['ТребуетсяЭтикетирование'];// 0,
                $result [] = $obj;
            }
        }

        return $result;
    }

    // +
    private function makeObjSpecificationGetOrder($items,$inboundHeader)
    {
        $result = [];
        if($items) {
            foreach($items as $item) {
                $value = unserialize($item->product_serialize_data);
                $specification = $value['specification'];
                $obj =  new \stdClass();
                $obj->МатНомер = $specification['МатНомер'];//, //  "06165000",
                    $obj->Артикул =  $specification['Артикул'];//,// "62782410",
                    $obj->ФабНомер =  $specification['ФабНомер'];//,// "",
                    $obj->КомНомер = $specification['КомНомер'];//,// "",
                    $obj->НомерГТД =  $specification['НомерГТД'];//,// "",
                    $obj->Количество =   $item->expected_qty;//,// 8,
                    $obj->КоличествоНеадаптированное =  $specification['КоличествоНеадаптированное'];
                    $obj->КоличествоБрак =  0;
                $result [] = $obj;
            }
        }

        return $result;
    }

// ======================== ONLY FOR TEST END======================================


    // OK
    public function makeGetChangedOrders($inbounds)
    {
        $result = [];
        foreach ($inbounds as $key=>$inbound) {
            $result['GetChangedInboundOrdersResult']['Запись'][$key] = $this->makeHeaderGetOrder($inbound['order']);
            $result['GetChangedInboundOrdersResult']['Запись'][$key]['МастерДанныеНоменклатура'] = $this->makeMasterDataNomenclatureGetOrder($inbound['items']);
            $result['GetChangedInboundOrdersResult']['Запись'][$key]['Спецификация'] = $this->makeSpecificationGetOrder($inbound['items'],$inbound['order']);
        }
        file_put_contents('makeGetChangedOrders-InboundDTO.log',print_r($result,true)."\n"."\n",FILE_APPEND);
        return $result;
    }

//    public function makeObjGetChangedOrders($inbounds)
//    {
//        $result = [];
//        foreach ($inbounds as $key=>$inbound) {
//            $result['GetChangedInboundOrdersResult']['Запись'][$key] = $this->makeHeaderGetOrder($inbound['order']);
//            $result['GetChangedInboundOrdersResult']['Запись'][$key]['МастерДанныеНоменклатура'] = $this->makeMasterDataNomenclatureGetOrder($inbound['items']);
//            $result['GetChangedInboundOrdersResult']['Запись'][$key]['Спецификация'] = $this->makeSpecificationGetOrder($inbound['items'],$inbound['order']);
//        }
//        file_put_contents('makeGetChangedOrders-InboundDTO.log',print_r($result,true)."\n"."\n",FILE_APPEND);
//        return $result;
//    }

    //OK
    public function prepareMarkAsUnchangedOrder($request)
    {
        return [
            'id' => (isset($request->id) ? $request->id : '' ) ,// '736beb9a-6158-11e7-9c01-94de80bd5cf8',
            'status' =>(isset($request->status) ? $request->status : '' )// 3,
        ];
    }

    // OK
    public function makeMarkAsUnchangedOrder()
    {
        $std = [
            'MarkAsUnchangedInboundOrderResult' => true
        ];

        return $std;
    }
}