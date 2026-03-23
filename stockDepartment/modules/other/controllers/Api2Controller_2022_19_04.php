<?php

namespace stockDepartment\modules\other\controllers;

use common\api\DeFactoSoapAPI;
use common\modules\client\models\Client;
use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\product\models\defacto\Products;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use yii;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

//class Api2Controller extends \stockDepartment\components\Controller
class Api2Controller extends \yii\web\Controller
{
    public $pageSize = 250;
    public $layout = '/main-no-top-menu';


    public function actionIndex()
    {
        return $this->render('index');
    }

    /*
     * */
    public function actionGetWarehouseAppointments()
    { // get-warehouse-appointments

        $api = new DeFactoSoapAPIV2();

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];

        $result = $api->sendRequest('GetWarehouseAppointments',$params);

        if($resultDataArray = @ArrayHelper::getValue($result['response'],'GetWarehouseAppointmentsResult.Data.WarehouseAppointmentThreePL')) {
            $resultDataArray = count($resultDataArray) <=1 ? [$resultDataArray] : $resultDataArray;
        } else {
            $resultDataArray = [];
        }

        $providerArray = new yii\data\ArrayDataProvider([
            'allModels' =>$resultDataArray,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        return $this->renderAjax('_get-warehouse-appointments',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'providerArray'=>$providerArray,
            'title'=>'GetWarehouseAppointments',
        ]);
    }

    /*
 * */
    public function actionMarkAppointmentforInBound()
    { // mark-appointmentfor-in-bound
        $AppointmentBarcode = Yii::$app->request->post('AppointmentBarcode');
        $api =  new DeFactoSoapAPIV2();

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'AppointmentBarcode'=>$AppointmentBarcode,
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];
//        $result = [];
        $result = $api->sendRequest('MarkAppointmentforInBound',$params);

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'MarkAppointmentforInBound',
        ]);
    }

    /*
    * */
    public function actionPrepareInboundData()
    { // prepare-inbound-data
        $api =  new DeFactoSoapAPIV2();

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];
        $result = $api->sendRequest('PrepareInboundData',$params);

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'PrepareInboundData',
        ]);
    }
    /*
    * */
    public function actionGetAppointmentInboundData  ()
    { // get-appointment-inbound-data

        $AppointmentBarcode = Yii::$app->request->post('AppointmentBarcode');

        $result['response'] = [];
        $params = [];
        if($AppointmentBarcode) {
            $api =  new DeFactoSoapAPIV2();
            $params['request'] = [
                'BusinessUnitId' => '1029',
                'AppointmentBarcode' => $AppointmentBarcode,
                'PageSize' => '0',
                'PageIndex' => '0',
                'CountAllItems' => false,
            ];
            $result = $api->sendRequest('GetAppointmentInBoundData', $params);
        }

        if($resultDataArray = @ArrayHelper::getValue($result['response'],'GetAppointmentInBoundDataResult.Data.InBoundThreePLDTO')) {
            $resultDataArray = count($resultDataArray) <=1 ? [$resultDataArray] : $resultDataArray;
        } else {
            $resultDataArray = [];
        }

        $providerArray = new yii\data\ArrayDataProvider([
            'allModels' =>$resultDataArray,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        return $this->renderAjax('_get-appointment-inbound-data',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'providerArray'=>$providerArray,
            'title'=>'GetAppointmentInBoundData',
        ]);
    }

    /*
 * */
    public function actionSendInboundFeedbackData()
    { // send-inbound-feedback-data
        $InBoundId = Yii::$app->request->post('InBoundId');
        $AppointmentBarcode = Yii::$app->request->post('AppointmentBarcode');
        $PackBarcode = Yii::$app->request->post('PackBarcode');
        $SkuBarcode = Yii::$app->request->post('SkuBarcode');
        $SkuQuantity = Yii::$app->request->post('SkuQuantity');

        $data = $result = $params = [];
//        if($InBoundId && $AppointmentBarcode && $SkuBarcode) {
            $data['InBoundFeedBackThreePLResponse'][] = [
                'InboundId' => $InBoundId,//'48', // если плюсы то передаем null
//                'InBoundId' => $InBoundId=null,//'48', // если плюсы то передаем null
                'AppointmentBarcode' => $AppointmentBarcode,//'D10AA00000043',
                'LcOrCartonLabel' => $PackBarcode, // OLD PackBarcode '2430000072423',
                'LotOrSingleBarcode' => $SkuBarcode, // OLD SkuBarcode '9000003635927',
                'LotOrSingleQuantity' => $SkuQuantity, //OLD SkuQuantity '1',
            ];

            $params['request'] = [
                'PageSize' => '0',
                'PageIndex' => '0',
                'CountAllItems' => false,
                'FeedBackData' => $data
            ];

            $api = new DeFactoSoapAPIV2();
            $result = $api->sendRequest('SendInBoundFeedBackData', $params);
//        }

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'SendInBoundFeedBackData',
        ]);
    }

    /*
     * */
    public function actionMarkAppointmentforCompleted()
    { // mark-appointmentfor-in-bound
        $AppointmentBarcode = Yii::$app->request->post('AppointmentBarcode');

        $params = $result = [];
        if($AppointmentBarcode) {
            $params['request'] = [
                'BusinessUnitId' => '1029',
                'AppointmentBarcode' => $AppointmentBarcode,
                'PageSize' => '0',
                'PageIndex' => '0',
                'CountAllItems' => false,
            ];

            $api = new DeFactoSoapAPIV2();
            $result = $api->sendRequest('MarkAppointmentforCompleted', $params);
        }

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'MarkAppointmentforCompleted',
        ]);
    }

    // OUTBOUND-OUTBOUND-OUTBOUND-OUTBOUND-OUTBOUND-OUTBOUND-OUTBOUND
    // OUTBOUND-OUTBOUND-OUTBOUND-OUTBOUND-OUTBOUND-OUTBOUND-OUTBOUND
    /*
     *
    * */
    public function actionGetBatchsWms()
    { // get-batchs-wms

        $api =  new DeFactoSoapAPIV2();

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];

        $result = $api->sendRequest('GetBatchsWms',$params);
