<?php

namespace stockDepartment\modules\alix\controllers\api\v1\outbound\dto\add_order;

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
