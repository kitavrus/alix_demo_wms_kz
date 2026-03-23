<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.10.14
 * Time: 10:49
 */
namespace common\components;

use app\modules\stock\models\StockZone;
use common\modules\client\models\Client;
use common\modules\codebook\models\BaseBarcode;
use common\modules\codebook\models\Codebook;
use common\modules\employees\models\Employees;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\product\models\Product;
use common\modules\product\models\ProductBarcodes;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\RackAddress;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\helpers\Url;
use yii\helpers\BaseFileHelper;

//use common\modules\client\models\ClientEmployees;
//use common\modules\store\models\Store;
//use yii\helpers\VarDumper;
//use yii\validators\EmailValidator;
include(Yii::getAlias('@common/components/extentions/php-barcode/Barcode.php'));
class BarcodeManager extends Component
{
    /**
     * @var integer
     */
    const BOX_BARCODE_M3 = 333;

    /*
    * Is box
    * @param string $barcode
    * @return boolean
    * */
    public static function isBox($barcode)
    {
        $prefix = self::_getPrefixBarcode($barcode);

        if(!empty($prefix)) {
            $result = Codebook::find()->where(['cod_prefix'=>$prefix,'base_type'=>Codebook::BASE_TYPE_BOX])->exists();

            if($result || ($prefix{0} == 'b' || $prefix{0} == 'B') ) {
                if((strlen($barcode) <=13 && strlen($barcode) >=9)) {
                    return true;
                }
            }

            //s: 243
            $barcode = trim($barcode);
            $code = substr($barcode, 0, 3);
            if($code == "243") {
                return true;
            }
            //e: 243

            if(trim($barcode) == '0-inventory-0') {
                return true;
            }
        }
        return false;
    }

    public static function isDefactoBox($box)
    {
        $barcode = trim($box);
        $code = substr($barcode, 0, 3);
        $defactoBoxPrefix = [
            "243","486","233",'141','142',
            "143","144","145",'146','147',
            "148",
        ];
        return in_array($code, $defactoBoxPrefix);
    }

    public static function isDefactoProduct($product)
    {
        $product = trim($product);
        $code = substr($product, 0, 3);
        if(in_array($code, ["230",'900','868'])) {
            return true;
        }
        return false;
    }

    /*
    * Is box
    * @param string $barcode
    * @return boolean
    * */
    public static function isBoxOnlyOur($barcode)
    {
        $prefix = self::_getPrefixBarcode($barcode);

        if(!empty($prefix)) {
            $result = Codebook::find()->andWhere(['cod_prefix'=>$prefix,'base_type'=>Codebook::BASE_TYPE_BOX])->exists();

            if($result || ($prefix{0} == 'b' || $prefix{0} == 'B') ) {
                if((strlen($barcode) <=13 && strlen($barcode) >=9)) {
                    return true;
                }
            }
        }
        return false;
    }

    /*
    * Is regiment
    * @param string $barcode
    * @return boolean
    * */
    public static function isRegiment($barcode)
    {
        $a = explode('-',trim($barcode));

        if(!empty($a) && is_array($a) && isset($a['2'],$a['3']) && strlen($a['2'])<=2 && strlen($a['3']) == 1 && in_array(count($a),[3,4,5])) {
           return true;
        }

        return RackAddress::checkForExist($barcode);

//        return false;

        // $prefix = self::_getPrefixBarcode($barcode);
        // return (!empty($prefix) ? (int)Codebook::find()->where(['cod_prefix'=>$prefix,'base_type'=>Codebook::BASE_TYPE_REGIMENT])->exists() : 0 );
    }

    /*
    * Is regiment
    * @param string $barcode
    * @return boolean
    * */
    public static function isRack($barcode)
    {
        $prefix = self::_getPrefixBarcode($barcode);

        return (!empty($prefix) ? (int)Codebook::find()->where(['cod_prefix'=>$prefix,'base_type'=>Codebook::BASE_TYPE_RACK])->exists() : 0 );
    }

