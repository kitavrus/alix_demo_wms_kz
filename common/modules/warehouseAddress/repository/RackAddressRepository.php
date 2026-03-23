<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */

namespace common\modules\warehouseAddress\repository;


use common\modules\stock\models\RackAddress;
use common\overloads\ArrayHelper;

class RackAddressRepository
{
    private $id;

    public function create($dto) { return $dto; }

    public function isExists($rackAddressBarcode) {
        return RackAddress::find()->andWhere(['address'=>$rackAddressBarcode])->exists();
    }

    public function isExistsWithClientZone($rackAddressBarcode,$clientId) {
        return RackAddress::find()->andWhere(['address'=>$rackAddressBarcode,'client_id'=>$clientId])->exists();
    }

    public function isExistsWithWarehouse($rackAddressBarcode,$warehouseId) {
        return RackAddress::find()->andWhere(['address'=>$rackAddressBarcode,'warehouse_id'=>$warehouseId])->exists();
    }

    //
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
}