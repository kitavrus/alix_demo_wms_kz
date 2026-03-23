<?php

namespace console\controllers;


use common\api\DeFactoSoapAPI;
use common\b2b\domains\checkBox\entities\CheckBox;
use common\b2b\domains\checkBox\entities\CheckBoxStock;
use common\clientObject\constants\Constants;
use common\clientObject\hyundaiAuto\inbound\service\InboundOrderService;
//use common\clientObject\hyundaiAuto\outbound\service\OutboundService;
//use common\clientObject\hyundaiAuto\outbound\validation\ValidationOutbound;
use common\clientObject\main\outbound\repository\OutboundRepository;
use common\clientObject\main\service\SpreadsheetService;
use common\components\BarcodeManager;
use common\components\DeliveryProposalManager;
use common\components\MailManager;
use common\components\OutboundManager;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\defacto\api\ECommerceAPI;
use common\ecommerce\defacto\api\ECommerceAPINew;
use common\ecommerce\entities\EcommerceStock;
use common\helpers\DateHelper;
use common\managers\base\AllocateManager;
use common\modules\audit\models\OutboundOrderAudit;
use common\modules\audit\models\StockAudit;
use common\modules\client\models\ClientEmployees;
use common\modules\outbound\service\OutboundBoxService;
use common\modules\placementUnit\models\InboundUnitAddress;
use common\modules\product\models\Product;
use common\modules\product\models\ProductBarcodes;
use common\modules\returnOrder\models\ReturnOrderItemProduct;
use common\modules\stock\models\Inventory;
use common\modules\stock\models\InventoryRows;
use common\modules\stock\models\RackAddress;
use common\modules\stock\models\StockExtraField;
use common\modules\stock\service\ChangeAddressPlaceService;
use common\modules\user\models\User;
use common\modules\warehouseAddress\repository\RackAddressRepository;
use dektrium\user\helpers\Password;
use stockDepartment\modules\crossDock\models\CrossDockSearch;
use stockDepartment\modules\outbound\models\ScanningForm;
use stockDepartment\modules\report\models\TlDeliveryProposalFormSearch;
use stockDepartment\modules\returnOrder\entities\TmpOrder\ReturnTmpOrder;
use stockDepartment\modules\sheetShipment\repositories\PlaceAddressRepository;
use stockDepartment\modules\wms\managers\defacto\api\CrossDockItemDTO;
use stockDepartment\modules\wms\managers\defacto\api\CrossDockItemService;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use stockDepartment\modules\wms\managers\defacto\api\ProductSerializeDataDTO;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\city\models\City;
use common\modules\client\models\Client;
use common\modules\crossDock\models\ConsignmentCrossDock;
use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItemProducts;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use stockDepartment\modules\outbound\models\AllocationListForm;

use stockDepartment\modules\report\models\TlDeliveryProposalSearchReportExport;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use Yii;
use yii\validators\EmailValidator;
use yii\web\Response;

use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use function React\Promise\all;
use yii\console\Controller;
class Request1 {

}

class Solution {

	/**
	 * @param Integer[] $nums
	 * @param Integer $target
	 * @return Integer[]
	 */
     static function  twoSum($nums, $target) {
		$result = [];
		$mapStore = [];
		foreach($nums as $k=>$value) {

			$diff = $target - $value;
			if(isset($mapStore[$diff])) {
				$result[0] = $k;
				$result[1] = $mapStore[$diff];
				return $result;
			} else  {
				$mapStore[$value] = $k;
			}
		}

		return $result;

	}
}

//class OneController extends \stockDepartment\components\Controller
class OneController extends Controller
{
//	public function actionIndex()
//	{
//		// other/one/index
//		$caseOne = [2, 7, 11, 15];
//		VarDumper::dump(Solution::twoSum($caseOne, 17));
//		return "";
//	}


	public function actionIndexTest()
	{
		// other/one/index-test
		$api = new ECommerceAPINew();
		$params['request'] = [
			'BusinessUnitId'=>$api->BUSINESS_UNIT_ID(),
		];

		$appointmentsResult = $api->GetAppointmentsV2($params);
//			VarDumper::dump($api->GetAppointments($params),10,true);
			VarDumper::dump($appointmentsResult,10,true);
			VarDumper::dump($appointmentsResult["response"],10,true);
			VarDumper::dump($appointmentsResult["response"]->GetAppointmentsV2Result,10,true);
			VarDumper::dump($appointmentsResult["response"]->GetAppointmentsV2Result->Data->GetAppointmentResponse,10,true);

			foreach ($appointmentsResult["response"]->GetAppointmentsV2Result->Data->GetAppointmentResponse as $key => $value) {
//				VarDumper::dump($key,10,true);
				VarDumper::dump($value->AppointmentBarcode,10,true);
				VarDumper::dump($value->AppointmentDate,10,true);
			}
//			VarDumper::dump($appointmentsResult["response"]["GetAppointmentsV2Result"],10,true);
//			VarDumper::dump($api->GetAppointmentsV2($params),10,true);

//		$params2['request'] = [
//			"BusinessUnitId"=>$api->BUSINESS_UNIT_ID(),
//			"ProcessRequestedB2CDataType"=>"Full",
//			"ChangeDate"=>"Changed",
//		];

//		$r = new \stdClass();
//		$r->request = new \stdClass();
//		$r->request->BusinessUnitId = $api->BUSINESS_UNIT_ID();
//		$r->request->ProcessRequestedB2CDataType = "Full";
//		$r->request->ChangeDate = "Changed";


//			VarDumper::dump($api->GetProductInfo($params2),10,true);
//			VarDumper::dump($api->GetProductInfoV2($params2),10,true);

		return "-END-";
	}

/*
*
* */
	public function actionIndex()
	{
		$integerM3BoxM3 = \common\components\BarcodeManager::getIntegerM3(0.0865);
		VarDumper::dump($integerM3BoxM3,10,true);
		$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize($integerM3BoxM3);
//		$integerM3BoxM3 = \common\components\BarcodeManager::getIntegerM3(0.096);
//		$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize($integerM3BoxM3);
//		VarDumper::dump($integerM3BoxM3,10,true);
		VarDumper::dump($boxSize,10,true);
		die;
		return $this->render('index');
	}


	public function actionAaa()
	{
		// other/one/aaa
		$inventoryID = 1;
		$placeAddressList = CheckBox::find()
//		$placeAddressList = CheckBoxStock::find()
									->select("DISTINCT `place_address`")
									->andWhere(["inventory_id" => $inventoryID])
									->groupBy("place_address")
									->asArray()
									->column();

		$uniquePlaceAddress = [];
		foreach ($placeAddressList as $place) {
//			VarDumper::dump($place,10,true);
//			die;
			$sa = explode('-', trim($place));
			$stage = preg_replace('/[^0-9]/', '', $sa['0']); // этаж
			$row = preg_replace('/[^0-9]/', '', $sa['1']); // ряд
			$uniquePlaceAddress[$stage . "-" . $row] = [
				"stage" => $stage,
				"row" => $row,
				"fullPlaceAddress" => $place,
			];
		}

		$fileHeader = "Ряд;	Адрес;	Короб;	Ожидали; Приняли;";
		file_put_contents("report-by-row-for-inventory-by-box.csv", $fileHeader . "\n", FILE_APPEND);
		foreach ($uniquePlaceAddress as $stagePlusRow => $place) {
			$allPlaceAddressList = Stock::find()
										->select("DISTINCT `primary_address`, `secondary_address`")
										->andWhere(["client_id" => 2, "status_availability" => 2])
										->andWhere("secondary_address LIKE '" . $stagePlusRow . "-%'")
										->asArray()
										->all();


			foreach ($allPlaceAddressList as $item) {

				$productInBoxDetail = CheckBox::find()
											  ->select("expected_qty,scanned_qty")
											  ->andWhere(["place_address" => $item["secondary_address"], "box_barcode" => $item["primary_address"]])
											  ->asArray()
											  ->one();

				$strToFile = $stagePlusRow . ";"
					. $item["secondary_address"] . ";"
					. $item["primary_address"] . ";";

				if (isset($productInBoxDetail)) {
					if ($productInBoxDetail["expected_qty"] != $productInBoxDetail["scanned_qty"]) {
						$strToFile .= $productInBoxDetail["expected_qty"] . ";" . $productInBoxDetail["scanned_qty"] . ";";
					} else {
						$strToFile .= "ДА" . ";" . "ДА" . ";";
					}
				} else {
					$strToFile .= "НЕТ;НЕТ;";
				}

				file_put_contents("report-by-row-for-inventory-by-box.csv", $strToFile . "\n", FILE_APPEND);
			}
			file_put_contents("report-by-row-for-inventory-by-box.csv", "\n", FILE_APPEND);

		}

//		$minMax = Inventory::getMinMaxSecondaryAddress("1-10-01-0");
//		$minMax = Inventory::getMinMaxSecondaryAddress("1-6-12-0");
//		VarDumper::dump($minMax,10,true);
//		VarDumper::dump($uniquePlaceAddress,10,true);
//		VarDumper::dump($placeAddressList,10,true);
		return "";
	}

	public function actionIndex1()
	{
		// other/one/index1
		// almaty-mart-mall
//        $format = "%08d-%04d-%04d-%04d-%012d";

//        $a1 = 1;
//        $a1_8 = (strlen($a1) == 8);
//        $a2 = 1;
//        $a2_4 = (strlen($a2) == 4);
//        $a3 = 1;
//        $a3_4 = (strlen($a3) == 4);
//        $a4 = 1;
//        $a4_8 = (strlen($a4) == 4);
//        $a5 = 123;
//        $a5_8 = (strlen($a4) == 8);
//
//        echo sprintf($format,$a1,$a2,$a3,$a4,$a5);
//        echo sprintf($format,$a1,$a2,$a3,$a4,$a5);
//        echo $this->generate_uuid() ;
//        die;
//  astana-mega-silk-way-mall
// almaty-adk-mall
		//return Password::hash("almaty-asi2"); // $2y$12$AKbv7hGkUWrV6GTAsKGb5.d8KIpgn7UDM.L9QXtAEv.rBbi.Aym7a
//        return Password::hash("__123456");// __123456 = $2y$12$1HY18LBMhaEwzF/Urjy1Iuk68yF7Vl0SAHrZ3BvBnBn55ZZRthjke
//		return Password::hash("aq12sw");// aq12sw = $2y$12$uCtwEYaB0KL3r5.3rhDFzOHsZZFq.zQDHysjFOFKF3hfiyUX81Km2
		return Password::hash("20Elena24@#_!");// aq12sw = $2y$12$uCtwEYaB0KL3r5.3rhDFzOHsZZFq.zQDHysjFOFKF3hfiyUX81Km2
	}

	public function actionDiffReport($lot)
	{
		// other/one/diff-report
		$lotBarcodeList = [$lot];
		$result = [];
		foreach ($lotBarcodeList as $lotBarcode) {
			$inboundItems = InboundOrderItem::find()->andWhere(['product_barcode' => $lotBarcode])->all();
			foreach ($inboundItems as $itemInbound) {

				$inbound = InboundOrder::find()->andWhere(['id' => $itemInbound->inbound_order_id])->one();

				$qtyStock = Stock::find()->andWhere(['product_barcode' => $itemInbound->product_barcode, 'inbound_order_item_id' => $itemInbound->id, 'inbound_order_id' => $itemInbound->inbound_order_id])->count();

				$result[$lotBarcode]['inbound'][] = [
					'InboundId' => ArrayHelper::getValue($inbound, 'id'),
					'InboundOrderNumber' => ArrayHelper::getValue($inbound, 'order_number'),
					'InboundParentOrderNumber' => ArrayHelper::getValue($inbound, 'parent_order_number'),
					'InboundExpectedQty' => ArrayHelper::getValue($inbound, 'expected_qty'),
					'InboundAcceptedQty' => ArrayHelper::getValue($inbound, 'accepted_qty'),

					'ItemId' => ArrayHelper::getValue($itemInbound, 'id'),
					'ItemExpectedQty' => $itemInbound->expected_qty,
					'ItemAcceptedQty' => $itemInbound->accepted_qty,

					'QtyOnStock' => $qtyStock,
				];

				$qtyStock = Stock::find()->andWhere(['product_barcode' => $itemInbound->product_barcode])->count();
				$result[$lotBarcode]['stock'][] = [
					'QtyAllOnStock' => $qtyStock,
				];


				$outboundItemList = OutboundOrderItem::find()->andWhere(['product_barcode' => $itemInbound->product_barcode])->all();


				foreach ($outboundItemList as $itemOutbound) {

					$outbound = OutboundOrder::find()->andWhere(['id' => $itemOutbound->outbound_order_id])->one();

					$qtyStock = Stock::find()->andWhere(['product_barcode' => $itemOutbound->product_barcode, 'outbound_order_item_id' => $itemOutbound->id, 'outbound_order_id' => $itemOutbound->outbound_order_id])->count();

					$result[$lotBarcode]['outbound'][] = [
						'OutboundId' => ArrayHelper::getValue($outbound, 'id'),
						'OutboundOrderNumber' => ArrayHelper::getValue($outbound, 'order_number'),
						'OutboundParentOrderNumber' => ArrayHelper::getValue($outbound, 'parent_order_number'),
						'OutboundExpectedQty' => ArrayHelper::getValue($outbound, 'expected_qty'),
						'OutboundAcceptedQty' => ArrayHelper::getValue($outbound, 'accepted_qty'),
						'OutboundAllocatedQty' => ArrayHelper::getValue($outbound, 'allocated_qty'),

						'ItemId' => ArrayHelper::getValue($itemOutbound, 'id'),
						'ItemExpectedQty' => $itemOutbound->expected_qty,
						'ItemAcceptedQty' => $itemOutbound->accepted_qty,
						'ItemAllocatedQty' => $itemOutbound->allocated_qty,

						'QtyOnStock' => $qtyStock,
					];

				}


			}
		}

		VarDumper::dump($result, 10, true);
	}

	public function actionInboundOrderCompare()
	{ // /other/one/inbound-order-compare
		// die(" other/one/inbound-order-compare - DIE");

		$izRow = [];
		$iz = InboundOrder::findOne('112496'); // из D10AA00165063  KZ-19-TR-061
		if ($items = InboundOrderItem::findAll(['inbound_order_id' => $iz->id, 'status' => [1]])) {
			foreach ($items as $item) {
				$izRow [] = [
					'inbound_id' => $item->inbound_order_id,
					'id' => $item->id,
					'product_barcode' => $item->product_barcode,
					'box_barcode' => $item->box_barcode,
					'accepted_qty' => $item->accepted_qty,
				];
			}
		}

		$vRow = [];
		$v = InboundOrder::findOne('112497'); // в D10AA00156472  KZ-19-TR-045
		if ($items = InboundOrderItem::findAll(['inbound_order_id' => $v->id])) {
			foreach ($items as $item) {
				$vRow [] = [
					'inbound_id' => $item->inbound_order_id,
					'id' => $item->id,
					'product_barcode' => $item->product_barcode,
					'box_barcode' => $item->box_barcode,
					'accepted_qty' => $item->accepted_qty,
				];
			}
		}

		$exist = [];
		$diffExist = [];
		foreach ($izRow as $izItem) {
			foreach ($vRow as $vItem) {
//                if ($izItem['product_barcode'] == $vItem['product_barcode'] && $izItem['box_barcode'] == $vItem['box_barcode'] && $izItem['accepted_qty'] == $vItem['accepted_qty']) {
				if ($izItem['product_barcode'] == $vItem['product_barcode'] && $izItem['box_barcode'] == $vItem['box_barcode']) {
					$exist [$izItem['product_barcode'] . '-' . $izItem['box_barcode']] = [
						'inbound_id' => $vItem['inbound_id'],
						'id' => $vItem['id'],
						'izId' => $izItem['id'],
						'izInboundId' => $izItem['inbound_id'],
						'product_barcode' => $izItem['product_barcode'],
						'box_barcode' => $izItem['box_barcode'],
						'accepted_qty' => $izItem['accepted_qty'],
					];
//                    $inboundItem = InboundOrderItem::find()->andWhere(['id' => $izItem['id'],'inbound_order_id' => $izItem['inbound_id']])->one();
//                    $inboundItem->product_composition =  $izItem['product_barcode'].'-'.$izItem['box_barcode'];
//                    $inboundItem->save(false);
				} else {
//                    $diffExist [$izItem['product_barcode'].'-'.$izItem['box_barcode']] = [
//                        'id'=>  $izItem['id'],
//                        'product_barcode'=>  $izItem['product_barcode'],
//                        'box_barcode'=>  $izItem['box_barcode'],
//                        'accepted_qty'=>  $izItem['accepted_qty'],
//                    ];
				}
			}
		}

		foreach ($exist as $item) {
			$inboundItem = InboundOrderItem::find()->andWhere(['id' => $item['id'], 'inbound_order_id' => $item['inbound_id']])->one();
//            $inboundItem->product_composition =  $item['product_barcode'].'-'.$item['box_barcode'];
			$inboundItem->product_composition = $item['izId'] . '-' . $item['izInboundId'];
//            $inboundItem->accepted_qty =  $item['accepted_qty'];
//            $inboundItem->save(false);

//            Stock::updateAll(
//                ['inbound_order_id'=>$item['id'],'inbound_order_item_id'=>$item['inbound_id']],
//                ['inbound_order_id'=>$item['izInboundId'],'inbound_order_item_id'=>$item['izId']]);
		} // `inbound_order_id` = '68704' AND `inbound_order_item_id` = '461662'


//        echo "izRow : <br />";
//        VarDumper::dump($izRow, 10, true);
		echo "<br />";
		echo "EXIST : <br />";
		VarDumper::dump($exist, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "DIFF EXIST : <br />";
		VarDumper::dump($diffExist, 10, true);
		die('YPA');

		VarDumper::dump($rows, 10, true);
		echo "<br />";
		echo "<br />";
		VarDumper::dump($outResult, 10, true);
	}

	/*
     *
     * */
	// получаем список отгруженных заказов
	// делаем заказы доступными для резерва
	// дорезервируем заказы из списка
	public function actionReAllocated() // TODO OK
	{
		die('START-DIE-actionReAllocated');
		// /other/one/re-allocated
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		$outboundIDsFree = [
			'5840',
			'5839',
			'5838',
			'5837',
			'5836',
			'5835',
		];

		$clientID = 2;
		$outboundAll = OutboundOrder::find()->select('id')
									->andWhere([
//                            'parent_order_number'=>$outboundIDsFree,
										'id' => $outboundIDsFree,
										'client_id' => $clientID,
									])
									->asArray()->column();

		VarDumper::dump($outboundAll, 10, true);
		if (!empty($outboundAll) && is_array($outboundAll)) {
			foreach ($outboundAll as $outboundOrderId) {
				echo $outboundOrderId . "<br />";
				if ($StockInOutboundAll = Stock::find()->andWhere(['client_id' => $clientID, 'outbound_order_id' => $outboundOrderId])->all()) { // !!!!!!!!
					foreach ($StockInOutboundAll as $stock) {
						//$stock->primary_address = $stock->box_barcode; // !!!!!!!!
						//$stock->secondary_address = '';// !!!!!!!!
						$stock->box_barcode = '';
						$stock->box_size_barcode = '';
						$stock->box_kg = '';
						$stock->box_size_m3 = '';
						$stock->outbound_order_id = '0';
						$stock->outbound_picking_list_id = '0';
						$stock->outbound_picking_list_barcode = '';
						$stock->scan_out_datetime = '';
						$stock->scan_out_employee_id = '0';
						$stock->status = Stock::STATUS_NOT_SET;
						$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
						$stock->save(false);
						echo $stock->id . ':' . "<br />";
					}
				}
			}
			echo 'OutboundOrderItem:' . "<br />";
			OutboundOrderItem::deleteAll(['outbound_order_id' => $outboundAll]);
			echo 'OutboundPickingLists:' . "<br />";
			OutboundPickingLists::deleteAll(['client_id' => $clientID, 'outbound_order_id' => $outboundAll]);
			///
			echo 'ConsignmentOutboundOrder:' . "<br />";
			ConsignmentOutboundOrder::deleteAll(['client_id' => $clientID, 'party_number' => $outboundIDsFree]);
			echo 'OutboundOrder:' . "<br />";
			OutboundOrder::deleteAll(['client_id' => $clientID, 'id' => $outboundAll]);

			//
			echo "-END-", "<br />";
		}

		return $this->render('index');
	}


	/*
     *
     * */
	public function actionAddInbound()
	{
		// add-inbound
		die('END DIE');
		$client_id = Client::CLIENT_DEFACTO;
//        $order_number = 'akmaral-20160218';
//        $consignmentInboundOrder =  ConsignmentInboundOrders::findOne();
//        $consignmentInboundOrder->client_id = Client::CLIENT_AKMARAL;
//        $consignmentInboundOrder->party_number = $order_number;
//        $consignmentInboundOrder->delivery_type = InboundOrder::DELIVERY_TYPE_CROSS_DOCK_A;
//        $consignmentInboundOrder->order_type = InboundOrder::ORDER_TYPE_INBOUND;
//        $consignmentInboundOrder->status = Stock::STATUS_INBOUND_NEW;
//        $consignmentInboundOrder->save(false);

		// $id =  16118
		// $id =  16360 Order 510398
		// $id =  16445 Order 510615
		// $id =  17650 Order	510675

		$inboundModel = InboundOrder::findOne(17650);
//        $inboundModel->client_id = $client_id;
//        $inboundModel->consignment_inbound_order_id = $consignmentInboundOrder->id;
//        $inboundModel->order_number = $order_number;
//        $inboundModel->parent_order_number = $order_number;
//        $inboundModel->status = Stock::STATUS_INBOUND_NEW;
//        $inboundModel->expected_qty += 297;
		$inboundModel->expected_qty += 20;
//        $inboundModel->expected_qty += 49;
//        $inboundModel->accepted_qty = '0';
//        $inboundModel->accepted_number_places_qty = '0';
//        $inboundModel->expected_number_places_qty = '0';
//        $inboundModel->order_type = InboundOrder::ORDER_TYPE_INBOUND;
//        $inboundModel->save(false);

		$inboundModelID = $inboundModel->id;
		$expectedQty = 0;

		$parsedData = [];
		$parsedData[] = [
			'barcode' => '9000004697030',
			'model' => 'F1105ABE',
			'qty' => '20',
			'name' => '',
		];
//        $parsedData[] = [
//            'barcode'=> '9000005029663',
//            'model'=> 'F2948AA5',
//            'qty'=> '13',
//            'name'=> '',
//        ];
//        $parsedData[] = [
//            'barcode'=> '9000005029687',
//            'model'=> 'F2949AA7',
//            'qty'=> '20',
//            'name'=> '',
//        ];

//        $parsedData[] = [
//            'barcode'=> '9000004986653',
//            'model'=> 'F3536AKZC',
//            'qty'=> '54',
//            'name'=> '',
//        ];
//        $parsedData[] = [
//            'barcode'=> '9000004866559',
//            'model'=> 'F1499AKZA',
//            'qty'=> '14',
//            'name'=> '',
//        ];
//        $parsedData[] = [
//            'barcode'=> '9000004744895',
//            'model'=> 'F2995AC',
//            'qty'=> '5',
//            'name'=> '',
//        ];
//        $parsedData[] = [
//            'barcode'=> '9000004986646',
//            'model'=> 'F3536AKZB',
//            'qty'=> '70',
//            'name'=> '',
//        ];
//
//        $parsedData[] = [
//            'barcode'=> '9000004986639',
//            'model'=> 'F3536AKZA',
//            'qty'=> '64',
//            'name'=> '',
//        ];
//        $parsedData[] = [
//            'barcode'=> '9000004697337',
//            'model'=> 'F1105AF',
//            'qty'=> '39',
//            'name'=> '',
//        ];
//
//        $parsedData[] = [
//            'barcode'=> '9000004986691',
//            'model'=> 'F3556AKZA',
//            'qty'=> '51',
//            'name'=> '',
//        ];


		foreach ($parsedData as $productData) {

			$ioi = new InboundOrderItem();
			$ioi->inbound_order_id = $inboundModelID;
			$ioi->product_barcode = $productData['barcode'];
			$ioi->product_name = $productData['name'];
			$ioi->product_model = $productData['model'];
			$ioi->expected_qty = $productData['qty'];
			$ioi->status = Stock::STATUS_INBOUND_NEW;
//            $ioi->save(false);

			$expectedQty += $ioi->expected_qty;

			for ($i = 1; $i <= $ioi->expected_qty; $i++) {

				$stock = new Stock();
				$stock->client_id = $client_id;
				$stock->inbound_order_id = $ioi->inbound_order_id;
				$stock->product_barcode = $ioi->product_barcode;
				$stock->product_model = $ioi->product_model;
				$stock->product_name = $ioi->product_name;
				$stock->status = Stock::STATUS_INBOUND_NEW;
				$stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
//                $stock->save(false);
			}
		}

//        InboundOrder::updateAll(['expected_qty' => $expectedQty], ['id' => $inboundModelID]);
//        echo "<br />" . "Приходная накладная успешно создана" . "<br />";
		die('-DIE-');
	}

	public function filterMail($emails, $filterMails)
	{
		if (!empty($emails) && is_array($emails)) {
			foreach ($emails as $key => $email) {
				if (in_array($email, $filterMails)) {
					unset($emails[$key]);
				}
			}
		}

		return $emails;
	}

	/*
*
* */
	public function actionIndexX()
	{ // /other/one/index
//        $emails [] = "EkaterinaYurchakova@Tupperware.com";
//        $emails [] = "kit@ya.com";
//        $filterMails[] = 'EkaterinaYurchakova@Tupperware.com';
//        $emails = $this->filterMail($emails, $filterMails);
//        VarDumper::dump($emails, 10, true);
//        die();
//        $s = "2220004150657";
//        $kzk3 = [];
//        $pathToCSVFile = 'tmp-file/defacto/16-11-2016/returnCSV.csv';
//        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
//            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//                $key = substr($data['1'],0,strlen($data['1'])-1);//
//                $kzk3[$key] = $data['1'];
//            }
//        }
//        VarDumper::dump($kzk3,10,true);
//        if(substr($s,0,3) == '222') {
//            echo "Yes";
//        }
//        echo substr($s,0,4);
//        echo count($kzk3);
//        echo "<br />";
//        echo count($kzkX);
//        die('END DIE');
//       $productAll =  Product::find()->andWhere(['client_id'=>Client::CLIENT_AKMARAL])->asArray()->all();
//
//        foreach($productAll as $product) {
//            if(!empty($product['name'])) {
//                $productBarcode = ProductBarcodes::find()->select('barcode')->andWhere(['product_id' => $product['id']])->scalar();
//                if ($stockAll = Stock::find()->andWhere(['product_barcode' => $productBarcode, 'client_id' => Client::CLIENT_AKMARAL])->all()) {
//                    foreach ($stockAll as $stock) {
//
//                        $stock->product_name = $product['name'];
//                        $stock->save(false);
//                    }
//                }
//            }
//        }
//
//        VarDumper::dump($productAll,10,true);

		return $this->render('index');
	}

	/*
*
* */
	public function actionSetEmptyPackingDate()
	{
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "Start:";
		echo "<br />";
		return 'Y';
		$all = OutboundOrder::find()->andWhere('date_left_warehouse != "" AND packing_date is NULL ')->limit(99999)->all();
		foreach ($all as $order) {
			echo $order->order_number . " " . $order->parent_order_number . " " . $order->id . "<br />";
			if ($audit = OutboundOrderAudit::find()->andWhere(['parent_id' => $order->id, 'before_value_text' => ['Напеч-ли этикетки на короба', 'Файл для API выгружен', 'Печатаются этикетки на короба']])->one()) {
				echo strtotime($audit->date_created) . "<br />";
				$order->packing_date = strtotime($audit->date_created);
				$order->save(false);
			}

		}

		return $this->render('index');
		die;
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";

		$client_id = 21;
		$stockAllQuery = Stock::find()
							  ->andWhere(['client_id' => $client_id, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])
							  ->andWhere('secondary_address != ""')
//            ->orderBy(['secondary_address'=>SORT_ASC])
							  ->orderBy([
				'address_sort_order' => SORT_ASC,
				'primary_address' => SORT_DESC,
			])/*->limit(500)*/
		;
		//->all();
		$addresses = [];
		foreach ($stockAllQuery->batch() as $stocks) {
			foreach ($stocks as $stock) {
				$sa = explode('-', trim($stock->secondary_address));
				$stage = preg_replace('/[^0-9]/', '', $sa['0']); // этаж
				$row = preg_replace('/[^0-9]/', '', $sa['1']); // ряд
				//$rack = preg_replace('/[^0-9]/', '',$sa['2']); // полка
				//$level = preg_replace('/[^0-9]/', '',$sa['3']); // уровень

				$addresses[$stage . '-' . $row][$stock->secondary_address][$stock->primary_address] = $stock->primary_address;

				//echo $stock->secondary_address . "<br />";

				//file_put_contents('KOTON-NEW-OUTBOUND-1.CSV', $stock->secondary_address . "\n", FILE_APPEND);
			}
		}

//        VarDumper::dump(Inventory::getMinMaxSecondaryAddress('3-11-01-0'),10,true);
//        VarDumper::dump($addresses,10,true);

		foreach ($addresses as $key => $address) {
			foreach ($address as $primKey => $boxes) {
				foreach ($boxes as $box) {
					$row = $primKey . ';' . $box . ';';
					echo $row . "<br />";
					file_put_contents('KOTON-NEW-OUTBOUND-3.CSV', $row . "\n", FILE_APPEND);
				}
			}
		}

		//       foreach($addresses as $key=>$address) {

//            $newCoo = new ConsignmentOutboundOrder();
//            $newCoo->setAttributes([
//                'client_id' => $client_id,
//                'party_number' => 'out-of-stock' . '-' . $key,
//                'status' => Stock::STATUS_OUTBOUND_NEW,
//                'expected_qty' => 0,
//                'allocated_qty' => 0,
//            ], false);
//            $newCoo->save(false);
//
//
//            $partyNumber = $newCoo->party_number;
//            $orderNumber =  $newCoo->party_number;
//
//            $oManager = new OutboundManager();
//            $oManager->initBaseData($client_id, $partyNumber, $orderNumber);
//            $oManager->setConsignmentID($newCoo->id);
//
//            $newOutboundOrder  = new OutboundOrder();
//            $newOutboundOrder->setAttributes([
//                'client_id'=> $client_id,
//                'from_point_id'=> 4,
//                'to_point_id'=>4,
//                'order_number'=> $orderNumber,
//                'parent_order_number'=> $partyNumber,
//                'consignment_outbound_order_id'=> $newCoo->id,
//                'status'=> Stock::STATUS_OUTBOUND_NEW,
//                'cargo_status'=> 2,
//                'expected_qty'=> 0,
//                'allocated_qty'=> 0,
//                'accepted_qty'=> 0,
//            ],false);
//            $newOutboundOrder->save(false);
//
//
//            $stockAll =  Stock::find()
//                ->select('id, product_barcode, count(*) as qty')
//                ->andWhere(['client_id'=>$client_id,'secondary_address'=>$address,'status_availability'=>Stock::STATUS_AVAILABILITY_YES])
//                ->groupBy('product_barcode')
//                ->asArray()
//                ->all();
//
//            $expectedQtyConsignmentNew = 0;
//            $expectedQtyOutboundNew = 0;
//
//            foreach($stockAll as $stock) {
//                $newOutboundOrderItem = new OutboundOrderItem();
//                $newOutboundOrderItem->setAttributes([
//                    'outbound_order_id' => $newOutboundOrder->id,
//                    'product_barcode' =>$stock['product_barcode'],
//                    'status' => Stock::STATUS_OUTBOUND_NEW,
//                    'expected_qty' => $stock['qty'],
//                    'allocated_qty' => 0,
//                    'accepted_qty' => 0,
//                ], false);
//                $newOutboundOrderItem->save(false);
//
//                $expectedQtyConsignmentNew += $stock['qty'];
//                $expectedQtyOutboundNew += $stock['qty'];
//            }
//
//            $newOutboundOrder->expected_qty = $expectedQtyOutboundNew;
//            $newOutboundOrder->save(false);
//
//            $newCoo->expected_qty = $expectedQtyOutboundNew;
//            $newCoo->save(false);
//
//            $oManager->createUpdateDeliveryProposalAndOrder();
//            $oManager->reservationOnStockByPartyNumber();
		//}

		echo "-Y-";
		return $this->render('index');
	}

	/*
*
* */
	public function actionFindDubleBox()
	{
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo '-START-';
		echo "<br />";
		die;
//        $orders = [
//            '11649'=>'509640',
//            '11644'=>'509648-509649',
//        ];
		$ids = [115026];
		$box = [];
//        foreach($orders as $id=>$order) {
		$stockAll = Stock::find()->select('primary_address')->andWhere([
			'inbound_order_id' => $ids,
//                'status'=>[
//                    Stock::STATUS_INBOUND_SCANNED,
//                    Stock::STATUS_INBOUND_OVER_SCANNED,
//                ]
		])->groupBy('primary_address')->column();


//        VarDumper::dump($stockAll,10,true);
//        die;
		foreach ($stockAll as $primary_address) {

			$stockAllsub = Stock::find()->select('id, inbound_order_id, primary_address')->andWhere([
				'inbound_order_id' => $ids,
				'primary_address' => $primary_address,
//                    'status'=>[
//                        Stock::STATUS_INBOUND_SCANNED,
//                        Stock::STATUS_INBOUND_OVER_SCANNED,
//                    ]
//                ])->andWhere('inbound_order_id != inbound_order_id')->asArray()->all();
			])->asArray()->all();
			$uniID = [];
			if (!empty($stockAllsub)) {
				foreach ($stockAllsub as $b) {
//                        echo $b['inbound_order_id'].' '.$b['primary_address'].' '.$b['id']."<br />";
					$uniID [$b['inbound_order_id']] = $b['inbound_order_id'];
					if (count($uniID) > 1) {
						echo $b['inbound_order_id'] . ' ' . $b['primary_address'] . "<br />";
						$box[$b['primary_address']] = $b['primary_address'];
					}
				}
//                    echo "<br />";
//                    echo "<br />";
			}
//                VarDumper::dump($stockAllsub,10,true);
		}

		foreach ($box as $b) {
			echo $b . "<br />";
		}

//        }


		echo '-END-';
		return $this->render('index');
	}

	/*
*
* */
	public function actionFindDubleBoxInAddress()
	{
		// /other/one/FindDubleBoxInAddress
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo '-START-';
		echo "<br />";
//        die;
		$id = 115120;
		$box = [];
		$toBox = [];
		$stockAll = Stock::find()->select('primary_address, secondary_address, inbound_client_box')->andWhere([
			'inbound_order_id' => $id,
//                'status'=>[
//                    Stock::STATUS_INBOUND_SCANNED,
//                    Stock::STATUS_INBOUND_OVER_SCANNED,
//                ]
		])
			//->groupBy('primary_address, secondary_address')
						 ->all();

//        VarDumper::dump($stockAll,10,true);
//        die;

		foreach ($stockAll as $item) {
			$box[$item['primary_address']][$item['secondary_address']] = $item["inbound_client_box"];
		}

		foreach ($box as $item) {
			if (count($item) > 1) {
				VarDumper::dump($item, 10, true);
			}
		}

//		VarDumper::dump($box,10,true);


		echo '-END-';
		return $this->render('index');
	}

	/*
    *
    * */
	public function actionKotonInboundConfirm()
	{
		// koton-inbound-confirm
		return 'NO';
		$client_id = 21;
		$conInboundOrderAll = ConsignmentInboundOrders::find()->andWhere(['client_id' => $client_id])->all();
		$boxs = [];
		foreach ($conInboundOrderAll as $conInboundOrder) {
			$inboundOrderAll = InboundOrder::find()->andWhere(['consignment_inbound_order_id' => $conInboundOrder->id, 'status' => [
				Stock::STATUS_INBOUND_SCANNED,
				Stock::STATUS_INBOUND_OVER_SCANNED,
				Stock::STATUS_INBOUND_SCANNING,
//                Stock::STATUS_INBOUND_NEW,
			]])->all();

			if ($inboundOrderAll) {
				foreach ($inboundOrderAll as $inboundOrder) {

					////// BEGIN
//                    $inboundOrder->status = Stock::STATUS_INBOUND_CONFIRM;
//                    $inboundOrder->date_confirm = time();
//                    $inboundOrder->save(false);
//
//                    Stock::updateAll([
//                        'status'=>Stock::STATUS_INBOUND_CONFIRM,
//                        'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
//                    ],[
//                        'inbound_order_id'=>$inboundOrder->id,
//                        'status'=>[
//                            Stock::STATUS_INBOUND_SCANNED,
//                            Stock::STATUS_INBOUND_OVER_SCANNED,
//                        ]
//                    ]);
//
//                    Stock::deleteAll('inbound_order_id = :inbound_order_id AND status != :status',[':inbound_order_id'=>$inboundOrder->id,':status'=>Stock::STATUS_INBOUND_CONFIRM]);
//
//                    if($coi = ConsignmentInboundOrders::findOne($inboundOrder->consignment_inbound_order_id)) {
//                        $coi->status = Stock::STATUS_INBOUND_SCANNING;
//                        if(!InboundOrder::find()->andWhere('status != :status AND consignment_inbound_order_id = :consignment_inbound_order_id',[':status'=>Stock::STATUS_INBOUND_CONFIRM,':consignment_inbound_order_id'=>$inboundOrder->consignment_inbound_order_id])->exists()) {
//                            $coi->status = Stock::STATUS_INBOUND_CONFIRM;
//                        }
//                        $coi->save(false);
//                    }

					////// END

					$inboundStockAll = [];
					$inboundStockAll = Stock::find()->andWhere([
						'inbound_order_id' => $inboundOrder->id,
						'status' => [
							Stock::STATUS_INBOUND_SCANNED,
							Stock::STATUS_INBOUND_OVER_SCANNED,
						]
					])->all();

					if ($inboundStockAll) {
						foreach ($inboundStockAll as $inboundStock) {
							//echo $inboundStock->product_barcode.' '.$inboundStock->primary_address.' '.$inboundStock->secondary_address."<br />";
							file_put_contents('inTEXT2.csv', $inboundStock->product_barcode . ';' . $inboundStock->primary_address . ';' . $inboundStock->secondary_address . ';' . "\n", FILE_APPEND);

							if (empty($inboundStock->secondary_address)) {
								$boxs [$inboundStock->primary_address] = $inboundStock->primary_address;
								file_put_contents('inTEXT2-empty.csv', $inboundStock->product_barcode . ';' . $inboundStock->primary_address . ';' . $inboundStock->secondary_address . ';' . "\n", FILE_APPEND);
							}
						}
					}
				}
			}
		}

		foreach ($boxs as $box) {
			file_put_contents('inTEXT2-empty-uni.csv', $box . "\n", FILE_APPEND);
		}
		return '-END-';
	}

	/*
    *
    * */
	public function actionReCreateKotonOutbound()
	{
		return 'NO';
		// re-create-koton-outbound
		$client_id = 21;
		$orders = [
			'182' => '20160114-21-40',
			'183' => '20160114-21-41',
			'184' => '20160114-21-42',
			'185' => '20160114-21-43',
			'186' => '20160114-21-44',
			'187' => '20160114-21-45',
			'188' => '20160114-21-46',
			'189' => '20160114-21-47',
			'190' => '20160114-21-48',
			'191' => '20160114-21-49',
			'192' => '20160114-21-50',
			'193' => '20160114-21-51',
		];

		foreach ($orders as $id => $orderNumber) {
			$coOrderAll = ConsignmentOutboundOrder::find()->andWhere(['id' => $id])->all();
			if ($coOrderAll) {
				foreach ($coOrderAll as $coOrderNumber) {

					$newCoo = new ConsignmentOutboundOrder();
					$newCoo->setAttributes([
						'client_id' => $coOrderNumber->client_id,
						'party_number' => $coOrderNumber->party_number . '-1',
						'status' => Stock::STATUS_OUTBOUND_NEW,
						'expected_qty' => 0,
						'allocated_qty' => 0,
					], false);
					$newCoo->save(false);

//                    $data = [];
					$partyNumber = $newCoo->party_number;
					$orderNumber = $newCoo->party_number;

					$oManager = new OutboundManager();
					$oManager->initBaseData($client_id, $partyNumber, $orderNumber);
					$oManager->setConsignmentID($newCoo->id);

					$ooAll = OutboundOrder::find()->andWhere(['consignment_outbound_order_id' => $coOrderNumber->id])->all();
					if ($ooAll) {
						foreach ($ooAll as $oOrderNumber) {
							$xQty = 0;
							$newOutboundOrder = new OutboundOrder();
							$newOutboundOrder->setAttributes([
								'client_id' => $coOrderNumber->client_id,
								'from_point_id' => $oOrderNumber->from_point_id,
								'to_point_id' => $oOrderNumber->to_point_id,
								'order_number' => $newCoo->party_number,
								'parent_order_number' => $newCoo->party_number,
								'consignment_outbound_order_id' => $newCoo->id,
								'status' => Stock::STATUS_OUTBOUND_NEW,
								'cargo_status' => 2,
								'expected_qty' => 0,
								'allocated_qty' => 0,
								'accepted_qty' => 0,
							], false);
							$newOutboundOrder->save(false);

							$oManager->setOutboundID($newOutboundOrder->id);

							$expectedQtyConsignmentNew = 0;
							$expectedQtyOutboundNew = 0;


							$ooIAll = OutboundOrderItem::find()->andWhere(['outbound_order_id' => $oOrderNumber->id])->all();
							if ($ooIAll) {
								foreach ($ooIAll as $oIOrderNumber) {
									$expected_qtyNew = ($oIOrderNumber->expected_qty - $oIOrderNumber->accepted_qty);
									if ($expected_qtyNew) {
										$newOutboundOrderItem = new OutboundOrderItem();
										$newOutboundOrderItem->setAttributes([
											'outbound_order_id' => $newOutboundOrder->id,
											'product_barcode' => $oIOrderNumber->product_barcode,
											'status' => Stock::STATUS_OUTBOUND_NEW,
											'expected_qty' => $expected_qtyNew,
											'allocated_qty' => 0,
											'accepted_qty' => 0,
										], false);
										$newOutboundOrderItem->save(false);

										$expectedQtyConsignmentNew += $expected_qtyNew;
										$expectedQtyOutboundNew += $expected_qtyNew;
									}
								}

								$newOutboundOrder->expected_qty = $expectedQtyOutboundNew;
								$newOutboundOrder->save(false);

								$oOrderNumber->expected_qty = ($newOutboundOrder->expected_qty + $oOrderNumber->accepted_qty);
								$oOrderNumber->save(false);
							}
						}

						$newCoo->expected_qty = $expectedQtyConsignmentNew;
						$newCoo->save(false);

						$coOrderNumber->expected_qty = $oOrderNumber->expected_qty;
						$coOrderNumber->save(false);

						$oManager->createUpdateDeliveryProposalAndOrder();
						$oManager->reservationOnStockByPartyNumber();
					}
				}
			}
		}


		return 'INDEX';
	}

	/*
*
* */
	public function actionCancelInboundOrder()
	{
		return 'cancel-inbound-order-NO-RUN';
		$client_id = '21';
		$orders [] = '20151225-263-21101';
		$cioAll = ConsignmentInboundOrders::find()->andWhere(['party_number' => $orders, 'client_id' => $client_id])->all();

		if ($cioAll) {
			foreach ($cioAll as $cioItem) {
				$ioAll = InboundOrder::find()->andWhere(['consignment_inbound_order_id' => $cioItem->id])->all();
				if ($ioAll) {
					foreach ($ioAll as $io) {
						// TODO Добавить возможность отменять приходы
						//InboundOrderItem::find()->andWhere(['inbound_order_id'=>$io->id])->all();
						//Stock::find()->andWhere(['inbound_order_id'=>$io->id])->all();
						InboundOrderItem::deleteAll(['inbound_order_id' => $io->id]);
						Stock::deleteAll(['inbound_order_id' => $io->id]);
						$io->delete();
					}
				}
				$cioItem->delete();
			}
		}

		return 'cancel-inbound-order-RETURN';
	}


	/*
    *
    * */
	public function actionBelgeIdDefactoReport()
	{
		return 'NO';
		echo "<br />";
		echo "<br />";
		echo "<br />";
		$files = [
//            'tmp-file/defacto/2015-12-23/BelgeID.csv',
			'tmp-file/defacto/2015-12-23/iade-kabul-belge-ID.csv',
		];

		foreach ($files as $pathToCSVFile) {
			$row = 0;
			if (($handle = fopen($pathToCSVFile, "r")) !== false) {
				while (($data = fgetcsv($handle, 5000, "	")) !== false) {
					$row++;
					VarDumper::dump($data, 10, true);
					$product_barcode = $data['0'];
					$s = Stock::find()->andWhere(['product_barcode' => $product_barcode])->one();
					$i = InboundOrder::find()->andWhere(['id' => $s->inbound_order_id])->one();
					file_put_contents('iade-kabul-belge-ID-report1.csv', '"' . $product_barcode . '";"' . $i->order_number . '"' . "\n", FILE_APPEND);

					if ($i && $s) {
						echo "YES" . "<br />";
					} else {
						echo "NO" . "<br />";
					}

				}
			}
		}

		return 'belge-id-defacto-report';
	}

	public function actionDeleteReturnByFileDefacto()
	{
		return 'NO';
		echo "<br />";
		echo "<br />";
		echo "<br />";
		return "Выполнен на живом, повтороно выполнять ненужно";

		$files = [
			'tmp-file/defacto/2015-12-23/Book1-2.csv',
		];

		foreach ($files as $pathToCSVFile) {
			$row = 0;
			if (($handle = fopen($pathToCSVFile, "r")) !== false) {
				while (($data = fgetcsv($handle, 5000, "	")) !== false) {
					$row++;
					VarDumper::dump($data, 10, true);
					$order_number = $data['0'];
					$product_barcode = $data['1'];
					$r = ReturnOrder::find()->andWhere(['order_number' => $order_number])->one();
					$i = InboundOrder::find()->andWhere(['order_number' => $order_number])->one();

					$s = '';
					if ($i) {
						$s = Stock::find()->andWhere(['product_barcode' => $product_barcode, 'inbound_order_id' => $i->id])->one();
					}

					if ($r && $i && $s) {
						echo "YES" . "<br />";
//                        echo "YES DELETE"."<br />";
//                        $r->delete();
//                        $i->delete();
//                        $s->delete();
//                        echo $s->status_availability."<br />";
					} else {
						echo "NO" . "<br />";
					}
					echo $row . "<br />";
				}
			}
		}

		return 'delete-return-by-file-defacto';
	}

	/*
    *
    * */
	public function actionIndexKoton()
	{
		return 'NO';
		echo "<br />";
		echo "<br />";
		echo "<br />";
		$files = [
//            'tmp-file/koton/2/5825070.txt',
			'tmp-file/koton/2/7145061.txt',
		];
		$rowFile = '';
		$boxes = [];
		foreach ($files as $pathToCSVFile) {
			$row = 0;
			$rowFile = trim(basename($pathToCSVFile));
			if (($handle = fopen($pathToCSVFile, "r")) !== false) {
				while (($data = fgetcsv($handle, 5000, "	")) !== false) {
					$row++;

					VarDumper::dump($data, 10, true);

					for ($i = 1; $i <= $data['1']; $i++) {
						file_put_contents('7145061-done.csv', $data['0'] . "\n", FILE_APPEND);
					}

//                    $parsedData = [];
//                    foreach ($data as $d) {
//                        if (!empty($d)) {
//                            $parsedData[] = $d;
//                        }
//                    }

//                    if (!empty($parsedData) && isset($parsedData[3])) {
//                        if (isset($boxes[$rowFile][$parsedData[3]])) {
//                            $boxes[$rowFile][$parsedData[3]] += 1;
//                        } else {
//                            $boxes[$rowFile][$parsedData[3]] = 1;
//                        }
//                    }
//                    VarDumper::dump($parsedData, 10, true);
				}
			}
//            VarDumper::dump($boxes, 10, true);
		}

		return 'INDEX';
	}

	public function actionRestoreResetedOrder()
	{
		// other/one/restore-reseted-order

		die('-- Die actionRestoreResetedOrder');
		$dbReset = Yii::$app->dbReset;
		$clientId = 2;
		$partyNumber = '407243';
//        $outboundOrdersOld = OutboundOrder::find()->where(['client_id' =>$clientId, 'parent_order_number' =>$partyNumber])->asArray()->all($dbReset);
		$outboundOrdersOld = OutboundOrder::find()->where(['id' => [24631, 24630, 24629, 24628, 24627]])->asArray()->all($dbReset);
//        $outboundOrdersOld = OutboundOrder::find()->where(['id' =>[24626,24625,24624,24623,24622]])->asArray()->all($dbReset);
		foreach ($outboundOrdersOld as $orderOld) {


			// Stock done
//            $stocksOld  = Stock::find()->where(['outbound_order_id'=>$orderOld])->asArray()->all($dbReset);
//            foreach($stocksOld as $stockOld) {
//                $stock = Stock::find()->where(['id'=>$stockOld['id']])->one();
//                $stock->setAttributes($stockOld,false);
//                $stock->save(false);
//            }

			// Outbound Item Done
//           $OutboundOrderItemsOld =  OutboundOrderItem::find()->where(['outbound_order_id'=>$orderOld])->asArray()->all($dbReset);
//
//            foreach($OutboundOrderItemsOld as $itemOld) {
//                $item = OutboundOrderItem::find()->where(['id'=>$itemOld['id']])->one();
//                $item->setAttributes($itemOld,false);
//                $item->save(false);
//            }

			// Outbound Order Done
//            $outboundOrderNew = OutboundOrder::find()->where(['id' =>$orderOld['id']])->one();
//            $outboundOrderNew->setAttributes($orderOld,false);
//            $outboundOrderNew->save(false);

		}

//        $outboundOrderNew = OutboundOrder::find()->where(['client_id' =>$clientId, 'parent_order_number' =>$partyNumber])->one();

//        VarDumper::dump($outboundOrder,10,true);
		die('OK');
	}

	/*
    *
    * */
	public function actionDemoChartDelivery()
	{
		return 'NO';
		$searchModel = new TlDeliveryProposalFormSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$clientArray = Client::getActiveItems();
		$storeArray = TLHelper::getStockPointArray();

		$client_id = 2;
		if (!empty($searchModel->client_id)) {
			$client_id = $searchModel->client_id;
		} else {
			$searchModel->client_id = $client_id;
		}

		// SHIPPED DATETIME
		$dateFrom = '';
		$dateTo = '';
		if (!empty($searchModel->shipped_datetime)) {
			$date = explode('/', $searchModel->shipped_datetime);

			$dateFrom = trim($date[0]) . ' 00:00:00';
			$dateTo = trim($date[1]) . ' 23:59:59';

			$dateFrom = strtotime($dateFrom);
			$dateTo = strtotime($dateTo);
		}

		$cityStoreAll = Store::find()
							 ->select('city_id, GROUP_CONCAT(id) as ids')
							 ->andWhere(['client_id' => $client_id, 'type_use' => Store::TYPE_USE_STORE])
							 ->groupBy('city_id')
							 ->asArray()
							 ->all();

		foreach ($cityStoreAll as $store) {

			$queryDP = TlDeliveryProposal::find();

			if (!empty($dateFrom) && !empty($dateTo)) {
				$queryDP->andWhere(['between', 'shipped_datetime', $dateFrom, $dateTo]);
			}

			$dps = $queryDP->andWhere(['client_id' => $client_id, 'route_from' => 4, 'route_to' => explode(',', $store['ids'])])->all();

			$i = 1;
			$statusKey[$store['city_id']]['tariff_not_found'] = [
				'qty' => 0,
				'title' => 'Тариф не найден'
			];
			$statusKey[$store['city_id']]['no_delivery_time'] = [
				'qty' => 0,
				'title' => 'Нет сроков доставки'
			];
			$statusKey[$store['city_id']]['no_delivery_date'] = [
				'qty' => 0,
				'title' => 'Нет даты доставки'
			];
			$statusKey[$store['city_id']]['in_time'] = [
				'qty' => 0,
				'title' => 'В срок'
			];
			$statusKey[$store['city_id']]['more_delivery_time'] = [
				'qty' => 0,
				'title' => 'Больше сроков доставки'
			];
//            $statusKey[$store['city_id']]['less_delivery_time'] = [
//                'qty'=>0,
//                'title'=>'Меньше сроков доставки'
//            ];
//            $statusKey[$store['city_id']]['all'] = [
//                'qty'=>0,
//                'title'=>'Всего'
//            ];

			foreach ($dps as $model) {

				$billing = TlDeliveryProposalBilling::find()
													->select('delivery_term, delivery_term_from, delivery_term_to, id')
													->andWhere(
														[
															'client_id' => $model->client_id,
															'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
															'route_from' => $model->route_from,
															'route_to' => $model->route_to,
														]
													)
													->one();

				$i++;
				$daysOnWay = $model->calculateDiffTR();

				if ($billing) {
					if (!empty($model->delivery_date) && !empty($model->shipped_datetime)) {
						if (empty($billing->delivery_term_from) || empty($billing->delivery_term_to)) {
							$statusKey[$store['city_id']]['no_delivery_time']['qty'] += 1;
						} elseif ($daysOnWay >= (int)$billing->delivery_term_from && $daysOnWay <= (int)$billing->delivery_term_to) {
							$statusKey[$store['city_id']]['in_time']['qty'] += 1;
						} elseif ($daysOnWay < $billing->delivery_term_from) {
							$statusKey[$store['city_id']]['in_time']['qty'] += 1;

						} else {
							$statusKey[$store['city_id']]['more_delivery_time']['qty'] += 1;
						}
					} else {
						$statusKey[$store['city_id']]['no_delivery_date']['qty'] += 1;
					}
				} else {
					// TODO Что писать если не заданы сроки доставки
					$statusKey[$store['city_id']]['tariff_not_found']['qty'] += 1;
				}
//                $statusKey[$store['city_id']]['all']['qty'] += 1;
			}
		}

		$statusKeyColumn = [];
		$statusKeyColumn['tariff_not_found'] = [
			'title' => 'Тариф не найден'
		];
		$statusKeyColumn['no_delivery_time'] = [
			'title' => 'Нет сроков доставки'
		];
		$statusKeyColumn['no_delivery_date'] = [
			'title' => 'Нет даты доставки'
		];
		$statusKeyColumn['in_time'] = [
			'title' => 'В срок'
		];
		$statusKeyColumn['more_delivery_time'] = [
			'title' => 'Больше сроков доставки'
		];
//        $statusKeyColumn['less_delivery_time'] = [
//            'title'=>'Меньше сроков доставки'
//        ];
//        $statusKeyColumn['all'] = [
//            'title'=>'Всего'
//        ];
		$cityAll = [];
		$columns = [];
		$columns[0][] = 'x';
		foreach ($statusKey as $key => $value) {
			$cityName = City::findOne($key)->name;
			$columns[0][] = $cityName;
			$cityAll[$key] = $cityName;
		}
//        $columns[0][] = 'Всего'; // all

		$groups = [];
		$names = [];
		$k = 0;
		foreach ($statusKeyColumn as $keyColumn => $valueColumn) {
//
			$title = $valueColumn['title'];
			$groups[0][] = $keyColumn;
			$k++;
			$columns[$k][] = $keyColumn;
			$qty = 0;

			$names[$keyColumn] = $title;

			foreach ($statusKey as $key => $value) {
				$columns[$k][] = $value[$keyColumn]['qty'];
				$qty += $value[$keyColumn]['qty'];
			}
//            $columns[$k][] = $qty;// all
		}

		$columnsByCity = $columns;
		$groupsByCity = $groups;
		$namesByCity = $names;

		$storeM3 = [];
		foreach ($cityStoreAll as $store) {

			$queryDP = TlDeliveryProposal::find();
			if (!empty($dateFrom) && !empty($dateTo)) {
				$queryDP->andWhere(['between', 'shipped_datetime', $dateFrom, $dateTo]);
			}
			$dps = $queryDP->andWhere(['client_id' => $client_id, 'route_from' => 4, 'route_to' => explode(',', $store['ids'])])->all();

			foreach ($dps as $model) {
				if (isset($storeM3[$store['city_id']])) {
					$storeM3[$store['city_id']] += $model->mc_actual;
				} else {
					$storeM3[$store['city_id']] = $model->mc_actual;
				}
			}
		}

		$columns = [];
		$columns[0][] = 'x';
		$columns[1][] = 'по М3';
		$qty = 0;
		foreach ($storeM3 as $cityID => $qtyInCity) {
			$columns[0][] = City::findOne($cityID)->name;
			$columns[1][] = $qtyInCity;
			$qty += $qtyInCity;
		}

		$columnsM3 = $columns;


		$storeM3 = [];
		foreach ($cityStoreAll as $store) {

			$queryDP = TlDeliveryProposal::find();
			if (!empty($dateFrom) && !empty($dateTo)) {
				$queryDP->andWhere(['between', 'shipped_datetime', $dateFrom, $dateTo]);
			}
			$dps = $queryDP->andWhere(['client_id' => $client_id, 'route_from' => 4, 'route_to' => explode(',', $store['ids'])])->all();

			foreach ($dps as $model) {
				if (isset($storeM3[$store['city_id']])) {
					$storeM3[$store['city_id']] += $model->number_places_actual;
				} else {
					$storeM3[$store['city_id']] = $model->number_places_actual;
				}
			}
		}

		$columns = [];
		$columns[0][] = 'x';
		$columns[1][] = 'по местам';
		$qty = 0;
		foreach ($storeM3 as $cityID => $qtyInCity) {
			$columns[0][] = City::findOne($cityID)->name;
			$columns[1][] = $qtyInCity;
			$qty += $qtyInCity;
		}

		$columnsNP = $columns;

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'clientArray' => $clientArray,
			'storeArray' => $storeArray,

			'columnsByCity' => $columnsByCity,
			'groupsByCity' => $groupsByCity,
			'namesByCity' => $namesByCity,

			'columnsM3' => $columnsM3,

			'columnsNP' => $columnsNP,
		]);
	}

	/*
    *
    * */
	public function actionConfirmOutboundOrder()
	{
		// other/one/confirm-outbound-order
		return 'NO INDEX';
		$orders = [
			9920,
		];

		$apiResponse = [];
		$str = 'RezerveId;Barkod;Miktar;IrsaliyeNo;KoliId;KoliDesi;KoliKargoEtiketId;' . "\n";
		if (!empty($orders)) {
			foreach ($orders as $outID) {
				$outboundOrderModel = OutboundOrder::findOne($outID);
				if ($outboundOrderModel->client_id == Client::CLIENT_DEFACTO && YII_ENV == 'prod' || 1) { // id = 2 Дефакто

					$outboundOrderItems = OutboundOrderItem::find()
														   ->andWhere(['outbound_order_id' => $outboundOrderModel->id])
														   ->andWhere('accepted_qty > 0')
														   ->all();

					if ($outboundOrderItems) {
						//Start Вынести в отдельную функцию
						$countBoxBarcodes = Stock::find()
												 ->andWhere(['outbound_order_id' => $outboundOrderModel->id])// ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
												 ->andWhere(['status' => [
								Stock::STATUS_OUTBOUND_SCANNED,
								Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
								Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
							]
							])
												 ->groupBy('box_barcode')
												 ->count();
						$apiManager = new DeFactoSoapAPIV2Manager();
						$result = $apiManager->CreateLcBarcode($countBoxBarcodes + 2);
						if ($result['HasError']) {
							echo $result['ErrorMessage'];
							file_put_contents("CreateLcBarcode-Error-resend.log", print_r($result, true) . "\n", FILE_APPEND);
							die();
							return 0;
						}

						$createLcBarcodes = $result['Data'];
						//Start Вынести в отдельную функцию
						$mappingOurBobBarcodeToDefacto = [];
						$mappingWaybillNumber = [];
						$boxCountStep = 1;

						StockExtraField::deleteAllById(Stock::find()->select('id')->andWhere(['outbound_order_id' => $outboundOrderModel->id])->column());

						foreach ($outboundOrderItems as $outboundOrderItem) {
							$stocks = Stock::find()->select('product_barcode, inbound_order_id, count(id) as accepted_qty, box_barcode, box_size_m3, box_size_barcode, outbound_order_item_id')
										   ->andWhere(['outbound_order_item_id' => $outboundOrderItem->id])// ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
										   ->andWhere(['status' => [
									Stock::STATUS_OUTBOUND_SCANNED,
									Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
									Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
								]
								])
										   ->groupBy('product_barcode, box_barcode, outbound_order_item_id')
										   ->orderBy('box_barcode')
										   ->asArray()
										   ->all();

							if ($tmp = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks, $outboundOrderItem)) {

								foreach ($tmp as $tmpAPIValue) {
									if (!isset($mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']])) {
										$mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']] = array_shift($createLcBarcodes);
										$mappingWaybillNumber[$tmpAPIValue['LcBarcode']] = DeFactoSoapAPIV2Manager::makeWaybillNumber($outboundOrderModel, $boxCountStep);
										$boxCountStep++;
										file_put_contents("mappingOutboundBarcodeToDefacto-resend.csv",
											$outboundOrderModel->id . ";"
											. $tmpAPIValue['LcBarcode'] . ";"
											. $mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']] . ";"
											. $mappingWaybillNumber[$tmpAPIValue['LcBarcode']] . ";"
											. "\n", FILE_APPEND);

										$stockIdsOutboundBoxes = Stock::find()
																	  ->select('id')
																	  ->andWhere([
																		  'outbound_order_id' => $outboundOrderModel->id,
																		  'box_barcode' => $tmpAPIValue['LcBarcode'],
																	  ])
																	  ->andWhere(['status' => [
																		  Stock::STATUS_OUTBOUND_SCANNED,
																		  Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
																		  Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
																	  ]])
																	  ->column();

//                                    VarDumper::dump($stockIdsOutboundBoxes,10,true);
//                                    echo "<br />";

										if ($stockIdsOutboundBoxes) {
//                                        foreach($stockIdsOutboundBoxes as $stockIdOutboundBoxItem=>$stockIdsOutboundBoxId) {
											StockExtraField::saveBoxDefacto($stockIdsOutboundBoxes, [
//                                                StockExtraField::OUTBOUND_BOX_FIELD_NAME_DEFACTO=>$tmpAPIValue['LcBarcode'],
												StockExtraField::OUTBOUND_LC_BARCODE_FIELD_NAME_DEFACTO => $mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']],
												StockExtraField::OUTBOUND_WAYBILL_NUMBER_FIELD_NAME_DEFACTO => $mappingWaybillNumber[$tmpAPIValue['LcBarcode']],
											]);
//                                        }
										}

									}
								}
							}

							foreach ($stocks as $keyStock => $stock) {
								$stocks[$keyStock]['box_barcode'] = $mappingOurBobBarcodeToDefacto[$stock['box_barcode']];
								$stocks[$keyStock]['our_box_barcode'] = $stock['box_barcode'];
								$stocks[$keyStock]['waybill_number'] = $mappingWaybillNumber[$stock['box_barcode']];
							}

							$outboundPreparedDataRows = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks, $outboundOrderItem);
							if ($outboundPreparedDataRows) {
								foreach ($outboundPreparedDataRows as $outboundPreparedDataRow) {
									unset($outboundPreparedDataRow['our_box_barcode']);
									$rowsDataForAPI['OutBoundFeedBackThreePLResponse'][] = $outboundPreparedDataRow;
								}
							}
						}

						if (!empty($rowsDataForAPI)) {

							foreach ($rowsDataForAPI['OutBoundFeedBackThreePLResponse'] as $key => $pItem) {
								if (!in_array($pItem['WaybillNumber'], ['1588098360001', '1588098360002', '1588098360003'])) {
//                                    unset($rowsDataForAPI['OutBoundFeedBackThreePLResponse'][$key]);
								}
//
//                                if($pItem['WaybillNumber'] == '1588098360001') {
//                                    $rowsDataForAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'] = '1588098361101';
//                                }
//
//                                if($pItem['WaybillNumber'] == '1588098360002') {
//                                    $rowsDataForAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'] = '1588098361102';
//                                }
//
//                                if($pItem['WaybillNumber'] == '1588098360003') {
//                                    $rowsDataForAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'] = '1588098361103';
//                                }

							}
							VarDumper::dump($rowsDataForAPI, 10, true);
							VarDumper::dump(count($rowsDataForAPI['OutBoundFeedBackThreePLResponse']), 10, true);
							die("XXX");
							//$api = new DeFactoSoapAPIV2Manager();
							//file_put_contents('Send-OutBound-FeedBack-Data-Serialize-TEST-1.csv',print_r($rowsDataForAPI,true)."\n",FILE_APPEND);
//                        die;
							//$api->SendOutBoundFeedBackData($rowsDataForAPI);
							// file_put_contents('SendOutBoundFeedBackDataSerializezzzzzz.csv',print_r($rowsDataForAPI,true)."\n",FILE_APPEND);
							file_put_contents('SendOutBoundFeedBackDataSerialize-resend.csv', serialize($rowsDataForAPI) . "\n", FILE_APPEND);
						}
					}
					$outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
					$outboundOrderModel->save(false);
				}
			}
		}
		die('-die return-');
	}

	/*
    *
    * */
	public function actionTestDeliveryBilling()
	{
		return 'NO INDEX';
		$client_id = 2;
		$ids = [1, 2, 96, 7, 8, 9, 93, 308];

		$billingIds = TlDeliveryProposalBilling::find()
											   ->andWhere(
												   [
													   'client_id' => $client_id,
													   'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
													   'route_from' => 4,
													   'route_to' => $ids,
												   ]
											   )
											   ->column();


		$billingAll = TlDeliveryProposalBilling::find()
											   ->andWhere(
												   [
													   'client_id' => $client_id,
													   'tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL,
//                    'route_from' =>4,
//                    'route_to' =>$ids,
												   ]
											   )
											   ->andWhere(['NOT IN', 'id', $billingIds])
			//->andWhere(['not in','route_from',$ids])
											   ->all();

		foreach ($billingAll as $billing) {
//            $billing->price_invoice_with_vat = 1500;
//            $billing->price_invoice = 1339.286;
//            $billing->rule_type = 2;

			$billing->price_invoice_with_vat = 8300;
			$billing->price_invoice = 7410.714;
			$billing->rule_type = 1;

			$billing->save(false);

			echo $billing->id . "<br />";
		}
		$dpsIDS = TlDeliveryProposal::find()->andWhere([
			'client_id' => $client_id,
			'route_from' => 4,
			'route_to' => $ids
		])
//                                          ->andWhere(['not in','route_from',4])
//                                          ->andWhere(['not in','route_from',$ids])
									->orderBy(['id' => SORT_DESC])
									->limit('500')
									->column();


		echo "TlDeliveryProposal" . "<br />";
		$dps = TlDeliveryProposal::find()->andWhere([
			'client_id' => $client_id,
//                                                    'route_from'=>4,
//                                                    'route_to'=>$ids
		])
								 ->andWhere(['not in', 'id', $dpsIDS])
//                                          ->andWhere(['not in','route_from',$ids])
								 ->orderBy(['id' => SORT_DESC])
								 ->limit('500')
								 ->all();
		foreach ($dps as $dp) {
			$dp->save(false);
			$dpManager = new DeliveryProposalManager(['id' => $dp->id]);
			$dpManager->onUpdateProposal();
			echo $dp->id . "<br />";
		}

		return $this->render('index');
	}

	/*
    *
    * */
	public function actionDiffOutboundAportKoton()
	{
		return 'NO INDEX';
//        $pathToCSVFile = 'tmp-file/iade-csv.csv';
		echo "<br />";
		echo "<br />";
		echo "<br />";
		$files = [
			'tmp-file/koton/box 1.txt',
			'tmp-file/koton/box 2.txt',
			'tmp-file/koton/box 3.txt',
			'tmp-file/koton/box 4.txt',
			'tmp-file/koton/box 5.txt',
			'tmp-file/koton/box  6.txt',
			'tmp-file/koton/box 7.txt',
			'tmp-file/koton/box 8.txt',
			'tmp-file/koton/box 9.txt',
			'tmp-file/koton/box 10.txt',
			'tmp-file/koton/box 11.txt',
			'tmp-file/koton/box 12.txt',
			'tmp-file/koton/box 13.txt',
			'tmp-file/koton/box 14.txt',
			'tmp-file/koton/box-15.txt',
			'tmp-file/koton/box 16.txt',
			'tmp-file/koton/box 17.txt',
			'tmp-file/koton/box 18.txt',
			'tmp-file/koton/box 19.txt',
			'tmp-file/koton/box 20.txt',
			'tmp-file/koton/box 21.txt',
			'tmp-file/koton/box 22.txt',
			'tmp-file/koton/box 23.txt',
			'tmp-file/koton/box 24.txt',
			'tmp-file/koton/box 25.txt',
		];
		$rowFile = '';
		$boxes = [];
		foreach ($files as $pathToCSVFile) {
			$row = 0;
			$rowFile = trim(basename($pathToCSVFile));
			if (($handle = fopen($pathToCSVFile, "r")) !== false) {
				while (($data = fgetcsv($handle, 5000, " ")) !== false) {
					$row++;
					$parsedData = [];
					foreach ($data as $d) {
						if (!empty($d)) {
							$parsedData[] = $d;
						}
					}

					if (!empty($parsedData) && isset($parsedData[3])) {
						if (isset($boxes[$rowFile][$parsedData[3]])) {
							$boxes[$rowFile][$parsedData[3]] += 1;
						} else {
							$boxes[$rowFile][$parsedData[3]] = 1;
						}
					}
//                    VarDumper::dump($parsedData, 10, true);
				}
			}
//            VarDumper::dump($boxes, 10, true);
		}

//        VarDumper::dump($boxes, 10, true);

		$id = 1221;
		$oo = OutboundOrder::findOne($id);

		$boxProducts = [];
		foreach ($boxes as $box) {
			foreach ($box as $inBoxBarcode => $inBoxQty) {
				if (isset($boxProducts[$inBoxBarcode])) {
					$boxProducts[$inBoxBarcode] += $inBoxQty;
				} else {
					$boxProducts[$inBoxBarcode] = $inBoxQty;
				}
			}
		}

		$qtySum = 0;
		$qtyDiff = 0;
		foreach ($boxProducts as $inBoxBarcode => $inBoxQty) {
			$qtySum += $inBoxQty;
			$qtyInStock = Stock::find()
							   ->andWhere(['product_barcode' => $inBoxBarcode, 'outbound_order_id' => $oo->id])
							   ->andWhere('box_barcode IS NOT NULL')
							   ->asArray()
							   ->count();
			if ($qtyInStock == $inBoxQty) {
				//echo "Y"."<br />";
			} else {
				echo $qtyInStock - $inBoxQty;
				//echo $qtyDiff// = $qtyInStock-$inBoxQty;
				echo "<br />";
				echo $inBoxBarcode . " " . $qtyInStock . " " . $inBoxQty . " " . "N" . "<br />";
			}
		}

//        echo "<br />";
//        echo $qtySum1;
//        echo "<br />";
//        echo "<br />";
//        echo $qtyDiff;
//        echo "<br />";

		echo "<br />";
		echo $qtySum;
		echo "<br />";
		die;

		VarDumper::dump($boxes, 10, true);

//        die;

//            $ooItems = OutboundOrderItem::find()->andWhere(['outbound_order_id'=>$oo->id])->all();
		$stockItems = Stock::find()->select('product_barcode, box_barcode')->andWhere(['outbound_order_id' => $oo->id])->asArray()->all();
//        $stockItemsB = Stock::find()->andWhere(['outbound_order_id'=>$oo->id])->groupBy('box_barcode')->asArray()->count();

		$stockBoxes = [];
		$stockRowBox = 0;
		foreach ($stockItems as $item) {
//            $stockRowBox++;
			if (!empty($item['box_barcode'])) {
				if (isset($stockBoxes[$item['box_barcode']][$item['product_barcode']])) {
					$stockBoxes[$item['box_barcode']][$item['product_barcode']] += 1;
				} else {
					$stockBoxes[$item['box_barcode']][$item['product_barcode']] = 1;
					$stockRowBox++;
				}
			}
//            VarDumper::dump($item, 10, true);
		};
		VarDumper::dump($stockBoxes, 10, true);
//        echo $stockItemsB."<br />";
//        echo count($stockBoxes);
//die;
		return $this->render('index');
	}

	/*
    *
    * */
	public function actionInboundOrderNovember()
	{ //
		return 'NO INDEX';
		$query = InboundOrder::find();
		$date = explode('/', '2015-11-01 / 2015-11-20');
		$date[0] = trim($date[0]) . ' 00:00:00';
		$date[1] = trim($date[1]) . ' 23:59:59';

		$query->andWhere(['order_type' => '1', 'client_id' => 2]);
		$query->andWhere(['between', 'created_at', strtotime($date[0]), strtotime($date[1])]);
		$query->select('id, order_number, date_confirm');
		$inbounds = $query->all();
		$dataOut = [];
		foreach ($inbounds as $in) {
			$inboundItems = InboundOrderItem::find()->andWhere(['inbound_order_id' => $in->id])->all();
			foreach ($inboundItems as $inItem) {
//        $inboundsCount =  $query->count();
				// count(product_barcode) as product_qty,
				$inStockAvailabilityYES = Stock::find()
											   ->select('id, product_barcode, product_model, primary_address, secondary_address, inbound_order_id')
											   ->andWhere(['inbound_order_id' => $in->id, 'product_barcode' => $inItem->product_barcode, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])
//                    ->asArray()
//                    ->groupBy('product_barcode')
											   ->count();

				$inStockAvailabilityNO = Stock::find()
											  ->select(' id, product_barcode, product_model, primary_address, secondary_address, inbound_order_id')
											  ->andWhere(['inbound_order_id' => $in->id, 'product_barcode' => $inItem->product_barcode])
//                    ->asArray()
											  ->count();

				$dataOut[] = [
					'order_number' => $in->order_number,
					'date_confirm' => $in->date_confirm,
					'product_barcode' => $inItem->product_barcode,
					'product_model' => $inItem->product_model,
					'exp_qty' => $inStockAvailabilityNO,
					'acc_qty' => $inStockAvailabilityYES,
				];
				/*
                                    $toFileRow = '"' . $inboundOrder->order_number . '";"'
                                        . Yii::$app->formatter->asDatetime($inboundOrder->date_confirm) . '";"'
                                        . $st['product_barcode'] . '";"'
                                        . $st['product_qty'] . '";"'
                                        . $st['product_model'] . '";'
                //                         . $st['primary_address'] . '";"'
                //                         . $st['secondary_address'] . '";'
                                        . "\n";*/

//                    file_put_contents('inbound-11-01-11-20-l-5.csv', $toFileRow, FILE_APPEND);
//                }
			}
		}
		foreach ($dataOut as $data) {

			$toFileRow = '"' . $data['order_number'] . '";"'
				. Yii::$app->formatter->asDatetime($data['date_confirm']) . '";"'
				. $data['product_barcode'] . '";"'
				. $data['exp_qty'] . '";"'
				. $data['acc_qty'] . '";"'
				. $data['product_model'] . '";'
//                         . $st['primary_address'] . '";"'
//                         . $st['secondary_address'] . '";'
				. "\n";
			file_put_contents('inbound-11-01-11-20-new-1.csv', $toFileRow, FILE_APPEND);
		}

		VarDumper::dump($dataOut, 10, true);
//        VarDumper::dump($inbounds,10,true);/
//        die;

//        return $this->render('index');
	}

	/*
    *
    * */
	public function actionReturnDiffQty()
	{ //
		return 'NO INDEX';
		$pathToCSVFile = 'tmp-file/iade-csv.csv';
		$row = 0;

		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			$parsedData = [];
			while (($data = fgetcsv($handle, 5000, ";")) !== false) {
				$row++;
				VarDumper::dump($data, 10, true);
				if ($row > 1) {
					if ($rOrder = ReturnOrder::find()->andWhere(['order_number' => $data['0'], 'client_id' => 2])->one()) {
						$toFileRow = '"' . $rOrder->order_number . '";"' . $rOrder->expected_qty . '";"' . $rOrder->accepted_qty . '";' . "\n";
						file_put_contents('return-diff-qty-report-3.csv', $toFileRow, FILE_APPEND);
					}
				}
			}
		}

		return 'готово';
	}

	/*
 *
 * */
	public function actionDiffCrossDock()
	{
		return 'NO INDEX';
		$pathToCSVFile = 'tmp-file/cross-dock/Han-shatir-scv.csv';
//        $pathToCSVFile = 'tmp-file/cross-dock/Atirau65-66-scv.csv';
//        $pathToCSVFile = 'tmp-file/cross-dock/Shimkent118-119-scv.csv';
//        $row = 0;
//        $cdIDsAll = CrossDock::find()->select('id')->andWhere(['internal_barcode'=>'2-509318-19-20-21'])->column(); //->andWhere(['status'=>3])
//        VarDumper::dump($cdIDsAll, 10, true);
//        $cdIDsAll = [1074];// Шимкент не найден 000000523983
//        $cdIDsAll = [1082];// Атырау не найден  000000523422
		$cdIDsAll = [1089];// Хан-шатыр не найден  000000524225
		$boXes = CrossDockItems::find()->select('id,box_barcode')->andWhere(['cross_dock_id' => $cdIDsAll])->asArray()->all();
//        $boXes = ArrayHelper::map($boXes,'id','box_barcode');
//        VarDumper::dump($boXes, 10, true);
//        VarDumper::dump(count($boXes), 10, true);
//        die;
		$parsedData = [];
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 5000, ";")) !== false) {
				if ($id = CrossDockItems::find()->select('id')->andWhere(['cross_dock_id' => $cdIDsAll])->andWhere('box_barcode LIKE "%' . $data['0'] . '%"')->scalar()) {
					$parsedData[] = $id;
					echo "YES" . ' ' . $data['0'] . "<br />";
				} else {
					echo "NO" . ' ' . $data['0'] . "<br />";;
				}
//                }
			}
			$datax = CrossDockItems::find()->select('id,box_barcode')->andWhere(['not in', 'id', $parsedData])->andWhere(['cross_dock_id' => $cdIDsAll])->asArray()->all();
			VarDumper::dump($datax, 10, true);
		}
		return $this->render('index');
	}

	/*
     *
     * */
	public function actionReturnReportFromFile()
	{
		return 'NO';
		$pathToCSVFile = 'tmp-file/eksi-stoklar-csv.csv';
		$row = 0;
		$returnIDsAll = ReturnOrder::find()->select('id')->andWhere(['status' => 3])->column();

		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			$parsedData = [];
			while (($data = fgetcsv($handle, 5000, ";")) !== false) {
				$row++;
				if ($row > 1) {
//                    VarDumper::dump($data, 10, true);
					$qty = preg_replace('/[^0-9]/', '', trim(str_replace('000', '', $data[4])));
					$accepted_qty = $qty;
//                    echo "<br />";
					$barcode = trim($data[10]);
//                    echo $barcode."<br />";
					if ($items = ReturnOrderItems::find()->andWhere(['product_barcode' => $barcode, 'return_order_id' => $returnIDsAll])->orderBy(['id' => SORT_DESC])->all()) {
						foreach ($items as $item) {
							$qty -= $item['accepted_qty'];

//                           if($item['accepted_qty'] == $accepted_qty) {
//
							//}else
//                            if($qty < 1) {
//                               break;
//                           }
//                               echo $barcode.' '.$accepted_qty.' YES '."<br />";

							if ($returnOne = ReturnOrder::findOne($item['return_order_id'])) {
								if ($returnOne->status == 3) {
									$out = '';
									$out2 = '';
//                                        $in = InboundOrder::find()->andWhere([''=>$returnOne->order_number])->one();
									if ($returnOne->extra_fields) {
										$extraFields = \yii\helpers\Json::decode($returnOne->extra_fields);
										$koliResponseData = [];
										if (isset($extraFields['IadeKabulResult->Koli'])) {
											$koliResponseData = $extraFields['IadeKabulResult->Koli'];
										} elseif (isset($extraFields['KoliIadeKabulResult->Koli'])) {
											$koliResponseData = $extraFields['KoliIadeKabulResult->Koli'];
										} elseif (isset($extraFields['koliResponse'])) {
											$koliResponseData = $extraFields['koliResponse'];
										}

										if (!empty($koliResponseData) && isset($koliResponseData['KoliBarkod'])) {
											$out = $koliResponseData['KoliBarkod'];
										}
										if (!empty($koliResponseData) && isset($koliResponseData['UrunKodu'])) {
											$out2 = $koliResponseData['UrunKodu'];
										}
									}

									echo $barcode . ' ' . $accepted_qty . ' ' . $item['accepted_qty'] . ' ' . $out . ' YES ' . "<br />";
									$toFileRow = '"' . $barcode . '"; "' . $accepted_qty . '"; "' . $item['accepted_qty'] . '"; "' . $out . '"; "' . $out2 . '"; "' . $returnOne->order_number . '";' . "\n";// YES; ';."<br />";
									file_put_contents('return-report-3.csv', $toFileRow, FILE_APPEND);
								} else {
									echo $barcode . ' ' . $accepted_qty . ' NO ' . "<br />";
								}
							} else {
								echo $barcode . ' ' . $accepted_qty . ' NO ALL' . "<br />";
							}

							if ($qty < 1) {
								break;
							}
//                               break;
//                           }
						}
					} else {
						echo $barcode . ' ' . 'NO' . "<br />";
					}
				}
			}
		}

		return 'NO';
//        $query = TlDeliveryProposal::find();
//            $date = explode('/','2015-10-01 / 2015-11-04');
//            $date[0] = trim($date[0]).' 00:00:00';
//            $date[1] = trim($date[1]).' 23:59:59';
//
//            $query->andWhere(['between', 'shipped_datetime', strtotime($date[0]),strtotime($date[1])]);
//
//       $dps =  $query->all();
//
//        foreach($dps as $dpModel) {
//           if($dpRoutes = $dpModel->getProposalRoutes()->all()) {
//               foreach($dpRoutes as $route) {
//                   if($carItems = $route->getCarItems()->all()) {
//                       foreach ($carItems as $item) {
//
//                       }
//                   }
//               }
//           }
//        }

	}

	public function actionAddCrossDock()
	{
		return 'NO';
		// add-cross-dock
		// создаем пустой кросс док по казахстану

//        echo "Выполнено на живом, повторно выполнять не нужно";
//        die;
		$client_id = 2;
//        $stores = Store::find()->andWhere(['type_use'=>1,'client_id'=>$client_id])->andWhere('shop_code2 != "" AND shop_code2 != "-" AND shop_code2 != "0" ')->all();
		$stores = Store::find()
					   ->andWhere(['type_use' => 1, 'client_id' => $client_id])
					   ->andWhere('shop_code2 != "" AND shop_code2 != "-" AND shop_code2 != "0" ')
//                    ->andWhere(['id'=>321])
					   ->all();

//        $party_number = '509218-19-20-21-22-24';
		$party_number = '509166';

		$cCD = new ConsignmentCrossDock();
		$cCD->client_id = $client_id;
		$cCD->party_number = $party_number;
		$cCD->expected_rpt_places_qty = 0;
		$cCD->expected_number_places_qty = 0;
		$cCD->save(false);


		foreach ($stores as $store) {
			$newCrossDock = new CrossDock();
			$newCrossDock->client_id = $client_id;
			$newCrossDock->party_number = $party_number;
			$newCrossDock->consignment_cross_dock_id = $cCD->id;
			$newCrossDock->from_point_id = 4;
			$newCrossDock->to_point_id = $store->id;
			$newCrossDock->to_point_title = $store->shop_code2;
			$newCrossDock->from_point_title = 4;
			$newCrossDock->internal_barcode = $client_id . '-' . $party_number;
			$newCrossDock->status = Stock::STATUS_CROSS_DOCK_NEW;
			$newCrossDock->expected_number_places_qty = 0;
			$newCrossDock->box_m3 = 0;
			if ($newCrossDock->save(false)) {
				$newCrossDock->createDeliveryProposal();
			}
		}
		return 'ok';
	}

	/*
     *
     * */
	public function actionCrossDockPdf()
	{
		echo "Выполнено на живом, повторно выполнять не нужно";
		die;

		$cCrossDockId = 64;
		$crossDockAll = CrossDock::find()->select('id, to_point_id, expected_number_places_qty')->andWhere(['consignment_cross_dock_id' => $cCrossDockId])->asArray()->all();
		$outputArray = [];
		$i = 0;
		foreach ($crossDockAll as $cd) {

			$storeTitle = 'МАГАЗИН НЕ НАЙДЕН';
			if ($store = \common\modules\store\models\Store::findOne($cd['to_point_id'])) {
				$storeTitle = \common\modules\store\models\Store::getPointTitle($store->id);
			}

			$outputArray[$i]['to_store_title'] = $storeTitle;
			$outputArray[$i]['expected_number_places_qty'] = $cd['expected_number_places_qty'];

			$cdItemAll = CrossDockItems::find()->select('id')->andWhere(['cross_dock_id' => $cd['id']])->asArray()->all();
			foreach ($cdItemAll as $cdItem) {
				$cdItemProductAll = CrossDockItemProducts::find()->select('count(*) as qty, product_barcode')->andWhere(['cross_dock_item_id' => $cdItem['id']])->groupBy('product_barcode')->asArray()->all();

				$outputArray[$i]['to_point_id'] = $cd['to_point_id'];
				foreach ($cdItemProductAll as $cdItemProduct) {
					if (isset($outputArray[$i][$cdItemProduct['product_barcode']])) {
						$outputArray[$i][$cdItemProduct['product_barcode']] += $cdItemProduct['qty'];
					} else {
						$outputArray[$i][$cdItemProduct['product_barcode']] = $cdItemProduct['qty'];
					}
				}

//                VarDumper::dump($cdItemProductAll,10,true);
//                echo "<br />";
//                echo "<br />";
//                echo "<br />";
//                echo "<br />";
			}
			$i++;
		}

//                        VarDumper::dump($outputArray,10,true);
//        die;
//        return $this->renderPartial('_print-cross-dock-pdf',[])
		return $this->render('_print-cross-dock-pdf', ['items' => $outputArray]);
	}

	public function actionTest()
	{
		return 'NO';
		// $t = '1-9-07-2';
		// $t = '6-1-09-0';
		// $t = '1-9-07-2b0000021659';
		// VarDumper::dump(BarcodeManager::isRegiment($t),10,true);
		// die;
	}

	public function actionAddNewInbound()
	{
		///other/one/add-new-inbound
		echo "Выполнено на живом, повторно выполнять не нужно";

		die;
		ConsignmentInboundOrders::deleteAll(['id' => '106']);
		InboundOrder::deleteAll(['id' => '11660']);
		InboundOrderItem::deleteAll(['inbound_order_id' => '11660']);
		Stock::deleteAll(['inbound_order_id' => '11660']);
		$pathToCSVFile = 'tmp-file/Akmaral/MC_005(2).csv';
//        $row = 0;
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			$parsedData = [];
			while (($data = fgetcsv($handle, 1000, ";")) !== false) {

//                VarDumper::dump($data,10,true);
				if (isset($parsedData[$data['1']])) {
					$parsedData[$data['1']] = [
						'model' => $data['4'],
						'name' => $data['3'],
						'barcode' => $data['1'],
						'qty' => $parsedData[$data['1']]['qty'] + (int)preg_replace('/[^\d]+/', '', $data['7'])
					];
				} else {
					$parsedData[$data['1']] = [
						'model' => $data['4'],
						'name' => $data['3'],
						'barcode' => $data['1'],
						'qty' => (int)preg_replace('/[^\d]+/', '', $data['7'])
					];
				}

			}
		}

//        VarDumper::dump($parsedData,10,true);
//        die;

		$client_id = 66;
		$order_number = 'akmaral-20160218';
		$consignmentInboundOrder = new ConsignmentInboundOrders();
		$consignmentInboundOrder->client_id = Client::CLIENT_AKMARAL;
		$consignmentInboundOrder->party_number = $order_number;
		$consignmentInboundOrder->delivery_type = InboundOrder::DELIVERY_TYPE_CROSS_DOCK_A;
		$consignmentInboundOrder->order_type = InboundOrder::ORDER_TYPE_INBOUND;
		$consignmentInboundOrder->status = Stock::STATUS_INBOUND_NEW;
		$consignmentInboundOrder->save(false);


		$inboundModel = new InboundOrder();
		$inboundModel->client_id = $client_id;
		$inboundModel->consignment_inbound_order_id = $consignmentInboundOrder->id;
		$inboundModel->order_number = $order_number;
		$inboundModel->parent_order_number = $order_number;
		$inboundModel->status = Stock::STATUS_INBOUND_NEW;
		$inboundModel->expected_qty = '0';
		$inboundModel->accepted_qty = '0';
		$inboundModel->accepted_number_places_qty = '0';
		$inboundModel->expected_number_places_qty = '0';
		$inboundModel->order_type = InboundOrder::ORDER_TYPE_INBOUND;
		$inboundModel->save(false);

		$inboundModelID = $inboundModel->id;
		$expectedQty = 0;
//        foreach ($parsedData as $barcode => $qty) {
		foreach ($parsedData as $productData) {

			$ioi = new InboundOrderItem();
			$ioi->inbound_order_id = $inboundModelID;
			$ioi->product_barcode = $productData['barcode'];
			$ioi->product_name = $productData['name'];
//            $ioi->product_model = $productData['model'];
			$ioi->expected_qty = $productData['qty'];
			$ioi->status = Stock::STATUS_INBOUND_NEW;
			$ioi->save(false);

			$expectedQty += $ioi->expected_qty;

//            Stock::deleteAll(['client_id' => $client_id, 'inbound_order_id' => $ioi->inbound_order_id, 'product_barcode' => $ioi->product_barcode, 'product_model' => $ioi->product_model]);

			for ($i = 1; $i <= $ioi->expected_qty; $i++) {

				$stock = new Stock();
				$stock->client_id = $client_id;
				$stock->inbound_order_id = $ioi->inbound_order_id;
				$stock->product_barcode = $ioi->product_barcode;
//                $stock->product_model = $ioi->product_model;
				$stock->product_name = $ioi->product_name;
				$stock->status = Stock::STATUS_INBOUND_NEW;
				$stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
				$stock->save(false);
			}
		}

		InboundOrder::updateAll(['expected_qty' => $expectedQty], ['id' => $inboundModelID]);
		echo "<br />" . "Приходная накладная успешно создана" . "<br />";
		die;
	}

	/*
    *
    * */
	public function actionAddInboundItem()
	{
		// /other/one/add-inbound-item
		die("-START DIE-");
		$inboundModelID = 19513; //19492; // 511208
		$inboundModel = InboundOrder::findOne($inboundModelID);
		$parsedData = [];
		$parsedData[] = [
			'barcode' => '9000005728788',
			'model' => '-',
			'qty' => '113',
			'name' => '',
		];
		$parsedData[] = [
			'barcode' => '9000005728825',
			'model' => '-',
			'qty' => '116',
			'name' => '',
		];

		foreach ($parsedData as $productData) {

			$ioi = new InboundOrderItem();
			$ioi->inbound_order_id = $inboundModelID;
			$ioi->product_barcode = $productData['barcode'];
			$ioi->product_name = $productData['name'];
			$ioi->product_model = $productData['model'];
			$ioi->expected_qty = $productData['qty'];
			$ioi->status = Stock::STATUS_INBOUND_NEW;
			$ioi->save(false);

			for ($i = 1; $i <= $ioi->expected_qty; $i++) {

				$stock = new Stock();
				$stock->client_id = $inboundModel->client_id;
				$stock->inbound_order_id = $ioi->inbound_order_id;
				$stock->product_barcode = $ioi->product_barcode;
				$stock->product_model = $ioi->product_model;
				$stock->product_name = $ioi->product_name;
				$stock->status = Stock::STATUS_INBOUND_NEW;
				$stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
				$stock->save(false);

			}

			$inboundModel->expected_qty += $ioi->expected_qty;
			$inboundModel->save(false);

			echo "Добавлено: " . $productData['barcode'] . " " . $ioi->expected_qty . "<br />";
		}
		die('-END-');
	}

	/*
     * Update stock by id from file/ Перезаписываем сток записи из файла по ид полностью
     * // TODO РАБОЧИЙ МЕТОД
     * */
	public function actionUpdateStockById()
	{
		// other/one/update-stock-by-id
		$pathToCSVFile = 'tmp-file/defacto/14092016/stock.csv';
		$row = 0;
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		die("-START DIE other/one/update-stock-by-id");

		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			$parsedData = [];

			while (($data = fgetcsv($handle, 1000, ";")) !== false) {
				$row += 1;
//                VarDumper::dump($data,10,true);
//                if($row > 1) {
				$attributes = [
//                        '﻿id' => $data[0],
//                        'scan_in_employee_id' => $data[1],
//                        'scan_out_employee_id' => $data[2],
//                        'client_id' => $data[3],
//                        'inbound_order_id' => $data[4],
//                        'consignment_inbound_id' => $data[5],
//                        'inbound_order_item_id' => $data[6],
//                        'inbound_order_number' => $data[7],
//                        'outbound_order_id' => $data[8],
//                        'consignment_outbound_id' => $data[9],
//                        'outbound_order_item_id' => $data[10],
//                        'outbound_picking_list_id' => $data[11],
//                        'outbound_picking_list_barcode' => $data[12],
//                        'outbound_order_number' => $data[13],
//                        'warehouse_id' => $data[14],
//                        'product_id' => $data[15],
//                        'product_name' => $data[16],
//                        'product_barcode' => $data[17],
//                        'product_model' => $data[18],
//                        'product_sku' => $data[19],
					'box_barcode' => $data[1],
//                        'box_size_barcode' => $data[21],
//                        'box_size_m3' => '34',
//                        'box_kg' => '0.102',
//                        'condition_type' => $data[24],
//                        'status' => $data[25],
//                        'status_availability' => $data[26],
//                        'status_lost' => $data[27],
//                        'inventory_id' => $data[28],
//                        'inventory_primary_address' => $data[29],
//                        'inventory_secondary_address' => $data[30],
//                        'status_inventory' => $data[31],
//                        'primary_address' => $data[32],
//                        'secondary_address' => $data[33],
//                        'address_sort_order' => $data[34],
//                        'kpi_value' => $data[35],
//                        'scan_out_datetime' => $data[36],
//                        'scan_in_datetime' => $data[37],
//                        'created_user_id' => $data[38],
//                        'updated_user_id' => $data[39],
//                        'created_at' => $data[40],
//                        'updated_at' => $data[41],
//                        'deleted' => $data[42],
				];

				if ($stock = Stock::findOne($data[0])) {
					$stock->setAttributes($attributes, false);
//                        $stock->save(false);
					echo "SAVE OK: " . $data[0] . " " . $row . "<br />";
				} else {
					echo "SAVE ERROR: " . $data[0] . "<br />";
				}
//                }
			}
		}
		return $this->render('index');
	}

	/*
     *
    */
	public function actionAddAndAllocatedProductOnStock()
	{
		// /other/one/add-and-allocated-product-on-stock
		die("START-END-DIE");
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		$stockItems = [
//            '9000005367383'=>'2', // 4202
//            '9000005399643'=>'2', // 4202
//            '9000005416722'=>'2',// 4202
//            '9000005416784'=>'1',// 4202
//            '9000005482482'=>'1',// 4202
//            '9000005609056'=>'1',// 4202
//            '9000005609094'=>'2',// 4202
//            '9000005627609'=>'1',// 4202
//            '9000005672678'=>'1',// 4202
//            '9000005672746'=>'1',// 4202
//            '9000005678625'=>'1',// 4202
//            '9000005915782'=>'1',// 4202
//            '9000005915775'=>'1',// 4202
//            '9000005915805'=>'1',// 4202

//            '9000005416791'=>'1',
//            '9000005915775'=>'1',
//            '9000005918530'=>'2',
//             '9000005416845'=>'2', // 26216319 4205
//            '9000005915775'=>'1', // 26216319 4205

//              '9000005416845'=>'2', // 26216322 4204
//              '9000005915775'=>'1', // 26216322 4204
//              '9000005918530'=>'1', // 26216322 4204
			'9000005416791' => '1', // 26216331 4207
		];

		$client_id = 2;
		$outbound_id = 4207; //4202 4205
		$fileName = time();
		$stockIDs = [];
		foreach ($stockItems as $stockBarcode => $stockQty) {
			$inStocks = Stock::find()->andWhere([
					'client_id' => $client_id,
					'product_barcode' => $stockBarcode,
					'status_availability' => Stock::STATUS_AVAILABILITY_YES]
			)
							 ->limit($stockQty)
							 ->all();

			if ($inStocks) {
				foreach ($inStocks as $stockLine) {
					$stockLine->outbound_order_id = $outbound_id;
					$stockLine->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
					$stockLine->status_availability = Stock::STATUS_AVAILABILITY_RESERVED;
//                    $stockLine->save(false);

					$stockIDs[] = $stockLine->id;
					$toSave = $stockLine->secondary_address . ";"
						. $stockLine->primary_address . ";"
						. $stockBarcode . ";1;" . "\n";
					file_put_contents('picking-list-' . $fileName . '.csv', $toSave, FILE_APPEND);
					echo $toSave . "<br />";
				}
			}
		}

//        $opl = new OutboundPickingLists();
//        $opl->client_id = $client_id;
//        $opl->status = 3;
//        $opl->barcode =  '26216322-52624-2-2';
//        $opl->outbound_order_id = $outbound_id;
//        $opl->page_number = 2;
//        $opl->page_total = 2;
//        $opl->employee_id = 3;
//        $opl->save(false);
		$outbound_picking_list_id = '13387';//$opl->id;
		$outbound_picking_list_barcode = '26216331-52624-2-1';//$opl->barcode;
//        Stock::updateAll([
//            'outbound_picking_list_id'=> $outbound_picking_list_id,
//            'outbound_picking_list_barcode'=> $outbound_picking_list_barcode,
////            'status' => Stock::STATUS_OUTBOUND_PICKED
//            'status' => Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST
//        ],
//            [
//                'client_id'=>$client_id,
//                'id'=>$stockIDs
//            ]);

		return $this->render('index');
	}

	/*
     *
     * */
	public function actionUpdateStockByIds()
	{
		// /other/one/update-stock-by-ids
		$client_id = 2;
		$outbound_order_id = 4306;
		$box_size_barcode = 32;

		$inStocks = Stock::find()->select('id, box_barcode')->andWhere([
				'client_id' => $client_id,
				'outbound_order_id' => $outbound_order_id,
				'box_size_barcode' => $box_size_barcode
			]
		)
						 ->all();

		$fileName = time();
		if ($inStocks) {
			foreach ($inStocks as $stockLine) {
				$toSave = $stockLine->id . ";"
					. $stockLine->box_barcode . ";"
					. "\n";
				file_put_contents('update-stock-box-barcode-' . $fileName . '.csv', $toSave, FILE_APPEND);
			}
		}

		return $this->render('index');
	}


	/*
     * Если распечатали этикетки возвращаемся на один шаг назад
     * */
	public function actionGoBackOneStep()
	{
		//other/one/go-back-one-step
		die("-die-start-");
		$outboundOrder = OutboundOrder::findOne(4321);

		$stockAll = Stock::find()
						 ->select('id,outbound_order_id, box_barcode, box_size_m3')
						 ->where([
							 'outbound_order_id' => [$outboundOrder->id],
							 'status' => [
								 Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
							 ],
						 ])
						 ->all();
		foreach ($stockAll as $stock) {
			$stock->status = Stock::STATUS_OUTBOUND_SCANNED;
			$stock->save(false);
		}

		OutboundPickingLists::updateAll(['status' => OutboundPickingLists::STATUS_END], ['outbound_order_id' => $outboundOrder->id]);
//        OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL],'id = :id AND accepted_qty > 0',[':id'=>$items[0]['outbound_order_id']]);


		$outboundOrder->status = Stock::STATUS_OUTBOUND_SCANNING;
		$outboundOrder->save(false);


		return $this->render('index');
	}

	/*
     *
     * */
	public function actionAddNewShopCode()
	{ // other/one/add-new-shop-code

		$pathToCSVFile = 'tmp-file/defacto/29092016/1.csv';
		$row = 0;
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		die("-START DIE other/one/add-new-shop-code");
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {

			while (($data = fgetcsv($handle, 1000, ";")) !== false) {
				$row += 1;
				if ($row > 1) {
					Store::updateAll(['shop_code3' => $data[0]],
						[
							'client_id' => 2,
							'shop_code2' => $data[1],
						]);
				}

				VarDumper::dump($data, 10, true);
			}
		}

		return $this->render('index');
	}

	/*
     *
     * */
	public function actionRenameInbound()
	{ // other/one/rename-inbound
		die("START DIE --other/one/rename-inbound");

		$inboundIdOLD = 19520;
		$inboundIdNEW = 19523;
//
		$new = InboundOrder::findOne($inboundIdNEW);

		$stockNews = Stock::find()->andWhere(['inbound_order_id' => $inboundIdNEW])->all();
		$findStockIDs = [];
		foreach ($stockNews as $stockNew) {
			$stockOld = Stock::find()
							 ->andWhere([
								 'product_barcode' => $stockNew->product_barcode,
								 'inbound_order_id' => $inboundIdOLD])
							 ->andWhere(['not in', 'id', $findStockIDs])
							 ->one();
			if ($stockOld) {
				$findStockIDs[] = $stockOld->id;
				$stockNew->status = Stock::STATUS_INBOUND_SCANNED;
				$stockNew->status_availability = Stock::STATUS_AVAILABILITY_NO;
				$stockNew->primary_address = $stockOld->primary_address;
				$stockNew->secondary_address = $stockOld->secondary_address;
				$stockNew->address_sort_order = $stockOld->address_sort_order;
				$stockNew->save(false);
			}
		}
		$inboundOrderItemNews = InboundOrderItem::find()->andWhere(['inbound_order_id' => $inboundIdNEW])->all();
		foreach ($inboundOrderItemNews as $inboundOrderItemNew) {
			$inboundOrderItemNew->accepted_qty = $inboundOrderItemNew->expected_qty;
			$inboundOrderItemNew->status = Stock::STATUS_INBOUND_SCANNED;;
			$inboundOrderItemNew->save(false);
		}
		$new->accepted_qty = 72;
		$new->save(false);

		InboundOrder::deleteAll(['id' => $inboundIdOLD]);
		InboundOrderItem::deleteAll(['inbound_order_id' => $inboundIdOLD]);
		Stock::deleteAll(['inbound_order_id' => $inboundIdOLD]);


		return $this->render('index');
	}

	public function actionRestInboundOrderInStock()
	{ // other/one/rest-inbound-order-in-stock

		die("START DIE --other/one/rest-inbound-order-in-stock");
		$inboundId = 19521;
		Stock::updateAll(['inbound_order_id' => '99999999'], ['inbound_order_id' => $inboundId]);

		$in = InboundOrder::findOne($inboundId);
		$InboundOrderItems = InboundOrderItem::find()->andWhere(['inbound_order_id' => $inboundId])->all();

		$findStockIDs = [];
		foreach ($InboundOrderItems as $inItem) {
			for ($i = 1; $i <= $inItem->expected_qty; ++$i) {
				$stock = new Stock();
				$stock->client_id = $in->client_id;
				$stock->inbound_order_id = $in->id;
				$stock->inbound_order_item_id = $inItem->id;
				$stock->product_barcode = $inItem->product_barcode;
				$stock->product_model = '';
				$stock->status = Stock::STATUS_INBOUND_NEW;
				$stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
				$stock->inbound_client_box = $inItem->box_barcode;
				$stock->save(false);

				$stockOld = Stock::find()
								 ->andWhere([
									 'product_barcode' => $inItem->product_barcode,
									 'inbound_order_item_id' => $inItem->id,
									 'inbound_order_id' => '99999999',
									 'status' => Stock::STATUS_INBOUND_CONFIRM
								 ])
								 ->andWhere(['not in', 'id', $findStockIDs])
								 ->one();

				if ($stockOld) {
					$findStockIDs[] = $stockOld->id;

					$stock->status = Stock::STATUS_INBOUND_SCANNED;
					$stock->primary_address = $stockOld->primary_address;
					$stock->secondary_address = $stockOld->secondary_address;
					$stock->system_status = $stockOld->system_status;
					$stock->system_status_description = $stockOld->system_status_description;
					$stock->inbound_client_box = $stockOld->inbound_client_box;
					$stock->address_sort_order = $stockOld->address_sort_order;
					$stock->save(false);
				}
			}
		}

		$in->status = Stock::STATUS_INBOUND_SCANNING;
		$in->save(false);

		Stock::deleteAll(['inbound_order_id' => '99999999']);

		return $this->render('index');
	}

	/*
     *
     * */
	public function actionReturnReport()
	{ // return-report
		die("BEGIN -DIE return-report-from-stock-");
//        $kzk1 = [];
//        $pathToCSVFile = 'tmp-file/defacto/11-11-2016/KZK1.csv';
//        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
//            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//                $kzk1[$data[0]] = $data;
//            }
//        }

//        $kzk2 = [];
//        $pathToCSVFile = 'tmp-file/defacto/11-11-2016/KZK2.csv';
//        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
//            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//                $kzk2[$data[0]] = $data;
//            }
//        }

//        $kzk3 = [];
//        $pathToCSVFile = 'tmp-file/defacto/11-11-2016/KZK3.csv';
//        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
//            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//                $kzk3[] = $data;
//            }
//        }

		$i = 0;
		$items = ['2220003733011', '2220003733028', '2220003733035', '2220003733059', '2220003734209'];
		foreach ($items as $barcode) {
			$returnOrder = ReturnOrder::find()->andWhere(['LIKE', 'extra_fields', trim($barcode)])->one();
			$i += 1;
			if ($returnOrder) {
				$returnOrderItems = ReturnOrderItems::find()->andWhere(['return_order_id' => $returnOrder->id])->all();
				if ($returnOrderItems) {
					foreach ($returnOrderItems as $returnOrderItem) {
						file_put_contents('ozet-old5.csv', $barcode . ";" . $returnOrderItem->product_barcode . ";" . $returnOrderItem->expected_qty . ";" . "\n", FILE_APPEND);
					}
				} else {
					if (!empty($returnOrder->extra_fields)) {
						$json = Json::decode($returnOrder->extra_fields);
						VarDumper::dump($json, 10, true);

						if (isset($json['KoliDetay->KzkDCIadeDetay'])) {
							foreach ($json['KoliDetay->KzkDCIadeDetay'] as $returnOrderItem) {
								file_put_contents('ozet-old5.csv', $barcode . ";" . $returnOrderItem['Barkod'] . ";" . $returnOrderItem['Miktar'] . ";" . "\n", FILE_APPEND);
							}
						} else {
							die('-NO-');
						}

					}

				}
			} else {
				echo $i . " : " . $barcode . " : НЕ НАЙДЕН<br />";
			}
		}


//        VarDumper::dump(count($kzk1),10,true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump(count($kzk2),10,true);

		return $this->render('index');
	}

	/*
     *
     * */
	public function actionReturnReportFromStock() // OK
	{ // return-report-from-stock

		// по лучаем все лоты шк которых начинается на 222 которые у нас сейчас на остатке.
		die("BEGIN -DIE return-report-from-stock-");

		$returnItemsOnStock = Stock::find()
								   ->select('product_barcode,primary_address')
								   ->andWhere([
									   'client_id' => Client::CLIENT_DEFACTO,
									   'status_availability' => Stock::STATUS_AVAILABILITY_YES,
								   ])
								   ->andWhere("product_barcode LIKE '222%'")
								   ->asArray()
								   ->all();
		$i = 0;
		$fileName = '-2016-11-29-return-on-stock-' . time() . '.csv';
		file_put_contents($fileName,
			"Number" . ";"
			. "Barcode 222" . ";"
			. "Lot barcode" . ";"
			. "Lot qty" . ";"
			. "Sezon" . ";"
			. "DepoTanim" . ";"
			. "IrsaliyeBuyerGroup" . ";"
			. "IrsaliyeMerchGroup" . ";"
			. "Nomadex 3PL stock box" . ";"
			. "\n", FILE_APPEND);
		foreach ($returnItemsOnStock as $returnItemOnStock) {
			$barcode = $returnItemOnStock['product_barcode'];
			$ourBox = $returnItemOnStock['primary_address'];

			$returnOrder = ReturnOrder::find()->andWhere(['LIKE', 'extra_fields', trim($barcode)])->one();
			$i += 1;

			if ($returnOrder) {
//                $returnOrderItems = ReturnOrderItems::find()->andWhere(['return_order_id'=>$returnOrder->id])->all();
//                if($returnOrderItems) {
//                    foreach ($returnOrderItems as $returnOrderItem) {
//                        file_put_contents($fileName, $barcode . ";" . $returnOrderItem->product_barcode . ";" . $returnOrderItem->expected_qty . ";" . "\n", FILE_APPEND);
//                    }
//                } else {
				if (!empty($returnOrder->extra_fields)) {
					$json = Json::decode($returnOrder->extra_fields);
//                        VarDumper::dump($json,10,true);
					$Sezon = '';
					$DepoTanim = '';
					$IrsaliyeBuyerGroup = '';
					$IrsaliyeMerchGroup = '';
					$IrsaliyeMiktar = '';
					if (isset($json['koliResponse']) && !empty($json['koliResponse'])) {
						$key = 'koliResponse';
						$Sezon = $json[$key]['Sezon'];
						$DepoTanim = $json[$key]['DepoTanim'];
						$IrsaliyeBuyerGroup = $json[$key]['IrsaliyeBuyerGroup'];
//                            $IrsaliyeMiktar = $json[$key]['IrsaliyeMiktar'];
						$IrsaliyeMerchGroup = $json[$key]['IrsaliyeMerchGroup'];
					} else if (isset($json['KoliIadeKabulResult->Koli']) && !empty($json['KoliIadeKabulResult->Koli'])) {
						$key = 'KoliIadeKabulResult->Koli';
						$Sezon = $json[$key]['Sezon'];
						$DepoTanim = $json[$key]['DepoTanim'];
						$IrsaliyeBuyerGroup = $json[$key]['IrsaliyeBuyerGroup'];
//                            $IrsaliyeMiktar = $json[$key]['IrsaliyeMiktar'];
						$IrsaliyeMerchGroup = $json[$key]['IrsaliyeMerchGroup'];
					}


					if (isset($json['KoliDetay->KzkDCIadeDetay'])) {
						foreach ($json['KoliDetay->KzkDCIadeDetay'] as $returnOrderItem) {
							file_put_contents($fileName,
								$i . ";"
								. $barcode . ";"
								. $returnOrderItem['Barkod'] . ";"
								. $returnOrderItem['Miktar'] . ";"
								. $Sezon . ";"
								. $DepoTanim . ";"
								. $IrsaliyeBuyerGroup . ";"
								. $IrsaliyeMerchGroup . ";"
								. $ourBox . ";"
								//. $IrsaliyeMiktar . ";"
								. "\n", FILE_APPEND);
						}
					} else {
						die('-NO-');
					}

				}

//                }
			} else {
				echo $i . " : " . $barcode . " : НЕ НАЙДЕН<br />";
			}

//            if($i == 50 ) break;
		}

//        VarDumper::dump($returnItemsOnStock,10,true);

//        $i = 0;
//        $items = ['2220003733011','2220003733028','2220003733035','2220003733059','2220003734209'];
//        foreach ($items as $barcode) {
//            $returnOrder = ReturnOrder::find()->andWhere(['LIKE','extra_fields',trim($barcode)])->one();
//            $i += 1;
//            if($returnOrder) {
//                $returnOrderItems = ReturnOrderItems::find()->andWhere(['return_order_id'=>$returnOrder->id])->all();
//                if($returnOrderItems) {
//                    foreach ($returnOrderItems as $returnOrderItem) {
//                        file_put_contents('ozet-old5.csv', $barcode . ";" . $returnOrderItem->product_barcode . ";" . $returnOrderItem->expected_qty . ";" . "\n", FILE_APPEND);
//                    }
//                } else {
//                    if(!empty($returnOrder->extra_fields)) {
//                        $json = Json::decode($returnOrder->extra_fields);
//                        VarDumper::dump($json,10,true);
//
//                        if(isset($json['KoliDetay->KzkDCIadeDetay'])) {
//                            foreach ($json['KoliDetay->KzkDCIadeDetay'] as $returnOrderItem) {
//                                file_put_contents('ozet-old5.csv', $barcode . ";" . $returnOrderItem['Barkod'] . ";" . $returnOrderItem['Miktar'] . ";" . "\n", FILE_APPEND);
//                            }
//                        } else {
//                            die('-NO-');
//                        }
//
//                    }
//
//                }
//            } else {
//                echo $i." : ".$barcode." : НЕ НАЙДЕН<br />";
//            }
//        }


		return $this->render('index');
	}

	public function actionPrintEmptyBox()
	{ // /other/one/print-empty-box
		die("BEGIN -DIE print-empty-box-");
		$boxesBarcode = [];
		$pathToCSVFile = 'tmp-file/defacto/21-11-2016/KZKDCLC4000.csv';
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ";")) !== false) {
				$boxesBarcode[$data[0]] = $data[0];
			}
		}
		return $this->render('_print-any-barcode', ['boxesBarcode' => $boxesBarcode]);
	}


	/*
    *
    * */
	public function actionStockWithSkuidDefacto() // OK
	{   // stock-with-skuid-defacto
		// die("BEGIN -DIE stock-with-skuid-defacto");

//        die("dddd");

		$lotsOnStock = Stock::find()
							->select('product_barcode, count(product_barcode) as qty ')
							->andWhere([
								'client_id' => Client::CLIENT_DEFACTO,
								'status_availability' => Stock::STATUS_AVAILABILITY_YES,
							])->andWhere("field_extra1 = ''")
							->groupBy('product_barcode')
							->asArray()
			//->limit()
							->all();

		$fileName = '-2016-12-22-stock-and-sku-id-defacto-' . time() . '.csv';
		file_put_contents($fileName,
			"Lot Barcode" . ";" .
			"Qty" . ";" .
			"Defacto sku id" . ";" .
			"\n", FILE_APPEND);
		$x = [];
		foreach ($lotsOnStock as $lot) {

			$barcode = $lot['product_barcode'];
			$qty = $lot['qty'];

			if (!isset($x[$barcode])) {
				$skqId = $this->getAPISkuIdFromDefacto($lot['product_barcode']);

				$x[$barcode] = $skqId;
				Stock::updateAll(['field_extra1' => $skqId],
					[
						'client_id' => Client::CLIENT_DEFACTO,
						'product_barcode' => $lot['product_barcode'],
					]
				);
			}
//            $skqId = isset($x[$barcode]) ? $x[$barcode] : $this->getAPISkuIdFromDefacto($lot['product_barcode']);
//            $x[$barcode] = $skqId;

			file_put_contents($fileName,
				$barcode . ";"
				. $qty . ";"
				. $skqId . ";"
				. "\n", FILE_APPEND);
		}

		return $this->render('index');
	}

	private function getAPISkuIdFromDefacto($LotOrSingleBarcode)
	{
		if (!empty($LotOrSingleBarcode)) {

			$api = new DeFactoSoapAPIV2();
			$params['request'] = [
				'BusinessUnitId' => '1029',
				'PageSize' => 0,
				'PageIndex' => 0,
				'CountAllItems' => false,
				'ProcessRequestedDataType' => 'Full',
				'LotOrSingleBarcode' => $LotOrSingleBarcode,
			];
//
			$result = $api->sendRequest('GetMasterData', $params);
			if ($resultDataArray = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.Data.MasterDataThreePL')) {
				$resultDataArray = count($resultDataArray) <= 1 ? [$resultDataArray] : $resultDataArray;
			} else {
				$resultDataArray = [];
			}

			foreach ($resultDataArray as $value) {
//            VarDumper::dump($value,10,true);
				return $value->SkuId;
			}
		}

		return -1;
	}

	public function actionAddCrossDockItem()
	{ // /other/one/add-cross-dock-item
		die('actionAddCrossDockItem');
		$crossDockID = 3945;
		$crossDockShopCode = '2470';
		$crossDockItemBoxBarcode = '14100694391';
		$crossDockBoxM3 = '32';
		$crossDockItemLotBarcode = '9000006350087';
		$crossDockItemLotQuantity = '2';
		$numberPlacesQty = 1;

		if ($crossDock = CrossDock::findOne($crossDockID)) {
			$crossDockItem = CrossDockItems::find()->andWhere(['cross_dock_id' => $crossDockID])->one();
			if (!$crossDockItem) {
				return false;
			}

			$productSerializeDataDefault = CrossDockItemService::extractJsonData($crossDockItem->product_serialize_data);
			if (empty($productSerializeDataDefault)) {
				return false;
			}
			// ProductSerializeData DTO
			$productSerializeDataDTO = new ProductSerializeDataDTO(
				'',
				ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.FromBusinessUnitId'),
				$crossDockItemBoxBarcode,
				$numberPlacesQty,
				ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.SkuId'),
				$crossDockItemLotBarcode,
				$crossDockItemLotQuantity,
				ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.Status'),
				ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.AppointmentBarcode'),
				$crossDockShopCode, //ToBusinessUnitId
				ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.FlowType')
			);
			// CrossDockItem DTO
			$crossDockItemDTO = new CrossDockItemDTO(
				$crossDockItemBoxBarcode,
				$numberPlacesQty,
				CrossDockItemService::getBoxM3($crossDockBoxM3),
				0,
				0,
				'',
				$crossDockItemLotBarcode,
				CrossDockItemService::makePartyNumber($crossDock->internal_barcode)
			);

			$crossDockItemService = new CrossDockItemService($crossDock, $productSerializeDataDTO, $crossDockItemDTO);
			$crossDockItemService->create();
		}


		return $this->render('index', []);
	}


	public function actionCompleteCrossDock() // Ok
	{ // other/one/complete-cross-dock
		die("actionCompleteCrossDock - DIE");

//        $crossDockParty = 'D10AA00033870'; // Y
//        $crossDockParty = 'D10AA00035137'; // Y
//        $crossDockParty = 'D10AA00036216'; // Y
//        $crossDockParty = 'D10AA00036535'; // Y
//        $crossDockParty = 'D10AA00036666'; // Y
//        $crossDockParty = 'D10AA00038405'; // Y
//        $crossDockParty = 'D10AA00039294'; // Y
//        $crossDockParty = 'D10AA00039343'; // Y
//        $crossDockParty = 'D10AA00028448'; // Y
//        $crossDockParty = 'D10AA00030440'; // Y
//        $crossDockParty = 'D10AA00031398'; // Y
//        $crossDockParty = 'D10AA00031886'; // Y
//        $crossDockParty = 'D10AA00033034'; // Y
//        $crossDockParty = 'D10AA00035136'; // Y
//        $crossDockParty = 'D10AA00039893'; // Y
		$crossDockParty = 'D10AA00064157'; // Y

//        die("actionCompleteCrossDock - DIE");
		//START Принимаем сразу и CROSS-DOCK если он есть
		$api = new DeFactoSoapAPIV2Manager();
		if ($crossDocks = CrossDock::findAll(['party_number' => $crossDockParty, 'client_id' => Client::CLIENT_DEFACTO])) {
			foreach ($crossDocks as $crossDock) {
				$row = [];
				$rows = [];
				if ($crossDockItems = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id])) {
					foreach ($crossDockItems as $crossDockItem) {
						$rowTmp = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataCrossDock($crossDockItem);
						$rows[] = array_shift($rowTmp);
					}
				}

				if (!empty($rows)) {
//                    foreach($rows as $itemKey=>$rowValue) {
//                        if($rowValue['LcOrCartonLabel'] == '2330002558604') {
//                            unset($rows[$itemKey]);

//                            $products = [];
//                            $products['8681555571287'] = '2.00';
//                            $products['8681555571263'] = '1.00';
//                            $products['8681555571270'] = '3.00';
//                            $products['8681555571256'] = '4.00';
//                            $products['8681555571249'] = '2.00';
//
//                            foreach($products as $productBarcode=>$productQty) {
//                                $itemKey += 1;
//                                $rows[$itemKey]['InboundId'] = '';
//                                $rows[$itemKey]['AppointmentBarcode'] = 'D10AA00064157';
//                                $rows[$itemKey]['LcOrCartonLabel'] = '2330002558604';
//                                $rows[$itemKey]['LotOrSingleBarcode'] = (string)$productBarcode; // 9000005164135
//                                $rows[$itemKey]['LotOrSingleQuantity'] = $productQty; // 9000005164135
//                            }
//                        }
//                    }
					$row = [];
					$rows = [];
					$products = [];
					$itemKey = 0;
					$products['8681555571287'] = '2.00';
					$products['8681555571263'] = '1.00';
					$products['8681555571270'] = '3.00';
					$products['8681555571256'] = '4.00';
					$products['8681555571249'] = '2.00';

					foreach ($products as $productBarcode => $productQty) {
//                        $itemKey += 1;
						$rows[$itemKey]['InboundId'] = '';
						$rows[$itemKey]['AppointmentBarcode'] = 'D10AA00064157';
						$rows[$itemKey]['LcOrCartonLabel'] = '2330002558604';
						$rows[$itemKey]['LotOrSingleBarcode'] = (string)$productBarcode; // 9000005164135
						$rows[$itemKey]['LotOrSingleQuantity'] = $productQty; // 9000005164135
						$itemKey += 1;
					}

					$row['InBoundFeedBackThreePLResponse'] = $rows;
//                    VarDumper::dump($rows,10,true);
//                    VarDumper::dump($row,10,true);
//                   die("XX");

//                    $api->SendInBoundFeedBackData($row);
					file_put_contents("SendInBoundFeedBackData-CRoss-dock-" . $crossDockParty . ".log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				} else {
					file_put_contents("SendInBoundFeedBackData-CRoss-dock-ERROR.log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				}
			}
		}

		echo "<br />";
		echo "<br />";
		echo "<br />";

		// второй шаг.
		if ($crossDocks = CrossDock::findAll(['party_number' => $crossDockParty, 'client_id' => Client::CLIENT_DEFACTO])) {
			foreach ($crossDocks as $crossDock) {
				echo $crossDock->id . "<br />";
//                if($crossDock->id != 4305) {
//                    continue;
//                }
				$row = [];
				$rows = [];
//                die("ddd");
				if ($items = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id, 'status' => Stock::STATUS_CROSS_DOCK_SCANNED])) {
					foreach ($items as $item) {
						$rowTmp = DeFactoSoapAPIV2Manager::preparedSendCrossDockOutBoundFeedBackDataOutbound($item, $crossDock);
						$rows[] = array_shift($rowTmp);
					}
				}

//                foreach($rows as $itemKey=>$rowValue) {
//                    if($rowValue['LcBarcode'] == '2330002558604') {
////                        $rows[$itemKey]['LotOrSingleBarcode'] = '---'; // 2300003555247
////                        $rows[$itemKey]['LotOrSingleQuantity'] = '1'; // 1.00
//
//                        unset($rows[$itemKey]);
//
//                        $products = [];
//                        $products['8681555571287'] = '2.00';
//                        $products['8681555571263'] = '1.00';
//                        $products['8681555571270'] = '3.00';
//                        $products['8681555571256'] = '4.00';
//                        $products['8681555571249'] = '2.00';
//
//                        foreach($products as $productBarcode=>$productQty) {
//                            $itemKey += 1;
//                            $rows[$itemKey]['OutBoundId'] = '';
//                            $rows[$itemKey]['InBoundId'] = '';
//                            $rows[$itemKey]['LcBarcode'] = '2330002558604';
//                            $rows[$itemKey]['LotOrSingleBarcode'] = (string)$productBarcode; // 9000005164135
//                            $rows[$itemKey]['LotOrSingleQuantity'] = $productQty; // 9000005164135
//                            $rows[$itemKey]['WaybillSerial'] = 'KZK'; // KZK
//                            $rows[$itemKey]['WaybillNumber'] = '1'; // 1
//                            $rows[$itemKey]['Volume'] = '32'; // 32
//                            $rows[$itemKey]['CargoShipmentNo'] = '1'; // 1
//                            $rows[$itemKey]['InvoiceNumber'] = 'KZ-18-080'; // KZ-18-080
//                        }
//                    }
//                }

				$row = [];
				$rows = [];
				$products = [];
				$itemKey = 0;
				$products['8681555571287'] = '2.00';
				$products['8681555571263'] = '1.00';
				$products['8681555571270'] = '3.00';
				$products['8681555571256'] = '4.00';
				$products['8681555571249'] = '2.00';

				foreach ($products as $productBarcode => $productQty) {

					$rows[$itemKey]['OutBoundId'] = '';
					$rows[$itemKey]['InBoundId'] = '';
					$rows[$itemKey]['LcBarcode'] = '2330002558604';
					$rows[$itemKey]['LotOrSingleBarcode'] = (string)$productBarcode; // 9000005164135
					$rows[$itemKey]['LotOrSingleQuantity'] = $productQty; // 9000005164135
					$rows[$itemKey]['WaybillSerial'] = 'KZK'; // KZK
					$rows[$itemKey]['WaybillNumber'] = '1'; // 1
					$rows[$itemKey]['Volume'] = '32'; // 32
					$rows[$itemKey]['CargoShipmentNo'] = '1'; // 1
					$rows[$itemKey]['InvoiceNumber'] = 'KZ-18-080'; // KZ-18-080
					$itemKey += 1;
				}

				$row['OutBoundFeedBackThreePLResponse'] = $rows;
//                                VarDumper::dump($rows,10,true);
//                VarDumper::dump($row,10,true);
//                die("XX");
				if (!empty($rows)) {
//                    $api->SendOutBoundCrossDockFeedBackData($row);
					file_put_contents("SendOutBoundCrossDockFeedBackData-" . $crossDockParty . ".log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				} else {
					file_put_contents("SendOutBoundCrossDockFeedBackData-CRoss-dock-ERROR.log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				}
			}
		}

		// END
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "OK : " . $crossDockParty;
		return $this->render('index');
	}


	public function actionTestResendReturn()
	{
		// // other/one/test-resend-return
		// 5857254

		$toSendDataForAPI = [];
		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857254', //'5857248',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489458', //'2430007481901',
			'LotOrSingleBarcode' => '2300011729395', //'2300011729333',
			'LotOrSingleQuantity' => '1',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857260', //'5857248',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007492946', //'2430007481901',
			'LotOrSingleBarcode' => '2300011729463', //'2300011729333',
			'LotOrSingleQuantity' => '1',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857246', //'5857248',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007481430', //'2430007481901',
			'LotOrSingleBarcode' => '2300011729319', //'2300011729333',
			'LotOrSingleQuantity' => '1',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857252', //'5857248',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489122', //'2430007481901',
			'LotOrSingleBarcode' => '2300011729371', //'2300011729333',
			'LotOrSingleQuantity' => '1',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857258',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007491884',
			'LotOrSingleBarcode' => '2300011729449',
			'LotOrSingleQuantity' => '1',
		];


		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857250',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489038',
			'LotOrSingleBarcode' => '2300011729357',
			'LotOrSingleQuantity' => '1',
		];


		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857256',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489774',
			'LotOrSingleBarcode' => '2300011729418',
			'LotOrSingleQuantity' => '1',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857247',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007481777',
			'LotOrSingleBarcode' => '2300011729326',
			'LotOrSingleQuantity' => '1',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857253',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489226',
			'LotOrSingleBarcode' => '2300011729388',
			'LotOrSingleQuantity' => '1',
		];


		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857259',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007492496',
			'LotOrSingleBarcode' => '2300011729456',
			'LotOrSingleQuantity' => '1',
		];


		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857245',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007480199',
			'LotOrSingleBarcode' => '2300011729302',
			'LotOrSingleQuantity' => '1',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857251',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489042',
			'LotOrSingleBarcode' => '2300011729364',
			'LotOrSingleQuantity' => '1',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857257',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007490430',
			'LotOrSingleBarcode' => '2300011729432',
			'LotOrSingleQuantity' => '1',
		];

		$toSendDataForAPI['InBoundFeedBackThreePLResponse'] = $returnOrderItemProductPrepared;
		$responseAPI = [];
//        $api = new DeFactoSoapAPIV2Manager();
//        $responseAPI = $api->SendInBoundFeedBackDataReturn($toSendDataForAPI);


		VarDumper::dump($toSendDataForAPI, 10, true);
		echo "<br />";
		VarDumper::dump($responseAPI, 10, true);
		die;
	}

	public function actionMakeReturn()
	{ //make-return
		die("actionMakeReturn - DIE");

		$fileDataOur1 = $this->makeReturnFile("1-csv.csv");
		$fileDataOur2 = $this->makeReturnFile("2-csv.csv");
		$fileDataDefacto = $this->makeReturnFile("KZKIadeFormatAll-csv.csv");

		$oneFileReturnOur1 = $this->makeReturnArrayOur($fileDataOur1);
		$oneFileReturnOur2 = $this->makeReturnArrayOur($fileDataOur2);
		$oneFileReturnDefacto = $this->makeReturnArrayDefacto($fileDataDefacto);

		$fileLastOur = array_merge($oneFileReturnOur1, $oneFileReturnOur2);
//        VarDumper::dump($fileLastOur,10,true);
//        die;


		$oneFileReturnActive = [];
		foreach ($fileLastOur as $item) {
			$oneFileReturnActive[$item[1]] = $item[0];
		}

		$oneFileReturnTotal = [];
		$i = 0;
		foreach ($oneFileReturnActive as $key => $item) {
			if (isset($oneFileReturnDefacto[$key])) {
				$oneFileReturnTotal[$i]["productBarcode"] = $oneFileReturnDefacto[$key];
				$oneFileReturnTotal[$i]["boxBarcode"] = $item;
				$oneFileReturnTotal[$i]["returnCode"] = $key;
				$i += 1;
			} else {
				file_put_contents('actionMakeReturn.csv', '"' . $key . '"' . ";" . $item . ";" . "\n", FILE_APPEND);
			}
		}

		VarDumper::dump($oneFileReturnTotal, 10, true);
//        VarDumper::dump($oneFileReturnActive,10,true);
		echo "<br />ИТОгО результат:" . (count($oneFileReturnTotal));
		echo "<br />ИТОгО мы отсканировали:" . (count($oneFileReturnActive));
		echo "<br />ИТОгО дефакто:" . (count($oneFileReturnDefacto));

		/*
        $in = new InboundOrder();
        $in->client_id = Client::CLIENT_DEFACTO;
        $in->status = Stock::STATUS_INBOUND_CONFIRM;
        $in->order_number = "5669NMDX";
        $in->parent_order_number = "5669PNMDX";
        $in->order_type = InboundOrder::ORDER_TYPE_INBOUND;
        $in->expected_qty = 0;
        $in->accepted_qty = 0;
        $in->save(false);

        $expectedQty = 0;
        foreach($oneFileReturnTotal as $item) {
            $attribute = [
                'inbound_order_id' => $in->id,
                'product_barcode' => $item['productBarcode'],
                'expected_qty' => 1,
                'status' => Stock::STATUS_INBOUND_CONFIRM,
                'product_serialize_data' =>'',
                'box_barcode' =>$item['boxBarcode'],
            ];
            $inItem = new InboundOrderItem();
            $inItem->setAttributes($attribute, false);
            $inItem->save(false);

            $expectedQty += 1;
            for($i=1;$i <=1;++$i) {
                $stock = new Stock();
                $stock->client_id = $in->client_id;
                $stock->inbound_order_id = $in->id;
                $stock->inbound_order_item_id = $inItem->id;
                $stock->product_barcode = $inItem->product_barcode;
                $stock->product_model = '';
                $stock->status = Stock::STATUS_INBOUND_CONFIRM;
                $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
                $stock->inbound_client_box = $item['returnCode'];
                $stock->primary_address = $item['boxBarcode'];
                $stock->save(false);
            }
        }
        $in->expected_qty = $expectedQty;
        $in->accepted_qty = $expectedQty;
        $in->begin_datetime = time();
        $in->date_confirm = time();
        $in->save(false);
        */

		return $this->render('index');
	}

	private function makeReturnFile($fileName)
	{
		$pathToCSVFile = 'tmp-file/defacto/21-12-2016-return/csv/' . $fileName;
		$oneFileReturnActive = [];

		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ";")) !== false) {
				$oneFileReturnActive[] = $data;
			}
		}
		return $oneFileReturnActive;
	}


	private function makeReturnArrayOur($oneFileReturnActive)
	{
		$oneFileReturn = [];
		$lastKey = 0;
		foreach ($oneFileReturnActive as $key => $item) {
			if (!($lastKey % 2)) {
//                echo $item[0]."-<br />";
				$oneFileReturn [$lastKey][1] = $item[0];
//                if( substr($oneFileReturn[$lastKey][1],0,3) == "243") {
//                    die($oneFileReturn[$lastKey][1]);
//                }
			} else {
//                echo $item[0]."+<br />";
				$oneFileReturn [$lastKey - 1][0] = $item[0];
			}

			$lastKey += 1;


		}
		return $oneFileReturn;
	}

	private function makeReturnArrayDefacto($oneFileReturnActive)
	{
		$oneFileReturn = [];
		foreach ($oneFileReturnActive as $key => $item) {
			$oneFileReturn [$item[0]] = $item[1];
		}
		return $oneFileReturn;
	}


	public function actionReport()
	{ // /other/one/report

		die("actionReport - DIE");

		$pathToCSVFile = 'SendOutBoundFeedBackDataSerialize.csv';
		$report = [];
		$qty = 0;

		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 70000, "\n")) !== false) {
				$d = unserialize($data[0]);

				if (isset($d['OutBoundFeedBackThreePLResponse']) && is_array($d['OutBoundFeedBackThreePLResponse']) && !empty($d['OutBoundFeedBackThreePLResponse'])) {

					foreach ($d['OutBoundFeedBackThreePLResponse'] as $value) {
						$parentOrderNumber = substr($value['WaybillNumber'], 0, 5);
						if ($parentOrderNumber == '22483') {
							$report[] = $value;
							$qty += $value['LotOrSingleQuantity'];
						}
					}
				}
			}
			file_put_contents('report-22483.csv', "Store;OutBoundId;InBoundId;LcBarcode;LotOrSingleBarcode;LotOrSingleQuantity;WaybillSerial;WaybillNumber;Volume;CargoShipmentNo;InvoiceNumber;" . "\n");
			foreach ($report as $value) {

				$storeCode = $value['WaybillNumber'];

				$strToFile = $storeCode . ';' . $value['OutBoundId'] . ';' . $value['InBoundId'] . ';' . $value['LcBarcode'] . ';' . $value['LotOrSingleBarcode'] . ';'
					. $value['LotOrSingleQuantity'] . ';' . $value['WaybillSerial'] . ';' . $value['WaybillNumber'] . ';' . $value['Volume'] . ';'
					. $value['CargoShipmentNo'] . ';' . $value['InvoiceNumber'] . ';';
				file_put_contents('report-22483.csv', $strToFile . "\n", FILE_APPEND);
			}

			VarDumper::dump($report, 10, true);
			echo "<br />";
			echo "<br />";
			echo "<br />";
			echo $qty;
		}
		return $this->render('index');
	}

	public function actionFloor4()
	{
		// /other/one/floor4

		die("actionFloor4 - die begin");
		$pathToCSVFile = 'tmp-file/defacto/07-01-2017-inventory/floor4.csv';

		$rowAndBoxes = [];
		$rowLast = '';
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 70000, ";")) !== false) {
				if (!empty($data[0])) {
					$rowLast = trim($data[0]);
				}

				if (!empty($data[1])) {
					$rowAndBoxes[$rowLast][] = trim($data[1]);
				}
			}
		}

		foreach ($rowAndBoxes as $row => $boxes) {
			foreach ($boxes as $box) {
				$stock = Stock::find()->andWhere([
					'client_id' => Client::CLIENT_DEFACTO,
					'status_availability' => Stock::STATUS_AVAILABILITY_YES,
					'status_inventory' => Inventory::STATUS_SCAN_PROCESS,
					'inventory_primary_address' => $box,
					//'secondary_address'=>$row,
					'inventory_id' => 1,
				])->one();
				$strToFile = '';
				if ($stock) {
					$stock->primary_address = $stock->inventory_primary_address;
					$stock->status_inventory = Inventory::STATUS_SCAN_YES;
					//$stock->save(false);
					$strToFile = "OK;" . $row . ";" . $box . ";" . "\n";
				} else {
					$strToFile = "NO;" . $row . ";" . $box . ";" . "\n";
					file_put_contents('actionFloor4-NO.log', $strToFile, FILE_APPEND);
				}

				file_put_contents('actionFloor4.log', $strToFile, FILE_APPEND);
			}
		}

		VarDumper::dump($rowAndBoxes, 10, true);
		die;
		return $this->render('index');
	}


	public function actionAddInventoryPlus()
	{
		// /other/one/add-inventory-plus

		die("actionAddInventoryPlus - die begin");

//        $pathToCSVFile = 'tmp-file/defacto/07-01-2017-inventory/report-plus.csv';
//        $pathToCSVFile = 'tmp-file/defacto/03-11-2017-inventory/add-plus-to-stock.csv';
		$pathToCSVFile = 'tmp-file/defacto/30-11-2018-inventory/plus-inventory-2018-11-30.csv';
		$rowAndBoxes = [];
		$rowLast = '';
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 70000, ";")) !== false) {
				if (!empty($data[0])) {
					$rowLast = trim($data[0]);
				}

				if (!empty($data[1])) {
					if (!isset($rowAndBoxes[$rowLast][trim($data[1])])) {
						$rowAndBoxes[$rowLast][trim($data[1])] = 1;
					} else {
						$rowAndBoxes[$rowLast][trim($data[1])] += 1;
					}
				}
			}
		}


		foreach ($rowAndBoxes as $box => $products) {
			foreach ($products as $barcode => $qty) {
				for ($i = 1; $i <= $qty; ++$i) {
					$stock = new Stock();
					$stock->client_id = Client::CLIENT_DEFACTO;
					$stock->secondary_address = '';
					$stock->product_barcode = $barcode;
					$stock->primary_address = $box;
					$stock->box_barcode = '';
					$stock->box_size_barcode = '';
					$stock->box_kg = '';
					$stock->box_size_m3 = '';
					$stock->outbound_order_id = '0';
					$stock->outbound_picking_list_id = '0';
					$stock->outbound_picking_list_barcode = '';
					$stock->scan_out_datetime = '';
					$stock->scan_out_employee_id = '0';
					$stock->status = Stock::STATUS_NOT_SET;
					$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
					$stock->system_status = 'inventory-plus-20181130';
					$stock->system_status_description = 'это плюсы после инвентаризации';
					//$stock->save(false);
				}
			}
		}

		VarDumper::dump($rowAndBoxes, 10, true);
		die;
	}

	public function actionAddInventoryPlusV1()
	{
		// /other/one/add-inventory-plus-v1

		die("actionAddInventoryPlus V 1 - die begin");

//        $pathToCSVFile = 'tmp-file/defacto/07-01-2017-inventory/report-plus.csv';
//        $pathToCSVFile = 'tmp-file/defacto/03-11-2017-inventory/add-plus-to-stock.csv';
//        $pathToCSVFile = 'tmp-file/defacto/30-11-2018-inventory/plus-inventory-2018-11-30.csv';
//        $pathToCSVFile = 'tmp-file/defacto/30-11-2018-inventory/lost-inventory2018-11-30.csv';
//        $pathToCSVFile = 'tmp-file/defacto/30-11-2018-inventory/in-order-but-in-inventory2018-11-30.csv';
//


		$pathToCSVFile = 'tmp-file/defacto/09-12-2019-inventory/plus-09-12-2019.csv';
		$pathToCSVFile = 'tmp-file/defacto/09-12-2019-inventory/addition-pluses-09-12-2019-4-plus.csv';

		$products = [];
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 70000, ";")) !== false) {
				$products [] = [
					'boxBarcode' => trim($data[0]),
					'lotBarcode' => trim($data[1]),
					'lcBarcode' => trim(''),
				];
			}
		}

		array_shift($products);


		foreach ($products as $barcode) {
			$stock = new Stock();
			$stock->client_id = Client::CLIENT_DEFACTO;
			$stock->secondary_address = '';
			$stock->product_barcode = $barcode['lotBarcode'];
			$stock->primary_address = $barcode['boxBarcode'];
			$stock->inbound_client_box = $barcode['lcBarcode'];
			$stock->box_barcode = '';
			$stock->box_size_barcode = '';
			$stock->box_kg = '';
			$stock->box_size_m3 = '';
			$stock->outbound_order_id = '0';
			$stock->outbound_picking_list_id = '0';
			$stock->outbound_picking_list_barcode = '';
			$stock->scan_out_datetime = '';
			$stock->scan_out_employee_id = '0';
			$stock->status = Stock::STATUS_NOT_SET;
			$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
			$stock->system_status = 'inventory-plus-20191206';
			$stock->system_status_description = 'это плюсы после инвентаризации 4доп товара';
//            if (!$stock->save(false)) {
//                echo "NO " . $barcode['lotBarcode'];
//            }
		}

		VarDumper::dump($products, 10, true);
		die;
	}


	public function actionUpdateProductSku()
	{
		// /other/one/update-product-sku
		// report-inventory-all-on-stock-csv
		die('actionUpdateProductSku die begin');

		$pathToCSVFile = 'tmp-file/defacto/12-01-2017/report-inventory-all-on-stock-csv.csv';
		$rowAndBoxes = [];
		$lotBr = [];
		$i = 0;
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 70000, ";")) !== false) {

				$stockExist = Stock::find()
								   ->andWhere(['client_id' => Client::CLIENT_DEFACTO, 'product_barcode' => $data[2], 'product_model' => ''])
								   ->orWhere(['client_id' => Client::CLIENT_DEFACTO, 'product_barcode' => $data[2], 'product_model' => '509'])
								   ->orWhere(['client_id' => Client::CLIENT_DEFACTO, 'product_barcode' => $data[2], 'product_model' => null])
								   ->orWhere(['client_id' => Client::CLIENT_DEFACTO, 'product_barcode' => $data[2], 'product_model' => '100'])
								   ->exists();


				if ($stockExist) {
					Stock::updateAll([
						'product_model' => $this->getLotSkuFromDefactoAPI($data[2]),
					], [
						'product_barcode' => $data[2],
						'client_id' => Client::CLIENT_DEFACTO
					]);
				}


//                if(!isset($lotBr[$data[2]])) {
//                    $data[4] = $this->getLotSkuFromDefactoAPI($data[2]);
//                    $lotBr[$data[2]] = $data[4];
//                } else {
//                    $data[4] = $lotBr[$data[2]];
//                }
//
//                $rowAndBoxes[] = $data;

				$i += 1;
				if ($i == 200) {
					sleep(1);
					$i = 0;
				}
				if (!isset($lotBr[$data[2]])) {
					$stockOne = Stock::find()->select('product_model')->andWhere(['client_id' => Client::CLIENT_DEFACTO, 'product_barcode' => $data[2]])->one();
					if ($stockOne) {
						$data[4] = $stockOne->product_model;
						$lotBr[$data[2]] = $data[4];
					} else {
						$lotBr[$data[2]] = '';
					}
				}
//                } else {
//                    $data[4] = $lotBr[$data[2]];
//                }

				$data[4] = $lotBr[$data[2]];

				$rowAndBoxes[] = $data;
			}
		}

		foreach ($rowAndBoxes as $item) {
			file_put_contents('updateProductSkuInventoryResult-5.csv', $item['0'] . ";" . $item['1'] . ";" . $item['2'] . ";" . $item['3'] . ";" . $item['4'] . ";" . "\n", FILE_APPEND);
		}

//        VarDumper::dump($rowAndBoxes,10,true);
//        die;
		return $this->render('index');
	}

	public function actionReturnReportKitap()
	{
		// /other/one/return-report-kitap

		die("actionReturnReportKitap - die begin");

		$pathToCSVFile = 'tmp-file/defacto/12-01-2017/Kitap1-csv.csv';
		$rowAndBoxes = [];
		$stockIds = '';
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 70000, ";")) !== false) {
				$r = ReturnOrder::find()->select('order_number')->andWhere(['LIKE', 'extra_fields', $data[0]])->one();
				if ($r) {
					$storeCodeTMP = explode(";", $r->order_number);
					$store = Store::find()->andWhere(['shop_code2' => $storeCodeTMP['1']])->one();
					$inbound = InboundOrder::find()->andWhere(['order_number' => $r->order_number])->one();
					$stock = Stock::find()->andWhere(['inbound_order_id' => $inbound->id])->one();
					$stockAudit = StockAudit::find()->andWhere([
						'parent_id' => $stock->id,
						'field_name' => 'outbound_order_id',
						'after_value_text' => '0',
					])->one();

//                    $onStock = '';
					$outboundOrderCell = '';
					$outboundOrderDateLeftCell = '';
					if ($stock->status_availability == Stock::STATUS_AVAILABILITY_YES) {
						$onStock = 'On Stock';
					} elseif ($outbound = OutboundOrder::find()->andWhere(['id' => $stock->outbound_order_id])->one()) {
						$outboundOrderCell = $outbound->parent_order_number . " / " . $outbound->order_number;
						$outboundOrderDateLeftCell = date("Y-m-d", $outbound->date_left_warehouse);
					}

					if ($stockAudit) {
						if ($outbound = OutboundOrder::find()->andWhere(['id' => $stockAudit->before_value_text])->one()) {
							$outboundOrderCell = $outbound->parent_order_number . " / " . $outbound->order_number;
							$outboundOrderDateLeftCell = date("Y-m-d", $outbound->date_left_warehouse);
						}
					}

					$data[3] = "\"" . $r->order_number . "\"";
					$data[4] = "\"" . $store->city_lat . ' ' . $store->shopping_center_name_lat . "\"";
					$data[5] = $storeCodeTMP['1'];
					$data[6] = date("Y-m-d", $inbound->updated_at);
					$data[7] = $stock->product_barcode;
//                    $data[8] = $onStock;
//                    $data[9] = $stock->status_lost;
					$data[8] = $outboundOrderCell;
					$data[9] = $outboundOrderDateLeftCell;
					$data[10] = $stock->id;
//                    $stockIds .= $stock->id.',';
					$rowAndBoxes[] = $data;
				} else {
					echo "error = " . $data[0];
				}
			}
		}
		file_put_contents('Kitap-result-1.csv', "Pre Pack Sku Code; Style Code;Reading Count;Return number; Store; Store Code; Created Code 222; Barcode; Outbound order; Date Left warehouse; " . "\n", FILE_APPEND);
		foreach ($rowAndBoxes as $item) {
			file_put_contents('Kitap-result-1.csv', $item['0'] . ";" . $item['1'] . ";" . $item['2'] . ";" . $item['3'] . ";"
				. $item['4'] . ";" . $item['5'] . ";" . $item['6']
				. ";" . $item['7'] . ";" . $item['8'] . ";" . $item['9'] . ";" . $item['10'] . ";" . "\n", FILE_APPEND);
		}

		VarDumper::dump($rowAndBoxes, 10, true);
//        VarDumper::dump($stockIds,10,true);
		die;
	}


	private function getLotSkuFromDefactoAPI($lotBArcod)
	{
		$api = new DeFactoSoapAPIV2();
		$dataFromAPI = $api->getMasterData(null, $lotBArcod);
		if (!$dataFromAPI['HasError']) {
			if (!empty($dataFromAPI['Data'])) {
				$resultDataArray = $dataFromAPI['Data'];

				$resultDataArray = count($resultDataArray) == 1 ? [$resultDataArray] : $resultDataArray;
				foreach ($resultDataArray as $resultData) {

					return $resultData->ShortCode;
				}
			}
		}

		return '';
	}


	public function actionResetBoxCrossDockM3()
	{ // /other/one/reset-box-cross-dock-m3

		$crossDockID = 4463;
		$totalBoxM3 = 0;
		$totalBoxM3s = CrossDockItems::find()->select('box_m3')->andWhere(['cross_dock_id' => $crossDockID])->groupBy('box_barcode')->asArray()->column();
		foreach ($totalBoxM3s as $k) {
			$totalBoxM3 += $k;
		}

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo $totalBoxM3 . "<br />";

		return $this->render('index');


		die('actionResetBoxCrossDockM3');
		$cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize ' => '2560 MB');
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);


		$xls = \PHPExcel_IOFactory::load('tmp-file/defacto/20170216/D10AA00010441.xlsx');
		$xls->setActiveSheetIndex(0);
		$dataArray = $xls->getActiveSheet()->toArray();
		$crossDockIDs = [];
		foreach ($dataArray as $box) {
			if (isset($box['1']) && !empty($box['2'])) {
				echo $box['1'] . ' - ' . $box['2'] . "<br />";
				$crossDockItems = CrossDockItems::find()->andWhere(['box_barcode' => $box['1']])->all();
				foreach ($crossDockItems as $crossDockItem) {
					$crossDockItem->box_m3 = $box['2'];
					$crossDockItem->save(false);

					$crossDockIDs[$crossDockItem->cross_dock_id] = $crossDockItem->cross_dock_id;
				}
			}
		}

		foreach ($crossDockIDs as $id) {
			$crossDock = CrossDock::find()->andWhere(['id' => $id])->one();
			$totalBoxM3 = CrossDockItems::find()->select('sum(box_m3) as total_box_m3')->andWhere(['cross_dock_id' => $crossDock->id])->groupBy('box_barcode')->scalar();
			$crossDock->box_m3 = round($totalBoxM3, 3);
			$crossDock->save(false);
		}

		$crossDockIDs = CrossDock::find()->select('id')->andWhere(['client_id' => Client::CLIENT_DEFACTO, 'party_number' => 'D10AA00010441'])->column();

		foreach ($crossDockIDs as $id) {
			$crossDock = CrossDock::find()->andWhere(['id' => $id])->one();
			$totalBoxM3 = 0;//CrossDockItems::find()->select('sum(box_m3) as total_box_m3')->andWhere(['cross_dock_id'=>$crossDock->id])->groupBy('box_barcode')->scalar();
			$totalBoxM3s = CrossDockItems::find()->select('box_m3')->andWhere(['cross_dock_id' => $crossDock->id])->groupBy('box_barcode')->asArray()->column();
			foreach ($totalBoxM3s as $k) {
				$totalBoxM3 += $k;
			}

			$crossDock->box_m3 = round($totalBoxM3, 3);
			$crossDock->save(false);
		}

//       $crossDock = CrossDock::find()->andWhere([''])->one();

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//        VarDumper::dump($dataArray,10,true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		return $this->render('index');
	}

	public function actionFixResendInbound()
	{ // /other/one/fix-resend-inbound

		die("actionFixResendInbound - DIE");
		$items = [];
		$i = 0;
		$pathToCSVFile = 'tmp-file/defacto/25032017/Z.csv';
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ":")) !== false) {
				//VarDumper::dump($data, 10, true);
				//echo "<br />";
				if (isset($data[0]) && isset($data[1])) {
					$i += 1;
				}
				if (!isset($data[1])) {
					$items[$i][] = array_shift($data);
				}
			}
		}

//        $api = new DeFactoSoapAPIV2Manager();
		$rows = [];
		foreach ($items as $item) {
			$row[] = [
				'InboundId' => $item[0],//'48', // если плюсы то передаем null
				'AppointmentBarcode' => $item[1],//'D10AA00000043',
				'LcOrCartonLabel' => $item[2], //'2430000072423',
				'LotOrSingleBarcode' => $item[3], //'9000003635927',
				'LotOrSingleQuantity' => $item[4], //'1',
			];
			$rows['InBoundFeedBackThreePLResponse'] = $row;
//            $api->SendInBoundFeedBackData($rows);
			VarDumper::dump($rows, 10, true);
			$row = [];
		}

//        VarDumper::dump($rows,10,true);
	}


	public function actionAddInboundNumberPlace()
	{ // /other/one/add-inbound-number-place

		$inboundOrderItems = InboundOrderItem::find()->all();
		foreach ($inboundOrderItems as $inboundOrderItem) {
			if (!empty($inboundOrderItem->product_serialize_data)) {
				$productSerializeData = Json::decode($inboundOrderItem->product_serialize_data);

				if (empty($productSerializeData) || !is_array($productSerializeData)) {
					continue;
				}
				if (!isset($productSerializeData['extra_fields']) || empty($productSerializeData['extra_fields'])) {
					continue;
				}

				$extraFields = Json::decode($productSerializeData['extra_fields']);

				if (empty($extraFields) || !is_array($extraFields)) {
					continue;
				}
				if (!isset($extraFields['apiLogValue']['NumberOfCartons']) || empty($extraFields['apiLogValue']['NumberOfCartons'])) {
					continue;
				}

				//VarDumper::dump($extraFields,10,true);
				$inboundOrderItem->expected_number_places_qty = $extraFields['apiLogValue']['NumberOfCartons'];
				$inboundOrderItem->save(false);
			}
		}

		return 'ok';
	}

	// Заполняем отчет, добавляем модель и числоо позиций в лоте
	public function actionInboundWetBoxReport()
	{ // other/one/inbound-wet-box-report

		die("other/one/inbound-wet-box-report DIE");


		$cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize ' => '2560 MB');
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$rootPath = 'tmp-file/defacto/20170607/';
		$xls = \PHPExcel_IOFactory::load($rootPath . '11111.xlsx');
		$xls->setActiveSheetIndex(0);
		$dataArray = $xls->getActiveSheet()->toArray();

		$params['request'] = [
			'BusinessUnitId' => '1029',
			'PageSize' => '0',
			'PageIndex' => '0',
			'CountAllItems' => false,
			'ProcessRequestedDataType' => 'Full',
		];
		$api = new DeFactoSoapAPIV2();
		foreach ($dataArray as $lotBarcode) {
			$lotBarcode = array_shift($lotBarcode);
//            VarDumper::dump($lotBarcode,10,true);
//            die;
			if (!empty($lotBarcode)) {
				$params['request']['LotOrSingleBarcode'] = $lotBarcode;

				$result = $api->sendRequest('GetMasterData', $params);

				if ($resultDataArray = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.Data.MasterDataThreePL')) {
					$resultDataArray = count($resultDataArray) <= 1 ? $resultDataArray : array_shift($resultDataArray);
				}

//            VarDumper::dump($resultDataArray,10,true);
//            VarDumper::dump($resultDataArray->ShortCode,10,true);
//            VarDumper::dump($resultDataArray->Nop,10,true);
				$shortCode = $resultDataArray->ShortCode;
				$nop = $resultDataArray->Nop;
//
				//file_put_contents($rootPath . 'wet-box-report.xlsx', $lotBarcode . ";" . $shortCode . ";" . $nop . ";" . "\n", FILE_APPEND);
			} else {
				//file_put_contents($rootPath . 'wet-box-report.xlsx', "" . ";" . "" . ";" . "" . ";" ."\n", FILE_APPEND);
			}
		}
	}

	public function actionNestedSet()
	{ // other/one/nested-set
		$data = [
			[
				'title' => "Одежда",
				'left' => 1,
				'right' => 22
			],
			[
				'title' => "Мужская",
				'left' => 2,
				'right' => 9
			],
			[
				'title' => "Женская",
				'left' => 10,
				'right' => 21
			],
			[
				'title' => "Костюмы",
				'left' => 3,
				'right' => 8
			],
			[
				'title' => "Платья",
				'left' => 11,
				'right' => 16
			],
			[
				'title' => "Юбки",
				'left' => 17,
				'right' => 18
			],
			[
				'title' => "Блузы",
				'left' => 19,
				'right' => 20
			],
			[
				'title' => "Брюки",
				'left' => 4,
				'right' => 5
			],
			[
				'title' => "Жакеты",
				'left' => 6,
				'right' => 7
			],
			[
				'title' => "Вечерние",
				'left' => 12,
				'right' => 13
			],
			[
				'title' => "Летние",
				'left' => 14,
				'right' => 15
			]
		];

		$data = [
			[
				'title' => '10',
				'left' => '4',
				'right' => '5',
			],
			[
				'title' => '11',
				'left' => '6',
				'right' => '7',
			],
			[
				'title' => '12',
				'left' => '14',
				'right' => '15',
			],
			[
				'title' => '13',
				'left' => '16',
				'right' => '17',
			],
			[
				'title' => '14',
				'left' => '18',
				'right' => '19',
			],
			[
				'title' => '15',
				'left' => '26',
				'right' => '27',
			],
			[
				'title' => '16',
				'left' => '28',
				'right' => '29',
			],
			[
				'title' => '5',
				'left' => '3',
				'right' => '8',
			],
			[
				'title' => '6',
				'left' => '11',
				'right' => '12',
			],
			[
				'title' => '7',
				'left' => '13',
				'right' => '20',
			],
			[
				'title' => '8',
				'left' => '21',
				'right' => '22',
			],
			[
				'title' => '9',
				'left' => '25',
				'right' => '30',
			],
			[
				'title' => '2',
				'left' => '2',
				'right' => '9',
			],
			[
				'title' => '3',
				'left' => '10',
				'right' => '23',
			],
			[
				'title' => '4',
				'left' => '24',
				'right' => '31',
			],
			[
				'title' => '1',
				'left' => '1',
				'right' => '32',
			],
		];


//        VarDumper::dump($data,10,true);
		//$tmp = [];
		$flag = 1;
		$lenght = count($data);
		$i = 1;
		while ($flag) {
			if ($data[$i - 1]['left'] > $data[$i]['left']) {  // 1[0] > 2[1]
				$tmp = $data[$i - 1];
				$data[$i - 1] = $data[$i];
				$data[$i] = $tmp;
			}
			$i += 1;
			if (empty($tmp) && $i == $lenght) {
				$flag = false;
			}
			if ($i >= $lenght) {
				$i = 1;
				$tmp = [];
			}
		}

		$level = 0;
		$oldLevel = 0;
		$stack = [];
		$up = false;
		foreach ($data as $key => $value) {
			echo 'level: ' . $level . ' oldLevel: ' . $oldLevel . ' title: ' . $data[$key]['title'] . "<br />";
			if ($data[$key]['left'] == 1) {
				$data[$key]['level'] = 0;
				$level += 1;
			} else {

				if ($up == false) {
					foreach ($stack as $stockValue) {
						if ($stockValue['right'] + 1 == $value['left']) {
							$level = $stockValue['level'];
						}
					}
					$data[$key]['level'] = $level;
				}

				if ($value['left'] + 1 != $value['right']) {
					$data[$key]['level'] = $level;
					$stack[] = $data[$key];
					$level += 1;
					$up = true;
				}

				if ($value['left'] + 1 == $value['right']) {
					$data[$key]['level'] = $level;
					$up = false;
				}
			}
		}
		echo "<br />";
		echo "<br />";
		foreach ($data as $value) {
			$l = ($value['level'] < 0 ? $value['level'] * -1 : $value['level']);
			echo str_repeat(' - ', $l) . " " . $value['title'] . "<br />";
		}

		echo "<br />";
		echo "<br />";
		VarDumper::dump($stack, 10, true);
		echo "<br />";
		echo "<br />";
//        VarDumper::dump($data,10,true);
		die;
	}

	public function actionPrintBoxLabel()
	{ // /other/one/print-box-label

//        $items = [];
//        $model = [];
//        $outboundOrderModel = [];
//        $mappingOurBobBarcodeToDefacto = [];
		$outboundOrderID = 15777;

		$items = Stock::find()
					  ->select('id,outbound_order_id, box_barcode, box_size_m3')
					  ->where([
						  'outbound_order_id' => $outboundOrderID,
						  'status' => [
							  Stock::STATUS_OUTBOUND_SCANNED,
							  Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
							  Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
						  ],
					  ])
					  ->groupBy('box_barcode')
//            ->orderBy('box_barcode')
					  ->orderBy('scan_out_datetime')
					  ->asArray()
					  ->all();

		$outboundOrderModel = OutboundOrder::findOne($outboundOrderID);
		$dpo = TlDeliveryProposalOrders::findOne(['order_id' => $outboundOrderID, 'order_type' => TlDeliveryProposalOrders::ORDER_TYPE_RPT]);
		$model = TlDeliveryProposal::findOne($dpo->tl_delivery_proposal_id);


		$mappingOurBobBarcodeToDeFacto = [];
		$mappingOurBobBarcodeToDeFactoRevers = [];
		$pathToCSVFile = 'mappingOutboundBarcodeToDefacto.csv';
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ";")) !== false) {
				$mappingOurBobBarcodeToDeFacto[$data[1]] = $data[2];
//                $mappingOurBobBarcodeToDeFacto[$data[1]]['LC'] = $data[2];
//                $mappingOurBobBarcodeToDeFacto[$data[1]]['WAYBILL'] = $data[3];

//                $mappingOurBobBarcodeToDeFactoRevers[$data[2]] = $data[1];
			}
		}


		return $this->render('print/_box-label-pdf', [
			'boxes' => $items,
			'model' => $model,
			'outboundOrderModel' => $outboundOrderModel,
			'mappingOurBobBarcodeToDefacto' => $mappingOurBobBarcodeToDeFacto,
		]);
	}


	public function actionAllocateTest()
	{ // other/one/allocate-test
//        $r = AllocateManager::strategyClearEmptyBox('2300000068924',3);
//        Stock::AllocateByOutboundOrderId(12366);
//        VarDumper::dump($r,10,true);
//        $ordersTest = [
//            186722,
//        ];
//
//        $outbounds = OutboundOrder::find()->andWhere(["parent_order_number"=>$ordersTest])->all();
//        foreach($outbounds as $outbound) {
//            Stock::resetByOutboundOrderId($outbound->id);
//        }

		foreach ([19027] as $id) {
//            Stock::resetByOutboundOrderId($id);
		}

//        $outbounds = OutboundOrder::find()->andWhere(['client_id' => Client::CLIENT_DEFACTO, "status" => Stock::STATUS_OUTBOUND_NEW, 'cargo_status' => OutboundOrder::CARGO_STATUS_NEW])->orderBy('id')->all();

		$outbounds = OutboundOrder::find()->andWhere([
			'client_id' => Client::CLIENT_DEFACTO,
			"status" => Stock::STATUS_OUTBOUND_NEW,
			'cargo_status' => OutboundOrder::CARGO_STATUS_NEW,
//                'parent_order_number' => [
			//10046900, // ok
			//10046899, // +-
//                    10083455, // +-
//                    10083447, // -
			//10093707, // ok
			//10093708,
//                ]
		])->orderBy('id')
								  ->all();

		foreach ($outbounds as $outbound) {
//            Stock::AllocateByOutboundOrderId($outbound->id);
			Stock::allocateAnyBarcodeByOutboundOrderId($outbound->id);
//            die;
		}

//        die("OK");
		echo "<h1 class='text-center'><br />Резервация успешна!<br /></h1>";
		return $this->render('index');
	}

	public function actionExportRusBelReportTest()
	{ // other/one/export-rus-bel-report-test
		$outboundOrderID = 13521;
		$outboundOrder = OutboundOrder::findOne($outboundOrderID);
		$outboundStocks = Stock::find()
							   ->select('id, inbound_order_id, product_barcode,box_barcode,box_size_barcode,box_size_m3, box_kg, count(id) as qtyLotInBox')
							   ->andWhere([
								   'outbound_order_id' => $outboundOrder->id,
								   'status' => [
									   Stock::STATUS_OUTBOUND_SCANNED,
									   Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
									   Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
								   ]
							   ])
							   ->asArray()
							   ->groupBy('product_barcode, box_barcode')
							   ->orderBy('box_barcode')
							   ->all();

		$toBusinessUnitTitle = $this->getStoreName($outboundOrder->to_point_id);
		$partyIdTitle = $outboundOrder->parent_order_number;
		$columnHeaders = [
			'ToBusinessUnit' => [
				'title' => 'To Business Unit',
				'column' => 'A',
			],
			'PartyId' => [
				'title' => 'Party Id',
				'column' => 'B',
			],
			'PickingId' => [
				'title' => 'Picking Id',
				'column' => 'C',
			],
			'Short' => [
				'title' => 'Short',
				'column' => 'D',
			],
			'StyleCode' => [
				'title' => 'Style Code',
				'column' => 'E',
			],
			'SKUBarcode' => [
				'title' => 'SKUBarcode',
				'column' => 'F',
			],
			'Description' => [
				'title' => 'Description',
				'column' => 'G',
			],
			'MerchGroup' => [
				'title' => 'Merch Group',
				'column' => 'H',
			],
			'Class' => [
				'title' => 'Class',
				'column' => 'I',
			],
			'Sizes' => [
				'title' => 'Sizes',
				'column' => 'J',
			],
			'Mensei' => [
				'title' => 'Mensei',
				'column' => 'K',
			],
			'NrOfLots' => [
				'title' => 'Nr Of Lots',
				'column' => 'L',
			],
			'QtyPerLot' => [
				'title' => 'Qty Per Lot',
				'column' => 'M',
			],
			'NrOfSnglUnit' => [
				'title' => 'Nr Of Sngl Unit',
				'column' => 'N',
			],
			'WaybillSerial' => [
				'title' => 'Waybill Serial',
				'column' => 'O',
			],
			'WaybillNumber' => [
				'title' => 'Waybill Number',
				'column' => 'P',
			],
			'LC' => [
				'title' => 'LC',
				'column' => 'Q',
			],
			'LC2' => [
				'title' => 'LC',
				'column' => 'R',
			],
			'Desi' => [
				'title' => 'Desi',
				'column' => 'S',
			],
			'BoxNo' => [
				'title' => 'Box No',
				'column' => 'T',
			],
			'BoxNetWeight' => [
				'title' => 'Box Net Weight',
				'column' => 'U',
			],
			'BoxGrossWeight' => [
				'title' => 'Box Gross Weight',
				'column' => 'V',
			],
			'BoxSize' => [
				'title' => 'Box Size',
				'column' => 'W',
			],
			'Volume' => [
				'title' => 'Volume',
				'column' => 'X',
			],
		];

		VarDumper::dump($outboundStocks, 10, true);
//        die();
		$objPHPExcel = new \PHPExcel();

		$objPHPExcel->getProperties()
					->setCreator("Report Reportov")
					->setLastModifiedBy("Report Reportov")
					->setTitle("Office 2007 XLSX Test Document")
					->setSubject("Office 2007 XLSX Test Document")
					->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
					->setKeywords("office 2007 openxml php")
					->setCategory("Report");

		$activeSheet = $objPHPExcel
			->setActiveSheetIndex(0)
			->setTitle('report-' . date('d.m.Y'));
		$i = 1;
		foreach ($columnHeaders as $columnHeader) {
			$activeSheet->setCellValue($columnHeader['column'] . $i, $columnHeader['title']); // +
		}

		foreach ($outboundStocks as $outboundStock) {
			$i++;
			$activeSheet->setCellValue('A' . $i, $toBusinessUnitTitle);
			$activeSheet->setCellValue('B' . $i, $partyIdTitle);
			$activeSheet->setCellValue('D' . $i, $partyIdTitle);
		}

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('export-rus-bel-report-test' . '.xlsx');
		die;
	}

	private function getStoreName($storeId)
	{
		return Store::findOne($storeId)->name;
	}

	private function getProductAttributesFromDeFactoAPI($lotBarcode)
	{
		$params = [];
		$params['request'] = [
			'BusinessUnitId' => '1029',
			'PageSize' => '0',
			'PageIndex' => '0',
			'CountAllItems' => false,
			'ProcessRequestedDataType' => 'Full',
		];

		$params['request']['LotOrSingleBarcode'] = $lotBarcode;

		$api = new DeFactoSoapAPIV2();
		$result = $api->sendRequest('GetMasterData', $params);

		if ($resultDataArray = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.Data.MasterDataThreePL')) {
			$resultDataArray = count($resultDataArray) <= 1 ? [$resultDataArray] : $resultDataArray;
		} else {
			$resultDataArray = [];
		}

		if (!empty($result['Data'])) {
			$resultDataArray = $result['Data'];
			$resultDataArray = count($resultDataArray) <= 1 ? [$resultDataArray] : $resultDataArray;
		} else {
			$resultDataArray = [];
		}

		return $resultDataArray;
	}

	private function addProductAttributesIfNoExist($lotBarcode)
	{
		foreach ($resultDataArray as $data) {
			//Products::create($data->SkuId,$data->LotOrSingleBarcode,$data->ShortCode,$data->Color);
			file_put_contents("GetMasterData-Size.csv", $data->SkuId . ';' . (isset($data->Size) ? $data->Size : '') . ";" . "\n", FILE_APPEND);
		}
	}

	public function actionRemoveDoubleReturns()
	{ // other/one/remove-double-returns
		die('other/one/remove-double-returns');
		/* $returnInbounds = InboundOrder::find()->andWhere([
             'client_id'=>Client::CLIENT_DEFACTO,
             'order_type'=>InboundOrder::ORDER_TYPE_RETURN,
         ])->andWhere('created_at > 1485536407')->all();

 //        $returnInboundIDs = InboundOrder::find()->select('id')->andWhere([
 //            'client_id'=>Client::CLIENT_DEFACTO,
 //            'order_type'=>InboundOrder::ORDER_TYPE_RETURN,
 //        ])->column();

         foreach($returnInbounds as $returnInbound) {
             $returnInboundItems = InboundOrderItem::find()->andWhere([
                 'inbound_order_id'=>$returnInbound->id,
             ])->all();

             foreach($returnInboundItems as $returnInboundItem) {
                 $returnStockDouble = Stock::find()->andWhere([
 //                    'inbound_order_id'=>$returnInboundIDs,
                     'product_barcode'=>$returnInboundItem->product_barcode,
                     'inbound_client_box'=>$returnInboundItem->box_barcode
                 ])->count();
 //
                 file_put_contents('actionRemoveDoubleReturns.log',$returnStockDouble.'; '.$returnInboundItem->product_barcode.'; '.$returnInboundItem->box_barcode.';'."\n",FILE_APPEND);
 //
 //                VarDumper::dump($returnStockDouble,10,true);

             }
         } */

		$returnOrders = ReturnOrder::find()->select('id')->andWhere([
			'client_id' => Client::CLIENT_DEFACTO,
			'party_number' => 'D10AA00005505',
		])->andWhere('created_at > 1485536407')
								   ->orderBy('id')
								   ->column();

		$returnOrderItems = ReturnOrderItems::find()
											->select('count(id) as qty, client_box_barcode, product_barcode, GROUP_CONCAT(id) as ids')
											->andWhere(['return_order_id' => $returnOrders])
											->groupBy('client_box_barcode')
											->orderBy('id')
											->asArray()
											->all();

		foreach ($returnOrderItems as $returnOrderItem) {
			if ($returnOrderItem['qty'] > 1) {
				$toDelete = explode(',', $returnOrderItem['ids']);
				array_pop($toDelete);

				file_put_contents('actionRemoveDoubleReturns.log',
					$returnOrderItem['product_barcode'] . '; '
					. $returnOrderItem['client_box_barcode'] . '; '
					. $returnOrderItem['qty'] . ';'
					. $returnOrderItem['ids'] . ';'
					. implode(',', $toDelete) . ';'
					. "\n", FILE_APPEND);

				ReturnOrderItemProduct::deleteAll(['return_order_item_id' => $toDelete]);
				ReturnOrderItems::deleteAll(['id' => $toDelete]);

				$inboundOrderItemIDs = InboundOrderItem::find()
													   ->select('id')
													   ->andWhere([
														   'product_barcode' => $returnOrderItem['product_barcode'],
														   'box_barcode' => $returnOrderItem['client_box_barcode'],
													   ])
													   ->orderBy('id')
													   ->column();

				array_pop($inboundOrderItemIDs);

				InboundOrderItem::deleteAll(['id' => $inboundOrderItemIDs]);
				Stock::deleteAll(['inbound_order_item_id' => $inboundOrderItemIDs]);
			}
		}
		return '-END-';
	}

	public function actionTestReturnOneBoxLot()
	{ // other/one/test-return-one-box-lot
		$productBarcode = '700000503735'; // лотокороб ок
//        $productBarcode = '700000503700'; // лотокороб ок
//        $productBarcode = '700000503734'; // лотокороб ок
//        $productBarcode = '700000520149'; // лотокороб ок
//        $productBarcode = '700000518465'; // возврат ок
//        $productBarcode = '700000517443'; // возврат ок
		if (BarcodeManager::isReturnBoxBarcode($productBarcode)) {
			$productBarcode = BarcodeManager::findProductInStockByReturnBarcodeBox($productBarcode);
			echo "Это возврат : " . $productBarcode . "<br />";
		}

		if (BarcodeManager::isOneBoxOneProduct($productBarcode)) {
			$productBarcode = BarcodeManager::findProductInStockByReturnBarcodeBox($productBarcode);
			echo "Это лотокороб : " . $productBarcode . "<br />";
		}


		return '-OK-';
	}


	public function actionAddAddressToDiffReport()
	{ // other/one/add-address-to-diff-report

//        die("- die add-address-to-diff-report -");

		$pathToCSVFile = 'tmp-file/defacto/DCstok27.10.2017-csv.csv';
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ";")) !== false) {
				$addressOnStockFree = Stock::find()->andWhere([
					'status_availability' => 2,
					'client_id' => Client::CLIENT_DEFACTO,
					'product_barcode' => trim($data[1]),
				])->one();
				$box = '';
				$address = '';
				$free = '';
				$isReturn = 'N';
				if ($addressOnStockFree) {
					$box = $addressOnStockFree->primary_address;
					$address = $addressOnStockFree->secondary_address;
					$free = 'Доступен';
					if (BarcodeManager::isReturnProductBarcode($addressOnStockFree->product_barcode)) {
						$isReturn = 'Y';

						$returnOrderItemProducts = ReturnOrderItemProduct::find()
																		 ->select('return_order_item_id, product_barcode, product_serialize_data, field_extra1, client_box_barcode, expected_qty')
																		 ->andWhere(['product_barcode' => $addressOnStockFree->product_barcode, 'accepted_qty' => 0])
//                            ->asArray()
																		 ->one();
//                        VarDumper::dump($returnOrderItemProducts,10,true);
						if ($returnOrderItemProducts) {
//                            if (!self::sendDataToAPI($returnOrderItemProducts)) {
//                                file_put_contents("makeInboundAndStockForAPI-diff-report-ERROR.log", date("Ymd") . "\n" . print_r($returnOrderItemProducts, true) . "\n", FILE_APPEND);
//                            }
						} else {

							$returnOrderItemProducts = ReturnOrderItemProduct::find()
																			 ->select('return_order_item_id, product_barcode, product_serialize_data, field_extra1, client_box_barcode, expected_qty')
																			 ->andWhere(['product_barcode' => $addressOnStockFree->product_barcode, 'accepted_qty' => 1])
//                                ->asArray()
																			 ->one();
							VarDumper::dump($returnOrderItemProducts, 10, true);


							if ($returnOrderItemProducts) {

//                                if (!self::sendDataToAPI($returnOrderItemProducts)) {
//                                    file_put_contents("makeInboundAndStockForAPI-diff-report-ERROR.log", date("Ymd") . "\n" . print_r($returnOrderItemProducts, true) . "\n", FILE_APPEND);
//                                }


								$csvRow = $data[0] . ';' . $data[1] . ';' . $data[2] . ';' . $data[3] . ';' . $data[4] . ';' . $box . ";" . $address . ";" . $free . ";" . $isReturn . ";";
								file_put_contents('address-to-diff-report-return-2' . '27-04' . '.csv', $csvRow . "\n", FILE_APPEND);
							}
						}

					}
					//$csvRow = $data[0].';'.$data[1].';'.$data[2].';'.$data[3].';'.$data[4].';'.$box.";".$address.";".$free.";".$isReturn.";";
					//file_put_contents('address-to-diff-report-'.'27-03'.'.csv',$csvRow."\n",FILE_APPEND);
				}

//                $addressOnStockLost = Stock::find()->andWhere([
//                    'client_id'=>Client::CLIENT_DEFACTO,
//                    'product_barcode'=>$data[1],
//                ])->one();
//                if($addressOnStockLost && empty($addressOnStockFree)) {
//                    $box = $addressOnStockLost->primary_address;
//                    $address = $addressOnStockLost->secondary_address;
//                    $free = 'Не доступен';
//                }

//                $csvRow = $data[0].';'.$data[1].';'.$data[2].';'.$data[3].';'.$data[4].';'.$box.";".$address.";".$free.";";
//                file_put_contents('address-to-diff-report-'.time().'.csv',$csvRow."\n",FILE_APPEND);
//                file_put_contents('address-to-diff-report-'.time().'.csv',$csvRow."\n",FILE_APPEND);

//                VarDumper::dump($data,10,true);

			}
		}

	}


	private static function sendDataToAPI($returnOrderItemProducts)
	{
		if (YII_ENV == 'prod') {
			$toSendDataForAPI = [];
			$returnOrderItemProductPrepared[] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataReturn($returnOrderItemProducts);
			$toSendDataForAPI['InBoundFeedBackThreePLResponse'] = $returnOrderItemProductPrepared;
			file_put_contents("SendInBoundFeedBackDataReturn-sendDataToAPI-live.log", date("Ymd") . "\n" . print_r($toSendDataForAPI, true) . "\n", FILE_APPEND);
			file_put_contents("SendInBoundFeedBackDataReturn-sendDataToAPI-serialize-live.log", serialize($toSendDataForAPI) . "\n", FILE_APPEND);
			$api = new DeFactoSoapAPIV2Manager();
			$responseAPI = $api->SendInBoundFeedBackDataReturn($toSendDataForAPI);
//            $responseAPI = [];
//            $responseAPI['HasError'] = false;
			if (!$responseAPI['HasError']) {
				return true;
			}
		}
		return false;
	}

	public function actionCarPartsInboundReAccepted()
	{
		// /other/one/car-parts-inbound-re-accepted
		// Hyundai Auto
		// удаляю неправильные накладные
		$clientIdHyundaiAuto = 95; //
		$inboundOrderAll = InboundOrder::findAll(['client_id' => $clientIdHyundaiAuto]);
		foreach ($inboundOrderAll as $inboundOrder) {
			$inboundOrder->accepted_qty = 0;
			$inboundOrder->save(false);
			InboundOrderItem::updateAll(['accepted_qty' => 0], ['inbound_order_id' => $inboundOrder->id]);
		}

		foreach ($inboundOrderAll as $inboundOrder) {
			$qtyAccepted = 0;
			$inboundOrderItemAll = InboundOrderItem::findAll(['inbound_order_id' => $inboundOrder->id]);
			foreach ($inboundOrderItemAll as $inboundOrderItem) {
				$countInStock = Stock::find()->andWhere([
					'client_id' => $clientIdHyundaiAuto,
					'product_barcode' => $inboundOrderItem->product_barcode,
					'inbound_order_id' => $inboundOrder->id,
				])
									 ->count();
				$inboundOrderItem->accepted_qty = $countInStock;
				$inboundOrderItem->save(false);
				$qtyAccepted += $countInStock;
			}
			$inboundOrder->accepted_qty = $qtyAccepted;
			$inboundOrder->save(false);
		}

		Stock::updateAll([
			'status' => Stock::STATUS_INBOUND_CONFIRM,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		],
			[
				'client_id' => $clientIdHyundaiAuto,
				'status' => Stock::STATUS_INBOUND_SCANNED,
				'status_availability' => Stock::STATUS_AVAILABILITY_NO,
			]);

		// Hyundai Truck
		// удаляю неправильные накладные
		$clientIdHyundaiTruck = 96; //
		$inboundOrderAll = InboundOrder::findAll(['client_id' => $clientIdHyundaiTruck]);
		foreach ($inboundOrderAll as $inboundOrder) {
			$inboundOrder->accepted_qty = 0;
			$inboundOrder->save(false);
			InboundOrderItem::updateAll(['accepted_qty' => 0], ['inbound_order_id' => $inboundOrder->id]);
		}

		foreach ($inboundOrderAll as $inboundOrder) {
			$qtyAccepted = 0;
			$inboundOrderItemAll = InboundOrderItem::findAll(['inbound_order_id' => $inboundOrder->id]);
			foreach ($inboundOrderItemAll as $inboundOrderItem) {
				$countInStock = Stock::find()->andWhere([
					'client_id' => $clientIdHyundaiTruck,
					'product_barcode' => $inboundOrderItem->product_barcode,
					'inbound_order_id' => $inboundOrder->id,
				])
									 ->count();
				$inboundOrderItem->accepted_qty = $countInStock;
				$inboundOrderItem->save(false);
				$qtyAccepted += $countInStock;
			}
			$inboundOrder->accepted_qty = $qtyAccepted;
			$inboundOrder->save(false);
		}

		Stock::updateAll([
			'status' => Stock::STATUS_INBOUND_CONFIRM,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		],
			[
				'client_id' => $clientIdHyundaiTruck,
				'status' => Stock::STATUS_INBOUND_SCANNED,
				'status_availability' => Stock::STATUS_AVAILABILITY_NO,
			]);

		// Subaru Auto
		// удаляю неправильные накладные
		$clientIdSubaruAuto = 97; //
		$inboundOrderAll = InboundOrder::findAll(['client_id' => $clientIdSubaruAuto]);
		foreach ($inboundOrderAll as $inboundOrder) {
			$inboundOrder->accepted_qty = 0;
			$inboundOrder->save(false);
			InboundOrderItem::updateAll(['accepted_qty' => 0], ['inbound_order_id' => $inboundOrder->id]);
		}

		foreach ($inboundOrderAll as $inboundOrder) {
			$qtyAccepted = 0;
			$inboundOrderItemAll = InboundOrderItem::findAll(['inbound_order_id' => $inboundOrder->id]);
			foreach ($inboundOrderItemAll as $inboundOrderItem) {
				$countInStock = Stock::find()->andWhere([
					'client_id' => $clientIdSubaruAuto,
					'product_barcode' => $inboundOrderItem->product_barcode,
					'inbound_order_id' => $inboundOrder->id,
				])
									 ->count();
				$inboundOrderItem->accepted_qty = $countInStock;
				$inboundOrderItem->save(false);
				$qtyAccepted += $countInStock;
			}
			$inboundOrder->accepted_qty = $qtyAccepted;
			$inboundOrder->save(false);
		}

		Stock::updateAll([
			'status' => Stock::STATUS_INBOUND_CONFIRM,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		],
			[
				'client_id' => $clientIdSubaruAuto,
				'status' => Stock::STATUS_INBOUND_SCANNED,
				'status_availability' => Stock::STATUS_AVAILABILITY_NO,
			]);

		return $this->render('index');
	}

	public function actionCarPartsInboundReAcceptedV2()
	{
		// /other/one/car-parts-inbound-re-accepted
		// Hyundai Auto
		// удаляю неправильные накладные
		$clientIdHyundaiAuto = 95; //
//        $inboundToDelete = [33347,33354,33362];
//        InboundOrder::deleteAll(['id'=>$inboundToDelete]);
//        InboundOrderItem::deleteAll(['inbound_order_id'=>$inboundToDelete]);
//        Stock::updateAll(['inbound_order_id'=>0,'inbound_order_item_id'=>0],['client_id'=>$clientIdHyundaiAuto]);

		$inboundOrderAll = InboundOrder::findAll(['client_id' => $clientIdHyundaiAuto]);
		foreach ($inboundOrderAll as $inboundOrder) {
			$inboundOrder->accepted_qty = 0;
			$inboundOrder->save(false);
			InboundOrderItem::updateAll(['accepted_qty' => 0], ['inbound_order_id' => $inboundOrder->id]);
		}

//        $inboundOrderAll = InboundOrder::findAll(['client_id'=>$clientIdHyundaiAuto]);
		foreach ($inboundOrderAll as $inboundOrder) {
			$qtyAccepted = 0;
			$inboundOrderItemAll = InboundOrderItem::findAll(['inbound_order_id' => $inboundOrder->id]);
			foreach ($inboundOrderItemAll as $inboundOrderItem) {
				$stockCount = Stock::find()->andWhere([
					'client_id' => $clientIdHyundaiAuto,
					'product_barcode' => $inboundOrderItem->product_barcode,
					'inbound_order_id' => $inboundOrder->id,
//                       'inbound_order_id'=>0,
//                       'inbound_order_item_id'=>0,
				])
//                       ->limit($inboundOrderItem->expected_qty)
								   ->count();

//                $qtyAcceptedItem = 0;
//                foreach($stockAll as $stock) {
//                    $stock->inbound_order_id = $inboundOrder->id;
//                    $stock->inbound_order_item_id = $inboundOrderItem->id;
//                    $stock->save(false);
//                    $qtyAcceptedItem += 1;
//                }

				$inboundOrderItem->accepted_qty = $stockCount;
				$inboundOrderItem->save(false);
				$qtyAccepted += $stockCount;
			}

			$inboundOrder->accepted_qty = $qtyAccepted;
			$inboundOrder->save(false);
		}

		Stock::updateAll([
			'status' => Stock::STATUS_INBOUND_CONFIRM,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		],
			[
				'client_id' => $clientIdHyundaiAuto,
				'status' => Stock::STATUS_INBOUND_SCANNED,
				'status_availability' => Stock::STATUS_AVAILABILITY_NO,
			]);

		// Hyundai Truck
		// удаляю неправильные накладные
		$clientIdHyundaiTruck = 96; //
		$inboundToDelete = [33363];
		InboundOrder::deleteAll(['id' => $inboundToDelete]);
		InboundOrderItem::deleteAll(['inbound_order_id' => $inboundToDelete]);
		Stock::updateAll(['inbound_order_id' => 0, 'inbound_order_item_id' => 0], ['client_id' => $clientIdHyundaiTruck]);

		$inboundOrderAll = InboundOrder::findAll(['client_id' => $clientIdHyundaiTruck]);
		foreach ($inboundOrderAll as $inboundOrder) {
			$inboundOrder->accepted_qty = 0;
			$inboundOrder->save(false);
			InboundOrderItem::updateAll(['accepted_qty' => 0], ['inbound_order_id' => $inboundOrder->id]);
		}

		$inboundOrderAll = InboundOrder::findAll(['client_id' => $clientIdHyundaiTruck]);
		foreach ($inboundOrderAll as $inboundOrder) {
			$qtyAccepted = 0;
			$inboundOrderItemAll = InboundOrderItem::findAll(['inbound_order_id' => $inboundOrder->id]);
			foreach ($inboundOrderItemAll as $inboundOrderItem) {
				$stockAll = Stock::find()->andWhere([
					'client_id' => $clientIdHyundaiTruck,
					'product_model' => $inboundOrderItem->product_model,
					'inbound_order_id' => 0,
					'inbound_order_item_id' => 0,
				])
								 ->limit($inboundOrderItem->expected_qty)
								 ->all();

				$qtyAcceptedItem = 0;
				foreach ($stockAll as $stock) {
					$stock->inbound_order_id = $inboundOrder->id;
					$stock->inbound_order_item_id = $inboundOrderItem->id;
					$stock->save(false);
					$qtyAcceptedItem += 1;
				}

				$inboundOrderItem->accepted_qty = $qtyAcceptedItem;
				$inboundOrderItem->save(false);
				$qtyAccepted += $qtyAcceptedItem;
			}
			$inboundOrder->accepted_qty = $qtyAccepted;
			$inboundOrder->save(false);
		}

		Stock::updateAll([
			'status' => Stock::STATUS_INBOUND_CONFIRM,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		],
			[
				'client_id' => $clientIdHyundaiTruck,
				'status' => Stock::STATUS_INBOUND_SCANNED,
				'status_availability' => Stock::STATUS_AVAILABILITY_NO,
			]);

		// Subaru Auto
		// удаляю неправильные накладные
		$clientIdSubaruAuto = 97; //
		Stock::updateAll(['inbound_order_id' => 0, 'inbound_order_item_id' => 0], ['client_id' => $clientIdSubaruAuto]);

		$inboundOrderAll = InboundOrder::findAll(['client_id' => $clientIdSubaruAuto]);
		foreach ($inboundOrderAll as $inboundOrder) {
			$inboundOrder->accepted_qty = 0;
			$inboundOrder->save(false);
			InboundOrderItem::updateAll(['accepted_qty' => 0], ['inbound_order_id' => $inboundOrder->id]);
		}

		$inboundOrderAll = InboundOrder::find()->andWhere(['client_id' => $clientIdSubaruAuto])->orderBy(['id' => SORT_ASC])->all();
		foreach ($inboundOrderAll as $inboundOrder) {
			$qtyAccepted = 0;
			$inboundOrderItemAll = InboundOrderItem::findAll(['inbound_order_id' => $inboundOrder->id]);
			foreach ($inboundOrderItemAll as $inboundOrderItem) {
				$stockAll = Stock::find()->andWhere([
					'client_id' => $clientIdSubaruAuto,
					'product_model' => $inboundOrderItem->product_model,
					'inbound_order_id' => 0,
					'inbound_order_item_id' => 0,
				])
								 ->limit($inboundOrderItem->expected_qty)
								 ->all();

				$qtyAcceptedItem = 0;
				foreach ($stockAll as $stock) {
					$stock->inbound_order_id = $inboundOrder->id;
					$stock->inbound_order_item_id = $inboundOrderItem->id;
					$stock->save(false);
					$qtyAcceptedItem += 1;
				}

				$inboundOrderItem->accepted_qty = $qtyAcceptedItem;
				$inboundOrderItem->save(false);
				$qtyAccepted += $qtyAcceptedItem;
			}
			$inboundOrder->accepted_qty = $qtyAccepted;
			$inboundOrder->save(false);
		}

		Stock::updateAll([
			'status' => Stock::STATUS_INBOUND_CONFIRM,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		],
			[
				'client_id' => $clientIdSubaruAuto,
				'status' => Stock::STATUS_INBOUND_SCANNED,
				'status_availability' => Stock::STATUS_AVAILABILITY_NO,
			]);

		return $this->render('index');
	}

	public function actionCarPartsInboundReportDiff()
	{
		// /other/one/car-parts-inbound-report-diff
		// Hyundai Auto
		$clientIdHyundaiAuto = 95; //
		$inboundOrderIDs = [33425];

		$inboundOrderAll = InboundOrder::findAll(['id' => $inboundOrderIDs]);
		foreach ($inboundOrderAll as $inboundOrder) {
			$qtyAccepted = 0;
			$inboundOrderItemAll = InboundOrderItem::findAll(['inbound_order_id' => $inboundOrder->id]);
			foreach ($inboundOrderItemAll as $inboundOrderItem) {
				$qtyOnStock = Stock::find()->andWhere([
					'client_id' => $clientIdHyundaiAuto,
					'product_model' => $inboundOrderItem->product_model,
					'inbound_order_id' => $inboundOrder->id,
				])
								   ->count();

				$inboundOrderItem->accepted_qty = $qtyOnStock;
				$inboundOrderItem->save(false);
				$qtyAccepted += $qtyOnStock;
			}
			$inboundOrder->accepted_qty = $qtyAccepted;
			$inboundOrder->save(false);
		}

		return $this->render('index');
	}

	//

	public function actionTestUploadExel()
	{ // other/one/test-upload-exel
//        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
//        $cacheSettings = array('memoryCacheSize ' => '2560 MB');
//        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$rootPath = 'tmp-file/car-parts/';
//        $excel = \PHPExcel_IOFactory::load($rootPath . 'SUB0000130522.11.2017.xls');
//        $excel =\PhpOffice\PhpSpreadsheet\IOFactory::load($rootPath . 'SUB0000133122.11.2017.xls');
//        $excel =\PhpOffice\PhpSpreadsheet\IOFactory::load($rootPath . '00000000591-20-2017.xls');
//        $excel =\PhpOffice\PhpSpreadsheet\IOFactory::load($rootPath . 'SUB00001337-22112017.xls');
//        $excel =\PhpOffice\PhpSpreadsheet\IOFactory::load($rootPath . 'act 55.xlsx');
//        $excel->setActiveSheetIndex(0);
//        $excelActive = $excel->getActiveSheet();
//
//        $order = new \stdClass();
//        $order->qtyRows = 0;
//        $order->qtyItems = 0;
//        $order->items = [];
//
//        $start = 2;
//        for ($i = $start; $i <= 1000; $i++)  {
//
//            $rowItem = (int)$excelActive->getCell('A' . $i)->getValue();
//            $productBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
//            $productName = (string)$excelActive->getCell('C' . $i)->getValue();
//            $productQty = (int)$excelActive->getCell('D' . $i)->getValue();
//
//            if($productBarcode == null || $productName == null || $productQty == null ) continue;
//
//            $row = new \stdClass();
//            $row->row = $rowItem;
//            $row->productBarcode = $productBarcode;
//            $row->productName = $productName;
//            $row->productQty = $productQty;
//
//            $order->qtyRows += 1;
//            $order->qtyItems += $row->productQty;
//
//            if (isset($order->items[$productBarcode])) {
//                $order->items[$productBarcode]->productQty += $row->productQty;
//            } else {
//                $order->items[$productBarcode] = $row;
//            }
//
//        }
		$order = SpreadsheetService::parseFile($rootPath . 'act 55.xlsx');
		echo "<pre>";
		print_r($order);
		die;
	}


	public function actionUpdateRockIds()
	{
		// other/one/update-rock-ids

//        $racks = RackAddress::find()->select('id, address')->andWhere(['warehouse_id'=>2,'address_unit3'=>1])->asArray()->all();
		$racks = RackAddress::find()->select('id, address')->andWhere(['warehouse_id' => 2])->asArray()->all();
//        $racks = RackAddress::find()->select('id, address')->asArray()->all();
		foreach ($racks as $rack) {
			Stock::updateAll(['address_sort_order' => $rack['id']], ['secondary_address' => $rack['address']]);
		}

//        $allList = Stock::find()
//                    ->andWhere('`address_sort_order` IS NULL AND `secondary_address` IS NOT NULL')
//                    ->andWhere(['client_id'=>Constants::getCarPartClientIDs()])
//                    ->all();
//        foreach($allList as $stockRow) {
//            $stockRow->address_sort_order = RackAddress::find()->select('id')->andWhere(['address'=>$stockRow->secondary_address])->scalar();
//            $stockRow->save(false);
//        }


//        $racks = RackAddress::find()->select('id, address')->andWhere(['warehouse_id'=>2])->asArray()->all();
//        foreach ($racks as $rack) {
//            Stock::updateAll(['address_sort_order' => $rack['id']], ['secondary_address' => $rack['address']]);
//        }


		return $this->render('index');
	}

	public function actionTestBox()
	{
		$barcode = 'IDEM CVT 20 л';
		$barcode = 'NLZ.20.48.020 NEW';
//        $barcode = '60X40X40/20';
//        $barcode = '60X40X40\20';
//        $barcode = '60X40X40';
//        $barcode = '10';
//        $barcode = '99';
//        $barcode = '31918,31919';
//        $barcode = '60X40X40/32,33';
//        $barcode = '60X40X40/32.33';
//        $barcode = '40x40x25/13,33';
//        $barcode = '40x40x25/13.33';
//        $barcode = '40x40x25/13/33';

//        echo BarcodeManager::getBoxM3($barcode);
		echo SpreadsheetService::preparedProductBarcode($barcode);

		return;
	}


	public function actionResendOneInboundOrder()
	{ // /other/one/resend-one-inbound-order
		die('/other/one/resend-one-inbound-order');
		$rows = [];
		$outResult = [];
//        $row[] = [
//            'InboundId' => '',//'48', // если плюсы то передаем null
//            'AppointmentBarcode' => 'D10AA00079314',//'D10AA00033871',
//            'LcOrCartonLabel' => '147008117740', //'2300000187083',
//            'LotOrSingleBarcode' => '2300004462957', //'2300000187083',
//            'LotOrSingleQuantity' => '2', //'1',
//        ];
		$row[] = [
			'InboundId' => '',//'48', // если плюсы то передаем null
			'AppointmentBarcode' => 'D10AA00079304',//'D10AA00033871',
			'LcOrCartonLabel' => '2430005548329', //'2300000187083',
			'LotOrSingleBarcode' => '2300005568238', //'2300000187083',
			'LotOrSingleQuantity' => '1', //'1',
		];

		$rows['InBoundFeedBackThreePLResponse'] = $row;
		$api = new DeFactoSoapAPIV2Manager();
		$outResult = $api->SendInBoundFeedBackData($rows);
		VarDumper::dump($rows, 10, true);
		echo "<br />";
		echo "<br />";
		VarDumper::dump($outResult, 10, true);
	}

	public function actionReturnQtyOnStock()
	{
		// /other/one/return-qty-on-stock
		$clientId = Client::CLIENT_DEFACTO;
		$stocks = Stock::find()->select('product_barcode')->andWhere([
			'client_id' => $clientId,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES
		])->groupBy('product_barcode');
		$items = [];
		foreach ($stocks->each(50) as $stock) {
			if (BarcodeManager::isReturnProductBarcode($stock->product_barcode)) {
				echo $stock->product_barcode . "<br />";
				$items[] = $stock->product_barcode;
				file_put_contents('actionReturnQtyOnStock-10-04-2018-01.log', $stock->product_barcode . "\n", FILE_APPEND);
			}
		}
		echo "<br />";
		echo count($items);
		echo "<br />";
	}


	public function actionResetInventory()
	{  // /other/one/reset-inventory
		die('actionResetInventory');
		$inventoryID = 4;
		$inventory = Inventory::find()->andWhere(['id' => $inventoryID])->one();
		$inventoryRows = InventoryRows::find()->andWhere(['inventory_id' => $inventory->id])->all();

		$stockItems = Stock::find()->andWhere([
			'inventory_id' => $inventory->id,
			'primary_address' => '0-inventory-0'
		])->all();

		foreach ($stockItems as $stock) {
			$stock->primary_address = $stock->inventory_primary_address;
			$stock->save(false);
		}

		Stock::updateAll([
			'inventory_id' => 0,
			'inventory_primary_address' => '',
			'inventory_secondary_address' => '',
			'status_inventory' => 0,
		], ['inventory_id' => $inventory->id]);

		$inventory->delete();

		foreach ($inventoryRows as $inventoryRow) {
			$inventoryRow->delete();
		}

		return 'YPA';
	}


	public function actionCreateDp()
	{
		// /other/one/create-dp
		die('- /other/one/create-dp -');
		$outboundOrderID = '20741'; // +
		$outboundOrderID = '20740'; // +
		$outboundOrderID = '20739'; // +
		$outboundOrderID = '20742'; // +
		$outboundOrderID = '20744'; // +
		$outboundOrderID = '20743'; // +
		$outboundOrderID = '20745'; // +
		$outboundOrderID = '20748'; // +
		$outboundOrderID = '20746'; // +
		$outboundOrderID = '20738'; // +
		$outboundOrderID = '20747'; // +
		$outboundOrder = OutboundOrder::find()->andWhere(['id' => $outboundOrderID, 'client_id' => Client::CLIENT_DEFACTO])->one();
		$dp = new TlDeliveryProposal();
		$dpOrder = new TlDeliveryProposalOrders();

		$dp->status = TlDeliveryProposal::STATUS_NEW;
		$dp->client_id = $outboundOrder->client_id;
		$dp->route_from = $outboundOrder->from_point_id;
		$dp->route_to = $outboundOrder->to_point_id;
		$dp->cash_no = TlDeliveryProposal::METHOD_CHAR;
		$dp->save(false);

		$dpOrder->client_id = $dp->client_id;
		$dpOrder->tl_delivery_proposal_id = $dp->id;
		$dpOrder->order_id = $outboundOrder->id;
		$dpOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
		$dpOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
		$dpOrder->order_number = $outboundOrder->parent_order_number . ' ' . $outboundOrder->order_number;
		$dpOrder->kg = $outboundOrder->kg;
		$dpOrder->kg_actual = $outboundOrder->kg;
		$dpOrder->mc = $outboundOrder->mc;
		$dpOrder->mc_actual = $outboundOrder->mc;
		$dpOrder->number_places = $outboundOrder->accepted_number_places_qty;
		$dpOrder->number_places_actual = $outboundOrder->accepted_number_places_qty;
		$dpOrder->title = $outboundOrder->title;
		$dpOrder->description = $outboundOrder->description;
		$dpOrder->save(false);

		$dpManager = new DeliveryProposalManager(['id' => $dp->id]);
		$dpManager->onCreateProposal();
		return $outboundOrderID;
	}


	public function actionFind2EqualOrder()
	{
		// /other/one/find-2-equal-order
		$clients = [95, 96, 97];
		$orders = [];
		foreach ($clients as $clientID) {
			$all = OutboundOrder::find()->andWhere(['client_id' => $clientID])->all();
			foreach ($all as $order) {
				$currentOrder = trim($order->order_number);
				if (!isset($orders[$currentOrder])) {
					$orders[$currentOrder] = 1;
				} else {
					$orders[$currentOrder] += 1;
				}
			}
			foreach ($orders as $order => $value) {
				if ($value > 1) {
					echo $order . "<br />";
				}
			}
			$orders = [];
		}


//        VarDumper::dump($orders,10,true);

	}

	function actionTestDateTime()
	{
		// other/one/test-date-time
		$currentDateTime = DateHelper::getTimestamp();
//            VarDumper::dump($currentDateTime,10,true);
//            echo "<br />";

		$old = new \DateTime();
		$old->setTimestamp(null);
		$o = (int)$old->format("Ymd");

		$current = new \DateTime();
		$current->setTimestamp(1525944787);
//            $current->setTimestamp($currentDateTime);
		$current->modify("+1 hours");
		$c = (int)$current->format("Ymd");

//
		echo "<br />";
		VarDumper::dump($o, 10, true);
		echo "<br />";
		VarDumper::dump($c, 10, true);


		echo "Old: <br />";
		VarDumper::dump($old, 10, true);

		echo "Current: <br />";
		VarDumper::dump($current, 10, true);
		$diff = $old->diff($current);

		echo "Diff: <br />";
		VarDumper::dump($diff, 10, true);

//            if($old == $current) {
//                echo "==";
//            }
//            if($old < $current) {
//                echo "<";
//            }
//            if($old > $current) {
//                echo ">";
//            }


		die;
	}

	function canChangeShippedDatetime($aOldDateTime, $aCurrentDateTime)
	{

		$old = new \DateTime();
		$old->setTimestamp($aOldDateTime);
		$oldYmd = (int)$old->format("Ymd");

		$current = new \DateTime();
		$current->setTimestamp($aCurrentDateTime);
		$currentYmd = (int)$current->format("Ymd");

		return $oldYmd == $currentYmd;
	}

	public function actionTestNewSendOutbound()
	{
		// other/one/test-new-send-outbound

		die("dddd");
		$outboundOrderModel = OutboundOrder::findOne(22709);

		$outboundOrderItems = OutboundOrderItem::find()
											   ->andWhere(['outbound_order_id' => $outboundOrderModel->id])
											   ->andWhere('accepted_qty > 0')
											   ->all();

//        $countBoxBarcodes = Stock::find()
//            ->andWhere(['outbound_order_id' => $outboundOrderModel->id]) // ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
//            ->andWhere(['status'=>[
//                Stock::STATUS_OUTBOUND_SCANNED,
//                Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
//                Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
//            ]
//            ])
//            ->groupBy('box_barcode')
//            ->count();

//        $apiManager = new DeFactoSoapAPIV2Manager();
//        $result = $apiManager->CreateLcBarcode($countBoxBarcodes);
//        if($result['HasError']) {
//            echo $result['ErrorMessage'];
//            file_put_contents("CreateLcBarcode-Error.log",print_r($result,true)."\n",FILE_APPEND);
//            die();
////            return 0;
//        }
//
//        $createLcBarcodes = $result['Data'];

		$mappingOurBobBarcodeToDefacto = [];
		$mappingWaybillNumber = [];
		$boxCountStep = 1;

		foreach ($outboundOrderItems as $outboundOrderItem) {

			$stocks = Stock::find()->select('product_barcode, inbound_order_id, count(id) as accepted_qty, box_barcode, box_size_m3, box_size_barcode, outbound_order_item_id')
						   ->andWhere(['outbound_order_item_id' => $outboundOrderItem->id])
						   ->andWhere(['status' => [
							   Stock::STATUS_OUTBOUND_SCANNED,
							   Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
							   Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
						   ]
						   ])
						   ->groupBy('product_barcode, box_barcode, outbound_order_item_id')
						   ->orderBy('box_barcode')
						   ->asArray()
						   ->all();

			if ($tmp = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks, $outboundOrderItem)) {

//                VarDumper::dump($tmp,10,true);
//                echo "<br />";
//                echo "<br />";
//                die;

				foreach ($tmp as $tmpAPIValue) {
					if (!isset($mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']])) {
						$mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']] = (new OutboundBoxService)->getClientBoxByBarcode($tmpAPIValue['LcBarcode']); //array_shift($createLcBarcodes);
						$mappingWaybillNumber[$tmpAPIValue['LcBarcode']] = DeFactoSoapAPIV2Manager::makeWaybillNumber($outboundOrderModel, $boxCountStep);
						$boxCountStep++;
						file_put_contents("mappingOutboundBarcodeToDefacto.csv",
							$outboundOrderModel->id . ";"
							. $tmpAPIValue['LcBarcode'] . ";"
							. $mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']] . ";"
							. $mappingWaybillNumber[$tmpAPIValue['LcBarcode']] . ";"
							. "\n", FILE_APPEND);

//                        $stockIdsOutboundBoxes = Stock::find()
//                            ->select('id')
//                            ->andWhere([
//                                'outbound_order_id'=>$outboundOrderModel->id,
//                                'box_barcode'=>$tmpAPIValue['LcBarcode'],
//                            ])
//                            ->andWhere(['status'=>[
//                                Stock::STATUS_OUTBOUND_SCANNED,
//                                Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
//                                Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
//                            ]])
//                            ->column();

						OutboundBoxService::addLcAndWaybillByBoxBarcode($tmpAPIValue['LcBarcode'], $mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']], $mappingWaybillNumber[$tmpAPIValue['LcBarcode']]);
//
//                        if($stockIdsOutboundBoxes) {
//                            StockExtraField::saveBoxDefacto($stockIdsOutboundBoxes,[
//                                StockExtraField::OUTBOUND_LC_BARCODE_FIELD_NAME_DEFACTO=>$mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']],
//                                StockExtraField::OUTBOUND_WAYBILL_NUMBER_FIELD_NAME_DEFACTO=>$mappingWaybillNumber[$tmpAPIValue['LcBarcode']],
//                            ]);
//                        }
					}
				}
			}

//            VarDumper::dump($stocks,10,true);
//            die;

			foreach ($stocks as $keyStock => $stock) {
				$stocks[$keyStock]['box_barcode'] = $mappingOurBobBarcodeToDefacto[$stock['box_barcode']];
				$stocks[$keyStock]['our_box_barcode'] = $stock['box_barcode'];
				$stocks[$keyStock]['waybill_number'] = $mappingWaybillNumber[$stock['box_barcode']];
			}
//
			$outboundPreparedDataRows = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks, $outboundOrderItem);
			if ($outboundPreparedDataRows) {
				foreach ($outboundPreparedDataRows as $outboundPreparedDataRow) {
					unset($outboundPreparedDataRow['our_box_barcode']);
					$rowsDataForAPI['OutBoundFeedBackThreePLResponse'][] = $outboundPreparedDataRow;
				}
			}

		}

		VarDumper::dump($rowsDataForAPI, 10, true);
		die;

	}

	/* Скрипт генерирует адреса для полок из
    * заданых диапазонов
    **/
	public function actionGenerateRackAddressTmp()
	{ // other/one/generate-rack-address-tmp
		echo 'start other/one/generate-rack-address-tmp end' . "\n";
		//die('-other/one/generate-rack-address-tmp-');
		// 5-1-01-0 до 5-1-30-0
		// 5-1-01-1 до 5-1-30-1
		// 5-1-01-2 до 5-1-30-2

		//
		$lvlMin = 100;//10; //8; //7 //Этаж минимальное значение
		$lvlMax = 110; //10; //8; //7 //Этаж максимальное значение

		$rowMin = 1; //1 //Ряд минимальное значение
		$rowMax = 1; //1 //Ряд максимальное значение

		$rackInRowMin = 1; //1 //Полка в ряду минимальное значение
		$rackInRowMax = 20; //17;//15; //41;  //Полка в ряду максимальное значение

		$upperMin = null; //Полка в ряду минимальное значение
		$upperMax = null; //Полка в ряду максимальное значение
		$warehouseId = 405;

		// С 5-1-23-0 по 5-1-23-2
		// С 5-1-32-0 по 5-1-32-2
		// с 7-1-01-1 по 7-1-30-1

		for ($i1 = $lvlMin; $i1 <= $lvlMax; $i1++) {
			if ($a = RackAddress::createAddress($i1, $rowMin, $rackInRowMin, $upperMin, $warehouseId)) {
				echo 'address ' . $a . ' was generated' . "\n";
			} else {
				echo 'address was not created' . "\n";
			}

			for ($i2 = $rowMin; $i2 <= $rowMax; $i2++) {
				if ($a = RackAddress::createAddress($i1, $i2, $rackInRowMin, $upperMin, $warehouseId)) {
					echo 'address ' . $a . ' was generated' . "\n";
				} else {
					echo 'address was not created' . "\n";
				}

				for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {

					if ($a = RackAddress::createAddress($i1, $i2, $i3, $upperMin, $warehouseId)) {
						echo 'address ' . $a . ' was generated' . "\n";
					} else {
						echo 'address was not created' . "\n";
					}
//					for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
//						if ($a = RackAddress::createAddress($i1, $i2, $i3, $i4, $warehouseId)) {
//							echo 'address ' . $a . ' was generated' . "\n";
//						} else {
//							echo 'address was not created' . "\n";
//						}
//					}
				}
			}
		}

//		$i = 0;
//        foreach(Stock::find()->andWhere(['client_id'=>2])->each(100) as $stock) {
//            if($address = RackAddress::find()->where(['address'=>$stock->secondary_address])->one()) {
//                $stock->address_sort_order = $address->sort_order;
//                $stock->save(false);
//                echo $i++." ".$stock->secondary_address.' '.$address->sort_order."\n";
//            }
//        }

		echo ' end other/generate-rack-address end' . "\n";
		return 0;
	}


	public function actionCheckWarehouseDop()
	{
		// other/one/check-warehouse-dop

		echo "other/one/check-warehouse-dop<br />";
		die("--DIE--");
		$levels = [1, 2, 3];
		//$levels = [4,7,8,9,10];


		foreach ($levels as $level) {

			foreach (Stock::find()->andWhere(['client_id' => 2, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])->andWhere('secondary_address LIKE "' . $level . '-%"')->each(100) as $stock) {
				if (BarcodeManager::isReturnBoxBarcode($stock->primary_address)) {
					echo "--------------return product exist" . $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . ';' . "<br />";
					file_put_contents('IsReturn-1.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "\n", FILE_APPEND);
				} else {
					echo "OK = > " . $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "<br />";
					// file_put_contents('IsNOReturn-1.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode ."\n", FILE_APPEND);
				}
			}
		}


		return '';
	}

	public function actionResetRackAddress()
	{
		// /other/one/reset-rack-address

		echo "other/one/reset-rack-address<br />";

		foreach (Stock::find()->andWhere(['client_id' => 2, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])->each(100) as $stock) {
			if ($address = RackAddress::find()->where(['address' => $stock->secondary_address])->one()) {
				//$stock->address_sort_order = (int)$address->address_unit1 + (int)$address->address_unit2 + (int)$address->address_unit3 + (int)$address->address_unit4;
				$sort1 = (int)$address->address_unit1 * 1000;
				$sort2 = (int)$address->address_unit2;
				$sort3 = (int)$address->address_unit3;
				$sort4 = (int)$address->address_unit14;

				$stock->address_sort_order = $sort1 + $sort2 + $sort3 + $sort4;

				//				$stock->address_sort_order = $address->sort_order;
				$stock->save(false);
				file_put_contents('actionResetRackAddress-OK.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "\n", FILE_APPEND);
			} else {
				file_put_contents('actionResetRackAddress-NO.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "\n", FILE_APPEND);
			}
		}

//        foreach (Stock::find()->andWhere(['client_id' => 2, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])->each(100) as $stock) {
//            if ($address = RackAddress::find()->where(['address' => $stock->secondary_address])->one()) {
//                $stock->address_sort_order = $address->sort_order;
//                $stock->save(false);
//                file_put_contents('actionResetRackAddress-OK.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "\n", FILE_APPEND);
//            } else {
//                file_put_contents('actionResetRackAddress-NO.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "\n", FILE_APPEND);
//            }
//        }


		return 'OK-DONE';
	}

	public function actionReloadDefatoEmployees()
	{
		// other/one/reload-defato-employees
		die('-START DIE END-');
		$excel = \PHPExcel_IOFactory::load('tmp-file/defacto/employees/DC-NOMADEX-CONTACT-INFO.XLSX');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$employees = [];
		for ($i = 2; $i < 33; $i++) {
			$storeName = $excelActive->getCell('A' . $i)->getValue();
			$employee = $excelActive->getCell('B' . $i)->getValue();
			$employeeContact = $excelActive->getCell('C' . $i)->getValue();
			$email = $excelActive->getCell('D' . $i)->getValue();
			$storeId = $excelActive->getCell('E' . $i)->getValue();

			$employees[] = [
				'storeName' => $storeName,
				'employee' => $employee,
				'employeeContact' => $this->separateContact($employeeContact),
				'email' => $email,
				'storeId' => $storeId,
			];
		}

//        $clientEmployee = ClientEmployees::find()->where(['id'=>160])->one();
//        $clientEmployee->manager_type = ClientEmployees::TYPE_DIRECTOR;
//        $clientEmployee->save(false);
//
//        $clientEmployee = ClientEmployees::find()->where(['id'=>124])->one();
//        $clientEmployee->manager_type = ClientEmployees::TYPE_DIRECTOR;
//        $clientEmployee->save(false);
//
//        foreach($employees as $employee) {
//
//            $clientEmployeesOther = ClientEmployees::find()->where([
//                'client_id'=>Client::CLIENT_DEFACTO,
//                'store_id'=>$employee['storeId'],
//            ])->all();
//
//            Yii::$app->db->transaction(function() use ($clientEmployeesOther) {
//
//
//                if(!empty($clientEmployeesOther) && is_array($clientEmployeesOther)) {
//                    foreach($clientEmployeesOther as $clientEmployee) {
//
//                        $user = User::find()->where(['id'=>$clientEmployee->user_id])->one();
//                        if($user) {
//                            $user->email = Yii::$app->security->generateRandomString(8).'@old-demo-mail.kz';
//                            $user->save(false);
//                        }
//                    }
//                }
//            });
//        }

		$dataForEmail = [];

		foreach ($employees as $employee) {

			$director = ClientEmployees::find()->where([
				'client_id' => Client::CLIENT_DEFACTO,
				'store_id' => $employee['storeId'],
				'manager_type' => ClientEmployees::TYPE_DIRECTOR,
			])->andWhere('user_id != 0')->one();

//            if(!$director) {
//                file_put_contents('separateContact.log',$employee['storeId']."\n",FILE_APPEND);
//                continue;
//            }

//            $director->full_name = '';
//            $director->first_name = '';
//            $director->middle_name = '';
//            $director->last_name = '';
//            $director->phone = $this->preparePhone($employee['employeeContact'],'phone');
//            $director->phone_mobile = $this->preparePhone($employee['employeeContact'],'phone2');
//            $director->email = $employee['email'];
//            $director->username = $this->formatStoreNameToUserName($employee['storeName']);
//            $director->deleted = 0;
//            $director->status = ClientEmployees::STATUS_ACTIVE;

			$store = Store::find()->andWhere(['id' => $director->store_id])->one();
//            $store->name = 'Defacto';
//            $store->legal_point_name = 'Defacto Retail Store KZ';
//            $store->contact_first_name = '';
//            $store->contact_middle_name = '';
//            $store->contact_last_name = '';
//            $store->email = $employee['email'];
//            $store->phone = '-';
//            $store->phone_mobile = '-';

			//file_put_contents('separateContact-user.log',$director->user_id.' - '.$director->id."\n",FILE_APPEND);
			$newPass = "D" . $store->shop_code3;
//            $user = User::find()->andWhere(['id'=>$director->user_id])->one();
//            $user->username = $director->username;
//            $user->email = $employee['email'];
//            $user->password_hash = Password::hash($newPass);
//            $user->blocked_at = '';

//            $clientEmployeesOther = ClientEmployees::find()->andWhere([
//                'client_id'=>Client::CLIENT_DEFACTO,
//                'store_id'=>$employee['storeId'],
//            ])
//            ->andWhere('id != :id',[':id'=>$director->id])
//            ->all();

//            file_put_contents('separateContact-NEW-EMPLOYEE.log',$employee['email'].';'.$director->username.';'.$newPass.';'."\n",FILE_APPEND);
//            file_put_contents('separateContact-NEW-EMPLOYEE.xlsx',$employee['email'].';'.$director->username.';'.$newPass.';'."\n",FILE_APPEND);

			$dataForEmail[] = [
				'email' => $employee['email'],
				'login' => $director->username,
				'password' => $newPass,
			];


//            Yii::$app->db->transaction(function() use ($director,$user,$store,$clientEmployeesOther) {
//
//                $director->save(false);
//                $user->save(false);
//                $store->save(false);
//
//                file_put_contents('separateContact-STORE.log',$store->id."\n",FILE_APPEND);
//
//                if(!empty($clientEmployeesOther) && is_array($clientEmployeesOther)) {
//                    foreach($clientEmployeesOther as $clientEmployee) {
//                        $clientEmployee->deleted = '1';
//                        $clientEmployee->status = ClientEmployees::STATUS_DELETED;
//                        $clientEmployee->save(false);
//
//                        file_put_contents('separateContact-clientEmployeesOther.log',$clientEmployee->user_id.' - '.$clientEmployee->id."\n",FILE_APPEND);
//
//                        $user = User::find()->andWhere(['id'=>$clientEmployee->user_id])->one();
//                        if($user) {
//                            $user->email = Yii::$app->security->generateRandomString(8).'@old-demo-mail.kz';
//                            $user->blocked_at = time();
//                            $user->save(false);
//                        }
//                    }
//                }
//            });
		}

		foreach ($dataForEmail as $newContact) {

			$validator = new EmailValidator();
			$email = trim($newContact['email']);

			if ($validator->validate($email)) {
//                $mailManager = new MailManager();
//                $mailManager->sendOneMail($email,$newContact);
			} else {
				file_put_contents('separateContact-BAD-EMAIL.log', $email . "\n", FILE_APPEND);
			}

		}

		return $this->render('index');
	}

	private function preparePhone($employeeContact, $type)
	{

		if (isset($employeeContact['0']) && $type == 'phone') {
			return $employeeContact['0']['phone'];
		}

		if (isset($employeeContact['1']) && $type == 'phone2') {
			return $employeeContact['1']['phone'];
		}
		return '';
	}

	private function separateContact($contact)
	{
		$namePhoneList = explode(";", trim($contact));
		$result = [];
		foreach ($namePhoneList as $namePhone) {
			list($name, $phone) = explode("-", trim($namePhone));
			$result [] = [
				'name' => trim($name),
				'phone' => trim($phone),
			];

		}
		return $result;
	}

	private function formatStoreNameToUserName($storeName)
	{
		return strtolower(str_replace(' ', "-", trim($storeName)));
	}


	public function actionTestReOpenExcel()
	{
		// other/one/test-re-open-excel

		$orderNumber = 'ZXASQW'; // <номер поступления>
		$createdAt = '2012-12-15'; // <дата поступления заказа в базу>

		$header6 = 'Принят и осмотрен груз, прибывший по документу  №__________________??__________________  «_____» ________________ 20____ года';
		$header7 = '№   Приходной накладной №:_______??________ «_____» ________________ 20____ года';
		$header8 = 'Отправитель/Поставщик: ____________??_____________________';
		$header11 = 'Состояние тары и упаковки в момент осмотра продукции, количество мест: Без повреждений___?Руками?______мест';


		$excel = \PHPExcel_IOFactory::load('tmp-file/defacto/re-open-excel/x.xls');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();


		// Header
		$excelActive->setCellValue('I3', $orderNumber);
		$excelActive->setCellValue('J3', $createdAt);

		// Rows
		$rowStart = 16;
		for ($i = $rowStart; $i <= $rowStart + 10; $i++) {

			$excelActive->insertNewRowBefore($i, 1);
			$excelActive->mergeCells("C" . $i . ":E" . $i);
			$excelActive->setCellValue('A' . $i, $i);
			$excelActive->setCellValue('B' . $i, "B" . $i);
			$excelActive->setCellValue('C' . $i, "C" . $i);
			$excelActive->setCellValue('F' . $i, "F" . $i);
			$excelActive->setCellValue('G' . $i, "G" . $i);
			$excelActive->setCellValue('H' . $i, "H" . $i);
			$excelActive->setCellValue('I' . $i, "I" . $i);
			$excelActive->setCellValue('J' . $i, "J" . $i);

		}


		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="outbound-orders-report-' . time() . '.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->save('php://output');
		Yii::$app->end();


//        $objWriter = new \PHPExcel_Writer_Excel5($excel);
//        return $objWriter->save('php://output');
//        $excel->save('php://output');
	}


	public function actionCompareInbound()
	{
		$excel = \PHPExcel_IOFactory::load('tmp-file/inbound/inbound10092018.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		// Rows
		$rowStart = 1;
		for ($i = $rowStart; $i <= 1000; $i++) {
			$skuId = $excelActive->getCell('A' . $i)->getValue();
			$inboundQuantity = $excelActive->getCell('C' . $i)->getValue();
			$feedbackQuantity = $excelActive->getCell('D' . $i)->getValue();
			echo $skuId . "<br />";
		}
	}

	public function actionMergeFiles()
	{
		// other/one/merge-files
		die('other/one/merge-files');
		$excel = \PHPExcel_IOFactory::load('tmp-file/defacto/merge/defacto.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActiveDefacto = $excel->getActiveSheet();

		$excel = \PHPExcel_IOFactory::load('tmp-file/defacto/merge/our-box.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActiveOur = $excel->getActiveSheet();


		$ourBarcodeMap = [];
		$rowStart = 2;
		for ($i = $rowStart; $i <= 2000; $i++) {
			$boxBarcode = $excelActiveOur->getCell('A' . $i)->getValue();
			$lotBarcode = $excelActiveOur->getCell('B' . $i)->getValue();
			$boxBarcode = trim($boxBarcode);
			$lotBarcode = trim($lotBarcode);
			if (!empty($boxBarcode) && !empty($lotBarcode)) {
				$ourBarcodeMap[$lotBarcode] = $boxBarcode;
			}

		}

		// Rows
		$rowStart = 2;
		$mapOurBox2Lot = [];
		$mapOurBox2LotNoExist = [];
		for ($i = $rowStart; $i <= 1000; $i++) {
			$lotBarcode = $excelActiveDefacto->getCell('A' . $i)->getValue();
			$lotBarcode = trim($lotBarcode);

			if (!empty($lotBarcode)) {
				if (array_key_exists($lotBarcode, $ourBarcodeMap)) {
					$mapOurBox2Lot[$ourBarcodeMap[$lotBarcode]] = $lotBarcode;
				} else {
					$mapOurBox2LotNoExist[] = $lotBarcode;
				}
			}
		}


		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->getProperties()
					->setCreator("Report Reportov")
					->setLastModifiedBy("Report Reportov")
					->setTitle("Office 2007 XLSX Test Document")
					->setSubject("Office 2007 XLSX Test Document")
					->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
					->setKeywords("office 2007 openxml php")
					->setCategory("Report");

		$activeSheet = $objPHPExcel
			->setActiveSheetIndex(0)
			->setTitle('report-' . date('d.m.Y'));

		$i = 1;
		foreach ($mapOurBox2Lot as $boxBarcode => $lotBarcode) {
			$activeSheet->setCellValue('A' . $i, $lotBarcode); // +
			$activeSheet->setCellValue('B' . $i, $boxBarcode); // +
			$i++;
		}
		foreach ($mapOurBox2LotNoExist as $lotBarcode) {
			$activeSheet->setCellValue('A' . $i, $lotBarcode); // +
			$i++;
		}


		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="order489977-' . time() . '.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		Yii::$app->end();


		VarDumper::dump($mapOurBox2Lot, 10, true);
		echo "<br />";
		echo count($mapOurBox2Lot);
		echo "<br />";
		echo "<br />";
		VarDumper::dump($mapOurBox2LotNoExist, 10, true);
		echo "<br />";
		echo count($mapOurBox2LotNoExist);

		die('Y');
	}

	public function actionQtyReturnInWarehouse()
	{
		// other/one/qty-return-in-warehouse

		echo "other/one/qty-return-in-warehouse<br />";
//        die("--DIE--");
//        $levels = [1,2,3];
		//$levels = [4,7,8,9,10];

		$rackId = RackAddress::find()->select('sort_order')->andWhere(['warehouse_id' => [0, 3]]);
		$i = 1;
		foreach (Stock::find()->andWhere(['client_id' => 2, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])->andWhere(['address_sort_order' => $rackId])->each(100) as $stock) {
			echo $i++ . "<br />";
			if (BarcodeManager::isReturnBoxBarcode($stock->primary_address)) {
				echo $i++ . "--------------return product exist" . $stock->inbound_client_box . ';' . $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . ';' . "<br />";
				file_put_contents('IsReturnReport01-10-2019.xlsx', $stock->inbound_client_box . ';' . $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "\n", FILE_APPEND);
			} else {
				echo "OK = > " . $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "<br />";
//                 file_put_contents('IsNOReturn-1.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode ."\n", FILE_APPEND);
			}
		}


		return '';
	}


	public function actionInboundDiffOrder()
	{
		// other/one/inbound-diff-order
		/*
         *  Это скрипт помогает найти расхождения и плюсы в приходных накладных.
         *  Сравнивая наши данные и данные дефакто
         * */

		$ourItems = InboundOrderItem::find()->andWhere(['inbound_order_id' => 51192])->all();


		$excel = \PHPExcel_IOFactory::load('tmp-file/defacto/inbound/D10AA00081944.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$rowStart = 2;
		$fileItems = [];
		for ($i = $rowStart; $i <= 2000; $i++) {
			$boxBarcode = $excelActive->getCell('I' . $i)->getValue();
			$boxBarcode = trim($boxBarcode);

			$expendQty = $excelActive->getCell('M' . $i)->getValue();
			$realQty = $excelActive->getCell('N' . $i)->getValue();

			if (empty($boxBarcode) && is_null($expendQty)) {
				continue;
			}

			$fileItems[] = [
				'boxBarcode' => $boxBarcode,
				'expectedQty' => $expendQty,
				'acceptedQty' => $realQty
			];
		}

		$plus = [];
		$fileExpectedSum = 0;
		$fileAcceptedSum = 0;

		foreach ($fileItems as $fileItem) {
			$fileExpectedSum += $fileItem['expectedQty'];
			$fileAcceptedSum += $fileItem['acceptedQty'];
		}

		$ourDbExpectedSum = 0;
		$ourDbAcceptedSum = 0;
		foreach ($ourItems as $fileKey => $ourItem) {

			$ourDbExpectedSum += $ourItem->expected_qty;
			$ourDbAcceptedSum += $ourItem->accepted_qty;

			$isExist = false;
			foreach ($fileItems as $fileItem) {
				if ($fileItem['boxBarcode'] == $ourItem->box_barcode &&
					$fileItem['expectedQty'] == $ourItem->expected_qty &&
					$fileItem['acceptedQty'] == $ourItem->accepted_qty
				) {
					$isExist = true;
				}
			}

			if (!$isExist) {
				$plus[] = [
					'productBarcode' => $ourItem->product_barcode,
					'boxBarcode' => $ourItem->box_barcode,
					'expectedQty' => $ourItem->expected_qty,
					'acceptedQty' => $ourItem->accepted_qty
				];
			}
		}

		echo "fileExpectedSum : " . $fileExpectedSum . "<br />";
		echo "fileAcceptedSum : " . $fileAcceptedSum . "<br />";
		echo " <br />";
		echo "ourDbExpectedSum : " . $ourDbExpectedSum . "<br />";
		echo "ourDbAcceptedSum : " . $ourDbAcceptedSum . "<br />";
		VarDumper::dump($plus, 10, true);
		die;
	}

	public function actionDefactoCrossDockWithProduct()
	{
		// /other/one/defacto-cross-dock-with-product

		$crossDockItems = CrossDockItems::find()->andWhere(['cross_dock_id' => 6688])->all();
		foreach ($crossDockItems as $crossDockItem) {
			$row = $crossDockItem->field_extra3 . ';' .
				$crossDockItem->box_barcode . ';' .
				$crossDockItem->field_extra2 . ';'
				. "\n";
			file_put_contents('DefactoCrossDockWithProduct1.csv', $row, FILE_APPEND);
		}
		return 'Y';
	}

	public function actionQtyBoxDefacto()
	{
		// other/one/qty-box-defacto

		$clientId = Client::CLIENT_DEFACTO;

		$query = Stock::find()->select('primary_address');
		$query->andWhere([
			'client_id' => $clientId,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
		]);
		$query->groupBy('primary_address');

		echo "<br />";
		echo $query->count();
		echo "<br />";

		$stocks = Stock::find()->select('product_barcode')->andWhere([
			'client_id' => $clientId,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES
		])->groupBy('product_barcode');

		$i = 0;
		foreach ($stocks->each(500) as $stock) {
			if (BarcodeManager::isReturnProductBarcode($stock->product_barcode)) {
				$i++;
			}
		}
		echo "<br />";
		echo $i;
		echo "<br />";


		return 'Y';
	}

	/*
     *  Находим инвентори ряды в которых старый адрес равен инвентори адресу
     *  Идем в живую базу и берем от туда старые адреса коробов
     * */
	public function actionMoveOldBoxAddressForInventory()
	{
		// other/one/move-old-box-address-for-inventory

		die('other/one/move-old-box-address-for-inventory');

		$dbLiving = Yii::$app->dbLiving;
//        $dbLiving = '';
		$stockList = Stock::find()->andWhere([
			'client_id' => Client::CLIENT_DEFACTO,
			'inventory_id' => 7,
			'inventory_primary_address' => '0-inventory-0',
			'primary_address' => '0-inventory-0'
		])->all();

		foreach ($stockList as $stockDemo) {
			$stockLiving = Stock::find()->andWhere(['id' => $stockDemo->id])->one($dbLiving);
			if ($stockLiving) {
				$stockDemo->inventory_primary_address = $stockLiving->primary_address;
				$stockDemo->save(false);
			}
		}

	}

	public function actionDeleteReservedLotFromLivingDb()
	{
		// other/one/delete-reserved-lot-from-living-db

		die('other/one/delete-reserved-lot-from-living-db');

		$dbLiving = Yii::$app->dbLiving;
//        $dbLiving = '';
		$stockList = Stock::find()->andWhere([
			'client_id' => Client::CLIENT_DEFACTO,
			'inventory_id' => 7,
		])->all();

		foreach ($stockList as $stockDemo) {
			$stockLiving = Stock::find()->andWhere(['id' => $stockDemo->id])->one($dbLiving);
			if ($stockLiving) {
				if ($stockLiving->status_availability == 3) {
					$stockDemo->deleted = 2;
					//$stockDemo->save(false);
				}
			}
		}


	}

	public function actionSetProductType()
	{
		// other/one/set-product-type

		echo "other/one/set-product-type<br />";
//        die("--DIE--");

		$i = 1;

		foreach (Stock::find()->andWhere('is_product_type NOT IN (23)')->andWhere(['client_id' => 2, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])->orderBy(['id' => SORT_DESC])->each(50) as $stock) {
			if (BarcodeManager::isReturnBoxBarcode($stock->primary_address)) {
				$stock->is_product_type = Stock::IS_PRODUCT_TYPE_RETURN;
				//file_put_contents('IsReturnReport09-10-2018.xlsx', $stock->inbound_client_box . ';'.$stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode ."\n", FILE_APPEND);
			} else if (BarcodeManager::isOneBoxOneProduct($stock->primary_address, 2)) {
				$stock->is_product_type = Stock::IS_PRODUCT_TYPE_LOT_BOX;
				//echo "OK = > " . $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode . "<br />";
//                 file_put_contents('IsNOReturn-1.xlsx', $stock->secondary_address . ';' . $stock->primary_address . ';' . $stock->product_barcode ."\n", FILE_APPEND);
			}
			$stock->save(false);
		}

		return '<br />- - return OK - -<br />';
	}

	public function actionResetStockOutboundOrder()
	{
		// other/one/reset-stock-outbound-order
		die('other/one/reset-stock-outbound-order');

		$orderListIDs[] = 49724;  // 11374237	97318

		foreach ($orderListIDs as $outboundOrderID) {
			//Stock::resetByOutboundOrderId($outboundOrderID);
		}

		return 'return OK';
	}

	public function actionTestLongRack()
	{
		// other/one/test-long-rack
		$addressList = Inventory::getMinMaxSecondaryAddress('6-1-01-0');
		$addressList = Inventory::getMinMaxSecondaryAddress('6-1-25-3');
		$addressList = Inventory::getMinMaxSecondaryAddress('1-7-01-1');
//         $addressList = Inventory::getMinMaxSecondaryAddress('1-13-01-1');
		VarDumper::dump($addressList, 10, true);

//        $inventory_primary_address = '700000691309';
		$inventory_primary_address = '';
//        $productBarcode = '2300007063250'; // 6-1-01-0
//        $productBarcode = '2300006997518'; // 6-1-25-3
//        $productBarcode = '700000697884'; // 6-1-25-3
//        $productBarcode = '2300004492046'; // 1-13-01-1
		$productBarcode = '2300004492046'; // 1-7-01-1
		$boxBarcode = '700000677138'; // 1-7-01-1


		$inventory_id = '0';
		$x = Stock::find()->andWhere([
//            'inventory_primary_address'=>$inventory_primary_address,
			'product_barcode' => $productBarcode,
			'secondary_address' => $addressList,
//            'status_inventory'=>Inventory::STATUS_SCAN_PROCESS,
			'inventory_id' => $inventory_id
		])->one();
		VarDumper::dump($x, 10, true);


		echo '<br />--------------------------------------------------------<br />';


//        $productBarcode = "700000665167";// не в адресе
//        $productBarcode = "70000";// не в адресе


//        if(BarcodeManager::isBoxLotOrReturnBox($productBarcode)) {
//        if(BarcodeManager::isBoxOrProductTypeReturn($productBarcode)) {
//        if(BarcodeManager::isBoxLotOrReturnBox($productBarcode) && BarcodeManager::isBoxOrProductTypeReturn($productBarcode)) {
//        if(BarcodeManager::isBoxOrProductTypeLotBox($productBarcode)) {
//        if(BarcodeManager::isBoxLotOrReturnBox($productBarcode) && BarcodeManager::isBoxOrProductTypeLotBox($productBarcode)) {
//        if(BarcodeManager::isBoxOrProductTypeLot($productBarcode)) {
//        if($if) {

		echo "Box : " . $boxBarcode . "<br />";
		echo "REal : " . $productBarcode . "<br />";
		if (BarcodeManager::isBoxLotOrReturnBox($productBarcode, $addressList, $boxBarcode)) {
			$productBarcode = BarcodeManager::findProductInStockByReturnBarcodeBoxInventory($productBarcode);
			echo "isBoxLotOrReturnBox<br />";
		}
		echo "After : " . $productBarcode . "<br />";


//        if(BarcodeManager::isBoxLotOrReturnBox($productBarcode,$addressList)) {

//            if(BarcodeManager::isBoxOnlyOur($productBarcode)) {
//                $productBarcode1=  Stock::find()->select('product_barcode')->andWhere(['inventory_primary_address'=>$productBarcode])->scalar();
//                echo "isBoxOnlyOur : ".$productBarcode1."<br />";
//            }

//            $productBarcode = BarcodeManager::findProductInStockByReturnBarcodeBoxInventory($productBarcode);
//
//            echo "After : ".$productBarcode."<br />";
//        }

		return 'Y';
	}


	public function actionTestNewTtn()
	{
		// other/one/test-new-ttn
		$model = TlDeliveryProposal::findOne(38346);

//        $userName = '';
		$storeFrom = $model->routeFrom;
//        $managersNamesTo = 'Контакты получателей:<br />';
//        if($routeTo = $model->routeTo) {
//            // находим всех директоров магазина и отправляем им имейлы
//            $clientEmployees = ClientEmployees::find()
//                ->andWhere([
//                    'client_id'=>$model->client_id,
//                    'store_id'=>$routeTo->id,
//                    'manager_type'=>[
//                        ClientEmployees::TYPE_BASE_ACCOUNT,
//                        ClientEmployees::TYPE_DIRECTOR,
//                        ClientEmployees::TYPE_DIRECTOR_INTERN,
//                    ]
//                ])
//                ->all();
//
//            foreach($clientEmployees as $item) {
//                $managersNamesTo .= $item->first_name.' '.$item->last_name.' / '.$item->phone_mobile.' '.$item->phone."<br />";
//            }
//            $managersNamesTo .= $routeTo->phone_mobile.' / '.$routeTo->phone."<br />";
//        }


		// Пунк погрузки
//        $loadingPoint = $model->routeFrom->getPointTitleByPattern('{city_name},  {street} {house}');

		// Пункт разгрузки
		$endPointAddress = $model->routeTo->getPointTitleByPattern('{city_name} / {street} {house}');
		$endPointCompanyName = $model->routeTo->getPointTitleByPattern('{name}');

		$day = Yii::$app->formatter->asDatetime($model->shipped_datetime, 'php:d');
		$monthYear = Yii::$app->formatter->asDatetime($model->shipped_datetime, 'php:F Y');
		$dateTime = [];
		$dateTime['day'] = $day;
		$dateTime['monthYear'] = $monthYear;
		$ttnNumber = $model->id;


//        echo $day."<br />";
//        echo $monthYear."<br />";
//        die();
		// если отправляем груз со склада, то печатаем 3 копии файла ТТН
		// 4 = DC - это наш склад
		if (in_array($storeFrom->id, [4])) {
			$model->shipped_datetime = DateHelper::getTimestamp();
			$model->delivery_date = DateHelper::getTimestamp();
			$model->status = TlDeliveryProposal::STATUS_ON_ROUTE;
			$model->save(false);
		}

		$dpManager = new DeliveryProposalManager(['id' => $model->id]);
		$dpManager->onPrintTtn();
		$dpManager->setCascadeDeliveryDate();
		$dpManager->setCascadedStatus();


		$outboundOrderItems = [];
		$outboundOrderItems['products'] = [];
		$outboundOrderItems['totalProductQty'] = 0;

		if ($relatedOrders = $model->proposalOrders) {
			foreach ($relatedOrders as $order) {
				if ($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
					if ($oo = $order->outboundOrder) {

						$oo->date_delivered = $model->delivery_date;
						$oo->status = Stock::STATUS_OUTBOUND_COMPLETE;
						$oo->save(false);

						$orderItems = $oo->orderItems;

						if ($orderItems) {
							foreach ($orderItems as $orderItem) {
								$outboundOrderItems['products'][] = [
									'productName' => $orderItem->product_name,
									'productBarcode' => $orderItem->product_barcode,
									'acceptedQty' => $orderItem->accepted_qty,
								];

								$outboundOrderItems['totalProductQty'] += $orderItem->accepted_qty;
							}
						}

					}
				}
			}
		}

		return $this->render('print-ttn/print-ttn-pdf', [
			'model' => $model,
//            'userName'=>$userName,
//            'managersNamesTo'=>$managersNamesTo,
			'outboundOrderItems' => $outboundOrderItems,
			'endPointAddress' => $endPointAddress,
			'endPointCompanyName' => $endPointCompanyName,
			'dateTime' => $dateTime,
			'ttnNumber' => $ttnNumber,
		]);
	}


	public function actionReOpenExcel()
	{
		// other/one/re-open-excel

		$productsOnStock = Stock::find()
								->select('product_barcode as productBarcode, count(product_barcode) as productQty')
								->where([
									'client_id' => 95,
									'status_availability' => Stock::STATUS_AVAILABILITY_YES,
								])
								->groupBy('product_barcode')
								->asArray()
								->all();

		$excel = \PHPExcel_IOFactory::load('tmp-file/car-parts/hak/stock-template.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$orderNumber = '000001'; // <Номер документа>
		$createdAt = '2019-01-23'; // <Дата составления>
		// Header
		$excelActive->setCellValue('AR13', $orderNumber);
		$excelActive->setCellValue('AW13', $createdAt);

		// Rows
		$row = 58;
		$i = 1;
		foreach ($productsOnStock as $product) {
			$i++;
			$row++;

			$excelActive->insertNewRowBefore($row, 1);
			$excelActive->mergeCells("B" . $row . ":D" . $row);
			$excelActive->setCellValue('B' . $row, $i);

			$excelActive->mergeCells("E" . $row . ":P" . $row);
			$excelActive->setCellValue('E' . $row, "E" . $row);

			$excelActive->mergeCells("Q" . $row . ":U" . $row);
			// Номенклатурный номер
			$excelActive->mergeCells("V" . $row . ":AA" . $row);
			$excelActive->setCellValue('V' . $row, $product['productBarcode']);

			$excelActive->mergeCells("AB" . $row . ":AE" . $row);
			$excelActive->setCellValue('AB' . $row, "ШТ");

			$excelActive->mergeCells("AF" . $row . ":AJ" . $row);
			// количество
			$excelActive->mergeCells("AK" . $row . ":AN" . $row);
			$excelActive->setCellValue('AK' . $row, $product['productQty']);

			$excelActive->mergeCells("AO" . $row . ":AS" . $row);

			// количество
			$excelActive->mergeCells("AT" . $row . ":AW" . $row);
			$excelActive->setCellValue('AT' . $row, $product['productQty']);

			$excelActive->mergeCells("AX" . $row . ":BB" . $row);
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="re-open-excel' . time() . '.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->save('php://output');
		Yii::$app->end();


//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Content-Disposition: attachment;filename="re-open-excel' . time() . '.xlsx"');
//        header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->save('re-open-excelX.xlsx');
		//Yii::$app->end();
	}


	public function actionResendCrossDock()
	{
		// other/one/resend-cross-dock
		die("actionResendCrossDock die");
		$crossDockLc = [
			'141013874155',
			'141013896560',
			'141013918699',
			'141014024450',
			'141014051197',
			'141014054976',
			'141014059117',
			'141014069857',
			'141014073335',
			'141014079948',
			'141014086212',
			'141014411410',
			'142012172631',
			'142014264006',
			'142014514163',
			'142014552110',
			'142014643412',
			'142014644235',
			'142014645126',
			'142014655934',
			'142014661775',
			'142014662352',
			'142014668552',
			'142014673068',
			'142014673129',
			'142014679817',
			'142014683340',
			'142014683777',
			'142014798495',
			'142014809931',
			'142014915366',
			'143013171319',
			'143013588308',
			'143013683263',
			'143013712536',
			'143013729619',
			'143013742892',
			'143013743080',
			'143013743561',
			'143013766324',
			'143013777108',
			'143013779515',
			'143013780146',
			'143013780641',
			'143013780740',
			'143013780818',
			'143013781891',
			'143013787688',
			'144005087168',
			'144008160356',
			'145017432274',
			'145017433363',
			'145017436791',
			'145017438078',
			'145017438238',
			'145017438306',
			'145017438627',
			'145017439600',
			'145017447476',
			'145017456539',
			'145017470153',
			'145017477664',
			'145017478500',
			'145017488882',
			'145017489001',
			'145018412671',
			'145018431368',
			'145018432884',
			'145018435618',
			'145018435625',
			'145018435649',
			'145018435663',
			'145018563977',
			'145018563984',
			'145018564301',
			'145018564318',
			'145018564707',
			'145018564738',
			'145018564790',
			'145018564820',
			'145018564844',
			'146012628143',
			'146013884227',
			'146014759074',
			'146014783123',
			'146015084229',
			'146015104842',
			'146015340950',
			'146015427392',
			'146015439043',
			'146015444320',
			'146015451205',
			'146015459201',
			'146015474259',
			'146015634417',
			'146015637432',
			'146015696514',
			'146015710173',
			'146015837665',
			'146015907757',
			'146015988824',
			'146015989425',
			'146015992555',
			'146015992869',
			'146016000303',
			'146016000389',
			'146016001102',
			'146016005643',
			'146016024781',
			'147011521084',
			'147014967810',
			'147014968879',
			'147015167103',
			'147015193041',
			'147015356293',
			'147015418625',
			'147015444204',
			'147015503093',
			'147015504151',
			'147015506445',
			'147015511036',
			'147015551780',
			'147015585860',
			'147015611675',
			'147015663018',
			'147015666019',
			'147015668600',
			'147015675172',
			'147015675356',
			'147015676292',
			'147015676308',
			'147015680565',
			'147015681203',
			'147015705527',
			'147015712709',
			'147015719753',
			'147015720018',
			'147015720117',
			'147015720452',
			'148007178473',
			'148007278074',
			'148007497048',
			'148007566690',
			'148008078109',
			'148008622760',
			'148008627635',
			'148008868892',
			'148008877764',
			'2430009073184',
			'2430009240134',
			'2430009240135',
			'2430009391398',
			'2430009489342',
			'2430009489343',
			'2430009489344',
			'2430009489346',
			'2430009489347',
			'2430009489349',
			'2430009489350',
			'2430009489351',
			'2430009489352',
			'2430009489353',
			'2430009489354',
			'2430009489355',
			'2430009489356',
			'2430009489357',
			'2430009522326',
			'2430009522327',
			'2430009522328',
			'2430009522330',
			'2430009522332',
			'2430009523042',
			'2430009523043',
			'2430009523044',
			'2430009523047',
			'2430009539378',
			'2430009539379',
			'2430009541668',
			'2430009579123',
			'2430009579124',
			'2430009579127',
			'2430009579128',
			'2430009598410',
			'2430009627340',
			'2430009627356',
			'2430009627358',
			'2430009648977',
			'2430009648978',
			'2430009648979',
			'2430009648980',
			'2430009648981',
			'2430009648989',
			'2430009652352',
			'2430009652364',
			'2430009652490',

		];
		foreach ($crossDockLc as $i => $lc) {
			$crossDockItem = CrossDockItems::find()->andWhere(['box_barcode' => $lc])->one();
			$crossDockID = $crossDockItem->cross_dock_id;
			$lcBarcodeToValidate = $crossDockItem->box_barcode;
			$pref = 'D10AA00204242';

			$this->sendCrossDock($crossDockID, $lcBarcodeToValidate, $pref);

			echo "<br />crossDockID = " . $crossDockID;
			echo "<br />lcBarcodeToValidate = " . $lcBarcodeToValidate;
			echo "<br />pref = " . $pref;
		}


		return '- DONE - ';
	}

	private function sendCrossDock($crossDockID, $lcBarcodeToValidate, $pref)
	{

		// other/one/resend-cross-dock

//        $crossDockID  = 12028;
//        $pref = 0;
//        $lcBarcodeToValidate = '2430007042325';

		$api = new DeFactoSoapAPIV2Manager();
		if ($crossDocks = CrossDock::findAll(['id' => $crossDockID, 'client_id' => Client::CLIENT_DEFACTO])) {
			foreach ($crossDocks as $crossDock) {
				$row = [];
				$rows = [];
				if ($crossDockItems = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id])) {
					foreach ($crossDockItems as $crossDockItem) {
						$rowTmp = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataCrossDock($crossDockItem);
						$rows[] = array_shift($rowTmp);
					}
				}

				$rowsFiltered = [];
				foreach ($rows as $rowLine) {
					if ($rowLine['LcOrCartonLabel'] == $lcBarcodeToValidate) {
						$rowsFiltered[] = $rowLine;
					}
				}

				$rows = $rowsFiltered;
				$row['InBoundFeedBackThreePLResponse'] = $rows;
				if (!empty($rows)) {
					$resInbound = $api->SendInBoundFeedBackData($row);
					VarDumper::dump($resInbound, 10, true);
					echo "<br />";
					echo "<br />";
					file_put_contents($pref . "_SendInBoundFeedBackData-CRoss-dock-" . $crossDock->party_number . ".log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				} else {
					file_put_contents($pref . "_SendInBoundFeedBackData-CRoss-dock-ERROR.log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				}
			}
		}

		// второй шаг.
		if ($crossDocks = CrossDock::findAll(['id' => $crossDockID, 'client_id' => Client::CLIENT_DEFACTO]) && false) {
			foreach ($crossDocks as $crossDock) {
				$row = [];
				$rows = [];
				if ($items = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id, 'status' => Stock::STATUS_CROSS_DOCK_SCANNED])) {
					foreach ($items as $item) {
						$rowTmp = DeFactoSoapAPIV2Manager::preparedSendCrossDockOutBoundFeedBackDataOutbound($item, $crossDock);
						$rows[] = array_shift($rowTmp);
					}
				}

				$rowsFiltered = [];
				foreach ($rows as $rowLine) {
					if ($rowLine['LcBarcode'] == $lcBarcodeToValidate) {
						$rowsFiltered[] = $rowLine;
					}
				}

				$rows = $rowsFiltered;

				$row['OutBoundFeedBackThreePLResponse'] = $rows;
				if (!empty($rows)) {
					//$resOutbound = $api->SendOutBoundCrossDockFeedBackData($row);

					// VarDumper::dump($resOutbound, 10, true);
					echo "<br />";
					echo "<br />";
					file_put_contents($pref . "_SendOutBoundCrossDockFeedBackData-" . $crossDock->party_number . ".log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				} else {
					file_put_contents($pref . "_SendOutBoundCrossDockFeedBackData-CRoss-dock-ERROR.log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				}
			}
		}
//        }
		return 'Y - ' . $pref . ' - ' . $lcBarcodeToValidate . ' - ' . $crossDockID;
	}

	public function actionResendCrossDockFull()
	{ // other/one/resend-cross-dock-full

		die('other/one/resend-cross-dock-full');

		$crossDockIDs = [
			'14351',
			'14350',
			'14349',
			'14348',
			'14347',
			'14346',
			'14345',
			'14344',
			'14343',
			'14342',
			'14341',
			'14340',
			'14339',
		];

		foreach ($crossDockIDs as $crossDockID) {
			$this->sendCompleteCrossDockAPI($crossDockID);
		}
	}

	private function sendCompleteCrossDockAPI($crossDockID)
	{

		$api = new DeFactoSoapAPIV2Manager();
		if ($crossDocks = CrossDock::findAll(['id' => $crossDockID, 'client_id' => Client::CLIENT_DEFACTO])) {
			foreach ($crossDocks as $crossDock) {
				$row = [];
				$rows = [];
				if ($crossDockItems = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id])) {
					foreach ($crossDockItems as $crossDockItem) {
						$rowTmp = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataCrossDock($crossDockItem);
						$rows[] = array_shift($rowTmp);
					}
				}
				$row['InBoundFeedBackThreePLResponse'] = $rows;
				if (!empty($rows)) {
					//VarDumper::dump($rows,10,true);
					$api->SendInBoundFeedBackData($row);
					file_put_contents("SendInBoundFeedBackData.CSV", serialize($row) . "\n" . "\n", FILE_APPEND);
					file_put_contents("ReSendInBoundFeedBackData-CRoss-dock-ONE-OTHER" . $crossDock->party_number . ".log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				} else {
					file_put_contents("ReSendInBoundFeedBackData-CRoss-dock-ERROR-ONE-OTHER.log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				}
			}
		}

		// второй шаг.
		if ($crossDocks = CrossDock::findAll(['id' => $crossDockID, 'client_id' => Client::CLIENT_DEFACTO])) {
			foreach ($crossDocks as $crossDock) {
				$row = [];
				$rows = [];
				if ($items = CrossDockItems::findAll(['cross_dock_id' => $crossDock->id, 'status' => Stock::STATUS_CROSS_DOCK_SCANNED])) {
					foreach ($items as $item) {
						$rowTmp = DeFactoSoapAPIV2Manager::preparedSendCrossDockOutBoundFeedBackDataOutbound($item, $crossDock);
						$rows[] = array_shift($rowTmp);
					}
				}
				$row['OutBoundFeedBackThreePLResponse'] = $rows;
				if (!empty($rows)) {
					//VarDumper::dump($row,10,true);
					$api->SendOutBoundCrossDockFeedBackData($row);
					file_put_contents("ReSendOutBoundCrossDockFeedBackData-ONE-OTHER-" . $crossDock->party_number . ".log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				} else {
					file_put_contents("ReSendOutBoundCrossDockFeedBackData-CRoss-dock-ERROR-ONE-OTHER.log", print_r($row, true) . "\n" . "\n", FILE_APPEND);
				}
			}
		}
	}

	public function actionExistInInboundOrder()
	{
		// other/one/exist-in-inbound-order
		//$excel = \PHPExcel_IOFactory::load('tmp-file/defacto/inbound/Damage.xlsx');
		$excel = \PHPExcel_IOFactory::load('tmp-file/defacto/inbound/Norm.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";

		$start = 1;
		$inbound_order_id = 59691;
		$ii = 1;
		for ($i = $start; $i <= 5000; $i++) {
			$box = $excelActive->getCell('A' . $i)->getValue();
			$lc = $excelActive->getCell('B' . $i)->getValue();
//            $id = (int)$excelActive->getCell('C' . $i)->getValue();
			if (empty($box) && empty($lc)) {
				continue;
			}

			$stock = Stock::find()->select('id, inbound_client_box,primary_address')
						  ->andWhere(['primary_address' => $box, 'inbound_order_id' => $inbound_order_id])
						  ->andWhere(['or', ['inbound_client_box' => $lc], ['product_barcode' => $lc]])
						  ->one();
			if ($stock) {
			} else {
				$stock = Stock::find()->select('id, inbound_client_box,primary_address')
							  ->andWhere(['primary_address' => $box, 'inbound_order_id' => $inbound_order_id])
							  ->andWhere(['or', ['inbound_client_box' => $lc], ['product_barcode' => $lc], ['primary_address' => $box]])
							  ->one();

				if ($stock) {
					echo $ii++ . "; " . $box . "; " . $lc . ";короб есть,а элси нет; <br />";
				} else {
					echo $ii++ . "; " . $box . "; " . $lc . ";нет совсем; <br />";
				}

			}
		}

		return $this->render('index');
	}

	public function actionPrintOldTtn()
	{ // other/one/print-old-ttn?id=39773
//        $id = Yii::$app->request->get('id');
		die('other/one/print-old-ttn?id=39773');
		$modelList = [];
		$iDs = [
			42699,
			42413,
			40651,
			40493, 41368, 41251, 41116, 41040, 40898, 40653, 40245, 40236, 40256, 42683, 42678, 42693, 42469, 42481, 41776, 41734, 41763, 41612, 41487, 41467
		];

		foreach ($iDs as $id) {
			$model = TlDeliveryProposal::findOne($id);

			$managersNamesTo = $this->makeSippingContact($model->routeTo, $model->client_id);

			$modelList[] = [
				'model' => $model,
				'managersNamesTo' => $managersNamesTo,
			];
		}


		return $this->render('old-ttn/print-ttn-pdf', ['modelList' => $modelList]);

//        return $this->render('old-ttn/print-ttn-pdf', ['model' => $model, 'userName' => $userName, 'managersNamesTo' => $managersNamesTo]);
	}

	public function actionPrintOldTtnOne()
	{ // other/one/print-old-ttn?id=39773
		$id = Yii::$app->request->get('id');
		$model = TlDeliveryProposal::findOne($id);

		$userName = '';
//        $storeFrom = $model->routeFrom;

		$managersNamesTo = $this->makeSippingContact($model->routeTo, $model->client_id);

		// если отправляем груз со склада, то печатаем 3 копии файла ТТН
		// 4 = DC - это наш склад
//        if (in_array($storeFrom->id, [4])) {
//            $model->shipped_datetime = DateHelper::getTimestamp();
//            $model->status = TlDeliveryProposal::STATUS_ON_ROUTE;
//            $model->save(false);
//
//        }
//        $dpManager = new DeliveryProposalManager(['id' => $model->id]);
//        $dpManager->onPrintTtn();

		$this->render('old-ttn/print-ttn-pdf', ['model' => $model, 'userName' => $userName, 'managersNamesTo' => $managersNamesTo]);

//        return $this->render('old-ttn/print-ttn-pdf', ['model' => $model, 'userName' => $userName, 'managersNamesTo' => $managersNamesTo]);
	}

	private function makeSippingContact($routeTo, $clientId)
	{
		$managersNamesTo = 'Контакты получателей:' . "<br />";
		if (empty($routeTo) || empty($clientId)) {
			return '';
		}

		// находим всех директоров магазина и отправляем им имейлы
		$clientEmployees = ClientEmployees::find()
										  ->andWhere([
											  'status' => ClientEmployees::STATUS_ACTIVE,
											  'client_id' => $clientId,
											  'store_id' => $routeTo->id,
											  'manager_type' => [
												  ClientEmployees::TYPE_BASE_ACCOUNT,
												  ClientEmployees::TYPE_DIRECTOR,
												  ClientEmployees::TYPE_DIRECTOR_INTERN,
												  ClientEmployees::TYPE_MANAGER,
											  ]
										  ])
										  ->all();

		if (empty($clientEmployees)) {
			return '';
		}

		$managersNamesTo .= '<table width="100%" border="0">';
		$managersNamesTo .= "<tr>";
		foreach ($clientEmployees as $item) {
			$managersNamesTo .= "<td width=\"23%\">" . $item->first_name . ' ' . $item->last_name . ' / ' . $item->phone_mobile . ' ' . $item->phone . "</td>";
		}
		$managersNamesTo .= "</tr>";
		$managersNamesTo .= '</table>';

		return $managersNamesTo;
	}


	public function actionCreateReturn()
	{ // /other/one/create-return
		die('/other/one/create-return');
//        $returnOrderItemProductPrepared[] = [
//            'InboundId' =>'5857254',
//            'AppointmentBarcode' => 'D10AA00136007',
//            'LcOrCartonLabel' =>'2430007489458',
//            'LotOrSingleBarcode' =>'2300011729395',
//            'LotOrSingleQuantity' => '1',
//            'OurBoxBarcode' => '---------------------',
//        ];

//        $returnOrderItemProductPrepared[] = [
//            'InboundId' =>'5857258',
//            'AppointmentBarcode' => 'D10AA00136007',
//            'LcOrCartonLabel' =>'2430007491884',
//            'LotOrSingleBarcode' =>'2300011729449',
//            'LotOrSingleQuantity' => '1',
//            'OurBoxBarcode' => '------------------',
//        ];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857254',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007481901',
			'LotOrSingleBarcode' => '2300011729333',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '7388',
			'OurBoxBarcode' => '700000762009',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857260',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007492946',
			'LotOrSingleBarcode' => '2300011729463',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '990',
			'OurBoxBarcode' => '700000762007',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857246',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007481430',
			'LotOrSingleBarcode' => '2300011729319',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '836',
			'OurBoxBarcode' => '700000741294',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857252',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489122',
			'LotOrSingleBarcode' => '2300011729371',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '990',
			'OurBoxBarcode' => '700000757189',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857250',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489038',
			'LotOrSingleBarcode' => '2300011729357',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '7388',
			'OurBoxBarcode' => '700000762008',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857256',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489774',
			'LotOrSingleBarcode' => '2300011729418',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '836',
			'OurBoxBarcode' => '700000740293',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857247',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007481777',
			'LotOrSingleBarcode' => '2300011729326',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '7388',
			'OurBoxBarcode' => '700000762003',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857253',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489226',
			'LotOrSingleBarcode' => '2300011729388',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '9873',
			'OurBoxBarcode' => '700000762010',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857259',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007492496',
			'LotOrSingleBarcode' => '2300011729456',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '9873',
			'OurBoxBarcode' => '700000762011',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857245',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007480199',
			'LotOrSingleBarcode' => '2300011729302',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '2190',
			'OurBoxBarcode' => '700000762005',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857251',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007489042',
			'LotOrSingleBarcode' => '2300011729364',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '7388',
			'OurBoxBarcode' => '700000762004',
		];

		$returnOrderItemProductPrepared[] = [
			'InboundId' => '5857257',
			'AppointmentBarcode' => 'D10AA00136007',
			'LcOrCartonLabel' => '2430007490430',
			'LotOrSingleBarcode' => '2300011729432',
			'LotOrSingleQuantity' => '1',
			'FromBusinessUnitId' => '2190',
			'OurBoxBarcode' => '700000757192',
		];

		$toPointClientId = 1029;
		$toPointId = 4;
		$api = new DeFactoSoapAPIV2Manager();

		foreach ($returnOrderItemProductPrepared as $return) {
			//Inbound
			$inbound = new InboundOrder();
			$inbound->parent_order_number = ReturnTmpOrder::PARTY_NUMBER;
			$inbound->order_number = $return['LcOrCartonLabel'];//.'/'.$return['AppointmentBarcode'];
			$inbound->status = Stock::STATUS_INBOUND_SCANNED;
			$inbound->order_type = InboundOrder::ORDER_TYPE_RETURN;
			$inbound->client_id = Client::CLIENT_DEFACTO;
			$inbound->expected_qty = 1;
			$inbound->accepted_qty = 1;
			$inbound->save(false);

			//Inbound item
			$inboundOrderItem = new InboundOrderItem();
			$inboundOrderItem->inbound_order_id = $inbound->id;
			$inboundOrderItem->product_barcode = $return['LotOrSingleBarcode'];
			$inboundOrderItem->product_serialize_data = serialize($return);
			$inboundOrderItem->box_barcode = $return['LcOrCartonLabel'];
			$inboundOrderItem->expected_qty = 1;
			$inboundOrderItem->accepted_qty = 1;
			$inboundOrderItem->status = Stock::STATUS_INBOUND_SCANNED;
			$inboundOrderItem->save(false);

			$stock = new Stock();
			$stock->client_id = Client::CLIENT_DEFACTO;;
			$stock->inbound_order_id = $inboundOrderItem->inbound_order_id;
			$stock->inbound_order_item_id = $inboundOrderItem->id;
			$stock->product_barcode = $inboundOrderItem->product_barcode;
			$stock->product_model = '';
			$stock->status = Stock::STATUS_INBOUND_SCANNED;
			$stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
			$stock->inbound_client_box = $inboundOrderItem->box_barcode;
			$stock->primary_address = $return['OurBoxBarcode'];
			$stock->secondary_address = '00000';
			$stock->address_sort_order = 0;
			$stock->save(false);


			$inbound->status = Stock::STATUS_INBOUND_CONFIRM;
			$inbound->date_confirm = time();
			$inbound->begin_datetime = time();
			$inbound->from_point_id = $api->getStore($return['FromBusinessUnitId']);
			$inbound->from_point_title = $return['FromBusinessUnitId'];
			$inbound->to_point_id = $toPointId;
			$inbound->to_point_title = $toPointClientId;
			$inbound->save(false);

			InboundOrderItem::updateAll(['status' => Stock::STATUS_INBOUND_CONFIRM], ['inbound_order_id' => $inbound->id]);

			Stock::updateAll([
				'status' => Stock::STATUS_INBOUND_CONFIRM,
				'status_availability' => Stock::STATUS_AVAILABILITY_YES,
			], [
				'inbound_order_id' => $inbound->id,
				'status' => [
					Stock::STATUS_INBOUND_SCANNED,
					Stock::STATUS_INBOUND_OVER_SCANNED,
				]
			]);
//            VarDumper::dump($inbound,10,true);
//            die;
		}
	}


	private function findDuplicate($prefMonth, $currentMonth)
	{
		echo "<br />" . date('Y-m-d') . "<br /><br />";
		foreach ($prefMonth as $key => $value) {
			if (in_array($key, $currentMonth)) {
				echo $key . "-ДУБЛИ<br />";
			}
		}
	}

	private function parsTtnFiles($path)
	{

		$excel = \PHPExcel_IOFactory::load($path);
//        $excel = \PHPExcel_IOFactory::load($rootPath . '01052019/31-05-2019.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$reportFrom = [];
		$start = 1;
		for ($i = $start; $i <= 1000; $i++) {
			$id = (int)$excelActive->getCell('I' . $i)->getValue();
			if ($id == null) {
				continue;
			}
			$reportFrom[$id] = $id;
		}

		return $reportFrom;
	}

	public function actionReportReturnOnStock()
	{
		// /other/one/report-return-on-stock
		$clientId = Client::CLIENT_DEFACTO;
		$stocks = Stock::find()->select('product_barcode,inbound_client_box')->distinct()->andWhere([
			'client_id' => $clientId,
			'status_availability' => [Stock::STATUS_AVAILABILITY_NO, Stock::STATUS_AVAILABILITY_NOT_SET, Stock::STATUS_AVAILABILITY_RESERVED]
		])->groupBy('product_barcode');

		//$items = [];
		foreach ($stocks->each(50) as $stock) {
			if (BarcodeManager::isReturnProductBarcode($stock->product_barcode)) {
				file_put_contents('Report-Return-On-Stock-no-' . date("Y-m-d-H") . '.log', $stock->product_barcode . ';' . $stock->inbound_client_box . ';' . "\n", FILE_APPEND);
			}
		}
		return 'Done';
	}

	public function actionResendInboundOrder()
	{ // /other/one/resend-inbound-order
		//die("actionResendInboundOrder - DIE");

		$io = InboundOrder::findOne('69024'); // D10AA00165063	KZ-19-TR-061

//        if ($items = InboundOrderItem::findAll(['inbound_order_id' => $io->id,'status'=>[4]])) {
		if ($items = InboundOrderItem::findAll(['inbound_order_id' => $io->id])) {
			$row = [];
			$api = new DeFactoSoapAPIV2Manager();
			foreach ($items as $item) {
				$rows['InBoundFeedBackThreePLResponse'] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackData($item, $io->order_number);
				if (!empty($rows['InBoundFeedBackThreePLResponse'])) {
					$api->SendInBoundFeedBackData($rows);
				}
			}
		}
		die('YPA');


		$rows = [];
		$outResult = [];
		$row[] = [
			'InboundId' => '',//'48', // если плюсы то передаем null
			'AppointmentBarcode' => 'D10AA00079314',//'D10AA00033871',
			'LcOrCartonLabel' => '147008117740', //'2300000187083',
			'LotOrSingleBarcode' => '2300004462957', //'2300000187083',
			'LotOrSingleQuantity' => '2', //'1',
		];
		$rows['InBoundFeedBackThreePLResponse'] = $row;
//        $api = new DeFactoSoapAPIV2Manager();
//        $outResult = $api->SendInBoundFeedBackData($rows);
		VarDumper::dump($rows, 10, true);
		echo "<br />";
		echo "<br />";
		VarDumper::dump($outResult, 10, true);
	}


	public function actionLotReportByInboundOrder()
	{
		// other/one/lot-report-by-inbound-order
		$lotBarcodeList = [
			'D10AA00127409' => 2300010954606
		];

		$result = [];
		foreach ($lotBarcodeList as $inboundNumber => $lotBarcode) {

			$inbound = InboundOrder::find()->andWhere(['order_number' => $inboundNumber])->one();
			if (!$inbound) {
				continue;
			}

			$inboundItems = InboundOrderItem::find()->andWhere(['inbound_order_id' => $inbound->id, 'product_barcode' => $lotBarcode])->all();
			echo "<br />";
			echo "<br />";
			VarDumper::dump($inbound, 10, true);
			echo "<br />";
			echo "<br />";
			die();
			foreach ($inboundItems as $itemInbound) {

				$qtyStock = Stock::find()->andWhere([
					'product_barcode' => $itemInbound->product_barcode,
					'inbound_order_item_id' => $itemInbound->id,
					'inbound_order_id' => $itemInbound->inbound_order_id
				])->count();

				$result[$inboundNumber . '-' . $lotBarcode]['inbound'][] = [
					'InboundId' => ArrayHelper::getValue($inbound, 'id'),
					'InboundOrderNumber' => ArrayHelper::getValue($inbound, 'order_number'),
					'InboundParentOrderNumber' => ArrayHelper::getValue($inbound, 'parent_order_number'),
					'InboundExpectedQty' => ArrayHelper::getValue($inbound, 'expected_qty'),
					'InboundAcceptedQty' => ArrayHelper::getValue($inbound, 'accepted_qty'),

					'ItemId' => ArrayHelper::getValue($itemInbound, 'id'),
					'ItemExpectedQty' => $itemInbound->expected_qty,
					'ItemAcceptedQty' => $itemInbound->accepted_qty,

					'QtyOnStock' => $qtyStock,
				];

				$qtyStock = Stock::find()->andWhere(['product_barcode' => $itemInbound->product_barcode])->count();

				$result[$inboundNumber . '-' . $lotBarcode]['stock'][] = [
					'QtyAllOnStock' => $qtyStock,
				];

				$outboundItemList = OutboundOrderItem::find()->andWhere(['product_barcode' => $itemInbound->product_barcode])->all();

				foreach ($outboundItemList as $itemOutbound) {

					$outbound = OutboundOrder::find()->andWhere(['id' => $itemOutbound->outbound_order_id])->one();

					$qtyStock = Stock::find()->andWhere([
						'product_barcode' => $itemOutbound->product_barcode,
						'outbound_order_item_id' => $itemOutbound->id,
						'outbound_order_id' => $itemOutbound->outbound_order_id
					])->count();

					$result[$inboundNumber . '-' . $lotBarcode]['outbound'][] = [
						'OutboundId' => ArrayHelper::getValue($outbound, 'id'),
						'OutboundOrderNumber' => ArrayHelper::getValue($outbound, 'order_number'),
						'OutboundParentOrderNumber' => ArrayHelper::getValue($outbound, 'parent_order_number'),
						'OutboundExpectedQty' => ArrayHelper::getValue($outbound, 'expected_qty'),
						'OutboundAcceptedQty' => ArrayHelper::getValue($outbound, 'accepted_qty'),
						'OutboundAllocatedQty' => ArrayHelper::getValue($outbound, 'allocated_qty'),

						'ItemId' => ArrayHelper::getValue($itemOutbound, 'id'),
						'ItemExpectedQty' => $itemOutbound->expected_qty,
						'ItemAcceptedQty' => $itemOutbound->accepted_qty,
						'ItemAllocatedQty' => $itemOutbound->allocated_qty,

						'QtyOnStock' => $qtyStock,
					];
				}
			}
		}

		VarDumper::dump($result, 10, true);
	}

	public function actionResendInboundOrderOne()
	{
		// /other/one/resend-inbound-order-one

		die("actionResendInboundOrder ONE - DIE");

		$rows = [];
		$outResult = [];
		$row[] = [
			'InboundId' => 18482289, // -
			'AppointmentBarcode' => 'D10AA00383221', // -
			'LcOrCartonLabel' => '2430015817250', // +
			'LotOrSingleBarcode' => '2300025891880', // +
			'LotOrSingleQuantity' => 1, // -
			'IsDamaged' => false,
		];

		$rows['InBoundFeedBackThreePLResponse'] = $row;
		$api = new DeFactoSoapAPIV2Manager();
//        $outResult = $api->SendInBoundFeedBackData($rows);
//        VarDumper::dump($rows, 10, true);
		echo "<br />";
		echo "<br />";
		VarDumper::dump($outResult, 10, true);
		echo "<br />";
		echo "<br />";
		VarDumper::dump($rows, 10, true);
	}


	public function actionFindDiffInbound()
	{
		// /other/one/find-diff-inbound
		$rootPath = 'tmp-file/defacto/nnn/D10AA00181524.xlsx';
		$excel = \PHPExcel_IOFactory::load($rootPath);
//        $excel = \PHPExcel_IOFactory::load($rootPath . '01052019/31-05-2019.xlsx');
		$excel->setActiveSheetIndex(0);
		$activeSheet = $excel->getActiveSheet();

		$report = [];
		$start = 1;
		for ($i = $start; $i <= 1000; $i++) {
			$productBarcode = $activeSheet->getCell('A' . $i)->getValue();
			$productQtyDefacto = $activeSheet->getCell('B' . $i)->getValue();
			$productQtyNomadex = $activeSheet->getCell('C' . $i)->getValue();
			$productDiff = $activeSheet->getCell('D' . $i)->getValue();
			if ($productBarcode == null) {
				continue;
			}
			$report[] = [
				'productBarcode' => $productBarcode,
				'productQtyDefacto' => $productQtyDefacto,
				'productQtyNomadex' => $productQtyNomadex,
				'productDiff' => $productDiff,
				'productQtyNomadexOnStock' => 0,
			];
		}


		foreach ($report as $key => $row) {
			$qtyInOurStock = Stock::find()->andWhere(['product_barcode' => $row['productBarcode'], 'inbound_order_id' => '69024', 'client_id' => 2])->count();
			$report[$key]['productQtyNomadexOnStock'] = $qtyInOurStock;

			$if = ($report[$key]['productQtyNomadexOnStock'] == $report[$key]['productQtyDefacto']) && ($report[$key]['productQtyDefacto'] == $report[$key]['productQtyNomadex']);
//            if($if) {
//                unset($report[$key]);
//            }
			$activeSheet->setCellValue('E' . ($key += 1), $qtyInOurStock);
		}

//        foreach($report as $key=>$row)
//        {
//            $qtyInOurStock = Stock::find()->andWhere(['product_barcode'=>$row['productBarcode'],'inbound_order_id'=>'69024','client_id'=>2])->count();
//            $report[$key]['productQtyNomadexOnStock'] = $qtyInOurStock;
//
//            $if = ($report[$key]['productQtyNomadexOnStock'] == $report[$key]['productQtyDefacto']) && ($report[$key]['productQtyDefacto'] == $report[$key]['productQtyNomadex']);
//            if($if) {
//                unset($report[$key]);
//            }
//        }


		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="ordersWithProducts' . date('d.m.Y') . '.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->save('php://output');
		Yii::$app->end();

		VarDumper::dump($report, 10, true);

		die;
	}

	public function actionBeforeInventoryDiff()
	{

//        die('other/one/before-inventory-diff');
		//  other/one/before-inventory-diff


//        $this->helperUpdateDefactoSkuIdUpdate();
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//        echo "OK";
//        die;

		$excel = \PHPExcel_IOFactory::load('tmp-file/defacto/inventory03122019/SKU.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$reportFrom = [];
		$start = 2;
		for ($i = $start; $i <= 1000; $i++) {
			$skuID = $excelActive->getCell('A' . $i)->getValue();
			if ($skuID == null) {
				continue;
			}

			$product_barcode = Stock::find()->select('product_barcode')->andWhere(['field_extra1' => $skuID])->scalar();
			echo $skuID . "<br />";

//            if(empty($product_barcode)) {
			$productBarcodeList = $this->helperUpdateDefactoSkuIdUpdate3($skuID);
//                $product_barcode = Stock::find()->select('product_barcode')->andWhere(['field_extra1'=>$skuID])->scalar();
//            }

			foreach ($productBarcodeList as $productBarcode) {
				$reportFrom[] = [
					'product_barcode' => $productBarcode,
					'sku_id' => $skuID,
					'countOnStock' => Stock::find()
										   ->andWhere(
											   [
												   'product_barcode' => $productBarcode,
												   'client_id' => 2,
												   'status_availability' => Stock::STATUS_AVAILABILITY_YES
											   ])->count(),
				];
			}
		}

		$str = '';
		foreach ($reportFrom as $product) {
			$str .= $product['sku_id'] . ';' . $product['product_barcode'] . ';' . $product['countOnStock'] . ';' . "\n";
		}


		file_put_contents('find-on-stock.csv', $str, FILE_APPEND);
		VarDumper::dump($reportFrom, 10, true);
//        VarDumper::dump(count($reportFrom),10,true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
//        VarDumper::dump($reportFrom,10,true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
//        VarDumper::dump(count($result),10,true);
//        VarDumper::dump($result,10,true);
		echo "<br />";

	}

	/*
 * */
	protected function helperUpdateDefactoSkuIdUpdate()
	{
		$lotsOnStock = Stock::find()
							->select('product_barcode')
							->andWhere([
								'client_id' => Client::CLIENT_DEFACTO,
//                'status_availability' => Stock::STATUS_AVAILABILITY_YES,
							])
							->andWhere("field_extra1 = ''")
							->groupBy('product_barcode')
							->asArray()
							->all();
		$x = [];
		foreach ($lotsOnStock as $lot) {

			$barcode = $lot['product_barcode'];

			if (!isset($x[$barcode])) {
				$skqId = $this->getAPISkuIdFromDefacto2($lot['product_barcode']);

				$x[$barcode] = $skqId;
				Stock::updateAll(['field_extra1' => $skqId],
					[
						'client_id' => Client::CLIENT_DEFACTO,
						'product_barcode' => $lot['product_barcode'],
					]
				);
			}
		}
	}

	protected function getAPISkuIdFromDefacto2($LotOrSingleBarcode)
	{
		if (!empty($LotOrSingleBarcode)) {

			$api = new DeFactoSoapAPIV2();
			$params['request'] = [
				'BusinessUnitId' => '1029',
				'PageSize' => 0,
				'PageIndex' => 0,
				'CountAllItems' => false,
				'ProcessRequestedDataType' => 'Full',
//                'SkuId' => $SkuId,
				'LotOrSingleBarcode' => $LotOrSingleBarcode,
			];
//
			$result = $api->sendRequest('GetMasterData', $params);
			if ($resultDataArray = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.Data.MasterDataThreePL')) {
				$resultDataArray = count($resultDataArray) <= 1 ? [$resultDataArray] : $resultDataArray;
			} else {
				$resultDataArray = [];
			}

			foreach ($resultDataArray as $value) {
				return $value->SkuId;
			}
		}

		return '';
	}

	protected function getAPISkuIdFromDefacto3($SkuId)
	{
		if (!empty($SkuId)) {

			$api = new DeFactoSoapAPIV2();
			$params['request'] = [
				'BusinessUnitId' => '1029',
				'PageSize' => 0,
				'PageIndex' => 0,
				'CountAllItems' => false,
				'ProcessRequestedDataType' => 'Full',
				'SkuId' => $SkuId,
//                'LotOrSingleBarcode' => $LotOrSingleBarcode,
			];
//
			$result = $api->sendRequest('GetMasterData', $params);
			if ($resultDataArray = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.Data.MasterDataThreePL')) {
				$resultDataArray = count($resultDataArray) <= 1 ? [$resultDataArray] : $resultDataArray;
			} else {
				$resultDataArray = [];
			}

			$result = [];

			foreach ($resultDataArray as $key => $value) {
				$result[] = $value->LotOrSingleBarcode;
//                if($key) {
//                    return $value->LotOrSingleBarcode;
//                }
//                return 0;
			}
		}

		return $result;
	}

	protected function helperUpdateDefactoSkuIdUpdate3($SkuId)
	{
		$product_barcodeList = $this->getAPISkuIdFromDefacto3($SkuId);

		return $product_barcodeList;

		Stock::updateAll(['field_extra1' => $SkuId],
			[
				'client_id' => Client::CLIENT_DEFACTO,
				'product_barcode' => $product_barcode,
			]
		);
	}

	public function actionAllPartReservedOrder()
	{
		//        die('other/one/all-part-reserved-order');
		//  other/one/all-part-reserved-order

		$outboundIdList = OutboundOrder::find()
									   ->select('id')
									   ->andWhere(['client_id' => 2])
									   ->andWhere(['between', 'outbound_orders.created_at', 1543622400, 1575590399])
									   ->andWhere(' expected_qty != accepted_qty');
//                    ->all();

		$outboundIdListMAP = OutboundOrder::find()
										  ->select('id,  order_number,parent_order_number')
										  ->andWhere(['client_id' => 2])
										  ->andWhere(['between', 'outbound_orders.created_at', 1543622400, 1575590399])
										  ->andWhere('expected_qty != accepted_qty')
										  ->all();

		$map = ArrayHelper::map($outboundIdListMAP, 'id', function ($value) {
			$to = new \stdClass();
			$to->orderNumber = $value->order_number;
			$to->parentOrderNumber = $value->parent_order_number;
			return $to;
		});


//        VarDumper::dump($str,10,true)
		$outboundOrderItemList = OutboundOrderItem::find()
												  ->andWhere(['outbound_order_id' => $outboundIdList])
												  ->andWhere(' expected_qty != accepted_qty')
												  ->all();

		$str = '';
		foreach ($outboundOrderItemList as $item) {
			$str .= $map[$item->outbound_order_id]->parentOrderNumber . ';' . $map[$item->outbound_order_id]->orderNumber . ';' . $item->product_barcode . ';' . "\n";
		}

		file_put_contents('AllPartReservedOrder.csv', $str);

		VarDumper::dump($str, 10, true);
	}

	public function actionValidateOrdersM3InTtn()
	{
		// other/one/validate-orders-m3-in-ttn

		$dpList = TlDeliveryProposal::find()
									->select('id')
									->andWhere(['client_id' => 2])
									->andWhere(['between', 'shipped_datetime', 1575158400, 1577836799])
									->all();
		$i = 0;
		echo 'Порядковый номер' . ' -  ' . 'М3' . ' - ' . 'ID Заявки на доставки' . ' - ' . 'ID заказа' . ' - ' . '.номер заказа' . "<br />";
		foreach ($dpList as $dp) {
			$dpOrderList = TlDeliveryProposalOrders::find()->andWhere(['tl_delivery_proposal_id' => $dp->id])->all();
			foreach ($dpOrderList as $dpOrder) {
				if (floatval($dpOrder->mc) < 0.001) {
					echo ++$i . ' -  ' . $dpOrder->mc . ' - ' . $dp->id . ' - ' . $dpOrder->order_id . ' - ' . $dpOrder->order_number . "<br />";
				}
			}
		}

		return 'Y';
	}

	public function actionResendOutboundOrder()
	{ // /other/one/resend-outbound-order
		die("actionResendOutboundOrder - DIE");

		$orderId = '68903';
		$outboundOrder = OutboundOrder::findOne($orderId);
		if (empty($outboundOrder)) {
			Yii::$app->session->setFlash('danger', 'Нет номера накладной');
			return $this->redirect('/outbound/report/index');

		}
		$strToUnSerialize = $outboundOrder->api_send_data;
		if (empty($strToUnSerialize)) {
			Yii::$app->session->setFlash('danger', 'Нет данных для повторной отправки');
			return $this->redirect('/outbound/report/index');
		}

		$dataForToResendOutboundOrderAPI = unserialize($strToUnSerialize);

//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($dataForToResendOutboundOrderAPI, 10, true);
//        die;
		$LcBox = [
			// 34470633	103512	Актау / Defacto 103512 [ ТЦ KAZ AKTAU AKTAU MALL NEW ] - - OK
			// 34470633	105482	Алматы / Defacto 105482 [ ТЦ ALMATY APORT EAST MALL ] - - OK
			// WaybillNumber 1
//			2430053315603=>2430053412232,
//			2430053315604=>2430053412233,
//			2430053315606=>2430053412234,
//			2430053315607=>2430053412235,
//			2430053315609=>2430053412236,
//			2430053315610=>2430053412237,
//			2430053315605=>2430053412238,
			// 34470633	836	Атырау / Defacto 2470 [ ТЦ ATYRAU ATYRAU ] проспект Сатпаева 17 «А» --OK
			// WaybillNumber 2
//			2430053315771=>2430053412239,
//			2430053315772=>2430053412240,
//			2430053315773=>2430053412241,
//			2430053315774=>2430053412242,
//			2430053315775=>2430053412243,
//			2430053315776=>2430053412244,
//			2430053315777=>2430053412245,
//			2430053315778=>2430053412246,
//			2430053315779=>2430053412247,
			// 34470633	969	Жанаозен / Defacto 2702 [ ТЦ ZHANAOZEN ZHANAOZEN ] 5-й мкр. дом.1 -- ok
			// 34470633	103512	Актау / Defacto 103512 [ ТЦ KAZ AKTAU AKTAU MALL NEW ] - - ???
			// DeFacto	34470633	348	Алматы / Defacto 1909 [ ТЦ ALMATY MART ] ул. Рихарда Зорге 18 ok
			// 34470633	2190	Нурсултан / Defacto 2190 [ ТЦ ASTANA MEGA SILK WAY ] KZK ASTANA MEGA SILK WAY MALL 0 -- ok
			// 34470633	2482	Атырау / Defacto 2952 [ ТЦ ATYRAU KULMANOV ] Кулманова 144 - ok

		];

		$OutBoundIdList = [
//            89117851,
		];

		$productBarcodeList = [
			'8683524026590',
		];

		foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key => $item) {
			$lcBoxBarcode = ArrayHelper::getValue($item, 'LotOrSingleBarcode');
			if (in_array($lcBoxBarcode, $productBarcodeList)) {
				unset($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]);
			}
		}

//        foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key => $item) {
//            $LotOrSingleBarcode = ArrayHelper::getValue($item, 'LotOrSingleBarcode');
//            $lcBoxBarcode = ArrayHelper::getValue($item, 'LcBarcode');
//            if (!array_key_exists($lcBoxBarcode, $LcBox)) {
//                unset($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]);
//                continue;
//            }
//        }

//        foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key => $item) {
//            $OutBoundId = ArrayHelper::getValue($item, 'OutBoundId');
//            if (!in_array($OutBoundId, $OutBoundIdList)) {
//                unset($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]);
//                continue;
//            }
//        }

//        foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key => $item) {
//            $lcBoxBarcode = ArrayHelper::getValue($item, 'LcBarcode');
//            if (array_key_exists($lcBoxBarcode, $LcBox)) {
//                $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['LcBarcode'] = $LcBox[$lcBoxBarcode];
//            }
//        }
//
//        foreach($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key=>$item) {
//            $WaybillNumber = $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'];
//            $WaybillSerial = $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillSerial'];
//            $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'] = '2'.$WaybillNumber;
////            $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillSerial'] = 'A'.$WaybillSerial;
//        }

		sort($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse']);

		$res = [];
		$api = new DeFactoSoapAPIV2Manager();
//        $res = $api->SendOutBoundFeedBackData($dataForToResendOutboundOrderAPI);

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//        VarDumper::dump($one, 10, true);
		echo "<br />";
		echo "<br />";
		VarDumper::dump($dataForToResendOutboundOrderAPI, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($res, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		die;

		return $this->render('index');
	}


	public function actionResendOutboundOrderOne()
	{ // /other/one/resend-outbound-order-one
		die("actionResendOutboundOrderOne - DIE");

		$row[] = [
			'OutBoundId' => 89117859,
			'InBoundId' => '',
			'LcBarcode' => 2430013999701,
			'LotOrSingleBarcode' => 2300017655742,
			'LotOrSingleQuantity' => 1,
			'WaybillSerial' => 'AKZK',
			'WaybillNumber' => 1205661034808,
			'Volume' => 38,
			'CargoShipmentNo' => 1,
			'InvoiceNumber' => 'KZ-21-TR-003',
		];

		$row[] = [
			'OutBoundId' => 89117851,
			'InBoundId' => '',
			'LcBarcode' => 2430013999702,
			'LotOrSingleBarcode' => 2300013825897,
			'LotOrSingleQuantity' => 1,
			'WaybillSerial' => 'AKZK',
			'WaybillNumber' => 1205661034801,
			'Volume' => 32,
			'CargoShipmentNo' => 1,
			'InvoiceNumber' => 'D10AA00220002',
		];

		$row[] = [
			'OutBoundId' => 89117734,
			'InBoundId' => '',
			'LcBarcode' => 2430013999703,
			'LotOrSingleBarcode' => 2300018743615,
			'LotOrSingleQuantity' => 1,
			'WaybillSerial' => 'AKZK',
			'WaybillNumber' => 1205661034805,
			'Volume' => 32,
			'CargoShipmentNo' => 1,
			'InvoiceNumber' => 'KZ-20-TR-066',
		];

		$row[] = [
			'OutBoundId' => 89117795,
			'InBoundId' => '',
			'LcBarcode' => 2430013999704,
			'LotOrSingleBarcode' => 2300020266324,
			'LotOrSingleQuantity' => 1,
			'WaybillSerial' => 'AKZK',
			'WaybillNumber' => 1205661034803,
			'Volume' => 32,
			'CargoShipmentNo' => 1,
			'InvoiceNumber' => 'KZ-21-019-RW',
		];

		$data['OutBoundFeedBackThreePLResponse'] = $row;
		$res = [];
		$api = new DeFactoSoapAPIV2Manager();
//        $res = $api->SendOutBoundFeedBackData($data);

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($data, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($res, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		die;

		return $this->render('index');
	}


	public function actionResendPartCloseOutboundOrderFromFile()
	{ // /other/one/resend-part-close-outbound-order-from-file
		die("actionResendOutboundOrderOne - DIE");

		$strToUnSerialize = 'a:1:{s:31:"OutBoundFeedBackThreePLResponse";a:722:{i:0;a:10:{s:10:"OutBoundId";i:108203414;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300026140680";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:1;a:10:{s:10:"OutBoundId";i:108203415;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300026252833";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:2;a:10:{s:10:"OutBoundId";i:108203416;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300016390781";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:3;a:10:{s:10:"OutBoundId";i:108203417;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300027496465";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:4;a:10:{s:10:"OutBoundId";i:108203418;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300027296942";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:5;a:10:{s:10:"OutBoundId";i:108203419;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300022305700";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:6;a:10:{s:10:"OutBoundId";i:108203420;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300027131977";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:7;a:10:{s:10:"OutBoundId";i:108203421;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300022276543";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:8;a:10:{s:10:"OutBoundId";i:108203422;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026271483";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:9;a:10:{s:10:"OutBoundId";i:108203423;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300028386246";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:10;a:10:{s:10:"OutBoundId";i:108203424;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300025682822";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:11;a:10:{s:10:"OutBoundId";i:108203425;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300026050811";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:12;a:10:{s:10:"OutBoundId";i:108203426;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300028990559";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:13;a:10:{s:10:"OutBoundId";i:108203427;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026789735";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:14;a:10:{s:10:"OutBoundId";i:108203428;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300028824465";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:15;a:10:{s:10:"OutBoundId";i:108203429;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300028804276";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:16;a:10:{s:10:"OutBoundId";i:108203430;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300028214440";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:17;a:10:{s:10:"OutBoundId";i:108203431;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300023055277";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:18;a:10:{s:10:"OutBoundId";i:108203432;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300025259680";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:19;a:10:{s:10:"OutBoundId";i:108203433;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300028217304";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:20;a:10:{s:10:"OutBoundId";i:108203434;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300022909427";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:21;a:10:{s:10:"OutBoundId";i:108203435;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300026058060";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:22;a:10:{s:10:"OutBoundId";i:108203436;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300024058598";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:23;a:10:{s:10:"OutBoundId";i:108203437;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300022309036";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:24;a:10:{s:10:"OutBoundId";i:108203438;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300022283183";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:25;a:10:{s:10:"OutBoundId";i:108203439;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026250426";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:26;a:10:{s:10:"OutBoundId";i:108203440;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026847176";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:27;a:10:{s:10:"OutBoundId";i:108203441;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026724798";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:28;a:10:{s:10:"OutBoundId";i:108203442;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026595510";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:29;a:10:{s:10:"OutBoundId";i:108203443;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300023839983";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:30;a:10:{s:10:"OutBoundId";i:108203444;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026093849";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:31;a:10:{s:10:"OutBoundId";i:108203445;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300025682723";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:32;a:10:{s:10:"OutBoundId";i:108203446;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300023440561";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:33;a:10:{s:10:"OutBoundId";i:108203447;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300026719183";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:34;a:10:{s:10:"OutBoundId";i:108203448;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300028214273";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:35;a:10:{s:10:"OutBoundId";i:108203449;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300026336175";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:36;a:10:{s:10:"OutBoundId";i:108203450;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300026857311";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:37;a:10:{s:10:"OutBoundId";i:108203451;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300023055697";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:38;a:10:{s:10:"OutBoundId";i:108203452;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300026331552";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:39;a:10:{s:10:"OutBoundId";i:108203453;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300022000544";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:40;a:10:{s:10:"OutBoundId";i:108203454;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026808962";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:41;a:10:{s:10:"OutBoundId";i:108203455;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026097120";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:42;a:10:{s:10:"OutBoundId";i:108203456;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026324882";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:43;a:10:{s:10:"OutBoundId";i:108203457;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026250464";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:44;a:10:{s:10:"OutBoundId";i:108203458;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300028234448";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:45;a:10:{s:10:"OutBoundId";i:108203459;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300026749142";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:46;a:10:{s:10:"OutBoundId";i:108203460;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300026180280";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:47;a:10:{s:10:"OutBoundId";i:108203461;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300028237487";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:48;a:10:{s:10:"OutBoundId";i:108203462;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300025682853";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:49;a:10:{s:10:"OutBoundId";i:108203463;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300026833568";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:50;a:10:{s:10:"OutBoundId";i:108203464;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300026275504";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:51;a:10:{s:10:"OutBoundId";i:108203465;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300028508778";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:52;a:10:{s:10:"OutBoundId";i:108203466;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300026555583";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:53;a:10:{s:10:"OutBoundId";i:108203467;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300022615601";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:54;a:10:{s:10:"OutBoundId";i:108203468;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300024219722";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:55;a:10:{s:10:"OutBoundId";i:108203469;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300028508167";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:56;a:10:{s:10:"OutBoundId";i:108203470;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300028508143";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:57;a:10:{s:10:"OutBoundId";i:108203471;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300016305297";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:58;a:10:{s:10:"OutBoundId";i:108203472;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300027121794";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:59;a:10:{s:10:"OutBoundId";i:108203473;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026316986";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:60;a:10:{s:10:"OutBoundId";i:108203474;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300028369973";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:61;a:10:{s:10:"OutBoundId";i:108203475;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300027498285";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:62;a:10:{s:10:"OutBoundId";i:108203476;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026097847";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:63;a:10:{s:10:"OutBoundId";i:108203477;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026453742";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:64;a:10:{s:10:"OutBoundId";i:108203478;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300027757375";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:65;a:10:{s:10:"OutBoundId";i:108203479;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300028214655";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:66;a:10:{s:10:"OutBoundId";i:108203480;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300028217182";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:67;a:10:{s:10:"OutBoundId";i:108203481;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026719411";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:68;a:10:{s:10:"OutBoundId";i:108203482;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300027449942";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:69;a:10:{s:10:"OutBoundId";i:108203483;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300026250655";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:70;a:10:{s:10:"OutBoundId";i:108203484;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300026336144";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:71;a:10:{s:10:"OutBoundId";i:108203485;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300026210529";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:72;a:10:{s:10:"OutBoundId";i:108203486;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300026260821";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:73;a:10:{s:10:"OutBoundId";i:108203487;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300028995998";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:74;a:10:{s:10:"OutBoundId";i:108203488;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300023025546";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:75;a:10:{s:10:"OutBoundId";i:108203489;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300016390743";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:76;a:10:{s:10:"OutBoundId";i:108203490;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300017404821";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:77;a:10:{s:10:"OutBoundId";i:108203491;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300025763996";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:78;a:10:{s:10:"OutBoundId";i:108203492;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300025079660";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:79;a:10:{s:10:"OutBoundId";i:108203493;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300021732934";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:80;a:10:{s:10:"OutBoundId";i:108203494;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300026863961";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:81;a:10:{s:10:"OutBoundId";i:108203495;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300026254103";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:82;a:10:{s:10:"OutBoundId";i:108203496;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300027413516";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:83;a:10:{s:10:"OutBoundId";i:108203497;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300016401265";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:84;a:10:{s:10:"OutBoundId";i:108203498;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300026590959";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:85;a:10:{s:10:"OutBoundId";i:108203499;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300023447669";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:86;a:10:{s:10:"OutBoundId";i:108203500;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300025615288";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:87;a:10:{s:10:"OutBoundId";i:108203501;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300027502296";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:88;a:10:{s:10:"OutBoundId";i:108203502;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300023436977";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:89;a:10:{s:10:"OutBoundId";i:108203503;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300027470809";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:90;a:10:{s:10:"OutBoundId";i:108203504;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300023111300";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:91;a:10:{s:10:"OutBoundId";i:108203505;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300025768397";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:92;a:10:{s:10:"OutBoundId";i:108203506;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300028219018";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:93;a:10:{s:10:"OutBoundId";i:108203507;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300024630527";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:94;a:10:{s:10:"OutBoundId";i:108203508;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300028206070";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:95;a:10:{s:10:"OutBoundId";i:108203509;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300025098289";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:96;a:10:{s:10:"OutBoundId";i:108203510;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300028218936";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:97;a:10:{s:10:"OutBoundId";i:108203511;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300025602592";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:98;a:10:{s:10:"OutBoundId";i:108203512;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300025414287";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:99;a:10:{s:10:"OutBoundId";i:108203513;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300025049410";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:100;a:10:{s:10:"OutBoundId";i:108203514;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300026082850";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:101;a:10:{s:10:"OutBoundId";i:108203515;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026453728";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:102;a:10:{s:10:"OutBoundId";i:108203516;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300025601236";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:103;a:10:{s:10:"OutBoundId";i:108203517;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300025252773";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:104;a:10:{s:10:"OutBoundId";i:108203518;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026338803";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:105;a:10:{s:10:"OutBoundId";i:108203519;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300022883079";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:106;a:10:{s:10:"OutBoundId";i:108203520;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300026831014";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:107;a:10:{s:10:"OutBoundId";i:108203521;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300025685823";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:108;a:10:{s:10:"OutBoundId";i:108203522;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300024377347";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:109;a:10:{s:10:"OutBoundId";i:108203523;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300022936348";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:110;a:10:{s:10:"OutBoundId";i:108203524;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300022540651";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:111;a:10:{s:10:"OutBoundId";i:108203525;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300028571918";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:112;a:10:{s:10:"OutBoundId";i:108203526;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300027373995";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:113;a:10:{s:10:"OutBoundId";i:108203527;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300028434336";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:114;a:10:{s:10:"OutBoundId";i:108203528;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300026059210";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:115;a:10:{s:10:"OutBoundId";i:108203529;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300026318072";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:116;a:10:{s:10:"OutBoundId";i:108203530;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300025143071";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:117;a:10:{s:10:"OutBoundId";i:108203531;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300025603049";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:118;a:10:{s:10:"OutBoundId";i:108203532;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300026500354";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:119;a:10:{s:10:"OutBoundId";i:108203533;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300019485040";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:120;a:10:{s:10:"OutBoundId";i:108203534;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300025385051";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:121;a:10:{s:10:"OutBoundId";i:108203535;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300025601465";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:122;a:10:{s:10:"OutBoundId";i:108203536;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300025581668";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:123;a:10:{s:10:"OutBoundId";i:108203537;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300022509450";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:124;a:10:{s:10:"OutBoundId";i:108203538;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300023409544";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:125;a:10:{s:10:"OutBoundId";i:108203539;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300023440097";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:126;a:10:{s:10:"OutBoundId";i:108203540;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300023440110";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:127;a:10:{s:10:"OutBoundId";i:108203541;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300026859827";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:128;a:10:{s:10:"OutBoundId";i:108203542;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300027110354";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:129;a:10:{s:10:"OutBoundId";i:108203543;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300023306270";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:130;a:10:{s:10:"OutBoundId";i:108203544;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026328125";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:131;a:10:{s:10:"OutBoundId";i:108203545;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300019506981";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:132;a:10:{s:10:"OutBoundId";i:108203546;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300017018387";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:133;a:10:{s:10:"OutBoundId";i:108203547;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300023409476";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:134;a:10:{s:10:"OutBoundId";i:108203548;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300022293649";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:135;a:10:{s:10:"OutBoundId";i:108203549;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300025372761";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:136;a:10:{s:10:"OutBoundId";i:108203550;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026334560";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:137;a:10:{s:10:"OutBoundId";i:108203551;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300023010443";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:138;a:10:{s:10:"OutBoundId";i:108203552;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300023055307";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:139;a:10:{s:10:"OutBoundId";i:108203553;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300028338436";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:140;a:10:{s:10:"OutBoundId";i:108203554;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300025601434";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:141;a:10:{s:10:"OutBoundId";i:108203555;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300025364797";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:142;a:10:{s:10:"OutBoundId";i:108203556;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026140550";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:143;a:10:{s:10:"OutBoundId";i:108203557;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300028336678";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:144;a:10:{s:10:"OutBoundId";i:108203558;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300025600291";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:145;a:10:{s:10:"OutBoundId";i:108203559;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300023286831";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:146;a:10:{s:10:"OutBoundId";i:108203560;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300024825299";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:147;a:10:{s:10:"OutBoundId";i:108203561;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300025452531";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:148;a:10:{s:10:"OutBoundId";i:108203562;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300025702681";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:149;a:10:{s:10:"OutBoundId";i:108203563;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300026596241";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:150;a:10:{s:10:"OutBoundId";i:108203564;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300026596623";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:151;a:10:{s:10:"OutBoundId";i:108203565;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300022539921";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:152;a:10:{s:10:"OutBoundId";i:108203566;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300025965345";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:153;a:10:{s:10:"OutBoundId";i:108203567;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300028912094";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:154;a:10:{s:10:"OutBoundId";i:108203568;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026868119";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:155;a:10:{s:10:"OutBoundId";i:108203569;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300025574134";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:156;a:10:{s:10:"OutBoundId";i:108203570;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026542750";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:157;a:10:{s:10:"OutBoundId";i:108203571;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300022536548";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:158;a:10:{s:10:"OutBoundId";i:108203572;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026998854";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:159;a:10:{s:10:"OutBoundId";i:108203573;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300025414997";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:160;a:10:{s:10:"OutBoundId";i:108203574;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300023096379";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:161;a:10:{s:10:"OutBoundId";i:108203575;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300025061528";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:162;a:10:{s:10:"OutBoundId";i:108203576;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300026495124";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:163;a:10:{s:10:"OutBoundId";i:108203577;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300026492376";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:164;a:10:{s:10:"OutBoundId";i:108203578;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300025660233";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:165;a:10:{s:10:"OutBoundId";i:108203579;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300018987224";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";i:32;s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:166;a:10:{s:10:"OutBoundId";i:108203580;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300025764153";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:167;a:10:{s:10:"OutBoundId";i:108203581;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300025764092";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:168;a:10:{s:10:"OutBoundId";i:108203582;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300025879901";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:169;a:10:{s:10:"OutBoundId";i:108203583;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026749395";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:170;a:10:{s:10:"OutBoundId";i:108203584;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026051337";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:171;a:10:{s:10:"OutBoundId";i:108203585;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300026718872";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:172;a:10:{s:10:"OutBoundId";i:108203586;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300028993147";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:173;a:10:{s:10:"OutBoundId";i:108203587;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300025364681";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:174;a:10:{s:10:"OutBoundId";i:108203588;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300027082422";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:175;a:10:{s:10:"OutBoundId";i:108203589;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300025602387";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:176;a:10:{s:10:"OutBoundId";i:108203590;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026179598";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:177;a:10:{s:10:"OutBoundId";i:108203591;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300028499649";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:178;a:10:{s:10:"OutBoundId";i:108203592;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300028499205";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:179;a:10:{s:10:"OutBoundId";i:108203593;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300022992375";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:180;a:10:{s:10:"OutBoundId";i:108203594;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300026452929";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:181;a:10:{s:10:"OutBoundId";i:108203595;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026720714";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:182;a:10:{s:10:"OutBoundId";i:108203596;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300026214381";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:183;a:10:{s:10:"OutBoundId";i:108203597;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026180969";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:184;a:10:{s:10:"OutBoundId";i:108203598;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300024542356";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:185;a:10:{s:10:"OutBoundId";i:108203599;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300024582772";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:186;a:10:{s:10:"OutBoundId";i:108203600;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300023121330";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:187;a:10:{s:10:"OutBoundId";i:108203601;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300022539808";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:188;a:10:{s:10:"OutBoundId";i:108203602;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300026588567";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:189;a:10:{s:10:"OutBoundId";i:108203603;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300026183298";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:190;a:10:{s:10:"OutBoundId";i:108203604;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300028500130";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:191;a:10:{s:10:"OutBoundId";i:108203605;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300023492232";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:192;a:10:{s:10:"OutBoundId";i:108203606;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300028328697";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:193;a:10:{s:10:"OutBoundId";i:108203607;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026847848";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:194;a:10:{s:10:"OutBoundId";i:108203608;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026833773";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:195;a:10:{s:10:"OutBoundId";i:108203609;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300027159803";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:196;a:10:{s:10:"OutBoundId";i:108203610;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300026260463";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:197;a:10:{s:10:"OutBoundId";i:108203611;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026456279";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:198;a:10:{s:10:"OutBoundId";i:108203612;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026254714";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:199;a:10:{s:10:"OutBoundId";i:108203613;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300027086468";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:200;a:10:{s:10:"OutBoundId";i:108203614;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300023930581";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:201;a:10:{s:10:"OutBoundId";i:108203615;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300017513455";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:202;a:10:{s:10:"OutBoundId";i:108203616;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300027012344";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:203;a:10:{s:10:"OutBoundId";i:108203617;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300022160811";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:204;a:10:{s:10:"OutBoundId";i:108203618;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300023005791";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:205;a:10:{s:10:"OutBoundId";i:108203619;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300023476904";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:206;a:10:{s:10:"OutBoundId";i:108203620;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300025965369";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:207;a:10:{s:10:"OutBoundId";i:108203621;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300021993748";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:208;a:10:{s:10:"OutBoundId";i:108203622;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300026718643";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:209;a:10:{s:10:"OutBoundId";i:108203623;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026749104";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:210;a:10:{s:10:"OutBoundId";i:108203624;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300028216017";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:211;a:10:{s:10:"OutBoundId";i:108203625;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026158579";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:212;a:10:{s:10:"OutBoundId";i:108203626;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026318638";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:213;a:10:{s:10:"OutBoundId";i:108203627;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300021948496";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:214;a:10:{s:10:"OutBoundId";i:108203628;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300026313534";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:215;a:10:{s:10:"OutBoundId";i:108203629;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300026104699";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:216;a:10:{s:10:"OutBoundId";i:108203630;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026707081";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:217;a:10:{s:10:"OutBoundId";i:108203631;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300018510811";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:218;a:10:{s:10:"OutBoundId";i:108203632;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300025257105";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:219;a:10:{s:10:"OutBoundId";i:108203633;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300026464335";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:220;a:10:{s:10:"OutBoundId";i:108203634;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300025672038";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:221;a:10:{s:10:"OutBoundId";i:108203635;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300025444567";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:222;a:10:{s:10:"OutBoundId";i:108203636;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026857434";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:223;a:10:{s:10:"OutBoundId";i:108203637;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026868638";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:224;a:10:{s:10:"OutBoundId";i:108203638;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300028887613";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:225;a:10:{s:10:"OutBoundId";i:108203639;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300026151068";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:226;a:10:{s:10:"OutBoundId";i:108203640;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300018865898";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:227;a:10:{s:10:"OutBoundId";i:108203641;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300026249925";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:228;a:10:{s:10:"OutBoundId";i:108203642;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300025598956";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:229;a:10:{s:10:"OutBoundId";i:108203643;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300026254356";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:230;a:10:{s:10:"OutBoundId";i:108203644;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300019484753";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:231;a:10:{s:10:"OutBoundId";i:108203645;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300016884242";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:232;a:10:{s:10:"OutBoundId";i:108203646;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300025600727";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:233;a:10:{s:10:"OutBoundId";i:108203647;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300025601748";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:234;a:10:{s:10:"OutBoundId";i:108203648;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300023474290";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:235;a:10:{s:10:"OutBoundId";i:108203649;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300017307214";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:236;a:10:{s:10:"OutBoundId";i:108203650;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300028336937";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:237;a:10:{s:10:"OutBoundId";i:108203651;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300025367385";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:238;a:10:{s:10:"OutBoundId";i:108203652;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300028222728";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:239;a:10:{s:10:"OutBoundId";i:108203653;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026454466";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:240;a:10:{s:10:"OutBoundId";i:108203654;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300027498292";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:241;a:10:{s:10:"OutBoundId";i:108203655;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300025359243";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:242;a:10:{s:10:"OutBoundId";i:108203656;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300028885954";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:243;a:10:{s:10:"OutBoundId";i:108203657;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026233535";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:244;a:10:{s:10:"OutBoundId";i:108203658;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300023103305";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:245;a:10:{s:10:"OutBoundId";i:108203659;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300027471165";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:246;a:10:{s:10:"OutBoundId";i:108203660;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300026851715";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:247;a:10:{s:10:"OutBoundId";i:108203661;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300023490719";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:248;a:10:{s:10:"OutBoundId";i:108203662;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300023438940";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:249;a:10:{s:10:"OutBoundId";i:108203663;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300018722504";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:250;a:10:{s:10:"OutBoundId";i:108203664;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300026856550";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:251;a:10:{s:10:"OutBoundId";i:108203665;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300023129152";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:252;a:10:{s:10:"OutBoundId";i:108203666;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300028453191";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:253;a:10:{s:10:"OutBoundId";i:108203667;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300022988279";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:254;a:10:{s:10:"OutBoundId";i:108203668;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300026105061";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:255;a:10:{s:10:"OutBoundId";i:108203669;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300025252070";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:256;a:10:{s:10:"OutBoundId";i:108203670;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300023438025";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:257;a:10:{s:10:"OutBoundId";i:108203671;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026157602";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:258;a:10:{s:10:"OutBoundId";i:108203672;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300023306089";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:259;a:10:{s:10:"OutBoundId";i:108203673;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300021909121";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:260;a:10:{s:10:"OutBoundId";i:108203674;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300023129107";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:261;a:10:{s:10:"OutBoundId";i:108203675;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300027207245";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:262;a:10:{s:10:"OutBoundId";i:108203676;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300026259801";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:263;a:10:{s:10:"OutBoundId";i:108203677;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026388532";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:264;a:10:{s:10:"OutBoundId";i:108203678;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300024748468";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:265;a:10:{s:10:"OutBoundId";i:108203679;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300028298754";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:266;a:10:{s:10:"OutBoundId";i:108203680;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026595381";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:267;a:10:{s:10:"OutBoundId";i:108203681;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026455890";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:268;a:10:{s:10:"OutBoundId";i:108203682;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300027131953";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:269;a:10:{s:10:"OutBoundId";i:108203683;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300022278608";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:270;a:10:{s:10:"OutBoundId";i:108203684;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300027303756";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:271;a:10:{s:10:"OutBoundId";i:108203685;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300021624406";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:272;a:10:{s:10:"OutBoundId";i:108203686;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300021944238";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:273;a:10:{s:10:"OutBoundId";i:108203687;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300026327760";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:274;a:10:{s:10:"OutBoundId";i:108203688;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300023442923";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:275;a:10:{s:10:"OutBoundId";i:108203689;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026716151";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:276;a:10:{s:10:"OutBoundId";i:108203690;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300022306066";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:277;a:10:{s:10:"OutBoundId";i:108203691;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300022282223";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:278;a:10:{s:10:"OutBoundId";i:108203692;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026151181";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:279;a:10:{s:10:"OutBoundId";i:108203693;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026633595";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:280;a:10:{s:10:"OutBoundId";i:108203694;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300025267906";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:281;a:10:{s:10:"OutBoundId";i:108203695;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300024536737";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:282;a:10:{s:10:"OutBoundId";i:108203696;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300027161318";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:283;a:10:{s:10:"OutBoundId";i:108203697;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300028230280";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:284;a:10:{s:10:"OutBoundId";i:108203698;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300016922487";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:285;a:10:{s:10:"OutBoundId";i:108203699;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300027615309";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:286;a:10:{s:10:"OutBoundId";i:108203700;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026259238";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:287;a:10:{s:10:"OutBoundId";i:108203701;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300028229826";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:288;a:10:{s:10:"OutBoundId";i:108203702;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725788";s:18:"LotOrSingleBarcode";s:13:"2300038111821";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104732";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-TR-020";}i:289;a:10:{s:10:"OutBoundId";i:108203703;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043726154";s:18:"LotOrSingleBarcode";s:13:"2300037118685";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104733";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-TR-020";}i:290;a:10:{s:10:"OutBoundId";i:108203704;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708651";s:18:"LotOrSingleBarcode";s:13:"2300036006273";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104734";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-146-RW";}i:291;a:10:{s:10:"OutBoundId";i:108203705;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026254387";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:292;a:10:{s:10:"OutBoundId";i:108203706;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026043899";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:293;a:10:{s:10:"OutBoundId";i:108203707;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300022152700";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:294;a:10:{s:10:"OutBoundId";i:108203708;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300018496009";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:295;a:10:{s:10:"OutBoundId";i:108203709;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300028805211";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:296;a:10:{s:10:"OutBoundId";i:108203710;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026252222";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:297;a:10:{s:10:"OutBoundId";i:108203711;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026848517";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:298;a:10:{s:10:"OutBoundId";i:108203712;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300023001038";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:299;a:10:{s:10:"OutBoundId";i:108203713;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300026210116";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:300;a:10:{s:10:"OutBoundId";i:108203714;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026309988";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:301;a:10:{s:10:"OutBoundId";i:108203715;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026253359";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:302;a:10:{s:10:"OutBoundId";i:108203716;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026754269";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:303;a:10:{s:10:"OutBoundId";i:108203717;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300026103661";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:304;a:10:{s:10:"OutBoundId";i:108203718;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026495162";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:305;a:10:{s:10:"OutBoundId";i:108203719;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026254196";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:306;a:10:{s:10:"OutBoundId";i:108203720;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026391525";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:307;a:10:{s:10:"OutBoundId";i:108203721;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300026711620";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:308;a:10:{s:10:"OutBoundId";i:108203722;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300025486192";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:309;a:10:{s:10:"OutBoundId";i:108203723;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026254615";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:310;a:10:{s:10:"OutBoundId";i:108203724;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300026253021";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:311;a:10:{s:10:"OutBoundId";i:108203725;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300022532298";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:312;a:10:{s:10:"OutBoundId";i:108203726;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300022307858";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:313;a:10:{s:10:"OutBoundId";i:108203727;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300021731913";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:314;a:10:{s:10:"OutBoundId";i:108203728;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300025457697";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:315;a:10:{s:10:"OutBoundId";i:108203729;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300026272213";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:316;a:10:{s:10:"OutBoundId";i:108203730;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300021627223";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:317;a:10:{s:10:"OutBoundId";i:108203731;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300027030072";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:318;a:10:{s:10:"OutBoundId";i:108203732;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026097663";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:319;a:10:{s:10:"OutBoundId";i:108203733;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300026854563";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:320;a:10:{s:10:"OutBoundId";i:108203734;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300027161417";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:321;a:10:{s:10:"OutBoundId";i:108203735;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026269312";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:322;a:10:{s:10:"OutBoundId";i:108203736;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300016922586";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:323;a:10:{s:10:"OutBoundId";i:108203737;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300026273128";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:324;a:10:{s:10:"OutBoundId";i:108203738;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300022308084";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:325;a:10:{s:10:"OutBoundId";i:108203739;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300026386576";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:326;a:10:{s:10:"OutBoundId";i:108203740;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300022988309";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:327;a:10:{s:10:"OutBoundId";i:108203741;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026387726";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:328;a:10:{s:10:"OutBoundId";i:108203742;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300024141368";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:329;a:10:{s:10:"OutBoundId";i:108203743;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300023478533";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:330;a:10:{s:10:"OutBoundId";i:108203744;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300027770589";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:331;a:10:{s:10:"OutBoundId";i:108203745;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300023452625";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:332;a:10:{s:10:"OutBoundId";i:108203746;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026160862";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:333;a:10:{s:10:"OutBoundId";i:108203747;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300024177695";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:334;a:10:{s:10:"OutBoundId";i:108203748;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300026890394";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:335;a:10:{s:10:"OutBoundId";i:108203749;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300028231133";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:336;a:10:{s:10:"OutBoundId";i:108203750;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300016713672";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:337;a:10:{s:10:"OutBoundId";i:108203751;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300026749678";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:338;a:10:{s:10:"OutBoundId";i:108203752;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300024263626";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:339;a:10:{s:10:"OutBoundId";i:108203753;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300028561384";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:340;a:10:{s:10:"OutBoundId";i:108203754;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300028366903";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:341;a:10:{s:10:"OutBoundId";i:108203755;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300025672113";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:342;a:10:{s:10:"OutBoundId";i:108203756;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300027360476";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:343;a:10:{s:10:"OutBoundId";i:108203757;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300028222193";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:344;a:10:{s:10:"OutBoundId";i:108203758;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300017076707";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:345;a:10:{s:10:"OutBoundId";i:108203759;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300028213535";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:346;a:10:{s:10:"OutBoundId";i:108203760;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300022307490";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:347;a:10:{s:10:"OutBoundId";i:108203761;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300028230075";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:348;a:10:{s:10:"OutBoundId";i:108203762;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300022304482";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:349;a:10:{s:10:"OutBoundId";i:108203763;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300017076653";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:350;a:10:{s:10:"OutBoundId";i:108203764;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300025262338";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:351;a:10:{s:10:"OutBoundId";i:108203765;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026252871";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:352;a:10:{s:10:"OutBoundId";i:108203766;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026466452";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:353;a:10:{s:10:"OutBoundId";i:108203767;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300023121996";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:354;a:10:{s:10:"OutBoundId";i:108203768;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026710340";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:355;a:10:{s:10:"OutBoundId";i:108203769;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300027037989";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:356;a:10:{s:10:"OutBoundId";i:108203770;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026317334";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:357;a:10:{s:10:"OutBoundId";i:108203771;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300025601496";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:358;a:10:{s:10:"OutBoundId";i:108203772;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300025685908";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:359;a:10:{s:10:"OutBoundId";i:108203773;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300027300748";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:360;a:10:{s:10:"OutBoundId";i:108203774;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300024058635";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:361;a:10:{s:10:"OutBoundId";i:108203775;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300026718346";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:362;a:10:{s:10:"OutBoundId";i:108203776;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300025598918";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:363;a:10:{s:10:"OutBoundId";i:108203777;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300028230860";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:364;a:10:{s:10:"OutBoundId";i:108203778;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300027161349";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:365;a:10:{s:10:"OutBoundId";i:108203779;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300025602332";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:366;a:10:{s:10:"OutBoundId";i:108203780;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300024299243";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:367;a:10:{s:10:"OutBoundId";i:108203781;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026863770";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:368;a:10:{s:10:"OutBoundId";i:108203782;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300026140147";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:369;a:10:{s:10:"OutBoundId";i:108203783;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300025686356";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:370;a:10:{s:10:"OutBoundId";i:108203784;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300022151222";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:371;a:10:{s:10:"OutBoundId";i:108203785;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300017076691";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:372;a:10:{s:10:"OutBoundId";i:108203786;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300022302761";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:373;a:10:{s:10:"OutBoundId";i:108203787;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300024259742";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:374;a:10:{s:10:"OutBoundId";i:108203788;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300027513346";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:375;a:10:{s:10:"OutBoundId";i:108203789;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026250457";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:376;a:10:{s:10:"OutBoundId";i:108203790;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300024259728";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:377;a:10:{s:10:"OutBoundId";i:108203791;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300027111061";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:378;a:10:{s:10:"OutBoundId";i:108203792;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300024360851";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:379;a:10:{s:10:"OutBoundId";i:108203793;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026324776";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:380;a:10:{s:10:"OutBoundId";i:108203794;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026717844";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:381;a:10:{s:10:"OutBoundId";i:108203795;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026059623";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:382;a:10:{s:10:"OutBoundId";i:108203796;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026724989";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:383;a:10:{s:10:"OutBoundId";i:108203797;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300026097953";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:384;a:10:{s:10:"OutBoundId";i:108203798;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300026902639";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:385;a:10:{s:10:"OutBoundId";i:108203799;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026321843";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:386;a:10:{s:10:"OutBoundId";i:108203800;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300026753156";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:387;a:10:{s:10:"OutBoundId";i:108203801;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300025763309";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:388;a:10:{s:10:"OutBoundId";i:108203802;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300025444802";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:389;a:10:{s:10:"OutBoundId";i:108203803;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300027499640";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:390;a:10:{s:10:"OutBoundId";i:108203804;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300022129160";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:391;a:10:{s:10:"OutBoundId";i:108203805;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026250075";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:392;a:10:{s:10:"OutBoundId";i:108203806;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300022316317";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:393;a:10:{s:10:"OutBoundId";i:108203807;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300026844670";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:394;a:10:{s:10:"OutBoundId";i:108203808;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300025602813";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:395;a:10:{s:10:"OutBoundId";i:108203809;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026752265";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:396;a:10:{s:10:"OutBoundId";i:108203810;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300023116916";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:397;a:10:{s:10:"OutBoundId";i:108203811;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300026273067";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:398;a:10:{s:10:"OutBoundId";i:108203812;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300027754633";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:399;a:10:{s:10:"OutBoundId";i:108203813;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300025583716";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:400;a:10:{s:10:"OutBoundId";i:108203814;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300028210763";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:401;a:10:{s:10:"OutBoundId";i:108203815;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300025271071";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:402;a:10:{s:10:"OutBoundId";i:108203816;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026718476";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:403;a:10:{s:10:"OutBoundId";i:108203817;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300028210688";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:404;a:10:{s:10:"OutBoundId";i:108203818;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300025100302";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:405;a:10:{s:10:"OutBoundId";i:108203819;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300026325322";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:406;a:10:{s:10:"OutBoundId";i:108203820;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300022497627";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:407;a:10:{s:10:"OutBoundId";i:108203821;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300025267920";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:408;a:10:{s:10:"OutBoundId";i:108203822;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300023597371";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:409;a:10:{s:10:"OutBoundId";i:108203823;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026749623";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:410;a:10:{s:10:"OutBoundId";i:108203824;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300024464627";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:411;a:10:{s:10:"OutBoundId";i:108203825;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300028205431";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:412;a:10:{s:10:"OutBoundId";i:108203826;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300028219391";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:413;a:10:{s:10:"OutBoundId";i:108203827;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026059616";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:414;a:10:{s:10:"OutBoundId";i:108203828;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300028217472";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:415;a:10:{s:10:"OutBoundId";i:108203829;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300022162143";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:416;a:10:{s:10:"OutBoundId";i:108203830;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026095157";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:417;a:10:{s:10:"OutBoundId";i:108203831;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026720769";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:418;a:10:{s:10:"OutBoundId";i:108203832;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300028547555";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:419;a:10:{s:10:"OutBoundId";i:108203833;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300025373102";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:420;a:10:{s:10:"OutBoundId";i:108203834;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300028222490";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:421;a:10:{s:10:"OutBoundId";i:108203835;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300025466989";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:422;a:10:{s:10:"OutBoundId";i:108203836;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300028222414";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:423;a:10:{s:10:"OutBoundId";i:108203837;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300025659831";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:424;a:10:{s:10:"OutBoundId";i:108203838;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300025447704";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:425;a:10:{s:10:"OutBoundId";i:108203839;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300023268806";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:426;a:10:{s:10:"OutBoundId";i:108203840;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300028674213";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:427;a:10:{s:10:"OutBoundId";i:108203841;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300018517568";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:428;a:10:{s:10:"OutBoundId";i:108203842;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026180358";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:429;a:10:{s:10:"OutBoundId";i:108203843;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300025615134";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:430;a:10:{s:10:"OutBoundId";i:108203844;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300022278790";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:431;a:10:{s:10:"OutBoundId";i:108203845;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026590355";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:432;a:10:{s:10:"OutBoundId";i:108203846;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300025972459";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:433;a:10:{s:10:"OutBoundId";i:108203847;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026714850";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:434;a:10:{s:10:"OutBoundId";i:108203848;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026752418";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:435;a:10:{s:10:"OutBoundId";i:108203849;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300033721940";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-TR-017";}i:436;a:10:{s:10:"OutBoundId";i:108203850;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300026725009";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:437;a:10:{s:10:"OutBoundId";i:108203851;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300019474532";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:438;a:10:{s:10:"OutBoundId";i:108203852;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300018491837";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:439;a:10:{s:10:"OutBoundId";i:108203853;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300022276758";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:440;a:10:{s:10:"OutBoundId";i:108203854;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300023123365";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:441;a:10:{s:10:"OutBoundId";i:108203855;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026754412";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:442;a:10:{s:10:"OutBoundId";i:108203856;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300022302426";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:443;a:10:{s:10:"OutBoundId";i:108203857;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300028222452";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:444;a:10:{s:10:"OutBoundId";i:108203858;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300026139219";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:445;a:10:{s:10:"OutBoundId";i:108203859;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300027328834";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:446;a:10:{s:10:"OutBoundId";i:108203860;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300021948861";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:447;a:10:{s:10:"OutBoundId";i:108203861;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026996713";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:448;a:10:{s:10:"OutBoundId";i:108203862;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300023476874";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:449;a:10:{s:10:"OutBoundId";i:108203863;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300023488709";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:450;a:10:{s:10:"OutBoundId";i:108203864;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300027316152";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:451;a:10:{s:10:"OutBoundId";i:108203865;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300027453369";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:452;a:10:{s:10:"OutBoundId";i:108203866;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300028230853";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:453;a:10:{s:10:"OutBoundId";i:108203867;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026559444";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:454;a:10:{s:10:"OutBoundId";i:108203868;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026929728";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:455;a:10:{s:10:"OutBoundId";i:108203869;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026495551";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:456;a:10:{s:10:"OutBoundId";i:108203870;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026387603";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:457;a:10:{s:10:"OutBoundId";i:108203871;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300023434737";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:458;a:10:{s:10:"OutBoundId";i:108203872;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026317600";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:459;a:10:{s:10:"OutBoundId";i:108203873;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300028221639";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:460;a:10:{s:10:"OutBoundId";i:108203874;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300023407069";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:461;a:10:{s:10:"OutBoundId";i:108203875;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300028221219";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:462;a:10:{s:10:"OutBoundId";i:108203876;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300023101608";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:463;a:10:{s:10:"OutBoundId";i:108203877;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300023276047";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:464;a:10:{s:10:"OutBoundId";i:108203878;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300027457657";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:465;a:10:{s:10:"OutBoundId";i:108203879;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300025104034";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:466;a:10:{s:10:"OutBoundId";i:108203880;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300025672861";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:467;a:10:{s:10:"OutBoundId";i:108203881;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300026831557";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:468;a:10:{s:10:"OutBoundId";i:108203882;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300026097816";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:469;a:10:{s:10:"OutBoundId";i:108203883;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300022908956";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:470;a:10:{s:10:"OutBoundId";i:108203884;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026724897";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:471;a:10:{s:10:"OutBoundId";i:108203885;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026861752";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:472;a:10:{s:10:"OutBoundId";i:108203886;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026061879";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:473;a:10:{s:10:"OutBoundId";i:108203887;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300027495932";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:474;a:10:{s:10:"OutBoundId";i:108203888;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026092132";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:475;a:10:{s:10:"OutBoundId";i:108203889;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300028510993";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:476;a:10:{s:10:"OutBoundId";i:108203890;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300028511068";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:477;a:10:{s:10:"OutBoundId";i:108203891;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300025602585";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:478;a:10:{s:10:"OutBoundId";i:108203892;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300028230891";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:479;a:10:{s:10:"OutBoundId";i:108203893;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300026705667";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:480;a:10:{s:10:"OutBoundId";i:108203894;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300025472997";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:481;a:10:{s:10:"OutBoundId";i:108203895;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300026849316";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:482;a:10:{s:10:"OutBoundId";i:108203896;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300026097991";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:483;a:10:{s:10:"OutBoundId";i:108203897;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300023063838";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:484;a:10:{s:10:"OutBoundId";i:108203898;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300027116646";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:485;a:10:{s:10:"OutBoundId";i:108203899;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300025426556";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:486;a:10:{s:10:"OutBoundId";i:108203900;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026494172";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:487;a:10:{s:10:"OutBoundId";i:108203901;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300025427935";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:488;a:10:{s:10:"OutBoundId";i:108203902;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300022542778";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:489;a:10:{s:10:"OutBoundId";i:108203903;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300028187317";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:490;a:10:{s:10:"OutBoundId";i:108203904;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300028187379";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:491;a:10:{s:10:"OutBoundId";i:108203905;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300028511266";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:492;a:10:{s:10:"OutBoundId";i:108203906;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026093436";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:493;a:10:{s:10:"OutBoundId";i:108203907;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300026625958";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:494;a:10:{s:10:"OutBoundId";i:108203908;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300022126640";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:495;a:10:{s:10:"OutBoundId";i:108203909;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300023442824";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:496;a:10:{s:10:"OutBoundId";i:108203910;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300017018264";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:497;a:10:{s:10:"OutBoundId";i:108203911;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300025593517";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:498;a:10:{s:10:"OutBoundId";i:108203912;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300028194186";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:499;a:10:{s:10:"OutBoundId";i:108203913;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300026753842";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:500;a:10:{s:10:"OutBoundId";i:108203914;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300028230419";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:501;a:10:{s:10:"OutBoundId";i:108203915;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300026451540";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:502;a:10:{s:10:"OutBoundId";i:108203916;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300026493397";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:503;a:10:{s:10:"OutBoundId";i:108203917;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300025448848";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:504;a:10:{s:10:"OutBoundId";i:108203918;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300025466774";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:505;a:10:{s:10:"OutBoundId";i:108203919;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300027117629";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:506;a:10:{s:10:"OutBoundId";i:108203920;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300025661322";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:507;a:10:{s:10:"OutBoundId";i:108203921;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300026931486";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:508;a:10:{s:10:"OutBoundId";i:108203922;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026764336";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:509;a:10:{s:10:"OutBoundId";i:108203923;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300026722077";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:510;a:10:{s:10:"OutBoundId";i:108203924;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300028220069";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:511;a:10:{s:10:"OutBoundId";i:108203925;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300026725658";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:512;a:10:{s:10:"OutBoundId";i:108203926;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300028229604";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:513;a:10:{s:10:"OutBoundId";i:108203927;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300026857519";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:514;a:10:{s:10:"OutBoundId";i:108203928;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300025105550";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:515;a:10:{s:10:"OutBoundId";i:108203929;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300021950673";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:516;a:10:{s:10:"OutBoundId";i:108203930;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300025143057";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:517;a:10:{s:10:"OutBoundId";i:108203931;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300022829695";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:518;a:10:{s:10:"OutBoundId";i:108203932;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300023077217";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:519;a:10:{s:10:"OutBoundId";i:108203933;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300026814154";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:520;a:10:{s:10:"OutBoundId";i:108203934;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300025069760";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:521;a:10:{s:10:"OutBoundId";i:108203935;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300028567959";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:522;a:10:{s:10:"OutBoundId";i:108203936;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300028567928";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:523;a:10:{s:10:"OutBoundId";i:108203937;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300028567119";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:524;a:10:{s:10:"OutBoundId";i:108203938;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300028566976";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:525;a:10:{s:10:"OutBoundId";i:108203939;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300023441506";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:526;a:10:{s:10:"OutBoundId";i:108203940;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026387382";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:527;a:10:{s:10:"OutBoundId";i:108203941;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300022909403";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:528;a:10:{s:10:"OutBoundId";i:108203942;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026752692";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:529;a:10:{s:10:"OutBoundId";i:108203943;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300023458801";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:530;a:10:{s:10:"OutBoundId";i:108203944;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300017018349";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:531;a:10:{s:10:"OutBoundId";i:108203945;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300026720837";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:532;a:10:{s:10:"OutBoundId";i:108203946;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300023434133";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:533;a:10:{s:10:"OutBoundId";i:108203947;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300022770676";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:534;a:10:{s:10:"OutBoundId";i:108203948;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300023489010";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:535;a:10:{s:10:"OutBoundId";i:108203949;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300026062890";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:536;a:10:{s:10:"OutBoundId";i:108203950;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300026996485";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:537;a:10:{s:10:"OutBoundId";i:108203951;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300026848937";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:538;a:10:{s:10:"OutBoundId";i:108203952;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300027354611";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:539;a:10:{s:10:"OutBoundId";i:108203953;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300027116523";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:540;a:10:{s:10:"OutBoundId";i:108203954;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300025602554";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:541;a:10:{s:10:"OutBoundId";i:108203955;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300017314366";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:542;a:10:{s:10:"OutBoundId";i:108203956;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300016713825";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:543;a:10:{s:10:"OutBoundId";i:108203957;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300028453504";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:544;a:10:{s:10:"OutBoundId";i:108203958;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300023102247";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:545;a:10:{s:10:"OutBoundId";i:108203959;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300017018271";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:546;a:10:{s:10:"OutBoundId";i:108203960;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300023656573";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:547;a:10:{s:10:"OutBoundId";i:108203961;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300026749708";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:548;a:10:{s:10:"OutBoundId";i:108203962;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300025414942";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:549;a:10:{s:10:"OutBoundId";i:108203963;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300026495186";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:550;a:10:{s:10:"OutBoundId";i:108203964;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300026377864";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:551;a:10:{s:10:"OutBoundId";i:108203965;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300028555901";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:552;a:10:{s:10:"OutBoundId";i:108203966;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300026259252";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:553;a:10:{s:10:"OutBoundId";i:108203967;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300026258835";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:554;a:10:{s:10:"OutBoundId";i:108203968;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300025579641";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:555;a:10:{s:10:"OutBoundId";i:108203969;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300025382319";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:556;a:10:{s:10:"OutBoundId";i:108203970;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300022151192";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:557;a:10:{s:10:"OutBoundId";i:108203971;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300022484801";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:558;a:10:{s:10:"OutBoundId";i:108203972;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300027470816";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:559;a:10:{s:10:"OutBoundId";i:108203973;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300027598831";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:560;a:10:{s:10:"OutBoundId";i:108203974;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300027769682";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:561;a:10:{s:10:"OutBoundId";i:108203975;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300026139387";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:562;a:10:{s:10:"OutBoundId";i:108203976;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300023437356";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:563;a:10:{s:10:"OutBoundId";i:108203977;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300027319603";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:564;a:10:{s:10:"OutBoundId";i:108203978;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300026140246";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:565;a:10:{s:10:"OutBoundId";i:108203979;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300025672199";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:566;a:10:{s:10:"OutBoundId";i:108203980;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300026959060";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:567;a:10:{s:10:"OutBoundId";i:108203981;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300025350080";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:568;a:10:{s:10:"OutBoundId";i:108203982;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300028374182";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:569;a:10:{s:10:"OutBoundId";i:108203983;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300026386620";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:570;a:10:{s:10:"OutBoundId";i:108203984;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300023003933";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:571;a:10:{s:10:"OutBoundId";i:108203985;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300028323128";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:572;a:10:{s:10:"OutBoundId";i:108203986;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300026339114";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:573;a:10:{s:10:"OutBoundId";i:108203987;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300022989757";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:574;a:10:{s:10:"OutBoundId";i:108203988;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300023078801";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:575;a:10:{s:10:"OutBoundId";i:108203989;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300028361311";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:576;a:10:{s:10:"OutBoundId";i:108203990;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300025582788";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:577;a:10:{s:10:"OutBoundId";i:108203991;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300026859346";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:578;a:10:{s:10:"OutBoundId";i:108203992;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300028234707";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:579;a:10:{s:10:"OutBoundId";i:108203993;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708663";s:18:"LotOrSingleBarcode";s:13:"2300025102702";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104718";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:580;a:10:{s:10:"OutBoundId";i:108203994;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300025483948";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-168-RW";}i:581;a:10:{s:10:"OutBoundId";i:108203995;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300025118147";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:582;a:10:{s:10:"OutBoundId";i:108203996;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300025386188";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-184-RW";}i:583;a:10:{s:10:"OutBoundId";i:108203997;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300037932373";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-181-RW";}i:584;a:10:{s:10:"OutBoundId";i:108203998;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300037932441";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-181-RW";}i:585;a:10:{s:10:"OutBoundId";i:108203999;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300038121288";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-181-RW";}i:586;a:10:{s:10:"OutBoundId";i:108204000;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300037948978";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-184-RW";}i:587;a:10:{s:10:"OutBoundId";i:108204001;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300029390105";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-184-RW";}i:588;a:10:{s:10:"OutBoundId";i:108204002;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300025339634";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-168-RW";}i:589;a:10:{s:10:"OutBoundId";i:108204003;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300028122134";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-184-RW";}i:590;a:10:{s:10:"OutBoundId";i:108204004;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300027122685";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:591;a:10:{s:10:"OutBoundId";i:108204005;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300025765310";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:592;a:10:{s:10:"OutBoundId";i:108204006;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300025666372";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:593;a:10:{s:10:"OutBoundId";i:108204007;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300028337613";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:594;a:10:{s:10:"OutBoundId";i:108204008;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300023651424";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:595;a:10:{s:10:"OutBoundId";i:108204009;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300025666303";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:596;a:10:{s:10:"OutBoundId";i:108204010;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725795";s:18:"LotOrSingleBarcode";s:13:"2300026554999";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104722";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:597;a:10:{s:10:"OutBoundId";i:108204011;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300026749371";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:598;a:10:{s:10:"OutBoundId";i:108204012;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300026581803";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:599;a:10:{s:10:"OutBoundId";i:108204013;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300026317884";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:600;a:10:{s:10:"OutBoundId";i:108204014;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300028333776";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:601;a:10:{s:10:"OutBoundId";i:108204015;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300026890318";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:602;a:10:{s:10:"OutBoundId";i:108204016;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725796";s:18:"LotOrSingleBarcode";s:13:"2300024141542";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010477";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:603;a:10:{s:10:"OutBoundId";i:108204017;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300028196777";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:604;a:10:{s:10:"OutBoundId";i:108204018;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708664";s:18:"LotOrSingleBarcode";s:13:"2300028195060";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010475";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:605;a:10:{s:10:"OutBoundId";i:108204019;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300022304284";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:606;a:10:{s:10:"OutBoundId";i:108204020;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300026597347";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:607;a:10:{s:10:"OutBoundId";i:108204021;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300027449720";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:608;a:10:{s:10:"OutBoundId";i:108204022;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300024204988";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:609;a:10:{s:10:"OutBoundId";i:108204023;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026848661";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:610;a:10:{s:10:"OutBoundId";i:108204024;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300028721382";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:611;a:10:{s:10:"OutBoundId";i:108204025;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026846469";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:612;a:10:{s:10:"OutBoundId";i:108204026;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300024614084";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:613;a:10:{s:10:"OutBoundId";i:108204027;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026325223";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:614;a:10:{s:10:"OutBoundId";i:108204028;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725794";s:18:"LotOrSingleBarcode";s:13:"2300026846490";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104724";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:615;a:10:{s:10:"OutBoundId";i:108204029;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300018514536";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:616;a:10:{s:10:"OutBoundId";i:108204030;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300040759899";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"CN-KZ-22-002";}i:617;a:10:{s:10:"OutBoundId";i:108204031;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300033245071";s:19:"LotOrSingleQuantity";s:1:"2";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:618;a:10:{s:10:"OutBoundId";i:108204032;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300033985533";s:19:"LotOrSingleQuantity";s:1:"2";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-123-RW";}i:619;a:10:{s:10:"OutBoundId";i:108204032;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300033985533";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-123-RW";}i:620;a:10:{s:10:"OutBoundId";i:108204033;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300039693470";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-123-RW";}i:621;a:10:{s:10:"OutBoundId";i:108204034;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300039693494";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-123-RW";}i:622;a:10:{s:10:"OutBoundId";i:108204035;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300035994632";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-156-RW";}i:623;a:10:{s:10:"OutBoundId";i:108204036;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300041149132";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-181-RW";}i:624;a:10:{s:10:"OutBoundId";i:108204037;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300033235072";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-184-RW";}i:625;a:10:{s:10:"OutBoundId";i:108204038;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300033614464";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-TR-012";}i:626;a:10:{s:10:"OutBoundId";i:108204039;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300032555669";s:19:"LotOrSingleQuantity";s:1:"2";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-TR-017";}i:627;a:10:{s:10:"OutBoundId";i:108204040;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300033570418";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-TR-017";}i:628;a:10:{s:10:"OutBoundId";i:108204041;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300038847980";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-133-RW";}i:629;a:10:{s:10:"OutBoundId";i:108204042;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300033880906";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:630;a:10:{s:10:"OutBoundId";i:108204043;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300038474506";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:631;a:10:{s:10:"OutBoundId";i:108204044;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300034709572";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:632;a:10:{s:10:"OutBoundId";i:108204045;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300034670001";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:633;a:10:{s:10:"OutBoundId";i:108204046;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300038506207";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:634;a:10:{s:10:"OutBoundId";i:108204047;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300039268869";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:635;a:10:{s:10:"OutBoundId";i:108204048;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300038950468";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:636;a:10:{s:10:"OutBoundId";i:108204049;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708652";s:18:"LotOrSingleBarcode";s:13:"2300039296541";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104727";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-184-RW";}i:637;a:10:{s:10:"OutBoundId";i:108204050;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300033214855";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:638;a:10:{s:10:"OutBoundId";i:108204051;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725789";s:18:"LotOrSingleBarcode";s:13:"2300028491087";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104731";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-22-152-RW";}i:639;a:10:{s:10:"OutBoundId";i:108204052;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300013069055";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-19-TR-107";}i:640;a:10:{s:10:"OutBoundId";i:108204053;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300013078309";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-20-TR-017";}i:641;a:10:{s:10:"OutBoundId";i:108204054;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300018756103";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"KZ-20-TR-069";}i:642;a:10:{s:10:"OutBoundId";i:108204055;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708653";s:18:"LotOrSingleBarcode";s:13:"2300018757193";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104735";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:12:"BD-KZ-21-002";}i:643;a:10:{s:10:"OutBoundId";i:108204056;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300025582948";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:644;a:10:{s:10:"OutBoundId";i:108204057;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300027013372";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:645;a:10:{s:10:"OutBoundId";i:108204058;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300028219384";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:646;a:10:{s:10:"OutBoundId";i:108204059;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300028218691";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:647;a:10:{s:10:"OutBoundId";i:108204060;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300027375081";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:648;a:10:{s:10:"OutBoundId";i:108204061;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300026831311";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:649;a:10:{s:10:"OutBoundId";i:108204062;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300027645931";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:650;a:10:{s:10:"OutBoundId";i:108204063;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300025661964";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:651;a:10:{s:10:"OutBoundId";i:108204064;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026753538";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:652;a:10:{s:10:"OutBoundId";i:108204065;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300025768755";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:653;a:10:{s:10:"OutBoundId";i:108204066;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300025673028";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:654;a:10:{s:10:"OutBoundId";i:108204067;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026140178";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:655;a:10:{s:10:"OutBoundId";i:108204068;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725792";s:18:"LotOrSingleBarcode";s:13:"2300026845172";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010476";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:656;a:10:{s:10:"OutBoundId";i:108204069;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725787";s:18:"LotOrSingleBarcode";s:13:"2300026260753";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104725";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:657;a:10:{s:10:"OutBoundId";i:108204070;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300026719718";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:658;a:10:{s:10:"OutBoundId";i:108204071;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300024544244";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:659;a:10:{s:10:"OutBoundId";i:108204074;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300026051696";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:660;a:10:{s:10:"OutBoundId";i:108204075;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300023100724";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:661;a:10:{s:10:"OutBoundId";i:108204076;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026750650";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:662;a:10:{s:10:"OutBoundId";i:108204077;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300028369447";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:663;a:10:{s:10:"OutBoundId";i:108204078;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300023551311";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:664;a:10:{s:10:"OutBoundId";i:108204079;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026251744";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:665;a:10:{s:10:"OutBoundId";i:108204080;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300026319659";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:666;a:10:{s:10:"OutBoundId";i:108204081;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026642856";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:667;a:10:{s:10:"OutBoundId";i:108204082;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300026317914";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:668;a:10:{s:10:"OutBoundId";i:108204083;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725802";s:18:"LotOrSingleBarcode";s:13:"2300027122647";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104712";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:669;a:10:{s:10:"OutBoundId";i:108204084;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300025110479";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:670;a:10:{s:10:"OutBoundId";i:108204085;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300026634318";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:671;a:10:{s:10:"OutBoundId";i:108204086;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300026750032";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:672;a:10:{s:10:"OutBoundId";i:108204087;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300026864210";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:673;a:10:{s:10:"OutBoundId";i:108204088;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300026581766";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:674;a:10:{s:10:"OutBoundId";i:108204089;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026389218";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:675;a:10:{s:10:"OutBoundId";i:108204090;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708657";s:18:"LotOrSingleBarcode";s:13:"2300023471077";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104728";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:676;a:10:{s:10:"OutBoundId";i:108204091;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300026464311";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:677;a:10:{s:10:"OutBoundId";i:108204092;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300027296959";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:678;a:10:{s:10:"OutBoundId";i:108204093;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300027736820";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:679;a:10:{s:10:"OutBoundId";i:108204094;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300025601762";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:680;a:10:{s:10:"OutBoundId";i:108204095;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300026253236";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:681;a:10:{s:10:"OutBoundId";i:108204096;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300023093668";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:682;a:10:{s:10:"OutBoundId";i:108204097;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725804";s:18:"LotOrSingleBarcode";s:13:"2300025602837";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104711";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:683;a:10:{s:10:"OutBoundId";i:108204098;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300025601809";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:684;a:10:{s:10:"OutBoundId";i:108204099;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725800";s:18:"LotOrSingleBarcode";s:13:"2300023055208";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010473";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:685;a:10:{s:10:"OutBoundId";i:108204100;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300027453437";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:686;a:10:{s:10:"OutBoundId";i:108204101;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300026465653";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:687;a:10:{s:10:"OutBoundId";i:108204102;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725805";s:18:"LotOrSingleBarcode";s:13:"2300022303829";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104730";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:688;a:10:{s:10:"OutBoundId";i:108204103;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300026854655";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:689;a:10:{s:10:"OutBoundId";i:108204104;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725790";s:18:"LotOrSingleBarcode";s:13:"2300022304444";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010471";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:690;a:10:{s:10:"OutBoundId";i:108204105;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300023008181";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:691;a:10:{s:10:"OutBoundId";i:108204106;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300028009084";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:692;a:10:{s:10:"OutBoundId";i:108204107;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725801";s:18:"LotOrSingleBarcode";s:13:"2300028373314";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104713";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:693;a:10:{s:10:"OutBoundId";i:108204108;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300026141076";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:694;a:10:{s:10:"OutBoundId";i:108204109;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300025666327";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:695;a:10:{s:10:"OutBoundId";i:108204110;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708654";s:18:"LotOrSingleBarcode";s:13:"2300025457819";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104715";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:696;a:10:{s:10:"OutBoundId";i:108204111;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300023484343";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:697;a:10:{s:10:"OutBoundId";i:108204112;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300027133780";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:698;a:10:{s:10:"OutBoundId";i:108204113;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300025267036";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:699;a:10:{s:10:"OutBoundId";i:108204114;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708659";s:18:"LotOrSingleBarcode";s:13:"2300025436388";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104716";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:700;a:10:{s:10:"OutBoundId";i:108204115;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708658";s:18:"LotOrSingleBarcode";s:13:"2300023034791";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104721";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:701;a:10:{s:10:"OutBoundId";i:108204116;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725797";s:18:"LotOrSingleBarcode";s:13:"2300025150949";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010478";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:702;a:10:{s:10:"OutBoundId";i:108204117;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300025073675";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:703;a:10:{s:10:"OutBoundId";i:108204118;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300022536579";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:704;a:10:{s:10:"OutBoundId";i:108204119;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725803";s:18:"LotOrSingleBarcode";s:13:"2300025102757";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104717";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:705;a:10:{s:10:"OutBoundId";i:108204120;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725793";s:18:"LotOrSingleBarcode";s:13:"2300026274002";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104710";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:706;a:10:{s:10:"OutBoundId";i:108204121;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725798";s:18:"LotOrSingleBarcode";s:13:"2300024458459";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104723";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:707;a:10:{s:10:"OutBoundId";i:108204122;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708655";s:18:"LotOrSingleBarcode";s:13:"2300024989618";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104714";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:708;a:10:{s:10:"OutBoundId";i:108204123;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300027468097";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:709;a:10:{s:10:"OutBoundId";i:108204124;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725799";s:18:"LotOrSingleBarcode";s:13:"2300023032292";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010479";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:710;a:10:{s:10:"OutBoundId";i:108204125;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300027131984";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:711;a:10:{s:10:"OutBoundId";i:108204126;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026044223";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:712;a:10:{s:10:"OutBoundId";i:108204127;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026472330";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:713;a:10:{s:10:"OutBoundId";i:108204128;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026716953";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:714;a:10:{s:10:"OutBoundId";i:108204129;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300025691367";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:715;a:10:{s:10:"OutBoundId";i:108204130;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043725791";s:18:"LotOrSingleBarcode";s:13:"2300026335277";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104720";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:716;a:10:{s:10:"OutBoundId";i:108204131;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708656";s:18:"LotOrSingleBarcode";s:13:"2300027510239";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104719";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:717;a:10:{s:10:"OutBoundId";i:108204132;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300026268209";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:718;a:10:{s:10:"OutBoundId";i:108204133;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708662";s:18:"LotOrSingleBarcode";s:13:"2300028218257";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104726";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:719;a:10:{s:10:"OutBoundId";i:108204134;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708660";s:18:"LotOrSingleBarcode";s:13:"2300024808308";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010474";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:720;a:10:{s:10:"OutBoundId";i:108204135;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708661";s:18:"LotOrSingleBarcode";s:13:"2300017307245";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:15:"232083561010472";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}i:721;a:10:{s:10:"OutBoundId";i:108204136;s:9:"InBoundId";s:0:"";s:9:"LcBarcode";s:13:"2430043708665";s:18:"LotOrSingleBarcode";s:13:"2300022309661";s:19:"LotOrSingleQuantity";s:1:"1";s:13:"WaybillSerial";s:3:"KZK";s:13:"WaybillNumber";s:16:"2320835610104729";s:6:"Volume";s:2:"32";s:15:"CargoShipmentNo";s:1:"1";s:13:"InvoiceNumber";s:0:"";}}}';

		$dataForToResendOutboundOrderAPI = unserialize($strToUnSerialize);

//		$rootPath = \Yii::getAlias('@stockDepartment') . '/web/resend-outbound/31-05-2022/22351354.xlsx';
		$rootPath = \Yii::getAlias('@stockDepartment') . '/web/resend-outbound/25-07-2022/23208356-101047.xlsx';

		$dataListAll = [];

		$excel = \PHPExcel_IOFactory::load($rootPath);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 2;
		for ($i = $start; $i <= 724; $i++) {

//			$defactoFeedbackQuantity = $excelActive->getCell('P' . $i)->getValue();
			$defactoFeedbackQuantity = $excelActive->getCell('S' . $i)->getValue();
			if ($defactoFeedbackQuantity > 0) {
				continue;
			}

			$OutboundId = $excelActive->getCell('B' . $i)->getValue();
			$SkuId = $excelActive->getCell('I' . $i)->getValue();
			$expectedQty = $excelActive->getCell('O' . $i)->getValue();

			$OutboundId = trim($OutboundId);
			$SkuId = trim($SkuId);
			$expectedQty = trim($expectedQty);

			$dataListAll [] = [
				'OutBoundId' => $OutboundId,
				'skuId' => $SkuId,
				'expectedQty' => $expectedQty,
				'products' => [] //$this->getAPIBarcodeBySkuId($SkuId),
			];
		}

//		VarDumper::dump($dataListAll,10,true);
//		die;

		$toSendDefacto = [];
		foreach ($dataListAll as $key => $value) {
			foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $kay2 => $value2) {
				if ($value["OutBoundId"] == $value2["OutBoundId"]) {
					$toSendDefacto['OutBoundFeedBackThreePLResponse'][] = $value2;
//					echo $value2["OutBoundId"]."<br />";
//					unset($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$kay2]);
				}
			}
		}

		foreach ($toSendDefacto['OutBoundFeedBackThreePLResponse'] as $key => $item) {
			$WaybillNumber = $toSendDefacto['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'];
			$toSendDefacto['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'] = '1' . $WaybillNumber;
		}

		sort($toSendDefacto['OutBoundFeedBackThreePLResponse']);


//		$api = new DeFactoSoapAPIV2Manager();
//        $res = $api->SendOutBoundFeedBackData($toSendDefacto);

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($res, 10, true);
//        VarDumper::dump($value, 10, true);
		echo "<br />";
		echo "<br />";
//        VarDumper::dump($value2, 10, true);
		echo "<br />";
		echo "<br />";
//		VarDumper::dump($dataForToResendOutboundOrderAPI, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
//		VarDumper::dump($dataListAll, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//		VarDumper::dump($toSendDefacto, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		die;


		return $this->render('index');
	}


	public function actionResendDiffOutboundOrder()
	{ // /other/one/resend-diff-outbound-order
		die("actionResendOutboundOrder - DIE");

		$strToUnSerialize = '';
		$dataForToResendOutboundOrderAPI = unserialize($strToUnSerialize);

//        VarDumper::dump($dataForToResendOutboundOrderAPI,10,true);
//        die;
		$strToUnSerialize2 = '';
		$dataForToResendOutboundOrderAPI2 = unserialize($strToUnSerialize2);

		$res = [];
		$count = 0;
		foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key => $item) {
			$count += ArrayHelper::getValue($item, 'LotOrSingleQuantity');
		}

		$count2 = 0;
		foreach ($dataForToResendOutboundOrderAPI2['OutBoundFeedBackThreePLResponse'] as $key => $item) {
			$count2 += ArrayHelper::getValue($item, 'LotOrSingleQuantity');
		}

		$strRowHeader = 'SENT;OutBoundId;LcBarcode;LotOrSingleBarcode;LotOrSingleQuantity;WaybillNumber;Volume2;';
		file_put_contents('ResendDiffOutboundOrder.csv', $strRowHeader . "\n");

		foreach ($dataForToResendOutboundOrderAPI2['OutBoundFeedBackThreePLResponse'] as $key => $item) {
			foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key2 => $item2) {

				if ($key != $key2) {
					continue;
				}

				$OutBoundId = ArrayHelper::getValue($item, 'OutBoundId');
				$LcBarcode = ArrayHelper::getValue($item, 'LcBarcode');
				$LotOrSingleBarcode = ArrayHelper::getValue($item, 'LotOrSingleBarcode');
				$LotOrSingleQuantity = ArrayHelper::getValue($item, 'LotOrSingleQuantity');
				$WaybillNumber = ArrayHelper::getValue($item, 'WaybillNumber');
				$Volume = ArrayHelper::getValue($item, 'Volume');

				$strRow = 'FIRST' . ';' . $OutBoundId . ';' . $LcBarcode . ';' . $LotOrSingleBarcode . ';' . $LotOrSingleQuantity . ';' . $WaybillNumber . ';' . $Volume . ";";

				$OutBoundId2 = ArrayHelper::getValue($item2, 'OutBoundId');
				$LcBarcode2 = ArrayHelper::getValue($item2, 'LcBarcode');
				$LotOrSingleBarcode2 = ArrayHelper::getValue($item2, 'LotOrSingleBarcode');
				$LotOrSingleQuantity2 = ArrayHelper::getValue($item2, 'LotOrSingleQuantity');
				$WaybillNumber2 = ArrayHelper::getValue($item2, 'WaybillNumber');
				$Volume2 = ArrayHelper::getValue($item2, 'Volume');

				$strRow2 = 'SECOND' . ';' . $OutBoundId2 . ';' . $LcBarcode2 . ';' . $LotOrSingleBarcode2 . ';' . $LotOrSingleQuantity2 . ';' . $WaybillNumber2 . ';' . $Volume2 . ";";
				file_put_contents('ResendDiffOutboundOrder.csv', $strRow . "\n", FILE_APPEND);
				file_put_contents('ResendDiffOutboundOrder.csv', $strRow2 . "\n", FILE_APPEND);
			}
		}

//
		foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key => $item) {
			foreach ($dataForToResendOutboundOrderAPI2['OutBoundFeedBackThreePLResponse'] as $key2 => $item2) {

				$OutBoundId = ArrayHelper::getValue($item, 'OutBoundId');
				$LcBarcode = ArrayHelper::getValue($item, 'LcBarcode');
				$LotOrSingleBarcode = ArrayHelper::getValue($item, 'LotOrSingleBarcode');
				$LotOrSingleQuantity = ArrayHelper::getValue($item, 'LotOrSingleQuantity');
				$WaybillNumber = ArrayHelper::getValue($item, 'WaybillNumber');
				$Volume = ArrayHelper::getValue($item, 'Volume');

				$strRow = $OutBoundId . ';' . $LcBarcode . ';' . $LotOrSingleBarcode . ';' . $LotOrSingleQuantity . ';' . $WaybillNumber . ';' . $Volume . ";";
				file_put_contents('OutBoundFeedBackThreePLResponse-ResendDiffOutboundOrder.csv', $strRow . "\n", FILE_APPEND);

//
				if (
					$OutBoundId == ArrayHelper::getValue($item2, 'OutBoundId') &&
					// $LcBarcode == ArrayHelper::getValue($item2,'LcBarcode') &&
//                    $LotOrSingleBarcode == ArrayHelper::getValue($item2,'LotOrSingleBarcode') &&
//                   $LotOrSingleQuantity == ArrayHelper::getValue($item2,'LotOrSingleQuantity')  &&
//                    $WaybillNumber == ArrayHelper::getValue($item2,'WaybillNumber')  &&
//                   $Volume == ArrayHelper::getValue($item2,'Volume') &&
					1) {
					unset($dataForToResendOutboundOrderAPI2['OutBoundFeedBackThreePLResponse'][$key2]);
				}

			}
		}


		$count3 = 0;
		foreach ($dataForToResendOutboundOrderAPI2['OutBoundFeedBackThreePLResponse'] as $key => $item) {
			$count3 += ArrayHelper::getValue($item, 'LotOrSingleQuantity');
		}

		foreach ($dataForToResendOutboundOrderAPI2['OutBoundFeedBackThreePLResponse'] as $key => $item) {
			$WaybillNumber = $dataForToResendOutboundOrderAPI2['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'];
//            $dataForToResendOutboundOrderAPI2['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'] = '1'.$WaybillNumber;
		}

		sort($dataForToResendOutboundOrderAPI2['OutBoundFeedBackThreePLResponse']);


		$api = new DeFactoSoapAPIV2Manager();
//        $res = $api->SendOutBoundFeedBackData($one);
//      $res = $api->SendOutBoundFeedBackData($dataForToResendOutboundOrderAPI2);

		// 1320221 7387

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//        VarDumper::dump($one, 10, true);
		echo "<br />";
		echo "<br />";
//        VarDumper::dump($dataForToResendOutboundOrderAPI, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($res, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";

		VarDumper::dump($dataForToResendOutboundOrderAPI2, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($count, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($count2, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($count3, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";

		die;
		return $this->render('index');
	}


	public function actionSendInventorySnapshot()
	{
		// other/one/send-inventory-snapshot

		die('other/one/send-inventory-snapshot');

		$api = new \common\modules\stock\service\StockAPIService();
		$api->helperUpdateDefactoSkuIdUpdate();

		$stockRepository = new \common\modules\stock\repository\Repository();
		$queryForSendInventorySnapshot = $stockRepository->getRemainsForSendInventorySnapshotDefacto();
		$oneSnapshot = $queryForSendInventorySnapshot->all();
		foreach ($oneSnapshot as $row) {
			$rowToSave = $row['product_barcode'] . ';';
			$rowToSave .= $row['primary_address'] . ';';
			$rowToSave .= $row['secondary_address'] . ';';
			$rowToSave .= $row['client_product_sku'] . ';';
			$rowToSave .= $row['productQty'] . ';';
			file_put_contents('queryForSendInventorySnapshotB2BCSV-' . date('Y-m-d') . '.csv', $rowToSave . "\n", FILE_APPEND);
		}
		//VarDumper::dump($oneSnapshot,10,true);
		//die('OK');
		$api->SendInventorySnapshot($oneSnapshot);

//        foreach ($queryForSendInventorySnapshot->batch() as $oneSnapshot) {
		//file_put_contents('queryForSendInventorySnapshotB2B-'.date('Y-m-d').'.log',print_r($oneSnapshot,true)."\n",FILE_APPEND);
		//$api->SendInventorySnapshot($oneSnapshot);
//        }
		return $this->render('index');
	}

	/**
	 *
	 * */
	public function actionCreateMoveOutbound()
	{
		$clientId = 2;
		$partyNumber = 'move-17042020';

		$newCoo = new ConsignmentOutboundOrder();
		$newCoo->setAttributes([
			'client_id' => $clientId,
			'party_number' => $partyNumber,
			'status' => Stock::STATUS_OUTBOUND_NEW,
			'expected_qty' => 0,
			'allocated_qty' => 0,
		], false);
		$newCoo->save(false);

		$partyNumber = $newCoo->party_number;
		$orderNumber = $newCoo->party_number;

		$oManager = new OutboundManager();
		$oManager->initBaseData($clientId, $partyNumber, $orderNumber);
		$oManager->setConsignmentID($newCoo->id);

		$newOutboundOrder = new OutboundOrder();
		$newOutboundOrder->setAttributes([
			'client_id' => $newCoo->client_id,
			'from_point_id' => 4,
			'to_point_id' => 1,
			'order_number' => $newCoo->party_number,
			'parent_order_number' => $newCoo->party_number,
			'consignment_outbound_order_id' => $newCoo->id,
			'status' => Stock::STATUS_OUTBOUND_NEW,
			'cargo_status' => 2,
			'expected_qty' => 0,
			'allocated_qty' => 0,
			'accepted_qty' => 0,
		], false);
		$newOutboundOrder->save(false);

		$oManager->setOutboundID($newOutboundOrder->id);

		$expectedQtyConsignmentNew = 0;
		$expectedQtyOutboundNew = 0;


		$ooIAll = OutboundOrderItem::find()->andWhere(['outbound_order_id' => $newOutboundOrder->id])->all();
		if ($ooIAll) {
			foreach ($ooIAll as $oIOrderNumber) {
				$expected_qtyNew = ($oIOrderNumber->expected_qty - $oIOrderNumber->accepted_qty);
				if ($expected_qtyNew) {
					$newOutboundOrderItem = new OutboundOrderItem();
					$newOutboundOrderItem->setAttributes([
						'outbound_order_id' => $newOutboundOrder->id,
						'product_barcode' => $oIOrderNumber->product_barcode,
						'status' => Stock::STATUS_OUTBOUND_NEW,
						'expected_qty' => $expected_qtyNew,
						'allocated_qty' => 0,
						'accepted_qty' => 0,
					], false);
					$newOutboundOrderItem->save(false);

					$expectedQtyConsignmentNew += $expected_qtyNew;
					$expectedQtyOutboundNew += $expected_qtyNew;
				}
			}

			$newOutboundOrder->expected_qty = $expectedQtyOutboundNew;
			$newOutboundOrder->save(false);
		}

		$newCoo->expected_qty = $expectedQtyConsignmentNew;
		$newCoo->save(false);

		$oManager->createUpdateDeliveryProposalAndOrder();
		$oManager->reservationOnStockByPartyNumber();

		return 'INDEX';
	}


	public function actionTestReserv()
	{
		// other/one/test-reserv
		$dto = new \stdClass();
		$dto->clientId = 95;
		$or = new OutboundRepository($dto);
		//$productList = $or->getProductsForReservation("FL1006");
		$productList = $or->getProductsForReservation("86363F2000");
		$productList = $or->getProductsForReservation("86362F2000");
		$productList = $or->getProductsForReservation("76003F2000");
		$productList = $or->getProductsForReservation("FL1006");

		$result = [];
//        if(count($productList) == 1) {
//            $result[] = [
//                'productQty'=>$productQty,
//                'productBarcode'=>$productBarcode,
//                'boxAddress'=>$boxAddress,
//                'placeAddress'=>$placeAddress,
//            ];
//            return $result;
//        }

		$expectedProductQty = 1;

		if (!empty($productList) && is_array($productList)) {
			foreach ($productList as $productInBox) {
				$productQty = ArrayHelper::getValue($productInBox, 'productQty', null);
				$productBarcode = ArrayHelper::getValue($productInBox, 'product_barcode', null);
				$boxAddress = ArrayHelper::getValue($productInBox, 'primary_address', null);
				$placeAddress = ArrayHelper::getValue($productInBox, 'secondary_address', null);
				$addressSortOrder = ArrayHelper::getValue($productInBox, 'address_sort_order', null);

				if ($productQty >= $expectedProductQty) {
					$result[] = [
						'productQty' => $productQty,
						'productBarcode' => $productBarcode,
						'boxAddress' => $boxAddress,
						'placeAddress' => $placeAddress,
						'addressSortOrder' => $addressSortOrder,
					];
					break;
				}
			}
		}


		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($productList, 10, true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		VarDumper::dump($result, 10, true);

		return $this->render('index');
	}


	public function actionAutoAddInbound()
	{
		// other/one/auto-add-inbound

		die(' other/one/auto-add-inbound ');
		$inboundId = 79609;
		$inboundOne = InboundOrder::find()->andWhere(['id' => $inboundId])->one();
		$inboundItemList = InboundOrderItem::find()->andWhere(['inbound_order_id' => $inboundId])->all();

		foreach ($inboundItemList as $item) {

			$dto = new \stdClass();
			$dto->orderNumberId = $inboundId;
			$dto->transportedBoxBarcode = '500000010480';
			$dto->conditionType = StockConditionType::UNDAMAGED;
			$dto->productBarcode = $item->product_barcode;
			$dto->productQty = $item->expected_qty;

			$inboundOrderService = new InboundOrderService($dto);
			$inboundOrderService->addScannedProductToStock($dto);
		}
	}


	public function actionAddRemoveInbound()
	{
//        die('other/one/add-remove-inbound DIE');

		$rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/02/stock79465.xlsx';
		$excel = \PHPExcel_IOFactory::load($rootPath);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 2;
		$row = '-1';
		for ($i = $start; $i <= 476; $i++) {

			$id = (int)$excelActive->getCell('A' . $i)->getValue();

			$stock = Stock::find()->andWhere(['id' => $id])->one();


			if ($stock) {
				continue;
			}

			$row .= $id . ',';
		}

		return $row;
	}


	public function actionDiffInbound()
	{
		die('other/one/diff-inbound DIE');
		$config = new \stdClass();
		$config->fileName = '03/D10AA00220008.xlsx';
		$config->inboundId = 79450;
		$config->maxRowInFile = 83;

		$rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/' . $config->fileName;
		$excel = \PHPExcel_IOFactory::load($rootPath);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 2;
		$row = '-1';
		for ($i = $start; $i <= $config->maxRowInFile; $i++) {

			$productBarcode = $excelActive->getCell('J' . $i)->getValue();
			$expectedQty = (int)$excelActive->getCell('S' . $i)->getValue();
			$acceptedQtyFromFile = (int)$excelActive->getCell('U' . $i)->getValue();
			$lcBox = $excelActive->getCell('K' . $i)->getValue();

			$inboundItem = InboundOrderItem::find()
										   ->andWhere([
											   'product_barcode' => $productBarcode,
											   'box_barcode' => $lcBox,
											   'inbound_order_id' => $config->inboundId
										   ])->one();

			if ($inboundItem) {

				$acceptedQtyDB = $inboundItem->accepted_qty;

				$inboundItem->product_description = '';
				$inboundItem->allocated_qty = 0;
				$inboundItem->save(false);

				$diffQty = $acceptedQtyDB - $acceptedQtyFromFile;
				if ($diffQty == 0) {
					$inboundItem->product_description = 'api-ok';
//                    $inboundItem->accepted_qty = $acceptedQtyDB;
					$inboundItem->allocated_qty = -1;
					$inboundItem->save(false);
				}

				echo $i . ' : ' . $productBarcode . ' : ' . ($acceptedQtyDB - $acceptedQtyFromFile) . " : " . $lcBox . " : OK " . "<br />";
			} else {
				echo $i . ' : ' . $productBarcode . ' : ' . ($acceptedQtyFromFile) . " : " . $lcBox . " : NO " . "<br />";
			}

		}

		return $row;
	}


	public function actionAutoAddOutbound()
	{
		// other/one/auto-add-outbound

		die(' other/one/auto-add-outbound ');

		echo "<br />";
		echo "<br />";
		echo "<br />";

		$outboundId = 46738;

		//Stock::resetByOutboundOrderId($outboundId);
		$outboundOne = OutboundOrder::find()->andWhere(['id' => $outboundId])->one();
		$outboundItemList = OutboundOrderItem::find()->andWhere(['outbound_order_id' => $outboundId])->all();
//        $validation = new \common\clientObject\hyundaiAuto\outbound\validation\ValidationOutbound();
//        $validation = new \common\clientObject\hyundaiTruck\outbound\validation\ValidationOutbound();
		$validation = new \common\clientObject\subaruAuto\outbound\validation\ValidationOutbound(); // 97

		$employeeBarcode = '01';
		$pickListBarcode = '46738-97-1';
		$pickList = $validation->getPickListByBarcode($pickListBarcode);
		$employee = $validation->getEmployeeByBarcode($employeeBarcode);

		foreach ($outboundItemList as $item) {

			$dto = new \stdClass();
			$dto->order = $outboundOne;
			$dto->pickList = $pickList;
			$dto->employee = $employee;
			$dto->boxBarcode = '400000029723';
			$dto->productBarcode = $item->product_barcode;
			$dto->productQty = $item->allocated_qty - $item->accepted_qty;

			if ($dto->productQty > 0) {
				echo $item->product_barcode . ' ; ' . $item->allocated_qty . ' ; ' . $dto->productQty . "<br />";

//                $service = new \common\clientObject\hyundaiAuto\outbound\service\OutboundService($dto);
//                $service = new \common\clientObject\hyundaiTruck\outbound\service\OutboundService($dto);
				$service = new \common\clientObject\subaruAuto\outbound\service\OutboundService($dto);
				$service->makeScannedQty();
			}
		}

		return $this->render('index');
	}

	public function actionResendReturn()
	{
		// other/one/resend-return

		die(' other/one/resend-return ');

		echo "<br />";
		echo "<br />";
		echo "<br />";

		$list = [
			'2430012033387',
			'2430012125567',
		];

		foreach ($list as $box) {
			$tmp = new ReturnTmpOrder();
			$returnOrderItemId = 10528;
			// Send data to APT
			$returnOrderItemProducts = ReturnOrderItemProduct::find()
															 ->select('return_order_item_id, product_barcode, product_serialize_data, field_extra1, client_box_barcode, expected_qty')
															 ->andWhere(['return_order_id' => $returnOrderItemId])
															 ->andWhere(['client_box_barcode' => $box, 'status' => 9])
															 ->one();

//            $toSendDataForAPI = [];
//            $returnOrderItemProductPrepared[] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataReturn($returnOrderItemProducts);
//            $toSendDataForAPI['InBoundFeedBackThreePLResponse'] = $returnOrderItemProductPrepared;

			$tmp->sendDataToAPI($returnOrderItemProducts);

//            VarDumper::dump($box, 10, true);
			echo $box . "<br />";
		}
		die;
//       $tmp->sendDataToAPI($returnOrderItemProducts);

		return $this->render('index');
	}

	public function actionFindLostOutboundBox()
	{
		// /other/one/find-lost-outbound-box
		$rootPath = 'tmp-file/defacto/20200730/700.xlsx';
		$excel = \PHPExcel_IOFactory::load($rootPath);
		$excel->setActiveSheetIndex(0);
		$activeSheet = $excel->getActiveSheet();

		$report = [];
		$report2 = [];
		$start = 1;
		for ($i = $start; $i <= 701; $i++) {
			$boxBarcode = $activeSheet->getCell('A' . $i)->getValue();
			if ($boxBarcode == null) {
				continue;
			}
			$report[$boxBarcode] = $boxBarcode;
			$report2[] = $boxBarcode;
		}

		$outbound_order_id = 46749;
		$all = Stock::find()
					->andWhere(['outbound_order_id' => $outbound_order_id, 'client_id' => 2])
					->all();


		foreach ($all as $stock) {
//            foreach($report as $key=>$row) {

//                $exist = Stock::find()
//                                 ->andWhere(['box_barcode'=>$row['boxBarcode'],'outbound_order_id'=>$outbound_order_id,'client_id'=>2])
//                                 ->exists();
			if (!isset($report[$stock->box_barcode])) {
				echo $stock->box_barcode . "<br>";
				echo $stock->product_barcode . "<br>";
				echo $stock->primary_address . "<br>";
				echo $stock->secondary_address . "<br>";
				echo "<br>";
				echo "<br>";
			}
//            }
		}

		VarDumper::dump(count($report), 10, true);
		echo "<br />";
		VarDumper::dump(count($report2), 10, true);
		echo "<br />";
		VarDumper::dump($report, 10, true);

		die("OK");
	}

	public function actionCheckDeliveryBill()
	{ // other/one/check-delivery-bill

		$rootPath = 'tmp-file/defacto/01-01-2018/';
		$_30_06_2020 = $this->parsTtnFiles($rootPath . '01062020/30-06-2020.xlsx');
		$_30_07_2020 = $this->parsTtnFiles($rootPath . '01072020/30-07-2020.xlsx');

		$this->findDuplicate($_30_06_2020, $_30_07_2020);
		$this->findDuplicate($_30_07_2020, $_30_06_2020);

		echo "<br />TIME : " . time() . " - OK-<br />";
	}

	public function actionMoveGetOutbound()
	{ // other/one/move-get-outbound

//        die(' die other/one/move-get-outbound ');

		$outboundList = [
			46751,//	DeFacto	RPT	8625342	97791	Алматы / KZK DC – 2 97791 [ ТЦ KZK DC – 2 ] KZK DC – 2 0
			46750,//	DeFacto	RPT	8625382	97791	Алматы / KZK DC – 2 97791 [ ТЦ KZK DC – 2 ] KZK DC – 2 0
			46749,//	DeFacto	RPT	8625537	97791	Алматы / KZK DC – 2 97791 [ ТЦ KZK DC – 2 ] KZK DC – 2 0
		];

//        $primaryAddress = '101000000001';
//        $secondaryAddress = '9-99-99-9';
//
		foreach ($outboundList as $outboundId) {
//            $allStockList = Stock::find()->andWhere(['outbound_order_id' => $outboundId])->all();
//            foreach ($allStockList as $stock) {
//                $stock->primary_address = $primaryAddress;
//                $stock->secondary_address = $secondaryAddress;
//                $stock->save(false);
//            }
			Stock::resetByOutboundOrderId($outboundId);
		}

		return '-return-move-get-outbound-';
	}


	public function actionGetOutboundLc()
	{ // other/one/get-outbound-lc

		die(' other/one/get-outbound-lc ');
		// 46751, 46750, 46749
		$outboundList = [
			46751,//	DeFacto	RPT	8625342	97791	Алматы / KZK DC – 2 97791 [ ТЦ KZK DC – 2 ] KZK DC – 2 0
			46750,//	DeFacto	RPT	8625382	97791	Алматы / KZK DC – 2 97791 [ ТЦ KZK DC – 2 ] KZK DC – 2 0
			46749,//	DeFacto	RPT	8625537	97791	Алматы / KZK DC – 2 97791 [ ТЦ KZK DC – 2 ] KZK DC – 2 0
		];

		foreach ($outboundList as $outboundId) {
			$lcLIst = [];
			$allStockList = Stock::find()->andWhere(['outbound_order_id' => $outboundId])->all();
			foreach ($allStockList as $stock) {

				$lcBarcode = (new OutboundBoxService())->getClientBoxByBarcode($stock->box_barcode);
				$lcLIst[$lcBarcode] = $lcBarcode;
			}


			file_put_contents($outboundId . '.csv', implode(';' . "\n", $lcLIst) . "\n", FILE_APPEND);
		}
		return '-return-other/one/get-outbound-lc-';
	}


	public function actionBoxOnMezanin()
	{ // other/one/box-on-mezanin

//        die(' other/one/box-on-mezanin ');

//
//        $sql = "SELECT count( DISTINCT `primary_address`)
//FROM `stock`
//WHERE `client_id` = '2' AND `status_availability` = '2' AND `is_product_type` = '2' AND  (`secondary_address` LIKE '3-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '1-%')";
//
//        $sql = "SELECT `product_barcode`, `primary_address`, `secondary_address`, `inbound_client_box`, `id`
//FROM `stock`
//WHERE `client_id` = '2' AND `status_availability` = '2' AND `is_product_type` = '2' AND  (`secondary_address` LIKE '3-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '1-%')";
//
//        $sql = "SELECT count( DISTINCT `primary_address`)
//FROM `stock`
//WHERE `client_id` = '2' AND `status_availability` = '2' AND (`is_product_type` = '0' OR `is_product_type` = '1' ) AND  (`secondary_address` LIKE '3-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '1-%')";
//
//        $sql = "SELECT `product_barcode`, `primary_address`, `secondary_address`, `inbound_client_box`, `id`
//FROM `stock`
//WHERE `client_id` = '2' AND `status_availability` = '2' AND (`is_product_type` = '0' OR `is_product_type` = '1' ) AND  (`secondary_address` LIKE '3-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '1-%')";


//        $i = 1;
//
//        foreach (Stock::find()->andWhere('is_product_type NOT IN (23)')->andWhere(['client_id' => 2, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])->orderBy(['id' => SORT_DESC])->each(50) as $stock) {
//            if (BarcodeManager::isReturnBoxBarcode($stock->primary_address)) {
//                $stock->is_product_type = Stock::IS_PRODUCT_TYPE_RETURN;
//            } else if (BarcodeManager::isOneBoxOneProduct($stock->primary_address, 2)) {
//                $stock->is_product_type = Stock::IS_PRODUCT_TYPE_LOT_BOX;
//            }
//            $stock->save(false);
//        }


//        $returnBoxCount = Stock::find()->select('count( DISTINCT `primary_address`)')
//                       ->andWhere(['client_id'=>2,'status_availability'=>Stock::STATUS_AVAILABILITY_YES,'is_product_type'=>2])
//                       ->andWhere("(`secondary_address` LIKE '3-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '1-%')")
//                       ->asArray()
//                       ->scalar();
//
//        $simpleBoxCount = Stock::find()->select('count( DISTINCT `primary_address`)')
//                       ->andWhere(['client_id'=>2,'status_availability'=>Stock::STATUS_AVAILABILITY_YES])
//                       ->andWhere("(`is_product_type` = '0' OR `is_product_type` = '1' )")
//                       ->andWhere("(`secondary_address` LIKE '3-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '1-%')")
//                       ->asArray()
//                       ->scalar();


		// возвраты на другом складе
//        $returnBoxOtherWarehouse = Stock::find()->select('count(`primary_address`)')
		$returnBoxOtherWarehouseList = Stock::find()->select('product_barcode, status_availability, primary_address,secondary_address, inbound_client_box, is_product_type')
											->andWhere(['client_id' => 2, 'status_availability' => Stock::STATUS_AVAILABILITY_YES])
//            ->andWhere(['client_id'=>2,'status_availability'=>Stock::STATUS_AVAILABILITY_YES,'is_product_type'=>2])
//            ->andWhere("(`secondary_address` LIKE '9-99-99%')")
											->asArray()
											->all();

		foreach ($returnBoxOtherWarehouseList as $item) {

			$isProductType = '';
			switch ($item['is_product_type']) {
				case Stock::IS_PRODUCT_TYPE_LOT :
				case Stock::IS_PRODUCT_TYPE_LOT_BOX :
					$isProductType = 'simple box';
					break;
				case Stock::IS_PRODUCT_TYPE_RETURN :
					$isProductType = 'return box';
					break;
			}

			$row = $item['product_barcode'] . ';' .
				$item['status_availability'] . ';' .
				$item['primary_address'] . ';' .
				$item['secondary_address'] . ';' .
				$item['inbound_client_box'] . ';' .
				$isProductType . ';';

			file_put_contents('allBoxList11-09-2020.csv', $row . "\n", FILE_APPEND);
		}

//        VarDumper::dump($returnBoxCount,10,true);
//        echo "<br />";
//        VarDumper::dump($simpleBoxCount,10,true);
//        VarDumper::dump($returnBoxOtherWarehouseList,10,true);

		echo "<br />";
		echo "<br />";
		echo "OK";
//        echo "кол-во возвратных коробов на мезанине : ".$returnBoxCount."<br />";
//        echo "кол-во обычных коробов на мезанине : ".$simpleBoxCount."<br />";

		die;
		return ' - return-other/one/box-on-mezanin - ';
	}

	public function actionOutboundBox()
	{
		// other/one/outbound-box

		$rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/16-08-2020/returnBOX700.xlsx';
		$excel = \PHPExcel_IOFactory::load($rootPath);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 2;
		$row = '-1';
		$dataList = [];
		$dataListXX = [123, 123];
		for ($i = $start; $i <= 700; $i++) {
			$box = $excelActive->getCell('A' . $i)->getValue();
			if (!isset($dataList[$box])) {
				$dataList[$box] = $box;
			} else {
				echo $box . "<br />";
			}


//            $stock = Stock::find()->andWhere(['id'=>$id])->one();


//            if($stock) {
//                continue;
//            }
//
//            $row .= $id.',';
		}

		VarDumper::dump(count($dataList), 10, true);
		VarDumper::dump(count($dataListXX), 10, true);
		die;

		return 'ok';
	}

	public function actionAutoReturnAccept()
	{
		// other/one/auto-return-accept

		die(' other/one/auto-return-accept ');

		$secondaryAddress = '9-99-99-8';
		$ttn = 58967;

		$outBoxList = InboundUnitAddress::find()->select('our_barcode')->andWhere("id > 431699")->asArray()->all();
		$clientBoxList = ReturnOrderItems::find()->select('client_box_barcode')->andWhere("return_order_id = 10528")->orderBy(['id' => SORT_DESC])->asArray()->all();

		$data = [];
		foreach ($outBoxList as $ourBox) {
			$ourBoxToStockBarcode = $ourBox['our_barcode'];
			if (!isset($data[$ourBoxToStockBarcode])) {
				$data[$ourBoxToStockBarcode] = array_shift($clientBoxList);
			}
		}

		$dataList = [];
		foreach ($data as $ourBox => $clientBox) {
			$ourBoxToStockBarcode = $ourBox;
			$clientBoxBarcode = $clientBox['client_box_barcode'];

			if (empty($clientBoxBarcode)) {
				continue;
			}

			$rTmpOrder = new ReturnTmpOrder();
			$rTmpOrder->makeBox($ttn, $ourBoxToStockBarcode, $clientBoxBarcode);
		}

		VarDumper::dump($dataList, 10, true);
		echo "<br />";
		VarDumper::dump(count($dataList), 10, true);
		echo "<br />";
		VarDumper::dump(count($data), 10, true);
		die;

		return 'ok';
	}


	public function actionSeparateBoxToOtherWarehouse()
	{
		// other/one/separate-box-to-other-warehouse

		$stockList = Stock::find()->select('outbound_picking_list_barcode, product_barcode, inbound_client_box,outbound_order_id')->andWhere("secondary_address LIKE '%9-99-99-%' AND status_availability != 2 ")->all();

		$clientStoreArray = TLHelper::getStoreArrayByClientID();
		$dataListBoxOK = [];
		foreach ($stockList as $stock) {
			$outbound = OutboundOrder::find()->andWhere(['id' => $stock->outbound_order_id])->one();
			if ($outbound) {
				$storeName = \yii\helpers\ArrayHelper::getValue($clientStoreArray, $outbound->to_point_id);
				$row = $stock->outbound_picking_list_barcode . ';'
					. $stock->product_barcode . ';'
					. $stock->inbound_client_box . ';'
					. $outbound->order_number . ';'
					. $outbound->parent_order_number . ';'
					. $storeName . ';';
				file_put_contents('separate-box-to-other-warehouse.csv', $row . "\n", FILE_APPEND);

			} else {

			}
		}

		echo "<br />";
		echo "<br />";
		VarDumper::dump($dataListBoxOK, 10, true);

//        echo "<br />";
//        echo "2099";
//        echo "<br />";
//        VarDumper::dump(count($dataListBoxOK2099),10,true);
		die;

		return 'ok';
	}

	/**
	 *
	 * */
	public function actionCreateNewOutbound()
	{
		// other/one/create-new-outbound
		die('other/one/separate-box-to-other-warehouse');

		//Stock::resetByOutboundOrderId(47144);
		//die("-die-end-");
		$rootPathList = [];
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/28-08-2020/b.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 424; $i++) {
				$skuId = $excelActive->getCell('B' . $i)->getValue();
				$lotQty = $excelActive->getCell('H' . $i)->getValue();
				$dataList[$skuId] = $lotQty;
			}
		}
		$dataListX = [];
		foreach ($dataList as $skuId => $lotQty) {
			$product_barcode = Stock::find()->select('product_barcode')->andWhere(['field_extra1' => $skuId, 'client_id' => 2])->orderBy('field_extra1')->limit(1)->scalar();
			$dataListX[] = [
				'skuId' => $skuId,
				'lotQty' => $lotQty,
				'product_barcode' => $product_barcode,
			];
		}

//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump(count($dataList),10,true);
//        echo "<br />";
//        VarDumper::dump($dataList,10,true);
//
//        die("-------------------------");

		$clientId = 2;
		$partyNumber = 'move28082020';

		$newCoo = new ConsignmentOutboundOrder();
		$newCoo->setAttributes([
			'client_id' => $clientId,
			'party_number' => $partyNumber,
			'status' => Stock::STATUS_OUTBOUND_NEW,
			'expected_qty' => 0,
			'allocated_qty' => 0,
		], false);
		$newCoo->save(false);

		$partyNumber = $newCoo->party_number;
		$orderNumber = $newCoo->party_number;

		$oManager = new OutboundManager();
		$oManager->initBaseData($clientId, $partyNumber, $orderNumber);
		$oManager->setConsignmentID($newCoo->id);

		$newOutboundOrder = new OutboundOrder();
		$newOutboundOrder->setAttributes([
			'client_id' => $newCoo->client_id,
			'from_point_id' => 4,
			'to_point_id' => 1,
			'order_number' => $newCoo->party_number,
			'parent_order_number' => $newCoo->party_number,
			'consignment_outbound_order_id' => $newCoo->id,
			'status' => Stock::STATUS_OUTBOUND_NEW,
			'cargo_status' => 2,
			'expected_qty' => 0,
			'allocated_qty' => 0,
			'accepted_qty' => 0,
		], false);
		$newOutboundOrder->save(false);

		$oManager->setOutboundID($newOutboundOrder->id);

		$expectedQtyConsignmentNew = 0;
		$expectedQtyOutboundNew = 0;

		foreach ($dataListX as $data) {

			$product_barcode = $data['product_barcode'];
			$lotQty = $data['lotQty'];
			$productSku = $data['skuId'];

			$newOutboundOrderItem = new OutboundOrderItem();
			$newOutboundOrderItem->setAttributes([
				'outbound_order_id' => $newOutboundOrder->id,
				'product_barcode' => $product_barcode,
				'status' => Stock::STATUS_OUTBOUND_NEW,
				'expected_qty' => $lotQty,
				'allocated_qty' => 0,
				'accepted_qty' => 0,
				'product_sku' => $productSku,
			], false);

			$newOutboundOrderItem->save(false);

			$expectedQtyConsignmentNew += $lotQty;
			$expectedQtyOutboundNew += $lotQty;
		}

		$newOutboundOrder->expected_qty = $expectedQtyOutboundNew;
		$newOutboundOrder->save(false);

		$newCoo->expected_qty = $expectedQtyConsignmentNew;
		$newCoo->save(false);

		$oManager->createUpdateDeliveryProposalAndOrder();
		$oManager->reservationOnStockByPartyNumber();

		echo "<br />";
		echo "<br />";
		VarDumper::dump(count($dataList), 10, true);
		echo "<br />";
		VarDumper::dump($dataList, 10, true);

		return 'INDEX';
	}


	public function actionCheckBoxWithOtherWarehouse()
	{
		// other/one/check-box-with-other-warehouse
		//die('other/one/check-box-with-other-warehouse');
		$rootPathList = [];
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/22-08-2020/DefactoStok03092020.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/22-08-2020/DefactoStok03092020.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/22-08-2020/logitrans16092020.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 7518; $i++) {
				$box = $excelActive->getCell('G' . $i)->getValue();

				$box = trim($box);
//                if (!isset($dataList[$box])) {
				$dataList[$box] = $box;
//                } else {
//                    echo $box . "<br />";
//                }
			}
		}

		$returnBoxOtherWarehouseList = Stock::find()->select('product_barcode, status_availability, primary_address,secondary_address, inbound_client_box, is_product_type')
//            ->andWhere(['client_id'=>2,'status_availability'=>Stock::STATUS_AVAILABILITY_YES])
//            ->andWhere(['client_id'=>2,'status_availability'=>Stock::STATUS_AVAILABILITY_YES,'is_product_type'=>2])
											->andWhere("(`secondary_address` LIKE '9-99-99%')")
											->asArray()
											->all();

		foreach ($returnBoxOtherWarehouseList as $item) {
			if (isset($dataList[$item['inbound_client_box']])) {
				$dataList[$item['inbound_client_box']] = 'OK';
			}
		}

		VarDumper::dump(count($dataList), 10, true);
		VarDumper::dump($dataList, 10, true);

		foreach ($dataList as $box => $item) {
			$row = $box . ';' . $item;
			file_put_contents('9999999-v2.csv', $row . "\n", FILE_APPEND);
		}

		echo "<br />";
		echo "<br />";
//        VarDumper::dump(count($dataListBoxOK),10,true);
//        echo "<br />";
//        VarDumper::dump($dataListBoxOK,10,true);

		die;

		return 'ok';
	}

	public function actionAddKgToBox()
	{
		// other/one/add-kg-to-box
		//die('other/one/add-kg-to-box');
		$rootPathList = [];
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/24-09-2020/230boxes.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 230; $i++) {
				$box = $excelActive->getCell('A' . $i)->getValue();
				$kg = $excelActive->getCell('B' . $i)->getValue();

				$box = trim($box);
				$kg = trim($kg);
//                if (!isset($dataList[$box])) {
				$dataList[$box] = number_format($kg, 2);
//                } else {
//                    echo $box . "<br />";
//                }
			}
		}

		asort($dataList);

		foreach ($dataList as $box => $kg) {
			$stock = Stock::find()->andWhere([
				'client_id' => 2,
				'outbound_order_id' => 47394,
				'box_barcode' => $box,
			])->one();
			if ($stock) {
//                $stock->box_kg = $kg;
//                $stock->save(false);
			} else {
				echo $box . "<br />";
			}
		}


		VarDumper::dump($dataList, 10, true);
		VarDumper::dump(count($dataList), 10, true);
		die;
	}

	/*
*
* */
	public function actionResetOutboundOrder()
	{
		//other/one/reset-outbound-order
		//return 'NO';
//
		$client_id = 103;

		$partyNumberList = [
			"20240418-103-040419",
		];

		Stock::resetByOutboundOrderId("71863");
		Stock::resetByOutboundOrderId("71862");
//		Stock::resetByOutboundOrderId("71743");
//		Stock::resetByOutboundOrderId("4");
//		Stock::resetByOutboundOrderId("71431");
//		Stock::resetByOutboundOrderId("71430");
		return 'XO';
		foreach ($partyNumberList as $partyNumber) {
			$dataList = OutboundOrder::find()->select('parent_order_number, order_number, id')->andWhere(['parent_order_number' => $partyNumber])->all();
//            $dataList = OutboundOrder::find()->select('parent_order_number, order_number, id')->andWhere(['id' => $partyNumber])->all();
			$row = 1;
			foreach ($dataList as $value) {
				$partyNumber = $value['parent_order_number'];
				$orderNumber = $value['order_number'];
				$id = $value['id'];
				echo $row++ . ' ; ' . $client_id . ' ; ' . $partyNumber . ' ; ' . $orderNumber . ' ; ' . $id . "<br />";

//				Stock::resetByOutboundOrderId($id);

//                $oManager = new OutboundManager();
//                $oManager->initBaseData($client_id, $partyNumber,$orderNumber);
//                $oManager->resetByPartyNumber();
			}
		}

		return 'XO';
	}

	public function actionAddOtherLc()
	{

		// /other/one/add-other-lc
		die("/other/one/add-other-lc");
		$primaryAddress = '101000000001';
		$secondaryAddress = '9-99-99-9';

		$rootPathList = [];
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/05-10-2020/stock-reserved.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/06-10-2020/DEFACTO_05_10_2020_STOK_DURUMU.XLSX';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 6695; $i++) {
				$oldBoxLc = $excelActive->getCell('G' . $i)->getValue();
				$isLogitrans = 'OK';//$excelActive->getCell('C' . $i)->getValue();

				$oldBoxLc = trim($oldBoxLc);

				$dataList[$oldBoxLc]['oldBoxLc'] = $oldBoxLc;
				$dataList[$oldBoxLc]['isLogitrans'] = $isLogitrans;
			}
		}

		$resultList = [];

		foreach ($dataList as $oldBoxLcKey => $oldBoxLcInfo) {

			$oldBoxLc = $oldBoxLcInfo['oldBoxLc'];
			$isLogitrans = $oldBoxLcInfo['isLogitrans'];

			$oldStock = Stock::find()->andWhere([
				'client_id' => 2,
				'inbound_client_box' => $oldBoxLc,
			])->one();

			$resultList[$oldBoxLc] = new \stdClass();
			$resultList[$oldBoxLc]->oldLc = $oldBoxLc;
			$resultList[$oldBoxLc]->old8 = "";
			$resultList[$oldBoxLc]->oldProductBarcode = "";
			$resultList[$oldBoxLc]->oldProductSkuId = "";
			$resultList[$oldBoxLc]->oldLogitrans = "";
			$resultList[$oldBoxLc]->oldAvailable = "";
			$resultList[$oldBoxLc]->oldPlaceBox = "";
			$resultList[$oldBoxLc]->oldPlaceAddress = "";

			$resultList[$oldBoxLc]->newLc = '';
			$resultList[$oldBoxLc]->new8 = '';
			$resultList[$oldBoxLc]->newProductBarcode = '';
			$resultList[$oldBoxLc]->newProductSkuId = '';
			$resultList[$oldBoxLc]->newAvailable = '';
			$resultList[$oldBoxLc]->newAvailable = '';
			$resultList[$oldBoxLc]->newPlaceBox = "";
			$resultList[$oldBoxLc]->newPlaceAddress = "";

			if ($oldStock) {

				if ($oldStock->primary_address != $primaryAddress && $oldStock->secondary_address != $secondaryAddress) {
					$oldStock->inventory_primary_address = $oldStock->primary_address;
					$oldStock->primary_address = $primaryAddress;
					$oldStock->inventory_secondary_address = $oldStock->secondary_address;
					$oldStock->secondary_address = $secondaryAddress;
					$oldStock->save(false);
				}


				$resultList[$oldBoxLc]->oldLc = $oldBoxLc;
				$resultList[$oldBoxLc]->old8 = $oldStock->box_barcode;
				$resultList[$oldBoxLc]->oldProductBarcode = $oldStock->product_barcode;
				$resultList[$oldBoxLc]->oldProductSkuId = $oldStock->field_extra1;
				$resultList[$oldBoxLc]->oldLogitrans = $isLogitrans;
				$resultList[$oldBoxLc]->oldAvailable = $oldStock->status_availability;
				$resultList[$oldBoxLc]->oldPlaceBox = $oldStock->primary_address;
				$resultList[$oldBoxLc]->oldPlaceAddress = $oldStock->secondary_address;

				$newLcBarcode = (new OutboundBoxService())->getClientBoxByBarcode($oldStock->box_barcode);

				$newStock = Stock::find()->andWhere([
					'client_id' => 2,
					'inbound_client_box' => $newLcBarcode,
				])->one();

				if ($newStock) {

					if ($newStock->primary_address != $primaryAddress && $newStock->secondary_address != $secondaryAddress) {
						$newStock->inventory_primary_address = $newStock->primary_address;
						$newStock->primary_address = $primaryAddress;
						$newStock->inventory_secondary_address = $newStock->secondary_address;
						$newStock->secondary_address = $secondaryAddress;
						$newStock->save(false);
					}

					$resultList[$oldBoxLc]->newLc = $newLcBarcode;
					$resultList[$oldBoxLc]->new8 = $newStock->box_barcode;
					$resultList[$oldBoxLc]->newProductBarcode = $newStock->product_barcode;
					$resultList[$oldBoxLc]->newProductSkuId = $newStock->field_extra1;
					$resultList[$oldBoxLc]->newLogitrans = '';
					$resultList[$oldBoxLc]->newAvailable = $newStock->status_availability;
					$resultList[$oldBoxLc]->newPlaceBox = $newStock->primary_address;
					$resultList[$oldBoxLc]->newPlaceAddress = $newStock->secondary_address;

				} else {
					echo $oldBoxLc . " - <br />";
				}
			} else {
				echo $oldBoxLc . " --- <br />";
			}
		}

		foreach ($resultList as $dataInfo) {
			$str = "";
			$str .= $dataInfo->oldLc . ';';
			$str .= $dataInfo->old8 . ';';
			$str .= $dataInfo->oldProductBarcode . ';';
			$str .= $dataInfo->oldProductSkuId . ';';
			$str .= $dataInfo->oldLogitrans . ';';
			$str .= $dataInfo->oldAvailable . ';';
			$str .= $dataInfo->oldPlaceBox . ';';
			$str .= $dataInfo->oldPlaceAddress . ';';

			$str .= $dataInfo->newLc . ';';
			$str .= $dataInfo->new8 . ';';
			$str .= $dataInfo->newProductBarcode . ';';
			$str .= $dataInfo->newProductSkuId . ';';
			$str .= $dataInfo->newAvailable . ';';
			$str .= $dataInfo->newPlaceBox . ';';
			$str .= $dataInfo->newPlaceAddress . ';';


			file_put_contents('add-other-lc-06102020.csv', $str . "\n", FILE_APPEND);
		}
//SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS
		//SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS
//SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS
//SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS
////SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS//
//        VarDumper::dump(10,true);
		die;
	}

	public function actionCheckLcBox()
	{
		// /other/one/check-lc-box
//        die("/other/one/check-lc-box");
		file_put_contents('check-lc-box-21-10-2020.csv', "\n");
		$rootPathList = [];
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/05-10-2020/stock-reserved.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/06-10-2020/DEFACTO_05_10_2020_STOK_DURUMU.XLSX';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/21-10-2020/1.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i < 143; $i++) {
				$boxLc = $excelActive->getCell('A' . $i)->getValue();
				$boxLc = trim($boxLc);
				$dataList[$boxLc] = $boxLc;
			}
		}

		$resultList = [];

		foreach ($dataList as $boxLc) {

			$stock = Stock::find()->andWhere([
				'client_id' => 2,
				'inbound_client_box' => $boxLc,
			])->one();

			$resultList[$boxLc] = new \stdClass();
			$resultList[$boxLc]->oldLc = $boxLc;
			$resultList[$boxLc]->old8 = "";
			$resultList[$boxLc]->oldProductBarcode = "";
			$resultList[$boxLc]->oldProductSkuId = "";
			$resultList[$boxLc]->oldLogitrans = "";
			$resultList[$boxLc]->oldAvailable = "";
			$resultList[$boxLc]->oldPlaceBox = "";
			$resultList[$boxLc]->oldPlaceAddress = "";

			if ($stock) {
				$resultList[$boxLc]->oldLc = $boxLc;
				$resultList[$boxLc]->old8 = $stock->box_barcode;
				$resultList[$boxLc]->oldProductBarcode = $stock->product_barcode;
				$resultList[$boxLc]->oldProductSkuId = $stock->field_extra1;
				$resultList[$boxLc]->oldAvailable = $stock->status_availability;
				$resultList[$boxLc]->oldPlaceBox = $stock->primary_address;
				$resultList[$boxLc]->oldPlaceAddress = $stock->secondary_address;
			} else {
				echo $boxLc . " -NO- <br />";
			}
		}

		foreach ($resultList as $dataInfo) {
			$str = "";
			$str .= $dataInfo->oldLc . ';';
			$str .= $dataInfo->old8 . ';';
			$str .= $dataInfo->oldProductBarcode . ';';
			$str .= $dataInfo->oldProductSkuId . ';';
			$str .= $dataInfo->oldLogitrans . ';';
			$str .= $dataInfo->oldAvailable . ';';
			$str .= $dataInfo->oldPlaceBox . ';';
			$str .= $dataInfo->oldPlaceAddress . ';';

			file_put_contents('check-lc-box-21-10-2020.csv', $str . "\n", FILE_APPEND);
		}
		die;

	}

	public function actionMoveBoxToOtherWarehouse()
	{
		// other/one/move-box-to-other-warehouse
		//die('other/one/move-box-to-other-warehouse');
		$rootPathList = [];
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/22-08-2020/700-1.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/22-08-2020/700-2.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/22-08-2020/700-3.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/22-08-2020/700-4.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/22-08-2020/700-5.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/22-08-2020/logitrans16092020.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/28-09-2020/Defacoguncelstok28.09.2020.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/29-10-2020/27-10-2020.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/29-10-2020/logitrans1374.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/03-11-2020/670KZK.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/06-11-2020/logitrans1374.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 1356; $i++) {
				$box = $excelActive->getCell('A' . $i)->getValue();

				$box = trim($box);
				if (!isset($dataList[$box])) {
					$dataList[$box] = $box;
				} else {
					echo $box . "<br />";
				}
			}
		}

//        $primaryAddress = '108000000008'; // BArsan
//        $secondaryAddress = '8-88-88-8';
		$primaryAddress = '101000000001';
		$secondaryAddress = '9-99-99-9';

		$dataListBoxOK = [];
		$isResetStock = 0;
		foreach ($dataList as $box) {
//            $stock = Stock::find()->andWhere(['inbound_client_box'=>$box,'is_product_type'=>2])->one();
//            $stock = Stock::find()->andWhere(['inbound_client_box'=>$box])->one();
//            $stock = Stock::find()->andWhere(['inbound_client_box'=>$box,'status_availability'=>2])->one();
			$stock = Stock::find()->andWhere(['inbound_client_box' => $box])->andWhere('status_availability = 2 OR status = 17 OR status = 15 OR status = 16')->one();
			if ($stock) {

				if ($stock->primary_address == $primaryAddress && $stock->secondary_address == $secondaryAddress) {
					continue;
				}

				$stock->inventory_primary_address = $stock->primary_address;
				$stock->primary_address = $primaryAddress;
				$stock->inventory_secondary_address = $stock->secondary_address;
				$stock->secondary_address = $secondaryAddress;
				$stock->product_name = "Logitrans06112020";
				//$stock->save(false);

				if ($isResetStock) {
//                    Stock::updateAll([
//                        'box_barcode' => '',
//                        'outbound_order_id' => '0',
//                        'outbound_picking_list_id' => '0',
//                        'outbound_picking_list_barcode' => '',
//                        'status' => Stock::STATUS_NOT_SET,
//                        'status_availability' => Stock::STATUS_AVAILABILITY_YES
//                    ], ['id' => $stock->id]);
				}

			} else {
				$dataListBoxOK[] = $box;
			}
		}

		VarDumper::dump(count($dataList), 10, true);
		VarDumper::dump($dataList, 10, true);
		echo "<br />";
		echo "<br />";
		VarDumper::dump(count($dataListBoxOK), 10, true);
		echo "<br />";
		VarDumper::dump($dataListBoxOK, 10, true);

//        echo "<br />";
//        echo "2099";
//        echo "<br />";
//        VarDumper::dump(count($dataListBoxOK2099),10,true);
		die;

		return 'ok';
	}


	public function actionResetBoxFromOtherWarehouse()
	{
		// other/one/reset-box-from-other-warehouse
		die('other/one/reset-box-from-other-warehouse');
		$rootPathList = [];
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/08-11-2020/1.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 290; $i++) {
				$lotBarcode = $excelActive->getCell('B' . $i)->getValue();
				$isProblem = $excelActive->getCell('H' . $i)->getValue();
				$lcBarcode = $excelActive->getCell('I' . $i)->getValue();
				$newBoxBarcode = $excelActive->getCell('j' . $i)->getValue();

				$lotBarcode = trim($lotBarcode);
				$isProblem = trim($isProblem);
				$lcBarcode = trim($lcBarcode);
				$newBoxBarcode = trim($newBoxBarcode);

				if ($isProblem != 'N') {
					continue;
				}

				$dataList [] = [
					'lotBarcode' => $lotBarcode,
					'isProblem' => $isProblem,
					'lcBarcode' => $lcBarcode,
					'newBoxBarcode' => $newBoxBarcode,
				];
			}
		}


		$dataListBoxOK = [];
		$isResetStock = 1;
		foreach ($dataList as $data) {
			$stock = Stock::find()->andWhere([
				'product_barcode' => $data['lotBarcode'],
				'inbound_client_box' => $data['lcBarcode'],
				'is_product_type' => 2
			])->one();

			if ($stock) {
				$stock->primary_address = $data['newBoxBarcode'];
				$stock->secondary_address = "7-77-77-77";
				$stock->product_name = "restProblem08112020";
				$stock->save(false);

				if ($isResetStock) {
					Stock::updateAll([
						'box_barcode' => '',
						'outbound_order_id' => '0',
						'outbound_picking_list_id' => '0',
						'outbound_picking_list_barcode' => '',
						'status' => Stock::STATUS_NOT_SET,
						'status_availability' => Stock::STATUS_AVAILABILITY_YES
					], ['id' => $stock->id]);
				}
			} else {
				$dataListBoxOK[] = $data['lotBarcode'];
			}
		}

		VarDumper::dump(count($dataList), 10, true);
		VarDumper::dump($dataList, 10, true);
		echo "<br />";
		echo "<br />";
		VarDumper::dump(count($dataListBoxOK), 10, true);
		echo "<br />";
		VarDumper::dump($dataListBoxOK, 10, true);

//        echo "<br />";
//        echo "2099";
//        echo "<br />";
//        VarDumper::dump(count($dataListBoxOK2099),10,true);
		die;

		return 'ok';
	}


	public function actionCreateOutboundToLogitrans()
	{
		// /other/one/create-outbound-to-logitrans
		die("/other/one/create-outbound-to-logitrans");

		$primaryAddress = '';
		$secondaryAddress = '9-99-99-9';

		// SELECT * FROM `stock` WHERE `client_id` = '2' AND `is_product_type` = '2' AND `status_availability` = '2' AND `secondary_address` LIKE '10-3%'
		$stockList = Stock::find()->andWhere([
			'client_id' => 2,
			'is_product_type' => 2,
			'status_availability' => 2,
		])->andWhere("secondary_address LIKE '10-3%'")
						  ->limit(700)
						  ->all();

		$fileName = 'create-outbound-to-logitrans-10-11-2020.csv';

		$headerTitles = "placeAddress" . ";" .
			"placeBox" . ";" .
			"productBarcode" . ";" .
			"productSkuId" . ";" .
			"lcBoxBarcode" . ";" .
			"stockId" . ";";

		file_put_contents($fileName, $headerTitles . "\n");

		$resultList = [];
		foreach ($stockList as $stock) {

			$result = new \stdClass();
			$result->productBarcode = $stock->product_barcode;
			$result->productSkuId = $stock->field_extra1;
			$result->placeBox = $stock->primary_address;
			$result->placeAddress = $stock->secondary_address;
			$result->lcBoxBarcode = $stock->inbound_client_box;
			$result->stockId = $stock->id;

			$resultList[] = $result;
		}

		foreach ($resultList as $row) {
			$str = "";
			$str .= $row->placeAddress . ';';
			$str .= $row->placeBox . ';';
			$str .= $row->productBarcode . ';';
			$str .= $row->productSkuId . ';';
			$str .= $row->lcBoxBarcode . ';';
			$str .= $row->stockId . ';';


			file_put_contents($fileName, $str . "\n", FILE_APPEND);
		}

		return $this->render('index');
	}

	public function actionCheckOutboundToLogitrans()
	{
		// /other/one/check-outbound-to-logitrans
		die("/other/one/check-outbound-to-logitrans");

		$primaryAddress = '';
		$secondaryAddress = '9-99-99-9';

		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/logitrans/10-11-2020/create-outbound-to-logitrans-10-11-2020-Start.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 700; $i++) {

				$lcBarcode = $excelActive->getCell('A' . $i)->getValue();
				$newBoxBarcode = $excelActive->getCell('B' . $i)->getValue();
				$newBoxBarcode = $excelActive->getCell('C' . $i)->getValue();
				$newBoxBarcode = $excelActive->getCell('D' . $i)->getValue();
				$newBoxBarcode = $excelActive->getCell('E' . $i)->getValue();
				$newBoxBarcode = $excelActive->getCell('F' . $i)->getValue();

				$lcBarcode = trim($lcBarcode);
				$newBoxBarcode = trim($newBoxBarcode); // placeAddress	placeBox	productBarcode	productSkuId	lcBoxBarcode	stockId


				$result = new \stdClass();
				$result->productBarcode = $stock->product_barcode;
				$result->productSkuId = $stock->field_extra1;
				$result->placeBox = $stock->primary_address;
				$result->placeAddress = $stock->secondary_address;
				$result->lcBoxBarcode = $stock->inbound_client_box;
				$result->stockId = $stock->id;

				$resultList[] = $result;
			}
		}

		$stockList = Stock::find()->andWhere([
			'client_id' => 2,
			'is_product_type' => 2,
			'status_availability' => 2,
		])->andWhere("secondary_address LIKE '10-3%'")
						  ->limit(700)
						  ->all();

		$fileName = 'create-outbound-to-logitrans-10-11-2020.csv';

		$headerTitles = "placeAddress" . ";" .
			"placeBox" . ";" .
			"productBarcode" . ";" .
			"productSkuId" . ";" .
			"lcBoxBarcode" . ";" .
			"stockId" . ";";

		file_put_contents($fileName, $headerTitles . "\n");

		$resultList = [];
		foreach ($stockList as $stock) {

			$result = new \stdClass();
			$result->productBarcode = $stock->product_barcode;
			$result->productSkuId = $stock->field_extra1;
			$result->placeBox = $stock->primary_address;
			$result->placeAddress = $stock->secondary_address;
			$result->lcBoxBarcode = $stock->inbound_client_box;
			$result->stockId = $stock->id;

			$resultList[] = $result;
		}

		foreach ($resultList as $row) {
			$str = "";
			$str .= $row->placeAddress . ';';
			$str .= $row->placeBox . ';';
			$str .= $row->productBarcode . ';';
			$str .= $row->productSkuId . ';';
			$str .= $row->lcBoxBarcode . ';';
			$str .= $row->stockId . ';';


			file_put_contents($fileName, $str . "\n", FILE_APPEND);
		}

		return $this->render('index');
	}


	public function actionResetBoxByLc()
	{
		// other/one/reset-box-by-lc
		die('other/one/reset-box-by-lc start exit');
		$rootPathList = [];
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/09-11-2020/lc-errors.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/09-11-2020/lc-with-sevens.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/10-11-2020/lc-with-sevens.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 64; $i++) {

				$newBoxBarcode = $excelActive->getCell('A' . $i)->getValue();
				$lcBarcode = $excelActive->getCell('B' . $i)->getValue();

				$lcBarcode = trim($lcBarcode);
				$newBoxBarcode = trim($newBoxBarcode);

				$dataList [] = [
					'lcBarcode' => $lcBarcode,
					'newBoxBarcode' => $newBoxBarcode,
				];
			}
		}

		$dataListBoxOK = [];
		$isResetStock = 0;
		foreach ($dataList as $data) {
			$stock = Stock::find()->andWhere([
				'inbound_client_box' => $data['lcBarcode'],
				'is_product_type' => 2
			])->one();
//            ])->count();

//            if($stock > 1) {
//                echo $data['lcBarcode']." ERRORS<br />";
//            }
//            continue;

			if ($stock) {
				$stock->primary_address = $data['newBoxBarcode'];
//                $stock->secondary_address = "7-77-77-77";
				$stock->product_name = "restProblem10112020";
//                $stock->save(false);

				if ($isResetStock) {
					Stock::updateAll([
						'box_barcode' => '',
						'outbound_order_id' => '0',
						'outbound_picking_list_id' => '0',
						'outbound_picking_list_barcode' => '',
						'status' => Stock::STATUS_NOT_SET,
						'status_availability' => Stock::STATUS_AVAILABILITY_YES
					], ['id' => $stock->id]);
				}
			} else {
				$dataListBoxOK[] = $data['lcBarcode'];
			}
		}

		VarDumper::dump(count($dataList), 10, true);
		VarDumper::dump($dataList, 10, true);
		echo "<br />";
		echo "<br />";
		echo "ERRORS : ";
		echo "<br />";
		VarDumper::dump(count($dataListBoxOK), 10, true);
		echo "<br />";
		VarDumper::dump($dataListBoxOK, 10, true);

//        echo "<br />";
//        echo "2099";
//        echo "<br />";
//        VarDumper::dump(count($dataListBoxOK2099),10,true);
		die;

		return 'ok';
	}


	public function actionResendOutboundOrderFromFile()
	{ // /other/one/resend-outbound-order-from-file

//        die("actionResendOutboundOrderFromFile - DIE");

//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-outbound/19-11-2020/9787090.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-outbound/08-12-2020/10579179.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-outbound/08-12-2020/9182821.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-outbound/08-12-2020/8991366.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-outbound/11-12-2020/10184230.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-outbound/20-12-2020/10785379.xlsx';

		$storeMap = [
			'KAZ ALMATY APORT MALL' => 49197,
			'KAZ OSKEMEN ADK MALL' => 49196,
		];

		$dataListAll = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 3; $i++) {

				$BusinessUnitName = $excelActive->getCell('C' . $i)->getValue();
				$SKU_ID = $excelActive->getCell('E' . $i)->getValue();
				$Outbound_Quantity = $excelActive->getCell('H' . $i)->getValue();
				$Processed_FeedBack_Quantity = $excelActive->getCell('I' . $i)->getValue();

				$SKU_ID = trim($SKU_ID);
				$Outbound_Quantity = trim($Outbound_Quantity);
				$Processed_FeedBack_Quantity = trim($Processed_FeedBack_Quantity);

				$dataListAll [] = [
					'businessUnitName' => $BusinessUnitName,
					'skuId' => $SKU_ID,
					'expectedQty' => $Outbound_Quantity,
					'acceptedQty' => $Processed_FeedBack_Quantity,
					'products' => $this->getAPIBarcodeBySkuId($SKU_ID),
				];
			}
		}

		$dataListDiff = [];
		foreach ($dataListAll as $data) {
			if ($data['expectedQty'] == $data['acceptedQty']) {
				continue;
			}

			$dataListDiff [$data['businessUnitName']][] = [
				'skuId' => $data['skuId'],
				'expectedQty' => $data['expectedQty'],
				'acceptedQty' => $data['acceptedQty'],
				'products' => $data['products'],
			];
		}

//          VarDumper::dump($dataListDiff, 10, true);
//          die;
		////////////// Оставляем только не отправленные лоты которые прислал Азамат
		foreach ($dataListDiff as $BusinessUnitName => $dataList) {
			$resultAPIList = [];
			//echo $storeMap[$BusinessUnitName]."<br />";
			foreach ($dataList as $data) {

				$outboundOrderId = $storeMap[$BusinessUnitName];
				$outboundOrderModel = OutboundOrder::find()->andWhere(['id' => $outboundOrderId])->one();
				$dataForToResendOutboundOrderAPI = $this->prepareOutboundForSendByAPI($outboundOrderModel);

				foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key => $item) {
					$LotOrSingleBarcode = ArrayHelper::getValue($item, 'LotOrSingleBarcode');
					$lcBoxBarcode = ArrayHelper::getValue($item, 'LcBarcode');
					$OutBoundId = ArrayHelper::getValue($item, 'OutBoundId');

					if (in_array($LotOrSingleBarcode, $data['products'])) {
						$resultAPIList[] = $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key];
						break;
					}
				}
			}

			if (empty($resultAPIList)) {
				continue;
			}

			$dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] = $resultAPIList;

			////////////// Удаляем ненужные короба
			$lcBoxBarcodeRemoveList = [
				'2430013171332' => '2430013327113',
				'2430013173288' => '2430013327114',
				'2430013173297' => '2430013327115',
				'2430013173299' => '2430013327116',
			];
			foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key => $item) {
				$LotOrSingleBarcode = ArrayHelper::getValue($item, 'LotOrSingleBarcode');
				$lcBoxBarcode = ArrayHelper::getValue($item, 'LcBarcode');
				$OutBoundId = ArrayHelper::getValue($item, 'OutBoundId');

				if (array_key_exists($lcBoxBarcode, $lcBoxBarcodeRemoveList)) {

//                    $LcBarcode = $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['LcBarcode'];
//                    $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['LcBarcode'] = $lcBoxBarcodeRemoveList[$lcBoxBarcode];
//                    unset($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]);
				}
			}

			////////////// Заменяем WaybillNumber и WaybillSerial
			foreach ($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'] as $key => $item) {
				$WaybillNumber = $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'];
				$WaybillSerial = $dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillSerial'];
				$dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillNumber'] = '1' . $WaybillNumber;
				$dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse'][$key]['WaybillSerial'] = 'A' . $WaybillSerial;
			}
//
			sort($dataForToResendOutboundOrderAPI['OutBoundFeedBackThreePLResponse']);

			$res = [];
			$api = new DeFactoSoapAPIV2Manager();
//                $res = $api->SendOutBoundFeedBackData($dataForToResendOutboundOrderAPI);

			echo "<br />";
			echo "<br />";
			echo "<br />";
			echo "<br />";
			echo "<br />";
			echo "<br />";
			echo "<br />";
//        VarDumper::dump($one, 10, true);
			echo "<br />";
			echo "<br />";
			VarDumper::dump($dataForToResendOutboundOrderAPI, 10, true);
			echo "<br />";
			echo "<br />";
			echo "<br />";
			VarDumper::dump($res, 10, true);
			echo "<br />";
			echo "<br />";
			echo "<br />";
		}


		die;

		return $this->render('index');
	}

	private function prepareOutboundForSendByAPI($outboundOrderModel)
	{

		$rowsDataForAPI = [];

		$outboundOrderItems = OutboundOrderItem::find()
											   ->andWhere(['outbound_order_id' => $outboundOrderModel->id])
											   ->andWhere('accepted_qty > 0')
											   ->all();

		if (!$outboundOrderItems) {
			return $rowsDataForAPI;
		}

		$mappingOurBobBarcodeToDefacto = [];
		$mappingWaybillNumber = [];
		$boxCountStep = 1;

		foreach ($outboundOrderItems as $outboundOrderItem) {
			$stocks = Stock::find()->select('product_barcode, inbound_order_id, count(id) as accepted_qty, box_barcode, box_size_m3, box_size_barcode, outbound_order_item_id')
						   ->andWhere(['outbound_order_item_id' => $outboundOrderItem->id])
						   ->andWhere(['status' => [
							   Stock::STATUS_OUTBOUND_SCANNED,
							   Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
							   Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
						   ]
						   ])
						   ->groupBy('product_barcode, box_barcode, outbound_order_item_id')
						   ->orderBy('box_barcode')
						   ->asArray()
						   ->all();

			if ($tmp = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks, $outboundOrderItem)) {
				foreach ($tmp as $tmpAPIValue) {
					if (!isset($mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']])) {
						$mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']] = (new OutboundBoxService())->getClientBoxByBarcode($tmpAPIValue['LcBarcode']);
						$mappingWaybillNumber[$tmpAPIValue['LcBarcode']] = DeFactoSoapAPIV2Manager::makeWaybillNumber($outboundOrderModel, $boxCountStep);
						$boxCountStep++;
						OutboundBoxService::addLcAndWaybillByBoxBarcode($tmpAPIValue['LcBarcode'], $mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']], $mappingWaybillNumber[$tmpAPIValue['LcBarcode']]);
					}
				}
			}

			foreach ($stocks as $keyStock => $stock) {
				$stocks[$keyStock]['box_barcode'] = $mappingOurBobBarcodeToDefacto[$stock['box_barcode']];
				$stocks[$keyStock]['our_box_barcode'] = $stock['box_barcode'];
				$stocks[$keyStock]['waybill_number'] = $mappingWaybillNumber[$stock['box_barcode']];
			}

			$outboundPreparedDataRows = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks, $outboundOrderItem);
			if ($outboundPreparedDataRows) {
				foreach ($outboundPreparedDataRows as $outboundPreparedDataRow) {
					unset($outboundPreparedDataRow['our_box_barcode']);
					$rowsDataForAPI['OutBoundFeedBackThreePLResponse'][] = $outboundPreparedDataRow;
				}
			}
		}

		return $rowsDataForAPI;
	}

	//
	private function getAPIBarcodeBySkuId($SkuId)
	{
		$api = new DeFactoSoapAPIV2();
		$params['request'] = [
			'BusinessUnitId' => '1029',
			'PageSize' => 0,
			'PageIndex' => 0,
			'CountAllItems' => false,
			'ProcessRequestedDataType' => 'Full',
			'SkuId' => $SkuId,
		];
//
		$result = $api->sendRequest('GetMasterData', $params);
		if ($resultDataArray = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.Data.MasterDataThreePL')) {
			$resultDataArray = count($resultDataArray) <= 1 ? [$resultDataArray] : $resultDataArray;
		} else {
			$resultDataArray = [];
		}
		$productBarcodes = [];
		foreach ($resultDataArray as $value) {
			$productBarcodes[] = $value->LotOrSingleBarcode;
		}

		return $productBarcodes;
	}


	//
	public function actionOutboundItemDiffInStock($inboundId = -1)
	{
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		//die('/other/one/outbound-item-diff-in-stock DIE');
		$outboundId = 49129;
		$outboundItemList = OutboundOrderItem::find()
											 ->andWhere(['outbound_order_id' => $outboundId])
											 ->all();

		$isSuccess = 1;
		foreach ($outboundItemList as $rowItem) {
			$count = Stock::find()->andWhere([
				'outbound_order_item_id' => $rowItem->id,
				'product_barcode' => $rowItem->product_barcode,
			])->count();

			if ($count != $rowItem->accepted_qty) {
				$isSuccess = 0;
				$onStockList = Stock::find()->andWhere([
					'outbound_order_id' => $rowItem->outbound_order_id,
					'product_barcode' => $rowItem->product_barcode,
				])->all();

				if (empty($onStockList)) {
					$row = $rowItem->product_barcode . ";" . '-' . ';' . '-' . "\n";//"<br />";
					echo $row . "<br />";
					file_put_contents("outboundItemList-full-" . $outboundId . ".csv", $row, FILE_APPEND);
				} else {
					foreach ($onStockList as $stockITem) {
						$row = $rowItem->id . ';' . $rowItem->product_barcode . ";" . $stockITem->id . "\n";//"<br />";
						echo $row . "<br />";
						file_put_contents("outboundItemList-full-" . $outboundId . ".csv", $row, FILE_APPEND);
					}
				}
			}
		}

		if ($isSuccess) {
			echo "<h1>Все хорошо, расхождений нет!</h1>" . "<br />";
		}

		return $this->render('index');
	}

	//
	public function actionResendReturnFromFile()
	{
		die(' other/one/resend-return-from-file ');

//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/resend-return-03-12-2020.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/return-resend-14-12-2020.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/01-02-2021/return_tmp_orders-9999-resend.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/22-02-2021/60return.xlsx';

		$dataListAll = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 60; $i++) {
				$clientBox = $excelActive->getCell('B' . $i)->getValue();
				$clientBox = trim($clientBox);
				$list[] = $clientBox;
			}
		}

		echo "<br />";
		echo "<br />";
		echo "<br />";
//            VarDumper::dump($list,10,true);
//            die;

		foreach ($list as $i => $box) {

//            $continue = [
//                '2430007582441',
//                '2430011180060',
//                '2430011166502',
//                '2430012900111',
//                '2430012964518',
//                '2430012964454',
//                '2430011219462',
//                '2430011183751',
//                '2430011219830',
//                '2430009483224',
//                '2430011212886',
//            ];
//
//            if(in_array($box,$continue)) {
//                continue;
//            }


//            $returnOrderItemId = 10528;
			// Send data to APT
			$returnOrderItemProducts = ReturnOrderItemProduct::find()
															 ->select('return_order_item_id, product_barcode, product_serialize_data, field_extra1, client_box_barcode, expected_qty')
															 ->andWhere(['client_box_barcode' => $box])
//                ->andWhere(['client_box_barcode' => $box, 'status' => [9]])
//                ->andWhere(['field_extra1' => $box, 'status' => 9])
//                ->andWhere(['return_order_id' => $returnOrderItemId])
//                ->andWhere(['client_box_barcode' => $box, 'status' => 9])
															 ->one();

			if ($returnOrderItemProducts) {
				$productSerializeDataDefault = CrossDockItemService::extractJsonData($returnOrderItemProducts->product_serialize_data);
				$SkuId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.SkuId');

//                if ($SkuId != $returnOrderItemProducts->field_extra2) {
//                    echo $SkuId . " DIFF - DIFF " . $returnOrderItemProducts->field_extra2 . "<br />";
//                }
			}

//            VarDumper::dump($returnOrderItemProducts->product_barcode,10,true);
//            VarDumper::dump(empty($returnOrderItemProducts->product_barcode),10,true);
//            die;
			$isNotFind = 'NO';
			if (empty($returnOrderItemProducts)) {
				$isNotFind = 'YES';

			}
//            $returnOrderItemProducts->status = 9;
//            $returnOrderItemProducts->accepted_qty = 1;
//            $returnOrderItemProducts->save(false);

			$tmp = new ReturnTmpOrder();
			// $tmp->sendDataToAPI($returnOrderItemProducts);
			echo $box . " - " . $isNotFind . " - " . $i . " : " . $returnOrderItemProducts->status . " : " . $returnOrderItemProducts->accepted_qty . "<br />";
//            die;
		}
//        die;
		return $this->render('index');
	}

	//
	public function actionResendReturnFromFileReport()
	{
		die(' other/one/resend-return-from-file-report ');


//        $allItemProducts = ReturnOrderItemProduct::find()->all();
//        foreach($allItemProducts as $ItemProduct) {
//            $prodSerialData = CrossDockItemService::extractJsonData($ItemProduct->product_serialize_data);
//            $skuId =  ArrayHelper::getValue($prodSerialData, 'apiLogValue.SkuId');
//            $ItemProduct->field_extra2 = $skuId;
//            $ItemProduct->save(false);
//
//           // echo $skuId."<br />";
//        }

//        die('END OK');


//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/return-resend-14-12-2020.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/diff-return-15-12-2020.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/22-12-2020.xlsx';

		$dataListAll = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 2; $i++) { // 1138

				$clientBox = $excelActive->getCell('B' . $i)->getValue();

				$clientBox = trim($clientBox);
				$list[] = $clientBox;
			}
		}

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//            VarDumper::dump($list,10,true);
//            die;
		$fileName = 'report-return-result02022021.csv';
		file_put_contents($fileName, "");

		$row = "product_barcode" . "; "
			. "client_box_barcode" . "; "
			. "expected_qty" . "; "
			. "accepted_qty" . "; "
			. "skuId" . "; "
			. "AppointmentBarcode" . "; "
			. "FromBusinessUnitId" . "; ";

		file_put_contents($fileName, $row . "\n", FILE_APPEND);

		$groupList = [];
		foreach ($list as $i => $box) {

			$returnOrderItemProducts = ReturnOrderItemProduct::find()
//                ->select('return_order_item_id, product_barcode, product_serialize_data, field_extra2, client_box_barcode, expected_qty,accepted_qty')
															 ->andWhere(['field_extra2' => $box, 'status' => 9])
															 ->all();

			if ($returnOrderItemProducts) {
				if (count($returnOrderItemProducts) > 1) {
					continue;
				}

				echo " " . $i . " : " . count($returnOrderItemProducts) . "----<br />";

				foreach ($returnOrderItemProducts as $returnOrderItemProduct) {
					$productSerializeDataDefault = CrossDockItemService::extractJsonData($returnOrderItemProduct->product_serialize_data);
					$SkuId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.SkuId');
					$AppointmentBarcode = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.AppointmentBarcode');
					$FromBusinessUnitId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.FromBusinessUnitId');
//
					$row = $returnOrderItemProduct->product_barcode . "; "
						. $returnOrderItemProduct->client_box_barcode . "; "
						. $returnOrderItemProduct->expected_qty . "; "
						. $returnOrderItemProduct->accepted_qty . "; "
						. $returnOrderItemProduct->field_extra2 . "; "
						. $AppointmentBarcode . "; "
						. $FromBusinessUnitId . "; ";

					file_put_contents($fileName, $row . "\n", FILE_APPEND);


					$groupList[$SkuId][] = [
						'product_barcode' => $returnOrderItemProduct->product_barcode,
						'client_box_barcode' => $returnOrderItemProduct->client_box_barcode,
						'expected_qty' => $returnOrderItemProduct->expected_qty,
						'accepted_qty' => $returnOrderItemProduct->accepted_qty,
						'skuId' => $returnOrderItemProduct->field_extra2,
						'AppointmentBarcode' => $AppointmentBarcode,
						'FromBusinessUnitId' => $FromBusinessUnitId,
					];

					$tmp = new ReturnTmpOrder();
					//$tmp->sendDataToAPI($returnOrderItemProduct);
				}
			}

//            VarDumper::dump($returnOrderItemProducts->product_barcode,10,true);
//            VarDumper::dump(empty($returnOrderItemProducts->product_barcode),10,true);
//            die;
			$isNotFind = 'NO';
			if (empty($returnOrderItemProducts)) {
				$isNotFind = 'YES';

			}

//            $tmp = new ReturnTmpOrder();
			//$tmp->sendDataToAPI($returnOrderItemProducts);
			//echo $box." - ".$isNotFind." - ".$i."<br />";
//            die;
		}


//        foreach($groupList as $i=>$box) {
//            echo " ".$i." : ".count($box)."<br />";
//        }

//        VarDumper::dump($groupList,10,true);

//        die;
		return $this->render('index');
	}

	// находим дубли возвратный коробов и ребята будут их проверять на складе
	public function actionResendReturnFromFileReport4()
	{
		//die(' other/one/resend-return-from-file-report4 ');


//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/02-02-2021/LC-return.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/04-02-2021/return_tmp_ordersX.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/04-02-2021/resendReturnBox.xlsx';

//        $dataListAll = [];
		$list = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 16; $i++) { // 1138
				$clientBox = $excelActive->getCell('A' . $i)->getValue();
				$clientBox = trim($clientBox);
				$list[] = $clientBox;
			}
		}

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//        VarDumper::dump($list,10,true);
//        echo "<br />";
//        VarDumper::dump($rootPathList,10,true);
//        die;
//        $fileName = 'report-return-result02022021.csv';
//        file_put_contents($fileName,"");
//
//        $row =  "product_barcode"."; "
//            ."client_box_barcode"."; "
//            ."expected_qty"."; "
//            ."accepted_qty"."; "
//            ."skuId"."; "
//            ."AppointmentBarcode"."; "
//            ."FromBusinessUnitId"."; ";
//
//        file_put_contents($fileName,$row."\n",FILE_APPEND);

//        $groupList = [];
		foreach ($list as $i => $box) {

			$returnOrderItemProducts = ReturnOrderItemProduct::find()
//                ->select('return_order_item_id, product_barcode, product_serialize_data, field_extra2, client_box_barcode, expected_qty,accepted_qty')
															 ->andWhere(['client_box_barcode' => $box, 'status' => 9])
															 ->all();

			if ($returnOrderItemProducts) {
//                if(count($returnOrderItemProducts) != 1) {
//                    continue;
//                }

				echo " " . $i . " : " . count($returnOrderItemProducts) . "----<br />";

//                foreach($returnOrderItemProducts as $returnOrderItemProduct) {
//                    $stockAll = Stock::find()->andWhere(['inbound_client_box'=>$box])->all();
//                    foreach($stockAll as $stockItem) {
//                        echo $stockItem->secondary_address."; ".
//                            $stockItem->primary_address."; ".
//                            $stockItem->inbound_client_box."; ".
//                            $stockItem->product_barcode."; ".
//                            $stockItem->status_availability."; ".
//                            $stockItem->field_extra1."; ".
//                            $stockItem->id."; ".
//                            "<br />";
//                    }

//                    if(count($stockAll) > 1) {
//                        $stockItem->deleted = 1;
//                        $stockItem->save(false);
//                    }

//                die;

//                }
//                echo $returnOrderItemProduct->client_box_barcode;
//                $returnOrderItemProduct->deleted = 1;
//                $returnOrderItemProduct->save(false);
//                die;
//                continue;

				foreach ($returnOrderItemProducts as $returnOrderItemProduct) {
//                    $productSerializeDataDefault = CrossDockItemService::extractJsonData($returnOrderItemProduct->product_serialize_data);
//                    $SkuId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.SkuId');
//                    $AppointmentBarcode = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.AppointmentBarcode');
//                    $FromBusinessUnitId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.FromBusinessUnitId');
////
//                    $row = $returnOrderItemProduct->product_barcode . "; "
//                        . $returnOrderItemProduct->client_box_barcode . "; "
//                        . $returnOrderItemProduct->expected_qty . "; "
//                        . $returnOrderItemProduct->accepted_qty . "; "
//                        . $returnOrderItemProduct->field_extra2 . "; "
//                        . $AppointmentBarcode . "; "
//                        . $FromBusinessUnitId . "; ";
//
//                    file_put_contents($fileName, $row . "\n", FILE_APPEND);


//                    $groupList[$SkuId][] = [
//                        'product_barcode' => $returnOrderItemProduct->product_barcode,
//                        'client_box_barcode' => $returnOrderItemProduct->client_box_barcode,
//                        'expected_qty' => $returnOrderItemProduct->expected_qty,
//                        'accepted_qty' => $returnOrderItemProduct->accepted_qty,
//                        'skuId' => $returnOrderItemProduct->field_extra2,
//                        'AppointmentBarcode' => $AppointmentBarcode,
//                        'FromBusinessUnitId' => $FromBusinessUnitId,
//                    ];

//                    VarDumper::dump($returnOrderItemProduct,10,true);

					$tmp = new ReturnTmpOrder();
					//$tmp->sendDataToAPI($returnOrderItemProduct);
				}
			}

//            VarDumper::dump($returnOrderItemProducts->product_barcode,10,true);
//            VarDumper::dump(empty($returnOrderItemProducts->product_barcode),10,true);
//            die;
//            $isNotFind = 'NO';
//            if(empty($returnOrderItemProducts)) {
//                $isNotFind = 'YES';
//            }

//            $tmp = new ReturnTmpOrder();
			//$tmp->sendDataToAPI($returnOrderItemProducts);
//            echo $box." - ".$isNotFind." - ".$i."<br />";
//            die;
		}


//        foreach($groupList as $i=>$box) {
//            echo " ".$i." : ".count($box)."<br />";
//        }

//        VarDumper::dump($groupList,10,true);

//        die;
		return $this->render('index');
	}

	public function actionAddSkuIdByLc()
	{
		// /other/one/add-sku-id-by-lc

//        die(" /other/one/add-sku-id-by-lc ");


		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/29-12-2020/add-sku-id-to-lc.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 163; $i++) {
				$lcBarcode = $excelActive->getCell('A' . $i)->getValue();
				$lcBarcode = trim($lcBarcode);
				$dataList[] = $lcBarcode;
			}
		}

//        VarDumper::dump($dataList,10,true);
//        die;

		$fileName = 'add-sku-id-by-lc.csv';

		$headerTitles = "LC" . ";" .
			"stockId" . ";";

		file_put_contents($fileName, $headerTitles . "\n");

		foreach ($dataList as $lc) {
			$skuId = Stock::find()->select('field_extra1')->andWhere([
				'client_id' => 2,
				'inbound_client_box' => $lc,
			])->scalar();


			$headerTitles = $lc . ";" .
				$skuId . ";";

			file_put_contents($fileName, $headerTitles . "\n", FILE_APPEND);
		}


		return $this->render('index');
	}

	//
	public function actionResendReturnFromFileReport2()
	{
		die(' other/one/resend-return-from-file-report2 ');

//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/31-12-2020/resend-by-lc.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/13-01-2021/resend-return-13-01-2021.xlsx';

		$dataListAll = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 66; $i++) {
				$clientBox = $excelActive->getCell('A' . $i)->getValue();
				$clientBox = trim($clientBox);
				$list[] = $clientBox;
			}
		}

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//            VarDumper::dump($list,10,true);
//            die;
		$fileName = 'resend-return-from-file-report-2' . time() . '.csv';
		file_put_contents($fileName, "");

		$row = "product_barcode" . "; "
			. "client_box_barcode" . "; "
			. "expected_qty" . "; "
			. "accepted_qty" . "; "
			. "skuId" . "; "
			. "AppointmentBarcode" . "; "
			. "FromBusinessUnitId" . "; ";

		file_put_contents($fileName, $row . "\n", FILE_APPEND);

		$groupList = [];
		foreach ($list as $i => $box) {

			$returnOrderItemProducts = ReturnOrderItemProduct::find()
//                ->andWhere(['field_extra2' => $box, 'status' => 9])
															 ->andWhere(['client_box_barcode' => $box, 'status' => 9])
															 ->all();

			if ($returnOrderItemProducts) {
//                if(count($returnOrderItemProducts) > 1) {
//                    continue;
//                }

				echo " " . $i . " : " . count($returnOrderItemProducts) . "----<br />";

				foreach ($returnOrderItemProducts as $k => $returnOrderItemProduct) {
					$productSerializeDataDefault = CrossDockItemService::extractJsonData($returnOrderItemProduct->product_serialize_data);
					$SkuId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.SkuId');
					$AppointmentBarcode = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.AppointmentBarcode');
					$FromBusinessUnitId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.FromBusinessUnitId');
//
					$row = $returnOrderItemProduct->product_barcode . "; "
						. $returnOrderItemProduct->client_box_barcode . "; "
						. $returnOrderItemProduct->expected_qty . "; "
						. $returnOrderItemProduct->accepted_qty . "; "
						. $returnOrderItemProduct->field_extra2 . "; "
						. $AppointmentBarcode . "; "
						. $FromBusinessUnitId . "; ";

					file_put_contents($fileName, $row . "\n", FILE_APPEND);

					$groupList[$SkuId][] = [
						'product_barcode' => $returnOrderItemProduct->product_barcode,
						'client_box_barcode' => $returnOrderItemProduct->client_box_barcode,
						'expected_qty' => $returnOrderItemProduct->expected_qty,
						'accepted_qty' => $returnOrderItemProduct->accepted_qty,
						'skuId' => $returnOrderItemProduct->field_extra2,
						'AppointmentBarcode' => $AppointmentBarcode,
						'FromBusinessUnitId' => $FromBusinessUnitId,
					];

					$tmp = new ReturnTmpOrder();
					//$tmp->sendDataToAPI($returnOrderItemProduct);
//                    echo $k."<br />";
//                    break;
				}
			}

//            VarDumper::dump($returnOrderItemProducts->product_barcode,10,true);
//            VarDumper::dump(empty($returnOrderItemProducts->product_barcode),10,true);
//            die;
			$isNotFind = '';
			if (empty($returnOrderItemProducts)) {
				$isNotFind = 'LOST';
			}

//            $tmp = new ReturnTmpOrder();
			//$tmp->sendDataToAPI($returnOrderItemProducts);
			echo $box . " - " . $isNotFind . " - " . $i . "<br />";
//            die;
		}

		return $this->render('index');
	}


	public function actionResetLostBoxFromFile()
	{
		// other/one/reset-lost-box-from-file
		die('other/one/reset-lost-box-from-file');

		$rootPathList = [];
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/NEW-DIFF-STOCK/stock-logitrans05012021.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/NEW-DIFF-STOCK/reset-lc-logitrans-06-12-2021.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 164; $i++) {
				$skuId = null; //$excelActive->getCell('A' . $i)->getValue();
				$lcBarcode = $excelActive->getCell('A' . $i)->getValue();

				$skuId = trim($skuId);
				$lcBarcode = trim($lcBarcode);

				$dataList [] = [
					'skuId' => $skuId,
					'lcBarcode' => $lcBarcode,
				];
			}
		}


		$dataListBoxOK = [];
		$isResetStock = 1;
		foreach ($dataList as $x => $data) {
			$stockList = Stock::find()->andWhere([
				'inbound_client_box' => $data['lcBarcode'],
				'is_product_type' => 2,
			])->andFilterWhere([
				'field_extra1' => $data['skuId'],
			])->all();

			if (empty($stockList)) {
				echo $x . "; " .
					$data['skuId'] . "; " .
					$data['lcBarcode'] . "; " .
					'-' . "; " .
					'-' . "; " .
					'-' . "; " .
					'-' . ";" .
					'-' . ";" .
					'-' . ";" .
					'-' . ";" .
					"<br />";
				continue;
			}


			foreach ($stockList as $stock) {

//                $ifLost = $stock->status == 17 && $stock->status_availability == 3;
//                $ifAvailable = $stock->status == 0 && $stock->status_availability == 2;
//
//                if($ifLost || $ifAvailable) {
//                    continue;
//                }

				echo $x . "; " .
					$data['skuId'] . "; " .
					$data['lcBarcode'] . "; " .
					$stock->secondary_address . "; " .
					$stock->status . "; " .
					$stock->status_availability . "; " .
					$stock->id . "; " .
					$stock->product_barcode . ";" .
					$stock->field_extra1 . ";" .
					$stock->outbound_picking_list_barcode . ";" .
					"<br />";

				/*                    Stock::updateAll([
                        'product_name' => 'Logitrans06012021',
                        'secondary_address' => '9-99-99-9',
//                        'inventory_secondary_address' => $stock->secondary_address,
//                        'inventory_primary_address' => $stock->primary_address,
//
                        'box_barcode' => '',
                        'outbound_order_id' => '0',
                        'outbound_picking_list_id' => '0',
                        'outbound_picking_list_barcode' => '',
                        'status' => Stock::STATUS_NOT_SET,
                        'status_availability' => Stock::STATUS_AVAILABILITY_YES
                    ], ['id' => $stock->id]);*/


//                $ifLost = $stock->status == 17 && $stock->status_availability == 3;
//                if($ifLost) {
//                    echo $x."; ".$data['skuId']."; ".$data['lcBarcode']."; ".$stock->secondary_address."; ".$stock->status."; ".$stock->status_availability.";"."<br />";

//                    Stock::updateAll([
//                        'box_barcode' => '',
//                        'outbound_order_id' => '0',
//                        'outbound_picking_list_id' => '0',
//                        'outbound_picking_list_barcode' => '',
//                        'status' => Stock::STATUS_NOT_SET,
//                        'status_availability' => Stock::STATUS_AVAILABILITY_YES
//                    ], ['id' => $stock->id]);
//                }
			}
		}

//        VarDumper::dump(count($dataList),10,true);
		VarDumper::dump($dataList, 10, true);
		echo "<br />";
		echo "<br />";
		die;

		return 'ok';
	}

	public function actionResetStockById()
	{

		// other/one/reset-stock-by-id
		die('other/one/reset-stock-by-id');

		$stockById = [
//            2810267,
//            2810288,
//            2810061,
//            2833017,
			-1
		];

		Stock::updateAll([
			'box_barcode' => '',
			'outbound_order_id' => '0',
			'outbound_picking_list_id' => '0',
			'outbound_picking_list_barcode' => '',
			'status' => Stock::STATUS_NOT_SET,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES
		], ['id' => $stockById]);

		return 'ok';
	}


	public function actionCheckForInventory()
	{

		// other/one/check-for-inventory
//        die('other/one/check-for-inventory');

		$rootPathList = [];
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/NEW-DIFF-STOCK/08-01-2021/ForInventory.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 125; $i++) {
				$skuId = $excelActive->getCell('A' . $i)->getValue();
				$dfQty = $excelActive->getCell('B' . $i)->getValue();
				$dfLotBarcode = $excelActive->getCell('E' . $i)->getValue();
				$dfLotBarcode1 = $excelActive->getCell('F' . $i)->getValue();

				$skuId = trim($skuId);
				$dfQty = trim($dfQty);
				$dfLotBarcode = trim($dfLotBarcode);
				$dfLotBarcode1 = trim($dfLotBarcode1);

				$dataList [] = [
					'skuId' => $skuId,
					'dfQty' => $dfQty,
					'dfLotBarcode' => $dfLotBarcode,
					'dfLotBarcode1' => $dfLotBarcode1,
				];
			}
		}

		foreach ($dataList as $k => &$item) {
			$stockCount = Stock::find()
							   ->andWhere(['client_id' => 2,
								   'status' => [17, 15],
								   'field_extra1' => $item['skuId']
							   ])
							   ->count();

			$item['lostQty'] = $stockCount;
//            $dataList[$k]['lostQty'] = $stockCount;
		}

		foreach ($dataList as $k => $item) {
			echo $k . "; " . $item['skuId'] . "; " . $item['dfQty'] . "; " . $item['dfLotBarcode'] . "; " . $item['dfLotBarcode1'] . "; " . $item['lostQty'] . ";" . "<br />";
		}

		VarDumper::dump($dataList, 10, true);
		die();

		return 'ok';
	}

	// Проверяем что отгружено у нас и что отгружено у дефакто из файла полученного от дефакто
	public function actionCheckForInventory2()
	{

		// other/one/check-for-inventory2
//        die('other/one/check-for-inventory2');

		$rootPathList = [];
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/NEW-DIFF-STOCK/b2b/11-01-2021/b2b-diff-inventory11-01-2021.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/NEW-DIFF-STOCK/b2b/11-01-2021/test.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 1490; $i++) {
//            for ($i = $start; $i <= 137; $i++) {

				$storeName = $excelActive->getCell('D' . $i)->getValue();
				$skuId = $excelActive->getCell('F' . $i)->getValue();
				$lotBarcode = $excelActive->getCell('H' . $i)->getValue();
				$partyNumber = $excelActive->getCell('I' . $i)->getValue();
				$waybillNumber = $excelActive->getCell('M' . $i)->getValue();
				$SKUQuantity = $excelActive->getCell('O' . $i)->getValue();

				$skuId = trim($skuId);
				$lotBarcode = trim($lotBarcode);
				$partyNumber = trim($partyNumber);
				$waybillNumber = trim(trim($waybillNumber, '-'));
				$storeName = trim($storeName);
				$SKUQuantity = trim($SKUQuantity);

				for ($q = 1; $q <= $SKUQuantity; $q++) {
					$dataList [] = [
						'skuId' => $skuId,
						'lotBarcode' => $lotBarcode,
						'partyNumber' => $partyNumber,
						'waybillNumber' => $waybillNumber,
						'storeName' => $storeName,
						'SKUQuantity' => $SKUQuantity,
						'stockId' => 0,
						'stockStatus' => 0,
						'stockPickingList' => 0,
						'stockOutboundBox' => 0,
						'stockStatusAvailability' => 0,
						'index' => 0,
					];
				}
			}
		}


		$stockIdList = [];
		foreach ($dataList as $k => $item) {
			$stockList = Stock::find()
							  ->andWhere(['client_id' => 2,
								  'field_extra1' => $item['skuId']
							  ])
							  ->andWhere('outbound_picking_list_barcode LIKE "%-' . $item['partyNumber'] . '-%"')
//                         ->andWhere(['NOT IN', 'id', $stockIdList])
							  ->all();
			$i = 0;

			foreach ($stockList as $stock) {
				$orderNumberInfo = explode('-', $stock->outbound_picking_list_barcode);
				$waybillNumber = $orderNumberInfo['1'] . '' . $orderNumberInfo['0'];
				$pos = strpos($item['waybillNumber'], $waybillNumber);

				if ($pos !== false && !in_array($stock->id, $stockIdList)) {
					$stockIdList[] = $stock->id;

					$dataList[$k]['stockId'] = $stock->id;
					$dataList[$k]['stockStatus'] = $stock->status;
					$dataList[$k]['stockPickingList'] = $stock->outbound_picking_list_barcode;
					$dataList[$k]['stockOutboundBox'] = $stock->box_barcode;
					$dataList[$k]['stockStatusAvailability'] = $stock->status_availability;
					$dataList[$k]['index'] = ++$i;
					break;
				}
			}
		}

		$row = '#' . "; " .
			'storeName' . "; " .
			'skuId' . "; " .
			'lotBarcode' . "; " .
			'SKUQuantity' . "; " .
			'partyNumber' . "; " .
			'waybillNumber' . "; " .
			'stockId' . ";" .
			'stockStatus' . ";" .
			'stockPickingList' . ";" .
			'stockOutboundBox' . ";" .
			'stockStatusAvailability' . ";" .
			'index' . ";" .
			"\n";

		file_put_contents("xxxx.lolo", $row);

		foreach ($dataList as $k => $item) {
			$row = $k . "; " .
				$item['storeName'] . "; " .
				$item['skuId'] . "; " .
				$item['lotBarcode'] . "; " .
				$item['SKUQuantity'] . "; " .
				$item['partyNumber'] . "; " .
				$item['waybillNumber'] . "; " .
				$item['stockId'] . ";" .
				$item['stockStatus'] . ";" .
				$item['stockPickingList'] . ";" .
				$item['stockOutboundBox'] . ";" .
				$item['stockStatusAvailability'] . ";" .
				$item['index'] . ";" .
				"\n";
			file_put_contents("xxxx.lolo", $row, FILE_APPEND);
		}

		$dataListFormat = ArrayHelper::index($dataList, null, 'skuId');

		$stockIdIn = [];
		$stockDiff = [];

		foreach ($dataListFormat as $skuId => $items) {

			$stockIdIn[$skuId]['defactoOutbound'] = ArrayHelper::getColumn($items, 'stockId');

			$stockIdIn[$skuId]['lost'] = Stock::find()->select('id')
											  ->andWhere(['client_id' => 2, 'field_extra1' => $skuId, 'status' => [17, 15]])
											  ->column();

			$stockIdIn[$skuId]['available'] = Stock::find()->select('id')
												   ->andWhere(['client_id' => 2, 'field_extra1' => $skuId, 'status_availability' => 2])
												   ->column();

			$stockIdIn[$skuId]['diff'] = Stock::find()->select('id')
											  ->andWhere(['client_id' => 2, 'field_extra1' => $skuId])
											  ->andWhere(['NOT IN', 'id', $stockIdIn[$skuId]['defactoOutbound']])
											  ->andWhere(['NOT IN', 'id', $stockIdIn[$skuId]['lost']])
											  ->andWhere(['NOT IN', 'id', $stockIdIn[$skuId]['available']])
											  ->column();

			$stockIdIn[$skuId]['diffStr'] = implode(',', $stockIdIn[$skuId]['diff']);

			$stockDiff[$skuId] = $stockIdIn[$skuId]['diff'];
		}

//        VarDumper::dump($dataList,10,true);
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//        VarDumper::dump($dataListFormat,10,true);
		VarDumper::dump($stockDiff, 10, true);
//        die();

		foreach ($stockDiff as $skuId => $stockIdList) {

			$stockList = Stock::find()
//                ->andWhere(['client_id'=>2,
//                    'field_extra1'=>$skuId
//                ])
							  ->andWhere(['id' => $stockIdList])
							  ->all();


			foreach ($stockList as $k => $stock) {
				$row =
					$skuId . "; " .
					$stock->outbound_picking_list_barcode . "; " .
					$stock->product_barcode . "; " .
//                    "\n";
					"<br />";
				echo $row;
//                file_put_contents("difffffff.csv",$row,FILE_APPEND);
			}
		}


		return $this->render('index');
	}


	//
	public function actionResendReturnFromFileReport3()
	{
		//die(' other/one/resend-return-from-file-report3 ');

		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/12-01-2021/resend-by-skuId.xlsx';

		$dataListAll = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 59; $i++) {
				$skuId = $excelActive->getCell('B' . $i)->getValue();
				$skuId = trim($skuId);
				$list[] = $skuId;
			}
		}

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//            VarDumper::dump($list,10,true);
//            die;
		$fileName = 'resend-return-from-file-report-3' . time() . '.csv';
		file_put_contents($fileName, "");

		$row = "product_barcode" . "; "
			. "client_box_barcode" . "; "
			. "expected_qty" . "; "
			. "accepted_qty" . "; "
			. "skuId" . "; "
			. "AppointmentBarcode" . "; "
			. "FromBusinessUnitId" . "; ";

		file_put_contents($fileName, $row . "\n", FILE_APPEND);

		$groupList = [];
		foreach ($list as $i => $skuId) {

			$returnOrderItemProducts = ReturnOrderItemProduct::find()
															 ->andWhere(['field_extra2' => $skuId, 'status' => 9])
															 ->all();

			$stockAvailable = Stock::find()
								   ->andWhere(['client_id' => 2, 'field_extra1' => $skuId, 'status_availability' => 2])
								   ->one();

			if ($returnOrderItemProducts) {
				foreach ($returnOrderItemProducts as $returnOrderItemProduct) {
					if ($returnOrderItemProduct->client_box_barcode == $stockAvailable->inbound_client_box) {
						continue;
					}

					$productSerializeDataDefault = CrossDockItemService::extractJsonData($returnOrderItemProduct->product_serialize_data);
					$SkuId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.SkuId');
					$AppointmentBarcode = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.AppointmentBarcode');
					$FromBusinessUnitId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.FromBusinessUnitId');
//
					$row = $returnOrderItemProduct->product_barcode . "; "
						. $returnOrderItemProduct->client_box_barcode . "; "
						. $returnOrderItemProduct->expected_qty . "; "
						. $returnOrderItemProduct->accepted_qty . "; "
						. $returnOrderItemProduct->field_extra2 . "; "
						. $AppointmentBarcode . "; "
						. $FromBusinessUnitId . "; ";

					file_put_contents($fileName, $row . "\n", FILE_APPEND);


//                    $groupList[$SkuId][] = [
//                        'product_barcode' => $returnOrderItemProduct->product_barcode,
//                        'client_box_barcode' => $returnOrderItemProduct->client_box_barcode,
//                        'expected_qty' => $returnOrderItemProduct->expected_qty,
//                        'accepted_qty' => $returnOrderItemProduct->accepted_qty,
//                        'skuId' => $returnOrderItemProduct->field_extra2,
//                        'AppointmentBarcode' => $AppointmentBarcode,
//                        'FromBusinessUnitId' => $FromBusinessUnitId,
//                    ];

					$tmp = new ReturnTmpOrder();
//                    $tmp->sendDataToAPI($returnOrderItemProduct);
				}
			}

//            VarDumper::dump($returnOrderItemProducts->product_barcode,10,true);
//            VarDumper::dump(empty($returnOrderItemProducts->product_barcode),10,true);
//            die;
			$isNotFind = '';
			if (empty($returnOrderItemProducts)) {
				$isNotFind = 'LOST';
			}

//            $tmp = new ReturnTmpOrder();
			//$tmp->sendDataToAPI($returnOrderItemProducts);
			echo $skuId . " - " . $isNotFind . " - " . $i . "<br />";
//            die;
		}

		return $this->render('index');
	}

	public function actionAutoScannedOutbound()
	{

		// other/one/auto-scanned-outbound
		die('other/one/auto-scanned-outbound');

		$outbound_order_id = 49767;
		$order = OutboundOrder::find()->where(['id' => $outbound_order_id])->one();
		$order->accepted_qty = $order->allocated_qty;
		$order->begin_datetime = time();
		$order->end_datetime = time();
		$order->status = Stock::STATUS_OUTBOUND_SCANNED;
		$order->save(false);

		$all = OutboundOrderItem::find()->where(['outbound_order_id' => $outbound_order_id])->all();

		$m3BoxValue = 0.096;

		foreach ($all as $item) {
			$item->accepted_qty = $item->allocated_qty;
			$item->status = Stock::STATUS_OUTBOUND_SCANNED;
			$item->begin_datetime = time();
			$item->end_datetime = time();
			$item->save(false);

			$stockOne = Stock::find()->andWhere(['outbound_order_id' => $outbound_order_id, 'outbound_order_item_id' => $item->id])->one();

			$stockOne->scan_out_employee_id = 1;
			$stockOne->scan_out_datetime = time();
			$stockOne->box_size_m3 = $m3BoxValue;
			$stockOne->box_size_barcode = BarcodeManager::getIntegerM3($m3BoxValue);
			$stockOne->box_barcode = $stockOne->inbound_client_box;
			$stockOne->status = Stock::STATUS_OUTBOUND_SCANNED;
			$stockOne->save(false);
		}
		return '-OK-';
	}

	public function actionResendOutboundAllOrder()
	{
		// /other/one/resend-outbound-all-order

		die('/other/one/resend-outbound-all-order');

		$apiResult = [];
		$rowsDataForAPI = [];
		$outboundId = 60596;
		$outboundOrderModel = OutboundOrder::findOne($outboundId);

		$outboundOrderItems = OutboundOrderItem::find()
											   ->andWhere(['outbound_order_id' => $outboundOrderModel->id])
											   ->andWhere('accepted_qty > 0')
											   ->all();

		if ($outboundOrderItems) {

			$mappingOurBobBarcodeToDefacto = [];
			$mappingWaybillNumber = [];
			$boxCountStep = 1;

			foreach ($outboundOrderItems as $outboundOrderItem) {
				$stocks = Stock::find()->select('product_barcode, inbound_order_id, count(id) as accepted_qty, box_barcode, box_size_m3, box_size_barcode, outbound_order_item_id')
							   ->andWhere(['outbound_order_item_id' => $outboundOrderItem->id]) // ,'status'=>[Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL, Stock::STATUS_OUTBOUND_SCANNED]
							   ->andWhere(['status' => [
						Stock::STATUS_OUTBOUND_SCANNED,
						Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
						Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
					]
					])
							   ->groupBy('product_barcode, box_barcode, outbound_order_item_id')
							   ->orderBy('box_barcode')
							   ->asArray()
							   ->all();

				if ($tmp = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks, $outboundOrderItem)) {
					foreach ($tmp as $tmpAPIValue) {
						if (!isset($mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']])) {
							$mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']] = (new OutboundBoxService())->getClientBoxByBarcode($tmpAPIValue['LcBarcode']);
							$mappingWaybillNumber[$tmpAPIValue['LcBarcode']] = DeFactoSoapAPIV2Manager::makeWaybillNumber($outboundOrderModel, $boxCountStep);
							$boxCountStep++;
							OutboundBoxService::addLcAndWaybillByBoxBarcode($tmpAPIValue['LcBarcode'], $mappingOurBobBarcodeToDefacto[$tmpAPIValue['LcBarcode']], $mappingWaybillNumber[$tmpAPIValue['LcBarcode']]);
						}
					}
				}
				foreach ($stocks as $keyStock => $stock) {
					$stocks[$keyStock]['box_barcode'] = $mappingOurBobBarcodeToDefacto[$stock['box_barcode']];
					$stocks[$keyStock]['our_box_barcode'] = $stock['box_barcode'];
					$stocks[$keyStock]['waybill_number'] = $mappingWaybillNumber[$stock['box_barcode']];
				}
				$outboundPreparedDataRows = DeFactoSoapAPIV2Manager::preparedSendOutBoundFeedBackData($stocks, $outboundOrderItem);
				if ($outboundPreparedDataRows) {
					foreach ($outboundPreparedDataRows as $outboundPreparedDataRow) {
						unset($outboundPreparedDataRow['our_box_barcode']);
						$rowsDataForAPI['OutBoundFeedBackThreePLResponse'][] = $outboundPreparedDataRow;
					}
				}
			}

			foreach ($rowsDataForAPI['OutBoundFeedBackThreePLResponse'] as $k => $value) {
				$rowsDataForAPI['OutBoundFeedBackThreePLResponse'][$k]["LotOrSingleQuantity"] = 0;
			}

			if (!empty($rowsDataForAPI)) {
				$api = new DeFactoSoapAPIV2Manager();
//                $apiResult = $api->SendOutBoundFeedBackData($rowsDataForAPI);
//                file_put_contents('SendOutBoundFeedBackDataSerialize-ReSend.csv',serialize($rowsDataForAPI)."\n",FILE_APPEND);
			}
		}

		VarDumper::dump($apiResult, 10, true);
		echo "<br />";
		echo "<br />";
		VarDumper::dump($rowsDataForAPI, 10, true);
		echo "<br />";
		echo "<br />";

		echo "<br />";
		VarDumper::dump(serialize($rowsDataForAPI), 10, true);
		echo "<br />";
		echo "<br />";

//            $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
//            $outboundOrderModel->save(false);
//        }
		//E: если накладная для DeFacto. отправляем отчет по отгруженным товарам

		die;
		return $this->render('index');
	}


	//
	public function actionAddLc()
	{
		die(' other/one/add-lc ');

//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/NEW-DIFF-STOCK/b2b/18-01-2021/1223.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/NEW-DIFF-STOCK/b2b/18-01-2021/add-lc-18-01-2021-x.xlsx';

		$dataListAll = [];
		$list = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 2;
			for ($i = $start; $i <= 1224; $i++) {
				$oldLc = $excelActive->getCell('A' . $i)->getValue();
				$newLc = $excelActive->getCell('B' . $i)->getValue();

				$oldLc = trim($oldLc);
				$newLc = trim($newLc);
				$list[] = [
					'oldLC' => $oldLc,
					'newLC' => $newLc,
				];
			}
		}

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//            VarDumper::dump($list,10,true);
//            die;
//        $apiManager = new DeFactoSoapAPIV2Manager();
//        $result = $apiManager->CreateLcBarcode(count($list) + 1);

		foreach ($list as $i => $lc) {

			$stockListByLc = Stock::find()
								  ->andWhere(['client_id' => 2, 'outbound_order_id' => 49767, 'box_barcode' => $lc['oldLC']])
								  ->one();

//            echo count($stockListByLc)." : ". $lc['oldLC'] ."<br />";

			$stockListByLc->product_model = $stockListByLc->box_barcode;
			$stockListByLc->box_barcode = $lc['newLC'];
			//$stockListByLc->save(false);

//            $dataListAll[] = [
//                'oldLC'=> $lc,
//                'newLC'=> $result['Data'][$i],
//            ];
//
//            $row = $lc.";".
//                   $result['Data'][$i].";"
//                    ;
//
//            file_put_contents("add-lc-18-01-2021.csv",$row."\n",FILE_APPEND);
		}

		return $this->render('index');
	}


	//
	public function actionReturnCheckFromFile()
	{
		die(' other/one/return-check-from-file ');

//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/26-01-2021/Остатки доп склад.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/resend-return/26-01-2021/ошибки 2.xlsx';

		$dataListAll = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 14; $i++) {
				$clientBox = $excelActive->getCell('A' . $i)->getValue();
				$clientBox = trim($clientBox);
				$list[] = $clientBox;
			}
		}

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
//            VarDumper::dump($list,10,true);
//            die;
		$fileName = 'resend-return-from-file-report-2' . time() . '.csv';
		file_put_contents($fileName, "");

		$row = "product_barcode" . "; "
			. "client_box_barcode" . "; "
			. "expected_qty" . "; "
			. "accepted_qty" . "; "
			. "skuId" . "; "
			. "AppointmentBarcode" . "; "
			. "FromBusinessUnitId" . "; ";

		file_put_contents($fileName, $row . "\n", FILE_APPEND);

		$groupList = [];
		foreach ($list as $i => $box) {

//            $returnOrderItemProducts = ReturnOrderItemProduct::find()
//                ->andWhere(['client_box_barcode' => $box, 'status' => 9])
//                ->all();

			$returnOrderItemProducts = Stock::find()
											->andWhere(['inbound_client_box' => $box, 'client_id' => 2])
											->all();

			if ($returnOrderItemProducts) {
//                if(count($returnOrderItemProducts) > 1) {
//                    continue;
//                }

//                echo " ".$i." : ".count($returnOrderItemProducts)."----<br />";

				foreach ($returnOrderItemProducts as $k => $stock) {
					$row = $stock->product_barcode . "; "
						. $stock->inbound_client_box . "; "
						. $stock->field_extra1 . "; "
						. $stock->status . "; "
						. $stock->status_availability . "; "
						. $stock->secondary_address . "; "
						. $stock->id . "; ";

					echo $row . "<br />";
//
					$stock->box_barcode = '';
					$stock->box_size_barcode = '';
					$stock->box_kg = '';
					$stock->box_size_m3 = '';
					$stock->outbound_order_id = '0';
					$stock->outbound_picking_list_id = '0';
					$stock->outbound_picking_list_barcode = '';
					$stock->scan_out_datetime = '';
					$stock->scan_out_employee_id = '0';
					$stock->status = Stock::STATUS_NOT_SET;
					$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
//                    $stock->save(false);

//                    $productSerializeDataDefault = CrossDockItemService::extractJsonData($returnOrderItemProduct->product_serialize_data);
//                    $SkuId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.SkuId');
//                    $AppointmentBarcode = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.AppointmentBarcode');
//                    $FromBusinessUnitId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.FromBusinessUnitId');
////
//                    $row = $returnOrderItemProduct->product_barcode . "; "
//                        . $returnOrderItemProduct->client_box_barcode . "; "
//                        . $returnOrderItemProduct->expected_qty . "; "
//                        . $returnOrderItemProduct->accepted_qty . "; "
//                        . $returnOrderItemProduct->field_extra2 . "; "
//                        . $AppointmentBarcode . "; "
//                        . $FromBusinessUnitId . "; ";
//
//                    file_put_contents($fileName, $row . "\n", FILE_APPEND);
//
//                    $groupList[$SkuId][] = [
//                        'product_barcode' => $returnOrderItemProduct->product_barcode,
//                        'client_box_barcode' => $returnOrderItemProduct->client_box_barcode,
//                        'expected_qty' => $returnOrderItemProduct->expected_qty,
//                        'accepted_qty' => $returnOrderItemProduct->accepted_qty,
//                        'skuId' => $returnOrderItemProduct->field_extra2,
//                        'AppointmentBarcode' => $AppointmentBarcode,
//                        'FromBusinessUnitId' => $FromBusinessUnitId,
//                    ];

//                    $tmp = new ReturnTmpOrder();
					//$tmp->sendDataToAPI($returnOrderItemProduct);
//                    echo $k."<br />";
//                    break;
				}
			}

//            VarDumper::dump($returnOrderItemProducts->product_barcode,10,true);
//            VarDumper::dump(empty($returnOrderItemProducts->product_barcode),10,true);
//            die;
			$isNotFind = '';
			if (empty($returnOrderItemProducts)) {
				$isNotFind = 'LOST';
			}

//            $tmp = new ReturnTmpOrder();
			//$tmp->sendDataToAPI($returnOrderItemProducts);
			echo $box . " - " . $isNotFind . " - " . $i . "<br />";
//            die;
		}

		return $this->render('index');
	}

	//
	public function actionCreateApiTestData()
	{
		//die(' other/one/create-api-test-data ');

		$orderList = OutboundOrder::find()->andWhere(['parent_order_number' => 11557791])->all();

		foreach ($orderList as $order) {
			$orderListItems = OutboundOrderItem::find()->andWhere(['outbound_order_id' => $order->id])->all();
			foreach ($orderListItems as $item) {

				$productSerializeDataDefault = CrossDockItemService::extractJsonData($item->product_serialize_data);

				VarDumper::dump($productSerializeDataDefault, 10, true);

				$OutboundId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.OutboundId');
				$BatchId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.BatchId');
				$ReservationId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.ReservationId');
				$SkuId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.SkuId');
				$Quantity = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.Quantity');
				$Status = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.Status');
				$ToBusinessUnitId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.ToBusinessUnitId');
				$CargoBusinessUnitId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.CargoBusinessUnitId');

				$row = ' $row = new \stdClass();' . "\n";
				$row .= ' $row->OutboundId =' . $OutboundId . "; " . "\n";
				$row .= ' $row->BatchId =' . $BatchId . "; " . "\n";
				$row .= ' $row->ReservationId =' . $ReservationId . "; " . "\n";
				$row .= ' $row->SkuId =' . $SkuId . "; " . "\n";
				$row .= ' $row->Quantity =' . $Quantity . "; " . "\n";
				$row .= ' $row->Status =' . $Status . "; " . "\n";
				$row .= ' $row->ToBusinessUnitId =' . $ToBusinessUnitId . "; " . "\n";
				$row .= ' $row->CargoBusinessUnitId =' . $CargoBusinessUnitId . "; " . "\n" . "\n";
				$row .= ' $dataList[] = $row;' . "\n" . "\n";


				file_put_contents("create-api-test-data-11557791.php", $row . "\n", FILE_APPEND);
			}
//            die;
		}

//$productSerializeDataDefault = CrossDockItemService::extractJsonData($returnOrderItemProduct->product_serialize_data);
//$SkuId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.SkuId');
//$AppointmentBarcode = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.AppointmentBarcode');
//$FromBusinessUnitId = ArrayHelper::getValue($productSerializeDataDefault, 'apiLogValue.FromBusinessUnitId');
////
//$row = $returnOrderItemProduct->product_barcode . "; "
//    . $returnOrderItemProduct->client_box_barcode . "; "
//    . $returnOrderItemProduct->expected_qty . "; "
//    . $returnOrderItemProduct->accepted_qty . "; "
//    . $returnOrderItemProduct->field_extra2 . "; "
//    . $AppointmentBarcode . "; "
//    . $FromBusinessUnitId . "; ";
//
//file_put_contents($fileName, $row . "\n", FILE_APPEND);
//
//$groupList[$SkuId][] = [
//    'product_barcode' => $returnOrderItemProduct->product_barcode,
//    'client_box_barcode' => $returnOrderItemProduct->client_box_barcode,
//    'expected_qty' => $returnOrderItemProduct->expected_qty,
//    'accepted_qty' => $returnOrderItemProduct->accepted_qty,
//    'skuId' => $returnOrderItemProduct->field_extra2,
//    'AppointmentBarcode' => $AppointmentBarcode,
//    'FromBusinessUnitId' => $FromBusinessUnitId,
//];


		return $this->render('index');
	}

	public function actionFindCrossDockByLot()
	{
		//die(' other/one/find-cross-dock-by-lot ');

		$lotList = [
			2300020025082,
			2300021613738,
			2300022603561,
			2300021611444,
			2300022300187,
			2300021621078,
			2300021862570,
		];
		$clientStoreArray = TLHelper::getStoreArrayByClientID();
		foreach ($lotList as $lot) {
			$crossDockItems = CrossDockItems::find()->andWhere(['field_extra2' => $lot])->all();
			foreach ($crossDockItems as $crossDockItem) {

				$crossDock = CrossDock::find()->andWhere(['id' => $crossDockItem->cross_dock_id])->one();

				$row = $crossDockItem->box_barcode . ';' .
					$crossDockItem->field_extra2 . ';' .
					$crossDockItem->field_extra3 . ';' .
					$clientStoreArray[$crossDock->to_point_id] . ';'
					. "\n";

				echo $row . "<br />";
//                file_put_contents('FindCrossDockByLot.csv', $row, FILE_APPEND);
			}
		}
	}


	public function actionChangeBoxAddress()
	{
		die('other/one/change-box-address');

		$rootPathList = [];
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/NEW-DIFF-STOCK/b2b/17-02-2021/44.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/NEW-DIFF-STOCK/b2b/17-02-2021/17.xlsx';

		$dataList = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 17; $i++) {
				$boxBarcode = $excelActive->getCell('A' . $i)->getValue();
				$lcBarcode = $excelActive->getCell('B' . $i)->getValue();
				$lotBarcode = $excelActive->getCell('C' . $i)->getValue();

				$boxBarcode = trim($boxBarcode);
				$lcBarcode = trim($lcBarcode);
				$lotBarcode = trim($lotBarcode);

				$dataList [] = [
					'boxBarcode' => $boxBarcode,
					'lcBarcode' => $lcBarcode,
					'lotBarcode' => $lotBarcode,
				];
			}
		}

		foreach ($dataList as $x => $data) {
			$stockList = Stock::find()->andWhere([
				'inbound_client_box' => $data['lcBarcode'],
				'field_extra1' => $data['lotBarcode'],
//                'product_barcode'=>$data['lotBarcode'],
				'is_product_type' => 2,
			])->all();

			if (empty($stockList)) {
				echo $x . "; " .
					$data['boxBarcode'] . "; " .
					$data['lcBarcode'] . "; " .
					$data['lotBarcode'] . "; " .
					"-----<br />";
				continue;
			}

//            VarDumper::dump(count($stockList),10,true);//."<br />";


			foreach ($stockList as $stock) {

				echo $x . "; " .
					$stock->status_availability . "; " .
					$stock->status . "; " .
					"-okokok<br />";

				$stock->product_model = $stock->primary_address;
				$stock->primary_address = $data['boxBarcode'];
				$stock->status_availability = 2;
				$stock->status = 9;
				$stock->system_status = 'resetManual17022021';
				//$stock->save(false);
			}
		}

		VarDumper::dump($dataList, 10, true);
		echo "<br />";
		echo "<br />";
		die;

		return 'ok';
	}

	public function actionInboundDiffAddToStock($inboundId = -1)
	{
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		//die('other/one/inbound-diff-add-to-stock DIE');
		$inboundId = 96739;
		file_put_contents("B2B-inboundItemList-full-" . $inboundId . ".csv", "");
		$inboundItemList = InboundOrderItem::find()
										   ->andWhere(['inbound_order_id' => $inboundId])
										   ->all();

		foreach ($inboundItemList as $rowItem) {
			$count = Stock::find()->andWhere([
				'inbound_order_id' => $rowItem->inbound_order_id,
				'inbound_client_box' => $rowItem->box_barcode,
				'product_barcode' => $rowItem->product_barcode,
			])->count();

			$row = $rowItem->box_barcode . ';' . $rowItem->product_barcode . ";" . $rowItem->expected_qty . ';' . $rowItem->accepted_qty . ';' . $count . "\n";//"<br />";

			if ($count != $rowItem->accepted_qty) {
				$onStockList = Stock::find()->andWhere([
					'inbound_order_id' => $rowItem->inbound_order_id,
					'inbound_client_box' => $rowItem->box_barcode,
					'product_barcode' => $rowItem->product_barcode,
				])->all();

				if (empty($onStockList)) {
					$row = $rowItem->box_barcode . ';' . $rowItem->product_barcode . ";" . '-' . ';' . '-' . "\n";//"<br />";
					echo $row . "<br />";
					file_put_contents("B2B-inboundItemList-full-" . $inboundId . ".csv", $row, FILE_APPEND);


					for ($i = 1; $i <= $rowItem->accepted_qty; $i++) {
						$stockData = new \stdClass();
						$stockData->order_number = $rowItem->inbound_order_id;
						$stockData->product_barcode = $rowItem->product_barcode;
						$stockData->box_barcode = $rowItem->box_barcode;
						$stockData->client_box_barcode = $rowItem->box_barcode;
						//$this->addStock($stockData);
					}

				} else {
					foreach ($onStockList as $stockITem) {
						$row = $rowItem->box_barcode . ';' . $rowItem->product_barcode . ";" . $stockITem->primary_address . ';' . $stockITem->secondary_address . "\n";//"<br />";
						echo $row . "++++++++<br />";
						file_put_contents("B2B-inboundItemList-full-" . $inboundId . ".csv", $row, FILE_APPEND);
					}

					$toQty = $rowItem->accepted_qty - count($onStockList);
					for ($i = 1; $i <= $toQty; $i++) {
						$stockData = new \stdClass();
						$stockData->order_number = $rowItem->inbound_order_id;
						$stockData->product_barcode = $rowItem->product_barcode;
						$stockData->box_barcode = $rowItem->box_barcode;
						$stockData->client_box_barcode = $rowItem->box_barcode;
						$this->addStock($stockData);
					}
				}
			}
		}

		return $this->render('index');
	}


	private function addStock($data)
	{

		$stock = new Stock();
		$attributes = [
			'scan_in_datetime' => time(),
			'client_id' => 2,
			'inbound_order_id' => $data->order_number,
			'product_barcode' => $data->product_barcode,
			'primary_address' => $data->box_barcode,
			'inbound_client_box' => $data->client_box_barcode,
			'status' => Stock::STATUS_INBOUND_CONFIRM,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
			'system_status' => 'addManual17022021-2',
			'system_status_description' => 'добавил руками потерянные товары-2',
		];
		$stock->setAttributes($attributes, false);
		//$stock->save(false);
	}


	public function actionAddInventoryPlusesLot()
	{
		// /other/one/add-inventory-pluses-lot
		die("actionAddInventoryPlus V 3 - die begin");

		$rootPathList = [];
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/add-product/b2b/inventory/22-02-2021/add-plusses-lot2.xlsx';
////        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/add-product/b2b/inventory/22-02-2021/add-38.xlsx';
//        $rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/add-product/b2b/inventory/16-02-2022/plus_after_inventory_16_02_2022.xlsx';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/add-product/b2b/inventory/09-12-2023/lots-pluse.xlsx';

		$products = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet(1);

			$start = 1;
			for ($i = $start; $i <= 207; $i++) {
				$lotBarcode = $excelActive->getCell('A' . $i)->getValue();
				$boxBarcode = $excelActive->getCell('B' . $i)->getValue();
				$lcBarcode = '-1';

				$boxBarcode = trim($boxBarcode);
				$lotBarcode = trim($lotBarcode);
				$lcBarcode = trim($lcBarcode);

				if (empty($boxBarcode) || empty($lcBarcode) || empty($lotBarcode)) {
					continue;
				}

				$products [] = [
					'boxBarcode' => $boxBarcode,
					'lotBarcode' => $lotBarcode,
					'lcBarcode' => $lcBarcode,
				];
			}
		}

		foreach ($products as $k => $barcode) {
			$products[$k]['lcBarcode'] = (string)Stock::find()->select('inbound_client_box')->andWhere(['client_id' => Client::CLIENT_DEFACTO, 'product_barcode' => $barcode['lotBarcode']])->scalar();
//            $products[$k]['lotBarcode'] = Stock::find()->select('product_barcode')->andWhere(['client_id'=>2,'inbound_client_box'=>$barcode['lcBarcode']])->scalar();
			//$products[$k]['lotBarcode'] = ReturnOrderItemProduct::find()->select('product_barcode')->andWhere(['client_box_barcode'=>$barcode['lcBarcode']])->scalar();
		}

		foreach ($products as $barcode) {
			$stock = new Stock();
			$stock->client_id = Client::CLIENT_DEFACTO;
			$stock->secondary_address = '';
			$stock->product_barcode = $barcode['lotBarcode'];
			$stock->primary_address = $barcode['boxBarcode'];
			$stock->inbound_client_box = $barcode['lcBarcode'];
			$stock->box_barcode = '';
			$stock->box_size_barcode = '';
			$stock->box_kg = '';
			$stock->box_size_m3 = '';
			$stock->outbound_order_id = '0';
			$stock->outbound_picking_list_id = '0';
			$stock->outbound_picking_list_barcode = '';
			$stock->scan_out_datetime = '';
			$stock->scan_out_employee_id = '0';
			$stock->status = Stock::STATUS_NOT_SET;
			$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
			$stock->system_status = 'inventory-plus-20231209';
			$stock->system_status_description = 'это плюсы после инвентаризации 2023 12 09';
//
//            $stock->system_status = 'inventory-plus-20220216-return';
//            $stock->system_status_description = 'это плюсы после инвентаризации 2022 02 16';
//            if (!$stock->save(false)) {
//                echo "NO " . $barcode['lotBarcode'];
//            }
//			echo $barcode['lotBarcode']."<br />";
		}

		VarDumper::dump($products, 10, true);
		die;
	}

	public function actionAddInventoryPlusesReturn()
	{
		// /other/one/add-inventory-pluses-return
		//die("actionAddInventoryPlus V 3 - die begin");

		$rootPathList = [];
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/add-product/b2b/inventory/16-02-2022/plus_after_inventory_16_02_2022.xlsx';

		$products = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(1);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 81; $i++) {
				$boxBarcode = $excelActive->getCell('A' . $i)->getValue();
				$lcBarcode = $excelActive->getCell('B' . $i)->getValue();
//                $lotBarcodeOrSkuId = $excelActive->getCell('C' . $i)->getValue();
				$lotBarcodeOrSkuId = "-1";

				$boxBarcode = trim($boxBarcode);
				$lotBarcodeOrSkuId = trim($lotBarcodeOrSkuId);
				$lcBarcode = trim($lcBarcode);

				if (empty($boxBarcode) || empty($lcBarcode) || empty($lotBarcodeOrSkuId)) {
					continue;
				}

				$products [] = [
					'boxBarcode' => $boxBarcode,
					'lotBarcode' => "",
					'lcBarcode' => $lcBarcode,
				];
			}
		}

		foreach ($products as $k => $barcode) {
//            $products[$k]['lcBarcode'] = (string)Stock::find()->select('inbound_client_box')->andWhere(['client_id'=>2,'product_barcode'=>$barcode['lotBarcode']])->scalar();
//            $products[$k]['lotBarcode'] = Stock::find()->select('product_barcode')->andWhere(['client_id'=>2,'inbound_client_box'=>$barcode['lcBarcode']])->scalar();
			$products[$k]['lotBarcode'] = ReturnOrderItemProduct::find()->select('product_barcode')->andWhere(['client_box_barcode' => $barcode['lcBarcode']])->scalar();
		}

		foreach ($products as $barcode) {
			$stock = new Stock();
			$stock->client_id = Client::CLIENT_DEFACTO;
			$stock->secondary_address = '';
			$stock->product_barcode = $barcode['lotBarcode'];
			$stock->primary_address = $barcode['boxBarcode'];
			$stock->inbound_client_box = $barcode['lcBarcode'];
			$stock->box_barcode = '';
			$stock->box_size_barcode = '';
			$stock->box_kg = '';
			$stock->box_size_m3 = '';
			$stock->outbound_order_id = '0';
			$stock->outbound_picking_list_id = '0';
			$stock->outbound_picking_list_barcode = '';
			$stock->scan_out_datetime = '';
			$stock->scan_out_employee_id = '0';
			$stock->status = Stock::STATUS_NOT_SET;
			$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
			$stock->system_status = 'inventory-20220216-lost';
			$stock->system_status_description = 'не нашли после инвентаризации 2022 02 16';
//            if (!$stock->save(false)) {
//                echo "NO " . $barcode['lotBarcode'];
//            }
			echo $barcode['lotBarcode'] . "<br />";
		}

		VarDumper::dump($products, 10, true);
		die;
	}

	public function actionAddInventoryReturn()
	{
		// /other/one/add-inventory-return

		die("other/one/add-inventory-return - die begin");

		$rootPathList = [];
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/add-product/b2b/inventory/22-02-2021/add-no-fb-return.xlsx';

		$products = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 76; $i++) {
				$ttn = $excelActive->getCell('A' . $i)->getValue();
				$boxBarcode = $excelActive->getCell('B' . $i)->getValue();
				$lcBarcode = $excelActive->getCell('C' . $i)->getValue();
				$placeAddress = $excelActive->getCell('D' . $i)->getValue();
				$lotBarcode = '';

				$boxBarcode = trim($boxBarcode);
				$lotBarcode = trim($lotBarcode);
				$lcBarcode = trim($lcBarcode);
				$placeAddress = trim($placeAddress);

//                if(empty($boxBarcode) || empty($lotBarcode)) {
//                    continue;
//                }
				$products [] = [
					'boxBarcode' => $boxBarcode,
					'lotBarcode' => $lotBarcode,
					'lcBarcode' => $lcBarcode,
					'placeAddress' => $placeAddress,
				];
			}
		}

		foreach ($products as $k => $barcode) {
			$products[$k]['lotBarcode'] = Stock::find()->select('product_barcode')->andWhere(['client_id' => 2, 'inbound_client_box' => $barcode['lcBarcode']])->scalar();
		}

		foreach ($products as $barcode) {
			$stock = new Stock();
			$stock->client_id = Client::CLIENT_DEFACTO;
//            $stock->secondary_address = '';
			$stock->product_barcode = $barcode['lotBarcode'];
			$stock->primary_address = $barcode['boxBarcode'];
			$stock->secondary_address = $barcode['placeAddress'];
			$stock->inbound_client_box = $barcode['lcBarcode'];
			$stock->box_barcode = '';
			$stock->box_size_barcode = '';
			$stock->box_kg = '';
			$stock->box_size_m3 = '';
			$stock->outbound_order_id = '0';
			$stock->outbound_picking_list_id = '0';
			$stock->outbound_picking_list_barcode = '';
			$stock->scan_out_datetime = '';
			$stock->scan_out_employee_id = '0';
			$stock->status = Stock::STATUS_NOT_SET;
			$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
//            $stock->system_status = 'inventory-plus-20210222Return';
			$stock->system_status = 'noReturnFB20210222';
			$stock->system_status_description = 'это возвраты которые были размещены по фидбеки по ним не были отправлены';
//            if (!$stock->save(false)) {
//                echo "NO " . $barcode['lotBarcode'];
//            }
		}

		VarDumper::dump($products, 10, true);
		die;
	}


	public function actionCreateLcBarcode()
	{
		die("other/one/create-lc-barcode - die begin");

		$apiManager = new DeFactoSoapAPIV2Manager();
		//$result = $apiManager->CreateLcBarcode(258);

		VarDumper::dump($result, 10, true);

		return '';
	}

	public function actionReturnProductToBackStock()
	{
		die("other/one/return-product-to-back-stock - die begin");

		$client_id = 2;
		$parentOrderNumber = [
			15772765,
			16046943
		];

		$orderList = OutboundOrder::find()->andWhere([
			'parent_order_number' => $parentOrderNumber,
			'client_id' => $client_id,
		])->all();

		foreach ($orderList as $order) {
			$stockItems = Stock::find()->andWhere([
				'client_id' => $client_id,
				'outbound_order_id' => $order->id,
			])->all();

			foreach ($stockItems as $stockItem) {

				$stockItem->inventory_primary_address = $stockItem->primary_address;
				$stockItem->inventory_secondary_address = $stockItem->secondary_address;
				$stockItem->primary_address = $stockItem->box_barcode;
				//$stockItem->field_extra3 = $stockItem->box_barcode;
				//$stockItem->save(false);
			}
		}


		foreach ($parentOrderNumber as $partyNumber) {
			$dataList = OutboundOrder::find()->select('parent_order_number, order_number, id')->andWhere(['parent_order_number' => $partyNumber])->all();
			$row = 1;
			foreach ($dataList as $value) {
				$partyNumber = $value['parent_order_number'];
				$orderNumber = $value['order_number'];
				$id = $value['id'];
				echo $row++ . ' ; ' . $client_id . ' ; ' . $partyNumber . ' ; ' . $orderNumber . ' ; ' . $id . "<br />";

				$oManager = new OutboundManager();
				$oManager->initBaseData($client_id, $partyNumber, $orderNumber);
				//$oManager->resetByPartyNumber();
			}
		}

		return 'OK';
	}

	public function actionAddOldAddressPalace()
	{
//		die("other/one/add-old-address-palace - die begin");

		$rootPathList = [];
//		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/accommodation-all-v1.xlsx';
//		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/accommodation-all.csv';
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/accommodation-return-move-from-to-all.csv';

		foreach ($rootPathList as $rootPath) {

//			$objReader = \PHPExcel_IOFactory::createReader('Excel2007');
//			$cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
//			$cacheSettings = array( ' memoryCacheSize ' => '16MB');
//			\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
//			$objReader->setReadDataOnly(true);
//			$excel = $objReader->load($rootPath);
//			$excel->setActiveSheetIndex(0);
//			$excelActive = $excel->getActiveSheet();

//			$start = 1;
//			for ($i = $start; $i <= 325304; $i++) {
//			for ($i = $start; $i <= 2; $i++) {
//				$fromBox = $excelActive->getCell('A' . $i)->getValue();
//				$toBox = $excelActive->getCell('B' . $i)->getValue();
//				$date = $excelActive->getCell('C' . $i)->getValue();
//
//				if(ChangeAddressPlaceService::isNotExist($fromBox,$toBox)) {
//					ChangeAddressPlaceService::add($fromBox,$toBox,strtotime($date));
//				};

//				echo $fromBox . " //////// " . $toBox . "======" . $date . " : ". strtotime($date) . " : ". date('Y-m-d',strtotime($date)) . " <br />";
//			}

			if (($handle = fopen($rootPath, "r")) !== false) {
				while (($data = fgetcsv($handle, 39433, ";")) !== false) {

					$fromBox = $data[1];
					$toBox = $data[2];
					//$date = $data[3];

					if (ChangeAddressPlaceService::isNotExist($fromBox, $toBox)) {
						ChangeAddressPlaceService::add($fromBox, $toBox);
					};
				}
			}

		}

		return 'OK';
	}

	public function actionReturnBoxStock()
	{
		die("other/one/return-box-stock die begin");

		$boxBarcodes = [
			'700001077859' => '800000270278',
			'700001077858' => '800000270279',
			'700001077857' => '800000270277',
			'700001077856' => '800000270287',
			'700001077855' => '800000270291',
			'700001077854' => '800000270296',
			'700001077853' => '800000270302',
			'700001077852' => '800000270304',
			'700001077851' => '800000270305',
			'700001077850' => '800000270281',
			'700001077849' => '800000270284',
			'700001077848' => '800000270285',
			'700001077847' => '800000270293',
			'700001077846' => '800000270297',
			'700001077845' => '800000270295',
			'700001077844' => '800000270282',
			'700001077843' => '800000270280',
			'700001077842' => '800000270294',
			'700001077841' => '800000270299',
			'700001077840' => '800000270298',
			'700001077839' => '800000270276',
			'700001077838' => '800000270283',
			'700001077837' => '800000270289',
			'700001077836' => '800000270292',
			'700001077835' => '800000270286',
			'700001077834' => '800000270303',
			'700001077833' => '800000270301',
			'700001077832' => '800000270300',
			'700001077831' => '800000270290'
		];
		$id = '53310';
		foreach ($boxBarcodes as $boxSeven => $box) {
//
			$stockAll = Stock::find()->andWhere([
				'outbound_order_id' => $id,
				'box_barcode' => $box,
			])
							 ->all();
			foreach ($stockAll as $stock) {
				$stock->system_status_description = 'return2022-' . $stock->primary_address . "+" . $stock->secondary_address;
				$stock->primary_address = $boxSeven;
				$stock->secondary_address = "";
//				$stock->save(false);
			}
		}
//		Stock::resetByOutboundOrderId($id);
		return "-OK-";
	}

	/*
*
* */
	public function actionFindDiffOurBoxClientBox()
	{
		// /other/one/find-diff-our-box-client-box

		$id = 115141;

		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo '-START-';
		echo $id;
		echo "<br />";
//        die;

		$boxes = [];
		$stockAll = Stock::find()->select('primary_address, secondary_address, inbound_client_box')->andWhere([
			'inbound_order_id' => $id,
//                'status'=>[
//                    Stock::STATUS_INBOUND_SCANNED,
//                    Stock::STATUS_INBOUND_OVER_SCANNED,
//                ]
		])
			//->groupBy('primary_address, secondary_address')
						 ->all();

//        VarDumper::dump($stockAll,10,true);
//        die;

		foreach ($stockAll as $item) {
//			$boxes[$item['primary_address']][$item["inbound_client_box"]] = $item["inbound_client_box"];
			$boxes[$item['inbound_client_box']][$item["primary_address"]] = $item["secondary_address"];
		}

		foreach ($boxes as $key => $item) {
			if (count($item) > 1) {
				VarDumper::dump($key, 10, true);
				VarDumper::dump($item, 10, true);
			}
		}

//		VarDumper::dump($box,10,true);


		echo '-END-';
		return $this->render('index');
	}

	public function actionInboundDiff($inboundId = -1)
	{
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		//die('other/one/inbound-diff?inboundId=115296 DIE');
//		$inboundId = 115147;
		file_put_contents("B2B-inboundItemList-full-" . $inboundId . ".csv", "");
		$inboundItemList = InboundOrderItem::find()
										   ->andWhere(['inbound_order_id' => $inboundId])
										   ->all();

		foreach ($inboundItemList as $rowItem) {
			$count = Stock::find()->andWhere([
				'inbound_order_id' => $rowItem->inbound_order_id,
				'inbound_client_box' => $rowItem->box_barcode,
				'product_barcode' => $rowItem->product_barcode,
			])->count();

			$row = $rowItem->box_barcode . ';' . $rowItem->product_barcode . ";" . $rowItem->expected_qty . ';' . $rowItem->accepted_qty . ';' . $count . "\n";//"<br />";
			//echo $row."<br />";
//            file_put_contents("inboundItemList-".$inboundId.".csv",$row,FILE_APPEND);

			if ($count != $rowItem->accepted_qty) {
				$onStockList = Stock::find()->andWhere([
					'inbound_order_id' => $rowItem->inbound_order_id,
					'inbound_client_box' => $rowItem->box_barcode,
					'product_barcode' => $rowItem->product_barcode,
				])->all();

				if (empty($onStockList)) {
					$row = $rowItem->box_barcode . ';' . $rowItem->product_barcode . ";" . '-' . ';' . '-' . "\n";//"<br />";
					echo $row . "<br />";
					file_put_contents("B2B-inboundItemList-full-" . $inboundId . ".csv", $row, FILE_APPEND);
				} else {
					foreach ($onStockList as $stockITem) {
						$row = $rowItem->box_barcode . ';' . $rowItem->product_barcode . ";" . $stockITem->primary_address . ';' . $stockITem->secondary_address . "\n";//"<br />";
						echo $row . "<br />";
						file_put_contents("B2B-inboundItemList-full-" . $inboundId . ".csv", $row, FILE_APPEND);
					}
				}
			}
		}

		return $this->render('index');
	}

	/**
	 * Создаем отчет из файла для поиска по нему всех лостов на складе. Нужно это для подготовки к инвенту
	 */
	public function actionCreateReportForFindLostByFile()
	{
		// /other/one/create-report-for-find-lost-by-file

		//die("/other/one/create-report-for-find-lost-by-file - die begin");

		$rootPath = \Yii::getAlias('@stockDepartment') . '/web/inventory/prepare_for_inventory/DefactoLostReport_09_11_2022.xlsx';

		$products = [];

		$excel = \PHPExcel_IOFactory::load($rootPath);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 1;
		for ($i = $start; $i <= 147; $i++) {
			$lotSKU = $excelActive->getCell('A' . $i)->getValue();
			$lotBarcode = $excelActive->getCell('B' . $i)->getValue();

			$lotSKU = trim($lotSKU);
			$lotBarcode = trim($lotBarcode);

			$products [] = [
				'lotSKU' => $lotSKU,
				'lotBarcode' => $lotBarcode,
			];
		}
		$result = [];
		foreach ($products as $barcode) {
			$result[] = Stock::find()
							 ->select('secondary_address, primary_address, product_barcode')
							 ->andWhere(['client_id' => 2, 'product_barcode' => $barcode['lotBarcode']])
							 ->andWhere('status != 34')
							 ->asArray()
							 ->all();
		}


		VarDumper::dump($result, 10, true);

		$strToFile = "";
		foreach ($result as $items) {
			foreach ($items as $item) {
				$strToFile .= $item["secondary_address"] . ";" . $item["primary_address"] . ";" . $item["product_barcode"] . ";1;" . "\n";
			}
		}
		file_put_contents("create-report-for-find-lost-by-file.csv", $strToFile);

		die;
	}

	/*
	* Добавляем товары на склад из файла
	* */
	public function actionAddProductToStockFromFile()
	{ // other/one/add-product-to-stock-from-file
		die('-DIE-END actionAddProductToStockFromFile-');
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		die("-START DIE other/one/add-product-to-stock-from-file");

//        $pathToCSVFile = 'tmp-file/defacto/07102016/stockLost.csv';
		$pathToCSVFile = 'tmp-file/defacto/lost-29-12-2017/defacto-lost-29-12-2017.csv';
		$i = 1;
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ";")) !== false) {

				$stock = new Stock();
				$stock->secondary_address = '';
				$stock->product_barcode = $data[0];
				$stock->primary_address = $data[1];
				$stock->box_barcode = '';
				$stock->box_size_barcode = '';
				$stock->box_kg = '';
				$stock->box_size_m3 = '';
				$stock->outbound_order_id = '0';
				$stock->outbound_picking_list_id = '0';
				$stock->outbound_picking_list_barcode = '';
				$stock->scan_out_datetime = '';
				$stock->scan_out_employee_id = '0';
				$stock->status = Stock::STATUS_NOT_SET;
				$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
				$stock->system_status = '-lost-';
				$stock->system_status_description = 'Это лосты от 29-12-2017';
//                $stock->save(false);

				echo $stock->id . "<br />";
				echo $i++ . " : " . $data[0] . " : " . $data[1] . "<br />";
				file_put_contents('stockLost-added-ids-29-12-2017.csv', $stock->id . "\n", FILE_APPEND);
			}
		}

		return $this->render('index');
	}

	/*
	* Добавляем товары на склад из файла
	* */
	public function actionTestScanningForm()
	{ // other/one/test-scanning-form
//		die('-DIE-END test-scanning-form-');
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";
		echo "<BR />";

		$sf = new ScanningForm();
		$sf->scenario = 'IsEmployeeBarcode';
		$sf->scenario = 'IsPickingListBarcode';
		$sf->scenario = 'IsBoxBarcode';
		$sf->scenario = 'IsProductBarcode';

		$sf->employee_barcode = "01";
		$sf->picking_list_barcode = "2482-29391138-2-1";
		$sf->picking_list_barcode_scanned = "";
		$sf->box_barcode = "b0000041081";
		$sf->product_barcode = "9000002058567";
		$sf->step = "";
		$sf->box_kg = "";

		if ($sf->validate()) {

		}
		$errors = ActiveForm::validate($sf);
		VarDumper::dump($errors);

		return $this->render('index');
	}

	public function actionLtrimZero()
	{
		$items = Stock::find()
					  ->andWhere(['client_id' => 103])
					  ->all();

		foreach ($items as $item) {
			$item->product_barcode = ltrim($item->product_barcode,"0");
			$item->save(false);
//			echo $item->product_barcode."\n";
		}
		echo "OK";
	}

	/**
	 */
	public function actionReplaceProductBarcode()
	{
		echo ' end other/replace-product-barcode end' . "\n";
		// other/update-store
		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load("resources/replaceNewBalance.xlsx");
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";

		$start = 2;
		$strToFile = "место".";"."короб".";"."старый шк".";"."новый шк".";"."\n";
		for ($i = $start; $i <= 20000; $i++) {
			$oldBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
			$newBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
			$productQty = (string)$excelActive->getCell('C' . $i)->getValue();

			if (empty($oldBarcode)) {
				continue;
			}
			$items = Stock::find()
						  			->andWhere([
										"inbound_order_id" => "115964",
										"product_barcode" => $oldBarcode,
										"field_extra1" => "",
									])
								  ->limit($productQty)
								  ->orderBy("address_sort_order")
								  ->all();

			foreach ($items as $item) {
				$item->field_extra1 = "reserved";
				$item->field_extra2 = $newBarcode;
				$item->save(false);
				$secondary_address = rtrim(ltrim( $item->secondary_address,"0-"),"-0");
				$strToFile .= $secondary_address.";".$item->primary_address.";".$item->product_barcode.";".$newBarcode.";".$item->address_sort_order.";"."\n";
			}

//			VarDumper::dump($rows, 10, true);
		}

		file_put_contents("replace-product-barcode.csv", $strToFile);

		return $this->render('index');
	}

	/**
	 */
	public function actionFixStockIntermode()
	{
		echo ' end other/fix-stock-intermode end' . "\n";
		return "xo";
		// other/update-store
		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load("resources/fix-21042024.xlsx");
		$excel->setActiveSheetIndex(6);
		$excelActive = $excel->getActiveSheet();
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";

		$start = 2;
		$strToFile = "место".";"."короб".";"."старый шк".";"."новый шк".";"."\n";
		for ($i = $start; $i <= 2530; $i++) {
			$productBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
			$productQty = (string)$excelActive->getCell('C' . $i)->getValue();

			if (empty($productBarcode)) {
				continue;
			}
			$items = InboundOrderItem::find()
						  ->andWhere([
							  "inbound_order_id" => 2,
							  "product_barcode" => $productBarcode,
						  ])
						  ->all();

			foreach ($items as $item) {
//				$item->expected_qty;
//				$item->accepted_qty;
				if ($item->expected_qty != $productQty) {
					echo $item->expected_qty."; ".$item->accepted_qty."; ".$productBarcode."; ".$productQty.";<br />";
				}

//				$item->field_extra1 = "reserved";
//				$item->field_extra2 = $newBarcode;
//				$item->save(false);
//				$secondary_address = rtrim(ltrim($item->secondary_address, "0-"), "-0");
			}

		}
	}

	public function actionFixStockIntermode2()
	{
		echo ' end other/fix-stock-intermode end' . "\n";
		return "xo";

		// other/update-store
		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load("resources/plus-intermode.xlsx");
		$excel->setActiveSheetIndex(3);
		$excelActive = $excel->getActiveSheet();
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		$start = 1;
		$items = [];
		for ($i = $start; $i <= 2530; $i++) {
			$productBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
			if (empty($productBarcode)) {
				continue;
			}
			if (!isset($items[$productBarcode])) {
				$items[$productBarcode] = 1;
			} else {
				$items[$productBarcode] +=1;
			}
		}

		$strToFile = "Style;Color;Description;UPC;Size;QTY;"."\n";
		foreach ($items as $barcode => $qty) {
			$strToFile .= "no;no;no;".$barcode.";no;".$qty.";"."\n";
		}
		file_put_contents("Converse24ss.csv",$strToFile. "\n", FILE_APPEND);
//		file_put_contents("Lacoste1847.csv",$strToFile. "\n", FILE_APPEND);
//		file_put_contents("REEBOK225paris.csv",$strToFile. "\n", FILE_APPEND);
//		file_put_contents("GANT1545.csv",$strToFile. "\n", FILE_APPEND);
		VarDumper::dump($items,10,true);
		VarDumper::dump($strToFile,10,true);
	}
}

