<?php

namespace app\modules\ecommerce\controllers\defacto;

use Yii;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use common\modules\client\models\Client;
use common\ecommerce\entities\EcommerceInventoryRows;
use common\ecommerce\entities\EcommerceInventory;
use common\ecommerce\entities\EcommerceInventorySearch;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\constants\StockAvailability;

//use common\modules\stock\models\Stock;
//use app\modules\stock\models\InventorySearch;

/*
1 - отчет б отсканнированных и не отсканированных по всем рядам
2 - отчет неразмещенные
3 - по ши  лота или короба найти старый адрес
4 - добавление плюсов и их размещение

 */
class InventoryController extends Controller
{
//    public  $stockAvailability;
//    public function init() {
//        $this->stockAvailability = new StockAvailability();
//        return parent::init();
//    }

    /**
     * Lists all EcommerceInventory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EcommerceInventorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EcommerceInventory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),

        ]);
    }

    /**
     * Creates a new EcommerceInventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EcommerceInventory();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->status = EcommerceInventory::STATUS_NEW;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $count = EcommerceInventory::find()->count();

            $model->order_number = $count.'-'.date('Ymd');

            return $this->render('create', [
                'model' => $model,
                'clientsArray' => Client::getActiveWMSItems(),
            ]);
        }
    }

    /**
     * Updates an existing EcommerceInventory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'clientsArray' => Client::getActiveWMSItems(),
            ]);
        }
    }

    /**
     * Deletes an existing EcommerceInventory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the EcommerceInventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EcommerceInventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EcommerceInventory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * */
    public function actionStart($id)
    {
        $model = $this->findModel($id);
        $model->status = EcommerceInventory::STATUS_IN_PROCESS;

        $condition = ['client_id'=>$model->client_id,'status_availability'=>StockAvailability::YES];

        // Выбираем ожидаемое кол-во товаров
        $stockAllQty = EcommerceStock::find()->andWhere($condition)->count();
        // Выбираем ожидаемое кол-во коробов
        $stockAllBoxQty = EcommerceStock::find()->andWhere($condition)->groupBy('box_address_barcode')->count();

        $model->expected_qty = $stockAllQty;
        $model->expected_places_qty = $stockAllBoxQty;
        $model->save(false);

        EcommerceStock::updateAll(['inventory_id'=>$model->id,
            'status_inventory'=>EcommerceInventory::STATUS_SCAN_NO,
            'inventory_box_address_barcode'=>'',
            'inventory_place_address_barcode'=>''
        ],$condition);

        EcommerceStock::updateAll([
            'status_inventory'=>EcommerceInventory::STATUS_SCAN_NO,
            'inventory_box_address_barcode'=>'',
            'inventory_place_address_barcode'=>''
        ],['client_id'=>$model->client_id]);


        Yii::$app->getSession()->setFlash('success', 'Инвентаризация успешно начата. Пожалуйста приступайте к сканированию товаров в рядах.');

        return $this->redirect(['view', 'id' => $model->id]);
    }
    //
    public function actionPrintFullReportExcel($id)
    {
        $model = $this->findModel($id);
        $items = EcommerceStock::find()
            ->select('count(product_barcode) as product_qty, product_barcode, product_model, status_inventory, inventory_box_address_barcode, box_address_barcode, inventory_place_address_barcode, place_address_barcode, client_product_sku')
            ->andWhere([
                //'status_inventory'=>[EcommerceInventory::STATUS_SCAN_NO,EcommerceInventory::STATUS_SCAN_PROCESS,EcommerceInventory::STATUS_SCAN_YES],
                'status_inventory'=>[EcommerceInventory::STATUS_SCAN_NO,EcommerceInventory::STATUS_SCAN_PROCESS],
                'inventory_id'=>$model->id,
                'status_availability'=>StockAvailability::YES,
            ])
            ->groupBy('product_barcode')
            ->orderBy([
//                'place_address_barcode'=>SORT_DESC,
                'address_sort_order'=>SORT_DESC,
            ])
            ->asArray()
            ->all();

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
        $activeSheet->setCellValue('A'.$i, 'Product barcode'); // +
        $activeSheet->setCellValue('B'.$i, 'Product model'); // +
        $activeSheet->setCellValue('C'.$i, 'Quantity'); // +
        $activeSheet->setCellValue('D'.$i, 'Status'); // +
        $activeSheet->setCellValue('E'.$i, 'inventory_box_address_barcode'); // +
        $activeSheet->setCellValue('F'.$i, 'inventory_place_address_barcode'); // +
        $activeSheet->setCellValue('G'.$i, 'box_address_barcode'); // +
        $activeSheet->setCellValue('H'.$i, 'place_address_barcode'); // +
        $activeSheet->setCellValue('I'.$i, 'client_product_sku'); // +

        // inventory_box_address_barcode, box_address_barcode, inventory_place_address_barcode, place_address_barcode

        foreach($items as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('B' . $i, $model['product_model']);
            $activeSheet->setCellValue('C' . $i, $model['product_qty']);
            $activeSheet->setCellValue('D' . $i, EcommerceInventory::getStatusScanValue($model['status_inventory']));
            $activeSheet->setCellValue('E' . $i, $model['inventory_box_address_barcode']);
            $activeSheet->setCellValue('F' . $i, $model['inventory_place_address_barcode']);
            $activeSheet->setCellValue('G' . $i, $model['box_address_barcode']);
            $activeSheet->setCellValue('H' . $i, $model['place_address_barcode']);
            $activeSheet->setCellValue('I' . $i, $model['client_product_sku']);

        }

        $filename = 'report-'.date('d-m-Y-H-i-s');


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
    //
    public function actionPrintFullReportPdf($id)
    {
        $model = $this->findModel($id);
        $items = EcommerceStock::find()
            ->select('count(product_barcode) as product_qty, product_barcode, product_model, place_address_barcode, inventory_box_address_barcode, status_inventory,client_product_sku')
            ->andWhere(['status_inventory'=>[EcommerceInventory::STATUS_SCAN_NO,EcommerceInventory::STATUS_SCAN_PROCESS],'inventory_id'=>$model->id])
            ->groupBy('product_barcode, place_address_barcode')
            ->orderBy([
//                'place_address_barcode'=>SORT_DESC,
                'address_sort_order'=>SORT_DESC,
            ])
            ->asArray()
            ->all();

        return $this->render('_print-full-report-pdf', ['items' => $items]);
    }
    //
    public function actionRemoveSecondaryAddress($id)
    {
        $model = $this->findModel($id);
        $condition = ['inventory_id'=>$model->id,'client_id'=>$model->client_id];
        EcommerceStock::updateAll(['place_address_barcode'=>''],$condition);
        Yii::$app->getSession()->setFlash('success', 'Адреса полок очищены. Можете приступать к размещению');

        return $this->redirect(['view', 'id' => $model->id]);
    }
    /*
     * */
    public function actionEnd($id)
    {
        $model = $this->findModel($id);
        $model->status = EcommerceInventory::STATUS_DONE;

        $condition = ['status_inventory'=>EcommerceInventory::STATUS_SCAN_YES,'client_id'=>$model->client_id,'status_availability'=>StockAvailability::YES];

        // Выбираем ожидаемое кол-во товаров
        $stockAllQty = EcommerceStock::find()->andWhere($condition)->count();
        // Выбираем ожидаемое кол-во коробов
        $stockAllBoxQty = EcommerceStock::find()->andWhere($condition)->groupBy('box_address_barcode')->count();

        $model->accepted_qty = $stockAllQty;
        $model->accepted_places_qty = $stockAllBoxQty;
        $model->save(false);

        Yii::$app->getSession()->setFlash('success', 'Инвентаризация успешно закончина');

        return $this->redirect(['view', 'id' => $model->id]);
    }
    /*
    * */
     public function actionPrintFullReportAcceptedPdf($id)
     {
         $model = $this->findModel($id);
         $items = EcommerceStock::find()
             ->select('count(product_barcode) as product_qty, product_barcode, product_model, place_address_barcode, inventory_box_address_barcode')
             ->andWhere(['status_inventory'=>EcommerceInventory::STATUS_SCAN_YES,'inventory_id'=>$model->id])
             ->groupBy('product_barcode, place_address_barcode')
             ->orderBy([
//                 'place_address_barcode'=>SORT_DESC,
                 'address_sort_order'=>SORT_DESC,
             ])
             ->asArray()
             ->all();

         return $this->render('_print-full-report-pdf', ['items' => $items]);
     }
    /*
    * */
    public function actionPrintFullReportAcceptedExcel($id)
    {
        $model = $this->findModel($id);
        $items = EcommerceStock::find()
            ->select('count(product_barcode) as product_qty, product_barcode, product_model,client_product_sku')
            ->andWhere(['status_inventory'=>EcommerceInventory::STATUS_SCAN_YES,'inventory_id'=>$model->id])
            ->groupBy('product_barcode')
            ->orderBy([
//                'place_address_barcode'=>SORT_DESC,
                'address_sort_order'=>SORT_DESC,
            ])
            ->asArray()
            ->all();

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
        $activeSheet->setCellValue('A'.$i, 'Product barcode'); // +
        $activeSheet->setCellValue('B'.$i, 'Product model'); // +
        $activeSheet->setCellValue('C'.$i, 'Quantity'); // +
        $activeSheet->setCellValue('D'.$i, 'Product skuId'); // +


        foreach($items as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('B' . $i, $model['product_model']);
            $activeSheet->setCellValue('C' . $i, $model['product_qty']);
            $activeSheet->setCellValue('D' . $i, $model['client_product_sku']);

        }

        $filename = 'report-'.date('d-m-Y-H-i-s');


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
    /*
     * */
    public function actionRemoveLost($id)
    {
        $model = $this->findModel($id);
        $condition = ['inventory_id'=>$model->id,'client_id'=>Client::CLIENT_DEFACTO,'status_inventory'=>[EcommerceInventory::STATUS_SCAN_NO,EcommerceInventory::STATUS_SCAN_PROCESS]];
        //Stock::updateAll(['deleted'=>'1'],$condition);
        EcommerceStock::deleteAll($condition);
        Yii::$app->getSession()->setFlash('success', 'Не найденные товары успешно удалены с систему учета складских остатков');

        return $this->redirect(['view', 'id' => $model->id]);
    }
     /*
     * */
    public function actionUpdateAcceptedInRow($id)
    {
        $model = EcommerceInventoryRows::findOne($id);
        $row = $model->row_number;
        $minMax = EcommerceInventory::getMinMaxSecondaryAddress($row);
        EcommerceInventory::saveAcceptedQtyByRow(EcommerceInventory::getCountProductInRow($minMax,$model->inventory_id),$row,$model->inventory_id);

        $rows = EcommerceInventoryRows::find()->andWhere(['inventory_id'=> $model->inventory_id])->all();

        foreach($rows as $rowItem) {
            if ( $rowItem->accepted_qty != $rowItem->expected_qty) {
                $row = $rowItem->row_number;
                $minMax = EcommerceInventory::getMinMaxSecondaryAddress($row);
                EcommerceInventory::saveAcceptedQtyByRow(EcommerceInventory::getCountProductInRow($minMax, $rowItem->inventory_id), $row, $rowItem->inventory_id);
            }
        }

        $accepted_qty = EcommerceStock::find()->andWhere([
            'inventory_id'=> $model->inventory_id,
            'status_inventory'=> EcommerceInventory::STATUS_SCAN_YES,
            'client_id'=>Client::CLIENT_DEFACTO,
            'status_availability'=>StockAvailability::YES,
        ])->count();

        EcommerceInventory::updateAll(['accepted_qty'=>$accepted_qty],['id'=>$model->inventory_id]);


        Yii::$app->getSession()->setFlash('success', 'Отсканированные товары для  '.$row.' '.' успешно обновлен');

        return $this->redirect(['view', 'id' => $model->inventory_id]);
    }
    /*
    *
    * */
    public function actionPrintDiffList($id)
    {
        $model = EcommerceInventoryRows::findOne($id);
        $row = $model->row_number;

        $minMax = EcommerceInventory::getMinMaxSecondaryAddress($row);
        EcommerceInventory::saveAcceptedQtyByRow(EcommerceInventory::getCountProductInRow($minMax,$model->inventory_id),$row,$model->inventory_id);
//        EcommerceInventory::saveAcceptedQtyByRow(EcommerceInventory::getCountProductInRow($minMax,$model->inventory_id),EcommerceInventory::getRowNumber($row),$model->inventory_id);

            $items = EcommerceStock::find()
                ->select('count(product_barcode) as product_qty,product_barcode,product_model, place_address_barcode, inventory_box_address_barcode')
                ->andWhere(['place_address_barcode'=>$minMax,'inventory_id'=>$model->inventory_id])
                ->andWhere('status_inventory = :status_inventory AND status_availability = :status_availability',[':status_inventory'=>EcommerceInventory::STATUS_SCAN_PROCESS,':status_availability'=>StockAvailability::YES])
                ->groupBy('product_barcode, place_address_barcode, inventory_box_address_barcode')
                ->orderBy([
                    'place_address_barcode'=>SORT_ASC,
                    'inventory_box_address_barcode'=>SORT_ASC,
                ])
                ->asArray()
                ->all();

        return $this->render('_print-diff-list-pdf', ['items' => $items]);
    }
    /*
    *
    * */
    public function actionPrintAcceptedList($id)
    {
        $model = EcommerceInventoryRows::findOne($id);
        $row = $model->row_number;

        $minMax = EcommerceInventory::getMinMaxSecondaryAddress($row);
//        EcommerceInventory::saveAcceptedQtyByRow(EcommerceInventory::getCountProductInRow($minMax,$model->inventory_id),EcommerceInventory::getRowNumber($row),$model->inventory_id);
        EcommerceInventory::saveAcceptedQtyByRow(EcommerceInventory::getCountProductInRow($minMax,$model->inventory_id),$row,$model->inventory_id);
        $items = EcommerceStock::find()
            ->select('count(product_barcode) as product_qty, product_barcode, product_model, place_address_barcode, box_address_barcode')
            ->andWhere(['place_address_barcode'=>$minMax,'inventory_id'=>$model->inventory_id])
            ->andWhere('status_inventory = :status_inventory AND status_availability = :status_availability',[':status_inventory'=>EcommerceInventory::STATUS_SCAN_YES,':status_availability'=>StockAvailability::YES])
            ->groupBy('product_barcode, box_address_barcode, place_address_barcode')
            ->orderBy([
                'place_address_barcode'=>SORT_ASC,
                'box_address_barcode'=>SORT_ASC,
            ])
            ->asArray()
            ->all();

        return $this->render('_print-accepted-list-pdf', ['items' => $items]);
    }
    /**
     *
    * */
    public function actionPrintFullAddressReportAcceptedExcel($id)
    {
        $model = $this->findModel($id);
        $items = EcommerceStock::find()
            ->select('count(product_barcode) as product_qty, product_barcode, product_model, place_address_barcode, box_address_barcode, client_product_sku')
            ->andWhere(['status_inventory'=>EcommerceInventory::STATUS_SCAN_YES,'inventory_id'=>$model->id])
            ->groupBy('product_barcode, inventory_box_address_barcode, place_address_barcode')
            ->orderBy([
//                'place_address_barcode'=>SORT_DESC,
                'address_sort_order'=>SORT_DESC,
            ])
            ->asArray()
            ->all();

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
        $activeSheet->setCellValue('A'.$i, 'Address'); // +
        $activeSheet->setCellValue('B'.$i, 'Box barcode'); // +
        $activeSheet->setCellValue('C'.$i, 'Product Barcode'); // +
        $activeSheet->setCellValue('D'.$i, 'Quantity'); // +
        $activeSheet->setCellValue('E'.$i, 'Product skuId'); // +

        foreach($items as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['place_address_barcode']);
            $activeSheet->setCellValue('B' . $i, $model['box_address_barcode']);
            $activeSheet->setCellValue('C' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('D' . $i, $model['product_qty']);
            $activeSheet->setCellValue('E' . $i, $model['client_product_sku']);
        }

        $filename = 'report-full-address-'.date('d-m-Y-H-i-s');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
}