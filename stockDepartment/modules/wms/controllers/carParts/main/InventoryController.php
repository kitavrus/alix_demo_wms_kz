<?php

namespace app\modules\wms\controllers\carParts\main;

use common\clientObject\constants\Constants;
use common\modules\client\models\Client;
use common\modules\stock\models\Stock;
use Yii;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;

use stockDepartment\modules\wms\models\carParts\Inventory;
use stockDepartment\modules\wms\models\carParts\InventoryRows;
use stockDepartment\modules\wms\models\carParts\InventorySearch;

/*
1 - отчет б отсканнированных и не отсканированных по всем рядам
2 - отчет неразмещенные
3 - по ши  лота или короба найти старый адрес
4 - добавление плюсов и их размещение

 */
class InventoryController extends Controller
{
    /**
     * Lists all Inventory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InventorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Inventory model.
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
     * Creates a new Inventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Inventory();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->status = Inventory::STATUS_NEW;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $count = Inventory::find()->count();

            $model->order_number = $count.'-'.date('Ymd');

            return $this->render('create', [
                'model' => $model,
                'clientsArray' => Client::getActiveByIDs(Constants::getCarPartClientIDs()),
            ]);
        }
    }

    /**
     * Updates an existing Inventory model.
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
     * Deletes an existing Inventory model.
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
     * Finds the Inventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Inventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Inventory::findOne($id)) !== null) {
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
        $model->status = Inventory::STATUS_IN_PROCESS;

        $condition = ['client_id'=>$model->client_id,'status_availability'=>Stock::STATUS_AVAILABILITY_YES];

        // Выбираем ожидаемое кол-во товаров
        $stockAllQty = Stock::find()->andWhere($condition)->count();
        // Выбираем ожидаемое кол-во коробов
        $stockAllBoxQty = Stock::find()->andWhere($condition)->groupBy('primary_address')->count();

        $model->expected_qty = $stockAllQty;
        $model->expected_places_qty = $stockAllBoxQty;
        $model->save(false);

        Stock::updateAll(['inventory_id'=>$model->id,
            'status_inventory'=>Inventory::STATUS_SCAN_NO,
            'inventory_primary_address'=>'',
            'inventory_secondary_address'=>''
        ],$condition);

        Stock::updateAll([
            'status_inventory'=>Inventory::STATUS_SCAN_NO,
            'inventory_primary_address'=>'',
            'inventory_secondary_address'=>''
        ],['client_id'=>$model->client_id]);


        Yii::$app->getSession()->setFlash('success', 'Инвентаризация успешно начата. Пожалуйста приступайте к сканированию товаров в рядах.');

        return $this->redirect(['view', 'id' => $model->id]);
    }
    //
    public function actionPrintFullReportExcel($id)
    {
        $model = $this->findModel($id);
        $items = Stock::find()
            ->select('count(product_barcode) as product_qty, product_barcode, product_model, status_inventory, inventory_primary_address, primary_address, inventory_secondary_address, secondary_address')
            ->andWhere([
                //'status_inventory'=>[Inventory::STATUS_SCAN_NO,Inventory::STATUS_SCAN_PROCESS,Inventory::STATUS_SCAN_YES],
                'status_inventory'=>[Inventory::STATUS_SCAN_NO,Inventory::STATUS_SCAN_PROCESS],
                'inventory_id'=>$model->id,
                'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
            ])
            ->groupBy('product_barcode')
            ->orderBy([
//                'secondary_address'=>SORT_DESC,
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
        $activeSheet->setCellValue('E'.$i, 'inventory_primary_address'); // +
        $activeSheet->setCellValue('F'.$i, 'inventory_secondary_address'); // +
        $activeSheet->setCellValue('G'.$i, 'primary_address'); // +
        $activeSheet->setCellValue('H'.$i, 'secondary_address'); // +

        // inventory_primary_address, primary_address, inventory_secondary_address, secondary_address

        foreach($items as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('B' . $i, $model['product_model']);
            $activeSheet->setCellValue('C' . $i, $model['product_qty']);
            $activeSheet->setCellValue('D' . $i, Inventory::getStatusScanValue($model['status_inventory']));
            $activeSheet->setCellValue('E' . $i, $model['inventory_primary_address']);
            $activeSheet->setCellValue('F' . $i, $model['inventory_secondary_address']);
            $activeSheet->setCellValue('G' . $i, $model['primary_address']);
            $activeSheet->setCellValue('H' . $i, $model['secondary_address']);

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
        $items = Stock::find()
            ->select('count(product_barcode) as product_qty, product_barcode, product_model, secondary_address, inventory_primary_address, status_inventory')
            ->andWhere(['status_inventory'=>[Inventory::STATUS_SCAN_NO,Inventory::STATUS_SCAN_PROCESS],'inventory_id'=>$model->id])
            ->groupBy('product_barcode, secondary_address')
            ->orderBy([
//                'secondary_address'=>SORT_DESC,
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
        Stock::updateAll(['secondary_address'=>''],$condition);
        Yii::$app->getSession()->setFlash('success', 'Адреса полок очищены. Можете приступать к размещению');

        return $this->redirect(['view', 'id' => $model->id]);
    }




    /*
     * */
    public function actionEnd($id)
    {
        $model = $this->findModel($id);
        $model->status = Inventory::STATUS_DONE;

        $condition = ['status_inventory'=>Inventory::STATUS_SCAN_YES,'client_id'=>$model->client_id,'status_availability'=>Stock::STATUS_AVAILABILITY_YES];

        // Выбираем ожидаемое кол-во товаров
        $stockAllQty = Stock::find()->andWhere($condition)->count();
        // Выбираем ожидаемое кол-во коробов
        $stockAllBoxQty = Stock::find()->andWhere($condition)->groupBy('primary_address')->count();

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
         $items = Stock::find()
             ->select('count(product_barcode) as product_qty, product_barcode, product_model, secondary_address, inventory_primary_address')
             ->andWhere(['status_inventory'=>Inventory::STATUS_SCAN_YES,'inventory_id'=>$model->id])
             ->groupBy('product_barcode, secondary_address')
             ->orderBy([
//                 'secondary_address'=>SORT_DESC,
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
        $items = Stock::find()
            ->select('count(product_barcode) as product_qty, product_barcode, product_model')
            ->andWhere(['status_inventory'=>Inventory::STATUS_SCAN_YES,'inventory_id'=>$model->id])
            ->groupBy('product_barcode')
            ->orderBy([
//                'secondary_address'=>SORT_DESC,
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


        foreach($items as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('B' . $i, $model['product_model']);
            $activeSheet->setCellValue('C' . $i, $model['product_qty']);

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
//        $condition = ['inventory_id'=>$model->id,'client_id'=>Client::CLIENT_DEFACTO,'status_inventory'=>[Inventory::STATUS_SCAN_NO,Inventory::STATUS_SCAN_PROCESS]];
        $condition = ['inventory_id'=>$model->id,'status_inventory'=>[Inventory::STATUS_SCAN_NO,Inventory::STATUS_SCAN_PROCESS]];
        //Stock::updateAll(['deleted'=>'1'],$condition);
        Stock::deleteAll($condition);
        Yii::$app->getSession()->setFlash('success', 'Не найденные товары успешно удалены с систему учета складских остатков');

        return $this->redirect(['view', 'id' => $model->id]);
    }

     /*
     * */
    public function actionUpdateAcceptedInRow($id)
    {
        $model = InventoryRows::findOne($id);
        $row = $model->row_number;
        //$minMax = Inventory::getMinMaxSecondaryAddress($row);
        //Inventory::saveAcceptedQtyByRow(Inventory::getCountProductInRow($minMax,$model->inventory_id),$row,$model->inventory_id);

        $rows = InventoryRows::find()->andWhere(['inventory_id'=> $model->inventory_id])->all();

        foreach($rows as $rowItem) {
            if ( $rowItem->accepted_qty != $rowItem->expected_qty) {
                $row = $rowItem->row_number;
                $minMax = Inventory::getMinMaxSecondaryAddress($row);
                Inventory::saveAcceptedQtyByRow(Inventory::getCountProductInRow($minMax, $rowItem->inventory_id), $row, $rowItem->inventory_id);
            }
        }

        $accepted_qty = Stock::find()->andWhere([
            'inventory_id'=> $model->inventory_id,
            'status_inventory'=> Inventory::STATUS_SCAN_YES,
            //'client_id'=>Client::CLIENT_DEFACTO,
            'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
        ])->count();

        Inventory::updateAll(['accepted_qty'=>$accepted_qty],['id'=>$model->inventory_id]);


        Yii::$app->getSession()->setFlash('success', 'Отсканированные товары для  '.$row.' '.' успешно обновлен');

        return $this->redirect(['view', 'id' => $model->inventory_id]);
    }

    /*
    *
    * */
    public function actionPrintDiffList($id)
    {
        $model = InventoryRows::findOne($id);
        $row = $model->row_number;

        $minMax = Inventory::getMinMaxSecondaryAddress($row);
        Inventory::saveAcceptedQtyByRow(Inventory::getCountProductInRow($minMax,$model->inventory_id),$row,$model->inventory_id);
//        Inventory::saveAcceptedQtyByRow(Inventory::getCountProductInRow($minMax,$model->inventory_id),Inventory::getRowNumber($row),$model->inventory_id);

            $items = Stock::find()
                ->select('count(product_barcode) as product_qty,product_barcode,product_model, secondary_address, inventory_primary_address')
                ->andWhere(['secondary_address'=>$minMax,'inventory_id'=>$model->inventory_id])
                ->andWhere('status_inventory = :status_inventory AND status_availability = :status_availability',[':status_inventory'=>Inventory::STATUS_SCAN_PROCESS,':status_availability'=>Stock::STATUS_AVAILABILITY_YES])
                ->groupBy('product_barcode, secondary_address, inventory_primary_address')
                ->orderBy([
                    //'secondary_address'=>SORT_DESC,
                    'address_sort_order'=>SORT_DESC,
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
        $model = InventoryRows::findOne($id);
        $row = $model->row_number;

        $minMax = Inventory::getMinMaxSecondaryAddress($row);
//        Inventory::saveAcceptedQtyByRow(Inventory::getCountProductInRow($minMax,$model->inventory_id),Inventory::getRowNumber($row),$model->inventory_id);
        Inventory::saveAcceptedQtyByRow(Inventory::getCountProductInRow($minMax,$model->inventory_id),$row,$model->inventory_id);
        $items = Stock::find()
            ->select('count(product_barcode) as product_qty, product_barcode, product_model, secondary_address, primary_address')
            ->andWhere(['secondary_address'=>$minMax,'inventory_id'=>$model->inventory_id])
            ->andWhere('status_inventory = :status_inventory AND status_availability = :status_availability',[':status_inventory'=>Inventory::STATUS_SCAN_YES,':status_availability'=>Stock::STATUS_AVAILABILITY_YES])
            ->groupBy('product_barcode, primary_address, secondary_address')
            ->orderBy([
//                'secondary_address'=>SORT_DESC,
                'address_sort_order'=>SORT_DESC,
            ])
            ->asArray()
            ->all();

        return $this->render('_print-accepted-list-pdf', ['items' => $items]);
    }
}