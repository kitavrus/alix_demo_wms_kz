<?php
namespace stockDepartment\modules\alix\controllers\api\v1\inbound\dto\add_order;

class AddOrderItemRequestDTO
{
	public $barcode = "";
	public $article = "";
	public $quantity;
	public $guid;
	public $datamatrix = [];
}