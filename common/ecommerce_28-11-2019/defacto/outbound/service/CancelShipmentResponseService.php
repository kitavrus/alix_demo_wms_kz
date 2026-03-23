<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 14.09.2019
 * Time: 13:54
 */

namespace common\ecommerce\defacto\outbound\service;


use common\ecommerce\entities\EcommerceCancelShipmentResponse;
use yii\helpers\ArrayHelper;

class CancelShipmentResponseService
{
    public static function save($aResponse,$aRequest,$ourOutboundId) {

        $id =  ArrayHelper::getValue($aRequest, 'id');

        if($aResponse['HasError']) {
            $response = new EcommerceCancelShipmentResponse();
            $response->our_outbound_id = $ourOutboundId;
            $response->cancel_shipment_request_id = $id;
            $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
            $response->save(false);
            return true;
        }

        if(empty($aResponse['Data'])) {
            $response = new EcommerceCancelShipmentResponse();
            $response->our_outbound_id = $ourOutboundId;
            $response->cancel_shipment_request_id = $id;
            $response->error_message = 'Нет данных для сохранения';
            $response->save(false);
            return true;
        }

//        foreach($aResponse['Data'] as $order) {
            $response = new EcommerceCancelShipmentResponse();
            $response->our_outbound_id = $ourOutboundId;
            $response->cancel_shipment_request_id = $id;
            $response->IsSuccess = ArrayHelper::getValue($aResponse, 'Data.IsSuccess');
            $response->save(false);
//        }
        return true;
    }
}