    /*
    * Is pallet
    * @param string $barcode
    * @return boolean
    * */
    public static function isPallet($barcode)
    {
        $prefix = self::_getPrefixBarcode($barcode);
        $barcode = trim($barcode);

        if(!empty($prefix)) {
            $result = Codebook::find()->where(['cod_prefix'=>$prefix,'base_type'=>Codebook::BASE_TYPE_PALLET])->exists();
            if(!empty($result) && (strlen($barcode) <=13 && strlen($barcode) >=9)) {
                return true;
            }
        }

        return false;
    }
    /*
     * Get prefix barcode
     * @param string $barcode
     * @return string Prefix
     * */
    private static function _getPrefixBarcode($barcode)
    {
        $barcode = trim($barcode);
        return !empty($barcode) ? substr($barcode, 0, 2) : '';
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
        return Stock::find()->andWhere(['product_barcode'=>$barcode])->exists();
    }

    /*
    * Is not allocated product
    * @param string $barcode
    * @return boolean
     * */
    public static function isFreeProduct($barcode)
    {
        $barcode = trim($barcode);
        return Stock::find()->andWhere(['product_barcode'=>$barcode,'status_availability'=>Stock::STATUS_AVAILABILITY_YES])->exists();
    }

    /*
    * Is picking list
    * @param string $barcode
    * @param integer $status
    * @return boolean
     * */
    public static function isPickingList($barcode,$status = null)
    {
        $barcode = trim($barcode);

        $q = OutboundPickingLists::find();
        $q->where(['barcode'=>$barcode]);

        $q->andFilterWhere([
            'status' => $status,
        ]);

        return $q->exists();
    }

   /*
    * Is employee
    * @param string $barcode
    * @return boolean
     * */
    public static function isEmployee($barcode)
    {
        $barcode = trim($barcode);
        return Employees::find()->where(['barcode'=>$barcode])->exists();
    }

    /*
     * Get Box prefix code
     * @return boolean | string Prefix code
     * */
    public function getBoxPrefix()
    {
        $c = Codebook::findOne(['base_type'=>Codebook::BASE_TYPE_BOX]);
        return ($c ? $c->cod_prefix : false);
    }

    /*
     * Get Pallet prefix code
     * @return boolean | string Prefix code
     * */
    public function getPalletPrefix()
    {
        $c = Codebook::findOne(['base_type'=>Codebook::BASE_TYPE_PALLET]);
        return ($c ? $c->cod_prefix : false);
    }


    /*
     * Create Base Barcode
     * @param string $boxNumber  Current box number -> 1 from 12
     * @param string $boxBarcode Box barcode
     * @param string $ttnBarcode TTN barcode Id number Delivery proposal
     * @param string $boxTotal   Box total items
     * @return string $baseBarcode Created BaseBarcode
     * */
    public static function createBaseBarcode($boxNumber,$boxBarcode,$ttnBarcode,$boxTotal)
    {
        $bbID = 0;
        if($bb = \common\modules\codebook\models\BaseBarcode::find()->select('id')->orderBy(['id'=>SORT_DESC])->scalar()) {
            $bbID = $bb;
        }

        $bb = new \common\modules\codebook\models\BaseBarcode();
        $bb->base_barcode = sprintf("%014d",$bbID+1);
        $bb->box_number = $boxNumber;
        $bb->box_barcode = $boxBarcode;
        $bb->ttn_barcode = $ttnBarcode;
        $bb->box_total = $boxTotal;
        $bb->save(false);

        return $bb->base_barcode;
    }


    /*
     * Is box size barcode
     * @param string $boxBarcode Example : 60X50X40/20, 60X60X40\40, 60X60X40, 21
     * @return mix number | null
     * */
    public static function isM3BoxBorder($boxBarcode)
    {
        $separateCode = 'x';
        $code = trim($boxBarcode);

        if(empty($code)) {
            return null;
        }

        if(is_numeric($code) && $code <= self::BOX_BARCODE_M3 && (in_array(strlen($code),[1,2,3]))) {
            return true;
        }

        $codePrepared = str_replace(['X','x',"\\",'/'],$separateCode,$code);
        $a = explode($separateCode,$codePrepared);
        if(!empty($a) && is_array($a)) {
            if(in_array(count($a),[3,4])) {
                return true;
            }
        }

        return false;
    }


