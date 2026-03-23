<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 12:23
 */

namespace common\clientObject\subaruAuto\outbound\validation;

use common\clientObject\subaruAuto\outbound\repository\OutboundRepository;
use Yii;

class OutboundOrderUploadValidation
{
    private $config;
    private $outboundRepository;
    private $productService;
    private $placementUnitService;
    private $outboundUnitAddressService;
    private $stockService;

    /**
     * Validation constructor.
     * @param $config array
     * @param $params array
     */
    public function __construct($config = [],$params = [])
    {
        $this->config = $config;
        $this->outboundRepository = new OutboundRepository();
        $this->productService = new \common\modules\product\service\ProductService();
        $this->placementUnitService = new \common\modules\placementUnit\service\Service();
        $this->outboundUnitAddressService = new \common\modules\placementUnit\service\OutboundUnitAddressService();
        $this->stockService = new \common\modules\stock\service\Service();
    }
    //
    public function isOrderExist($orderNumber)
    {
        return $this->outboundRepository->isOrderExist($orderNumber);
    }
}