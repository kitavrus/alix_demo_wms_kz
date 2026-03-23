<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 28.07.2019
 * Time: 14:05
 */
namespace common\ecommerce\defacto\api;

use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;

class ECommerceAPINew
{
    /*
     * @var string url
     * */
//    private $_url = 'http://195.46.145.251/DFStore.ProxyServices.UAT/ExternalWMS/WmsEcommerceProxy.asmx?WSDL'; // фигня
    //private $_url = 'http://195.46.145.251/DFStore.ProxyServices.T1/ExternalWMS/WmsEcommerceProxy.asmx?WSDL'; // TEST
    //private $_url = 'http://31.145.4.217/ExternalWMS/WmsEcommerceProxy.asmx?WSDL'; // LIVE BUSINESS_UNIT_ID 95540
//    private $_url = 'https://externalwms.defacto.com/ExternalWMS/WmsEcommerceProxy.asmx?WSDL'; // LIVE BUSINESS_UNIT_ID 95540
    // private $_url = 'https://dfstoretest.defacto.com.tr/DFStore.ProxyServices.T1/ExternalWMS/WmsEcommerceProxy.asmx?WSDL'; // TEST BUSINESS_UNIT_ID 95540
    private $_url = 'https://externalwms.defacto.com/ExternalWMS/WmsEcommerceProxy.asmx?WSDL'; // TEST BUSINESS_UNIT_ID 95540

    /*
     * @var soap client
     * */
    private $_client;

    /*
     * @var our warehouse id in defacto system
     * */
//    const BUSINESS_UNIT_ID = 1029;
//    const BUSINESS_UNIT_ID = 91121; // фигня
//    const BUSINESS_UNIT_ID = 95540; // LIVE
    const BUSINESS_UNIT_ID = 95540; // TEST
    /*
     * @var array Default format function result
     * */
    private $_outResult = [
        'HasError'=>true,
        'ErrorMessage'=>'',
        'Message'=>'',
        'Data'=>[],
    ];

    /*
     * @param string $connectType Value "io" or "r". Default 'io'
     * */
    public function __construct()
    {
//        $this->connect();
//        $this->setSoapHeader();
    }

	/*
*
* */
	public function setSoapHeaderTest()
	{
		$auth  = [
//            'UserName'=>'superUser', // TEST
//            'Password'=>'123456', // TEST
			'UserName'=>'Nomadex', // LIVE
			'Password'=>'nMdX543ab**z', // LIVE
		];
		$header = new \SoapHeader('http://ozon.com.tr/', "AuthenticationHeader", $auth);
		$this->_client->__setSoapHeaders($header);
	}

    /**
    *
    * */
    public function setSoapHeader()
    {
        $auth  = [
		     'UserName'=>'Nomadex', // LIVE
            'Password'=>'nMdX543ab**z', // LIVE
            //'UserName'=>'wmsservicetestuser', // LIVE
            //'Password'=>'wms123**', // LIVE

            //'UserName'=>'wmsservicetestuser', // TEST
            //'Password'=>'zxc123**', // TEST
//-----------------------------------------------------------------
//            'UserName'=>'NomadexUser', // фигня
//            'Password'=>'NomadexPassword', // фигня

//'UserName'=>'superUser', // TEST
//'Password'=>'123456', // TEST
//'UserName'=>'Nomadex', // LIVE
//'Password'=>'nMdX543ab**z', // LIVE
//'UserName'=>'UserName', // TEST 2
//'Password'=>'Password', // LIVE
        ];
        $header = new \SoapHeader('http://ozon.com.tr/', "AuthenticationHeader", $auth);
        $this->_client->__setSoapHeaders($header);
    }

    /*
    * Connect to server
    * */
    public function connect()
    {
        ini_set('default_socket_timeout', 99999);
        return $this->_client = new \SoapClient($this->_url,
            [
                'trace' => true,
                "exceptions" => true,
                "soap_version" => SOAP_1_2,
                "connection_timeout" => 99999,
                "cache_wsdl" =>  WSDL_CACHE_NONE,
				'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]),
            ]
        );
    }

    public function BUSINESS_UNIT_ID () {
        return self::BUSINESS_UNIT_ID;
    }



