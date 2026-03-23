<?php
namespace common\ecommerce\defacto\inbound\service;

use common\ecommerce\entities\EcommerceGetLotContentRequest;
use common\ecommerce\entities\EcommerceGetLotContentResponse;
use yii\helpers\ArrayHelper;

class GetLotContentResponseService
{
    public static function save($aResponse,$aRequest,$aOurInboundId) {

        $id =  ArrayHelper::getValue($aRequest, 'id');

        $isExistResponse = EcommerceGetLotContentResponse::find()->andWhere(['our_inbound_id' => $aOurInboundId,'get_lot_content_id'=>$id])->exists();
        if($isExistResponse) {
            return true;
        }

        if($aResponse['HasError']) {
            $response = new EcommerceGetLotContentResponse();
            $response->our_inbound_id = $aOurInboundId;
            $response->get_lot_content_id = $id;
            $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
            $response->save(false);
            return true;
        }

        foreach($aResponse['Data'] as $box) {
            $response = new EcommerceGetLotContentResponse();
            $response->our_inbound_id = $aOurInboundId;
            $response->get_lot_content_id = $id;
            $response->LotBarcode = ArrayHelper::getValue($box, 'LotBarcode');
            $response->ProductBarcode = ArrayHelper::getValue($box, 'ProductBarcode');
            $response->Quantity = ArrayHelper::getValue($box, 'Quantity');
            $response->save(false);
        }
        return true;
    }
}