<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 11.09.2019
 * Time: 17:46
 */
namespace common\ecommerce\defacto\barcodeManager\service;


use common\ecommerce\entities\EcommerceInboundPlaceBarcode;
use common\ecommerce\entities\EcommerceOutboundPackageBarcode;

class BarcodeService
//class OutboundPackageBarcodeService
{
    const MAX_LENGTH_OUR_BOX_BARCODE = 13;
    const MIN_LENGTH_OUR_BOX_BARCODE = 9;

    const MAX_LENGTH_CLIENT_BOX_BARCODE = 13;
    const MIN_LENGTH_CLIENT_BOX_BARCODE = 12;

    const MAX_LENGTH_PRODUCT_BARCODE = 13;
    const MIN_LENGTH_PRODUCT_BARCODE = 13;

    public static function createOutbound($barcode) {
        $packageBarcode = new EcommerceOutboundPackageBarcode();
        $packageBarcode->barcode = $barcode;
        $packageBarcode->save(false);
    }

    public static function createInbound($barcode) {
        $placeBarcode = new EcommerceInboundPlaceBarcode();
        $placeBarcode->barcode = $barcode;
        $placeBarcode->save(false);
    }

    public function isExist($barcode) {
        return EcommerceOutboundPackageBarcode::find()->andWhere(['barcode'=>$barcode])->exists();
    }

    public function isLotBarcode($lotBarcode) {
        $startBarcodeList = [
            "230"
        ];
        return in_array($this->subStr($lotBarcode), $startBarcodeList);
    }

    public function isDefactoBoxBarcode($lcBarcode)
    {
        $startBarcodeList = [
            "243","486","233",'141','142',
            "143","144","145",'146','147',
            "148",
        ];
        return in_array($this->subStr($lcBarcode),$startBarcodeList) && $this->checkClientBarcodeSize($lcBarcode);
    }

    public function isDefactoProductBarcode($productBarcode)
    {
        $startBarcodeList = ["868",'869','900'];
        return in_array($this->subStr($productBarcode), $startBarcodeList) && $this->checkProductBarcodeSize($productBarcode);
    }

    /**
     * @param $aBoxBarcode
     * @return bool
     */
//    public function isOurBoxBarcode($aBoxBarcode) {
    public function isOurInboundBoxBarcode($aBoxBarcode) {
        $prefix = $this->subStr($aBoxBarcode);
        return (!empty($prefix) && $prefix == '100' && (strlen($aBoxBarcode) <= self::MAX_LENGTH_OUR_BOX_BARCODE && strlen($aBoxBarcode) >= self::MIN_LENGTH_OUR_BOX_BARCODE));
    }
    /**
     * @param $aBoxBarcode
     * @return bool
     */
    public function isOurOutboundBoxBarcode($aBoxBarcode) {
        $prefix = $this->subStr($aBoxBarcode);
        return (!empty($prefix) && $prefix == '900' && (strlen($aBoxBarcode) <= self::MAX_LENGTH_OUR_BOX_BARCODE && strlen($aBoxBarcode) >= self::MIN_LENGTH_OUR_BOX_BARCODE ));
    }

    public function subStr($str,$start = 0,$length = 3) {
       return substr(trim($str), $start, $length);
    }

    public static function onlyDigital($str) {
       return preg_replace('/[^0-9]/', '',$str);
    }

    public static function trim($str) {
       return preg_replace('/\s/', '', $str);
    }

    public function checkProductBarcodeSize($aProductBarcode) {
        return (strlen($aProductBarcode) <= self::MAX_LENGTH_PRODUCT_BARCODE && strlen($aProductBarcode) >= self::MIN_LENGTH_PRODUCT_BARCODE);
    }

    public function checkClientBarcodeSize($aBoxBarcode) {
        return (strlen($aBoxBarcode) <= self::MAX_LENGTH_CLIENT_BOX_BARCODE && strlen($aBoxBarcode) >= self::MIN_LENGTH_CLIENT_BOX_BARCODE);
    }

}