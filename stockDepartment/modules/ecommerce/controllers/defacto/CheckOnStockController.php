<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\entities\EcommerceStockSearch;
use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use stockDepartment\components\Controller;
use TCPDF;
use Yii;

class CheckOnStockController extends Controller
{
    /**
     * Lists all EcommerceOutbound models.
     * @return mixed
     */
    public function actionIndex()
    {
        // find-product-on-stock
        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchCheckOnStock(Yii::$app->request->post());

        return $this->render('find-product-on-stock', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrintFindProductOnStock()
    {
        // print-find-product-on-stock
        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchCheckOnStock(Yii::$app->request->post());
        $dataProvider->pagination = false;
        $query = $dataProvider->query;
//        die;

        $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
        $batchCount = 47;
        $rowNumber = 1;

        if ($count = $query->count()) {
            $pages = ceil($count / $batchCount);
            $page = 1;
            foreach ($query->batch($batchCount) as $values) {

                $html .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
                    '   <tr align="center" valign="middle" >' .
					'      <th width="5%" align="center" valign="middle" border="1"><strong>' . "#" . '</strong></th>' .
					'      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Primary address') . '</strong></th>' .
					'      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Secondary address') . '</strong></th>' .
					'      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('stock/forms', 'Product barcode') . '</strong></th>' .
                    '      <th width="20%" align="center" valign="middle" border="1"><strong>' . Yii::t('forms', 'Quantity') . '</strong></th>' .
                    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('forms', 'В наличии') . '</strong></th>' .
                    '   </tr>';

                foreach ($values as $value) {

                    $pbr = $value['product_barcode'];
                    $codePart1 = substr($pbr, 0, 8);
                    $codePart4 = substr($pbr, 8, 5);

                    $pbrFormatText = $codePart1 . ' <b style="font-size: 3mm; font-weight: bold; ">' . $codePart4 . '</b>';

                    $html .= '<tr align="center" valign="middle">' .
                        '<td align="center" valign="middle" border="1">' .($rowNumber++) . '</td>' .
                        '<td align="center" valign="middle" border="1">' . $value['place_address_barcode'] . '</td>' .
                        '<td align="center" valign="middle" border="1">' . $value['box_address_barcode'] . '</td>' .
                        '<td align="center" valign="middle" border="1">' . $pbrFormatText . '</td>' .
                        '<td align="center" valign="middle" border="1">' . $value['qty'] . '</td>' .
                        '<td align="center" valign="middle" border="1">' . "     " . '</td>' .
                        '</tr>';
                    $countItem++;
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

    /*
    * Export to excel
    *
    **/
    public function actionStockExportToExcel()
    {
        $objPHPExcel = new PHPExcel();

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
        $activeSheet->setCellValue('A' . $i, '#')->getColumnDimension()->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'place_address_barcode')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'box_address_barcode')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'client_product_sku')->getColumnDimension('D')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'product_barcode')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('F' . $i, 'physical_qty')->getColumnDimension('F')->setAutoSize(true); // +
        $activeSheet->setCellValue('G' . $i, 'available_qty')->getColumnDimension('G')->setAutoSize(true); // +
        $activeSheet->setCellValue('H' . $i, 'outbound_qty')->getColumnDimension('H')->setAutoSize(true); // +
        $activeSheet->setCellValue('I' . $i, 'block_qty')->getColumnDimension('I')->setAutoSize(true); // +
        $activeSheet->setCellValue('J' . $i, 'outbound_plus_block_qty')->getColumnDimension('J')->setAutoSize(true); // +


		$searchModel = new EcommerceStockSearch();
		$dataProvider = $searchModel->searchCheckOnStock(Yii::$app->request->post());
		$dataProvider->pagination = false;
		$dps = $dataProvider->getModels();
		$rowNumber = 1;
        foreach ($dps as $item) {
            $i++;

			$available_qty = $this->_availableQty($item);
			$outbound_qty = $this->_outboundAvailableQty($item);
			$block_qty = $this->_blockAvailableQty($item);
			$outbound_plus_block_qty = $outbound_qty + $block_qty;

            $activeSheet->setCellValueExplicit('A' . $i, ($rowNumber++), DataType::TYPE_STRING);
            $activeSheet->setCellValueExplicit('B' . $i, $item['place_address_barcode'], DataType::TYPE_STRING);
            $activeSheet->setCellValueExplicit('C' . $i, $item['box_address_barcode'], DataType::TYPE_STRING);
            $activeSheet->setCellValueExplicit('D' . $i, $item['client_product_sku'], DataType::TYPE_NUMERIC);
            $activeSheet->setCellValueExplicit('E' . $i, $item['product_barcode'], DataType::TYPE_STRING);
            $activeSheet->setCellValueExplicit('F' . $i, "", DataType::TYPE_NUMERIC);
            $activeSheet->setCellValueExplicit('G' . $i, $available_qty, DataType::TYPE_NUMERIC);
            $activeSheet->setCellValueExplicit('H' . $i, $outbound_qty, DataType::TYPE_NUMERIC);
            $activeSheet->setCellValueExplicit('I' . $i, $block_qty, DataType::TYPE_NUMERIC);
            $activeSheet->setCellValueExplicit('J' . $i, $outbound_plus_block_qty, DataType::TYPE_NUMERIC);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="check-on-stock-' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

	private function _availableQty($item) {
		return  EcommerceStock::find()
							  ->andWhere(['place_address_barcode' => $item['place_address_barcode']])
							  ->andWhere(['box_address_barcode' =>  $item['box_address_barcode']])
							  ->andWhere(['client_product_sku' => $item['client_product_sku']])
							  ->andWhere(['product_barcode' =>  $item['product_barcode']])
							  ->andWhere(['status_availability' => 2])
							  ->count();
	}

	private function _outboundAvailableQty($item) {
		return EcommerceStock::find()
							 ->andWhere(['place_address_barcode' => $item['place_address_barcode']])
							 ->andWhere(['box_address_barcode' =>  $item['box_address_barcode']])
							 ->andWhere(['client_product_sku' => $item['client_product_sku']])
							 ->andWhere(['product_barcode' =>  $item['product_barcode']])
							 ->andWhere(['status_availability' => 3])
							 ->count();
	}

	private function _blockAvailableQty($item) {
		return EcommerceStock::find()
							 ->andWhere(['place_address_barcode' => $item['place_address_barcode']])
							 ->andWhere(['box_address_barcode' =>  $item['box_address_barcode']])
							 ->andWhere(['client_product_sku' => $item['client_product_sku']])
							 ->andWhere(['product_barcode' =>  $item['product_barcode']])
							 ->andWhere(['status_availability' => 4])
							 ->count();
	}
}