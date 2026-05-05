<?php

namespace stockDepartment\modules\alix\controllers\api\v1\inbound\dto\inbound_complete;

/**
 * Позиция приходования товара для 1С InboundComplete.
 *
 * @property string $guid     ключевое поле, по которому 1С ищет номенклатуру
 * @property string $article  для информации
 * @property string $barcode  для информации
 * @property int    $quantity фактическое количество принятого товара
 */
class InboundCompleteItemDTO
{
	public $guid = "";
	public $article = "";
	public $barcode = "";
	public $quantity = 0;
}
