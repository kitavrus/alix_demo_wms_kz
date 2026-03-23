<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain\dto\add_order;

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