    /*
     * Get box M3
     * @param string $boxBarcode Example : 60X40X40/20, 60X40X40\20, 60X40X40, 10
     * @return mix number | null
     * */
    public static function getBoxM3($boxBarcode)
    {
        $precision = 3;
        $separateCode = 'x';
        $sizeReturn = '';

        $code = trim($boxBarcode);
        $code = str_replace([' '],'',$code);
        if(empty($code)) {
            return null;
        }

        // 1 - Если это числовой код
        if(is_numeric($code) && $code <= self::BOX_BARCODE_M3) {
            $sizeReturn = $code / self::BOX_BARCODE_M3;
           return round($sizeReturn,$precision);
        }
        // 2 - Если нет кода, заменяем на наш спец код и разбиваем строку
        $codePrepared = str_replace(['X','x',"\\",'/'],$separateCode,$code);

        $a = explode($separateCode,$codePrepared);
        if(!empty($a) && is_array($a)) {
            if (count($a) == 3) {
                $a['0'] = preg_replace('/[^0-9]/', '',$a['0']);
                $a['1'] = preg_replace('/[^0-9]/', '',$a['1']);
                $a['2'] = preg_replace('/[^0-9]/', '',$a['2']);

                $size = $a['0'] * $a['1'] * $a['2'];
                $sizeReturn = ($size / 1000000);
            }

            if (count($a) == 4) {
                $a['3'] = str_replace([','],'.',$a['3']);
                $a['3'] = preg_replace('/[^0-9],./', '',$a['3']);
                $sizeReturn = ($a['3'] / self::BOX_BARCODE_M3);
                $sizeReturn = round($sizeReturn, $precision);
            }
        }

        return !empty($sizeReturn) ? $sizeReturn : null;
    }

    /*
     * Get number box M3
     * @param float $m3 Example : 0.072, 0.096, 0.033
     * @return mix number | null
     * */
    public static function getIntegerM3($m3)
    {
        $m3 = floatval($m3);
        if(is_float($m3) && $m3 > 0 && $m3 <= 1.0) {
            $r = $m3 * self::BOX_BARCODE_M3;
            return round($r,0);
        }
        return null;
    }

    /*
     *
     * */
    public static function createBarcodeImage($barcodeText, $rotate = 0, $hri = false, $xHeight = 30, $xWidth = 230, $pX = 115, $pY=30)
    {

            //VarDumper::dump(Yii::getAlias('common/components/extentions/php-barcode/Barcode.php'),10,true); die;
            $fontSize = 12;   // GD1 in px ; GD2 in point
            $marge    = 8;   // between barcode and hri in pixel
            $x        = $pX;  // barcode center
            $y        = $pY;  // barcode center
            $height   = 60;   // barcode height in 1D ; module size in 2D
            $width    = 2;    // barcode height in 1D ; not use in 2D
            $font     = Yii::getAlias('@common/components/extentions/php-barcode/sample/font/Arial.ttf');
            $code     = $barcodeText;
            $type     = 'code128';
            $angle     = $rotate;

            $iWidth = $xWidth;
            $iHeight = $xHeight;

            if($rotate > 0){
                $iWidth = $xHeight;
                $iHeight = $xWidth;
                $x        = $pY;  // barcode center
                $y        = $pX;  // barcode center
            }
            //создаем изображение
            $im = imagecreatetruecolor($iWidth, $iHeight);

            //создаем цвета
            $black  = ImageColorAllocate($im,0x00,0x00,0x00);
            $white  = ImageColorAllocate($im,0xff,0xff,0xff);

             //рисуем прямоугольник
            imagefilledrectangle($im, 0, 0, $iWidth, $iHeight, $white);

            $data = \Barcode::gd($im, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);
            if ($hri){
                $box = imagettfbbox($fontSize, 0, $font, $data['hri']);
                $len = $box[2] - $box[0];
                \Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
                imagettftext($im, $fontSize, $angle, $x + $xt, $y + $yt, $black, $font, $data['hri']);
            }
            //$dirPath = 'uploads/barcodes/';
            $dirPath = Yii::getAlias('web').'/uploads/barcodes/';
            $dirPath = 'uploads/barcodes/';
            $fileName = 'barcode-'.$barcodeText.'-'.$rotate.'.png';
            BaseFileHelper::createDirectory($dirPath);
            //$dirPath = 'uploads\barcodes\barcode-'.$barcodeText.'-'.$rotate.'.png';
            //header('Content-type: image/gif');
//            if($rotate){
//                $im = imagerotate($im, 90, $white);
//            }
            imagepng($im, $dirPath.$fileName);
            imagedestroy($im);

        if(file_exists($dirPath.$fileName)){
            //return Url::to('barcodes/barcode-'.$barcodeText.'-'.$rotate.'.png');
            return Url::to('@web/uploads/barcodes/barcode-'.$barcodeText.'-'.$rotate.'.png', true);
        }
        return false;
    }

