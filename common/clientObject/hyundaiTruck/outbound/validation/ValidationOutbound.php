<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:17
 */

namespace common\clientObject\hyundaiTruck\outbound\validation;


use common\modules\codebook\models\Codebook;
use common\clientObject\hyundaiTruck\outbound\repository\OutboundRepository;

class ValidationOutbound
{
    private $repository;

    public function __construct() {
        $this->repository = new OutboundRepository();
    }

    public function isProduct($pickList,$barcode) {
        $order = $this->getOrderByPickList($pickList);
        return $this->repository->isProductExistInOrder($order->id,$barcode);
    }

    public function isExtraBarcodeInOrder($pickList,$barcode) {
        $order = $this->getOrderByPickList($pickList);
        return $this->repository->isExtraBarcodeInOrder($order->id,$barcode);
    }

    /*
    * Is box
    * @param string $barcode
    * @return boolean
    * */
    public function isBox($barcode)
    {
        $prefix = self::_getPrefixBarcode($barcode);

        if(!empty($prefix)) {
            $result = Codebook::find()->where(['cod_prefix'=>$prefix,'base_type'=>Codebook::BASE_TYPE_BOX])->exists();
            if($result || ($prefix{0} == 'b' || $prefix{0} == 'B') ) {
                if((strlen($barcode) <=13 && strlen($barcode) >=9)) {
                    return true;
                }
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

    public function getOrderByPickList($pickList) {
        return $this->repository->findOrderByPickList($pickList);
    }

    public function getEmployeeByBarcode($employeeBarcode) {
        return $this->repository->getEmployeeByBarcode($employeeBarcode);
    }

    public function getPickListByBarcode($pickListBarcode) {
        return $this->repository->getPickListByBarcode($pickListBarcode);
    }
}