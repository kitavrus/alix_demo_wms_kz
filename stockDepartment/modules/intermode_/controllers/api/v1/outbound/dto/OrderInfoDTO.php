<?php

namespace app\modules\intermode\controllers\api\v1\outbound\dto;

use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;

/**
 *
 * @property OutboundOrder $order
 * @property OutboundOrderItem[] $items
 * @property string $outboundBoxBarcode
 */
class OrderInfoDTO
{
	public $order;
	public $items;
}