<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\OutboundCancelStatus;
use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\entities\EcommerceApiOutboundLog;
use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceInboundItemSearch;
use common\ecommerce\entities\EcommerceInboundSearch;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceOutboundItemSearch;
use common\ecommerce\entities\EcommerceOutboundSearch;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\entities\EcommerceStockSearch;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use stockDepartment\components\Controller;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use common\ecommerce\constants\OutboundStatus;


class ReportController extends Controller
{
    public function actionInbound()
    {
        $searchModel = new EcommerceInboundSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('inbound/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionInboundView($id)
    {
        $searchModel = new EcommerceInboundItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('inbound/view', [
            'model' => $this->InboundOne($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all EcommerceOutbound models.
     * @return mixed
     */
    public function actionOutbound()
    {
        $searchModel = new EcommerceOutboundSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;

        return $this->render('outbound/index', [
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
    public function actionOutboundView($id)
    {
        $searchModel = new EcommerceOutboundItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

		$query = EcommerceApiOutboundLog::find();
		$logHistory = new ActiveDataProvider([
			'query' => $query,
			'sort'=> ['defaultOrder' => ['id' => SORT_ASC]],
		]);
		$query->andWhere([
			'our_outbound_id' => $id,
		]);

        return $this->render('outbound/view', [
            'logHistory' =>$logHistory,
            'model' => $this->OutboundOne($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'outboundBoxBarcode' => EcommerceStock::find()->select('outbound_box')->andWhere(['outbound_id'=>$id])->scalar(),
        ]);
    }

    /**
     * Finds the EcommerceInbound model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EcommerceInbound the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function InboundOne($id)
    {
        if (($model = EcommerceInbound::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the EcommerceInbound model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EcommerceInbound the loaded model
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
        $activeSheet->setCellValue('E' . $i, 'Date Created')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('G' . $i, 'Date Packed')->getColumnDimension('G')->setAutoSize(true); // +
        $activeSheet->setCellValue('H' . $i, 'Date Courier')->getColumnDimension('H')->setAutoSize(true);
        $activeSheet->setCellValue('I' . $i, 'Shipment Source')->getColumnDimension('I')->setAutoSize(true);
        $activeSheet->setCellValue('J' . $i, 'Cancel reason')->getColumnDimension('J')->setAutoSize(true);

        if ($forDastan) {
            $activeSheet->setCellValue('K' . $i, 'TTN Delivery')->getColumnDimension('K')->setAutoSize(true);
        }

        $activeSheet->setCellValue('L' . $i, 'External order number')->getColumnDimension('L')->setAutoSize(true);
		
        $searchModel = new EcommerceOutboundSearch();

//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


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
            $activeSheet->setCellValue('B' . $i, \common\ecommerce\constants\OutboundStatus::getValue($model->status, 'EN'));
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

            $client_CancelReason = !empty($model->client_CancelReason) ? OutboundCancelStatus::getValue($model->client_CancelReason) : '-';
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

//    	die("actionOutboundExportToExcelWithProducts  ");
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
			$client_CancelReason = !empty($model->client_CancelReason) ? OutboundCancelStatus::getValue($model->client_CancelReason) : '-';
			$activeSheet->setCellValue('L' . $i, $client_CancelReason);
			$activeSheet->setCellValue('M' . $i, $model->external_order_number);
		}

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ordersWithProducts' . date('d.m.Y') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
//
//		return $this->render('outbound/index',[
//			'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
    }

    /*
    * Export to excel
    *
    **/
    public function actionOutboundExportToExcelWithProducts_OLD()
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
		
//        $activeSheet->setCellValue('M' . $i, 'TTN Delivery')->getColumnDimension('M')->setAutoSize(true);

//        $activeSheet->setCellValue('A' . $i, 'Order number'); // +
//        $activeSheet->setCellValue('B' . $i, 'Status'); // +
//        $activeSheet->setCellValue('C' . $i, 'Expected'); // +
//        $activeSheet->setCellValue('D' . $i, 'Reserved'); // +
//        $activeSheet->setCellValue('E' . $i, 'Scanned'); // +
//        $activeSheet->setCellValue('F' . $i, 'Date Created'); // +
//        $activeSheet->setCellValue('G' . $i, 'Date Packed'); // +
//        $activeSheet->setCellValue('H' . $i, 'Date Courier');


        $searchModel = new EcommerceOutboundSearch();

//        $forDastan  = Yii::$app->request->get('forDastan');
//        if ($forDastan) {
//            $dataProvider = $searchModel->searchByDefactoOrders(Yii::$app->request->post());
//        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        }

        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {
            /*
                $i++;
                $activeSheet->setCellValue('A' . $i, $model->order_number);
                $activeSheet->setCellValue('B' . $i, \common\ecommerce\constants\OutboundStatus::getValue($model->status, 'EN'));
                $activeSheet->setCellValue('C' . $i, '-');
                $activeSheet->setCellValue('D' . $i, '-');
                $activeSheet->setCellValue('E' . $i, '-');

                $activeSheet->setCellValue('F' . $i, $model->expected_qty);
                $activeSheet->setCellValue('G' . $i, $model->allocated_qty);
                $activeSheet->setCellValue('H' . $i, $model->accepted_qty);

                $created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
                $activeSheet->setCellValue('I' . $i, $created_at);

                $packing_date = !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date, $asDatetimeFormat) : '-';
                $activeSheet->setCellValue('J' . $i, $packing_date);

                $date_left_warehouse = !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse, $asDatetimeFormat) : '-';
                $activeSheet->setCellValue('K' . $i, $date_left_warehouse);

                $date_left_warehouse = !empty($model->client_CancelReason) ? OutboundCancelStatus::getValue($model->client_CancelReason) : '-';
                $activeSheet->setCellValue('L' . $i, $date_left_warehouse);
            */

            // SHOW PRODUCTS
            $productsInOrder = EcommerceOutboundItem::find()->andWhere(['outbound_id' => $model->id])->all();
            foreach ($productsInOrder as $product) {
                $i++;
                $activeSheet->setCellValueExplicit('A' . $i, $model->order_number, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('B' . $i, \common\ecommerce\constants\OutboundStatus::getValue($model->status, 'EN'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('C' . $i, $product->product_sku, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('D' . $i, $product->product_barcode, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('E' . $i, $product->product_model, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $activeSheet->setCellValueExplicit('F' . $i, $product->expected_qty, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('G' . $i, $product->allocated_qty, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('H' . $i, $product->accepted_qty, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
                $activeSheet->setCellValueExplicit('I' . $i, $created_at, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $packing_date = !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date, $asDatetimeFormat) : '-';
                $activeSheet->setCellValueExplicit('J' . $i, $packing_date, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $date_left_warehouse = !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse, $asDatetimeFormat) : '-';
                $activeSheet->setCellValueExplicit('K' . $i, $date_left_warehouse, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $client_CancelReason = !empty($model->client_CancelReason) ? OutboundCancelStatus::getValue($model->client_CancelReason) : '-';
                $activeSheet->setCellValue('L' . $i, $client_CancelReason);
                $activeSheet->setCellValue('M' . $i, $model->external_order_number);
				
//                $activeSheet->setCellValue('M' . $i, $model->client_ReferenceNumber);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ordersWithProducts' . date('d.m.Y') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
    * Export to excel
    *
    **/
    public function actionOutboundExportToExcelDastan()
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
        $activeSheet->setCellValue('M' . $i, 'TTN Delivery')->getColumnDimension('M')->setAutoSize(true);
        $activeSheet->setCellValue('N' . $i, 'External order number')->getColumnDimension('N')->setAutoSize(true);
		
        $searchModel = new EcommerceOutboundSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        $i = 1;
        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {

                $i++;
                $activeSheet->setCellValue('A' . $i, $model->order_number);
                $activeSheet->setCellValue('B' . $i, \common\ecommerce\constants\OutboundStatus::getValue($model->status, 'EN'));
                $activeSheet->setCellValue('C' . $i, '-');
                $activeSheet->setCellValue('D' . $i, '-');
                $activeSheet->setCellValue('E' . $i, '-');

                $activeSheet->setCellValue('F' . $i, $model->expected_qty);
                $activeSheet->setCellValue('G' . $i, $model->allocated_qty);
                $activeSheet->setCellValue('H' . $i, $model->accepted_qty);

                $created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
                $activeSheet->setCellValue('I' . $i, $created_at);

                $packing_date = !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date, $asDatetimeFormat) : '-';
                $activeSheet->setCellValue('J' . $i, $packing_date);

                $date_left_warehouse = !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse, $asDatetimeFormat) : '-';
                $activeSheet->setCellValue('K' . $i, $date_left_warehouse);

                $date_left_warehouse = !empty($model->client_CancelReason) ? OutboundCancelStatus::getValue($model->client_CancelReason) : '-';
                $activeSheet->setCellValue('L' . $i, $date_left_warehouse);

                $activeSheet->setCellValue('M' . $i, $model->client_ReferenceNumber);
                $activeSheet->setCellValue('N' . $i, $model->external_order_number);
				

            // SHOW PRODUCTS
            $productsInOrder = EcommerceOutboundItem::find()->andWhere(['outbound_id' => $model->id])->all();
            foreach ($productsInOrder as $product) {
                $i++;
                $activeSheet->setCellValueExplicit('A' . $i, $model->order_number, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('B' . $i, \common\ecommerce\constants\OutboundStatus::getValue($model->status, 'EN'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('C' . $i, $product->product_sku, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('D' . $i, $product->product_barcode, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('E' . $i, $product->product_model, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $activeSheet->setCellValueExplicit('F' . $i, $product->expected_qty, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('G' . $i, $product->allocated_qty, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('H' . $i, $product->accepted_qty, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
                $activeSheet->setCellValueExplicit('I' . $i, $created_at, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $packing_date = !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date, $asDatetimeFormat) : '-';
                $activeSheet->setCellValueExplicit('J' . $i, $packing_date, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $date_left_warehouse = !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse, $asDatetimeFormat) : '-';
                $activeSheet->setCellValueExplicit('K' . $i, $date_left_warehouse, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $client_CancelReason = !empty($model->client_CancelReason) ? OutboundCancelStatus::getValue($model->client_CancelReason) : '-';
                $activeSheet->setCellValue('L' . $i, $client_CancelReason);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ordersForDastan' . date('d.m.Y') . '.xlsx"');
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

    /*
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

    /**
     * Lists all EcommerceOutbound models.
     * @return mixed
     */
    public function actionOutboundByDefactoOrders()
    {
        $searchModel = new EcommerceOutboundSearch();
        $dataProvider = $searchModel->searchByDefactoOrders(Yii::$app->request->post());
        $dataProvider->pagination->pageSize = 100;

        return $this->render('outbound/index-by-defacto-orders', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReturnReport()
    {
        //die('ecommerce/defacto/report/return-report DIE');

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
        $activeSheet->setCellValue('A' . $i, 'Номер Заказа')->getColumnDimension('A')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('B' . $i, 'Шк товара')->getColumnDimension('B')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('C' . $i, 'Кол-во ожидали')->getColumnDimension('C')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('D' . $i, 'Кол-во приняли')->getColumnDimension('D')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('E' . $i, 'TTN')->getColumnDimension('E')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('F' . $i, 'короб отгрузки')->getColumnDimension('F')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('G' . $i, 'Дата создания')->getColumnDimension('G')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('H' . $i, 'IsRefundable')->getColumnDimension('H')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('I' . $i, 'RefundableMessage')->getColumnDimension('I')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('J' . $i, 'OrderSource')->getColumnDimension('J')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('K' . $i, 'Status')->getColumnDimension('K')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('L' . $i, 'SkuId')->getColumnDimension('L')->setAutoSize(true); // +; // +


        $allReport = EcommerceReturn::find()->asArray()->orderBy(['created_at'=>SORT_DESC])->all();
        $file = 'return-full-report-'.date('Y-m-d').'-result.xlsx';
        $asDatetimeFormat = 'php:d.m.Y';// H:i:s';
        foreach ($allReport as $return) {
            $allProducts = EcommerceReturnItem::find()->andWhere(['return_id'=>$return['id']])->asArray()->all();
            foreach ($allProducts as $product) {
                $i++;
                $activeSheet->setCellValue('A' . $i, $return['order_number']);
                $activeSheet->setCellValue('B' . $i, $product['product_barcode']);
                $activeSheet->setCellValue('C' . $i, $product['expected_qty']);
                $activeSheet->setCellValue('D' . $i, $product['accepted_qty']);
                $activeSheet->setCellValue('E' . $i, $return['client_ReferenceNumber']);
                $activeSheet->setCellValue('F' . $i, $return['outbound_box']);
                $activeSheet->setCellValue('G' . $i, Yii::$app->formatter->asDate($return['created_at'],$asDatetimeFormat));
                $activeSheet->setCellValue('H' . $i, $return['client_IsRefundable']);
                $activeSheet->setCellValue('I' . $i, $return['client_RefundableMessage']);
                $activeSheet->setCellValue('J' . $i, $return['client_OrderSource']);
                $activeSheet->setCellValue('K' . $i, ReturnOutboundStatus::getValue($return['status']));
                $activeSheet->setCellValue('L' . $i, $product['client_SkuId']);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file . '"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();

//        return \Yii::$app->response->sendFile($file);

        die('ok - ReturnReport - END');
    }
	

    /*
* Export to excel
*
**/
    public function actionInboundExportToExcel()
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
        $activeSheet->setCellValue('E' . $i, 'Date Created')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('G' . $i, 'Date Packed')->getColumnDimension('G')->setAutoSize(true); // +
        $activeSheet->setCellValue('H' . $i, 'Date Courier')->getColumnDimension('H')->setAutoSize(true);
        $activeSheet->setCellValue('I' . $i, 'Shipment Source')->getColumnDimension('I')->setAutoSize(true);
        $activeSheet->setCellValue('J' . $i, 'Cancel reason')->getColumnDimension('J')->setAutoSize(true);

        if ($forDastan) {
            $activeSheet->setCellValue('K' . $i, 'TTN Delivery')->getColumnDimension('K')->setAutoSize(true);
        }

        $activeSheet->setCellValue('L' . $i, 'External order number')->getColumnDimension('L')->setAutoSize(true);

        $searchModel = new EcommerceInboundSearch();

//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i, $model->order_number);
            $activeSheet->setCellValue('B' . $i, \common\ecommerce\constants\OutboundStatus::getValue($model->status, 'EN'));
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

            $client_CancelReason = !empty($model->client_CancelReason) ? OutboundCancelStatus::getValue($model->client_CancelReason) : '-';
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
    public function actionInboundExportToExcelForDastan()
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
        $activeSheet->setCellValue('E' . $i, 'Date Created')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('F' . $i, 'Place Address')->getColumnDimension('F')->setAutoSize(true); // +
        $activeSheet->setCellValue('G' . $i, 'Box Address')->getColumnDimension('G')->setAutoSize(true); // +

        $searchModel = new EcommerceInboundSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        $i = 1;
        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {
            $productsInOrder = EcommerceStock::find()->andWhere(['inbound_id' => $model->id])->all();
            foreach ($productsInOrder as $product) {

                $i++;
                $activeSheet->setCellValueExplicit('A' . $i, $model->order_number, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('B' . $i, \common\ecommerce\constants\InboundStatus::getValue($model->status, 'EN'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('C' . $i, $product->client_product_sku, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                $activeSheet->setCellValueExplicit('D' . $i, $product->product_barcode, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                $created_at = !empty ($product->created_at) ? Yii::$app->formatter->asDatetime($product->created_at, $asDatetimeFormat) : '-';
                $activeSheet->setCellValueExplicit('E' . $i, $created_at);
                $activeSheet->setCellValueExplicit('F' . $i, $product->place_address_barcode, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('G' . $i, $product->box_address_barcode, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="InboundOrdersForDastan' . date('d.m.Y') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

	public function actionGetLogs($id)
	{
		$outbound = EcommerceOutbound::find()->andWhere(['id' => $id])->one();
		$logList = EcommerceApiOutboundLog::find()->andWhere(['our_outbound_id' => $id])->orderBy(['id' => SORT_ASC])->all();
		$content = "";
		foreach ($logList as $log) {
			$content .= $log->method_name . " : " . $outbound->order_number . "\n" . "\n";
			$content .= "Request: " . "\n" . "\n";
			$content .= print_r(unserialize($log->request_data), true) . "\n" . "\n";
			$content .= "Response: " . "\n" . "\n";
			$content .= print_r(unserialize($log->response_data), true) . "\n" . "\n";
		}
		$filename = "outbound-logs-".$outbound->order_number.".txt";
		return  Yii::$app->response->sendContentAsFile( $content,$filename);
	}

	/*
 * Import to excel
 *
 **/
//	public function actionExportToExcelDataMatrix($id)
//	{
//		// /outbound/report/export-to-excel-data-matrix?id=72687
//		$outboundOrder = OutboundOrder::findOne($id);
//		$stockList  = Stock::find()
//						   ->select("outbound_picking_list_barcode,box_barcode,product_barcode,product_qrcode")
//						   ->andWhere(["outbound_order_id"=>$id])
//						   ->orderBy("product_qrcode,box_barcode")
//						   ->asArray()
//						   ->all();
//		$delimiter = "	";
//		$content = "";
//		foreach ($stockList as $line) {
//			$content .= implode($line,$delimiter).$delimiter."\n";
//		}
//		$filename = "data-matrix-".$outboundOrder->order_number.".csv";
//		return  Yii::$app->response->sendContentAsFile( $content,$filename);
//	}
}