<?php

namespace stockDepartment\modules\alix\controllers\api\v1\inbound\dto\inbound_complete;

/**
 * Запрос на проведение приёмки в 1С (POST /hs/NMDX/InboundComplete).
 *
 * @property string                    $wmsId       номер NMDX-документа поступления (InboundOrder.id)
 * @property string                    $orderNumber номер заказа в WMS — для логирования
 * @property string                    $status      InboundAPIStatus::COMPLETED | COMPLETED_WITH_DIFFERENCES
 * @property InboundCompleteItemDTO[]  $items
 */
class InboundCompleteRequestDTO
{
	public $wmsId = "";
	public $orderNumber = "";
	public $status = "";
	/** @var InboundCompleteItemDTO[] */
	public $items = [];
}
