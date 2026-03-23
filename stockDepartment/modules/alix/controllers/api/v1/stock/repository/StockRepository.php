<?php

namespace stockDepartment\modules\alix\controllers\api\v1\stock\repository;

use common\modules\stock\models\Stock;

class StockRepository
{
	private $id;

    public function getClientID()
    {
        return 103;
    }
    public function getAvailableStock()
    {
      return Stock::find()
			->select('product_barcode, product_model, product_sku,  count(id) as product_quantity')
			->andWhere([
				'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
			])
			->groupBy("product_barcode")
			  ->asArray()
			  ->all();
    }

}