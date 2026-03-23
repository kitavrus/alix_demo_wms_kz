<?php

namespace app\modules\report\controllers;

use app\modules\report\models\StockLostSearch;
use common\modules\outbound\models\OutboundOrder;
use Yii;
use common\modules\stock\models\Stock;
use clientDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

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

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'box' => $box,
        ]);
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
        $dataProvider->pagination = false;
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
        $activeSheet->setCellValue('F'.$i, 'Outbound order data'); // +
        $activeSheet->setCellValue('G'.$i, 'SkuId'); // +
        $activeSheet->setCellValue('H'.$i, 'LC Box'); // +

        $items = $dataProvider->getModels();
        foreach($items as $model) {
            $i++;
            $order = '';
            $orderDate = '';
            $outboundOrder = \common\modules\outbound\models\OutboundOrder::findOne($model->outbound_order_id);
            if($outboundOrder) {
                $order = $outboundOrder->order_number;//. ' / '. ;
                $orderDate = Yii::$app->formatter->asDate($outboundOrder->packing_date);
            }


//            $SkuId = $this->getAPISkuIdFromDefacto($model['product_barcode']);

            $activeSheet->setCellValue('A' . $i, $order);
            $activeSheet->setCellValue('B' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('C' . $i, $model['product_model']);
            $activeSheet->setCellValue('D' . $i, '1');
            $activeSheet->setCellValue('E' . $i, $model->getLostStatusValue());
            $activeSheet->setCellValue('F' . $i, $orderDate);
            $activeSheet->setCellValue('G' . $i, $model['field_extra1']);
//            $activeSheet->setCellValue('G' . $i,$SkuId);
            $activeSheet->setCellValue('H' . $i, $model['inbound_client_box']);

//            Stock::updateAll(['field_extra1'=>$SkuId],
//                [
//                    'client_id'=>2,
//                    'product_barcode'=>$model['product_barcode'],
//                ]
//            );

        }
        $filename = 'report-'.date('d-m-Y-H-i-s');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    protected function getAPISkuIdFromDefacto($LotOrSingleBarcode)
    {
        if(!empty($LotOrSingleBarcode)) {

            $api = new \stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2();
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
                return $value->SkuId;
            }
        }

        return '';
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
}