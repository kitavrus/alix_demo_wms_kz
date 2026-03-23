<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 14.09.2019
 * Time: 14:13
 */

namespace common\ecommerce\defacto\outbound\service;


use common\ecommerce\entities\EcommerceStockAdjustmentResponse;
use yii\helpers\ArrayHelper;

class StockAdjustmentResponseService
{
    public static function save($aResponse,$aRequest) {

        $id =  ArrayHelper::getValue($aRequest, 'id');

        if($aResponse['HasError']) {
            $response = new EcommerceStockAdjustmentResponse();
            $response->stock_adjustment_request_id = $id;
            $response->error_message = ArrayHelper::getValue($aResponse, 'ErrorMessage');
            $response->save(false);
            return true;
        }

        if(empty($aResponse['Data'])) {
            $response = new EcommerceStockAdjustmentResponse();
            $response->stock_adjustment_request_id = $id;
            $response->error_message = 'Нет данных для сохранения';
            $response->save(false);
            return true;
        }

        foreach($aResponse['Data'] as $order) {
            $response = new EcommerceStockAdjustmentResponse();
            $response->stock_adjustment_request_id = $id;
            $response->IsSuccess = ArrayHelper::getValue($order, 'IsSuccess');
            $response->save(false);
        }
        return true;
    }
}