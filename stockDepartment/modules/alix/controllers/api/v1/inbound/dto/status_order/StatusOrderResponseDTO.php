<?php

namespace stockDepartment\modules\alix\controllers\api\v1\inbound\dto\status_order;

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
