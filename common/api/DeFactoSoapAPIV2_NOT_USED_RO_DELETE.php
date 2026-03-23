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
use common\modules\outbound\models\OutboundOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;

class DeFactoSoapAPIV2 {

    /*
     * @var string url
     * */
    private $_url = 'http://service.defacto.com.tr/Depo/KzkDepoIade/KzkDCIadeIslemleri.asmx?WSDL';
//    private $urlToReturn = 'http://service.defacto.com.tr/Depo/KzkDepoIade/KzkDCIadeIslemleri.asmx?WSDL';
//    private $urlToReturnNew = 'http://service.defacto.com.tr/Depo/KzkIade/KzkDCIadeIslemleri.asmx?WSDL';
//    private $urlInOutboundOrder = 'http://service.defacto.com.tr/Depo/KzkDepoNew/KzkDcDepoOperations.asmx?WSDL';

    /*
     * @var soap client
     * */
    private $_client;

    /*
     * @param string $connectType Value "io" or "r". Default 'io'
     * */
    public function __construct($connectType = 'io') {}

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
     * получаем список всех товаров модель шк размер цвет и тд.
     * На онид SkuId может быть несколько шк товаров
     * Выполняется один раз потом с параметром:
     * Fall получить все
     * Changed - Получить только изменения
     * */
    public function getMasterData()
    {
        $params = [];
        $this->connect('io');
        $apiResponse = $this->sendRequest('GetMasterData',$params);
        if($data = ArrayHelper::getValue($apiResponse,'ReadListDataResponseOfMasterDataThreePL')) {
            $dataToSaveDb = [
                '1'=>$data['ShortCode'], //
                'name'=>$data['Description'], //
                'client_product_id'=>$data['SkuId'],  //
                '4'=>$data['Ean'], // Это баркод
                '5'=>$data['Note'], //
                '6'=>$data['Nop'], //
                '7'=>$data['LotSingle'], //
                '8'=>$data['Classification'], //
                'color'=>$data['Color'], //
                'size'=>$data['Size'], //
                '11'=>$data['FDate'], //
                '12'=>$data['Perc'], //
                '13'=>$data['Origin'], //
                '14'=>$data['ProcessTime'], //
                'sku'=>$data['SKU'], //
            ];
        }
    }

    /* Получаем приход и крос док
     *
     * */
    public function getInBoundData()
    {
        $params = [];
        $this->connect('io');
        $apiResponse = $this->sendRequest('GetInBoundData',$params);
        if($data = ArrayHelper::getValue($apiResponse,'ReadListDataResponseOfInBoundThreePL')) {
            $dataToSaveDb = [
                '1'=>$data['Order'], //
                '2'=>$data['Qc'], //
                '3'=>$data['Preadmission'], //
                '4'=>$data['Label'], //
                '5'=>$data['SkuId'], //
                '6'=>$data['Ean'], //
                '7'=>$data['Date'], //
                '8'=>$data['Short'], //
                '9'=>$data['Quantity'], //
                '10'=>$data['Carton'], //
                '11'=>$data['Origin'], //
                '12'=>$data['Nob'], //
                '13'=>$data['FromBusinessUnitId'], //
                '14'=>$data['ProcessTime'], //
                '15'=>$data['SKU'], //
            ];
        }
    }

    /*  Подтверждаем полученые данные по приходу и крос доку. Покоробочно
     *
     * */
    public function writeInBoundFeedBackData(array $WriteListDataRequestOfInBoundFeedBackThreePL)
    {
        $params = [];
        if(!empty($WriteListDataRequestOfInBoundFeedBackThreePL)) {
            $params = $WriteListDataRequestOfInBoundFeedBackThreePL;
        } else{
            return 0;
        }

        $this->connect('io');
        $apiResponse = $this->sendRequest('WriteInBoundFeedBackData',$params);
        if($data = ArrayHelper::getValue($apiResponse,'WriteInBoundFeedBackDataResult')) {

        }

        return 1;
    }

    /* Подготоваливаем данные полученые по апи от клиента для сохранения в нашу базу
     *
     * */
    public function preparedInBoundDataForSaveToDb(){}

    /* Подготоваливаем данные принятых нами коробов и лоты для отправки на подтверждение.
     *
     * */
    public function preparedWriteInBoundFeedBackData(){}
}