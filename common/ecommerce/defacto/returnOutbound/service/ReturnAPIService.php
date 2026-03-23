<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 26.08.2019
 * Time: 9:38
 */
namespace common\ecommerce\defacto\returnOutbound\service;

use common\ecommerce\defacto\api\service\EcommerceAPILogService;
use yii\helpers\ArrayHelper;

class ReturnAPIService
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
     * InboundAPI constructor.
     */
    public function __construct()
    {
         $this->api = new \common\ecommerce\defacto\api\ECommerceAPI();
    }

    public function makeGetReturnReasonListRequest() {
        return [];
    }

    public function parseGetReturnReasonListResponse($aApiResponse) {
        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'GetReturnReasonListResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
                $outResult['Data'] = $result->ResultList->B2CReturnReasonResult;
                return $outResult;
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    public function GetReturnReasonList() {

        $APILogService = new EcommerceAPILogService();

        $dataForSendByAPI = $this->makeGetReturnReasonListRequest();
        $APILogService->GetReturnReasonListRequest($dataForSendByAPI);

        $apiResponse = $this->api->GetReturnReasonList($dataForSendByAPI);
        $dataForSendByAPIResponse = $this->parseGetReturnReasonListResponse($apiResponse);
        $APILogService->GetReturnReasonListResponse($dataForSendByAPIResponse);
        return $dataForSendByAPIResponse;
    }


    public function makeGetReturnReasonProcessListRequest() {
        return [];
    }
    public function parseGetReturnReasonProcessListResponse($aApiResponse) {
        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'GetReturnReasonProcessListResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
                $outResult['Data'] = $result->ResultList->B2CReturnReasonProcessResult;
                return $outResult;
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    public function GetReturnReasonProcessList() {
        $APILogService = new EcommerceAPILogService();

        $dataForSendByAPI = $this->makeGetReturnReasonProcessListRequest();
        $APILogService->GetReturnReasonProcessListRequest($dataForSendByAPI);

        $apiResponse = $this->api->GetReturnReasonProcessList($dataForSendByAPI);
        $dataForSendByAPIResponse = $this->parseGetReturnReasonProcessListResponse($apiResponse);
        $APILogService->GetReturnReasonProcessListResponse($dataForSendByAPIResponse);
        return $dataForSendByAPIResponse;
    }

    public function makeGetShipmentForReturnRequest($aSearch,$aType) {

        $outResult = $this->_outResult;
        if(empty($aSearch) || empty($aType)) {
            $outResult['ErrorMessage'] = 'Нет данных для отсылки по АПИ';
            return $outResult;
        }

        $params = [];
        $params['request'] = [
            'Search'=>$aSearch,
            'Type'=>$aType, // Shipment or CargoReturnCode
        ];

        return $params;
    }

    public function parseGetShipmentForReturnResponse($aApiResponse) {

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'GetShipmentForReturnResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                $outResult['HasError'] = false;
                $outResult['Data'] = ArrayHelper::getValue($result, 'Data');
                return $outResult;
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    public function GetShipmentForReturn($aSearch,$aType) {
        $APILogService = new EcommerceAPILogService();

        $dataForSendByAPI = $this->makeGetShipmentForReturnRequest($aSearch,$aType);
        $APILogService->GetShipmentForReturnRequest($dataForSendByAPI);

        $apiResponse = $this->api->GetShipmentForReturn($dataForSendByAPI);
        $dataForSendByAPIResponse = $this->parseGetShipmentForReturnResponse($apiResponse);
        $APILogService->GetShipmentForReturnResponse($dataForSendByAPIResponse);
        return $dataForSendByAPIResponse;
    }

    /// OLD /////////////////////////////////////////////////////////////

    public function makeReturnShipmentRequest($aB2CReturnShipment) {

        $outResult = $this->_outResult;
        if(empty($aB2CReturnShipment)) {
            $outResult['ErrorMessage'] = 'Нет данных для отсылки по АПИ';
            return $outResult;
        }

        $B2CReturnShipmentItems = [];
        foreach($aB2CReturnShipment['items'] as $item) {
            $B2CReturnShipmentItems [] = [
                'SkuId'=> $item['SkuId'],
                'Quantity'=> $item['Quantity'],
                'ReturnReasonCode'=> $item['ReturnReasonCode'],
                'ReturnReasonProcessCode'=> $item['ReturnReasonProcessCode'],
            ];
        }

        $params = [];

        $params['request'] = [
                'BusinessUnitId'=> $this->api->BUSINESS_UNIT_ID(),
                'ExternalShipmentId'=>$aB2CReturnShipment['ExternalShipmentId'], //'OMC-8186455',
                'UniqueNumber'=>$aB2CReturnShipment['UniqueNumber'], // ? ,
                'CargoReturnCode'=>$aB2CReturnShipment['CargoReturnCode'], // ? ,
                'RefundUser'=>$aB2CReturnShipment['RefundUser'], // ? ,
                'RefundUserId'=>$aB2CReturnShipment['RefundUserId'], // ? ,
                'B2CReturnShipmentItems'=>$B2CReturnShipmentItems
        ];

        return $params;
    }

    private function parseReturnShipment($aApiResponse) {

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'ReturnShipmentResult')) {
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
    * Отправляем возвраты
    * */
    public function send($aB2CInBoundFeedBack,$aReturnId)
    {
        $APILogService = new EcommerceAPILogService();

        $dataForSendByAPI = $this->makeReturnShipmentRequest($aB2CInBoundFeedBack);
        $APILogService->ReturnShipmentRequest($aReturnId,$dataForSendByAPI);

        $apiResponse = $this->api->ReturnShipment($dataForSendByAPI);
        $dataForSendByAPIResponse = $this->parseReturnShipment($apiResponse);
        $APILogService->ReturnShipmentResponse($dataForSendByAPIResponse);
        return $dataForSendByAPIResponse;
    }
}