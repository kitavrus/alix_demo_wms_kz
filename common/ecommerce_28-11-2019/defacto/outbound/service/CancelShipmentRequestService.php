<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 14.09.2019
 * Time: 13:50
 */

namespace common\ecommerce\defacto\outbound\service;


use common\ecommerce\entities\EcommerceCancelShipmentRequest;
use yii\helpers\ArrayHelper;

class CancelShipmentRequestService
{
    public static function save($aRequest,$ourOutboundId) {

        $businessUnitId = ArrayHelper::getValue($aRequest,'request.BusinessUnitId');
        $shipmentId = ArrayHelper::getValue($aRequest,'request.ShipmentId');

        $request = new EcommerceCancelShipmentRequest();
        $request->our_outbound_id = $ourOutboundId;
        $request->BusinessUnitId = $businessUnitId;
        $request->ShipmentId = $shipmentId;
        $request->save(false);

        return $request;
    }
}