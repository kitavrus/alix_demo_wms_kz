<?php

namespace app\modules\intermode\controllers\api\v1\outbound\dto\status_order;

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
