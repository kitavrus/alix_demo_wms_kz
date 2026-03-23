<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 14.09.2019
 * Time: 14:10
 */

namespace common\ecommerce\defacto\outbound\service;


use common\ecommerce\entities\EcommerceStockAdjustmentRequest;
use yii\helpers\ArrayHelper;

class StockAdjustmentRequestService
{
    public static function save($aRequest,$ourOutboundId) {

        $businessUnitId = ArrayHelper::getValue($aRequest,'request.BusinessUnitId');
        $lotOrSingleBarcode = ArrayHelper::getValue($aRequest,'request.LotOrSingleBarcode');
        $quantity = ArrayHelper::getValue($aRequest,'request.Quantity');
        $operator = ArrayHelper::getValue($aRequest,'request.Operator');

        $request = new EcommerceStockAdjustmentRequest();
        $request->BusinessUnitId = $businessUnitId;
        $request->LotOrSingleBarcode = $lotOrSingleBarcode;
        $request->Quantity = $quantity;
        $request->Operator = $operator;
        $request->save(false);

        return $request;
    }
}