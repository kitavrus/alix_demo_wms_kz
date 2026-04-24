<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound;

use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItemSearch;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundSearch;
use stockDepartment\modules\alix\controllers\ecommerce\stock\domain\entities\EcommerceStock;
use stockDepartment\modules\alix\controllers\ecommerce\stock\domain\entities\EcommerceStockSearch;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
use common\modules\stock\models\Stock;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use stockDepartment\components\Controller;
use Yii;
use yii\web\NotFoundHttpException;


class ReportController extends Controller
{
    /**
     * Lists all EcommerceOutbound models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EcommerceOutboundSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EcommerceOutbound model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new EcommerceOutboundItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

//		$query = EcommerceApiOutboundLog::find();
//		$logHistory = new ActiveDataProvider([
//			'query' => $query,
//			'sort'=> ['defaultOrder' => ['id' => SORT_ASC]],
//		]);
//		$query->andWhere([
//			'our_outbound_id' => $id,
//		]);

        return $this->render('view', [
//            'logHistory' =>$logHistory,
            'logHistory' =>"",
            'model' => $this->OutboundOne($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'outboundBoxBarcode' => Stock::find()->select('box_barcode')->andWhere(['ecom_outbound_id'=>$id])->scalar(),
        ]);
    }

    /**
     * Finds the EcommerceInbound model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EcommerceOutbound the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function OutboundOne($id)
    {
        if (($model = EcommerceOutbound::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all EcommerceOutbound models.
     * @return mixed
     */
    public function actionOnStock()
    {
        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);

