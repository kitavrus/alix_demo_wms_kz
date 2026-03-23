<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 27.03.15
 * Time: 08:53
 */

namespace common\api;


use common\components\BarcodeManager;
use common\components\MailManager;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;

class DeFactoSoapAPI {

    /*
     * @var string url
     * */
//    public $urlToReturn = 'http://service.defacto.com.tr/Depo/KzkDepoNew/KzkDCIadeIslemleri.asmx?WSDL';
    public $urlToReturn = 'http://service.defacto.com.tr/Depo/KzkDepoIade/KzkDCIadeIslemleri.asmx?WSDL';
//    public $urlToReturnNew = 'http://service.defacto.com.tr/Depo/KzkDepoTest/KzkDCIadeIslemleri.asmx?WSDL';
//    public $urlToReturnNew = 'http://service.defacto.com.tr/Depo/KzkDepoIade/KzkDCIadeIslemleri.asmx?WSDL';
    //public $urlToReturnNew = 'http://service.defacto.com.tr/Depo/KzkDepoNew/KzkDCIadeIslemleri.asmx?WSDL';
    public $urlToReturnNew = 'http://service.defacto.com.tr/Depo/KzkIade/KzkDCIadeIslemleri.asmx?WSDL';
//    public $urlInOutboundOrder = 'http://service.defacto.com.tr/depo/KzkDepo/KzkDcDepoOperations.asmx?WSDL';
//    public $urlInOutboundOrder = 'http://service.defacto.com.tr/Depo/KzkDepoTest/KzkDepo/KzkDcDepoOperations.asmx?WSDL';
//    public $urlInOutboundOrder = 'http://service.defacto.com.tr/Depo/KzkDepoNew2/KzkDcDepoOperations.asmx?WSDL';
    public $urlInOutboundOrder = 'http://service.defacto.com.tr/Depo/KzkDepoNew/KzkDcDepoOperations.asmx?WSDL';
    //public $urlInOutboundOrder = 'http://service.defacto.com.tr/Depo/KzkDepoNew/KzkDcDepoOperations.asmx?WSDL';

    /*
     * @var soap client
     * */
    public $_client;

    /*
     * Connect to server
     * @param string $urlType r - return, io - inbound outbound order
     * */
    public function connectNew($urlType)
    {
        $url = $urlType== 'io' ? $this->urlInOutboundOrder : $this->urlToReturnNew;

        return $this->_client = new \SoapClient($url,
            [
                'trace' => 1,
                "exceptions" => 1,
                "soap_version" => SOAP_1_1,
                "connection_timeout" => 999
            ]
        );
    }

        /*
     * Connect to server
     * @param string $urlType r - return, io - inbound outbound order
     * */
    public function connect($urlType)
    {
        $url = $urlType== 'io' ? $this->urlInOutboundOrder : $this->urlToReturn;

        return $this->_client = new \SoapClient($url,
            [
                'trace' => 1,
                "exceptions" => 1,
                "soap_version" => SOAP_1_1,
                "connection_timeout" => 999
            ]
        );
    }

