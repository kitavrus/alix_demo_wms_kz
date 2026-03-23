<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 11.09.2019
 * Time: 17:46
 */
namespace app\modules\ecommerce\controllers\intermode\barcode\domain\service;


use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundPlaceBarcode;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutboundPackageBarcode;
use app\modules\ecommerce\controllers\intermode\stock\domain\entities\EcommerceStock;
use app\modules\ecommerce\controllers\intermode\stock\domain\constants\StockAvailability;
use yii\helpers\ArrayHelper;

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

//    public function isLotBarcode($lotBarcode) {
//        $startBarcodeList = [
//            "230"
//        ];
//        return in_array($this->subStr($lotBarcode), $startBarcodeList);
//    }

//    public function isDefactoBoxBarcode($lcBarcode)
//    {
//        $startBarcodeList = [
//            "243","486","233",'141','142',
//            "143","144","145",'146','147',
//            "148",
//        ];
//        return in_array($this->subStr($lcBarcode),$startBarcodeList) && $this->checkClientBarcodeSize($lcBarcode);
//    }

//    public function isDefactoProductBarcode($productBarcode)
//    {
//        $startBarcodeList = ["868",'869','900'];
//        return in_array($this->subStr($productBarcode), $startBarcodeList) && $this->checkProductBarcodeSize($productBarcode);
//    }

    /**
     * @param $aBoxBarcode
     * @return bool
     */
//    public function isOurBoxBarcode($aBoxBarcode) {
    public function isOurInboundBoxBarcode($aBoxBarcode) {
        $prefix = $this->subStr($aBoxBarcode);
        return (!empty($prefix) && $prefix == '100' && (strlen($aBoxBarcode) <= self::MAX_LENGTH_OUR_BOX_BARCODE && strlen($aBoxBarcode) >= self::MIN_LENGTH_OUR_BOX_BARCODE));// || $this->isOurOutboundBoxBarcode($aBoxBarcode);
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
	
	
    /*
* Is product
* @param string $barcode
* @return boolean
 * */
    public static function isProduct($barcode)
    {
        $barcode = trim($barcode);
//        return ProductBarcodes::find()->where(['barcode'=>$barcode])->exists();
        return EcommerceStock::find()->andWhere(['product_barcode'=>$barcode])->exists();
    }

    /*
    * Is not allocated product
    * @param string $barcode
    * @return boolean
     * */
    public static function isFreeProduct($barcode)
    {
        $barcode = trim($barcode);
        return EcommerceStock::find()->andWhere(['product_barcode'=>$barcode,'status_availability'=>StockAvailability::YES])->exists();
    }
	

//    public function getSkuIdByProductBarcodeInItem($aProductBarcode)
//    {
//        return EcommerceStock::find()->select('client_product_sku')
//            ->andWhere(['product_barcode' => trim($aProductBarcode)])
//            ->limit(1)->scalar();
//    }

//    public function getSkuIdByProductBarcode($aProductBarcode)
//    {
//        $apiService = new \common\ecommerce\defacto\barcodeManager\service\MasterDataAPIService();
//        $masterDataResult = $apiService->GetMasterData('','',$aProductBarcode);
//        return ArrayHelper::getValue($masterDataResult,'Data.SkuId');
//    }

//    public function getTotalProductsByProductBarcode($aProductBarcode)
//    {
//        $skuId = $this->getSkuIdByProductBarcode($aProductBarcode);
//        $apiService = new \common\ecommerce\defacto\barcodeManager\service\MasterDataAPIService();
//
//        $masterDataResult = $apiService->GetMasterData('',$skuId,'');
//
//        $productList[] = $aProductBarcode;
//        $productList = ArrayHelper::merge($productList,ArrayHelper::getValue($masterDataResult,'Data.products'));
//
//        return array_unique($productList);
//    }

//    public function getTotalProductsByProductBarcodeInItem($aProductBarcode)
//    {
//        $skuId = $this->getSkuIdByProductBarcodeInItem($aProductBarcode);
//        $apiService = new \common\ecommerce\defacto\barcodeManager\service\MasterDataAPIService();
//        $masterDataResult = $apiService->GetMasterData('',$skuId,'');
//
//        $productList[] = $aProductBarcode;
//        $productList = ArrayHelper::merge($productList,ArrayHelper::getValue($masterDataResult,'Data.products'));
//
//        return array_unique($productList);
//    }
}