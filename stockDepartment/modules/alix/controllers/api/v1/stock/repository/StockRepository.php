<?php

namespace stockDepartment\modules\alix\controllers\api\v1\stock\repository;

use common\ecommerce\entities\EcommerceStock;

class StockRepository
{
	private $id;

    public function getClientID()
    {
        return 103;
    }
    public function getAvailableStock()
    {
      return EcommerceStock::find()
			->select('product_barcode, product_model, product_sku,  count(id) as product_quantity')
			->andWhere([
				'status_availability'=>EcommerceStock::STATUS_AVAILABILITY_YES,
			])
			->groupBy("product_barcode")
			  ->asArray()
			  ->all();
    }

}