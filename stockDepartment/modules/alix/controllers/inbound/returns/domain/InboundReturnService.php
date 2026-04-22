<?php

namespace stockDepartment\modules\alix\controllers\inbound\returns\domain;

use common\modules\product\models\ProductBarcodes;

class InboundReturnService
{
	/**
	* @param string $productBarcode
	* @return boolean
	*
	* */
	public function isValidProductBarcode($productBarcode)
	{
		return ProductBarcodes::find()
							  ->andWhere(["barcode"=>$productBarcode])
							  ->exists();
	}
}