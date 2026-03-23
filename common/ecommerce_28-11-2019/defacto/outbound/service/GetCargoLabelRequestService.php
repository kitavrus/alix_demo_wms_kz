<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 14.09.2019
 * Time: 13:56
 */
namespace common\ecommerce\defacto\outbound\service;

use common\ecommerce\entities\EcommerceGetCargoLabelRequest;
use yii\helpers\ArrayHelper;

class GetCargoLabelRequestService
{
    public static function save($aRequest,$ourOutboundId) {

        $externalShipmentId = ArrayHelper::getValue($aRequest,'request.ExternalShipmentId');
        $volumetricWeight = ArrayHelper::getValue($aRequest,'request.VolumetricWeight');
        $cargoCompany = ArrayHelper::getValue($aRequest,'request.CargoCompany');
        $packageId = ArrayHelper::getValue($aRequest,'request.PackageId');
        $skuId = ArrayHelper::getValue($aRequest,'request.ShipmentItemInfos.Item.0.SkuId');
        $quantity = ArrayHelper::getValue($aRequest,'request.ShipmentItemInfos.Item.0.Quantity');

        $request = new EcommerceGetCargoLabelRequest();
        $request->our_outbound_id = $ourOutboundId;
        $request->ExternalShipmentId = $externalShipmentId;
        $request->VolumetricWeight = $volumetricWeight;
        $request->CargoCompany = $cargoCompany;
        $request->PackageId = $packageId;
        $request->SkuId = $skuId;
        $request->Quantity = $quantity;
        $request->save(false);

        return $request;
    }
}