<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */
namespace common\ecommerce\defacto\transfer\service;



use common\ecommerce\defacto\api\service\EcommerceAPILogService;
use yii\helpers\ArrayHelper;

class TransferAPIService
{
    private $api;

    /*
 * @var array Default format function result
 * */
    private $_outResult = [
        'HasError'=>true,
        'ErrorMessage'=>'',
        'Message'=>'',
        'Data'=>[],
    ];

    /**
     * TransferAPIService constructor.
     */
    public function __construct()
    {
        $this->api = new \common\ecommerce\defacto\api\ECommerceAPI();
    }

    private function makeGetBatchesRequest() {

        $params = [];
        $params['request'] = [
            'BusinessUnitId' => $this->api->BUSINESS_UNIT_ID(),
            'StatusList' =>[
                'OutBoundDataIsPrepared'
            ],
        ];

        return $params;
    }

    private function parseGetBatches($aApiResponse) {

        $outResult = $this->_outResult;
        if ($result = @ArrayHelper::getValue($aApiResponse['response'], 'GetBatchsResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if ($data = @ArrayHelper::getValue($result, 'Data.B2CBatchDto')) {
                    $resultDataArray = count($data) <= 1 ? [$data] : $data;
                    $outResult['HasError'] = false;
                    $outResult['Data'] = $resultDataArray;

                    return $outResult;
                } else {
                    $outResult['ErrorMessage'] = 'Дефакто вернул пустоту';
                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.[' . ArrayHelper::getValue($result, 'Error') . ']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    /**
    * Получаем короба которые нужно переместит на складе B2C to B2B
    * */
    public function GetBatches()
    {
        $APILogService = new EcommerceAPILogService();

        $dataForSendByAPI = $this->makeGetBatchesRequest();
        $APILogService->GetBatchesRequest($dataForSendByAPI);

        $apiResponse = $this->api->GetBatches($dataForSendByAPI);
        $dataForSendByAPIResponse = $this->parseGetBatches($apiResponse);
        $APILogService->GetBatchesResponse($dataForSendByAPIResponse);

        return $dataForSendByAPIResponse;
    }


    private function GetOutBoundRequest($aBatchId) {

        $outResult = $this->_outResult;
        if(empty($aBatchId)) {
            $outResult['ErrorMessage'] = 'Нет данных для отсылки по АПИ';
            return $outResult;
        }

        $params = [];
        $params['request'] = [
            'BusinessUnitId'=> $this->api->BUSINESS_UNIT_ID(),
            'BatchId'=>$aBatchId,
        ];

        return $params;
    }

    private function parseGetOutBound($aApiResponse) {

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'GetOutBoundResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
                $outResult['Data'] = $result;
                return $outResult;
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    /*
    * Получаем содержимое коробки(которые молучили через метод GetBatchs)
    * */
    public function GetOutBound($aBatchId) //
    {
        $APILogService = new EcommerceAPILogService();

        $dataForSendByAPI = $this->GetOutBoundRequest($aBatchId);
        $APILogService->GetBatchesRequest($dataForSendByAPI);

        $apiResponse = $this->api->GetOutBound($dataForSendByAPI);
        $dataForSendByAPIResponse = $this->parseGetOutBound($apiResponse);
        $APILogService->GetOutBoundResponse($dataForSendByAPIResponse);

        return $dataForSendByAPIResponse;
    }


    private function MarkBatchForCompletedRequest($aBatchId) {

        $outResult = $this->_outResult;
        if(empty($aBatchId)) {
            $outResult['ErrorMessage'] = 'Нет данных для отсылки по АПИ';
            return $outResult;
        }

        $params = [];
        $params['request'] = [
            'BusinessUnitId'=> $this->api->BUSINESS_UNIT_ID(),
            'BatchId'=>$aBatchId,
        ];

        return $params;
    }

    private function parseMarkBatchForCompleted($aApiResponse) {

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'MarkBatchforCompletedResponse')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
                $outResult['Data'] = $result;
                return $outResult;
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    /*
    * Закрываем короба которые мы приняли
    * */
    public function MarkBatchForCompleted($aBatchId) //
    {
        $APILogService = new EcommerceAPILogService();

        $dataForSendByAPI = $this->MarkBatchForCompletedRequest($aBatchId);
        $APILogService->MarkBatchForCompletedRequest($dataForSendByAPI);

        $apiResponse = $this->api->MarkBatchForCompleted($dataForSendByAPI);
        $dataForSendByAPIResponse = $this->parseMarkBatchForCompleted($apiResponse);
        $APILogService->MarkBatchForCompletedResponse($dataForSendByAPIResponse);

        return $dataForSendByAPIResponse;
    }

    private function SendOutBoundFeedBackRequest($aItems) {

        $outResult = $this->_outResult;
        if(empty($aItems) || !is_array($aItems)) {
            $outResult['ErrorMessage'] = 'Нет данных для отсылки по АПИ';
            return $outResult;
        }

        $B2COutBoundFeedBackDTO = [];
        foreach($aItems as $item) {
            $B2COutBoundFeedBackDTO [] = [
                    'OutBoundId'=> $item->OutBoundId,
                    'LcBarcode'=> $item->LcBarcode,
                    'LotOrSingleBarcode'=> $item->LotOrSingleBarcode,
                    'LotOrSingleQuantity'=> $item->LotOrSingleQuantity,
                    'WaybillSerial' => 'KZCOM',
                    'WaybillNumber'=> $item->WaybillNumber,
                    'Volume'=> '1',
                    'CargoShipmentNo'=> '?',
                    'InvoiceNumber'=> '?',
                ];
        }

        $params = [];
        $params['request']['FeedBackData']['B2COutBoundFeedBackDTO'] = $B2COutBoundFeedBackDTO;

        return $params;
    }

    private function parseSendOutBoundFeedBack($aApiResponse) {

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'SendOutBoundFeedBackResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
                $outResult['Data'] = $result;
                return $outResult;
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    /*
    * Отправляем принятные короба
    * */
    public function SendOutBoundFeedBack($aSendOutBoundFeedBack) //
    {
        $APILogService = new EcommerceAPILogService();

        $dataForSendByAPI = $this->SendOutBoundFeedBackRequest($aSendOutBoundFeedBack);
//        file_put_contents('SendOutBoundFeedBack-TEST-.log',print_r($dataForSendByAPI,true));
//        die;
        $APILogService->SendOutBoundFeedBackRequest($dataForSendByAPI);

        $apiResponse = $this->api->SendOutBoundFeedBack($dataForSendByAPI);
        $dataForSendByAPIResponse = $this->parseSendOutBoundFeedBack($apiResponse);
        $APILogService->SendOutBoundFeedBackResponse($dataForSendByAPIResponse);

        return $dataForSendByAPIResponse;
    }

    /*
     * Создаем новый шк короба
     * */
    public function CreateLcBarcode($aCountBarcode = 10) {
        return $this->api->createLcBarcode($aCountBarcode);
    }
}