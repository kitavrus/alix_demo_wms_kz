<?php

namespace stockDepartment\modules\other\controllers;


use common\api\DeFactoSoapAPI;
use common\components\BarcodeManager;
use common\modules\client\models\Client;
use common\modules\crossDock\models\ConsignmentCrossDock;
use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use stockDepartment\modules\outbound\models\AllocationListForm;

use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
use stockDepartment\modules\warehouseDistribution\managers\defacto\api\DeFactoSoapAPIV2Manager;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use Yii;
use yii\web\Response;

class DefaultController extends \stockDepartment\components\Controller
{
    /*
     *
     * */
    public function actionTest()
    {

//        $client = new \SoapClient('http://195.46.145.251/DFStore.ProxyServices.UAT/ExternalWMS/ExternalWMSProxy.asmx?wsdl');
//        $params = array("request" => array('PageSize' => "1", 'PageIndex' => "0", "CountAllItems" => "false", "BusinessUnitId" => "1029", "ProcessRequestedDataType" => "Full"));
//        $response = $client->GetSKUContentWMSData($params);
//        echo $response->GetSKUContentWMSDataResult->Data->SKUContentWMS->SkuId;
//        die('-EXIT-');



//        die('TEST DIE');
        $api =  new DeFactoSoapAPIV2();
//        $api->GetWarehouseAppointments();
//        $apiManager = new DeFactoSoapAPIV2Manager();
//        $apiResult = $apiManager->processingWarehouseAppointments();
//        $apiResult = $apiManager->processingAppointmentInBoundData('D10AA00000128');
//        $apiResult = $apiManager->processingWarehousePickings();
//        VarDumper::dump($apiResult,10,true);
//        die;

        // 1
        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];
        $result = $api->sendRequest('GetWarehouseAppointments',$params);

        // 2 Ждем 10-15 мин
//        $params['request'] = [
//            'AppointmentBarcode'=>'D10AA00000043',
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//        ];
//        $result = $api->sendRequest('MarkAppointmentforInBound',$params);

        //3
//        $params['request'] = [
//            'AppointmentBarcode'=>'D10AA00000043',
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//        ];
//        $result = $api->sendRequest('GetAppointmentInBoundData',$params);

        // PackUniversalIdentifierId - Это ID шк короба  его содержимое я ищу в MasterData
        // PackQuantity - количество коробов. Из турции 1
        // SkuUniversalIdentifierId - ид баркода лота. Ищем в мастер дате один ко многим
        // SkuQuantity - Общее кол-во лотов во всех коробах
        // SkuBarcode - Это штрих код лота
//        $data['InBoundFeedBackThreePLResponse'][] = [
//            'InBoundId'=>'46',
//            'AppointmentUniversalIdentifier'=>'',
//            'PackBarcode'=>'',
//            'PackQuantity'=>'',
//            'SkuBarcode'=>'',
//            'SkuQuantity'=>'',
//        ];
        // 4 Ждем, пока не работает
//        $data = $result = [];
//        $data['InBoundFeedBackThreePLResponse'][] = [
//            'InBoundId'=>'47',
//            'AppointmentBarcode'=>'D10AA00000043',
//            'PackBarcode'=>'2430000072486',
//            'SkuBarcode'=>'9000003635927',
//            'SkuQuantity'=>'1',
//        ];
//
//        $data['InBoundFeedBackThreePLResponse'][] = [
//                'InBoundId'=>'48',
//                'AppointmentBarcode'=>'D10AA00000043',
//                'PackBarcode'=>'2430000072423',
//                'SkuBarcode'=>'9000003635927',
//                'SkuQuantity'=>'1',
//        ];
//
//        $params['request'] = [
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//            'FeedBackData'=>$data
//        ];
////
//        $result = $api->sendRequest('SendInBoundFeedBackData',$params);
        // 5
//        $params['request'] = [
//            'BusinessUnitId'=>'1029',
//            'AppointmentBarcode'=>'D10AA00000043',
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//        ];
//        $result = $api->sendRequest('MarkAppointmentforCompleted',$params);



        // OUTBOUND
        // 1
//        $params['request'] = [
//            'BusinessUnitId'=>'1029',
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//        ];
//        $result = $api->sendRequest('GetWarehousePickings',$params);
        // 2 ok
//        $params['request'] = [
//            'PickingId'=>'71',
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//        ];
//
//        $result = $api->sendRequest('MarkPickingforOutBound',$params);
        // 3

//        $params['request'] = [
//            'PickingId'=>'71',
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//        ];
//
//        $result = $api->sendRequest('GetPickingOutBoundData',$params);



        // 'PageSize' => "1", 'PageIndex' => "0", "CountAllItems" => "false", "BusinessUnitId" => "1029", "ProcessRequestedDataType" => "Full"
//        $params['request'] = [
//            'BusinessUnitId'=>'1029', //  рабочий 1029
//            'ProcessRequestedDataType'=>'Full',
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//        ];
//        $result = $api->sendRequest('GetBusinessUnitWMSData',$params);
//        $result = $api->sendRequest('GetMasterData',$params);

//        $row = [];
//        $row['OutBoundFeedBackThreePLResponse'][] = [
//            'OutBoundId'=>'16', // это ид из SendOutBoundFeedBackData
//            'InBoundId'=>'0', // Что это такое ?
//            'PackBarcode'=>'700000001', // наш короб
//            'SkuBarcode'=>'8680654893689', // Что это такое ?
//            'SkuQuantity'=>'12', // кол-во лотов в коробе
//            'WaybillSerial'=>'KZK', //это не меняется
//            'WaybillNumber'=>'16', //ReservationId - брать из GetWarehousePickings (его тут нет) и он пуст в GetPickingOutBoundData
//            'Volume'=>'32', //размер короба это mapM3ToBoxSize 12, 17, 31 и т.д.
//            'CargoShipmentNo'=>'-', //не используем
//        ];
//
//        $params['request'] = [
//            'BusinessUnitId'=>'1029',
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//            'FeedBackData'=> $row // feedBackData
//        ];
//
//        $result = $api->sendRequest('SendOutBoundFeedBackData',$params);



        VarDumper::dump($params,30,true);
        echo "<br />";
        VarDumper::dump($result,30,true);

        echo "<br />";
        echo "<br />";
        die('-END-');
        // MarkAppointmentforInBound - Фиксируем когда мы получили товар на склад но не приняли. просто фиксируем время получение груза к нам на склад
//        $params = [
//            'appointmentUniversalIdentifier'=>'D10AA00000137',
//        ];
//        $result = $api->sendRequest('MarkAppointmentforInBound',$params); Ждем 15 мин потом вызываем GetAppointmentInBoundData
//
        // 2 FOR TEST [AppointmentUniversalIdentifierId] => 2748854 D10AA00000135
        // [BusinessUnitId] => 1034 Почему 1034 а не 1026
//        $params = [
//            'appointmentUniversalIdentifier'=>'D10AA00000125',
//        ];
//
//        $result = $api->sendRequest('GetAppointmentInBoundData',$params);

        // PackUniversalIdentifierId - Это ID шк короба  его содержимое я ищу в MasterData
        // PackQuantity - количество коробов. Из турции 1
        // SkuUniversalIdentifierId - ид баркода лота. Ищем в мастер дате один ко многим
        // SkuQuantity - Общее кол-во лотов во всех коробах
        // SkuBarcode - Это штрих код лота

//        $result = $api->GetAppointmentInBoundData('D10AA00000137');
//        [GetAppointmentInBoundDataResult] => stdClass#2
//        (
//        [HasError] => false
//        [Data] => stdClass#3
//        (
//        [InBoundThreePL] => [
//        0 => stdClass#4
//        (
//        [CreatedDate] => '2016-04-28T11:01:03.717'
//        [ModifiedDate] => null
//        [Timestamp] => '\0\0\0��o'
//        [Creator] => 76
//        [Modifier] => null
//        [IsItemDeleted] => false
//        [ActionId] => 0
//        [Id] => 23  это для метода SendInBoundFeedBackData значение InBoundId
//        [PurchaseOrder] => '57'
//        [Preadmission] => '15'
//        [Label] => '2757003' штрих код на коробе/ Если это из бангладеша или китая то это поле неюник
//        [SkuId] => 2368265
//        [Ean] => '132534' // Это баркод лота
//        [Quantity] => '300.00' //
//        [Carton] => '100.00' // Если картон больше одного то это из Китая или Бангладеша. Если из турци то равно 1. Сколько коробов
//        [FromBusinessUnitId] => 1034
//        [Status] => 'ReadyforProcessing'
//        [ToBusinessUnitId] => null // Если не пустой то это КРОСДОК. и это код магазина в который мы отгружаем
//        )


