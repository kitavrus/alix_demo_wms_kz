<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 31.08.2019
 * Time: 19:11
 */
namespace common\ecommerce\defacto\inbound\service;

use common\ecommerce\entities\EcommerceSendInBoundFeedbackDataRequest;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class SendInBoundDataRequestService
{
    public static function save($aRequest, $aOurInboundId)
    {
        $B2CInBoundFeedBack = ArrayHelper::getValue($aRequest, 'request.FeedBackData.B2CInBoundFeedBack.0');

//        VarDumper::dump($B2CInBoundFeedBack,10,true);

        $InboundId = ArrayHelper::getValue($B2CInBoundFeedBack, 'InboundId');
        $LcOrCartonBarcode = ArrayHelper::getValue($B2CInBoundFeedBack, 'LcOrCartonBarcode');
        $ProductBarcode = ArrayHelper::getValue($B2CInBoundFeedBack, 'ProductBarcode');
        $ProductQuantity = ArrayHelper::getValue($B2CInBoundFeedBack, 'ProductQuantity');
        $ProductDamaged = ArrayHelper::getValue($B2CInBoundFeedBack, 'ProductDamaged');

        $request = EcommerceSendInBoundFeedbackDataRequest::find()->andWhere(
            [
                'InboundId' => $InboundId,
                'LcOrCartonBarcode' => $LcOrCartonBarcode,
                'ProductBarcode' => $ProductBarcode,
                'ProductQuantity' => $ProductQuantity,
                'ProductDamaged' => $ProductDamaged,
            ])->one();

        if ($request) {
            return $request;
        }

        $request = new EcommerceSendInBoundFeedbackDataRequest();
        $request->our_inbound_id = $aOurInboundId;
        $request->InboundId = $InboundId;
        $request->LcOrCartonBarcode = $LcOrCartonBarcode;
        $request->ProductBarcode = $ProductBarcode;
        $request->ProductQuantity = $ProductQuantity;
        $request->ProductDamaged = (int)$ProductDamaged;
        $request->save(false);

        return $request;
    }
}