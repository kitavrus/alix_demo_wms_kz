<?php

namespace stockDepartment\modules\alix\controllers\outbound\domain\dto\add_order;

class AddOrderItemRequestDTO
{
	public $guid;
	
    public $quantity = 0;
    public $lamoda_sku = "";
    public $item_name = "";
    public $paidPrice = 0;
}