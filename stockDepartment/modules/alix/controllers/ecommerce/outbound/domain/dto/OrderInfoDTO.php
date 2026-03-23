<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\dto;

use stockDepartment\modules\alix\controllers\ecommerce\stock\domain\entities\EcommerceStock;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItem;

/**
 *
 * @property EcommerceOutbound $order
 * @property EcommerceOutboundItem[] $items
 * @property EcommerceStock[] $stocks
 * @property string $outboundBoxBarcode
 */
class OrderInfoDTO
{
	public $order;
	public $items;
	public $stocks;
	public $outboundBoxBarcode;
}