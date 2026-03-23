<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 09.09.2019
 * Time: 10:28
 */
namespace common\ecommerce\defacto\outbound\service;

use common\ecommerce\entities\EcommerceGetShipmentsResponse;
use yii\helpers\ArrayHelper;

class GetShipmentsResponseService
{
    public function save($aResponse,$aRequest) {

        $id =  ArrayHelper::getValue($aRequest, 'id');

        if($aResponse['HasError']) {
            $response = new EcommerceGetShipmentsResponse();
            $response->get_shipments_request_id = $id;
            $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
            $response->save(false);
            return [$response];
        }

        if(empty($aResponse['Data'])) {
            $response = new EcommerceGetShipmentsResponse();
            $response->get_shipments_request_id = $id;
            $response->error_message = 'Нет данных для сохранения';
            $response->save(false);
            return [$response];
        }
        $orderList = [];
        foreach($aResponse['Data'] as $order) {
            $response = new EcommerceGetShipmentsResponse();
            $response->get_shipments_request_id = $id;
            $response->ShipmentId = ArrayHelper::getValue($order, 'ExternalShipmentId');
            $response->ExternalShipmentNo = @ArrayHelper::getValue($order, 'ExternalShipmentNo');
            $response->ShipmentType = ArrayHelper::getValue($order, 'ShipmentType');
            $response->ShipmentSource = ArrayHelper::getValue($order, 'ShipmentSource');
//            $response->ShipmentDate = ArrayHelper::getValue($order, 'ShipmentDate');
            $response->Priority = ArrayHelper::getValue($order, 'Priority');
            $response->CustomerName = ArrayHelper::getValue($order, 'CustomerName');
            $response->ShippingAddress = ArrayHelper::getValue($order, 'ShippingAddress');
            $response->ShippingCountryCode = ArrayHelper::getValue($order, 'ShippingCountryCode');
            $response->ShippingCity = ArrayHelper::getValue($order, 'ShippingCity');
            $response->ShippingCounty = ArrayHelper::getValue($order, 'ShippingCounty');
            $response->ShippingZipCode = ArrayHelper::getValue($order, 'ShippingZipCode');
            $response->ShippingEmail = ArrayHelper::getValue($order, 'ShippingEmail');
            $response->ShippingPhone = ArrayHelper::getValue($order, 'ShippingPhone');
            $response->Destination = ArrayHelper::getValue($order, 'Destination');
            $response->CourierCompany = ArrayHelper::getValue($order, 'CourierCompany');
            $response->FromBusinessUnitId = ArrayHelper::getValue($order, 'FromBusinessUnitId');
            $response->CacStoreID = ArrayHelper::getValue($order, 'CacStoreID');
            $response->PartyApprovalId = ArrayHelper::getValue($order, 'PartyApprovalId');
            $response->PackMessage = @ArrayHelper::getValue($order, 'PackMessage');
            $response->IsGiftWrapping = ArrayHelper::getValue($order, 'IsGiftWrapping');
            $response->GiftWrappingMessage = @ArrayHelper::getValue($order, 'GiftWrappingMessage');
            $response->Ek1 = @ArrayHelper::getValue($order, 'Ek1');
            $response->Ek2 = @ArrayHelper::getValue($order, 'Ek2');
            $response->Ek3 = @ArrayHelper::getValue($order, 'Ek3');
            $response->B2CShipmentDetailList = serialize(@ArrayHelper::getValue($order, 'B2CShipmentDetailList'));

            $response->save(false);

            $orderList[] = $response;
        }

        return $orderList;
    }
}