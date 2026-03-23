<?php

namespace app\modules\intermode\controllers\product;

use app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundService;
use stockDepartment\modules\intermode\controllers\product\domains\dto\Filter;
use stockDepartment\modules\intermode\controllers\product\domains\ProductService;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;


class ProductController extends Controller
{
	public function actionIndex()
	{

	}

	public function actionTest()
	{
		$s = new ProductService();
		$filter = new Filter();
		$filter->barcode = [
			"0196247107548"
			//"5059862199334"
		];
//		$s->getAndSaveMetaData($filter);
		$data = $s->getByGuid("f5defc23-8c47-11ef-b371-005056011924");
		$o = new OutboundService();
		$o->getOrderInfo()
//		if(empty($data)) {
//			return "";
//		}
//
//		foreach ($data as $item) {
//			echo $item->guid. "<br />";
//		}


		VarDumper::dump($data);
		return $this->render("test");
	}

}