<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 10.10.2019
 * Time: 13:21
 */
namespace common\ecommerce\defacto\barcodeManager\service;

use common\ecommerce\defacto\api\service\EcommerceAPILogService;
use yii\helpers\ArrayHelper;

class MasterDataAPIService
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

    public function GetMasterData( $ShortCode = '', $SkuId = '', $LotOrSingleBarcode = '')
    {
        $requestParams = $this->GetMasterDataRequest($ShortCode,$SkuId,$LotOrSingleBarcode);
        $APILogService = new EcommerceAPILogService();
        $APILogService->GetMasterDataRequest($requestParams);

        $apiResponse = $this->api->GetMasterData($requestParams);
        $preparedApiResponse = $this->GetMasterDataResponse($apiResponse);

        $APILogService->GetMasterDataResponse($preparedApiResponse);

        return $preparedApiResponse;
    }

    private function GetMasterDataRequest( $ShortCode = '', $SkuId = '', $LotOrSingleBarcode = '') {
        $params = [];
        $params['request'] = [
            'BusinessUnitId' => $this->api->BUSINESS_UNIT_ID(),
            'ProcessRequestedB2CDataType' => 'Full',
            'CountAllItems' => false,
            'PageIndex' => 0,
            'PageSize' => 0,
        ];

        if($ShortCode) {
            $params['request']['ShortCode'] = $ShortCode;
        }
        if($SkuId) {
            $params['request']['SkuId'] = $SkuId;
        }
        if($LotOrSingleBarcode) {
            $params['request']['LotOrSingleBarcode'] = $LotOrSingleBarcode;
        }

        return $params;
    }

    private function GetMasterDataResponse( $apiResponse)
    {
        $outResult = $this->_outResult;
        if($result = @ArrayHelper::getValue($apiResponse['response'],'GetMasterDataResult')) {
            if (ArrayHelper::getValue($result, 'HasError') === false) {
                if($data = @ArrayHelper::getValue($result,'Data.B2CMasterData')) {
//                    $resultDataArray = count($data) <=1 ? [] : $data;
                    $outResult['HasError'] = false;
                    $outResult['Data'] = [
                        'SkuId'=> @ArrayHelper::getValue($data,'SkuId')
                    ];

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
}