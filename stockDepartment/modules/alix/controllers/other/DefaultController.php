<?php

namespace stockDepartment\modules\intermode\controllers\other;

use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\components\TLHelper;
use Yii;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;

class DefaultController extends Controller
{

	public function actionFindDm()
	{
		// /intermode/other/default/finddm
		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('intermode/2025-11-21/dm.xlsx');
		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('intermode/2025-11-21/dm2.csv');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();
		$lines = [];
		$delimiter = ";";
		$content = "";
		$orderName = "DM";
		for ($i = 2; $i <= 61; $i++) {
			$dm1 = (string)$excelActive->getCell('A' . $i)->getValue();
			$dm2 = (string)$excelActive->getCell('B' . $i)->getValue();
			$dm1 = trim($dm1);
			$dm2 = trim($dm2);
			$dm1 = substr($dm1, 0, 27);
			$dm2 = substr($dm2, 0, 27);

			if (empty($dm1)) {
				continue;
			}

			$lines[] = [
				"dm1"=>$dm1,
				"dm2"=>$dm2,
			];
		}
		//file_put_contents("bbbbbbb.log",print_r($lines,true));
		//VarDumper::dump($lines,10,true);
		$clientStoreArray = TLHelper::getStoreArrayByClientID();
		foreach ($lines as $line) {
			$stockRows	 = Stock::find()
					 ->andWhere([
						"outbound_order_id"=>[
							78122,
							78123,
							78124,
							78125,
							78126,
							78127,
							78128,
							78129
						],
					])
					 ->andWhere('product_qrcode LIKE "%'.$line["dm1"].'%"')
				->asArray()
		 		 ->all();
			foreach ($stockRows as $stockRow) {
				$row = [
					$clientStoreArray[OutboundOrder::findOne($stockRow["outbound_order_id"])->to_point_id],
					$stockRow["box_barcode"],
					$stockRow["product_name"],
					$stockRow["product_barcode"],
				];
				$content .= implode($row,$delimiter).$delimiter."\n";
			}
		}

		$filename = "store-".$orderName.".csv";
		return Yii::$app->response->sendContentAsFile( $content,$filename);
	}


		public function actionDm()
	{
		// /intermode/other/default/dm
		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2025-08-26/00TK-000946_.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();
		$count = 0;
		$lines = [];
		for ($i = 2; $i <= 100000; $i++) {
			$article = $excelActive->getCell('A' . $i)->getValue();
			$barcode = $excelActive->getCell('B' . $i)->getValue();
			$dm = $excelActive->getCell('C' . $i)->getValue();
			$article = trim($article);
			$barcode = trim($barcode);
			$dm = trim($dm);
			$count++;
			
			if (empty($dm)) {
				continue;
			}
			
			$lines[$count]["article"] = $article;
			$lines[$count]["barcode"] = $barcode;
			$lines[$count]["dm"] = $dm;
		}
		$delimiter = ";";
		$content = "Style;Color;Description;UPC;Size;QTY;"."\n";
		$orderName = "00TK-000946_25_08_2025_DM_04";
		foreach ($lines as $line) {
			$row = [
				$line["article"], // Style
				$line["article"], // Color
				$line["article"], // Description
				$line["barcode"], // UPC
				$line["article"], // Size
				1, // QTY
				$line["dm"], // dm
			];
			$content .= implode($row,$delimiter).$delimiter."\n";
		}

		$filename = "inbound-order-".$orderName.".csv";
		return Yii::$app->response->sendContentAsFile( $content,$filename);

	}

	public function actionIndex()
	{
		// /intermode/other/default/index
		$id = "76358";
		$outbound = OutboundOrder::findOne($id);

		$outboundItem = OutboundOrderItem::find()->select("product_id,expected_qty")
										 ->andWhere(["outbound_order_id"=>$outbound->id])
										 ->andWhere("expected_qty != allocated_qty")
										 ->asArray()
										 ->all();
		$p = [];
		foreach ($outboundItem as $item) {
			$productOnStock = Stock::find()->select("product_barcode, product_model, product_color")
								   ->andWhere(["product_id"=>$item["product_id"]])
								   ->limit($item["expected_qty"])
								   ->asArray()
								   ->all();


			$p [] = [
				"product_barcode"=>$productOnStock[0]["product_barcode"],
				"product_model"=>$productOnStock[0]["product_model"],
				"product_color"=>$productOnStock[0]["product_color"],
				"qty"=>count($productOnStock),
				"expected_qty"=>$item["expected_qty"],
			];

		}
		$delimiter = ";";
		$content = "Style;Color;Description;UPC;Size;QTY;"."\n";
		$orderName = $outbound->order_number;
		foreach ($p as $line) {
			$row = [
				$line["product_model"], // Style
				$line["product_color"], // Color
				"-", // Description
				$line["product_barcode"], // UPC
				"-", // Size
				$line["qty"], // QTY
				$line["expected_qty"], // expected_qty
			];
			$content .= implode($row,$delimiter).$delimiter."\n";
		}

		$filename = "outbound-order-".$orderName.".csv";
		return Yii::$app->response->sendContentAsFile( $content,$filename);

//		VarDumper::dump($p,10,true);
//		die;

		return $this->render('index');
	}
}