        // Label - штрих код на коробе
//        $row['InBoundFeedBackThreePL'][] = [
/*        $row['InBoundFeedBackThreePLResponse'][] = [
            'SkuQuantity'=>'10',
            'InBoundId'=>'3',
        ];

        $params = [
//            'SendInBoundFeedBackData'=>[
                'feedBackData'=> $row // feedBackData
//                ]
        ];*/

//        VarDumper::dump($params,10,true);
//        die;
//        $result = $api->sendRequest('SendInBoundFeedBackData',$params);
//
//      // SendInBoundFeedBackData2 - Отправляем лишнее. то чего мы не получили через GetAppointmentInBoundData.
//      Заполняем все поля которые есть в апи http://195.46.145.251/DFStore.ProxyServices.UAT/ExternalWMS/ExternalWMSProxy.asmx?op=SendInBoundFeedBackData
         // Вместо InBoundId будет  D10AA00000125

        /*
         * <InBoundId>long</InBoundId>
          <PackUniversalIdentifierId>long</PackUniversalIdentifierId>
          <PackBarcode>string</PackBarcode>
          <PackQuantity>decimal</PackQuantity>
          <SkuUniversalIdentifierId>long</SkuUniversalIdentifierId>
          <SkuBarcode>string</SkuBarcode>
          <SkuQuantity>decimal</SkuQuantity>
         *
         * */

        // MarkAppointmentforCompleted - вызываем когда накладная принята на склад.


        ////---------------------------------
//        $params = [
//            'businessUnitId'=>'1026', //  рабочий 1029
//        ];
//
//        $result = $api->sendRequest('GetWarehousePickings',$params);


//        $params = [
//            'pickingId'=>'23', // 23, 35, 36
//        ];
        //
        //$result = $api->sendRequest('MarkPickingforOutBound',$params);
        // ждем 15 мин после вызова

//        [Id] => 15
//        [WarehouseId] => 1
//        [Status] => 'New'
//        [AsyncJobCount] => 5
//        [ShiftDefinitionItemId] => null
//        [PickingType] => 'Shipment'
//        [PickingSubType] => 'Other'
//        [PickingFromLocationId] => 4
//        [PickingToLocationId] => 86570
//        [TotalQuantity] => '52.00'
//        [RemainingQuantity] => '0.00'
//        [BusinessUnitCount] => 1
//        [PickingSorterId] => 1
//        [PickingWMSStatus] => 'MarkedforOutBoundData'

//        $params = [
////            'pickingId'=>'23', // 23, 35, 36
//            'pickingId'=>'15', // 23, 35, 36
//        ];

//        [CreatedDate] => '2016-04-28T15:44:58.753'
//        [ModifiedDate] => null
//        [Timestamp] => '\0\0\0�.s�'
//        [Creator] => 76
//        [Modifier] => null
//        [IsItemDeleted] => false
//        [ActionId] => 0
//        [Id] => 8 // нужна будет для подтверждения
//        [Parti] => '13'
//        [ResId] => '21'
//        [SkuId] => 1221842 // один ко многим того что нужно зарезервировать
//        [Quantity] => '32.00' // количество
//        [Status] => 'ReadyforProcessing'
//        [PickingId] => 15
//        [ToBusinessUnitId] => 254  // код магазина
//        [CargoBusinessUnitId] => null

//        $params = [
//            'pickingId'=>'21',
//        ];
//
//         $result = $api->sendRequest('GetPickingOutBoundData',$params);
        // pickingId - номер портии это прсто ид из базы
        // BatchId - номер заказа (не используем)
        // ReservationId - номер партии
        // Id - по этому ид мы будем делать подтверждение  SendOutBoundFeedBackData поле OutBoundId



        // SkuId - ид лота брать из мастер дата
        // Quantity - количество лотов в заказе

        // SendOutBoundFeedBackData
/*        $row = [];
        $row['OutBoundFeedBackThreePLResponse'][] = [
            'OutBoundId'=>'1', // это ид из SendOutBoundFeedBackData
            'PackBarcode'=>'70000123456', // наш короб
            'SkuQuantity'=>'12', // кол-во лотов в коробе
            'SkuUniversalIdentifierId'=>'1221846', //id лота (мастер дата). это товар который мы отстканировали
            'WaybillSerial'=>'KZK', //это не меняется
            'WaybillNumber'=>'25', //ReservationId - брать из GetWarehousePickings
            'Volume'=>'32', //размер короба это mapM3ToBoxSize 12, 17, 31 и т.д.
            'CargoShipmentNo'=>'s', //не используем
        ];

        $params = [
            'feedBackData'=> $row // feedBackData
        ];*/
//        // KoliId
//        $result = $api->sendRequest('SendOutBoundFeedBackData',$params);