    /*
     * Is empty box
     * @param string box baecode
     * @return boolean
     * */
    public static function isEmptyBox($boxBarcode)
    {
//        $stockProducts = Stock::find()->where(['primary_address' => $boxBarcode,'status_availability'=>[
//            Stock::STATUS_AVAILABILITY_NOT_SET,
//            Stock::STATUS_AVAILABILITY_NO,
//            Stock::STATUS_AVAILABILITY_YES,
//        ]])->asArray()->all();
        $stockProducts = Stock::find()->andWhere(['primary_address' => $boxBarcode])->asArray()->all();
        $isEmptyBox = false;
        $boxIsEmpty = count($stockProducts);

        $countOnStock = Stock::find()->andWhere(['primary_address' => $boxBarcode,'status'=>Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL])->count();

        if($boxIsEmpty == $countOnStock) {
            return true;
        }

        if ($boxIsEmpty > 0) {
            $countOutOfStock = 0;
            if ($stockProducts) {
                foreach ($stockProducts as $stockProductOne) {
                    if (!empty($stockProductOne['outbound_order_id'])) {
                        if ($oo = OutboundOrder::findOne(['id' => $stockProductOne['outbound_order_id']])) {
                            // Так договорились с Эрмеком
                            if ($oo->cargo_status == OutboundOrder::CARGO_STATUS_ON_ROUTE || $oo->cargo_status == OutboundOrder::CARGO_STATUS_DELIVERED) {
                                $countOutOfStock += 1;
                            }
                        }
                    }
                }
            }
            if($countOutOfStock == $boxIsEmpty) {
                $isEmptyBox = true;
            }
        } else {
            $isEmptyBox = true;
        }

//        if($boxBarcode == '0-inventory-0') {
//            $isEmptyBox = true;
//        }
        return $isEmptyBox;
    }

    /*
 * Is empty box
 * @param string box baecode
 * @return boolean
 * */
    public static function isEmptyAddress($addressBarcode)
    {
        $stockProducts = Stock::find()->andWhere(['secondary_address' => $addressBarcode])->asArray()->all();
        $isEmptyBox = false;
        $boxIsEmpty = count($stockProducts);
        if ($boxIsEmpty > 0) {
            $countOutOfStock = 0;
            if ($stockProducts) {
                foreach ($stockProducts as $stockProductOne) {
                    if (!empty($stockProductOne['outbound_order_id'])) {
                        if ($oo = OutboundOrder::findOne(['id' => $stockProductOne['outbound_order_id']])) {
                            // Так договорились с Эрмеком
                            if ($oo->cargo_status == OutboundOrder::CARGO_STATUS_ON_ROUTE || $oo->cargo_status == OutboundOrder::CARGO_STATUS_DELIVERED) {
                                $countOutOfStock += 1;
                            }
                        }
                    }
                }
            }
            if($countOutOfStock == $boxIsEmpty) {
                $isEmptyBox = true;
            }
        } else {
            $isEmptyBox = true;
        }

//        if($boxBarcode == '0-inventory-0') {
//            $isEmptyBox = true;
//        }
        return $isEmptyBox;
    }

