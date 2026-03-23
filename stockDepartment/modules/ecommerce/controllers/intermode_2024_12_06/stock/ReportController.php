<?php

namespace app\modules\ecommerce\controllers\intermode\stock;

use app\modules\ecommerce\controllers\intermode\stock\domain\entities\EcommerceStockSearch;
use stockDepartment\components\Controller;
use Yii;


class ReportController extends Controller
{
    /**
     * Lists all EcommerceStock models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionFindProductOnStock()
    {
        // find-product-on-stock
        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchFindProductOnStock(Yii::$app->request->queryParams);

        return $this->render('stock/find-product-on-stock', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrintFindProductOnStock()
    {
        // print-find-product-on-stock
        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchFindProductOnStock(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
//        $dataProvider = $searchModel->searchFindProductOnStock('EcommerceStockSearch[place_address_barcode]=4-9-06-1&EcommerceStockSearch[box_address_barcode]=&EcommerceStockSearch[condition_type]=&EcommerceStockSearch[status_availability]=2&EcommerceStockSearch[status_outbound]=');
//        $productListOnStock = $dataProvider->getModels();
        $query = $dataProvider->query;

        $pdf = new \TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('nmdx.com');
        $pdf->SetTitle('nmdx.com');
        $pdf->SetSubject('nmdx.com');
        $pdf->SetKeywords('nmdx.com');
        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        $pdf->SetMargins(10, 10, 10, true);
        //set auto page breaks
        $pdf->SetAutoPageBreak(true, 5);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
        $pdf->AddPage('P', 'A4', true);
        $pdf->SetFont('arial', 'B', 7);

        $html = '';
        $countPages = 0;
        $page = 0;
        $pages = 0;
        $countItem = 0;
        $batchCount = 30;

        if ($count = $query->count()) {
            $pages = ceil($count / $batchCount);
            $page = 1;
            foreach ($query->batch($batchCount) as $values) {

                $html .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
                    '   <tr align="center" valign="middle" >' .
                    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('forms', 'Quantity') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Product barcode') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Primary address') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Secondary address') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Outbound Status') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Брак/Не брак') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Status availability') . '</strong></th>' .
                    '   </tr>';

                foreach ($values as $value) {

                    $pbr = $value['product_barcode'];
                    $codePart1 = substr($pbr, 0, 8);
                    $codePart4 = substr($pbr, 8, 5);

                    $pbrFormatText = $codePart1 . ' <b style="font-size: 3mm; font-weight: bold; ">' . $codePart4 . '</b>';

                    $html .= '<tr align="center" valign="middle">' .
                        '<td align="center" valign="middle" border="1">' . $value['qty'] . '</td>' .
                        '<td align="center" valign="middle" border="1">' . $pbrFormatText . '</td>' .
                        '<td align="left" valign="middle" border="1">' . $value['box_address_barcode'] . '</td>' .
                        '<td align="center" valign="middle" border="1">' . $value['place_address_barcode'] . '</td>' .
                        '<td align="center" valign="middle" border="1">' . (new \common\ecommerce\constants\StockOutboundStatus())->getValue($value['status_outbound']) . '</td>' .
                        '<td align="center" valign="middle" border="1">' .  (new \common\ecommerce\constants\StockConditionType)->getConditionTypeValue($value['condition_type']) . '</td>' .
                        '<td align="center" valign="middle" border="1">' . (new \common\ecommerce\constants\StockAvailability())->getValue($value['status_availability']) . '</td>' .
                        '</tr>';
                    $countItem++;

                    //             [['status_outbound','status_availability','client_id','condition_type'], 'integer'],
//                    [['product_barcode','box_address_barcode','place_address_barcode'], 'string'],
                }
                $html .= '</table>';
                $pdf->writeHTML($html);

                $pdf->Cell(0, 0, $page . ' из ' . $pages, 0, 0, 'R');
                $pdf->Ln(2);
                if ($count > $countItem) {
                    $pdf->AddPage('P', 'A4', true);
                }
                $html = '';
                $page++;
            }
        }
        $pdf->lastPage();
        $pdf->Output(date("d-m-Y-H-i-s") . '-whereOnWarehouse.pdf', 'D');
        Yii::$app->end();
    }

    /**
    * Export to excel
    *
    **/
    public function actionStockExportToExcel()
    {
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
        $activeSheet->setCellValue('E' . $i, 'SKU')->getColumnDimension('E')->setAutoSize(true); // +
//        $activeSheet->setCellValue('B' . $i, 'Product condition')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('A' . $i, 'Place address')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'Box address')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'Product Barcode')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'Product quantity')->getColumnDimension('D')->setAutoSize(true); // +

        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {
            $i++;
//            $activeSheet->setCellValueExplicit('B' . $i, (new StockConditionType())->getConditionTypeValue($model['condition_type']) , \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
           $activeSheet->setCellValueExplicit('E' . $i, $model['client_product_sku'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->setCellValueExplicit('A' . $i, $model['place_address_barcode'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->setCellValueExplicit('B' . $i, $model['box_address_barcode'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->setCellValueExplicit('C' . $i, $model['product_barcode'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->setCellValueExplicit('D' . $i, $model['qty'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="products-on-stock-' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }



}