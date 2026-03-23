<?php

namespace app\modules\intermode\controllers\api\v1\inbound\dto\add_order;

use common\modules\inbound\models\InboundOrder;

/**
 *
 * @property string $orderNumber
 * @property integer $clientId
 * @property integer $expectedQty
 * @property array $items
 */
class AddOrderRequestDTO
{
	public $orderNumber = "";
	public $comment = "";
	public $clientId = 103;
	public $uuid_1c = ""; // client_order_id
	public $from_location = 0; // from_point_id
	public $expectedQty = 0;
	public $type = InboundOrder::ORDER_TYPE_INBOUND;
	public $items = [];
}