        // MarkPickingforCompleted - подтверждаем отгрузку pickingId

//        VarDumper::dump($params,30,true);
//        echo "<br />";
//        VarDumper::dump($result,30,true);
//        die;

//        $q = InboundOrder::find();
//        $q->select('id');
//        $q->andWhere(['client_id'=>1]);
//        $q->indexBy('id');
//        $q->asArray();
//        foreach( $q->batch() as $v) {
//            VarDumper::dump(array_keys($v),10,true);
//        }
//
//        die();
    }


    public function actionConfirmInboundOrder()
    { // confirm-inbound-order
        // 228
        die('die return start NO RUN');
        /*
        $client_id = 2;
        $io = new InboundOrder();
        $io->client_id = $client_id;
        $io->order_number = '508896-2';
        $io->order_type = InboundOrder::ORDER_TYPE_INBOUND;
        $io->status =  Stock::STATUS_INBOUND_NEW;
        $io->expected_qty =  '0';
        $io->accepted_qty =  '0';
        $io->accepted_number_places_qty = '0';
        $io->expected_number_places_qty = '0';
        $io->save(false);

        $items = [
//            [
//              'barcode'=> '9000003483900',
//              'model'=>'Е3177AKZA',
//              'qty'=>'57'
//            ],[
//              'barcode'=> '9000003506876',
//              'model'=>'E4075AKZD',
//              'qty'=>'40'
//            ],[
//              'barcode'=> '9000003484280',
//              'model'=>'E3177AKZB',
//              'qty'=>'50'
//            ],
[
              'barcode'=> '9000003509556',
              'model'=>'E4154AKZA',
              'qty'=>'32'
            ],
        ];
        $expectedQty = 0;
        foreach($items as $data) {

            $item = new InboundOrderItem();
            $item->inbound_order_id =  $io->id;
            $item->expected_qty = $data['qty'];
            $item->accepted_qty = 0;
            $item->product_barcode = $data['barcode'];
            $item->product_model = $data['model'];
            $item->status = Stock::STATUS_INBOUND_NEW;
            $item->save(false);

            $expectedQty += $item->expected_qty;

            for ($i = 1; $i <= $item->expected_qty; $i++) {

                $stock = new Stock();
                $stock->client_id = $client_id;
                $stock->inbound_order_id = $item->inbound_order_id;
                $stock->product_barcode = $item->product_barcode;
                $stock->product_model = $item->product_model;
                $stock->status = Stock::STATUS_INBOUND_NEW;
                $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
                $stock->save(false);
            }
        }
        InboundOrder::updateAll(['expected_qty' => $expectedQty], ['id' =>  $io->id]);
        echo  $io->id."<br />";
        die('die return YPA');
        */

//        $inboundOrderID = '5864';
//        $inboundOrderID = '5346';  // 508896
        //$inboundOrderID = '7846'; // 508896-1
        //$inboundOrderID = '11678'; // 510128
        //$inboundOrderID = '11680'; // 510305-06-07-08
        $inboundOrderID = '16118'; // 510397
//        $order_number = '';
        $io = InboundOrder::findOne($inboundOrderID);
        //S: отпарвляем данные приходной накладной по API для DeFacto
//        if($io->client_id == 2 && YII_ENV == 'prod') { // id 2 = Defacto
        $rows  = [];
        if($items = InboundOrderItem::findAll(['inbound_order_id'=>$io->id]) ) {
            foreach($items as $item) {
                if($item->accepted_qty >= 1 && $item->product_barcode == '9000004544433') {
                    $rows[] = [
                        'YurtDisiIrsaliyeNo'=>$io->order_number,
                        'Barkod'=>$item->product_barcode,
                        'CrossDockType'=>'P',
                        'Miktar'=>$item->accepted_qty,
                    ];
                }
            }
        }
//        VarDumper::dump($rows,10,true);
//        die;
        $urunOnKabulTamamlandiResult = [];
        if(!empty($rows) && 0) {

            $api = new DeFactoSoapAPI();
            $api->confirmInboundOrder($rows);

            $urunOnKabulTamamlandiResult = $api->getUrunOnKabulTamamlandiInbound($io->order_number);

            $extraFields = [];
            if($io->extra_fields) {
                $extraFields = Json::decode($io->extra_fields);
            }
//
            $extraFields['UrunOnKabulSend'] = $rows;
            $extraFields['UrunOnKabulTamamlandiResultRespons'] = $urunOnKabulTamamlandiResult;
            $io->extra_fields = Json::encode($extraFields);
            $io->status = Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API;
            $io->save(false);
        }

//        $extraFields = [];
//        $urunOnKabulTamamlandiResult = [];
//        $extraFields['UrunOnKabulSend'] = $rows;
//        $extraFields['UrunOnKabulTamamlandiResultRespons'] = $urunOnKabulTamamlandiResult;
//        $io->extra_fields = Json::encode($extraFields);
//        $io->status = Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API;
//        $io->save(false);

        echo "<br />";
        echo $io->order_number."<br />";
        echo "<br />";
        VarDumper::dump($rows,10,true);

        echo "<br />";
        VarDumper::dump($urunOnKabulTamamlandiResult,10,true);
//        }
        //E: отпарвляем данные приходной накладной по API для DeFacto

        die('die return');
    }

    public function actionIndex()
    {
        die('die return');
/*        for($i = 1; $i <= 32; $i++) {
            $cci = new CrossDockItems();
            $cci->cross_dock_id = 687;
            $cci->box_barcode = '010'.$i;
            $cci->expected_number_places_qty = 1;
            $cci->box_m3 = 0.06;
            $cci->save(false);
        }*/
//        die('-add-32-cross-dock-item');
//        die('-DIE-');
//        die('start - die - STOP ');
        // 1685 
//        $id = 611;
//        $oo = OutboundOrder::findOne($id);
//
//        $logTime = $oo->order_number.'-'.time();
//        $head =  'Barcode'.';'
//                .'Model'.';'
//                .'Quantity'.';';
//        file_put_contents('outbound-'.$logTime.'.log',$head."\n",FILE_APPEND);
//
//
//        if($oo) {
//           $ooi =  OutboundOrderItem::find()->andWhere(['outbound_order_id'=>$oo->id])->all();
//            if($ooi) {
//                $tofileRow = '';
//                foreach ($ooi as $item) {
//                    $tofileRow .= $item->product_barcode.';'.
//                                  $item->product_model.';'.
//                                  $item->accepted_qty.';'."\n";
//                }
//                file_put_contents('outbound-'.$logTime.'.log',$tofileRow."\n",FILE_APPEND);
//            }
//        }

//        die('-Outbound export product to file-');

//        die('BIGin end - STOP');
//        $products = [
//             //нет коробов
//            '9000002135732'=>1, // 12501696-38414-1
//            '9000002283334'=>1, // полностью отгружен со склада
//            '9000003100548'=>1, // полностью отгружен со склада
//            '9000002307009'=>1,  // полностью отгружен со склада
//             // нет в системе
//            '9000002318029'=>1,
//            '9000003792927'=>1,
//            '9000002142990'=>1,
//            '9000003265360'=>1,
//            '9000002193787'=>1,

        // 05082015
//               '9000002132816'=>1,
//               '9000003070797'=>1,
//               '9000002135183'=>1,
//               '9000003474328'=>1,
//               '9000002281675'=>1,
//               '9000002243451'=>1,
//               '9000002243697'=>1,
//               '9000002255447'=>1,
//        ];

        //$products = [
//            '700000062013' => [
//                '9000002132816'=>['qty'=>1,'model'=>'509'],
//                '9000003070797'=>['qty'=>1,'model'=>'509'],
//                '9000002135183'=>['qty'=>1,'model'=>'509'],
//                '9000003474328'=>['qty'=>1,'model'=>'509'],
//                '9000002281675'=>['qty'=>1,'model'=>'D7439ATXXL'],
//                '9000002243451'=>['qty'=>1,'model'=>'509'],
//                '9000002243697'=>['qty'=>1,'model'=>'509'],
//                '9000002255447'=>['qty'=>1,'model'=>'E0404AA'],
//            ],
//            '700000059899'=> [
//                '9000002064629'=>['qty'=>1,'model'=>'D7645AB'],
//            ],
//            '700000060030'=> [
//                '9000004003183'=>['qty'=>1,'model'=>'E7594AA'],
//            ],
//            '700000062013'=> [ // 06082015
//                '9000002286144'=>['qty'=>1,'model'=>'D7426ABS'],
//            ],
//            '700000070644'=> [ // 27082015
//                '9000003547152'=>['qty'=>2,'model'=>'E2774AKZB'],
//            ],
//            '700000069436'=> [ // 27082015
//                '9000003508962'=>['qty'=>1,'model'=>'E4061AKZA'],
//            ],
//            '700000070887'=> [ // 06102015
//                '9000004219348'=>['qty'=>17,'model'=>'E2502AKZG2'],
//            ],
//        ];
//
//        $client_id = '2';
//        foreach($products as $primary_address=>$productInCategory) {
//            foreach($productInCategory as $product=>$productOptions) {
//                for ($i=0; $i<$productOptions['qty']; $i++) {
//                    $stock = new Stock();
//                    $stock->client_id = $client_id;
//                    $stock->inbound_order_id = '5204';
//                    $stock->product_barcode = $product;
//                    $stock->product_model = $productOptions['model'];
//                    $stock->status = Stock::STATUS_INBOUND_NEW;
//                    $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
//                    $stock->primary_address = $primary_address;
////                    $stock->secondary_address = '1-6-09-1';
////                    $stock->save(false);
//                }
//            }
//        }
//        die('-YPA-add-product-to-stock-YES');

//        $client_id = '2';
////        $product_barcode = '';
//        $product_model = '';
//        $primary_address = '700000069033';
//        $primary_address = '700000062013';
//        foreach ($products as $product=>$qty) {
//            $stock = new Stock();
//            $stock->client_id = $client_id;
////            $stock->inbound_order_id = $ioi->inbound_order_id;
//            $stock->product_barcode = $product;
//            $stock->product_model = $product_model;
//            $stock->status = Stock::STATUS_INBOUND_NEW;
//            $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
//            $stock->primary_address = $primary_address;
//            $stock->save(false);
//        }
//        die('-YPA-');
//        for($i=0; $i < 6; $i++) {
//            $cdItem = new CrossDockItems();
//            $cdItem->cross_dock_id = 417;
//            $cdItem->box_barcode = '000000406847'.$i;
//            $cdItem->status = 0;
//            $cdItem->expected_number_places_qty = 1;
//            $cdItem->box_m3 = '0.06';
//            $cdItem->created_user_id = '2';
//            $cdItem->updated_user_id = '2';
//            $cdItem->created_at = '1438089464';
//            $cdItem->updated_at = '1438089464';
//            $cdItem->save(false);
//        }
//        echo "-OK-";

//        die('BIGin end - STOP');
//        $client_id = 21; // koton
//        $stockAll = Stock::find()->where(['client_id'=>$client_id])->andWhere('outbound_picking_list_barcode !=""')->all();
//        foreach($stockAll as $stock) {
//            $newStr = str_replace('-21-','-',$stock->outbound_picking_list_barcode);
//            $nList =  OutboundPickingLists::find()->where(['barcode'=>$newStr])->one();
//            $stock->outbound_picking_list_barcode = $newStr;
//            $stock->outbound_picking_list_id = $nList->id;
//            if($nList->status == OutboundPickingLists::STATUS_END) {
//                $stock->status = Stock::STATUS_OUTBOUND_PICKED;
//            }
//
//            if($nList->status == OutboundPickingLists::STATUS_BEGIN) {
//                $stock->status = Stock::STATUS_OUTBOUND_PICKING;
//            }
//
//
//            $stock->save(false);
//        }

//        die('BIGin end');
        //s: confirm all inbound order koton
//        $client_id = 21; // koton
//        $inboundAll = InboundOrder::find()->where(['client_id'=>$client_id])->all();
//        if($inboundAll) {
//            foreach ($inboundAll as $io) {
//                if((int)$io->accepted_qty  < 1)  {
//                    continue;
//                }
//                $inboundItemsAll = InboundOrderItem::find()->where(['inbound_order_id'=>$io->id])->all();
//                if($inboundItemsAll) {
//                    foreach($inboundItemsAll as $inboundItem) {
//                        if($inboundItem->expected_qty != $inboundItem->accepted_qty) {
//                            continue 2;
//                        }
//                    }
//                }

//                if ($io->status == Stock::STATUS_INBOUND_CONFIRM) {
////                    $messages [] = Yii::t('inbound/errors','Накладная с номером ' . $io->order_number . ' уже принята');
//                } else {
//                    $io->status = Stock::STATUS_INBOUND_CONFIRM;
//                    $io->date_confirm = time();
//                    $io->save(false);
//
//                    Stock::updateAll([
//                        'status' => Stock::STATUS_INBOUND_CONFIRM,
//                        'status_availability' => Stock::STATUS_AVAILABILITY_YES,
//                    ], [
//                        'inbound_order_id' => $io->id,
//                        'status' => [
//                            Stock::STATUS_INBOUND_SCANNED,
//                            Stock::STATUS_INBOUND_OVER_SCANNED,
//                        ]
//                    ]);
//
//                    Stock::deleteAll('inbound_order_id = :inbound_order_id AND status != :status', [':inbound_order_id' => $io->id, ':status' => Stock::STATUS_INBOUND_CONFIRM]);
//
//                    if ($coi = ConsignmentInboundOrders::findOne($io->consignment_inbound_order_id)) {
//                        $coi->status = Stock::STATUS_INBOUND_SCANNING;
//                        if (!InboundOrder::find()->where('status != :status AND consignment_inbound_order_id = :consignment_inbound_order_id', [':status' => Stock::STATUS_INBOUND_CONFIRM, ':consignment_inbound_order_id' => $io->consignment_inbound_order_id])->exists()) {
//                            $coi->status = Stock::STATUS_INBOUND_CONFIRM;
//                        }
////                        $coi->save(false);
//                    }
//                }
//            }
//        }
        //E: confirm all inbound order koton

        // S upload koton outbound order
//        $items = [];
//        $file = 'tmp-file/outbound-koton-dc-astanav1.csv';
//        if (($handle = fopen($file, "r")) !== FALSE) {
//            $row = 0;
//            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//                $row++;
//                if ($row > 1) {
//                    VarDumper::dump($data, 10, true);
//                    $items[] = [
//                        'product_barcode'=> trim($data[1]),
//                        'expected_qty'=> trim($data[0]),
//                    ];
//                }
//            }
//        }
//        die('-STOP-');



//        $client_id = 21; // koton
//        $party_number = 'dcahs2307';
//        $order_number = $party_number;
//
//        if ($outboundModelIDs = OutboundOrder::find()->select('id')->where(['client_id' => $client_id, 'parent_order_number' => $party_number])->column()) {
//
//            // TODO Доделать !!!!!
//            //S: Reset
//            OutboundOrder::updateAll(['data_created_on_client' => '', 'expected_qty' => '0', 'accepted_qty' => '0', 'allocated_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_NEW], ['id' => $outboundModelIDs]);
//            ConsignmentOutboundOrder::updateAll(['expected_qty' => '0', 'accepted_qty' => '0', 'allocated_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_NEW], ['client_id' => $client_id, 'party_number' => $party_number]);
//            OutboundOrderItem::updateAll(['expected_qty' => '0', 'accepted_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_NEW], ['outbound_order_id' => $outboundModelIDs]);
//            OutboundPickingLists::deleteAll(['outbound_order_id' => $outboundModelIDs]);
//            Stock::updateAll([
//                'box_barcode' => '',
//                'outbound_order_id' => '0',
//                'outbound_picking_list_id' => '0',
//                'outbound_picking_list_barcode' => '',
//                'status' => Stock::STATUS_NOT_SET,
//                'status_availability' => Stock::STATUS_AVAILABILITY_YES
//            ], ['outbound_order_id' => $outboundModelIDs]);
//            //E: Reset
//        }



//        if (!($consignmentModel = ConsignmentOutboundOrder::findOne(['client_id' => $client_id, 'party_number' => $party_number]))) {
//            $consignmentModel = new ConsignmentOutboundOrder();
//
//            $consignmentModel->client_id = $client_id;
//            $consignmentModel->party_number = $party_number;
//            $consignmentModel->status = Stock::STATUS_OUTBOUND_NEW;
//            $consignmentModel->save(false);
//        }

//        if (!($outboundModel = OutboundOrder::findOne(['client_id' => $client_id, 'parent_order_number' => $party_number, 'order_number' => $order_number]))) {
//            $outboundModel = new OutboundOrder();
//            $outboundModel->status = Stock::STATUS_OUTBOUND_NEW;
//            $outboundModel->client_id = $client_id;
//            $outboundModel->consignment_outbound_order_id = $consignmentModel->id;
//            $outboundModel->parent_order_number = $party_number;
//            $outboundModel->order_number = $order_number;
////        $outboundModel->data_created_on_client = $oolItem['data_created_on_client'];
//            $outboundModel->to_point_id = 190;// Koton Astana	ТРЦ HAN SHATYR
//            $outboundModel->to_point_title = 668; // internal koton shop code
//            $outboundModel->save(false);
//        }

//        $expected_qty = 0;
//        if ($items) {
//            foreach ($items as $line) {
//                if (!($ooiModel = OutboundOrderItem::findOne(['outbound_order_id' => $outboundModel->id, 'product_barcode' => $line['product_barcode']]))) {
//                    $ooiModel = new OutboundOrderItem();
//                    $ooiModel->expected_qty = 0;
//                    $ooiModel->status = Stock::STATUS_OUTBOUND_NEW;
//                    $ooiModel->outbound_order_id = $outboundModel->id;
//                    $ooiModel->product_barcode = $line['product_barcode'];
//                }
//
//                $ooiModel->expected_qty += $line['expected_qty'];
//                $ooiModel->save(false);
//
//                $expected_qty += $ooiModel->expected_qty;
//            }
//        }

//        $outboundModel->expected_qty = $expected_qty;
//        $outboundModel->save(false);

        //
//        $consignmentModel->expected_qty += $expected_qty;
//        $consignmentModel->save(false);
//
//        $dpOrderNumber = $outboundModel->parent_order_number;// . ' ' . $outboundModel->order_number;
//        if ($dpOrder = TlDeliveryProposalOrders::findOne(['client_id' => $client_id, 'order_id' => $outboundModel->id, 'order_number' => $dpOrderNumber])) {
//            $dp = TlDeliveryProposal::findOne($dpOrder->tl_delivery_proposal_id);
//        } else {
//            $dp = new TlDeliveryProposal();
//            $dpOrder = new TlDeliveryProposalOrders();
//        }
//
//        $dp->status = TlDeliveryProposal::STATUS_NEW;
//        $dp->client_id = $outboundModel->client_id;
//        $dp->route_from = '4'; // НАШ склад
//        $dp->route_to = $outboundModel->to_point_id;
//        $dp->cash_no = TlDeliveryProposal::METHOD_CHAR;
//        $dp->save(false);

        // Добавить заказы
//        $dpOrder->client_id = $dp->client_id;
//        $dpOrder->tl_delivery_proposal_id = $dp->id;
//        $dpOrder->order_id = $outboundModel->id;
//        $dpOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
//        $dpOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
//        $dpOrder->order_number = $outboundModel->parent_order_number;// . ' ' . $outboundModel->order_number;
//        $dpOrder->save(false);

        // Reservation on stock
//        if ($oos = OutboundOrder::find()->select('id')->where(['parent_order_number' => $outboundModel->parent_order_number])->asArray()->all()) {
//            foreach ($oos as $order) {
//                Stock::AllocateByOutboundOrderId($order['id']);
//            }
//        }
//        echo "END-OK";
//        die;
        // E upload koton outbound order

//        $file = 'tmp-file/return_order_items.csv';
//
//        if (($handle = fopen($file, "r")) !== FALSE) {
//            $row = 0;
//            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//                $row++;
//                if ($row > 1) {
//                    VarDumper::dump($data,10,true);
//                    $reternItem = new ReturnOrderItems();
//                    $reternItem->return_order_id = 808;
//                    $reternItem->product_barcode = $data[3];
//                    $reternItem->status = $data[5];
//                    $reternItem->expected_qty = $data[6];
//                    $reternItem->accepted_qty = $data[7];
//                    $reternItem->created_user_id = $data[10];
//                    $reternItem->updated_user_id = $data[11];
//                    $reternItem->created_at = $data[12];
//                    $reternItem->updated_at = $data[13];
////                    $reternItem->save(false);
//                }
//            }
//        }
//        $outID = '524';
//        $outboundOrderModel = OutboundOrder::findOne($outID);
//        $data = DeFactoSoapAPI::preparedDataForOutboundConfirm($outID);
//        $rows = $data;
//        if (!empty($rows) && 0) {
//            $api = new DeFactoSoapAPI();
//            $apiData = [];
//            if ($apiResponse = $api->confirmOutboundOrder($rows)) {
//                if (empty($apiResponse['errors'])) {
//                    $apiData = $apiResponse['response'];
//                }
//            }
//            $extraFields = [];
//            if (!empty($outboundOrderModel->extra_fields)) {
//                $extraFields = Json::decode($outboundOrderModel->extra_fields);
//            }
//            $extraFields ['requestToAPI'] = $rows;
//            $extraFields ['RezerveDagitimResult'] = $apiData;
//
//            $outboundOrderModel->extra_fields = Json::encode($extraFields);
//            $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
//            $outboundOrderModel->save(false);
//        }
//        VarDumper::dump($rows,10,true);
//        VarDumper::dump($extraFields,10,true);

//        InboundOrder::deleteAll(['client_id'=>1]);
//        Stock::deleteAll(['client_id'=>1]);
//        OutboundOrder::deleteAll(['client_id'=>1]);

/*        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";*/
        // 375 - 13017243 + +
        // 372 - 13017228 + +
        // 369 - 13017214 + +
        // 366 - 13017199 + +
        // 365 - 13017195 + +
        // 363 - 13017186 + +
        // 362 - 13017182 + +
        // 377 - 13019166 + +
        // NEW
        /*
            365 - 13019166,
                  13017248,
13017243,
13017238,
13017233,
13017228,
13017223,
13017218,
13017214,
13017209,
13017204,
13017199,
13017195,
13017190,
13017186,
13017182

         * */
//        die('---STOP--0');

        $orders = [
//            '361', // 12925967 17
//            '362', // 13017182 16
//            '363', // 13017186 15
//            '364', // 13017190 14
//            '365', // 13017195 13
//            '366', // 13017199 12
//            '367', // 13017204 11
//            '368', // 13017209 10
//            '369', // 13017214 9
//            '370', // 13017218 8
//            '371', // 13017223 7
//            '372', // 13017228 6
//            '373', // 13017233 5
//            '374', // 13017238 4
//            '375', // 13017243 3
//            '376', // 13017248 2
//            '377', // 13019166 1
//                '770'// 14564350
//                '807'// 14645461
//                '811'// 14705550
//                '854'// 14815230
//                '884',// 14269275
//                '883',// 14269269
//                '882',// 14269265
//                '881',// 14269261
//                '979',// 15197301

//                '1056',// 15458931 // 28.10.2015
//                '1054',// 15458926
//                '1049',// 15458911
//                '1047',// 15458905
//                '1046',// 15458902
//                '1044',// 15458896
//                '1042',// 15458890
//                  '1053',// 15458923
//                  '1055',// 15458928
//                  '1048',// 15458908
//                  '1045',// 15458899
//                  '1041',// 15458886
//                  '1039',// 15458880
//                  '1038',// 15458878
                  '3310',// 25905703

        ];

//        $outID = -1;
//        $outID = 375;
//        $outID = 365;
        $str = 'RezerveId;Barkod;Miktar;IrsaliyeNo;KoliId;KoliDesi;KoliKargoEtiketId;' . "\n";
        foreach($orders as $outID) {
            $outboundOrderModel = OutboundOrder::findOne($outID);
            $data = DeFactoSoapAPI::preparedDataForOutboundConfirm($outID);

            foreach ($data as $row) {
                $str .= '"' . $row['RezerveId'] . '";"' . $row['Barkod'] . '";"' . $row['Miktar'] . '";"' . $row['IrsaliyeNo'] . '";"' . $row['KoliId'] . '";"' . $row['KoliDesi'] . '";"' . $row['KoliKargoEtiketId'] . '";' . "\n";
            }

            $rows = $data;
            if (!empty($rows) && 0) {
                $api = new DeFactoSoapAPI();
                $apiData = [];
                if ($apiResponse = $api->confirmOutboundOrder($rows)) {
                    if (empty($apiResponse['errors'])) {
                        $apiData = $apiResponse['response'];
                    }
                }
                $extraFields = [];
                if (!empty($outboundOrderModel->extra_fields)) {
                    $extraFields = Json::decode($outboundOrderModel->extra_fields);
                }
                $extraFields ['requestToAPI'] = $rows;
                $extraFields ['RezerveDagitimResult'] = $apiData;

                $outboundOrderModel->extra_status = $apiData;
                $outboundOrderModel->extra_fields = Json::encode($extraFields);
                $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
                $outboundOrderModel->save(false);
            }

            echo "<br />";
            echo $outboundOrderModel->order_number."<br />";
            echo "<br />";
            VarDumper::dump($rows,10,true);
            file_put_contents('preparedDataForOutboundConfirm-'.date('Y-m-d_h').'-ordersV6.csv', $str, FILE_APPEND);
        }
        die('-STOP-');


/*        $rows = $data;
        if(!empty($rows) && 0) {
            $api = new DeFactoSoapAPI();
            $apiData = [];
            if($apiResponse = $api->confirmOutboundOrder($rows)) {
                if (empty($apiResponse['errors'])) {
                    $apiData = $apiResponse['response'];
                }
            }
            $extraFields = [];
            if(!empty($outboundOrderModel->extra_fields)) {
                $extraFields = Json::decode($outboundOrderModel->extra_fields);
            }
            $extraFields ['requestToAPI'] = $rows;
            $extraFields ['RezerveDagitimResult'] = $apiData;

            $outboundOrderModel->extra_fields = Json::encode($extraFields);
            $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
            $outboundOrderModel->save(false);
        }


        VarDumper::dump($data,10,true);
        echo "<br />";
        echo "<br />";
        die();*/
        //
/*       $outboundOne = OutboundOrder::findOne(359);
       $outboundOne = OutboundOrder::findOne(360);
       $outboundOne = OutboundOrder::findOne(358); // тут есть одинаковый товар в двух коробах
//       $outboundOne = OutboundOrder::findOne(357);
       $stockAll = Stock::find()->select('product_barcode, count(id) as accepted_qty, box_barcode, box_size_barcode')
                          ->where(['outbound_order_id'=>$outboundOne->id])
                          ->groupBy('product_barcode, box_barcode')
                          ->orderBy('box_barcode')
                          ->asArray()
                          ->all();

        $stockCountBoxBarcodeAll = Stock::find()->select('box_barcode, box_size_barcode')
            ->where(['outbound_order_id'=>$outboundOne->id])
            ->groupBy('box_barcode')
            ->asArray()
            ->all();

        if($stockAll) {
            $tmp = [];
            $miktarSum = 0;
            foreach($stockAll as $box=>$stock) {
                $boxI = 1;
                $k = 0;
                if(!empty($stockCountBoxBarcodeAll)) {
                    foreach($stockCountBoxBarcodeAll as $b) {
                        $k++;
                        if($b['box_barcode'] == $stock['box_barcode']) {
                            $boxI = $k;
                        }
                    }
                }

                if ($stock['accepted_qty'] >=1) {
                    $tmp[] = [
                        'RezerveId' => $outboundOne->order_number,
                        'Barkod' => $stock['product_barcode'],
                        'Miktar' => $stock['accepted_qty'],
                        'IrsaliyeNo' =>$outboundOne->order_number,
                        'KoliId' => $boxI, // который короб
                        'KoliDesi' => $stock['box_size_barcode'], // m3
                        'KoliKargoEtiketId' => $stock['box_barcode'], //
                    ];
                    $miktarSum += $stock['accepted_qty'];
                }
            }
        }*/
//
//
//        VarDumper::dump($tmp,10,true);
//        echo "<br />";
//        VarDumper::dump($stockCountBoxBarcodeAll,10,true);
//        echo "<br />";
//        echo "Sum Qty: ".$miktarSum;
//        echo "<br />";
//        die('-STOP-');

//        $pathToCSVFile = 'tir8.csv';

     /*   if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $str  = $data[1];
//                echo iconv('CP866','utf-8',$str)."<br />";
//                echo '<br />';
                foreach ($data as $key => $value){
                     $shopCode =  iconv('CP866','utf-8',$value);
//                    echo '<br />';
                    $shopCode = trim($shopCode);
                    $shopCode = str_replace(' ','=',$shopCode);
                    $shopCode = explode('=',$shopCode);

//                    if (isset($shopCode[1])) {
//                        unset ($shopCode[1]);
//                    }
                    $shop =  '-EMPTY-';
                    if (isset($shopCode[0])) {
                        $shop = $shopCode[0];
                    }

//                    $shop = implode('',$shopCode);
//                    echo '<br />';
                    $c = mb_strtoupper($shop,'utf-8');

                   if( !($s = Store::findClientStoreByShopCode(Client::CLIENT_COLINS, $c))) {
                       echo 'NO - '.$key.' '.$c.'<br />';
                   } else {
                       echo 'YES - '.$key.' '.$c.' '.$s.'<br />';
                   }

//                    VarDumper::dump($c,10,true);
//                    echo '-<br />';
                }
//                VarDumper::dump($data,10,true);
                die('-STOP-');
            }
        }*/

//        $str = 'Áàðêîä';
//        $str = '��મ�;';
//        echo  mb_detect_encoding($str);
//        echo iconv('CP866','utf-8',$str)."<br />";
//        echo iconv('CP866','utf-8',$this->cp866towin($str))."<br />";
//        echo $str."<br />";
//        echo "<br />";
//        echo $c = mb_convert_encoding($str, "windows-1251", "ASCII")."<br />";
//        echo $c = mb_convert_encoding($c, "UTF-8", "auto")."<br />";
//        echo mb_convert_encoding($str, "windows-1251", "auto")."<br />";
//        echo mb_convert_encoding($str, "windows-1251", "auto")."<br />";
//        echo mb_convert_encoding($str, "windows-1251", "utf-8")."<br />";
//        echo mb_convert_encoding($str, "utf-8", "auto")."<br />";
//        echo mb_convert_encoding($str, "UTF-8", mb_detect_encoding($str, "UTF-8, ISO-8859-1, ISO-8859-15", true))."<br />";

//        $map = array(
//            chr(0x8A) => chr(0xA9),
//            chr(0x8C) => chr(0xA6),
//            chr(0x8D) => chr(0xAB),
//            chr(0x8E) => chr(0xAE),
//            chr(0x8F) => chr(0xAC),
//            chr(0x9C) => chr(0xB6),
//            chr(0x9D) => chr(0xBB),
//            chr(0xA1) => chr(0xB7),
//            chr(0xA5) => chr(0xA1),
//            chr(0xBC) => chr(0xA5),
//            chr(0x9F) => chr(0xBC),
//            chr(0xB9) => chr(0xB1),
//            chr(0x9A) => chr(0xB9),
//            chr(0xBE) => chr(0xB5),
//            chr(0x9E) => chr(0xBE),
//            chr(0x80) => '&euro;',
//            chr(0x82) => '&sbquo;',
//            chr(0x84) => '&bdquo;',
//            chr(0x85) => '&hellip;',
//            chr(0x86) => '&dagger;',
//            chr(0x87) => '&Dagger;',
//            chr(0x89) => '&permil;',
//            chr(0x8B) => '&lsaquo;',
//            chr(0x91) => '&lsquo;',
//            chr(0x92) => '&rsquo;',
//            chr(0x93) => '&ldquo;',
//            chr(0x94) => '&rdquo;',
//            chr(0x95) => '&bull;',
//            chr(0x96) => '&ndash;',
//            chr(0x97) => '&mdash;',
//            chr(0x99) => '&trade;',
//            chr(0x9B) => '&rsquo;',
//            chr(0xA6) => '&brvbar;',
//            chr(0xA9) => '&copy;',
//            chr(0xAB) => '&laquo;',
//            chr(0xAE) => '&reg;',
//            chr(0xB1) => '&plusmn;',
//            chr(0xB5) => '&micro;',
//            chr(0xB6) => '&para;',
//            chr(0xB7) => '&middot;',
//            chr(0xBB) => '&raquo;',
//        );
//        echo $x =  html_entity_decode(mb_convert_encoding(strtr($str, $map), 'UTF-8', 'ISO-8859-2'), ENT_QUOTES, 'UTF-8');
//
//        echo mb_convert_encoding($x, "windows-1251", "auto")."<br />";
//        echo mb_convert_encoding($x, "windows-1251", "utf-8")."<br />";
//        echo mb_convert_encoding($x, "utf-8", "auto")."<br />";
//        echo mb_convert_encoding($x, "UTF-8", mb_detect_encoding($x, "UTF-8, ISO-8859-1, ISO-8859-15", true))."<br />";
//        echo iconv('utf-8','windows-1251',$x)."<br />";

//        $consCrossDockID = 1;
//        $cCD = ConsignmentCrossDock::findOne($consCrossDockID);
//        $crossDockIDs = CrossDock::find()->select('id')->where(['consignment_cross_dock_id' => $cCD->id])->column();


//        $crossDockIDs = [3];
//        $crossDockIDs = [2];
//        $crossDockIDs = [4];
//        $cd =  CrossDock::findOne($crossDockIDs);
//        $crossDockItems = CrossDockItems::find()->where(['cross_dock_id'=>$crossDockIDs])->asArray()->all();
//
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($crossDockItems,10,true);
//        $m3Sum = 0;
//        foreach($crossDockItems as $item) {
//            $m3Sum += $item['box_m3'];
//        }
//        echo "Sum box_m3 : ".$m3Sum."<br />";
//        echo "CD box_m3  : ".$cd->box_m3."<br />";
//        echo "CD expected_number_places_qty  : ".$cd->expected_number_places_qty."<br />";
//        $returns = ReturnOrder::find()->all();
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        $i= 0;
//        if($returns) {
//            foreach($returns as $return) {
//                if($return->extra_fields) {
//                    if($extraFields = Json::decode($return->extra_fields)) {
//                        if(isset($extraFields['IadeKabulResult->responseAPIErrorMessage']) && !empty($extraFields['IadeKabulResult->responseAPIErrorMessage'])) {
//                            echo "'".$return->order_number . "' '" . $extraFields['IadeKabulResult->responseAPIErrorMessage']."'<br />";
//                            $i++;
//                        }
//                    }
//                }
//            }
//            echo "<br />".$i;
//        }


        /*
        echo $m3BoxValue = BarcodeManager::getBoxM3(15);
        // для заказа 209
        // не заполняется id cross-dock в delivery proposal
//        die;

        $dp = TlDeliveryProposal::findOne(2360);

        $outboundID = 209;
        $outboundOrderModel = OutboundOrder::findOne($outboundID);
        $stocks = Stock::find()->where(['outbound_order_id'=>$outboundID])->all;

        foreach($stocks as $stock) {
            $stock->box_size_m3 = $m3BoxValue;
            $stock->box_size_barcode = 15;
//            $stock->save(false);

        }

        $items = Stock::find()
            ->select('id,outbound_order_id, box_barcode, box_size_m3')
            ->where([
                'outbound_order_id' => $outboundID,
                'status' => [
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
            ])
            ->groupBy('box_barcode')
            ->asArray()
            ->all();


        //S: Высчитываем m3 всех коробов заказа
        $m3Sum = 0;
        foreach($items as $boxM3) {
            if(isset($boxM3['box_size_m3']) && !empty($boxM3['box_size_m3']))
                $m3Sum += $boxM3['box_size_m3'];
        }

        $dp->mc = $m3Sum;
        $dp->mc_actual = $m3Sum;
        $dp->number_places_actual = count($items);
        $dp->number_places = count($items);
//        $dp->save(false);

        $outboundOrderModel->mc = $m3Sum;
        $outboundOrderModel->accepted_number_places_qty = count($items);
//        $outboundOrderModel->save(false);

        if($dpOrderModel = TlDeliveryProposalOrders::findOne(['order_id'=>$outboundOrderModel->id])) {
            $dpOrderModel->number_places = count($items);
            $dpOrderModel->mc = $m3Sum;
            $dpOrderModel->mc_actual = $m3Sum;
//            $dpOrderModel->save(false);
        }
        //E: Высчитываем m3 всех коробов заказа
        */

        /*
         *  SELECT count( box_barcode )
FROM `stock`
WHERE `outbound_order_id` =209
AND box_size_m3 =0
         *
         * */

        return $this->render('index');
    }

    /*
     *Only for test
     * */
    public function actionSavePrinters()
    {
        $printers = Yii::$app->request->post('printers');
        $str = Yii::$app->request->userIP.' '.implode(',',$printers)."\n";
        file_put_contents('printers-list.log',$str,FILE_APPEND);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['YPA'];
    }
    /*
 *
 * */
    public function actionA4()
    {
        return $this->renderPartial('a4');
    }
    /*
    *
    * */
    public function actionBl()
    {
        return $this->renderPartial('bl');
    }
    /*
     *
     * */
