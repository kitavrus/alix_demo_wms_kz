<?php

namespace app\modules\intermode\controllers\api\v1\inbound\dto;

use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;

/**
 *
 * @property InboundOrder $order
 * @property InboundOrderItem[] $items
 * @property string $outboundBoxBarcode
 */
class OrderInfoDTO
{
	public $order;
	public $items;
}