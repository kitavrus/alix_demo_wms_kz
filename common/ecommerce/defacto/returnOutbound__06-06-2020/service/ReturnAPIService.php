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

    public function makeReturnShipmentRequest($aB2CReturnShipment) {

        $outResult = $this->_outResult;
        if(empty($aB2CReturnShipment)) {
            $outResult['ErrorMessage'] = 'Нет данных для отсылки по АПИ';
            return $outResult;
        }

        $params = [];
        $params['request'] = [
                'BusinessUnitId'=> $this->api->BUSINESS_UNIT_ID(),
                'ExternalShipmentId'=>$aB2CReturnShipment['ExternalShipmentId'], //'OMC-8186455',
                'SkuBarcode'=>$aB2CReturnShipment['SkuBarcode'], //'2430007688586',
                'Quantity'=>$aB2CReturnShipment['Quantity'], //'1',
                'ReturnProcessType'=>$aB2CReturnShipment['ReturnProcess'], //'FirsQuality or Donation',
                'ReturnReason'=>'?',
				'IsVirtual'=>0,
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