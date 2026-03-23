<?php

namespace app\modules\stock\controllers;

use app\modules\stock\models\StockLostSearch;
use common\modules\outbound\models\OutboundOrder;
use Yii;
use common\modules\stock\models\Stock;
use app\modules\stock\models\StockSearch;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\b2b\domains\stock\service\StockAdjustmentService;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class LostController extends Controller
{


    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockLostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $box = $searchModel->primary_address;

        //$dataProvider->query->andWhere(['status'=>[Stock::STATUS_OUTBOUND_PICKED]]);
        //$dataProvider->query->andWhere(['status_availability'=>[Stock::STATUS_AVAILABILITY_RESERVED]]);

       // $date = explode('/', '2016-05-01 / 2016-08-31');
        //$date[0] = trim($date[0]) . ' 00:00:00';
        //$date[1] = trim($date[1]) . ' 23:59:59';
       $outboundIDs = OutboundOrder::find()->select('id')
                            ->andWhere("accepted_qty != allocated_qty")
                            ->andWhere("packing_date != ''")
                            ->andWhere("client_id = '2'")
//                            ->andWhere(['between', 'packing_date', strtotime($date[0]), strtotime($date[1])])
                            ->column();

        $IDs =  Stock::find()->select('id')
                           ->andWhere(['status'=>[Stock::STATUS_OUTBOUND_PICKED]])
                            ->andWhere(['status_availability'=>[Stock::STATUS_AVAILABILITY_RESERVED]])
                            ->andWhere(['outbound_order_id'=>$outboundIDs])
                            ->column();

        $dataProvider->query->andWhere(['id'=>$IDs]);
        $dataProvider->query->orderBy(['outbound_order_id'=>SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'box' => $box,
        ]);
    }

    /**
     * Lists all Stock models.
     * @return mixed
     */
//    public function actionItemLost($id)
//    {
//        if($model = $this->findModel($id)){
//            $model->status_lost = Stock::STATUS_LOST_FULL;
//            if($model->save(false)){
//                Yii::$app->getSession()->setFlash('warning', Yii::t('stock/messages', 'Product {0} was completely lost', [$model->product_barcode]));
//            }
//        }
//
//        return $this->redirect('index');
//    }

    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionItemFound($id)
//    public function actionItemFound($id,$box)
    {
        $productBarcode = '';
        $box = '';
        if($model = $this->findModel($id)){

            $model->outbound_order_id = 0;
            $model->outbound_picking_list_id = 0;
            $model->outbound_picking_list_barcode = '';
//            $model->address_sort_order = '0';
//            $model->secondary_address = '';
//            $model->primary_address = $box;
            $model->status = Stock::STATUS_INBOUND_CONFIRM;
            $model->status_availability = Stock::STATUS_AVAILABILITY_YES;
            $model->status_lost = Stock::STATUS_LOST_AVAILABLE;

            $productBarcode = $model->product_barcode;
            if($model->save(false)) {
                Yii::$app->getSession()->setFlash('success', Yii::t('stock/messages', 'Product {0} was found', [$model->product_barcode]));
            }
        }

        return $this->redirect(['index','StockLostSearch[product_barcode]'=>$productBarcode,'StockLostSearch[primary_address]'=>$box]);
    }

    /*
   * Print pick list
   *
   * */
    public function actionPrintLostList()
    {
        $idsData = Yii::$app->request->get('ids');

        $ids = [];
        if (!empty($idsData)) {
            $ids = explode(',', $idsData);
        }


        return $this->render('_print-lost-list-pdf', ['ids' => $ids]);
    }

    /*
     *
     * */
    public function actionExcel()
    {
        $searchModel = new StockLostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $box = $searchModel->primary_address;

        $outboundIDs = OutboundOrder::find()->select('id')
            ->andWhere("accepted_qty != allocated_qty")
            ->andWhere("packing_date != ''")
            ->andWhere("client_id = '2'")
            ->column();

        $IDs =  Stock::find()->select('id')
            ->andWhere(['status'=>[Stock::STATUS_OUTBOUND_PICKED]])
            ->andWhere(['status_availability'=>[Stock::STATUS_AVAILABILITY_RESERVED]])
            ->andWhere(['outbound_order_id'=>$outboundIDs])
            ->column();

        $dataProvider->query->andWhere(['id'=>$IDs]);
        $dataProvider->query->orderBy(['outbound_order_id'=>SORT_DESC]);

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
            ->setTitle('report-'.date('d.m.Y'));

        $i = 1;
        $activeSheet->setCellValue('A'.$i, 'Order'); // +
        $activeSheet->setCellValue('B'.$i, 'Product barcode'); // +
        $activeSheet->setCellValue('C'.$i, 'Product model'); // +
        $activeSheet->setCellValue('D'.$i, 'Quantity'); // +
        $activeSheet->setCellValue('E'.$i, 'Status'); // +
        $activeSheet->setCellValue('F'.$i, 'Our Box'); // +
        $activeSheet->setCellValue('G'.$i, 'LC Box'); // +
        $activeSheet->setCellValue('H'.$i, 'Packing date'); // +
        $activeSheet->setCellValue('I'.$i, 'Parent order number'); // +
        $activeSheet->setCellValue('J'.$i, 'Our place address'); // +

        $items = $dataProvider->getModels();
        foreach($items as $model) {
            $i++;
            $order = '';
            $parentOrderNumber = '';
            $outboundOrder = \common\modules\outbound\models\OutboundOrder::findOne($model->outbound_order_id);
            if($outboundOrder) {
                $order = $outboundOrder->order_number;//. ' / '.Yii::$app->formatter->asDate($outboundOrder->packing_date) ;
                $parentOrderNumber = $outboundOrder->parent_order_number;
            }
            $activeSheet->setCellValue('A' . $i, $order);
            $activeSheet->setCellValue('B' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('C' . $i, $model['product_model']);
            $activeSheet->setCellValue('D' . $i, '1');
            $activeSheet->setCellValue('E' . $i, $model->getLostStatusValue());

            $activeSheet->setCellValue('F' . $i, $model['primary_address']);
            $activeSheet->setCellValue('G' . $i, $model['inbound_client_box']);
            $activeSheet->setCellValue('H' . $i, Yii::$app->formatter->asDate($outboundOrder->packing_date));
            $activeSheet->setCellValue('I' . $i, $parentOrderNumber);
            $activeSheet->setCellValue('J' . $i, $model['secondary_address']);
        }

        $filename = 'report-'.date('d-m-Y-H-i-s');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /**
     * Finds the Stock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	
    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionItemLost($id)
    {
        if($model = $this->findModel($id)){
            $stockAdjustmentService = new StockAdjustmentService();
            $stockAdjustmentService->minus($model->id);
            Yii::$app->getSession()->setFlash('success', Yii::t('stock/messages', 'Product {0} was found', [$model->product_barcode]));
        }

        return $this->redirect(['index']);
    }
	
}