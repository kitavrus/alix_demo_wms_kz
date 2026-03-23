<?php

namespace stockDepartment\modules\alix\controllers\api\v1\outbound\dto\add_order;

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
//	public $uuid_1c = ""; // client_order_id
	public $toLocation = 0; // client_order_id
	public $expectedQty = 0;
	public $items = [];
}