    /*
    * get DP by BaseBarcode
    * @param string BaseBarcode
    * @return TlDeliveryProposal|bool
    * */
    public static function getDpByBaseBarcode($baseBarcode)
    {
        if($bb = BaseBarcode::findOne((int)$baseBarcode)){
            return TlDeliveryProposal::findOne($bb->ttn_barcode);
        }

        return false;
    }

    /*
     *
     * */
    public static function isZone($toAddress)
    {
        $rackAddressCurrent = RackAddress::find()->andWhere(['address'=>$toAddress])->one();
        if($rackAddressCurrent) {
            $stockZones = StockZone::find()->all();
            if($stockZones)  {
                foreach($stockZones as $stockZone) {
                    // TODO сделать это нормально
                    $beginAddress = RackAddress::find()->andWhere(['address'=>$stockZone->address_begin])->one()->id;
                    $endAddress =  RackAddress::find()->andWhere(['address'=>$stockZone->address_end])->one()->id;
                    $zonAddress = RackAddress::find()
                        ->andWhere('id >= :beginAddress AND id <= :endAddress',[':beginAddress'=>$beginAddress,':endAddress'=>$endAddress])
                        ->andWhere(['id'=>$rackAddressCurrent->id])
                        ->exists();
                    if($zonAddress) {
                        return true;
                    }
                }
            }
        }

        return null;
    }

    public static function isAddressExist($address) {
        return RackAddress::find()->andWhere(['address'=>$address])->exists();
    }


    public static function whatIsZone($address) {
        $rackAddressCurrent = RackAddress::find()->andWhere(['address'=>$address])->one();
        if($rackAddressCurrent) {
            $stockZones = StockZone::find()->all();
            if($stockZones)  {
                foreach($stockZones as $stockZone) {
                    // TODO сделать это нормально
                    $beginAddress = RackAddress::find()->andWhere(['address'=>$stockZone->address_begin])->one()->id;
                    $endAddress =  RackAddress::find()->andWhere(['address'=>$stockZone->address_end])->one()->id;
                    $zonAddress = RackAddress::find()
                        ->andWhere('id >= :beginAddress AND id <= :endAddress',[':beginAddress'=>$beginAddress,':endAddress'=>$endAddress])
                        ->andWhere(['id'=>$rackAddressCurrent->id])
                        ->exists();
                    if($zonAddress) {
                        return $stockZone->id;
                    }
                }
            }
        }

        return null;
    }

    public  static function addressInZone($address,$zone) {
        $addressZone = self::whatIsZone($address);
        return $addressZone != null && ($addressZone-1) == $zone;
//        if($addressZone != null && ($addressZone-1) == $zone) {
//            return true;
//        }
//        return false;
    }


    public static function isBoxTypeReturn($boxBarcode) {
        return self::_isBoxTypeQuery($boxBarcode,Stock::IS_PRODUCT_TYPE_RETURN);
    }

    public static function isBoxTypeLotBox($boxBarcode) {
        return self::_isBoxTypeQuery($boxBarcode,Stock::IS_PRODUCT_TYPE_LOT_BOX);
    }

    public static function isBoxTypeLot($boxBarcode) {
        return self::_isBoxTypeQuery($boxBarcode,Stock::IS_PRODUCT_TYPE_LOT);
    }

    public static function isProductTypeReturn($productBarcode) {
        return self::_isProductTypeQuery($productBarcode,Stock::IS_PRODUCT_TYPE_RETURN);
    }

    public static function isProductTypeLotBox($productBarcode) {
        return self::_isProductTypeQuery($productBarcode,Stock::IS_PRODUCT_TYPE_LOT_BOX);
    }

    public static function isProductTypeLot($productBarcode) {
        return self::_isProductTypeQuery($productBarcode,Stock::IS_PRODUCT_TYPE_LOT);
    }

    public static function isBoxOrProductTypeReturn($boxOrProductBarcode) {
        return self::_isProductBoxTypeQuery($boxOrProductBarcode,Stock::IS_PRODUCT_TYPE_RETURN);
    }



    public static function isBoxLorOrReturnBox($boxOrProductBarcode) {
        return self::_isProductBoxTypeQuery($boxOrProductBarcode,[Stock::IS_PRODUCT_TYPE_RETURN,Stock::IS_PRODUCT_TYPE_LOT_BOX]);
    }

