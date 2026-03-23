<?php
namespace stockDepartment\controllers;

ini_set('soap.wsdl_cache_enabled', 0);

use stockDepartment\modules\wms\managers\miele\APIService;

class MySoapServerHandler
{
    // INBOUND
    // T
    function SendInboundOrder($order)
    { // ЗаявкаНаПриемку
        file_put_contents('EDIService-SendInboundOrder.xml', print_r($order, true) . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-SendInboundOrder.xml', print_r($order->order, true) . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-SendInboundOrder.xml', print_r($order->order->СтрочноеПредставлениеДокументаПрообраза, true) . "\n" . "\n", FILE_APPEND);

        $service =  new APIService();
        return $service->SendInboundOrder($order);
    }
    //T
    function CancelInboundOrder($id)
    {
        file_put_contents('EDIService-CancelInboundOrder.xml', print_r($id, true) . "\n" . "\n", FILE_APPEND);

        $service =  new APIService();
        return $service->CancelInboundOrder($id);
    }
    // T
    function GetInboundOrders($idList)
    {
        file_put_contents('EDIService-GetInboundOrders.xml', print_r($idList, true) . "\n" . "\n", FILE_APPEND);
        $service = new APIService();
        return $service->GetInboundOrders($idList);
//        $std = [
//            'GetInboundOrdersResult' => [
//                'Запись' => [
//                    'Идентификатор' => "4f7d991c-5a46-11e7-80f4-00155d654809",
//                    'НомерДокументаПрообраза' => "00000000472",
//                    'СтрочноеПредставлениеДокументаПрообраза' => "Поступление ТМЗ и услуг 00000000472 от 15.06.2017 20:09:13",
//                    'ЗонаПриемки' => 1,
//                    'ТребуетсяЭтикетирование' => false,
//                    'Дата' => null,
//                    'Статус' => 4,
//                    'ОтОсновногоПоставщика' => false,
//                    'Комментарий' => "Тесть пройден",
//                    'МастерДанныеНоменклатура' => [
//                        'МатНомер' => "",
//                        'Артикул' => "1",
//                        'Наименование' => "Резинка переходник FIRAT 50",
//                        'ВесБрутто' => 0,
//                        'ВесНетто' => 0,
//                        'Объем' => 0,
//                        'Длина' => 0,
//                        'Ширина' => 0,
//                        'Высота' => 0,
//                        'EAN11' => 0,
//                        'УровеньШтабелирования' => 0,
//                        'УчетПоФабричнымНомерам' => 0,
//                        'УчетПоКоммерческимНомерам' => false,
//                        'УчетПоСрокамГодности' => 0,
//                        'ТребуетсяЭтикетирование' => 0,
//                    ],
//                    'Спецификация' => [
//                        'МатНомер' => '',
//                        'Артикул' => '1',
//                        'ФабНомер' => '',
//                        'КомНомер' => '',
//                        'НомерГТД' => '',
//                        'Количество' => 8,
//                        'КоличествоНеадаптированное' => 0,
//                        'КоличествоБрак' => 0,
//                    ]
//                ]
//            ]
//        ];

//        return $std;
    }
    //T
    function GetChangedInboundOrders()
    { //
        file_put_contents('EDIService-GetChangedInboundOrders.xml', "-" . "\n" . "\n", FILE_APPEND);
        $service = new APIService();
        return $service->GetChangedInboundOrders();
//        $std = [
//            'GetChangedInboundOrdersResult' => [
//                'Запись' => [
//                    'Идентификатор' => "736beb9a-6158-11e7-9c01-94de80bd5cf8",
//                    'НомерДокументаПрообраза' => "00000000474",
//                    'СтрочноеПредставлениеДокументаПрообраза' => "Поступление ТМЗ и услуг 00000000474 от 05.07.2017 11:27:19",
//                    'ЗонаПриемки' => 1,
//                    'ТребуетсяЭтикетирование' => false,
//                    'Дата' => "2017-07-05T11:27:18",
//                    'Статус' => 3,
//                    'ОтОсновногоПоставщика' => false,
//                    'Комментарий' => "Все хорошо",
//                    'МастерДанныеНоменклатура' => [[
//                        'МатНомер' => "",
//                        'Артикул' => "1",
//                        'Наименование' => "Резинка переходник FIRAT 50",
//                        'ВесБрутто' => 0,
//                        'ВесНетто' => 0,
//                        'Объем' => 0,
//                        'Длина' => 0,
//                        'Ширина' => 0,
//                        'Высота' => 0,
//                        'EAN11' => 0,
//                        'УровеньШтабелирования' => 0,
//                        'УчетПоФабричнымНомерам' => 0,
//                        'УчетПоКоммерческимНомерам' => false,
//                        'УчетПоСрокамГодности' => 0,
//                        'ТребуетсяЭтикетирование' => 0,
//                    ],
//                        [
//                            'МатНомер' => "06165000",
//                            'Артикул' => "62782410",
//                            'Наименование' => "Дезинфекционно-моечный автомат G 7824",
//                            'ВесБрутто' => 272,
//                            'ВесНетто' => 264,
//                            'Объем' => 0,
//                            'Длина' => 850,
//                            'Ширина' => 1200,
//                            'Высота' => 1600,
//                            'EAN11' => 4002513672886,
//                            'УровеньШтабелирования' => 1,
//                            'УчетПоФабричнымНомерам' => 0,
//                            'УчетПоКоммерческимНомерам' => false,
//                            'УчетПоСрокамГодности' => 0,
//                            'ТребуетсяЭтикетирование' => 0,
//                        ],
//                    ],
//                    'Спецификация' => [[
//                        'МатНомер' => '',
//                        'Артикул' => '1',
//                        'ФабНомер' => '',
//                        'КомНомер' => '',
//                        'НомерГТД' => '',
//                        'Количество' => 10,
//                        'КоличествоНеадаптированное' => 0,
//                        'КоличествоБрак' => 0,
//                    ],
//                        [
//                            'МатНомер' => '06165000',
//                            'Артикул' => '62782410',
//                            'ФабНомер' => '',
//                            'КомНомер' => '',
//                            'НомерГТД' => '',
//                            'Количество' => 10,
//                            'КоличествоНеадаптированное' => 0,
//                            'КоличествоБрак' => 0,
//                        ],
//                    ]
//                ]
//            ]
//        ];
//
//        return $std;
    }
    // T
    function MarkAsUnchangedInboundOrder($id = '', $status = 0)
    {
        file_put_contents('EDIService-MarkAsUnchangedInboundOrder.xml', print_r("id = ", true) . "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedInboundOrder.xml', print_r($id, true) . "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedInboundOrder.xml', print_r("status = ", true). "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedInboundOrder.xml', print_r($status, true). "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedInboundOrder.xml', "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return $service->MarkAsUnchangedInboundOrder($id,$status);
    }

    // OUTBOUND
    // OK
    function SendOutboundOrder($order)
    { // ЗаявкаНаОтгрузку

        file_put_contents('EDIService-SendOutboundOrder.xml', print_r($order, true) . "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return $service->SendOutboundOrder($order);
//        $std = [
//            'SendOutboundOrderResponse' => "что должно быть в ответе"
//        ];
//        return $std;
    }
    // OK
    function CancelOutboundOrder($id)
    {
        file_put_contents('EDIService-CancelOutboundOrder.xml', print_r($id, true) . "\n" . "\n", FILE_APPEND);
        $service =  new APIService();
        return $service->CancelOutboundOrder($id);
//        $std = [
//            'CancelOutboundOrderResponse' => "что должно быть в ответе"
//        ];
//        return $std;
    }
    // OK
    function GetOutboundOrders($idList)
    { // СписокИдентификаторов
        file_put_contents('EDIService-GetOutboundOrders.xml', print_r($idList, true) . "\n" . "\n", FILE_APPEND);
        $service = new APIService();
        return $service->GetOutboundOrders($idList);
//        $std = [
//            'GetOutboundOrdersResult' => [
//                'Запись' => [
//                    'Идентификатор' => "736beb9c-6158-11e7-9c01-94de80bd5cf8",
//                    'НомерДокументаПрообраза' => "00000001888",
//                    'СтрочноеПредставлениеДокументаПрообраза' => "еализация ТМЗ и услуг 00000001888 от 05.07.2017 12:03:56",
//                    'ЗонаОтгрузки' => 2,
//                    'Дата' => "2017-07-05T12:03:56",
//                    'Статус' => 3,
//                    'Комментарий' => "sdf sdf ",
//                    'СписаниеПодПеремещение' => 12,
//                    'Получатель' => "APIS ТОО",
//                    'Адрес' => " asdas dasda333",
//                    'ВидОтгрузки' => 4,
//                    'Спецификация' => [
//                        [
//                            'МатНомер' => '',
//                            'Артикул' => '1',
//                            'ФабНомер' => '',
//                            'КомНомер' => '',
//                            'НомерГТД' => '',
//                            'Количество' => 5, // "xs:decimal"
//                            'КоличествоНеадаптированное' => 0, // "xs:decimal"
//                            'КоличествоБрак' => 0, // "xs:decimal"
//                        ],
//                        [
//                            'МатНомер' => '06165000',
//                            'Артикул' => '62782410',
//                            'ФабНомер' => '',
//                            'КомНомер' => '',
//                            'НомерГТД' => '',
//                            'Количество' => 5, // "xs:decimal"
//                            'КоличествоНеадаптированное' => 0, // "xs:decimal"
//                            'КоличествоБрак' => 0, // "xs:decimal"
//                        ],
//
//                    ],
//                ]
//            ]
//        ];
//
//        return $std;
    }
    // OK
    function GetChangedOutboundOrders()
    {
        file_put_contents('EDIService-GetChangedOutboundOrders.xml', print_r("-", true) . "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return $service->GetChangedOutboundOrders();

//        $std = [
//            'GetChangedOutboundOrdersResult' => [
//                'Запись' => [
//                    'Идентификатор' => "736beb9c-6158-11e7-9c01-94de80bd5cf8",
//                    'НомерДокументаПрообраза' => "00000001888",
//                    'СтрочноеПредставлениеДокументаПрообраза' => "Реализация ТМЗ и услуг 00000001888 от 05.07.2017 12:03:56",
//                    'ЗонаОтгрузки' => 0,
//                    'Дата' => "2017-07-05T12:03:56",
//                    'Статус' => 3,
//                    'Комментарий' => "Все ок",
//                    'СписаниеПодПеремещение' => false,
//                    'Получатель' => "APIS ТОО",
//                    'Адрес' => "Киев 12 345",
//                    'ВидОтгрузки' => 0,
//                    'Спецификация' => [
//                        [
//                            'МатНомер' => '',
//                            'Артикул' => 1,
//                            'ФабНомер' => '',
//                            'КомНомер' => '',
//                            'НомерГТД' => '',
//                            'Количество' => 5, // "xs:decimal"
//                            'КоличествоНеадаптированное' => 0, // "xs:decimal"
//                            'КоличествоБрак' => 0, // "xs:decimal"
//                        ],
//                        [
//                            'МатНомер' => '06165000',
//                            'Артикул' => '62782410',
//                            'ФабНомер' => '',
//                            'КомНомер' => '',
//                            'НомерГТД' => '',
//                            'Количество' => 5, // "xs:decimal"
//                            'КоличествоНеадаптированное' => 0, // "xs:decimal"
//                            'КоличествоБрак' => 0, // "xs:decimal"
//                        ],
//                    ]
//                ]
//            ]
//        ];
//
//        return $std;
    }
    // OK
    function MarkAsUnchangedOutboundOrder($id = '', $status = 0)
    {
        file_put_contents('EDIService-MarkAsUnchangedOutboundOrder.xml', print_r("id = ", true) . "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedOutboundOrder.xml', print_r($id, true) . "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedOutboundOrder.xml', print_r("status = ", true). "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedOutboundOrder.xml', print_r($status, true). "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedOutboundOrder.xml', "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return  $service->MarkAsUnchangedOutboundOrder($id);
//        $std = [
//            'MarkAsUnchangedOutboundOrderResult' => true
//        ];
//
//        return $std;
    }

    // PRODUCTS
    // OK Y
    function UpdateMATMAS($list)
    { // НоменклатураСписок

        file_put_contents('EDIService-UpdateMATMAS.xml', print_r($list, true) . "\n" . "\n", FILE_APPEND);
        $service = new APIService();
        return $service->UpdateMATMAS($list);
//        $std = [
//            'UpdateMATMASResponse' => "что тут должно быть?"
//        ];
//
//        return $std;
    }

    //STOCK
    // OK
    function GetStock($date)
    {
        file_put_contents('EDIService-GetStock.xml', print_r($date, true) . "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return  $service->GetStock($date);

//        $std = [
//            'GetStockResult' => [
//                'Запись' => [
//                    [
//                        'МатНомер' => "06165000",
//                        'Артикул' => "62782410",
//                        'Зона' => 0,
//                        'Количество' => 5,
//                    ],
//                    [
//                        'МатНомер' => "",
//                        'Артикул' => "1",
//                        'Зона' => 0,
//                        'Количество' => 5,
//                    ],
//                ]
//            ]
//        ];
//
//        return $std;
    }

    public function GetSerialStock($date = '',$materialNo = '',$articul = '')
    {
        file_put_contents('EDIService-GetSerialStock.xml', "date :" . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetSerialStock.xml', print_r($date , true) . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetSerialStock.xml', "materialNo: " . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetSerialStock.xml', print_r($materialNo, true) . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetSerialStock.xml', "articul : " . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetSerialStock.xml', print_r($articul, true) . "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return $service->GetSerialStock($date = '',$materialNo = '',$articul = '');

//        $std = [
//            'GetSerialStockResult' => [
//                'Запись' => [
//                    [
//                        'МатНомер'=>"06165000",
//                        'Артикул'=>"62782410",
//                        'Зона'=>0,
//                        'ФабНомер'=>"zxzx1111",
//                    ],
//                    [
//                        'МатНомер'=>"",
//                        'Артикул'=>"1",
//                        'Зона'=>0,
//                        'ФабНомер'=>"zxzx2222",
//                    ],
//                ]
//            ]
//        ];
//
//        return $std;
    }

    // MOVEMENT
    //TODO STEP TWO
    public function SendMovementOrder($order)
    {
        file_put_contents('EDIService-SendMovementOrder.xml', print_r($order, true) . "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return $service->SendMovementOrder($order);

//        $std = [
//            'SendMovementOrderResponse' => true // "что должно быть в ответе"
//        ];
//
//        return $std;
    }

    public function CancelMovementOrder($id)
    {
        file_put_contents('EDIService-CancelMovementOrder.xml', print_r($id, true) . "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return $service->CancelMovementOrder($id);
//        $std = [
//            'CancelMovementOrderResponse' => "что должно быть в ответе"
//        ];
//        return $std;
    }

    public function GetMovementOrders($idList) {
        file_put_contents('EDIService-GetMovementOrders.xml', print_r($idList, true) . "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return $service->GetMovementOrders($idList);
//        $std = [
//            'GetMovementOrdersResult' => [
//                'Запись'=> [
//                    'Идентификатор' => "736beb9d-6158-11e7-9c01-94de80bd5cf8",
//                    //'Идентификатор' => "736beb9c-6158-11e7-9c01-94de80bd5cf8",
//                    'НомерДокументаПрообраза' => "00000001888",
//                    'СтрочноеПредставлениеДокументаПрообраза' => "Реализация ТМЗ и услуг 00000001888 от 05.07.2017 12:03:56",
//                    'ЗонаПриемки' => false,
//                    'ЗонаОтправки' => false,
//                    'Дата' => '2017-07-05T12:03:56',
//                    'Статус' => '4',
//                    'Комментарий' => 'Комментарий',
//                    'Спецификация' => [[
//                        'МатНомер' => '',
//                        'Артикул' => '1',
//                        'ФабНомер' => '',
//                        'КомНомер' => '',
//                        'НомерГТД' => '',
//                        'Количество' => '1',
//                        'КоличествоНеадаптированное' => '0',
//                        'КоличествоБрак' => '0',
//                    ],
//                        [
//                            'МатНомер' => '06165000',
//                            'Артикул' => '62782410',
//                            'ФабНомер' => '',
//                            'КомНомер' => '',
//                            'НомерГТД' => '',
//                            'Количество' => '1',
//                            'КоличествоНеадаптированное' => '0',
//                            'КоличествоБрак' => '0',
//                        ]],
//                ]
//            ]
//        ];
//
//        return $std;
    }

    public function GetChangedMovementOrders() {

        file_put_contents('EDIService-GetChangedMovementOrders.xml', print_r("", true) . "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return $service->GetChangedMovementOrders();

//        $std = [
//            'GetChangedMovementOrdersResult' => [
//                'Запись'=> [
//                    'Идентификатор' => "736beb9d-6158-11e7-9c01-94de80bd5cf8",
//                    //'Идентификатор' => "736beb9c-6158-11e7-9c01-94de80bd5cf8",
//                    'НомерДокументаПрообраза' => "00000000136",
//                    'СтрочноеПредставлениеДокументаПрообраза' => "Перемещение ТМЗ 00000000136 от 05.07.2017 14:57:53</СтрочноеПредставлениеДокументаПрообраза",
//                    'ЗонаПриемки' => 1,
//                    'ЗонаОтправки' => 0,
//                    'Дата' => '2017-07-05T12:03:56',
//                    'Статус' => '4',
//                    'Комментарий' => 'Комментарий',
//                    'Спецификация' => [[
//                        'МатНомер' => '',
//                        'Артикул' => '1',
//                        'ФабНомер' => '',
//                        'КомНомер' => '',
//                        'НомерГТД' => '',
//                        'Количество' => '1',
//                        'КоличествоНеадаптированное' => '0',
//                        'КоличествоБрак' => '0',
//                    ],
//                        [
//                            'МатНомер' => '06165000',
//                            'Артикул' => '62782410',
//                            'ФабНомер' => '',
//                            'КомНомер' => '',
//                            'НомерГТД' => '',
//                            'Количество' => '1',
//                            'КоличествоНеадаптированное' => '0',
//                            'КоличествоБрак' => '0',
//                        ]],
//                ]
//            ]
//        ];
//
//        return $std;
    }

    public function MarkAsUnchangedMovementOrder($id = '',$status = 0)
    {
        file_put_contents('EDIService-MarkAsUnchangedMovementOrder.xml', print_r("id = ", true) . "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedMovementOrder.xml', print_r($id, true) . "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedMovementOrder.xml', print_r("status = ", true). "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedMovementOrder.xml', print_r($status, true). "\n", FILE_APPEND);
        file_put_contents('EDIService-MarkAsUnchangedMovementOrder.xml', "\n" . "\n", FILE_APPEND);


        $service = new APIService();
        return $service->MarkAsUnchangedMovementOrder($id);

//        $std = [
//            'MarkAsUnchangedMovementOrderResult' => true
//        ];
//
//        return $std;
    }

    public function GetProductMovement($beginDate = '',$endDate = '',$materialNo = '',$articul = '')
    {
        file_put_contents('EDIService-GetProductMovement.xml', "beginDate:" . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetProductMovement.xml', print_r($beginDate, true) . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetProductMovement.xml', "endDate: " . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetProductMovement.xml', print_r($endDate, true) . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetProductMovement.xml', "materialNo: " . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetProductMovement.xml', print_r($materialNo, true) . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetProductMovement.xml', "articul : " . "\n" . "\n", FILE_APPEND);
        file_put_contents('EDIService-GetProductMovement.xml', print_r($articul, true) . "\n" . "\n", FILE_APPEND);

        $service = new APIService();
        return $service->GetProductMovement($beginDate);

//        $std = [
//            'GetProductMovementResult' => [
//                'Запись' => [
//                    [
//                        'ДатаВремя' => "2017-07-04T13:50:07",
//                        'МатНомер' => "",
//                        'Артикул' => "1",
//                        'Зона' => 0,
//                        'IDдокумента' => "736beb9a-6158-11e7-9c01-94de80bd5cf8",
//                        'Приход' => 0,
//                        'Расход' => 10,
//                    ],
////                    [
//                        'ДатаВремя' => "2017-07-04T13:50:07",
//                        'МатНомер' => "",
//                        'Артикул' => "1",
//                        'Зона' => 1,
//                        'IDдокумента' => "736beb9a-6158-11e7-9c01-94de80bd5cf8",
//                        'Приход' => 10,
//                        'Расход' => 0,
//                    ],

//                    [
//                        'ДатаВремя' => "2017-07-04T13:50:07",
//                        'МатНомер' => "06165000",
//                        'Артикул' => "62782410",
//                        'Зона' => 0,
//                        'IDдокумента' => "736beb9a-6158-11e7-9c01-94de80bd5cf8",
//                        'Приход' => 10,
//                        'Расход' => 0,
//                    ],
//                    [
//                        'ДатаВремя' => "2017-07-04T13:50:07",
//                        'МатНомер' => "",
//                        'Артикул' => "1",
//                        'Зона' => 0,
//                        'IDдокумента' => "736beb9c-6158-11e7-9c01-94de80bd5cf8",
//                        'Приход' => 0,
//                        'Расход' => 5,
//                    ],
//                    [
//                        'ДатаВремя' => "2017-07-04T13:50:07",
//                        'МатНомер' => "06165000",
//                        'Артикул' => "62782410",
//                        'Зона' => 0,
//                        'IDдокумента' => "736beb9c-6158-11e7-9c01-94de80bd5cf8",
//                        'Приход' => 0,
//                        'Расход' => 5,
//                    ]
//                ]
//            ]
//        ];
//
//        return $std;
    }
}