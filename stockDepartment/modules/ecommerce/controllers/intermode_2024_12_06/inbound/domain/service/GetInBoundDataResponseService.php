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
use yii\helpers\ArrayHelper;

class GetInBoundDataResponseService
{
    public static function save($aResponse,$aRequestInDB,$aOurInboundId) {

        $id =  ArrayHelper::getValue($aRequestInDB, 'id');

        $isExistResponse = EcommerceGetInBoundDataResponse::find()->andWhere(['our_inbound_id'=>$aOurInboundId,'get_inbound_data_id'=>$id])->exists();
        if($isExistResponse) {
            return true;
        }

        if($aResponse['HasError']) {
            $response = new EcommerceGetInBoundDataResponse();
            $response->our_inbound_id = $aOurInboundId;
            $response->get_inbound_data_id = $id;
            $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
            $response->save(false);
           return true;
        }

        foreach($aResponse['Data'] as $box) {
            $response = new EcommerceGetInBoundDataResponse();
            $response->our_inbound_id = $aOurInboundId;
            $response->get_inbound_data_id = $id;
            $response->InboundId = ArrayHelper::getValue($box, 'InboundId');
            $response->FromBusinessUnitId = ArrayHelper::getValue($box, 'FromBusinessUnitId');
            $response->LcOrCartonLabel = ArrayHelper::getValue($box, 'LcOrCartonLabel');
            $response->NumberOfCartons = ArrayHelper::getValue($box, 'NumberOfCartons');
            $response->SkuId = ArrayHelper::getValue($box, 'SkuId');
            $response->LotOrSingleBarcode = ArrayHelper::getValue($box, 'LotOrSingleBarcode');
            $response->LotOrSingleQuantity = ArrayHelper::getValue($box, 'LotOrSingleQuantity');
            $response->Status = ArrayHelper::getValue($box, 'Status');
            $response->ToBusinessUnitId = ArrayHelper::getValue($box, 'ToBusinessUnitId');
            $response->save(false);
        }
        return true;
    }
}