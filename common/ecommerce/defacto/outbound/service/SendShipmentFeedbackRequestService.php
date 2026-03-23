<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 15.09.2019
 * Time: 14:43
 */

namespace common\ecommerce\defacto\outbound\service;


use common\ecommerce\entities\EcommerceSendShipmentFeedbackRequest;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class SendShipmentFeedbackRequestService
{
    public static function save($aRequest, $aOurInboundId)
    {

        $B2CInBoundFeedBackItems = ArrayHelper::getValue($aRequest, 'request.B2CShipmentFeedBackList.B2CShipmentFeedBackDto');


//        VarDumper::dump($aRequest,10,true);
//        VarDumper::dump($B2CInBoundFeedBackItems,10,true);
//        die;
        foreach ($B2CInBoundFeedBackItems as $B2CInBoundFeedBack) {

            $BusinessUnitId = ArrayHelper::getValue($B2CInBoundFeedBack, 'BusinessUnitId');
            $ShipmentId = ArrayHelper::getValue($B2CInBoundFeedBack, 'ExternalShipmentId');
            $SkuId = ArrayHelper::getValue($B2CInBoundFeedBack, 'SkuId');
            $SkuBarcode = ArrayHelper::getValue($B2CInBoundFeedBack, 'SkuBarcode');
            $Quantity = ArrayHelper::getValue($B2CInBoundFeedBack, 'Quantity');
            $WaybillSerial = ArrayHelper::getValue($B2CInBoundFeedBack, 'WaybillSerial');
            $WaybillNumber = ArrayHelper::getValue($B2CInBoundFeedBack, 'WaybillNumber');

            $request = new EcommerceSendShipmentFeedbackRequest();
            $request->our_outbound_id = $aOurInboundId;
            $request->BusinessUnitId = $BusinessUnitId;
            $request->ShipmentId = $ShipmentId;
            $request->SkuId = $SkuId;
            $request->SkuBarcode = $SkuBarcode;
            $request->Quantity = $Quantity;
            $request->WaybillSerial = $WaybillSerial;
            $request->WaybillNumber = $WaybillNumber;
            $request->save(false);
        }

        return;
    }
}