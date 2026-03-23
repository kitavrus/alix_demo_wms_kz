<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 26.08.2019
 * Time: 9:38
 */
namespace common\ecommerce\defacto\inbound\service;


use common\ecommerce\defacto\api\service\EcommerceAPILogService;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class InboundAPIService
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
        // $this->api = new \common\ecommerce\defacto\api\ECommerceAPI();
         $this->api = new \common\ecommerce\defacto\api\ECommerceAPINew();
    }

    private function makeGetInBoundDataRequest($aLcBarcode) {
        $params = [];
        $params['request'] = [
            'BusinessUnitId' =>  $this->api->BUSINESS_UNIT_ID(),
            'LcBarcode' =>$aLcBarcode,
        ];

        return $params;
    }

    private function makeGetLotContentRequest($aLotBarcode) {
        $params = [];
        $params['request'] = [
            'BusinessUnitId' =>  $this->api->BUSINESS_UNIT_ID(),
            'LotBarcodes' =>[
                $aLotBarcode
            ],
        ];

        return $params;
    }

    public function get($aLcBarcode,$aOurInboundId) {

        $inboundDataRequest = $this->makeGetInBoundDataRequest($aLcBarcode);

        $APILogService = new EcommerceAPILogService();
        $APILogService->GetInBoundDataRequest($aOurInboundId,$inboundDataRequest);

        $inBoundData = $this->parseGetInBoundData($this->api->GetInBoundData($inboundDataRequest));
        $APILogService->GetInBoundDataResponse($inBoundData);

        foreach($inBoundData['Data'] as $boxInfo) {

            $dto = new \stdClass();
            $dto->ourInboundId = $aOurInboundId;
            $dto->clientInboundId = $boxInfo->InboundId;
            $dto->clientLcBarcode = $boxInfo->LcOrCartonLabel;
            $dto->clientProductSKU = $boxInfo->SkuId;
            $dto->clientProductBarcode = $boxInfo->LotOrSingleBarcode;
            $dto->clientProductQuantity = $boxInfo->LotOrSingleQuantity;

            InboundItemService::addProduct($dto);
        }

        return $inBoundData;
    }


    public function get_OLD($aLcBarcode,$aOurInboundId) {

        $inboundDataRequest = $this->makeGetInBoundDataRequest($aLcBarcode);

        $APILogService = new EcommerceAPILogService();
        $APILogService->GetInBoundDataRequest($aOurInboundId,$inboundDataRequest);

//        $inboundDataInDBRequest = $this->saveGetInBoundDataRequest($inboundDataRequest,$aOurInboundId);

        $inBoundData = $this->parseGetInBoundData($this->api->GetInBoundData($inboundDataRequest));
//        $this->saveGetInBoundDataResponse($inBoundData,$inboundDataInDBRequest,$aOurInboundId);
        $APILogService->GetInBoundDataResponse($inBoundData);

        foreach($inBoundData['Data'] as $lotBarcode) {

            $clientInboundId = $lotBarcode->InboundId;
            $clientLotSKU = $lotBarcode->SkuId;
			$lotOrSingleQuantity = $lotBarcode->LotOrSingleQuantity;
            $lotContentRequest = $this->makeGetLotContentRequest($lotBarcode->LotOrSingleBarcode);
//            $lotContentInDBRequest = $this->saveGetLotContentRequest($lotContentRequest,$aOurInboundId);

            $APILogService = new EcommerceAPILogService();
            $APILogService->GetLotContentRequest($aOurInboundId,$lotContentRequest);

            $lotContentData = $this->parseGetLotContent($this->api->GetLotContent($lotContentRequest));
//            $this->saveGetLotContentResponse($lotContentData, $lotContentInDBRequest,$aOurInboundId);
            $APILogService->GetLotContentResponse($lotContentData);

            if($lotContentData['HasError']) {
                $inBoundData['HasError'] = $lotContentData['HasError'];
                $inBoundData['ErrorMessage'] = $lotContentData['ErrorMessage'];
                return $inBoundData;
            }

            foreach($lotContentData['Data'] as $products) {
                $productList = count($products) <=1 ? [$products] : $products;
                InboundItemService::save($productList,$aOurInboundId,$aLcBarcode,$clientInboundId,$clientLotSKU,$lotOrSingleQuantity);
            }
        }

        return $inBoundData;
    }

//    private function saveInBoundItem($aRequest,$aInboundId) {
//        return GetInBoundDataRequestService::save($aRequest,$aInboundId);
//    }

