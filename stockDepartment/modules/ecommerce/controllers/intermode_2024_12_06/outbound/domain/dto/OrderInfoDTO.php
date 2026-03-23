<?php

namespace app\modules\ecommerce\controllers\intermode\outbound\domain\dto;

use app\modules\ecommerce\controllers\intermode\stock\domain\entities\EcommerceStock;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutbound;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutboundItem;

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