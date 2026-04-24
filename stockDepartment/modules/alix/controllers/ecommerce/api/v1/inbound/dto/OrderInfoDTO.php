<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto;

use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceInboundItem;

/**
 * @property EcommerceInbound $order
 * @property EcommerceInboundItem[] $items
 * @property string $outboundBoxBarcode
 */
class OrderInfoDTO
{
    public $order;
    public $items;
}
