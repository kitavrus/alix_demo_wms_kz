<?php

namespace app\modules\ecommerce\controllers\defacto\other;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\defacto\api\ECommerceAPINew;
use common\ecommerce\defacto\inbound\service\InboundAPIService2;
use common\ecommerce\entities\EcommerceStock;
use PHPExcel_IOFactory;
use stockDepartment\components\Controller;

// /ecommerce/defacto/other/un-block/run
class UnBlockController extends Controller
{
	public function actionRun() {

		//die('-block-from-file-defacto-');
		// /ecommerce/defacto/other/un-block/run
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";

		$excel = PHPExcel_IOFactory::load('defacto/unblocked/b2c/2024-09-13/block-and-unblock.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 2;
		$fileName = 'eCom-unBlock-2024-09-13';
		for ($i = $start; $i <= 100000; $i++) {
			$productBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
			$boxBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
			$qty = (integer)$excelActive->getCell('C' . $i)->getValue();
			$action = (string)$excelActive->getCell('D' . $i)->getValue();

			$productBarcode = trim($productBarcode);
			$boxBarcode = trim($boxBarcode);
			$action = trim($action);

			if ($productBarcode == null || $qty < 1 || $action != "активировать")  {
				continue;
			}

			$products = EcommerceStock::find()
									  ->andWhere([
										  'product_barcode'=>$productBarcode,
										  'box_address_barcode'=>$boxBarcode,
										  'status_availability'=>StockAvailability::BLOCKED,
										  'condition_type'=> StockConditionType::FULL_DAMAGED,
									  ])
									  ->limit($qty)
									  ->all();

			if(empty($products)) {
				$products = EcommerceStock::find()
										  ->andWhere([
											  'product_barcode'=>$productBarcode,
											  'box_address_barcode'=>$boxBarcode,
											  'status_availability'=>StockAvailability::YES,
											  'condition_type'=> StockConditionType::FULL_DAMAGED,
										  ])
										  ->limit($qty)
										  ->all();

			}

			if(empty($products)) {
				//VarDumper::dump($productBarcode,10,true);
				//VarDumper::dump($boxBarcode,10,true);
				//VarDumper::dump($qty,10,true);
				file_put_contents($fileName."-not-find.csv",$productBarcode.";".$boxBarcode.";".$qty.";".$action.";"."\n",FILE_APPEND);
			} else {
				//VarDumper::dump($productBarcode,10,true);
				foreach ($products as $product) {
					file_put_contents($fileName."-blocked.csv",$productBarcode.";".$boxBarcode.";".$qty.";".$action.";"."\n",FILE_APPEND);
					$product->status_availability = StockAvailability::YES;
					$product->condition_type = StockConditionType::UNDAMAGED;
					$product->note_message2 = $fileName;
					$product->save(false);
				}
			}
		}
		die(' return $this->render(test) ');
		return $this->render('test');// '<br />-END-TEST<br />';
	}
}