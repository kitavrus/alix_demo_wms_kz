<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\outbound;

use Yii;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use stockDepartment\components\Controller;
use common\ecommerce\constants\CourierCompany;
use common\ecommerce\entities\EcommerceOutboundList;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\OutboundListService;
use app\modules\ecommerce\controllers\intermode\outbound\domain\OutboundService;
use app\modules\intermode\controllers\ecommerce\outbound\domain\form\OutboundListForm;

class OutboundListController extends Controller
{
	public function actionIndex()
	{
		return $this->redirect('scanning-form');
	}

	//
	public function actionScanningForm()
	{  //
		$form = new OutboundListForm();
		$form->title = date('d-m-Y');
		return $this->render('scanning-form',['model'=>$form]);
	}
	/*
	* DONE
	* */
	public function actionCourierCompany()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';
		$result = '';
		$packageBarcodeQty = 0;

		$scanForm = new OutboundListForm();
		$scanForm->setScenario(OutboundListForm::SCENARIO_COURIER_COMPANY);


		if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
			$service = new OutboundListService();
			$packageBarcodeQty = $service->selectCourierCompany($scanForm->getDTO());

			$orderInList = $service->showOrdersInList($scanForm->getDTO());
			$result = $this->renderPartial('_order_in_list',['orderInList'=>$orderInList]);
		} else {
			$errors = ActiveForm::validate($scanForm);
		}
		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors,
			'messages' => $messages,
			'packageBarcodeQty' => $packageBarcodeQty,
			'result' => $result,
		];
	}

	/*
	* DONE
	* */
	public function actionOrderNumber()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';
		$result = '';

		$scanForm = new OutboundListForm();
		$scanForm->setScenario(OutboundListForm::SCENARIO_ORDER_NUMBER);


		if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
			$service = new OutboundListService();
		} else {
			$errors = ActiveForm::validate($scanForm);
		}
		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors,
			'messages' => $messages,
			'result' => $result,
		];
	}

	/*
 * DONE
 * */
	public function actionBarcode()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';
		$result = '';
		$packageBarcodeQty = 0;

		$scanForm = new OutboundListForm();
		$scanForm->setScenario(OutboundListForm::SCENARIO_ADD);


		if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
			$service = new OutboundListService();
			$packageBarcodeQty = $service->scanPackageBarcode($scanForm->getDTO());

			$orderInList = $service->showOrdersInList($scanForm->getDTO());
			$result = $this->renderPartial('_order_in_list',['orderInList'=>$orderInList]);
		} else {
			$errors = ActiveForm::validate($scanForm);
		}
		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors,
			'messages' => $messages,
			'packageBarcodeQty' => $packageBarcodeQty,
			'result' => $result,
		];
	}

	public function actionPrint()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$title = '';
		$courierCompany = '';
		$scanForm = new OutboundListForm();
		$scanForm->setScenario(OutboundListForm::SCENARIO_PRINT);

		if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
			$dto = $scanForm->getDTO();
			$service = new OutboundListService();
			$orderForPrintList = $service->printList($dto);
			$title = $dto->title;
			$courierCompany = $dto->courierCompany;

		} else {
			$errors = ActiveForm::validate($scanForm);
		}

		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors,
			'title' => $title,
			'courierCompany' => $courierCompany,
		];
	}

	public function actionShowOrderInList()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';
		$result = '';
		$scanForm = new OutboundListForm();
		$scanForm->setScenario(OutboundListForm::SCENARIO_SHOW_ORDER_IN_LIST);

		if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
			$service = new OutboundListService();
			$orderInList = $service->showOrdersInList($scanForm->getDTO());
			$result = $this->renderPartial('_order_in_list',['orderInList'=>$orderInList]);
		} else {
			$errors = ActiveForm::validate($scanForm);
		}
		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors,
			'messages' => $messages,
			'result' => $result,
		];
	}

	public function actionShowPackedOrderButNotScannedToList()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';
		$result = '';
		$scanForm = new OutboundListForm();
		$scanForm->setScenario(OutboundListForm::SCENARIO_SHOW_PACKED_ORDER_BUT_NOT_SCANNED_TO_LIST);

		if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
			$service = new OutboundListService();
			$orderNotInList = $service->allPackedOrderButNotScannedToList(); // $scanForm->getDTO()->title
			$result = $this->renderPartial('_packed_but_not_scanned_to_list',['orderNotInList'=>$orderNotInList]);
		} else {
			$errors = ActiveForm::validate($scanForm);
		}
		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors,
			'messages' => $messages,
			'result' => $result,
		];
	}

	public function actionShowKaspiOrders()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';
		$result = '';
		$scanForm = new OutboundListForm();
		$scanForm->setScenario(OutboundListForm::SCENARIO_SHOW_KASPI_ORDERS);

		if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
			$service = new OutboundListService();
			$anyCourierCompany = $service->showAllOrdersInAllOutboundList($scanForm->getDTO()->title); // $scanForm->getDTO()->title
			$result = $this->renderPartial('_any_courier_company',['anyCourierCompany'=>$anyCourierCompany]);
		} else {
			$errors = ActiveForm::validate($scanForm);
		}
		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors,
			'messages' => $messages,
			'result' => $result,
		];
	}

	public function actionDeleteList($listTitle,$courierCompany)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';
		$result = '';

		$service = new OutboundListService();
		$isDeleted = $service->deleteList($listTitle,$courierCompany);

		if(!$isDeleted) {
			$messages = "Удалить нельзя, лист уже распечатан";
		}

		$anyCourierCompany = $service->showAllOrdersInAllOutboundList($listTitle);
		$result = $this->renderPartial('_any_courier_company',['anyCourierCompany'=>$anyCourierCompany]);

		return [
			'success' => ($isDeleted ? '1' : '0'),
			'errors' => $errors,
			'messages' => $messages,
			'result' => $result,
		];
	}
	
	public function actionPrintDocument($title, $courierCompany)
	{
		$service = new OutboundListService();
		$scannedBoxList = $service->getDataForPrintList($title, $courierCompany);
		$courierCompanyTitle = CourierCompany::getValue($courierCompany);

		// Почему-то приходит 'LAMODA', а должно 'Lamoda', не стал менять константу что бы не сломать логику
		if (strtolower($courierCompany) === strtolower(CourierCompany::LAMODA)) {
			return $this->printDocumentLamoda($title, $scannedBoxList);
		} else {
			return $this->printDocumentDefault($courierCompanyTitle, $scannedBoxList, $title, $courierCompany);
		}
	}

	private function printDocumentDefault($courierCompanyTitle, $scannedBoxList, $title, $courierCompany) {
		$objPHPExcel = new Spreadsheet();
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
			->setTitle('АКТ');
		//            ->setTitle('report-' . date('d.m.Y'));

		// PRINTING SETTING BEGIN
		$objPHPExcel->getActiveSheet()->getPageSetup()
			->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()
			->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

		$iCell = 2;
		$activeSheet->setCellValue('D' . $iCell, 'АКТ ПРИЁМА-ПЕРЕДАЧИ ТОВАРА'); // +

		$iCell += 2;
		$activeSheet->setCellValue('A' . $iCell, 'г.Алматы');
		$activeSheet->setCellValue('H' . $iCell, date('d.m.Y'));
		$activeSheet->getStyle('H' . $iCell)->getFont()->setSize(10);

		$iCell += 1;
		$activeSheet->setCellValue('A' . $iCell, 'Мы, нижеподписавшиеся представитель компании ТОО "Effective 3PL" и представитель компании ТОО "' . $courierCompanyTitle . '" составили настоящий акт о том, что представитель компании ТОО "Nomadex 3PL" передал, а представитель компании ТОО "' . $courierCompanyTitle . '" принял груз согласно перечню:');
		$activeSheet->getStyle('A' . $iCell)->getAlignment()->setWrapText(true);
		$activeSheet->mergeCells('A' . $iCell . ':H' . ($iCell += 4));
		$iCell += 3;
		$borderCellStart = 'B' . $iCell;
		$activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/forms', '№'));
		$activeSheet->getStyle('B' . $iCell)
			->getAlignment()
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->setCellValue('C' . $iCell, Yii::t('outbound/titles', 'Номер заказа'));
		$activeSheet->getStyle('C' . $iCell)
			->getAlignment()
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$activeSheet->mergeCells('C' . $iCell . ':D' . $iCell);

		$activeSheet->setCellValue('E' . $iCell, Yii::t('outbound/titles', 'ТТН'));
		$activeSheet->getStyle('E' . $iCell)
			->getAlignment()
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->mergeCells('E' . $iCell . ':F' . $iCell);

		$activeSheet->setCellValue('G' . $iCell, Yii::t('outbound/titles', 'Штрих-код места'));
		$activeSheet->getStyle('G' . $iCell)
			->getAlignment()
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->mergeCells('G' . $iCell . ':H' . $iCell);

		$i = 0;
		foreach ($scannedBoxList as $row) {
			$iCell += 1;
			$activeSheet->setCellValue('B' . $iCell, ++$i);
			$activeSheet->getStyle('B' . $iCell)
				->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


			$activeSheet->setCellValue('C' . $iCell, $row->client_order_number);
			$activeSheet->mergeCells('C' . $iCell . ':D' . $iCell);

			$activeSheet->setCellValueExplicit('E' . $iCell, $row->ttn_delivery_company, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activeSheet->getStyle('E' . $iCell)
				->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$activeSheet->mergeCells('E' . $iCell . ':F' . $iCell);

			$activeSheet->setCellValueExplicit('G' . $iCell, $row->package_barcode, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activeSheet->getStyle('G' . $iCell)
				->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$activeSheet->mergeCells('G' . $iCell . ':H' . $iCell);
		}

		$borderCellEnd = 'G' . $iCell;
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					//                    'color' => ['argb' => FFFF0000],
				],
			],
		];

		$border = $borderCellStart . ':' . $borderCellEnd;
		$activeSheet->getStyle($border)->applyFromArray($styleArray);



		$activeSheet->setCellValue('B' . ($iCell + 4), 'Груз отгрузил'); // +
		$activeSheet->setCellValue('A' . ($iCell + 6), 'ФИО _________________________'); // +
		$activeSheet->setCellValue('A' . ($iCell + 8), 'Подпись _________'); // +

		$activeSheet->setCellValue('F' . ($iCell + 4), 'Груз принял'); // +
		$activeSheet->setCellValue('E' . ($iCell + 6), 'ФИО _________________________'); // +
		$activeSheet->setCellValue('E' . ($iCell + 8), 'Подпись _________'); // +
		$activeSheet->setCellValue('B' . ($iCell + 10), 'М.П'); // +
		$activeSheet->setCellValue('F' . ($iCell + 10), 'М.П'); // +

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="outbound-list-' . $title . '-' . $courierCompany . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
		$writer->save('php://output');

		Yii::$app->end();
		return true;
	}

	public function printDocumentLamoda($title, $scannedBoxList)
	{
		$service = new OutboundListService();

		$groupedOrders = [];
		foreach ($scannedBoxList as $orderItem) {
			$orderNumber = $orderItem->client_order_number;
			$order = $service->getEcommerceOutbound($orderNumber);
			$items = [];

			if (!empty($order['client_PackMessage'])) {
				$packMessage = json_decode($order['client_PackMessage'], true);

				if (isset($packMessage['items']) && is_array($packMessage['items'])) {
					foreach ($packMessage['items'] as $item) {
						$items[] = [
							'lamoda_sku' => $item['lamoda_sku'],
							'item_name' => $item['item_name'],
							'item_price' => $item['unit_price']
						];
					}
				}
				else {
					$items[] = [
						'lamoda_sku' => $packMessage['lamoda_sku'],
						'item_name' => $packMessage['item_name'],
						'item_price' => $packMessage['unit_price']
					];
				}
			}

			$groupedOrders[$orderNumber] = [
				'order' => $order,
				'items' => $items
			];
		}

		$objPHPExcel = new Spreadsheet();
		$activeSheet = $objPHPExcel->setActiveSheetIndex(0)->setTitle('Реестр');

		$activeSheet->getPageSetup()
			->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
			->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
			->setFitToWidth(1)
			->setFitToHeight(0);

		$currentRow = 1;

		$activeSheet->setCellValue('A' . $currentRow, 'Приложение №6 к Договору №211/01-23 от 03 июля 2024 г.');
		$activeSheet->mergeCells('A' . $currentRow . ':L' . $currentRow);
		$activeSheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
		$activeSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$currentRow++;

		$activeSheet->setCellValue('A' . $currentRow, 'Реестр сгруппированных отправлений -- акт приема -- передачи отправлений');
		$activeSheet->mergeCells('A' . $currentRow . ':L' . $currentRow);
		$activeSheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
		$activeSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$currentRow += 2;

		$tableStartRow = $currentRow;
		$headers = [
			'A' => '№ П/П',
			'B' => 'Номер заказа',
			'C' => 'Номер посылки',
			'D' => 'Получатель (ФИО)',
			'E' => 'Телефон',
			'F' => 'Город',
			'G' => 'Адрес доставки',
			'H' => 'Артикул товара',
			'I' => 'Наименование товара',
			'J' => 'К-во',
			'K' => 'Сумма',
			'L' => 'Примечания о доставке'
		];

		foreach ($headers as $col => $header) {
			$activeSheet->setCellValue($col . $currentRow, $header);
			$activeSheet->getStyle($col . $currentRow)->getFont()->setBold(true);
			$activeSheet->getStyle($col . $currentRow)->getAlignment()
				->setWrapText(true)
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		}

		$activeSheet->getRowDimension($currentRow)->setRowHeight(40);

		$index = 0;

		foreach ($groupedOrders as $orderNumber => $orderData) {
			$currentRow++;
			$index++;

			$order = $orderData['order'];
			$items = $orderData['items'];

			$skus = [];
			$itemNames = [];
			$itemPrices = [];

			foreach ($items as $item) {
				if (!empty($item['lamoda_sku'])) {
					$skus[] = $item['lamoda_sku'];
				}
				if (!empty($item['item_name'])) {
					$itemNames[] = $item['item_name'];
				}
				if (!empty($item['item_price'])) {
					$itemPrices[] = $item['item_price'];
				}
			}

			$skuString = implode("\n", $skus);
			$itemNameString = implode("\n", $itemNames);
			$itemPriceString = implode("\n", $itemPrices);

			$activeSheet->setCellValue('A' . $currentRow, $index);
			$activeSheet->setCellValue('B' . $currentRow, $orderNumber);
			$activeSheet->setCellValue('C' . $currentRow, '');
			$activeSheet->setCellValue('D' . $currentRow, $order['customer_name']);
			$activeSheet->setCellValue('E' . $currentRow, $order['phone_mobile1']);
			$activeSheet->setCellValue('F' . $currentRow, $order['city']);
			$activeSheet->setCellValue('G' . $currentRow, $order['street']);
			$activeSheet->setCellValue('H' . $currentRow, $skuString);
			$activeSheet->setCellValue('I' . $currentRow, $itemNameString);
			$activeSheet->setCellValue('J' . $currentRow, count($items));
			$activeSheet->setCellValue('K' . $currentRow, $itemPriceString);
			$activeSheet->setCellValue('L' . $currentRow, '');

			$itemCount = max(count($skus), count($itemNames), count($itemPrices), 1);
			$rowHeight = 40 + (($itemCount - 1) * 20);
			$activeSheet->getRowDimension($currentRow)->setRowHeight($rowHeight);
		}

		$dataEndRow = $currentRow;

		$activeSheet->getStyle('A' . ($tableStartRow + 1) . ':L' . $dataEndRow)->getAlignment()
			->setWrapText(true)
			->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

		$currentRow += 2;
		$activeSheet->setCellValue('A' . $currentRow, 'ТОО «Intermode (Интермоде)»');
		$activeSheet->setCellValue('G' . $currentRow, 'ТОО «Мода с доставкой»');

		$currentRow++;
		$activeSheet->setCellValue('A' . $currentRow, 'Передал (отгрузил) _________________________');
		$activeSheet->setCellValue('G' . $currentRow, 'Принял _________________________');

		$currentRow++;
		$activeSheet->setCellValue('A' . $currentRow, 'Дата отгрузки: ' . date('d.m.Y'));
		$activeSheet->setCellValue('G' . $currentRow, 'Номер документа:');

		// Настройка ширины колонок
		$columnWidths = [
			'A' => 6,
			'B' => 18,
			'C' => 15,
			'D' => 20,
			'E' => 14,
			'F' => 18,
			'G' => 25,
			'H' => 18,
			'I' => 25,
			'J' => 8,
			'K' => 12,
			'L' => 18
		];

		foreach ($columnWidths as $col => $width) {
			$activeSheet->getColumnDimension($col)->setWidth($width);
		}

		$tableRange = 'A' . $tableStartRow . ':L' . $dataEndRow;
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
		];
		$activeSheet->getStyle($tableRange)->applyFromArray($styleArray);

		$activeSheet->getStyle('A' . $tableStartRow . ':L' . $dataEndRow)
			->getAlignment()
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->getStyle('A' . $tableStartRow . ':L' . $tableStartRow)
			->getAlignment()
			->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="lamoda-reestr-' . $title . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
		$writer->save('php://output');
		Yii::$app->end();

		return true;
	}


	public function actionPrintDocument_2025_11_14($title,$courierCompany) {

		$service = new OutboundListService();
		$scannedBoxList = $service->getDataForPrintList($title,$courierCompany);
		$courierCompanyTitle = CourierCompany::getValue($courierCompany);

		if($courierCompany == CourierCompany::LAMODA) {
			return $this->actionPrintDocumentLamoda($title,$courierCompany);
		}

		$objPHPExcel = new Spreadsheet();
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
			->setTitle('АКТ');
//            ->setTitle('report-' . date('d.m.Y'));

		// PRINTING SETTING BEGIN
		$objPHPExcel->getActiveSheet()->getPageSetup()
					->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()
					->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

		$iCell = 2;
		$activeSheet->setCellValue('D' . $iCell, 'АКТ ПРИЁМА-ПЕРЕДАЧИ ТОВАРА'); // +

		$iCell += 2;
		$activeSheet->setCellValue('A' . $iCell,'г.Алматы');
		$activeSheet->setCellValue('H' . $iCell,date('d.m.Y'));
		$activeSheet->getStyle('H' . $iCell)->getFont()->setSize(10);

		$iCell += 1;
		$activeSheet->setCellValue('A' . $iCell,'Мы, нижеподписавшиеся представитель компании ТОО "Nomadex 3PL" и представитель компании ТОО "'.$courierCompanyTitle.'" составили настоящий акт о том, что представитель компании ТОО "Nomadex 3PL" передал, а представитель компании ТОО "'.$courierCompanyTitle.'" принял груз согласно перечню:');
		$activeSheet->getStyle('A' . $iCell)->getAlignment()->setWrapText(true);
		$activeSheet->mergeCells('A'.$iCell.':H'.($iCell+=4));
		$iCell += 3;
		$borderCellStart = 'B' . $iCell;
		$activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/forms','№'));
		$activeSheet->getStyle('B' . $iCell)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->setCellValue('C' . $iCell, Yii::t('outbound/titles','Номер заказа'));
		$activeSheet->getStyle('C' . $iCell)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$activeSheet->mergeCells('C'.$iCell.':D'.$iCell);

		$activeSheet->setCellValue('E' . $iCell, Yii::t('outbound/titles','ТТН'));
		$activeSheet->getStyle('E' . $iCell)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->mergeCells('E'.$iCell.':F'.$iCell);

		$activeSheet->setCellValue('G' . $iCell, Yii::t('outbound/titles','Штрих-код места'));
		$activeSheet->getStyle('G' . $iCell)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->mergeCells('G'.$iCell.':H'.$iCell);

		$i = 0;
		foreach ($scannedBoxList as $row) {
			$iCell += 1;
			$activeSheet->setCellValue('B' . $iCell, ++$i);
			$activeSheet->getStyle('B' . $iCell)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


			$activeSheet->setCellValue('C' . $iCell, $row->client_order_number);
			$activeSheet->mergeCells('C'.$iCell.':D'.$iCell);

			$activeSheet->setCellValueExplicit('E' . $iCell,$row->ttn_delivery_company,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activeSheet->getStyle('E' . $iCell)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$activeSheet->mergeCells('E'.$iCell.':F'.$iCell);

			$activeSheet->setCellValueExplicit('G' . $iCell,$row->package_barcode,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activeSheet->getStyle('G' . $iCell)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$activeSheet->mergeCells('G'.$iCell.':H'.$iCell);
		}

		$borderCellEnd = 'G' . $iCell;
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
				],
			],
		];

		$border = $borderCellStart.':'.$borderCellEnd;
		$activeSheet->getStyle($border)->applyFromArray($styleArray);



		$activeSheet->setCellValue('B' . ($iCell+4), 'Груз отгрузил'); // +
		$activeSheet->setCellValue('A' . ($iCell+6), 'ФИО _________________________'); // +
		$activeSheet->setCellValue('A' . ($iCell+8), 'Подпись _________'); // +

		$activeSheet->setCellValue('F' . ($iCell+4), 'Груз принял'); // +
		$activeSheet->setCellValue('E' . ($iCell+6), 'ФИО _________________________'); // +
		$activeSheet->setCellValue('E' . ($iCell+8), 'Подпись _________'); // +
		$activeSheet->setCellValue('B' . ($iCell+10), 'М.П'); // +
		$activeSheet->setCellValue('F' . ($iCell+10), 'М.П'); // +

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="outbound-list-' . $title . '-' . $courierCompany . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
		$writer->save('php://output');

		Yii::$app->end();
		return true;
	}

	public function actionPrintDocumentLamoda_2025_11_14($title,$courierCompany) {

		$service = new OutboundListService();
		$scannedBoxList = $service->getDataForPrintList($title,$courierCompany);
		$courierCompanyTitle = CourierCompany::getValue($courierCompany);

		$objPHPExcel = new Spreadsheet();
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
			->setTitle('АКТ');

		// PRINTING SETTING BEGIN
		$objPHPExcel->getActiveSheet()->getPageSetup()
					->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$objPHPExcel->getActiveSheet()->getPageSetup()
					->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);


		$iCell = 0;
		$iCell += 1;
		$activeSheet->setCellValue('E' . $iCell,date('d.m.Y'));
		$activeSheet->getStyle('E' . $iCell)->getFont()->setSize(10);

		$iCell += 1;
		$activeSheet->setCellValue('C' . $iCell, 'Акт приема передачи товара'); // +
		$activeSheet->getStyle('C' . $iCell)->getFont()->setSize(14);

		$iCell += 1;
		$activeSheet->setCellValue('A' . $iCell,'между Товарищество с ограниченной ответственностью "Мода с доставкой" и ТОО "Nomadex 3PL" ');
		$activeSheet->getStyle('A' . $iCell)->getFont()->setSize(12);
		$activeSheet->getStyle('A' . $iCell)->getAlignment()->setWrapText(true);
		$activeSheet->mergeCells('A'.$iCell.':E'.($iCell+=0));

		$iCell += 2;
		$borderCellStart = 'A' . $iCell;
		$activeSheet->setCellValue('A' . $iCell, Yii::t('outbound/forms','№'))->getColumnDimension('A')->setWidth(10);
		$activeSheet->getStyle('A' . $iCell)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

