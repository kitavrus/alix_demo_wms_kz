<?php
namespace app\modules\ecommerce\controllers\intermode\inbound\domain\dto\add_order;

class AddOrderItemRequestDTO
{
	public $name;
	public $barcode;
	public $brand;
	public $color;
	public $quantity;
	public $article;
	public $datamatrix = [];
}