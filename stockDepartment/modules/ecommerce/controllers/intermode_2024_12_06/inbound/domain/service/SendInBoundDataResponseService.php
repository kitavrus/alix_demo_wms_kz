<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 31.08.2019
 * Time: 19:11
 */
namespace common\ecommerce\defacto\inbound\service;

use common\ecommerce\entities\EcommerceGetInBoundDataRequest;
use common\ecommerce\entities\EcommerceGetInBoundDataResponse;
use common\ecommerce\entities\EcommerceSendInBoundFeedbackDataResponse;
use yii\helpers\ArrayHelper;

class SendInBoundDataResponseService
{
    public static function save($aResponse,$responseInDb,$aOurInboundId) {

        $id =  ArrayHelper::getValue($responseInDb, 'id');

        $isExistResponse = EcommerceSendInBoundFeedbackDataResponse::find()->andWhere(['send_inbound_feedback_data_id'=>$id])->exists();
        if($isExistResponse) {
            return true;
        }

        if($aResponse['HasError']) {
            $response = new EcommerceSendInBoundFeedbackDataResponse();
            $response->our_inbound_id = $aOurInboundId;
            $response->send_inbound_feedback_data_id = $id;
            $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
            $response->save(false);
           return true;
        }

        $response = new EcommerceSendInBoundFeedbackDataResponse();
        $response->our_inbound_id = $aOurInboundId;
        $response->send_inbound_feedback_data_id = $id;
        $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
        $response->IsSuccess = (int)ArrayHelper::getValue($aResponse, 'Data.IsSuccess');
        $response->save(false);

        return true;
    }
}