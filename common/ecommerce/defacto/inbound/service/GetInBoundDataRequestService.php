<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 31.08.2019
 * Time: 19:11
 */
namespace common\ecommerce\defacto\inbound\service;

use common\ecommerce\entities\EcommerceGetInBoundDataRequest;
use yii\helpers\ArrayHelper;

class GetInBoundDataRequestService
{
    public static function save($aRequest,$aInboundId) {

        $businessUnitId = ArrayHelper::getValue($aRequest,'request.BusinessUnitId');
        $lcBarcode = ArrayHelper::getValue($aRequest,'request.LcBarcode');

        $request = EcommerceGetInBoundDataRequest::find()->andWhere(['our_inbound_id'=>$aInboundId,'BusinessUnitId'=>$businessUnitId,'LcBarcode'=>$lcBarcode])->one();
        if($request) {
           return $request;
        }

        $request = new EcommerceGetInBoundDataRequest();
        $request->our_inbound_id = $aInboundId;
        $request->BusinessUnitId = $businessUnitId;
        $request->LcBarcode = $lcBarcode;
        $request->save(false);

        return $request;
    }
}