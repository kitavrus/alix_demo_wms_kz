<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.10.2017
 * Time: 14:48
 */

namespace common\ecommerce\main\inbound\validation;


use common\modules\warehouseAddress\service\RackAddressService;

class PlaceToAddressValidation extends \common\ecommerce\main\inbound\validation\Validation
{
    private $rackAddressService;
    /**
     * Validation constructor.
     * @param $config array
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->rackAddressService = new RackAddressService();
    }

    public function isTransportedBoxOrInboundBox($unitBarcode)
    {
        return $this->isTransportedBoxBarcode($unitBarcode) || $this->isInboundUnitAddress($unitBarcode);
    }


    public function isRackAddressExist($rackAddressBarcode) {
        return $this->rackAddressService->isExists($rackAddressBarcode);
    }
}