<?php

namespace app\modules\intermode\controllers\ecommerce\outbound\domain\dto\add_order;

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
	public $clientId = 103;
	public $expectedQty = 0;
	public $shipmentSource = "CRM";
	public $items = [];
}
