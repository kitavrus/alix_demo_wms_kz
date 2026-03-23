<?php

namespace app\modules\intermode\controllers\ecommerce\outbound\domain\dto;

use app\modules\intermode\controllers\ecommerce\stock\domain\entities\EcommerceStock;
use app\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
use app\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItem;

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