//    private function saveGetInBoundDataRequest($aRequest,$aInboundId) {
//        return GetInBoundDataRequestService::save($aRequest,$aInboundId);
//    }

//    private function saveGetInBoundDataResponse($aResponse,$aRequestInDB,$aOurInboundId) {
//        return GetInBoundDataResponseService::save($aResponse,$aRequestInDB,$aOurInboundId);
//    }

//    private function saveGetLotContentRequest($aRequest,$aOurInboundId) {
//        return GetLotContentRequestService::save($aRequest,$aOurInboundId);
//    }

//    private function saveGetLotContentResponse($aResponse,$aRequestInDB,$aOurInboundId) {
//        return GetLotContentResponseService::save($aResponse,$aRequestInDB,$aOurInboundId);
//    }

    private function parseGetInBoundData($aApiResponse) {

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'GetInBoundDataResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if($data = @ArrayHelper::getValue($result,'Data.B2CInboundDto')) {
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

    private function parseGetLotContent($aApiResponse) {

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'GetLotContentResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if($data = @ArrayHelper::getValue($result,'ResultList.B2CLotContentDto')) {
                        $resultDataArray = count($data) <=1 ? [$data] : $data;
                        $outResult['HasError'] = false;
                        $outResult['Data'] = $resultDataArray;

                    return $outResult;
                } else {
                    $outResult['ErrorMessage'] = 'Дефакто вернул пустоту';
                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    public function makeSendInBoundFeedBackDataRequest($aB2CInBoundFeedBack) {

        $outResult = $this->_outResult;
        if(empty($aB2CInBoundFeedBack) || !is_array($aB2CInBoundFeedBack)) {
            $outResult['ErrorMessage'] = 'Нет данных для отсылки по АПИ';
            return $outResult;
        }

        $params = [];

        foreach($aB2CInBoundFeedBack as $row) {
            $params['request']['FeedBackData']['B2CInBoundFeedBack'][] = [
                    //'InboundId'=>$row['client_inbound_id'], //'6875494',
					'InboundId'=> ($row['client_inbound_id'] != 0 ? $row['client_inbound_id'] : null), //'6875494',
                    'LcOrCartonBarcode'=>$row['client_box_barcode'], //'2430007688586',
                    'ProductBarcode'=>$row['product_barcode'], //'8681991024729',
                    'ProductQuantity'=> $row['items'],
                    'ProductIsDamaged'=>$row['condition_type'] == 1 ? false : true , //false,
            ];
        }

        return $params;
    }

    private function parseSendInBoundFeedBackData($aApiResponse) {

        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($aApiResponse['response'],'SendInBoundFeedBackDataResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
//                if($data = ArrayHelper::getValue($result,'IsSuccess')) {
//                    $resultDataArray = count($data) <=1 ? [$data] : $data;
                    $outResult['HasError'] = false;
                    $outResult['Data'] = $result;
//                    $outResult['Data'] = $resultDataArray;

                    return $outResult;
//                }
            } else {
                $outResult['ErrorMessage'] = 'Ошибка на стороне апи дефакто.['.ArrayHelper::getValue($result, 'Error').']';
            }
        } else {
            $outResult['ErrorMessage'] = 'АПИ дефакто вернуло пустой результат';
        }

        return $outResult;
    }

    /*
    * Получаем приходы
    * */
    public function send($aB2CInBoundFeedBack,$aOurInboundId)
    {
//        $outResult = $this->_outResult;
//        foreach($aB2CInBoundFeedBack as $row) {
        $APILogService = new EcommerceAPILogService();

//        $dataForSendByAPI = $this->makeSendInBoundFeedBackDataRequest([$aB2CInBoundFeedBack]);
        $dataForSendByAPI = $this->makeSendInBoundFeedBackDataRequest($aB2CInBoundFeedBack);
//echo print_r($dataForSendByAPI,true);
//VarDumper::dump($dataForSendByAPI,10,true);
//        die('--send--');
//      $responseInDb = SendInBoundDataRequestService::save($dataForSendByAPI,$aOurInboundId);
        $APILogService->SendInBoundFeedBackDataRequest($aOurInboundId,$dataForSendByAPI);

        $apiResponse = $this->api->SendInBoundFeedBackData($dataForSendByAPI);
        $dataForSendByAPIResponse = $this->parseSendInBoundFeedBackData($apiResponse);

//      SendInBoundDataResponseService::save($dataForSendByAPIResponse,$responseInDb,$aOurInboundId);

        $APILogService->SendInBoundFeedBackDataResponse($dataForSendByAPIResponse);

//        }
        return $dataForSendByAPIResponse;
    }
}