//    public function actionA4()
    public function actionPdf()
    {
//        return "OK";
        $model = new AllocationListForm();
        $outputData = [];
        $remnantInBox = []; // остаток в коробке
        $client_id = Client::CLIENT_COLINS;
        $model->box_barcode = '4200124025';
        $storeArray = TLHelper::getStockPointArray($client_id, true, false, '{internal_code}');

//        if ($model->load(Yii::$app->request->get())) {

        $boxBarcode = ltrim($model->box_barcode, 'k');
        $boxBarcode = ltrim($boxBarcode, 'K');
        if ($order = InboundOrder::find()->andWhere(['client_box_barcode' => $boxBarcode, 'client_id' => $client_id])->one()) {

//            if ($order = InboundOrder::find()->andWhere(['client_box_barcode' => $model->box_barcode, 'client_id' => $client_id])->one()) {

            $productBarcodes = InboundOrderItem::find()
                ->select('product_barcode, expected_qty')
                ->where(['inbound_order_id' => $order->id])
                ->asArray()
                ->all();

            $boxProductBarcodes = ArrayHelper::map($productBarcodes, 'product_barcode', 'expected_qty');

            $outboundIds = OutboundOrder::find()->select('id')->where(['client_id' => $client_id, 'status' => Stock::STATUS_OUTBOUND_NEW])->orderBy('to_point_id ASC')->column();

            file_put_contents('colins-allocate.log', "\n" . "\n" . "\n" . "--NEW--" . "\n" . "\n" . "\n", FILE_APPEND);

            if (!empty($boxProductBarcodes)) {
                foreach ($boxProductBarcodes as $productBarcode => $inBoxQty) {
                    $inBoxDiffAllocated = $inBoxQty;
                    while ($inBoxDiffAllocated) {
                        $outboundOrderItems = OutboundOrderItem::find()
                            ->where(['product_barcode' => $productBarcode, 'outbound_order_id' => $outboundIds])
                            ->andWhere('expected_qty != allocated_qty')
                            ->orderBy('outbound_order_id ASC')
                            ->limit(1)
                            ->all();

                        if (!empty($outboundOrderItems)) {
                            foreach ($outboundOrderItems as $outboundOrderItem) {
                                $expectedQtyItem = intval($outboundOrderItem->expected_qty);
                                $allocatedQtyItem = intval($outboundOrderItem->allocated_qty);
                                $diffInOrder = $expectedQtyItem - $allocatedQtyItem;
                                $diffWithBox = $diffInOrder - $inBoxDiffAllocated;

                                file_put_contents('colins-allocate.log', "\n" . "\n", FILE_APPEND);
                                file_put_contents('colins-allocate.log', "outboundOrderItem ID = " . $outboundOrderItem->id . "\n", FILE_APPEND);
                                file_put_contents('colins-allocate.log', "productBarcode = " . $productBarcode . "\n", FILE_APPEND);
                                file_put_contents('colins-allocate.log', "expectedQtyItem = " . $expectedQtyItem . "\n", FILE_APPEND);
                                file_put_contents('colins-allocate.log', "allocatedQtyItem = " . $allocatedQtyItem . "\n", FILE_APPEND);
                                file_put_contents('colins-allocate.log', "diffInOrder = " . $diffInOrder . "\n", FILE_APPEND);
                                file_put_contents('colins-allocate.log', "diffWithBox = " . $diffWithBox . "\n", FILE_APPEND);
                                file_put_contents('colins-allocate.log', "inBoxDiffAllocated = " . $inBoxDiffAllocated . "\n", FILE_APPEND);
                                file_put_contents('colins-allocate.log', "inBoxQty = " . $inBoxQty . "\n", FILE_APPEND);


                                if ($diffWithBox == 0) {
                                    if ($oo = $outboundOrderItem->outboundOrder) {
                                        $outputData[$outboundOrderItem->product_barcode][] = [
                                            'outbound_order_id' => $oo->id,
                                            'shop_id' => $oo->to_point_id,
                                            'product_barcode' => $outboundOrderItem->product_barcode,
                                            'product_model' => $outboundOrderItem->product_model,
                                            'expected_qty' => $inBoxDiffAllocated,
                                        ];

                                        $outboundOrderItem->allocated_qty += $inBoxDiffAllocated;
                                        $outboundOrderItem->status = Stock::STATUS_INBOUND_SORTING;
                                        $outboundOrderItem->detachBehavior('auditBehavior');
//                                            $outboundOrderItem->save(false);

                                        // STOCK
                                        if ($inStocks = Stock::find()->where([
                                                'client_id' => $client_id,
                                                'inbound_order_id' => $order->id,
                                                'product_barcode' => $outboundOrderItem->product_barcode,
                                                'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET]
                                        )->limit($inBoxDiffAllocated)->all()
                                        ) {
                                            foreach ($inStocks as $stockLine) {
                                                $stockLine->outbound_order_id = $oo->id;
                                                $stockLine->status = Stock::STATUS_INBOUND_SORTED;
                                                $stockLine->status_availability = Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED;
                                                $stockLine->detachBehavior('auditBehavior');
//                                                    $stockLine->save(false);
                                            }
                                        }


                                        $inBoxDiffAllocated = 0;
                                        $oo->detachBehavior('auditBehavior');
//                                        $oo->recalculateOrderItems(); // TODO Тут должен ставится статус!!!
                                        file_put_contents('colins-allocate.log', "outbound_order_id = " . $oo->id . "\n", FILE_APPEND);
                                        continue;
                                    }
                                }

                                //Если одижаем 4 в коробе 3
                                // 4 - 0 = 4
                                // 4 - 3 = 1
                                if ($diffWithBox > 0) {
                                    if ($oo = $outboundOrderItem->outboundOrder) {
                                        $outputData[$outboundOrderItem->product_barcode][] = [
                                            'outbound_order_id' => $oo->id,
                                            'shop_id' => $oo->to_point_id,
                                            'product_barcode' => $outboundOrderItem->product_barcode,
                                            'product_model' => $outboundOrderItem->product_model,
                                            'expected_qty' => $inBoxDiffAllocated,
                                        ];

                                        $outboundOrderItem->allocated_qty += $inBoxDiffAllocated;
                                        $outboundOrderItem->status = Stock::STATUS_INBOUND_SORTING;
                                        $outboundOrderItem->detachBehavior('auditBehavior');
//                                            $outboundOrderItem->save(false);

                                        // STOCK
                                        if ($inStocks = Stock::find()->where([
                                                'client_id' => $client_id,
                                                'inbound_order_id' => $order->id,
                                                'product_barcode' => $outboundOrderItem->product_barcode,
                                                'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET]
                                        )->limit($inBoxDiffAllocated)->all()
                                        ) {

                                            foreach ($inStocks as $stockLine) {
                                                $stockLine->outbound_order_id = $oo->id;
                                                $stockLine->status = Stock::STATUS_INBOUND_SORTED;
                                                $stockLine->status_availability = Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED;
                                                $stockLine->detachBehavior('auditBehavior');
//                                                    $stockLine->save(false);
                                            }
                                        }

                                        $inBoxDiffAllocated = 0;
                                        $oo->detachBehavior('auditBehavior');
//                                        $oo->recalculateOrderItems(); // TODO Тут должен ставится статус!!!
                                        file_put_contents('colins-allocate.log', "outbound_order_id = " . $oo->id . "\n", FILE_APPEND);
                                        continue;
                                    }
                                }

                                //Если одижаем 4 в коробе 7
                                // 4 - 0 = 4
                                // 4 - 7 = -3
                                if ($diffWithBox < 0) {
                                    if ($oo = $outboundOrderItem->outboundOrder) {
                                        $outputData[$outboundOrderItem->product_barcode][] = [
                                            'outbound_order_id' => $oo->id,
                                            'shop_id' => $oo->to_point_id,
                                            'product_barcode' => $outboundOrderItem->product_barcode,
                                            'product_model' => $outboundOrderItem->product_model,
                                            'expected_qty' => $diffInOrder,
                                        ];

                                        $outboundOrderItem->allocated_qty += $diffInOrder;
                                        $outboundOrderItem->status = Stock::STATUS_INBOUND_SORTING;
//                                            $outboundOrderItem->save(false);

                                        // STOCK
                                        if ($inStocks = Stock::find()->where([
                                                'client_id' => $client_id,
                                                'inbound_order_id' => $order->id,
                                                'product_barcode' => $outboundOrderItem->product_barcode,
                                                'status_availability' => Stock::STATUS_AVAILABILITY_NOT_SET]
                                        )->limit($outboundOrderItem->expected_qty)->all()
                                        ) {
                                            foreach ($inStocks as $stockLine) {
                                                $stockLine->outbound_order_id = $oo->id;
                                                $stockLine->status = Stock::STATUS_INBOUND_SORTED;
                                                $stockLine->status_availability = Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED;
                                                $stockLine->detachBehavior('auditBehavior');
//                                                    $stockLine->save(false);
                                            }
                                        }
                                        $oo->detachBehavior('auditBehavior');
//                                        $oo->recalculateOrderItems(); // TODO Тут должен ставится статус!!!
                                        $inBoxDiffAllocated = $diffWithBox * -1;
                                        file_put_contents('colins-allocate.log', "outbound_order_id = " . $oo->id . "\n", FILE_APPEND);
                                        continue;
                                    }
                                }
                            }
                        } else {
                            $remnantInBox[$productBarcode] = $inBoxDiffAllocated;
                            $inBoxDiffAllocated = 0;
                        }
//                            }
                    } // end While
                } // end foreach boxProductBarcodes
            }
            $order->status = Stock::STATUS_INBOUND_SORTED;
            $order->detachBehavior('auditBehavior');
//                $order->save(false);
        }

        $orderNumber = $model->box_barcode;

        $html = '';
        $html .= '<table width="60%" style="padding:2px">
            <tr>
                <td width="100%" colspan="2"><h1>Лист распределения Colins</h1></td>
            </tr>
             <tr>
                <td width="30%">Дата: ' . date('Y.m.d') . '</td>
                <td width="30%">ШК короба: ' . $orderNumber . '</td>
            </tr>
        </table><br>';

        $activeShops = [];
        if ($outputData) {
            foreach ($outputData as $data) {
                foreach ($data as $item) {
//            $shopIDs[$item['shop_id']] = $item['shop_id'];
                    $activeShops[$item['shop_id']] = isset($storeArray[$item['shop_id']]) ? $storeArray[$item['shop_id']] : '-НЕ найден-';
                }
            }
        }

        asort($activeShops);
        $shopSortingArray = [];
        asort($storeArray);


        $min = min($activeShops);
        $max = max($activeShops);

        if ($storeArray) {
            foreach ($storeArray as $storeId => $shopCode) {
                if ($shopCode >= $min && $shopCode <= $max) {
                    if (array_key_exists($storeId, $activeShops)) {
                        $shopSortingArray[$storeId] = $shopCode;
                    } else {
                        $shopSortingArray[$storeId] = $shopCode;
                    }
                }
            }
        }
//$pdf->SetFont('arial', 'b', 26); //ok
        $html = '<table width="100%" style="padding:2px; font-size: 11px" border="1" align="left">
            <tr>
                <th width="11%" style="background-color: #d9dad9">' . Yii::t('stock/forms', 'ШК') . '</th>
                <th width="10%" style="background-color: #d9dad9">' . Yii::t('stock/forms', 'Модель') . '</th>' .
            '<th width="7%" style="background-color: #d9dad9">' . Yii::t('stock/forms', 'Остаток') . '</th>';

        foreach ($shopSortingArray as $storeId => $shopCode) {
            $html .= '<th width="3%" style="background-color: #d9dad9">' . $shopCode . '</th>';
        }

        $html .= '</tr>';

        $v = [];
        if ($outputData) {
            foreach ($outputData as $data) {
                $html .= '<tr>';
                foreach ($shopSortingArray as $storeId => $shopCode) {
                    $v[$storeId] = '<td>0</td>';
                }
                foreach ($data as $k => $item) {
                    if (!$k) {
                        $html .= '<td>' . $item['product_barcode'] . '</td>
                          <td>' . $item['product_model'] . '</td>'
                            . '<td>' . (isset($remnantInBox[$item['product_barcode']]) ? $remnantInBox[$item['product_barcode']] : 0) . '</td>';
                    }

                    if (isset($v[$item['shop_id']])) {
                        $v[$item['shop_id']] = '<td style="background-color: #eef667">' . $item['expected_qty'] . '</td>';
                    }
                }
                $html .= implode('', $v);
                $html .= '</tr>';
            }
        }

        $html .= '</table>';


        return $html;
    }

    /*
     * Подтверждаем отгрузку по API для DeFacto
     * */
    public function actionConfirmOutboundApiDeFacto__()
    {
//        die('тестовый экшен');
        //        $outID = 365;
        $orders = [
//            '524', // Чимкент 13680081 84 0 +
            //'585', // Караганда 14073644 47 0 +
            //'586', // Усть-Каменогорск 14073653 24 14
//            '579', // Павлодар 14073616 59 0
            '600', // Павлодар 14073616 59 0
        ];
//        $bar = '90x55x15/24';
//        $bar = '10x10x10';
//        $bar = '32'; // 0.096
//        $bar = 0.096 * 333; //
//        $bar = 0.001 * 333; //
//        if(is_numeric($bar) && strlen($bar) <=3)  {
//            echo "Х = ".$bar.' LEN '.strlen($bar);
//        } else {
//            echo "NO = ".$bar.' LEN '.strlen($bar);
//        }
//        echo "<br />";
//        echo round($bar,0);
//        echo $m3BoxValue = BarcodeManager::getBoxM3($bar);
//        $testgetIntegerM3 = [
//            123,
//            0,
//            null,
//            '122',
//            '123,4',
//            '0.4',
//            11.01,
//            0.096,
//            1.0
//        ];

//        foreach($testgetIntegerM3 as $value) {
//            echo $value.' = '.BarcodeManager::getIntegerM3($value).' <br />';
//        }

        // 1693
        // 193
        // 3353 - 1
//        die;

        $str = 'RezerveId;Barkod;Miktar;IrsaliyeNo;KoliId;KoliDesi;KoliKargoEtiketId;' . "\n";
        foreach($orders as $outID) {
            $outboundOrderModel = OutboundOrder::findOne($outID);
            $data = DeFactoSoapAPI::preparedDataForOutboundConfirm($outID);
//            $byOneBarcode = [];
            foreach ($data as $row) {
                $str .= '"' . $row['RezerveId'] . '";"' . $row['Barkod'] . '";"' . $row['Miktar'] . '";"' . $row['IrsaliyeNo'] . '";"' . $row['KoliId'] . '";"' . $row['KoliDesi'] . '";"' . $row['KoliKargoEtiketId'] . '";' . "\n";
            }

            $rows = $data;
            foreach($rows as $k=>$row) {
                $byOneBarcode[] = $row;

//                if($row['Barkod'] != '9000002284935') {
//                    $byOneBarcode = [];
//                    continue;
//                } else {
//                    $row['Miktar'] = 1;
//                    $byOneBarcode = [];
//                    $byOneBarcode[] = $row;
//                }

                if ( !empty($byOneBarcode) && 0) {
//                if (!empty($byOneBarcode) && 0) {
                    $api = new DeFactoSoapAPI();
                    $apiData = [];

                    if ($apiResponse = $api->confirmOutboundOrder($byOneBarcode)) {
//                    if ($apiResponse = $api->confirmOutboundOrder($rows)) {
                        if (empty($apiResponse['errors'])) {
                            $apiData = $apiResponse['response'];
                        }
                    }

                    $extraFields = [];

                    if (!empty($outboundOrderModel->extra_fields)) {
                        $extraFields = Json::decode($outboundOrderModel->extra_fields);
                    }

                    $extraFields ['requestToAPI_'.$k] = $byOneBarcode;
                    $extraFields ['RezerveDagitimResult_'.$k] = $apiData;

                    $outboundOrderModel->extra_fields = Json::encode($extraFields);
                    // $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
//                    $outboundOrderModel->save(false);
                    VarDumper::dump($apiResponse,10,true);
                }

                VarDumper::dump($byOneBarcode,10,true);
                $byOneBarcode = [];
            }

            echo "<br />";
            echo $outboundOrderModel->order_number."<br />";
            echo "<br />";
            // 90x55x15/24
            // 700000060168 700000060168 меняем 700000060170
            // 700000060168700000060168 меняем 700000060170
            //

//            VarDumper::dump($byOneBarcode,10,true);
//            VarDumper::dump($rows,10,true);
            file_put_contents('preparedDataForOutboundConfirm-'.$outboundOrderModel->order_number.'-'.date('Y-m-d_H-i-s').'.csv', $str, FILE_APPEND);
        }

        return 'confirm-outbound-api-de-facto';
    }

    /**
     * Lists all Application models.
     * @return mixed
     */
    public function actionWhatsProt()
    {   // Whats-Prot
        $username = "77771506633"; // Your number with country code, ie: 34123456789
        $nickname = "Auto In City"; // Your nickname, it will appear in push notifications
        $debug = true;  // Shows debug log

// Create a instance of WhastPort.
        /*        $code = 749385;
                $password =  '/cdxVuAP//LjohyAjcZ2a6Fn3YI=';
                $w = new \WhatsProt($username, $nickname, $debug);
        //        $w->codeRequest('sms');
        //        $r = $w->codeRegister($code);
                $w->connect();
                $w->loginWithPassword($password);
                $target = '77018015959';// The number of the person you are sending the message
                $message = 'Хелло амига Гы-Гы)';
                $r =  $w->sendMessage($target , $message);*/
//        VarDumper::dump($r,10,true);
//        die('OK');


//
//        $w->connect();
//        $w->loginWithPassword($password);
//        $target = '77018015959';
//        $message = 'Привет ауто ин сити';
//        $r =  $w->sendMessage($target , $message);
        // e
//        die('-STOP-');
//        $w = new WhatsProt($username, '', $debug);
//        echo "\n\nType sms or voice: ";
//        $option = fgets(STDIN);
//        try {
//            $w->codeRequest(trim($option));
//        } catch(\Exception $e) {
//            echo $e->getMessage();
//            exit(0);
//        }
//        echo "\n\nEnter the received code: ";
//        $code = str_replace("-", "", $option);
//        try {
//            $result = $w->codeRegister(trim($code));
//            echo "\nYour password is: ".$result->pw."\n";
//        } catch(\Exception $e) {
//            echo $e->getMessage();
//            exit(0);
//        }


//        $w->connect();
//        $w->codeRequest('sms');
//        $w->checkCredentials();
//        $code = '949430';
//       $r = $w->codeRegister($code);
//        $w->connect(); // Connect to WhatsApp network
//        $w->loginWithPassword($password);
//        $target = '77015223848'; // The number of the person you are sending the message
//        $target = '77018015959';// The number of the person you are sending the message
//        $message = 'Хелло амига Гы-Гы)';

//       $r =  $w->sendMessage($target , $message);
//        $w->pollMessage();
//        VarDumper::dump($r,10,true);
//        VarDumper::dump($w,10,true);
//        die('-YPA-');
    }
}