//        $activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/titles','Город'))->getColumnDimension('B')->setAutoSize(true);
//        $activeSheet->getStyle('B' . $iCell)
//            ->getAlignment()
//            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/titles','Номер заказа'))->getColumnDimension('C')->setWidth(20);
		$activeSheet->getStyle('B' . $iCell)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->setCellValue('C' . $iCell, Yii::t('outbound/titles','Количество ед. в заказе'))->getColumnDimension('D')->setAutoSize(true);
		$activeSheet->getStyle('C' . $iCell)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$activeSheet->setCellValue('D' . $iCell, Yii::t('outbound/titles','Сумма заказа'))->getColumnDimension('E')->setWidth(32);
		$activeSheet->getStyle('D' . $iCell)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


		$os = new OutboundService();
		$i = 0;
		$totalQty = 0;
		$totalCost = 0;
		foreach ($scannedBoxList as $row) {

			$orderInfo = $os->getOrderInfo($row->our_outbound_id);

			$iCell += 1;
			$activeSheet->setCellValue('A' . $iCell, ++$i);
			$activeSheet->getStyle('A' . $iCell)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

//            $activeSheet->setCellValue('B' . $iCell, $orderInfo->order->city);
//            $activeSheet->setCellValueExplicit('B' . $iCell,$orderInfo->order->order_number,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('B' . $iCell,$row->cargo_company_ttn,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activeSheet->getStyle('B' . $iCell)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			$activeSheet->setCellValueExplicit('C' . $iCell,$orderInfo->order->accepted_qty,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activeSheet->getStyle('C' . $iCell)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


			$activeSheet->setCellValueExplicit('D' . $iCell,$orderInfo->order->total_price,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activeSheet->getStyle('D' . $iCell)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			$totalQty += $orderInfo->order->accepted_qty;
			$totalCost += $orderInfo->OrderTotalPrice;
		}

		$borderCellEnd = 'E' . $iCell;
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
		];

		$iCell += 1;
		$activeSheet->setCellValue('B' . $iCell,'Итого');
		$activeSheet->getStyle('B' . $iCell)->getFont()->setSize(12);
		$activeSheet->getStyle('B' . $iCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

		$activeSheet->setCellValue('C' . $iCell,$totalQty);
		$activeSheet->getStyle('C' . $iCell)->getFont()->setSize(12);

		$activeSheet->setCellValue('D' . $iCell,$totalCost);
		$activeSheet->getStyle('D' . $iCell)->getFont()->setSize(12);

		$border = $borderCellStart.':'.$borderCellEnd;
		$activeSheet->getStyle($border)->applyFromArray($styleArray);

		$iCell += 1;
		$activeSheet->setCellValue('A' . $iCell,'Итого сумма заказов:');
		$activeSheet->getStyle('A' . $iCell)->getFont()->setSize(12);
		$activeSheet->getStyle('A' . $iCell)->getAlignment()->setWrapText(true);
		$activeSheet->getStyle('A' . $iCell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		$activeSheet->mergeCells('A'.$iCell.':D'.($iCell+=1));


		$activeSheet->setCellValue('A' . ($iCell+3), 'Отгрузил ФИО _______________________'); // +
		$activeSheet->setCellValue('A' . ($iCell+5), 'Принял ФИО _________________________'); // +

		$activeSheet->setCellValue('E' . ($iCell+3), 'Подпись _________'); // +
		$activeSheet->setCellValue('E' . ($iCell+5), 'Подпись _________'); // +

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="outbound-list-' . $title . '-' . $courierCompany . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
		$writer->save('php://output');

		Yii::$app->end();
		return true;
	}


	/*
	* DONE
	* */
	public function actionDelete($id)
	{
		$ol = EcommerceOutboundList::find()->andWhere(['id'=>$id])->one();
		if(empty($ol)) {
			Yii::$app->session->setFlash('danger', "Такой заказ не найден");
		} else if($ol->status == \common\ecommerce\constants\OutboundListStatus::PRINTED) {
			Yii::$app->session->setFlash('danger', "Заказ <b>".$ol->package_barcode." / ".$ol->client_order_number." </b> уже отгружен, его нельзя удалять из списка");
		} else {
			$ol->delete();
			Yii::$app->session->setFlash('success', "Заказ <b>".$ol->package_barcode." / ".$ol->client_order_number." </b>, были успешно удален");
		}

		return $this->redirect('scanning-form');
	}

}