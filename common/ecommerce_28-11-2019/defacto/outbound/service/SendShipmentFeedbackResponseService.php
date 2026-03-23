<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 15.09.2019
 * Time: 14:45
 */

namespace common\ecommerce\defacto\outbound\service;


use common\ecommerce\entities\EcommerceSendShipmentFeedbackResponse;
use yii\helpers\ArrayHelper;

class SendShipmentFeedbackResponseService
{
    public static function save($aResponse,$responseKey,$aOurInboundId) {

//        $id =  ArrayHelper::getValue($responseInDb, 'id');

//        $isExistResponse = EcommerceSendShipmentFeedbackResponse::find()->andWhere(['send_inbound_feedback_data_id'=>$id])->exists();
//        if($isExistResponse) {
//            return true;
//        }

        if($aResponse['HasError']) {
            $response = new EcommerceSendShipmentFeedbackResponse();
            $response->our_outbound_id = $aOurInboundId;
            $response->send_shipment_feedback_id = $responseKey;
            $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
            $response->save(false);
            return true;
        }

        $response = new EcommerceSendShipmentFeedbackResponse();
        $response->our_outbound_id = $aOurInboundId;
        $response->send_shipment_feedback_id = $responseKey;
        $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
        $response->IsSuccess = (int)ArrayHelper::getValue($aResponse, 'Data.IsSuccess');
        $response->save(false);

        return true;
    }
}