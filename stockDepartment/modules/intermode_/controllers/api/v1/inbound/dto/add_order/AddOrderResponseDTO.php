<?php

namespace app\modules\intermode\controllers\api\v1\inbound\dto\add_order;

/**
 *
 * @property string $orderNumber
 * @property string $status
 * @property AddOrderItemResponseDTO[] $items
 */
class AddOrderResponseDTO
{
	public $orderNumber = "";
	public $status = "";
	public $items = [];
}
