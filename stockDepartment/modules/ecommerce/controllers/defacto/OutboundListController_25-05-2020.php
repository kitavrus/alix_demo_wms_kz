<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\CourierCompany;
use common\ecommerce\defacto\outbound\forms\OutboundListForm;
use common\ecommerce\defacto\outbound\service\OutboundListService;
use stockDepartment\components\Controller;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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

    public function actionPrintDocument($title,$courierCompany) {

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
//        $activeSheet->setCellValue('A' . $iCell,'Мы, нижеподписавшиеся представитель компании ТОО "Nomadex 3PL" и представитель компании ТОО "Pony express" составили настоящий акт о том, что представитель компании ТОО "Nomadex 3PL" передал, а представитель компании ТОО "Pony express" принял груз согласно перечню:');
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
}