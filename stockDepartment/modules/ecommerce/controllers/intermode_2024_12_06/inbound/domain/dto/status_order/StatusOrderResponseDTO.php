<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain\dto\status_order;

/**
 *
 * @property string $orderNumber
 * @property string $wmsId
 * @property string $status
 */
class StatusOrderResponseDTO
{
	public $orderNumber = "";
	public $wmsId = "";
	public $status = "";
	public $items = [];
}