    public static function isBoxOrProductTypeLotBox($boxOrProductBarcode) {
        return self::_isProductBoxTypeQuery($boxOrProductBarcode,Stock::IS_PRODUCT_TYPE_LOT_BOX);
    }

    public static function isBoxOrProductTypeLot($boxOrProductBarcode) {
        return self::_isProductBoxTypeQuery($boxOrProductBarcode,Stock::IS_PRODUCT_TYPE_LOT);
    }

    private static function _isProductBoxTypeQuery($boxOrProductBarcode,$productType) {
        return Stock::find()->andWhere(
             ['and', 'is_product_type'=>$productType, ['or', 'product_barcode'=>$boxOrProductBarcode, 'primary_address'=>$boxOrProductBarcode]]
        )->exists();
    }

    private static function _isProductTypeQuery($productBarcode,$productType) {
        return Stock::find()->andWhere(['product_barcode'=>$productBarcode,'is_product_type'=>$productType])->exists();
    }

    private static function _isBoxTypeQuery($boxBarcode,$productType) {
        return Stock::find()->andWhere(['primary_address'=>$boxBarcode,'is_product_type'=>$productType])->exists();
    }

    public static function isReturnBoxBarcode($boxBarcode) {

        if(!BarcodeManager::isDefactoBox($boxBarcode) && !BarcodeManager::isBoxOnlyOur($boxBarcode)) {
            return false;
        }

        //return self::isBoxTypeReturn($boxBarcode);


        // Если передали наш шк короба
        if(BarcodeManager::isBoxOnlyOur($boxBarcode)) {
            $barcode = Stock::find()->select('inbound_client_box')->andWhere(['primary_address'=>$boxBarcode])->scalar();
        }

        $isExistInInbound = InboundOrder::find()->andWhere(['order_number'=>$boxBarcode])->exists();
        $isExistInReturnItem = ReturnOrderItems::find()->andWhere(['client_box_barcode'=>$boxBarcode])->exists();
        if($isExistInInbound && $isExistInReturnItem) {
            return true;
        }

        return false;
    }

    public static function isReturnProductBarcode($productBarcode,$clientId = null)
    {
        //return self::isProductTypeReturn($productBarcode);

        $clientBoxBarcode = Stock::find()
                            ->select('inbound_client_box')
                            ->andWhere([
                                'product_barcode'=>$productBarcode
                            ])
                            ->scalar();

        $isExistInInbound = InboundOrder::find()->andWhere(['order_number'=>$clientBoxBarcode])->exists();
        $isExistInReturnItem = ReturnOrderItems::find()->andWhere(['client_box_barcode'=>$clientBoxBarcode])->exists();
        if($isExistInInbound && $isExistInReturnItem) {
            return true;
        }

        return false;
    }

    //
    public static function isOneBoxOneProduct($boxBarcode, $clientID = null, $warehouseID = null)
    {
        //return self::isBoxTypeLotBox($boxBarcode);

        if(BarcodeManager::isBoxOnlyOur($boxBarcode)) {
            return Stock::find()->andWhere(['primary_address'=>$boxBarcode])
                ->andFilterWhere(['client_id'=>$clientID])
                ->andFilterWhere(['warehouse_id'=>$warehouseID])
                ->count() == 1;
        }

        if(BarcodeManager::isDefactoBox($boxBarcode)) {
            return Stock::find()->andWhere(['inbound_client_box'=>$boxBarcode])
                ->andFilterWhere(['client_id'=>$clientID])
                ->andFilterWhere(['warehouse_id'=>$warehouseID])
                ->count() == 1;
        }

        return false;
    }



    public static function findProductInStockByReturnBarcodeBox($barcode) {

        // Если передали наш шк короба
        if(BarcodeManager::isBoxOnlyOur($barcode)) {
            return Stock::find()->select('product_barcode')->andWhere(['primary_address'=>$barcode])->scalar();
        }

        if(BarcodeManager::isDefactoBox($barcode)) {
            return Stock::find()->select('product_barcode')->andWhere(['inbound_client_box'=>$barcode])->scalar();
        }

        return -1;
    }

