<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 09.09.2019
 * Time: 10:28
 */
namespace common\ecommerce\defacto\outbound\service;

use common\ecommerce\entities\EcommerceGetShipmentsRequest;
use yii\helpers\ArrayHelper;

class GetShipmentsRequestService
{
    public static function save($aRequest) {

        $businessUnitId = ArrayHelper::getValue($aRequest,'request.BusinessUnitId');
        $OrderQuantity = ArrayHelper::getValue($aRequest,'request.OrderQuantity');

//        $request = EcommerceSendShipmentFeedbackRequest::find()->andWhere(['our_inbound_id'=>$aInboundId,'BusinessUnitId'=>$businessUnitId,'LcBarcode'=>$lcBarcode])->one();
//        if($request) {
//            return $request;
//        }

        $request = new EcommerceGetShipmentsRequest();
        $request->BusinessUnitId = $businessUnitId;
        $request->OrderQuantity = $OrderQuantity;
        $request->save(false);

        return $request;
    }
}