<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 14.09.2019
 * Time: 14:05
 */

namespace common\ecommerce\defacto\outbound\service;


use common\ecommerce\entities\EcommerceGetCargoLabelResponse;
use yii\helpers\ArrayHelper;

class GetCargoLabelResponseService
{
    public static function save($aResponse,$aRequest,$ourOutboundId) {

        $id =  ArrayHelper::getValue($aRequest, 'id');

        if($aResponse['HasError']) {
            $response = new EcommerceGetCargoLabelResponse();
            $response->our_outbound_id = $ourOutboundId;
            $response->cargo_label_request_id = $id;
            $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
            $response->save(false);
            return true;
        }

        if(empty($aResponse['Data'])) {
            $response = new EcommerceGetCargoLabelResponse();
            $response->our_outbound_id = $ourOutboundId;
            $response->cargo_label_request_id = $id;
            $response->error_message = 'Нет данных для сохранения';
            $response->save(false);
            return true;
        }

        foreach($aResponse['Data'] as $order) {
            $response = new EcommerceGetCargoLabelResponse();
            $response->our_outbound_id = $ourOutboundId;
            $response->cargo_label_request_id = $id;
            $response->ExternalShipmentId = ArrayHelper::getValue($order, 'ExternalShipmentId');
            $response->ShipmentId = ArrayHelper::getValue($order, 'ShipmentId');
            $response->FileExtension = ArrayHelper::getValue($order, 'FileExtension');
            $response->FileData = ArrayHelper::getValue($order, 'FileData');
            $response->TrackingNumber = ArrayHelper::getValue($order, 'TrackingNumber');
            $response->TrackingUrl = ArrayHelper::getValue($order, 'TrackingUrl');
            $response->ReferenceNumber = ArrayHelper::getValue($order, 'ReferenceNumber');
            $response->PageSize = ArrayHelper::getValue($order, 'PageSize');
            $response->save(false);
        }
        return true;
    }
}