//        $result = $api->sendRequest('GetWarehousePickings',$params);

        if($resultDataArray = @ArrayHelper::getValue($result['response'],'GetBatchsWmsResult.Data.BatchThreePL')) {
            $resultDataArray = count($resultDataArray) <=1 ? [$resultDataArray] : $resultDataArray;
        } else {
            $resultDataArray = [];
        }

        $providerArray = new yii\data\ArrayDataProvider([
            'allModels' =>$resultDataArray,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        return $this->renderAjax('_get-batchs-wms',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'providerArray'=>$providerArray,
            'title'=>'GetWarehousePickings',
        ]);
    }
    /*
     * TODO NOT USED OLd
    * */
    public function actionMarkPickingforOutbound()
    { // mark-pickingfor-outbound
        $PickingId = Yii::$app->request->post('PickingId');
        $api =  new DeFactoSoapAPIV2();

        $params['request'] = [
            'PickingId'=>$PickingId,
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];

        $result = $api->sendRequest('MarkPickingforOutBound',$params);

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'MarkPickingforOutBound',
        ]);
    }
    /*
    *
    * */
    public function actionPrepareOutboundData()
    { // prepare-inbound-data
        $api =  new DeFactoSoapAPIV2();

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];
        $result = $api->sendRequest('PrepareOutboundData',$params);

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'PrepareOutboundData',
        ]);
    }
    /*
        *
        * */
    public function actionGetOutboundData()
    { // get-outbound-data
        $BatchId = Yii::$app->request->post('BatchId');
        $api =  new DeFactoSoapAPIV2();
        $result['response'] = $params = [];
        if($BatchId) {
            $params['request'] = [
                'BatchId' => $BatchId,
                'BusinessUnitId' => '1029',
                'PageSize' => '0',
                'PageIndex' => '0',
                'CountAllItems' => false,
            ];

            $result = $api->sendRequest('GetOutBoundData', $params);
        }


        if($resultDataArray = @ArrayHelper::getValue($result['response'],'GetOutBoundDataResult.Data.OutBoundThreePLDTO')) {
            $resultDataArray = count($resultDataArray) <=1 ? [$resultDataArray] : $resultDataArray;
        } else {
            $resultDataArray = [];
        }

        $providerArray = new yii\data\ArrayDataProvider([
            'allModels' =>$resultDataArray,
//            'pagination'=>false
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        $storesData = \common\modules\store\models\Store::find()
            ->select('id, shop_code3')
            ->andWhere(['client_id' => Client::CLIENT_DEFACTO])
            ->asArray()
            ->all();

        $storesDataMap = ArrayHelper::map($storesData,'shop_code3','id');

        return $this->render('_get-outbound-data',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'providerArray'=>$providerArray,
            'storesDataMap'=>$storesDataMap,
            'title'=>'GetOutBoundData',
        ]);
    }

    /*
    * */
    public function actionSendOutboundFeedbackData()
    { // send-outbound-feedback-data
        $OutBoundId = Yii::$app->request->post('OutBoundId');
        $InBoundId = Yii::$app->request->post('InBoundId');
        $PackBarcode = Yii::$app->request->post('PackBarcode');
        $SkuBarcode = Yii::$app->request->post('SkuBarcode');
        $SkuQuantity = Yii::$app->request->post('SkuQuantity');
        $WaybillSerial = Yii::$app->request->post('WaybillSerial');
        $WaybillNumber = Yii::$app->request->post('WaybillNumber');
        $Volume = Yii::$app->request->post('Volume');
        $CargoShipmentNo = Yii::$app->request->post('CargoShipmentNo');
        $InvoiceNumber = Yii::$app->request->post('InvoiceNumber');

        $row = [];
        $row['OutBoundFeedBackThreePLResponse'][] = [
            'OutBoundId'=>$OutBoundId,//'16', // это ид из SendOutBoundFeedBackData
            'InBoundId'=>$InBoundId,//'0', // Что это такое ?
//            'LCBarcode'=>$PackBarcode,//'700000001', // наш короб
            'LcBarcode'=>$PackBarcode,// OLD PackBarcode '700000001', // наш короб
            'LotOrSingleBarcode'=>$SkuBarcode,// OLD SkuBarcode '8680654893689', // Что это такое ?
            'LotOrSingleQuantity'=>$SkuQuantity,//OLD SkuQuantity '12', // кол-во лотов в коробе
            'WaybillSerial'=>$WaybillSerial,//'KZK', //это не меняется
            'WaybillNumber'=>$WaybillNumber,//'16', //ReservationId - брать из GetWarehousePickings (его тут нет) и он пуст в GetPickingOutBoundData
            'Volume'=>$Volume,//'32', //размер короба это mapM3ToBoxSize 12, 17, 31 и т.д.
            'CargoShipmentNo'=>$CargoShipmentNo,//'-', //не используем
            'InvoiceNumber'=>$InvoiceNumber,//это номер приходной накладно по которой мы приняли этот товар
        ];
//
        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
            'FeedBackData'=> $row // feedBackData
        ];

        $api =  new DeFactoSoapAPIV2();
        $result = $api->sendRequest('SendOutBoundFeedBackData',$params);

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'SendOutBoundFeedBackData',
        ]);
    }

    /*
    * */
    public function actionMarkBatchForCompleted()
    { //mark-picking-for-completed
        $BatchId = Yii::$app->request->post('BatchId');
        $api =  new DeFactoSoapAPIV2();

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'BatchId'=>$BatchId,
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];

        $result = $api->sendRequest('MarkBatchforCompleted',$params);

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'MarkBatchforCompleted',
        ]);
    }


    /*
    * */
    public function actionGetBusinessUnitWmsData ()
    { // get-business-unit-wms-data

        $api =  new DeFactoSoapAPIV2();

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'ProcessRequestedDataType'=>'Full',
//            'CountryId'=>'0',
//            'BusinessUnitFilterId'=>'0',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];

        $result = $api->sendRequest('GetBusinessUnitWMSData',$params);

        if($resultDataArray = @ArrayHelper::getValue($result['response'],'GetBusinessUnitWMSDataResult.Data.BusinessUnitDTO')) {
            $resultDataArray = count($resultDataArray) <=1 ? [$resultDataArray] : $resultDataArray;
        } else {
            $resultDataArray = [];
        }

//        $storeList = Store::find()->andWhere(['type_use'=>1,'client_id'=>2])->andWhere("shop_code3 != ''")->limit(5)->all();
//        foreach($storeList as $store) {
//            foreach($resultDataArray as $BusinessUnitDTO) {
//                if($BusinessUnitDTO->BusinessUnitId == $store->shop_code3) {
//                    $store->shopping_center_name = $BusinessUnitDTO->Name;
//                    $store->shopping_center_name_lat = $BusinessUnitDTO->Name;
//
//                    echo $BusinessUnitDTO->BusinessUnitId."<br />";
//                    echo $BusinessUnitDTO->Name."<br />";
//                }
//            }
//        }
//
//
//        yii\helpers\VarDumper::dump($resultDataArray,10,true);
//        die;
        $providerArray = new yii\data\ArrayDataProvider([
            'allModels' =>$resultDataArray,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        return $this->renderAjax('_get-business-unit-wms-data',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'providerArray'=>$providerArray,
            'title'=>'GetBusinessUnitWMSData',
        ]);

    }

    /*
     * */
    public function actionGetMasterData()
    { // get-warehouse-appointments

        $ProcessRequestedDataType = Yii::$app->request->post('ProcessRequestedDataType');
        $ShortCode = Yii::$app->request->post('ShortCode');
        $SkuId = Yii::$app->request->post('SkuId');
        $Ean = Yii::$app->request->post('Ean');
        $params  = [];

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
            'ProcessRequestedDataType'=>'Full',
        ];
//
        if($ProcessRequestedDataType) {
            $params['request']['ProcessRequestedDataType'] = $ProcessRequestedDataType;
        }
        if($ShortCode) {
            $params['request']['ShortCode'] = $ShortCode;
        }
        if($SkuId) {
            $params['request']['SkuId'] = $SkuId;
        }
        if($Ean) {
            $params['request']['LotOrSingleBarcode'] = $Ean;
        }
//
        $api =  new DeFactoSoapAPIV2();
        $result = $api->sendRequest('GetMasterData',$params);

        if($resultDataArray = @ArrayHelper::getValue($result['response'],'GetMasterDataResult.Data.MasterDataThreePL')) {
            $resultDataArray = count($resultDataArray) <=1 ? [$resultDataArray] : $resultDataArray;
        } else {
            $resultDataArray = [];
        }

        if(!empty($result['Data'])) {
            $resultDataArray = $result['Data'];
            $resultDataArray = count($resultDataArray) <=1 ? [$resultDataArray] : $resultDataArray;
        } else {
            $resultDataArray = [];
        }

        $providerArray = new yii\data\ArrayDataProvider([
            'allModels' =>$resultDataArray,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        return $this->renderAjax('_get-master-data',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'providerArray'=>$providerArray,
            'title'=>'GetMasterData',
        ]);

    }

    /*
     * */
    public function actionGetMasterDataLoad()
    { // get-master-data-load
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $PageIndex = Yii::$app->request->post('PageIndex',0);
        $PageCount = Yii::$app->request->post('PageCount');
        $PageCount = !empty($PageCount) ? $PageCount : 1642;
        $next = 0;
        $params  = [];

        if($PageIndex <= $PageCount) {
            $api = new DeFactoSoapAPIV2();
            $params['request'] = [
                'BusinessUnitId' => '1029',
                'PageSize' => '0',
                'PageIndex' => $PageIndex,
                'CountAllItems' => false,
                'ProcessRequestedDataType' => 'Full',
            ];
//
            $result = $api->sendRequest('GetMasterData', $params);
            $PageCount = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.PageCount');
            if ($resultDataArray = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.Data.MasterDataThreePL')) {
                $resultDataArray = count($resultDataArray) <= 1 ? [$resultDataArray] : $resultDataArray;
            } else {
                $resultDataArray = [];
            }

            foreach ($resultDataArray as $data) {
                  //Products::create($data->SkuId,$data->LotOrSingleBarcode,$data->ShortCode,$data->Color);
                file_put_contents("GetMasterData-Size.csv",$data->SkuId.';'.(isset($data->Size) ? $data->Size : '').";"."\n",FILE_APPEND);
            }
            $next = 1;
        }

       return [
           'pageIndex'=>++$PageIndex,
           'next'=>$next,
           'pageCount'=>$PageCount,
       ];

    }

    /*
     * */
    public function actionGetSkuContentWmsData()
    { // get-sku-content-wms-data

        $api =  new DeFactoSoapAPIV2();

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
            'ProcessRequestedDataType'=>'Full',
        ];

        $result = $api->sendRequest('GetSKUContentWMSData',$params);

        if($resultDataArray = @ArrayHelper::getValue($result['response'],'GetSKUContentWMSDataResult.Data.SKUContentWMS')) {
            $resultDataArray = count($resultDataArray) <=1 ? [$resultDataArray] : $resultDataArray;
        } else {
            $resultDataArray = [];
        }

        $providerArray = new yii\data\ArrayDataProvider([
            'allModels' =>$resultDataArray,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        return $this->renderAjax('_get-sku-content-wms-data',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'providerArray'=>$providerArray,
            'title'=>'GetBusinessUnitWMSData',
        ]);

    }

    /*
    *
    * */
    public function actionGetInboundDataForReturn()
    { // get-inbound-data-for-return

        $api =  new DeFactoSoapAPIV2();
        $result['response'] = $params = [];
        $params['request'] = [
            'BusinessUnitId' => '1029',
            'PageSize' => '0',
            'PageIndex' => '0',
            'CountAllItems' => false,
        ];

        $result = $api->sendRequest('GetInBoundDataForReturn', $params);

        if($resultDataArray = @ArrayHelper::getValue($result['response'],'GetInBoundDataForReturnResult.Data.InBoundThreePLDTO')) {
            $resultDataArray = count($resultDataArray) <=1 ? [$resultDataArray] : $resultDataArray;
        } else {
            $resultDataArray = [];
        }

        $providerArray = new yii\data\ArrayDataProvider([
            'allModels' =>$resultDataArray,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        return $this->renderAjax('_get-inbound-data-for-return',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'providerArray'=>$providerArray,
            'title'=>'GetInBoundDataForReturn',
        ]);
    }

    /*
    *
    * */
    public function actionSendInboundFeedbackDataForReturn()
    { // send-inbound-feedback-data-for-return

        $result['response'] = $params = [];

        $InboundId = Yii::$app->request->post('InboundId');
        $AppointmentBarcode = Yii::$app->request->post('AppointmentBarcode');
        $PackBarcode = Yii::$app->request->post('PackBarcode');
        $SkuBarcode = Yii::$app->request->post('SkuBarcode');
        $SkuQuantity = Yii::$app->request->post('SkuQuantity');

        $row = [];
        $row['InBoundFeedBackThreePLResponse'][] = [
            'InboundId'=>$InboundId,
            'AppointmentBarcode'=>$AppointmentBarcode,
            'LcOrCartonLabel'=>$PackBarcode,
            'LotOrSingleBarcode'=>$SkuBarcode,
            'LotOrSingleQuantity'=>$SkuQuantity,
        ];
//
        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
            'FeedBackData'=> $row // feedBackData
        ];

        $api =  new DeFactoSoapAPIV2();
        $result = $api->sendRequest('SendInBoundFeedBackDataForReturn', $params);

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'SendOutBoundFeedBackData',
        ]);
    }


    /*
     *
     * */
    public function actionCreateLcBarcode()
    { // /other/api2/index/create-lc-barcode

        $result['response'] = $params = [];

        $Count = Yii::$app->request->post('Count');

        $params['request'] = [
            'BusinessUnitId'=>'1029',
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
            'Count'=> $Count
        ];

//        $api = new DeFactoSoapAPIV2();
//        $result = $api->sendRequest('CreateLcBarcode', $params);
//        $result = $api->createLcBarcode($Count);
        $apiManager =  new DeFactoSoapAPIV2Manager();
        $result = $apiManager->CreateLcBarcode($Count);

        return $this->renderAjax('_empty',[
            'requestParams'=>$params,
            'responseResult'=>$result,
            'title'=>'CreateLcBarcode',
        ]);
    }
    /*
     *
     * */
    public function actionTestInboundRpt()  // ok
    { // /other/api2/test-inbound-rpt
        die("-START DIE /other/api2/test-inbound-rpt");

        $inbound_order_id = 19539;
        $io = InboundOrder::findOne($inbound_order_id);
        if ($items = InboundOrderItem::findAll(['inbound_order_id' => $inbound_order_id])) {
            $row = [];
//            $api = new DeFactoSoapAPIV2Manager();
            foreach ($items as $item) {
                $rows['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackData($item,$io->order_number);
                if (!empty($rows['InBoundFeedBackThreePLResponse'])) {
                    yii\helpers\VarDumper::dump($rows,10,true);
//                    $api->SendInBoundFeedBackData($rows);
                }
            }
        }
        return $this->renderAjax('_empty',[
            'requestParams'=>[],
            'responseResult'=>[],
            'title'=>'--------------',
        ]);
    }

    /*
     *
     * */
    public function actionTestInboundRpt_XXXXXXXXXXXXXXXXXXx()  // ok
    { // /other/api2/test-inbound-rpt
        die("-START DIE /other/api2/test-inbound-rpt2");
        $acceptedLots = [
          '147852'=>'48',
          '147853'=>'120',
          '147854'=>'64',
          '147855'=>'83',
          '147857'=>'60',
          '147858'=>'11',
          '147859'=>'84',
          '147860'=>'93',
          '147861'=>'16',
          '147863'=>'80',
          '147864'=>'36',
          '147865'=>'48',
          '147866'=>'42',
          '147868'=>'71',
          '147869'=>'24',
          '147870'=>'49',
          '147871'=>'73',
          '147872'=>'96',
          '147874'=>'36',
          '147876'=>'14',
          '147877'=>'90',
        ];
        $totalQ = 0;
        foreach($acceptedLots as $q) {
            $totalQ+= $q;

        }
        echo $totalQ;
        echo "<br />";
//        die;
        $inbound_order_id = 19521;

        if ($items = InboundOrderItem::findAll(['inbound_order_id' => $inbound_order_id])) {
            $row = [];
//            $api = new DeFactoSoapAPIV2Manager();
            foreach ($items as $item) {
                $rows['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackData($item);
                if (!empty($rows['InBoundFeedBackThreePLResponse'])) {
                    $inboundDefactoID = $rows['InBoundFeedBackThreePLResponse']['0']['InboundId'];
                    $inboundDefactoQty = $rows['InBoundFeedBackThreePLResponse']['0']['LotOrSingleQuantity'];
                    if(isset($acceptedLots[$inboundDefactoID]) && $acceptedLots[$inboundDefactoID] < $inboundDefactoQty) {
                        $rows['InBoundFeedBackThreePLResponse']['0']['LotOrSingleQuantity'] = $inboundDefactoQty - $acceptedLots[$inboundDefactoID];

                        $totalQ+= $rows['InBoundFeedBackThreePLResponse']['0']['LotOrSingleQuantity'];

//                        yii\helpers\VarDumper::dump($rows,10,true);
//                        $api->SendInBoundFeedBackData($rows);
                    } else if(!isset($acceptedLots[$inboundDefactoID])) {
                        $totalQ += $rows['InBoundFeedBackThreePLResponse']['0']['LotOrSingleQuantity'];
//                        yii\helpers\VarDumper::dump($rows,10,true);
//                        $api->SendInBoundFeedBackData($rows);
                    }
                }
            }
            echo $totalQ;
            echo "<br />";
        }
        return $this->renderAjax('_empty',[
            'requestParams'=>[],
            'responseResult'=>[],
            'title'=>'--------------',
        ]);
    }

    /*
     *
     * */
    public function actionTestInboundCrossDock()  // ok
    { // /other/api2/test-inbound-cross-dock
        die("-START DIE /other/api2/test-inbound-cross-dock");
        $inbound_order_id = 19515;
        $inboundOrder = InboundOrder::findOne($inbound_order_id);
        $internalBarcode = $inboundOrder->client_id.'-'.$inboundOrder->parent_order_number;

        if ($crossDocks = CrossDock::findAll(['internal_barcode' => $internalBarcode,'party_number'=>$inboundOrder->order_number])) {
            foreach($crossDocks as $crossDock) {
                if ($crossDockItems = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id])) {
                    foreach ($crossDockItems as $crossDockItem) {
                        $row['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataCrossDock($crossDockItem);
                        yii\helpers\VarDumper::dump($row, 10, true);
                        if (!empty($row['InBoundFeedBackThreePLResponse'])) {
                            $api = new DeFactoSoapAPIV2Manager();
                            //$api->SendInBoundFeedBackData($row);
                        } else {
                            file_put_contents("InBoundFeedBackThreePLResponse-CRoss-dock-ERROR.txt",print_r($row,true)."\n"."\n",FILE_APPEND);
                        }
                    }
                }
            }
        }
        return $this->renderAjax('_empty',[
            'requestParams'=>[],
            'responseResult'=>[],
            'title'=>'CreateLcBarcode',
        ]);
    }

    /*
     *
     * */
    public function actionTestOutboundCrossDock()  // ok
    { // /other/api2/test-outbound-cross-dock
        die();
        $crossDockId = 3266;
        if ($crossDocks = CrossDock::findAll(['id'=>$crossDockId])) {
            foreach($crossDocks as $crossDock) {
                if ($crossDockItems = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id])) {
                    foreach ($crossDockItems as $crossDockItem) {
                        $row['OutBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendCrossDockOutBoundFeedBackDataOutbound($crossDockItem,$crossDock);
                        yii\helpers\VarDumper::dump($row, 10, true);
                        if (!empty($row['OutBoundFeedBackThreePLResponse'])) {
                            //$api = new DeFactoSoapAPIV2Manager();
                            //$api->SendOutBoundCrossDockFeedBackData($row);
                        } else {
                            file_put_contents("SendOutBoundCrossDockFeedBackData-CRoss-dock-ERROR.txt",print_r($row,true)."\n"."\n",FILE_APPEND);
                        }
                    }
                }
            }
        }
        return $this->renderAjax('_empty',[
            'requestParams'=>[],
            'responseResult'=>[],
            'title'=>'SendOutBoundCrossDockFeedBackData',
        ]);
    }
    /*
     *
     * */
    public function actionTestOutbound()
    { // /other/api2/test-outbound
        die("-DIE START actionTestOutbound-");
////        $outboundId = 4837;
//        $outboundOrderModel = OutboundOrder::findOne($outboundId);
//        $rows = DeFactoSoapAPI::preparedDataForOutboundConfirm($outboundOrderModel->id);
//        \yii\helpers\VarDumper::dump($rows,10,true);
//        die('START DIE actionTestOutbound');

        $outboundOrder_id = 4900;
        $outboundOrderModel = OutboundOrder::findOne($outboundOrder_id);
        $outboundOrderItems = OutboundOrderItem::find()
                                ->andWhere(['outbound_order_id' => $outboundOrderModel->id])
                                ->andWhere('accepted_qty > 0')
                                ->all();

        if( DeFactoSoapAPIV2Manager::checkCountCompleteOutboundOrders($outboundOrderModel->parent_order_number,$outboundOrderModel->consignment_outbound_order_id)) {
           echo "YPA";
        }
        die("ddddddddddddddddddddd");

        if ($outboundOrderItems) {
            //Start Вынести в отдельную функцию
            $countBoxBarcodes = Stock::find()
                ->andWhere(['outbound_order_id' => $outboundOrderModel->id]) // ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
                ->andWhere(['status'=>[
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ]
                ])
                ->groupBy('box_barcode')
                ->count();
            $apiManager = new DeFactoSoapAPIV2Manager();
            $result = $apiManager->CreateLcBarcode($countBoxBarcodes+2);
            $createLcBarcodes = $result['Data'];
            //Start Вынести в отдельную функцию
            $mappingOurBobBarcodeToDefacto = [];
            foreach ($outboundOrderItems as $outboundOrderItem) {
                $stocks = Stock::find()->select('product_barcode, inbound_order_id, count(id) as accepted_qty, box_barcode, box_size_m3, box_size_barcode, outbound_order_item_id')
                    ->andWhere(['outbound_order_item_id' => $outboundOrderItem->id]) // ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
                    ->andWhere(['status'=>[
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ]
                    ])
                    ->groupBy('product_barcode, box_barcode, outbound_order_item_id')
                    ->orderBy('box_barcode')
                    ->asArray()
                    ->all();

                if($tmp = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks,$outboundOrderItem)) {
                    foreach($tmp as $tmpAPIValue) {
                        if(!isset($mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']])) {
                            $mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']] = array_shift($createLcBarcodes);
                            file_put_contents("mappingOurBobBarcodeToDefacto.csv",$outboundOrderModel->id.";".$tmpAPIValue['LcBarcode'].";".$mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']].";"."\n",FILE_APPEND);
                        }
                    }
                }

                foreach ($stocks as $keyStock=>$stock) {
                    $stocks[$keyStock]['box_barcode'] = $mappingOurBobBarcodeToDefacto[$stock['box_barcode']];
                    $stocks[$keyStock]['our_box_barcode'] = $stock['box_barcode'];
                }

                $outboundPreparedDataRows = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks,$outboundOrderItem);
                if($outboundPreparedDataRows) {
                    foreach($outboundPreparedDataRows as $outboundPreparedDataRow){
                        $rowsDataForAPI['OutBoundFeedBackThreePLResponse'][] = $outboundPreparedDataRow;
                    }
                }
            }

            if (!empty($rowsDataForAPI)) {
                yii\helpers\VarDumper::dump($rowsDataForAPI,10,true);
//                $api = new DeFactoSoapAPIV2Manager();
//                $api->SendOutBoundFeedBackData($rowsDataForAPI);
            }
        }

        die('-END-');
    }




    public function actionTestXXXXXXX()
    {
        die();
        // Это рабочий тестовый вариант проверки АПи для отправки приходов
        $inbound_order_id = 3268;
        $crossDock = CrossDock::findOne($inbound_order_id);
        if($items = CrossDockItems::findAll(['cross_dock_id'=>$crossDock->id,'status'=>Stock::STATUS_CROSS_DOCK_SCANNED]) ) {
            $row = [];
            $api = new DeFactoSoapAPIV2Manager();
            foreach($items as $item) {
                $row['OutBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendCrossDockOutBoundFeedBackDataOutbound($item);
//                $row['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendCrossDockOutBoundFeedBackDataInbound($item);
                if(!empty($row['OutBoundFeedBackThreePLResponse'])) {
                    \yii\helpers\VarDumper::dump($row,10,true);
                    $api->SendOutBoundCrossDockFeedBackDataOutbound($row);
                }
            }
        }
        die('-END-');
    }
    /*
     *
     * */
    public function actionTestOutboundXXXXX()
    { // /other/api2/test-outbound
        die();
//        $outboundId = 4837;
//        $outboundOrderModel = OutboundOrder::findOne($outboundId);
//        $rows = DeFactoSoapAPI::preparedDataForOutboundConfirm($outboundOrderModel->id);
//        \yii\helpers\VarDumper::dump($rows,10,true);
//        die('ddd');

        $inbound_order_id = 4837;
        $outboundOrder = OutboundOrder::findOne($inbound_order_id);
        if ($items = OutboundOrderItem::findAll(['outbound_order_id' => $outboundOrder->id])) {

            $stockAll = Stock::find()->select('product_barcode, inbound_order_id, count(id) as accepted_qty, box_barcode, box_size_m3, box_size_barcode, outbound_order_item_id')
                ->andWhere(['outbound_order_id' => $outboundOrder->id]) // ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
                ->andWhere(['status' =>19]) // ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
                ->groupBy('product_barcode, box_barcode')
                ->orderBy('box_barcode')
                ->asArray()
                ->all();
            \yii\helpers\VarDumper::dump($stockAll, 10, true);
            die;



            $row = [];
            //$api = new DeFactoSoapAPIV2Manager();
            foreach ($items as $item) {
                $row['OutBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendCrossDockOutBoundFeedBackDataOutbound($item);
//                $row['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendCrossDockOutBoundFeedBackDataInbound($item);
                if (!empty($row['OutBoundFeedBackThreePLResponse'])) {
//                if(!empty($row['InBoundFeedBackThreePLResponse'])) {
                    \yii\helpers\VarDumper::dump($row, 10, true);
//                    $api->SendOutBoundCrossDockFeedBackDataOutbound($row);
//                    $api->SendOutBoundCrossDockFeedBackData($row);
                }
                //break;
            }
        }

        die('-END-');
    }

    public function actionTestReturnGet()
    { // /other/api2/test-return-get

        $api = new DeFactoSoapAPIV2();
        $dataFromAPI = $api->GetInBoundDataForReturn();
        $managerAPI = new DeFactoSoapAPIV2Manager();
        $outResult = $managerAPI->_outResult;

        if(!$dataFromAPI['HasError']) {
            $preparedData = $managerAPI->preparedReturnOrderPartyForSaveToDb($dataFromAPI['Data']);
//            yii\helpers\VarDumper::dump($preparedData,10,true);
            if(!$preparedData['HasError']) {
                $saveToDbData = $managerAPI->saveReturnOrderPartyToDb($preparedData['Data'],$preparedData['AppointmentBarcode']);
                if(!$saveToDbData['HasError']) {
                    $outResult['HasError'] = false;
                    $outResult['Message'] = $saveToDbData['Message'];
//                    return $outResult;
                } else {
                    $outResult['ErrorMessage'] = $saveToDbData['ErrorMessage'];
                }
            } else {
                $outResult['ErrorMessage'] = $preparedData['ErrorMessage'];
            }
        } else {
            $outResult['ErrorMessage'] = $dataFromAPI['ErrorMessage'];
        }

        return $this->renderAjax('_empty',[
            'requestParams'=>[],
            'responseResult'=>[],
            'title'=>'SendOutBoundCrossDockFeedBackData',
        ]);
    }

}