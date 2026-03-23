<?php
namespace common\ecommerce\defacto\inbound\service;

use common\ecommerce\entities\EcommerceGetLotContentRequest;
use common\ecommerce\entities\EcommerceGetLotContentResponse;
use yii\helpers\ArrayHelper;

class GetLotContentRequestService
{
    public static function save($aRequest,$aOurInboundId) {

        $businessUnitId = ArrayHelper::getValue($aRequest,'request.BusinessUnitId');
        $lotBarcodes = ArrayHelper::getValue($aRequest,'request.LotBarcodes');

        $lotBarcode = array_shift($lotBarcodes);

        $request = EcommerceGetLotContentRequest::find()->andWhere(['our_inbound_id'=>$aOurInboundId,'BusinessUnitId'=>$businessUnitId,'LotBarcode'=>$lotBarcode])->one();
        if($request) {
            return $request;
        }

        $request = new EcommerceGetLotContentRequest();
        $request->our_inbound_id = $aOurInboundId;
        $request->BusinessUnitId = $businessUnitId;
        $request->LotBarcode = $lotBarcode;
        $request->save(false);

        return $request;
    }
}