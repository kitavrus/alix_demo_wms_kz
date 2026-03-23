<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain\dto;

use app\modules\ecommerce\controllers\intermode\stock\domain\entities\EcommerceStock;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInbound;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundItem;

/**
 *
 * @property EcommerceInbound $order
 * @property EcommerceInboundItem[] $items
 * @property EcommerceStock[] $stocks
 * @property string $outboundBoxBarcode
 */
class OrderInfoDTO
{
	public $order;
	public $items;
//	public $stocks;
//	public $outboundBoxBarcode;
}