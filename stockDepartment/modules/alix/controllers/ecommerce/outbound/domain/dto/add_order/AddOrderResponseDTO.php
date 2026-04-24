<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\dto\add_order;

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