    /*
     * Call function GetKoli , IadeKabul , etc
     * @param string $method Call function name
     * @param array $params Data params for call function
     * @return array [errors, response]
     * */
    public function sendRequest($method,$params)
    {
        if(empty($this->_client)) {
           return false;
        }

        $response = [];
        $errors = [];
        $return = [];

        try {
            $response = $this->_client->$method($params);
        } catch (\SoapFault $exception) {
            $errors ['LastRequest'] = $this->_client->__getLastRequest();
            $errors ['Exception'] = $exception;
            $errors ['ExceptionMessage'] = $exception->getMessage();
         }

        $return['errors'] = $errors;
        $return['response'] = $response;


        $dirPath = 'api/de-facto/all/'.date('Ymd');
        BaseFileHelper::createDirectory($dirPath);

        file_put_contents($dirPath."/method-".$method.".log","\n"."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath."/method-".$method.".log",date('Ymd-H:i:s')."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath."/method-".$method.".log",print_r($params,true)."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath."/method-".$method.".log",print_r($return,true)."\n",FILE_APPEND);
        if(!empty($return['errors'])) {
            file_put_contents($dirPath . "/method-" . $method . "-errors.log", print_r($params, true) . "\n"."\n", FILE_APPEND);
            file_put_contents($dirPath . "/method-" . $method . "-errors.log", print_r($return, true) . "\n", FILE_APPEND);
        }

        return $return;
    }

    /*
     * Get inbound order
     * @param $invoice receive by mail from DeFacto manager
     * @return array ['errors'=>'...','response'=>['...'=>'...']]
     * */
    public function getInboundOrderByInvoice($invoice)
    {
        $return = [];
        $errors = [];
        $params = [];
        $apiResponse = [];

        $invoice = trim($invoice);
        if(empty($invoice)) {
            $return['errors'] = 'Invoice empty';
            $return['response'] = [];

            return $return;
        }

        $params = [
            'request' => [
                'ForeignInvoice' => '',
                'CrossDockType' => '',
                'BarkodId' => '',
                'Barkod' => '',
                'Miktar' => '',
                'DepoId' => '',
                'IrsaliyeNo' => '',
                'RezerveId' => '',
                'KoliId' => '',
                'KoliKargo' => '',
                'KoliDesi'  => '',
                'PartiNo'  => '',
                'UserName'=>'',
                'Password'=> '',
                'YurtDisiIrsaliyeNo' => $invoice
            ]
        ];

        $this->connect('io');
        $apiResponse = $this->sendRequest('UrunKabulBilgileri',$params);

//        VarDumper::dump($apiResponse,10,true);
//        die('TT');

        $outboundOrders = [];
        if( empty($apiResponse['errors']) ) {

            if(isset($apiResponse['response']->UrunKabulBilgileriResult->KZKDCUrunKabulBilgileriDto)) {
                $results = $apiResponse['response']->UrunKabulBilgileriResult->KZKDCUrunKabulBilgileriDto;

                if (!empty($results)) {
                    if(count($results) == 1) {
                        $x = [];
                        $x[] = $results;
                        $results = $x;
                    }

                    foreach ($results as $order) {


//                        VarDumper::dump($order,10,true);

                        $BelgeId = isset($order->BelgeId) ? trim($order->BelgeId) : '';
                        $IrsaliyeSeri = isset($order->IrsaliyeSeri) ? trim($order->IrsaliyeSeri) : '';
                        $IrsaliyeNo = isset($order->IrsaliyeNo) ? trim($order->IrsaliyeNo) : '';
                        $Barkod = isset($order->Barkod) ? trim($order->Barkod) : '';
                        $BarkodId = isset($order->BarkodId) ? trim($order->BarkodId) : '';
                        $DepoId = isset($order->DepoId) ? trim($order->DepoId) : '';
                        $KarsiDepoId = isset($order->KarsiDepoId) ? trim($order->KarsiDepoId) : '';
                        $KoliId = isset($order->KoliId) ? trim($order->KoliId) : '';
                        $KoliKapatmaBarkod = isset($order->KoliKapatmaBarkod) ? trim($order->KoliKapatmaBarkod) : '';
                        $CrossDock = isset($order->CrossDock) ? trim($order->CrossDock) : '';
                        $UrunId = isset($order->UrunId) ? trim($order->UrunId) : '';
                        $KisaKod = isset($order->KisaKod) ? trim($order->KisaKod) : '';
                        $Miktar = isset($order->Miktar) ? trim($order->Miktar) : '';
                        $Desi = isset($order->Desi) ? trim($order->Desi) : '';
                        $NetAgirlik = isset($order->NetAgirlik) ? trim($order->NetAgirlik) : '';
                        $BrutAgirlik = isset($order->BrutAgirlik) ? trim($order->BrutAgirlik) : '';


                        $outboundOrders[] = [
                            'BelgeId' => $BelgeId, // +
                            'IrsaliyeSeri' => $IrsaliyeSeri, // +
                            'IrsaliyeNo' => $IrsaliyeNo, // +
                            'Barkod' => $Barkod, // +
                            'BarkodId' => $BarkodId, // +
                            'DepoId' => $DepoId, // +
                            'KarsiDepoId' => $KarsiDepoId, // +
                            'KoliId' => $KoliId, // +
                            'KoliKapatmaBarkod' => $KoliKapatmaBarkod, // +
                            'CrossDock' => $CrossDock, // +
                            'UrunId' => $UrunId, // +
                            'KisaKod' => $KisaKod, // +
                            'Miktar' => $Miktar, // +
                            'Desi' => $Desi, // +
                            'NetAgirlik' => $NetAgirlik, // +
                            'BrutAgirlik' => $BrutAgirlik, // +
                        ];
                    }
                }

            }
        } else {
            $errors = $apiResponse['errors'];

//            $mm = new MailManager();
//            $mm->sendErrorsMessageMail($errors);
        }

        $return['errors'] = $errors;
        $return['response'] = $outboundOrders;

        $dirPath = 'api/de-facto/inbound/'.date('Ymd-His');
        BaseFileHelper::createDirectory($dirPath);

        file_put_contents($dirPath.'/getInboundOrderByInvoice-DE-FECTO-API.log',"\n"."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath.'/getInboundOrderByInvoice-DE-FECTO-API.log',"PARAMS: "."\n".print_r($params,true)."\n",FILE_APPEND);
        file_put_contents($dirPath.'/getInboundOrderByInvoice-DE-FECTO-API.log',print_r($return,true)."\n",FILE_APPEND);
        file_put_contents($dirPath.'/getInboundOrderByInvoice-DE-FECTO-API.log',print_r($apiResponse,true)."\n",FILE_APPEND);

        return $return;
    }

    /*
     * Confirm accepted order on stock. Send accepted order data to DeFacto API server
     * @param array $data Accepted inbound orders data
     * @return boolean true or false
     * */
    public function confirmInboundOrder($data)
    {
        $return = [];
        $apiResponse = [];

        if(!empty($data) && is_array($data)) {
            $this->connect('io');
            foreach($data as $order) {
                $params = [
                    'request' => [
                        'UserName'=>'',
                        'Password'=> '',
                        'YurtDisiIrsaliyeNo' => $order['YurtDisiIrsaliyeNo'],
                        'CrossDockType' => $order['CrossDockType'],
                        'Barkod' => $order['Barkod'],
                        'Miktar' => $order['Miktar'],
                    ]
                ];

//                VarDumper::dump($params,10,true);
                $apiResponse = $this->sendRequest('UrunOnKabul',$params);
                $return[] = $params;
                $return[] = $apiResponse;
            }
//            $mm = new MailManager();
//            $mm->sendErrorsMessageMail($return);
        }

        $dirPath = 'api/de-facto/inbound/'.date('Ymd-His');
        BaseFileHelper::createDirectory($dirPath);

        file_put_contents($dirPath.'/confirmInboundOrder-DE-FECTO-API.log',"\n"."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath.'/confirmInboundOrder-DE-FECTO-API.log',print_r($return,true)."\n",FILE_APPEND);

        return $return;
    }

    /*
     * Получаем количество принятых товаров по приходной накладной из АПИ Дефакто
     * @param string $invoice inbound orders data
     * @return integer
     * */
    public function getUrunOnKabulTamamlandiInbound($invoice)
    {
        $return = [];
        $errors = [];
        $apiResponse = [];
        $result = 0;

        $invoice = trim($invoice);
        if(empty($invoice)) {
            $return['errors'] = 'Invoice empty';
            $return['response'] = [];

            return $return;
        }

        $this->connect('io');
        $params = [
            'request' => [
                'ForeignInvoice' => '',
                'UserName'=>'',
                'Password'=> '',
                'CrossDockType' => '',
                'Barkod' => '',
                'BarkodId' => '',
                'Miktar' => '',
                'DepoId' => '',
                'IrsaliyeNo' => '',
                'IrsaliyeSeri' => '',
                'RezerveId' => '',
                'KoliId' => '',
                'KoliKargo' => '',
                'KoliDesi'  => '',
                'PartiNo'  => '',
                'YurtDisiIrsaliyeNo' => $invoice
            ]
        ];

        $apiResponse = $this->sendRequest('UrunOnKabulTamamlandi',$params);

        if( empty($apiResponse['errors']) ) {
            $result = $apiResponse['response']->UrunOnKabulTamamlandiResult;
        } else {
            $errors = $apiResponse['errors'];
        }

        $return['errors'] = $errors;
        $return['response'] = $result;

        $dirPath = 'api/de-facto/inbound/'.date('Ymd-His');
        BaseFileHelper::createDirectory($dirPath);
        file_put_contents($dirPath.'/getUrunOnKabulTamamlandiInbound-DE-FECTO-API.log',"\n"."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath.'/getUrunOnKabulTamamlandiInbound-DE-FECTO-API.log',"PARAMS: "."\n".print_r($params,true)."\n",FILE_APPEND);
        file_put_contents($dirPath.'/getUrunOnKabulTamamlandiInbound-DE-FECTO-API.log',print_r($return,true)."\n",FILE_APPEND);
        file_put_contents($dirPath.'/getUrunOnKabulTamamlandiInbound-DE-FECTO-API.log',print_r($apiResponse,true)."\n",FILE_APPEND);

        return $return;
    }

    /*
    * Get outbound order
    * @param $invoice receive by mail from DeFacto manager
    * @return array ['errors'=>'...','response'=>['...'=>'...']]
    * */
    public function getOutboundOrderByInvoice($invoice)
    {
        $return = [];
        $errors = [];
        $params = [];
        $apiResponse = [];

        $invoice = trim($invoice);
        if(empty($invoice)) {
            $return['errors'] = 'Invoice empty';
            $return['response'] = [];

            return $return;
        }

        $params = [
            'request' => [
                'ForeignInvoice' => '',
                'UserName'=>'',
                'Password'=> '',
                'CrossDockType' => '',
                'Barkod' => '',
                'BarkodId' => '',
                'Miktar' => '',
                'DepoId' => '',
                'IrsaliyeNo' => '',
                'IrsaliyeSeri' => '',
                'RezerveId' => '',
                'KoliId' => '',
                'KoliKargo' => '',
                'KoliDesi'  => '',
                'PartiNo'  => $invoice,
                'YurtDisiIrsaliyeNo' => ''
            ]
        ];

        $this->connect('io');
        $apiResponse = $this->sendRequest('RezerveDetayList',$params);

        $outboundOrders = [];
        if( empty($apiResponse['errors']) ) {

    //            VarDumper::dump($apiResponse['response']->RezerveDetayListResult, 10, true);
    //            die('-TT-');

            if (isset($apiResponse['response']->RezerveDetayListResult->KZKDCRezerveDetayBilgileriDto)) {
                $results = $apiResponse['response']->RezerveDetayListResult->KZKDCRezerveDetayBilgileriDto;


    //                VarDumper::dump($apiResponse['response']->RezerveDetayListResult, 10, true);
    //                VarDumper::dump($results,10,true);
    //                die;
                if (!empty($results) && count($results)>1) {
                    foreach ($results as $order) {
                        $outboundOrders[] = [
                            'RezerveId' => trim($order->RezerveId),
                            'CariId' => trim($order->CariId),
                            'CariYerId' => trim($order->CariYerId),
                            'Ad' => trim($order->Ad),
                            'BarkodId' => trim($order->BarkodId),
                            'Barkod' => trim($order->Barkod),
                            'Miktar' => trim($order->Miktar),
                            'PartiNo' => trim($order->PartiNo),
                            'PartiOnayTarih' => trim($order->PartiOnayTarih),
                        ];
                    }
                } elseif (!empty($results) && count($results)==1) {
                    $outboundOrders[] = [
                        'RezerveId' => trim($results->RezerveId),
                        'CariId' => trim($results->CariId),
                        'CariYerId' => trim($results->CariYerId),
                        'Ad' => trim($results->Ad),
                        'BarkodId' => trim($results->BarkodId),
                        'Barkod' => trim($results->Barkod),
                        'Miktar' => trim($results->Miktar),
                        'PartiNo' => trim($results->PartiNo),
                        'PartiOnayTarih' => trim($results->PartiOnayTarih),
                    ];
                }

            } else {
                $errors = 'Вы ввели неправильный номер накладной';
            }

        } else {
            $errors = $apiResponse['errors'];
        }

        $return['errors'] = $errors;
        $return['response'] = $outboundOrders;

//        $mm = new MailManager();
//        $mm->sendErrorsMessageMail($return);
        $dirPath = 'api/de-facto/outbound/'.date('Ymd-His');
        BaseFileHelper::createDirectory($dirPath);

        file_put_contents($dirPath.'/getOutboundOrderByInvoice-DE-FECTO-API.log',"\n"."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath.'/getOutboundOrderByInvoice-DE-FECTO-API.log',"PARAMS: "."\n".print_r($params,true)."\n",FILE_APPEND);
        file_put_contents($dirPath.'/getOutboundOrderByInvoice-DE-FECTO-API.log',print_r($return,true)."\n",FILE_APPEND);
        file_put_contents($dirPath.'/getOutboundOrderByInvoice-DE-FECTO-API.log',print_r($apiResponse,true)."\n",FILE_APPEND);

        return $return;
    }

    /*
     * Confirm accepted order on stock. Send accepted order data to DeFacto API server
     * @param array $data Accepted outbound orders data
     * @return boolean true or false
     * */
    public function confirmOutboundOrder($data)
    {
        $return = [];
        $errors = [];
        $params = [];
        $apiResponse = [];
        $result = 0;

        if(!empty($data) && is_array($data)) {

            $this->connect('io');
            $reserve_dagitim_list = [];
            foreach($data as $order) {
//                $reserve_id = $order;
//                $barkod = $order;
//                $miktar = $order;
//                $irsaliye_no =$order;
//                $koli_id = $order;
//                $koli_desi = $order;

                $tmp = [
                    'IrsaliyeNo' => $order['IrsaliyeNo'],
                    'IrsaliyeSeri' => 'E',
                    'RezerveId' => $order['RezerveId'],
                    'KoliId' => $order['KoliId'],
                    'Barkod' => $order['Barkod'],
                    'KoliKargoEtiketId'=>$order['KoliKargoEtiketId'], //
//                    'KoliKargoEtiketId'=> 1, //
                    'Miktar' => $order['Miktar'],
                    'KoliDesi'  => $order['KoliDesi'],
                    'ForeignInvoiceNo'  => $order['ForeignInvoiceNo'],
                ];

                $reserve_dagitim_list['RezerveDagitimDto'][] = $tmp;
            }

            $params = [
                'request' => [
                    'UserName'=>'',
                    'Password'=> '',
                    'RezerveDagitimListe' => $reserve_dagitim_list,
                ]
            ];

//            VarDumper::dump($params,10,true);
//            die;
            $apiResponse = $this->sendRequest('RezerveDagitim',$params);
//
            if( empty($apiResponse['errors']) ) {
                $result = $apiResponse['response']->RezerveDagitimResult;
            } else {
                $errors = $apiResponse['errors'];
            }

            $return['errors'] = $errors;
            $return['response'] = $result;
        } else {
            $return['errors'] = 'Invoice empty';
            $return['response'] = [];
        }

        $dirPath = 'api/de-facto/outbound/'.date('Ymd-His');
        BaseFileHelper::createDirectory($dirPath);

        file_put_contents($dirPath.'/confirmOutboundOrder-DE-FECTO-API.log',"\n"."\n"."\n",FILE_APPEND);
        file_put_contents($dirPath.'/confirmOutboundOrder-DE-FECTO-API.log',"PARAMS: "."\n".print_r($params,true)."\n",FILE_APPEND);
        file_put_contents($dirPath.'/confirmOutboundOrder-DE-FECTO-API.log',print_r($return,true)."\n",FILE_APPEND);
        file_put_contents($dirPath.'/confirmOutboundOrder-DE-FECTO-API.log',print_r($apiResponse,true)."\n",FILE_APPEND);
//        die("--confirmOutboundOrder--");
//        $mm = new MailManager();
//        $mm->sendErrorsMessageMail($return);

        return $return;
    }

    /*
     * Prepared data for outbound order confirm
     * @param integer $outboundOrderID Outbound order ID
     * @return array
     * */
    public static function preparedDataForOutboundConfirm($outboundOrderID)
    {
        $data = [];
        $outboundOne = OutboundOrder::findOne($outboundOrderID);
        if ($outboundOne) {
            $stockAll = Stock::find()->select('product_barcode, inbound_order_id, count(id) as accepted_qty, box_barcode, box_size_m3, box_size_barcode')
                ->andWhere(['outbound_order_id' => $outboundOne->id]) // ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
                ->andWhere(['status' => Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL]) // ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
                ->groupBy('product_barcode, box_barcode')
                ->orderBy('box_barcode')
                ->asArray()
                ->all();

            $stockCountBoxBarcodeAll = Stock::find()->select('box_barcode, box_size_barcode')
                ->where(['outbound_order_id' => $outboundOne->id])
                ->groupBy('box_barcode')
                ->asArray()
                ->all();

            if ($stockAll) {
                foreach ($stockAll as $box => $stock) {
                    $boxI = 1;
                    $k = 0;
                    if (!empty($stockCountBoxBarcodeAll)) {
                        foreach ($stockCountBoxBarcodeAll as $b) {
                            $k++;
                            if ($b['box_barcode'] == $stock['box_barcode']) {
                                $boxI = $k;
                            }
                        }
                    }
                    $toKoliDesi = 32;
                    if(!empty($stock['box_size_barcode'])) {
                        if($m333 = BarcodeManager::getIntegerM3($stock['box_size_m3'])) {
                            $toKoliDesi = $m333;
                        }
                    }

                    $KoliDesi = $toKoliDesi;

                    if ($stock['accepted_qty'] >= 1) {
                        $ForeignInvoiceNo = '';
                        if($inboundOrder = InboundOrder::findOne($stock['inbound_order_id'])) {
                            $ForeignInvoiceNo = $inboundOrder->order_number;
                        }
                        $data[] = [
                            'RezerveId' => $outboundOne->order_number,
                            'Barkod' => $stock['product_barcode'],
                            'Miktar' => $stock['accepted_qty'],
                            'IrsaliyeNo' => $outboundOne->order_number,
                            'KoliId' => $boxI, // который короб
                            'KoliDesi' => $KoliDesi, // m3
//                            'KoliDesi' => !empty($stock['box_size_barcode']) ? $stock['box_size_barcode'] : '32', // m3
                            'KoliKargoEtiketId' => $stock['box_barcode'], // box Barcode
                            'ForeignInvoiceNo' => $ForeignInvoiceNo, // Номер партии
//                            'KoliKargoEtiketId' => str_replace('b','7',$stock['box_barcode']), // box Barcode
                        ];
                    }
                }
            }
        }

        return $data;
    }

    // --------------------------RETURN BEGIN---------------------------


    /*
    * Prepared data for return with product to confirm
    * @param integer $returnID Return order ID
    * @return array
    * */
    public static function preparedDataForReturnConfirm($returnID)
    {
        $returnArray = [];

        $items = ReturnOrderItems::find()->where(['return_order_id'=>$returnID])->asArray()->all();
        if(!empty($items) && is_array($items)) {
            foreach($items as $item) {
                if($item['accepted_qty'] > 0) {
                    $returnArray[] = [
                        'Barkod' => $item['product_barcode'],
                        'Miktar' => $item['accepted_qty'],
                    ];
                }
            }
        }

        return $returnArray;
    }
    /*
     *
     * @param string $BelgeKodu  Order number
     * */
    public function KoliIadeKabul($BelgeKodu)
    {
//        $params = [
//            'request' => [
//                'UserName' => '',
//                'Password' => '',
//                'BelgeKodu' => $BelgeKodu,
////                'BelgeKodu' => $model->order_number,
//                'KzkDCIade'=>[
//                    'BelgeID'=>'',
//                ]
//            ]
//        ];
//        $this->connectNew('r');
////        $apiResponse = $this->sendRequest('KoliIadeKabul',$params);
//        VarDumper::dump($apiResponse,10,true);
//        die;
    }

    /*
     * @param string $BelgeKodu  Order number
     * */
    public function GetKoli($BelgeKodu)
    {
/*        $params = [
            'request' => [
                'UserName' => '',
                'Password' => '',
                'BelgeKodu' => $BelgeKodu,
//                'BelgeKodu' => $model->order_number,
            ]
        ];
        $this->connectNew('r');
        $apiResponse = $this->sendRequest('GetKoli',$params);
        VarDumper::dump($apiResponse,10,true);
        die;*/
    }
    /*
     *
     * */
    public static function returnApiMessageCode()
    {
        return [
            '000'=> ['m'=>'Başarılı','s'=>0], // Successful
            '001'=> ['m'=>'Belge kodu giriniz.','s'=>0], // Document code enter.
            '002'=> ['m'=>'Geçersiz belge kodu.Belge Kodu = BelgeID;MagazaKodu;OnayDurumu alanlarının birleşmesinden oluşmalıdır.','s'=>0], //Invalid document code.Document Code = BelgeID;store code;check the status field is formed from the merger of.
            '003'=> ['m'=>'Bekliyor ( Kabul edilmis, stoga alinmayi bekliyor. )','s'=>0], // Waiting ( accepted, waiting to be picked up stage. )
            '004'=> ['m'=>'Açık adet olarak kabul edildi.( Kabul edilmis)','s'=>0], //Outdoor units were considered.( Accepted)
            '005'=> ['m'=>'HatadanDolayiBekliyor ( Kabul edilmis, hatadan dolayi stoga alinamamis bekliyor. )','s'=>0], // Waiting for a mistake ( accepted error are not included in the inventory for waiting. )
            '006'=> ['m'=>'Koli dönüşümü yapıldı. ( Stoga alındı.)','s'=>0], // The transformation of the parcel. ( Stage).
            '008'=> ['m'=>'Iade girişi yapılmış.Tekrar iade işlemi yapılamaz.','s'=>0], // Return entry is made.Again, a refund can be processed.
            '009'=> ['m'=>'Koli barkod bilgisi oluşturulamadı.','s'=>0], // Parcel could be created from the barcode information.
            '010'=> ['m'=>'Koli bilgisi gönderilmelidir.','s'=>0], // Parcel information should be sent to.
            '011'=> ['m'=>'Koli bilgisi bulunamadı.','s'=>1], // + Parcel information was found.
            '098'=> ['m'=>'Exception hatası','s'=>1], // + Exception error
            '099'=> ['m'=>'Bilinmeyen Hata','s'=>1], // + Unknown Error
            '007'=> ['m'=>'511 - KZK DC IADE DEPO ya iade girisi yapabilirsiniz.Karşı depo Id boş veya hatalı','s'=>1], // + 511 - KZ storage or you can make a return DC return entry.Against the repository ID is blank or incorrect
            '097'=> ['m'=>'İrsaliye bulunamadı','s'=>1], // + The delivery note could not be found
            //'012'=> ['m'=>'Koli Barkod bilgisi oluşturulamadı.','s'=>1], // TODO  Новый статус нужно понять что он значит
        ];
    }

    // --------------------------RETURN END---------------------------

}