    /*
     * Call function GetKoli , IadeKabul , etc
     * @param string $method Call function name
     * @param array $params Data params for call function
     * @return array [errors, response]
     * */
    public function sendRequest($method,$params,$soapHeader = null)
    {
//        return;
        $this->connect();
//        $this->setSoapHeader();
		$this->setSoapHeaderTest();

        if(empty($this->_client)) {
            return false;
        }

        $response = [];
        $errors = [];
        $return = [];
//        VarDumper::dump($params,10,true);
        try {
            $response = $this->_client->$method($params);
//            $response = $this->_client->__soapCall($method,$params);
        } catch (\SoapFault $exception) {
            $errors ['Exception'] = $exception;
            $errors ['ExceptionMessage'] = $exception->getMessage();
        }
//		$errors ['LastRequest'] = htmlentities($this->_client->__getLastRequest());
		$errors ['__getLastRequest'] = $this->_client->__getLastRequest();
//		$errors ['__getFunctions'] = $this->_client->__getFunctions();
		$errors ['__getLastRequestHeaders'] = $this->_client->__getLastRequestHeaders();
        $return['errors'] = $errors;
        $return['response'] = $response;


        $dirPath = 'api/de-facto/e-commerce/'.date('Ymd');
        BaseFileHelper::createDirectory($dirPath);

        file_put_contents($dirPath."/method-".$method.".log","\n"."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath."/method-".$method.".log",date('Ymd-H:i:s')."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath."/method-".$method.".log",'PARAMS:'."\n".print_r($params,true)."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath."/method-".$method.".log",'RETURN:'."\n".print_r($return,true)."\n",FILE_APPEND);
        if(!empty($return['errors'])) {
            //file_put_contents($dirPath . "/method-" . $method . "-errors.log", print_r($params, true) . "\n"."\n", FILE_APPEND);
            //file_put_contents($dirPath . "/method-" . $method . "-errors.log", print_r($return, true) . "\n", FILE_APPEND);
        }

        return $return;
    }

    /*
    * Получаем список лотов в коробе
    * */
    public function GetInBoundData($params) // OK
    {
        return $this->sendRequest('GetInBoundData', $params);
    }

    /*
    * Получаем содержимое лота
    * */
    public function GetLotContent($params) // OK
    {
        return $this->sendRequest('GetLotContent', $params);
    }

    /*
    * Получаем приходы
    * */
    public function SendInBoundFeedBackData($params) //
    {
//        $outResult = $this->_outResult;
//        $params = [];
//        $params['request'] = [
//            'FeedBackData'=>[
//                'B2CInBoundFeedBack'=>[
//                    [
//                        'InboundId'=>'6875494',
//                        'LcOrCartonBarcode'=>'2430007688586',
//                        'ProductBarcode'=>'8681991024729',
//                        'ProductQuantity'=>'1',
//                    ]
//                ]
//            ]
//        ];
//        $apiResponse = $this->sendRequest('SendInBoundFeedBackData', $params);
//        VarDumper::dump($apiResponse,10,true);
//        die;
//        return $outResult;

        return $this->sendRequest('SendInBoundFeedBackData', $params);
    }

    /*
    * Отправляем возвраты
    * Этот метод используется для уведомления принимающего возврат средства.
    * */
    public function ReturnShipment($params) //
    {
        return $this->sendRequest('ReturnShipment', $params);
    }

    /*
    * Получаем причины возвратов
    * */
    public function GetReturnReasonList($params) //
    {
        return $this->sendRequest('GetReturnReasonList', $params);
    }

    /*
    * Непонятно что это
    * */
    public function GetReturnReasonProcessList($params) //
    {
        return $this->sendRequest('GetReturnReasonProcessList', $params);
    }

    /*
    * Получаем новые возвраты
    * */
    public function GetShipmentForReturn($params) //
    {
        return $this->sendRequest('GetShipmentForReturn', $params);
    }

    /*
    * получаем короба которые нужно переместит на складе B2C to B2B
    * */
    public function GetBatches($params) //
    {
        return $this->sendRequest('GetBatchs', $params);
    }

    /*
    * Получаем содержимое коробки(которые получили через метод GetBatchs)
    * */
    public function GetOutBound($params) //
    {
        return $this->sendRequest('GetOutBound', $params);
    }

    /*
    * Закрываем короба которые мы приняли
    * */
    public function MarkBatchForCompleted($params) //
    {
        return $this->sendRequest('MarkBatchforCompleted', $params);
    }

    /*
    * Отправляем принятые короба
    * */
    public function SendOutBoundFeedBack($params) //
    {
        return $this->sendRequest('SendOutBoundFeedBack', $params);
    }
	

    /*
    * Получаем приходы
    * */
    public function GetSkuInfo(
        $ShortCode = '',
        $LotOrSingleSkuIds = '',
        $LotOrSingleBarcodes = ''
    )
    {
        $outResult = $this->_outResult;
        $params = [];
        $params['request'] = [
            'BusinessUnitId' => 95540, //self::BUSINESS_UNIT_ID,
            'CountAllItems' => false,
            'PageIndex' => 0,
            'PageSize' => 0,
        ];

        if($ShortCode) {
            $params['request']['ShortCode'] = $ShortCode;
        }

        if($LotOrSingleBarcodes) {
            $params['request']['LotOrSingleBarcodes'] = $LotOrSingleBarcodes;
        }

        if($LotOrSingleSkuIds) {
            $params['request']['LotOrSingleSkuIds'] = $LotOrSingleSkuIds;
        }


        $apiResponse = $this->sendRequest('GetSkuInfo', $params);
        VarDumper::dump($apiResponse,10,true);
        die;
        return $outResult;
    }

    /*
    * Получаем приходы
    * */
    public function GetMasterData($requestParams) //
    {
//        $outResult = $this->_outResult;
//        $params = [];
//
//        $params['request'] = [
//            'BusinessUnitId' => 95540, //self::BUSINESS_UNIT_ID,
//            'ProcessRequestedB2CDataType' => 'Full', //Changed
//            'CountAllItems' => false,
//            'PageIndex' => 0,
//            'PageSize' => 0,
//        ];
//
//        if($ShortCode) {
//            $params['request']['ShortCode'] = $ShortCode;
//        }
//        if($SkuId) {
//            $params['request']['SkuId'] = $SkuId;
//        }
//        if($LotOrSingleBarcode) {
//            $params['request']['LotOrSingleBarcode'] = $LotOrSingleBarcode;
//        }

        $apiResponse = $this->sendRequest('GetMasterData', $requestParams);
//        VarDumper::dump($apiResponse,10,true);
//        die;
        return $apiResponse;
    }

    /*
    * Получаем сборки
    * */
    public function GetShipments($params) //
    {
//        $outResult = $this->_outResult;
//        $params = [];
//        $params['request'] = [
//            'FeedBackData'=>[
//                'B2CInBoundFeedBack'=>[
//                    [
//                        'InboundId'=>'6875494',
//                        'LcOrCartonBarcode'=>'2430007688586',
//                        'ProductBarcode'=>'8681991024729',
//                        'ProductQuantity'=>'1',
//                    ]
//                ]
//            ]
//        ];
//        $apiResponse = $this->sendRequest('SendInBoundFeedBackData', $params);
//        VarDumper::dump($apiResponse,10,true);
//        die;
//        return $outResult;

        return $this->sendRequest('GetShipments', $params);
    }

    /*
    * Отменяем заказ
    * */
    public function CancelShipment($params) //
    {
        return $this->sendRequest('CancelShipment', $params);
    }
    /*
    * Получаем этикетку курьера
    * */
    public function GetCargoLabel($params) //
    {
        return $this->sendRequest('GetCargoLabel', $params);
    }

      /*
    * ????????? ЧТО-ЭТО
    * */
    public function SendShipmentFeedback($params) //
    {
        return $this->sendRequest('SendShipmentFeedback', $params);
    }

    /*
    * Получаем этиетку крьерки
    * */
    public function CancellationRequestExistsByCustomer($params) //
    {
        return $this->sendRequest('CancellationRequestExistsByCustomer', $params);
    }

    /*
    * Если отправления принимаются службой GetShipments,
    * то для подтверждения того, что вы принимаете и обрабатываете отправления,
    * вам следует вызвать этот метод
    * */
    public function SendAcceptedShipments($params) //
    {
        return $this->sendRequest('SendAcceptedShipments', $params);
    }

    /**
    * Отправляем файл waybill PDF document
    * */
    public function UploadShipmentFile($params) //
    {
        return $this->sendRequest('UploadShipmentFile', $params);
    }

    /**
    * Уравниваем остатки на складе
    * */
    public function StockAdjustment($params) //
    {
        return $this->sendRequest('StockAdjustment', $params);
    }

    /*
    * Отправляем остатки
    * */
    public function SendInventorySnapshot($params) //
    {
        return $this->sendRequest('SendInventorySnapshot', $params);
    }
	
    //---------------------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------------------

    /*
    * Получаем список приходных накладный и кросдок накладных (партий)
    *
    * */
    public function GetWarehouseAppointments()
    {
        $params['request'] = [
            'BusinessUnitId'=>self::BUSINESS_UNIT_ID,
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];

        $apiResponse = $this->sendRequest('GetWarehouseAppointments',$params);

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($apiResponse['response'],'GetWarehouseAppointmentsResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if($data = ArrayHelper::getValue($result,'Data.WarehouseAppointmentThreePL')) {
                    $resultDataArray = count($data) <=1 ? [$data] : $data;
                    $outResult['HasError'] = false;
                    $outResult['Data'] = $resultDataArray;

                    return $outResult;
                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    /*
    * Получаем список приходных накладный и кросдок накладных (партий)
    * @param string $appointmentBarcode
    * */
    public function MarkAppointmentforInBound($AppointmentBarcode)
    {
        $outResult = $this->_outResult;

        if(!empty($AppointmentBarcode)) {
            $params['request'] = [
                'BusinessUnitId'=>self::BUSINESS_UNIT_ID,
                'AppointmentBarcode'=>$AppointmentBarcode,
                'PageSize'=>'0',
                'PageIndex'=>'0',
                'CountAllItems'=>false,
            ];
            $apiResponse = $this->sendRequest('MarkAppointmentforInBound', $params);

            if ($result = ArrayHelper::getValue($apiResponse['response'], 'MarkAppointmentforInBoundResult')) {
                if (ArrayHelper::getValue($result, 'HasError') === false) {
                    $outResult['HasError'] = false;
                } else {
                    $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто. ['.ArrayHelper::getValue($result, 'Error').']';
                }
            } else {
                $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
            }
        } else {
            $outResult['ErrorMessage'] = 'Необходимо передать AppointmentBarcode. т.е Номер приходной накладной';
        }
        return $outResult;
    }

    /*
     * Получаем приход и крос док
     * @param string $appointmentBarcode
     * */
    public function GetAppointmentInBoundData($AppointmentBarcode)
    {
        $outResult = $this->_outResult;
        if(!empty($AppointmentBarcode)) {
            $params['request'] = [
                'BusinessUnitId' =>self::BUSINESS_UNIT_ID,
                'AppointmentBarcode' => $AppointmentBarcode,
                'PageSize' => '0',
                'PageIndex' => '0',
                'CountAllItems' => false,
            ];
            $apiResponse = $this->sendRequest('GetAppointmentInBoundData', $params);
            if ($result = @ArrayHelper::getValue($apiResponse['response'], 'GetAppointmentInBoundDataResult')) {
                if (ArrayHelper::getValue($result, 'HasError') === false) {
                    if ($data = ArrayHelper::getValue($result, 'Data.InBoundThreePLDTO')) {
                        $resultDataArray = count($data) <=1 ? [$data] : $data;
                        $outResult['HasError'] = false;
                        $outResult['Data'] = $resultDataArray;
                        return $outResult;
                    }
                } else {
                    $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто. ['.ArrayHelper::getValue($result, 'Error').']';
                }
            } else {
                $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
            }
        } else {
            $outResult['ErrorMessage'] = 'Необходимо передать appointmentBarcode. т.е Номер приходной накладной';
        }
        return $outResult;


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

//            VarDumper::dump($data,10,true);
//            die();
//            $dataToSaveDb = [
//                '1'=>$data['Order'], //
//                '2'=>$data['Qc'], //
//                '3'=>$data['Preadmission'], //
//                '4'=>$data['Label'], //
//                '5'=>$data['SkuId'], //
//                '6'=>$data['Ean'], //
//                '7'=>$data['Date'], //
//                '8'=>$data['Short'], //
//                '9'=>$data['Quantity'], //
//                '10'=>$data['Carton'], //
//                '11'=>$data['Origin'], //
//                '12'=>$data['Nob'], //
//                '13'=>$data['FromBusinessUnitId'], //
//                '14'=>$data['ProcessTime'], //
//                '15'=>$data['SKU'], //
//            ];
//        }
    }

    /*
     *
     * */
    public function SendInBoundFeedBackData_OLD($data) // OK
    {
        $outResult = $this->_outResult;

        $params['request'] = [
            'PageSize' => '0',
            'PageIndex' => '0',
            'CountAllItems' => false,
            'FeedBackData' => $data
        ];

        $apiResponse = $this->sendRequest('SendInBoundFeedBackData', $params);

        if($result = ArrayHelper::getValue($apiResponse,'response.SendInBoundFeedBackDataResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }
        return $outResult;
    }

    /*
    * Вызываем когда накладная полностью принята
    * @param string $appointmentBarcode
    * */
    public function MarkAppointmentforCompleted($AppointmentBarcode)
    {
        $outResult = $this->_outResult;

        if(!empty($AppointmentBarcode)) {
            $params['request'] = [
                'BusinessUnitId'=>self::BUSINESS_UNIT_ID,
                'AppointmentBarcode'=>$AppointmentBarcode,
                'PageSize'=>'0',
                'PageIndex'=>'0',
                'CountAllItems'=>false,
            ];
            $apiResponse = $this->sendRequest('MarkAppointmentforCompleted', $params);

            if ($result = ArrayHelper::getValue($apiResponse['response'], 'MarkAppointmentforCompletedResult')) {
                if (ArrayHelper::getValue($result, 'HasError') === false) {
                    $outResult['HasError'] = false;
                } else {
                    $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто. ['.ArrayHelper::getValue($result, 'Error').']';
                }
            } else {
                $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
            }
        } else {
            $outResult['ErrorMessage'] = 'Необходимо передать AppointmentBarcode. т.е Номер приходной накладной';
        }
        return $outResult;
    }

    /*
     * Получаем список заказов на отгрузку
     * @return array
     * */
    public function GetWarehousePickings()
    {
//        $outResult = $this->_outResult;
//        $params['request'] = [
//            'BusinessUnitId'=>self::BUSINESS_UNIT_ID,
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//        ];
//        $apiResponse = $this->sendRequest('GetWarehousePickings',$params);
//
//        if($result = ArrayHelper::getValue($apiResponse,'response.GetWarehousePickingsResult')) {
//            if (ArrayHelper::getValue($result, 'HasError') === false) {
//                if($data = ArrayHelper::getValue($result,'Data.PickingLoj')) {
//                    $outResult['HasError'] = false;
//                    $outResult['Data'] = $data;
//                    return $outResult;
//                }
//            } else {
//                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
//            }
//        } else {
//            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
//        }
//        return $outResult;


        $params['request'] = [
            'BusinessUnitId'=>self::BUSINESS_UNIT_ID,
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
        ];

        $apiResponse = $this->sendRequest('GetBatchsWms',$params);

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($apiResponse['response'],'GetBatchsWmsResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if($data = ArrayHelper::getValue($result,'Data.BatchThreePL')) {
                    $resultDataArray = count($data) <=1 ? [$data] : $data;
                    $outResult['HasError'] = false;
                    $outResult['Data'] = $resultDataArray;

                    return $outResult;
                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    /*
     * Подтверждаем что заказ собран
     * @param integer $pickingID
     * */
    public function MarkPickingForOutbound($pickingID)
    {
        $outResult = $this->_outResult;
        if(!empty($pickingID)) {

            $params['request'] = [
                'PickingId'=>$pickingID,
                'BusinessUnitId'=>self::BUSINESS_UNIT_ID,
                'PageSize'=>'0',
                'PageIndex'=>'0',
                'CountAllItems'=>false,
            ];
            $apiResponse = $this->sendRequest('MarkPickingforOutBound', $params);

            if ($result = @ArrayHelper::getValue($apiResponse['response'], 'MarkPickingforOutBoundResult')) {
                if (ArrayHelper::getValue($result, 'HasError') === false) {
                    $outResult['HasError'] = false;
                } else {
                    $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто. ['.ArrayHelper::getValue($result, 'Error').']';
                }
            } else {
                $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
            }
        } else {
            $outResult['ErrorMessage'] = 'Необходимо передать PickingId. т.е Номер расходной накладной';
        }
        return $outResult;
    }

    /*
     * Получаем расходных накладных
     * @param integer $pickingID
     * @return array
     * */
    public function GetOutBoundData($BatchId)
    {
        $outResult = $this->_outResult;
        if(!empty($BatchId)) {
            $params['request'] = [
                'BatchId' =>$BatchId,
                'BusinessUnitId' => self::BUSINESS_UNIT_ID,
                'PageSize' => '0',
                'PageIndex' => '0',
                'CountAllItems' => false
            ];
            $apiResponse = $this->sendRequest('GetOutBoundData', $params);
            if ($result = @ArrayHelper::getValue($apiResponse['response'], 'GetOutBoundDataResult')) {
                if (ArrayHelper::getValue($result, 'HasError') === false) {
                    if ($data = ArrayHelper::getValue($result, 'Data.OutBoundThreePLDTO')) {
                        $outResult['HasError'] = false;
                        $outResult['Data'] = $data;
                        return $outResult;
                    }
                } else {
                    $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто. ['.ArrayHelper::getValue($result, 'Error').']';
                }
            } else {
                $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
            }
        } else {
            $outResult['ErrorMessage'] = 'Необходимо передать BatchId. т.е Номер расходной накладной';
        }
        return $outResult;
    }

    /*
    *
    * */
    public function SendOutBoundFeedBackData($data)
    {
        $outResult = $this->_outResult;

        $params['request'] = [
            'BusinessUnitId'=>self::BUSINESS_UNIT_ID,
            'PageSize' => '0',
            'PageIndex' => '0',
            'CountAllItems' => false,
            'FeedBackData' => $data
        ];

//        VarDumper::dump($params,10,true);
//        die('-SendInBoundFeedBackData-');
        $apiResponse = $this->sendRequest('SendOutBoundFeedBackData', $params);

        if($result = ArrayHelper::getValue($apiResponse['response'],'SendOutBoundFeedBackDataResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }
        return $outResult;
    }

    /*
        * Вызываем когда расходная накладная полностью собрана
        * @param string $PickingId
        * */
    public function MarkPickingforCompleted($PickingId)
    {
        $outResult = $this->_outResult;

        if(!empty($AppointmentBarcode)) {
            $params['request'] = [
                'BusinessUnitId'=>self::BUSINESS_UNIT_ID,
                'PickingId'=>$PickingId,
                'PageSize'=>'0',
                'PageIndex'=>'0',
                'CountAllItems'=>false,
            ];
            $apiResponse = $this->sendRequest('MarkPickingforCompleted', $params);

            if ($result = ArrayHelper::getValue($apiResponse['response'], 'MarkPickingforCompletedResult')) {
                if (ArrayHelper::getValue($result, 'HasError') === false) {
                    $outResult['HasError'] = false;
                } else {
                    $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто. ['.ArrayHelper::getValue($result, 'Error').']';
                }
            } else {
                $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
            }
        } else {
            $outResult['ErrorMessage'] = 'Необходимо передать PickingId. т.е Номер приходной накладной';
        }
        return $outResult;
    }


    /*
     * Список значений статусов для поля WarehouseAppointmentWMSStatus в методе GetWarehouseAppointments
     * */
    public static function WarehouseAppointmentWMSStatus()
    {
        return [
            'Nothing'=>'Новый',
            'MarkedforInBoundData'=>'В процессе подготовки. Ждем статус: InBoundDataIsPrepared',
            'InBoundDataIsPrepared'=>'Можно использовать метод GetAppointmentInBoundData',
            'Completed'=>'Заказ выполнен',
        ];
    }


    /*
     * получаем список всех товаров модель шк размер цвет и тд.
     * На онид SkuId может быть несколько шк товаров
     * @param string $processRequestedDataType Выполняется один раз потом с параметром:
     * Fall получить все
     * Changed - Получить только изменения
     * */
    public function getMasterData_OLD($SkuId = null,
                                  $Ean = null,
                                  $ShortCode = null,
                                  $processRequestedDataType = 'Full'
    )
    {
        $outResult = $this->_outResult;
        $params  = [];
        $params['request'] = [
            'BusinessUnitId' => self::BUSINESS_UNIT_ID,
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false
        ];

        if($processRequestedDataType) {
            $params['request']['ProcessRequestedDataType'] = $processRequestedDataType;
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
        $apiResponse = $this->sendRequest('GetMasterData', $params);
        if ($result = ArrayHelper::getValue($apiResponse, 'response.GetMasterDataResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if ($data = ArrayHelper::getValue($result, 'Data.MasterDataThreePL')) {
                    $outResult['HasError'] = false;
                    $outResult['Data'] = $data;
                    return $outResult;
                }
            }
        }
        return $outResult;
    }

    /*
     * @param string $countBarcode Получаем список новых шк. это нужно для отправки рпт со склада.
     * @return array ['22221234567899','2284567896325']
     * */
    public function createLcBarcode($countBarcode) // OK
    {
        $outResult = $this->_outResult;
        $params = [];
        $params['request'] = [
            'BusinessUnitId' => self::BUSINESS_UNIT_ID,
            'PageSize'=>'0',
            'PageIndex'=>'0',
            'CountAllItems'=>false,
            'Count'=> $countBarcode
        ];

        $apiResponse = $this->sendRequest('CreateLcBarcode', $params);
        if ($result = ArrayHelper::getValue($apiResponse, 'response.CreateLcBarcodeResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if ($data = ArrayHelper::getValue($result, 'Data.string')) {
                    $outResult['HasError'] = false;
                    $outResult['Data'] = $data;
                    return $outResult;
                }
            }
        }
        return $outResult;
    }

    /*
     * Получаем возвраты
     * */
    public function GetInBoundDataForReturn() // OK
    {
        $outResult = $this->_outResult;
        $params = [];
        $params['request'] = [
            'BusinessUnitId' =>self::BUSINESS_UNIT_ID,
            'PageSize' => '0',
            'PageIndex' => '0',
            'CountAllItems' => false,
        ];
        $apiResponse = $this->sendRequest('GetInBoundDataForReturn', $params);
        if ($result = @ArrayHelper::getValue($apiResponse['response'], 'GetInBoundDataForReturnResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if ($data = ArrayHelper::getValue($result, 'Data.InBoundThreePLDTO')) {
                    $resultDataArray = count($data) <=1 ? [$data] : $data;
                    $outResult['HasError'] = false;
                    $outResult['Data'] = $resultDataArray;
                    return $outResult;
                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто. ['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    /*
     * Отправляем принятые возвраты
     * */
    public function SendInBoundFeedBackDataForReturn($data) // OK
    {
        $outResult = $this->_outResult;
        $params = [];
        $params['request'] = [
            'BusinessUnitId'=>self::BUSINESS_UNIT_ID,
            'PageSize' => '0',
            'PageIndex' => '0',
            'CountAllItems' => false,
            'FeedBackData' => $data
        ];

        $apiResponse = $this->sendRequest('SendInBoundFeedBackDataForReturn', $params);

        if($result = ArrayHelper::getValue($apiResponse,'response.SendInBoundFeedBackDataForReturnResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }
        return $outResult;
    }


	/*
	* Это новый метод, он пока не используется, он возвращает информацию и товаре
	* */
	public function GetProductInfoV2 ($params) //
	{
		return $this->sendRequest('GetProductInfo', $params);
	}

	/*
	* Это новый метод, он пока не используется, он возвращает информацию и товаре
	* */
	public function GetProductInfo ($params) //
	{
		return $this->sendRequest('GetProductInfo', $params);
	}

	/*
	* Это новый метод, он пока не используется, он возвращает информацию о не принятых приходных накладных
	* */
	public function GetAppointments ($params) //
	{
		return $this->sendRequest('GetAppointments', $params);
	}

	/*
	* Это новый метод, он пока не используется, он возвращает информацию о не принятых приходных накладных
	* */
	public function GetAppointmentsV2($params) //
	{
		return $this->sendRequest('GetAppointmentsV2', $params);
	}

	/*
	* Это новый метод, он пока не используется,
	 * Это необязательная услуга, и если вы больше не хотите видеть полученные назначения в методе "GetAppointments",
	 * вам следует запустить эту услугу с параметрами запроса, указанными ниже.
	* */
	public function SendReadAppointments($params) //
	{
		return $this->sendRequest('SendReadAppointments', $params);
	}
	/*
	* Это новый метод, он пока не используется,
	 * Руководство подготовлено для того, чтобы объяснить,
	 * как настроить интеграционный сервис для перемещения продуктов по местоположениям на складах B2C DC.
	* */
	//public function MoveStock($params) //
	//{
	//	return $this->sendRequest('MoveStock', $params);
	//}

	/*
	* Это новый метод, он пока не используется,
	 * При использовании этого метода предполагаемое поступление товаров,
	 * отгруженных на склады, отображается в разрезе подразделений.
	 * Этот метод используется для проверки всех возвратов в бизнес-подразделения.
	* */
	public function GetReturnRequestShippedByWarehouse($params) //
	{
		return $this->sendRequest('GetReturnRequestShippedByWarehouse', $params);
	}

	/*
	* Это новый метод, он пока не используется,
	 * Не понятно что, делает и для чего?????
	 * Этот метод используется, чтобы отправили "читать" информацию на возврат, которое приходит в ответ
	 * Сделать запрос на возврат отправлен методом склада.Если будет отправлен сигнал read для возврата, то этот возврат больше
	 * не будет содержаться в ответе GetReturnRequestShippedByWarehouse. Возвращает значение isRead = 1 в случае
	 * успешного завершения
	* */
	public function UpdateReadReturnReques($params) //
	{
		return $this->sendRequest('UpdateReadReturnReques', $params);
	}

	/*
	* Это новый метод, он пока не используется,
	 * Не понятно что, делает и для чего?????
	* */
	public function GetShipmentsV2($params) //
	{
		return $this->sendRequest('GetShipmentsV2', $params);
	}

	/*
	* Это новый метод, он пока не используется,
	* Следует проверить ситуацию с отправкой, есть ли какие-либо отмены со стороны клиента.
	* Этот метод следует использовать на разных этапах отправки.
	* */
	public function PartialCancellationRequestExistsByCustomer($params) //
	{
		return $this->sendRequest('PartialCancellationRequestExistsByCustomer', $params);
	}

	/*
	* Это новый метод, он пока не используется,
	* Этот метод используется для отображения (для выбора причины возврата) кода возврата и причины возврата на
	* экране возврата товара со склада. Причина возврата - это информация, которая указывает клиенту, почему он вернул
	* товар (проблема с размером, качеством и т.д.) Ниже вы можете увидеть данные, которые будут получены от сервиса.
	* */
	public function GetReturnReasonResult($params) //
	{
		return $this->sendRequest('GetReturnReasonResult', $params);
	}

	/*
	 * * Это новый метод, он пока не используется,
	* ????????? ЧТО-ЭТО
	*/
	public function SendCargoDelivery($params) //
	{
		return $this->sendRequest('SendCargoDelivery', $params);
	}

	/*
	 * * Это новый метод, он пока не используется,
	* ????????? ЧТО-ЭТО
	*/
	public function SendReadBatches($params) //
	{
		return $this->sendRequest('SendReadBatches', $params);
	}

	/*
	*  Это новый метод, он пока не используется,
	* ????????? ЧТО-ЭТО
	*/
	public function ReturnShipmentV2($params) //
	{
		return $this->sendRequest('ReturnShipmentV2', $params);
	}

	/*
	* Это новый метод, он пока не используется,
	* ????????? ЧТО-ЭТО
	*/
	public function ReturnShipmentV3($params) //
	{
		return $this->sendRequest('ReturnShipmentV3', $params);
	}
		/*
	* The manual is prepared to explain integration service to be setup for move of the products within locations in the B2C DC warehouses.
	* BusinessUnitId: Business Unit ID of 3PL B2C DC
	* ProductBarcode: Single item barcode which is wanted to move.
	* Quantity: Number of products.
	* FromLocation: Lost or Stock or Damage
	* ToLocation: Lost or Stock or Damage
	* */
	public function MoveStock($items) //
	{
		$outResult = $this->_outResult;
		$params = [];
		$params['request'] = [
			'BusinessUnitId' => self::BUSINESS_UNIT_ID,
			'Items' => $items
		];

		$apiResponse = $this->sendRequest('MoveStock', $params);
		if ($result = ArrayHelper::getValue($apiResponse, 'response.MoveStockResult')) {
			if (ArrayHelper::getValue($result, 'HasError') === false) {
				if ($data = ArrayHelper::getValue($result, 'Data')) {
//					$resultDataArray = count($data) <=1 ? [$data] : $data;
					$outResult['HasError'] = false;
					$outResult['Data'] = $data;
					return $outResult;
				}
			} else {
				$outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.[' . ArrayHelper::getValue($result, 'Error') . ']';
			}
		} else {
			$outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
		}

		return $outResult;
	}

	/*
	 *
	 * */
	public function makeMoveStockRequest($productBarcode, $quantity, $fromLocation = "Stock", $toLocation = "Damage") // OK
	{
		$params = [];
		//$params['B2CMoveStockItem'][] = [
		$params[] = [
			"ProductBarcode" => $productBarcode,
			"Quantity" => $quantity,
			"FromLocation" => $fromLocation,
			"ToLocation" => $toLocation,
		];

		return $params;
	}
}
//ECommerceAPI_101020191