        return $this->render('stock/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
  * Export to excel
  *
  **/
    public function actionOutboundExportToExcel()
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

        $forDastan  = Yii::$app->request->get('forDastan');

        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Номер заказа')->getColumnDimension('A')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('B' . $i, 'Статус')->getColumnDimension('B')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('C' . $i, 'Предполагаемое кол-во')->getColumnDimension('C')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('D' . $i, 'Зарезервированое кол-во')->getColumnDimension('D')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('E' . $i, 'Отсканированное кол-во')->getColumnDimension('E')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('F' . $i, 'Дата создания')->getColumnDimension('F')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('G' . $i, 'Дата упаковки')->getColumnDimension('G')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('H' . $i, 'Дата отгрузки')->getColumnDimension('H')->setAutoSize(true); // +;

        $activeSheet->setCellValue('A' . $i, 'Order number')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'Status')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'Expected')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'Reserved')->getColumnDimension('D')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'Scanned')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('F' . $i, 'Date Created')->getColumnDimension('F')->setAutoSize(true); // +
        $activeSheet->setCellValue('G' . $i, 'Date Packed')->getColumnDimension('G')->setAutoSize(true); // +
        $activeSheet->setCellValue('H' . $i, 'Date Courier')->getColumnDimension('H')->setAutoSize(true);
        $activeSheet->setCellValue('I' . $i, 'Shipment Source')->getColumnDimension('I')->setAutoSize(true);
        $activeSheet->setCellValue('J' . $i, 'Cancel reason')->getColumnDimension('J')->setAutoSize(true);

        if ($forDastan) {
            $activeSheet->setCellValue('K' . $i, 'TTN Delivery')->getColumnDimension('K')->setAutoSize(true);
        }

        $activeSheet->setCellValue('L' . $i, 'External order number')->getColumnDimension('L')->setAutoSize(true);
		
        $searchModel = new EcommerceOutboundSearch();

        if ($forDastan) {
            $dataProvider = $searchModel->searchByDefactoOrders(Yii::$app->request->post());
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }


        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i, $model->order_number);
            $activeSheet->setCellValue('B' . $i, OutboundStatus::getValue($model->status, 'EN'));
            $activeSheet->setCellValue('C' . $i, $model->expected_qty);
            $activeSheet->setCellValue('D' . $i, $model->allocated_qty);
            $activeSheet->setCellValue('E' . $i, $model->accepted_qty);

            $created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('F' . $i, $created_at);

            $packing_date = !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('G' . $i, $packing_date);

            $date_left_warehouse = !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('H' . $i, $date_left_warehouse);

            $client_ShipmentSource = !empty($model->client_ShipmentSource) ? $model->client_ShipmentSource : '-';
            $activeSheet->setCellValue('I' . $i, $client_ShipmentSource);

            $client_CancelReason = '-';
            $activeSheet->setCellValue('J' . $i, $client_CancelReason);

            if ($forDastan) {
                $activeSheet->setCellValue('K' . $i, $model->client_ReferenceNumber);
            }
			$activeSheet->setCellValue('L' . $i, $model->external_order_number);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-orders-' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
	
	 /*
    * Export to excel
    *
    **/
    public function actionOutboundExportToExcelWithProducts()
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
        $activeSheet->setCellValue('A' . $i, 'Order number')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'Order Status')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'Product SkuId')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'Product Barcode')->getColumnDimension('D')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'Product Model')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('F' . $i, 'Expected quantity')->getColumnDimension('F')->setAutoSize(true); // +
        $activeSheet->setCellValue('G' . $i, 'Reserved quantity')->getColumnDimension('G')->setAutoSize(true); // +
        $activeSheet->setCellValue('H' . $i, 'Scanned quantity')->getColumnDimension('H')->setAutoSize(true); // +
        $activeSheet->setCellValue('I' . $i, 'Date Created')->getColumnDimension('I')->setAutoSize(true); // +
        $activeSheet->setCellValue('J' . $i, 'Date Packed')->getColumnDimension('J')->setAutoSize(true); // +
        $activeSheet->setCellValue('K' . $i, 'Date Courier')->getColumnDimension('K')->setAutoSize(true);
        $activeSheet->setCellValue('L' . $i, 'Cancel reason')->getColumnDimension('L')->setAutoSize(true);
        $activeSheet->setCellValue('M' . $i, 'External order number')->getColumnDimension('M')->setAutoSize(true);

		$searchModel = new EcommerceOutboundSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination = false;
		$asDatetimeFormat = 'php:d.m.Y H:i:s';
		foreach ($dataProvider->query->select([
			'ecommerce_outbound.order_number',
			'ecommerce_outbound.status',
			'ecommerce_outbound.created_at',
			'ecommerce_outbound.packing_date',
			'ecommerce_outbound.date_left_warehouse',
			'ecommerce_outbound.client_CancelReason',
			'ecommerce_outbound.external_order_number',
			'ecommerce_outbound_items.product_sku',
			'ecommerce_outbound_items.product_barcode',
			'ecommerce_outbound_items.product_model',
			'ecommerce_outbound_items.expected_qty',
			'ecommerce_outbound_items.allocated_qty',
			'ecommerce_outbound_items.accepted_qty',
		])
									 ->leftJoin('ecommerce_outbound_items', 'ecommerce_outbound.id = ecommerce_outbound_items.outbound_id')
									 ->asArray()
									 ->each(50) as $model) {

			$model = (object) $model;
			$i++;
			$activeSheet->setCellValueExplicit('A' . $i, $model->order_number, DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('B' . $i, OutboundStatus::getValue($model->status, 'EN'), DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('C' . $i, $model->product_sku, DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('D' . $i, $model->product_barcode, DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('E' . $i, $model->product_model, DataType::TYPE_STRING);

			$activeSheet->setCellValueExplicit('F' . $i, $model->expected_qty, DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('G' . $i, $model->allocated_qty, DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('H' . $i, $model->accepted_qty, DataType::TYPE_STRING);

			$created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
			$activeSheet->setCellValueExplicit('I' . $i, $created_at, DataType::TYPE_STRING);

			$packing_date = !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date, $asDatetimeFormat) : '-';
			$activeSheet->setCellValueExplicit('J' . $i, $packing_date, DataType::TYPE_STRING);

			$date_left_warehouse = !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse, $asDatetimeFormat) : '-';
			$activeSheet->setCellValueExplicit('K' . $i, $date_left_warehouse, DataType::TYPE_STRING);
//
			$client_CancelReason = '-';
			$activeSheet->setCellValue('L' . $i, $client_CancelReason);
			$activeSheet->setCellValue('M' . $i, $model->external_order_number);
		}

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ordersWithProducts' . date('d.m.Y') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    public function actionOutboundExportToExcelWithProductsDm()
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
		$activeSheet->setCellValue('A' . $i, 'Source')->getColumnDimension('F')->setAutoSize(true); // +
		$activeSheet->setCellValue('B' . $i, 'Order number')->getColumnDimension('A')->setAutoSize(true); // +
		$activeSheet->setCellValue('C' . $i, 'Order Status')->getColumnDimension('B')->setAutoSize(true); // +
		$activeSheet->setCellValue('D' . $i, 'Product SkuId')->getColumnDimension('C')->setAutoSize(true); // +
		$activeSheet->setCellValue('E' . $i, 'Product Barcode')->getColumnDimension('D')->setAutoSize(true); // +
		$activeSheet->setCellValue('F' . $i, 'DM')->getColumnDimension('E')->setAutoSize(true); // +

		$searchModel = new EcommerceOutboundSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination = false;
		foreach ($dataProvider->query->select([
			'ecommerce_outbound.order_number',
			'ecommerce_outbound.status',
			'ecommerce_outbound.client_ShipmentSource',
			'stock.product_sku',
			'stock.product_barcode',
			'stock.product_qrcode',
		])
									 ->leftJoin('stock', 'ecommerce_outbound.id = stock.ecom_outbound_id')
									 ->andWhere('ecommerce_outbound.client_ShipmentSource IN ("KASPI","LAMODA")')
									 ->asArray()
									 ->each(50) as $model) {

			$model = (object) $model;
			$i++;
			$activeSheet->setCellValueExplicit('A' . $i, $model->client_ShipmentSource, DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('B' . $i, $model->order_number, DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('C' . $i, OutboundStatus::getValue($model->status, 'EN'), DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('D' . $i, $model->product_sku, DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('E' . $i, $model->product_barcode, DataType::TYPE_STRING);
			$activeSheet->setCellValueExplicit('F' . $i, $model->product_qrcode, DataType::TYPE_STRING);

		}

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ordersWithProducts' . date('d.m.Y') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
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
}