    public static function findProductInStockByReturnBarcodeBoxInventory($barcode) {

        // Если передали наш шк короба
        if(BarcodeManager::isBoxOnlyOur($barcode)) {
            return Stock::find()->select('product_barcode')->andWhere(['inventory_primary_address'=>$barcode])->scalar();
        }

        if(BarcodeManager::isDefactoBox($barcode)) {
            return Stock::find()->select('product_barcode')->andWhere(['inbound_client_box'=>$barcode])->scalar();
        }

        return -1;
    }

    /*
     *
     * */
    public static function mapM3ToBoxSize($key)
    {
        $map = [
            '5'=> '23x17x33/5',
            '6'=> '32x27x22/6',
            '7'=> '29x27x27/7',
            '8'=> '52x47x10/8',
            '9'=> '55x38x13/9',
            '11'=>'40x40x20/11',
            '12'=>'42x30x28/12',
            '13'=>'38x38x28/13',// 38*38*28/13
            '14'=>'30x45x30/14',
            '15'=>'65x53x13/15',
            '16'=>'64x61x12/16',
            '17'=>'66x46x17/17',
            '18'=>'50x29x38/18',
            '19'=>'95x50x12/19',
            '20'=>'60x40x25/20',
            '21'=>'60x40x26/21',
            '22'=>'60x40x28/22',
            '23'=>'40x40x43/23',
            '24'=>'60x40x30/24',
            '25'=>'62x42x29/25',
            '26'=>'60x40x32/26',
            '27'=>'62x45x36/27',
            '28'=>'60x40x35/28',
            '29'=>'52x42x40/29', // '29'=>76x57x20/29
            '30'=>'60x40x37/30',
            '31'=>'76x56x22/31',
            '32'=>'60x40x40/32', // 0.096
            '33'=>'76x62x21/33',
            '34'=>'73x60x23/34', // 0.102
            '35'=>'76x58x24/35', // 0.105 '35'=>'73x52x28/35',
            '36'=>'75x55x26/36',
            '37'=>'78x60x24/37', // '37'=>'60x46x40/37',
            '38'=>'60x45x42/38',
            '39'=>'82x55x26/39',
            '40'=>'60x40x50/40',
            '41'=>'60x45x46/41',
            '42'=>'78x65x25/42',
            '43'=>'85x58x26/43',
            '44'=>'82x62x26/44',
            '45'=>'72x54x35/45',
            '46'=>'58x50x48/46', // '46'=>'75x53x35/46',
            '47'=>'80x65x27/47',
            '48'=>'75x55x35/48', // 0.144
            '49'=>'74x62x32/49',
            '50'=>'67x56x40/50', // 67*56*40/50
            '51'=>'90x65x26/51',
            '52'=>'65x50x48/52',
            '53'=>'88x70x26/53',
            '54'=>'65x55x45/54',
            '55'=>'70x55x43/55',
            '56'=>'75x60x37/56',
            '57'=>'70x58x42/57',
            '58'=>'65x50x48/58',
            '59'=>'80x60x37/59',
            '60'=>'74x57x43/60',
            '61'=>'78x63x37/61', // 0.183
            '62'=>'90x57x36/62',
            '63'=>'74x57x45/63',
            '64'=>'95x58x35/64',
            '65'=>'73x62x43/65',
            '66'=>'88x66x34/66', // 88*66*34/66
            '68'=>'105x65x30/68', // 105*65*30/68
            '69'=>'78x60x44/69',
            '71'=>'82x62x42/71',
            '73'=>'90x64x38/73',
            '74'=>'85x65x40/74',
            '76'=>'90x60x42/76', // 90*60*42/76
            '78'=>'75x57x55/78',
            '79'=>'90x66x40/79',
            '83'=>'77x60x54/83',
            '84'=>'92x64x43/84',
            '94'=>'75x65x58/94',
            '99'=>'90x60x55/99', // 0.297
        ];

        return ArrayHelper::getValue($map,$key,'');
    }
}