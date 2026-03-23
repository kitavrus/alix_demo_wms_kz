<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:17
 */

namespace stockDepartment\modules\wms\models\miele\validation;


use common\modules\client\models\Client;
use common\modules\codebook\models\Codebook;
use common\modules\product\models\Product;
use stockDepartment\modules\wms\models\miele\repository\MovementRepository;
use stockDepartment\modules\wms\models\miele\repository\OutboundRepository;

class ValidationMovement
{
    private $repository;

    public function __construct() {
        $this->repository = new MovementRepository();
    }

    public function isProduct($barcode) {
        return $this->repository->isProduct($barcode);
    }

    public function isFub($pickListBarcode,$productBarcode,$fabBarcode) {
        return $this->repository->isProductAndFabInMovementOrder($pickListBarcode,$productBarcode,$fabBarcode);
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

    //DONE
    public function isPickListExist($pickListBarcode) {
        return $this->repository->isPickListExist($pickListBarcode);
    }

    public function isProductInMovementOrder($pickListBarcode,$productBarcode) {
        return $this->repository->isProductInMovementOrder($pickListBarcode,$productBarcode);
    }

    public function isExtraBarcodeInOrder($movementId,$productBarcode) {
        return $this->repository->isExtraBarcodeInOrder($movementId,$productBarcode);
    }
    public function isNextBarcodeWithFabInOrder($movementId,$productBarcode) {
        return $this->repository->isNextBarcodeWithFabInOrder($movementId,$productBarcode);
    }
}