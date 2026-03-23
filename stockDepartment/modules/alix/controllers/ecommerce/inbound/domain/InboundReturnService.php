<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\inbound\domain;

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