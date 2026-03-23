<?php

namespace app\modules\bookkeeper\controllers;

use common\components\BookkeeperManager;
use common\modules\client\models\Client;
use common\modules\transportLogistics\components\TLHelper;
use Yii;
use stockDepartment\modules\bookkeeper\models\Bookkeeper;
use stockDepartment\modules\bookkeeper\models\BookkeeperSearch;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

//use yii\filters\VerbFilter;

/**
 * DefaultController implements the CRUD actions for Bookkeeper model.
 */
class DefaultController extends Controller
{
    /**
     * Lists all Bookkeeper models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BookkeeperSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        $storeArray = TLHelper::getStockPointArray(null,false,false,'small');
        $storeArray = TLHelper::getStockPointArray();
        $balance = Yii::$app->formatter->asCurrency(BookkeeperManager::showBalance());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'balance' => $balance,
            'storeArray' => $storeArray,
        ]);
    }

    /**
     * Displays a single Bookkeeper model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $storeArray = TLHelper::getStockPointArray(null,false,false,'full');
        return $this->render('view', [
            'model' => $this->findModel($id),
            'storeArray' => $storeArray,
        ]);
    }

    /**
     * Creates a new Bookkeeper model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type = Bookkeeper::TYPE_MINUS)
    {
        $model = new Bookkeeper();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);
            BookkeeperManager::recalculateBalance();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model->type_id = $type;
            $model->status = Bookkeeper::STATUS_NEW;
            $model->doc_type_id = Bookkeeper::DOC_TYPE_NO_CHECK;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Bookkeeper model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);

            BookkeeperManager::recalculateBalance();
            BookkeeperManager::updateDpRouteUnforeseenExpenses($model);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Bookkeeper model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);;
        $model->deleted = 1;
        $model->save(false);

        BookkeeperManager::recalculateBalance();
        return $this->redirect(['index']);
    }

    /**
     * Done
     * @param integer $id
     * @return mixed
     */
    public function actionDone()
    {
        BookkeeperManager::recalculateBalance();

        $searchModel = new BookkeeperSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {
            $model->status = Bookkeeper::STATUS_DONE;
            $model->save(false);
        }

        Yii::$app->session->setFlash('success', 'Расходы успешно закрыты.');

        return $this->redirect(['index']);
    }

    /*
    * Import to excel
    *
    **/
    public function actionExportToExcel()
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
            ->setTitle('bookkeeper ' . date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Дата'); // +
        $activeSheet->setCellValue('B' . $i, 'Приход'); // +
        $activeSheet->setCellValue('C' . $i, 'Поставщик'); // +
        $activeSheet->setCellValue('D' . $i, 'Описание'); // +
        $activeSheet->setCellValue('E' . $i, 'Отдел'); // +
        $activeSheet->setCellValue('F' . $i, 'Расход'); // +
        $activeSheet->setCellValue('G' . $i, 'Остаток'); // +
        $activeSheet->setCellValue('H' . $i, 'Статус'); // +
        $activeSheet->setCellValue('I' . $i, 'Тип док-та'); // +
        $activeSheet->setCellValue('J' . $i, 'Заявка на доставку'); // +
        $activeSheet->setCellValue('K' . $i, 'Форма оплаты'); // +
        $activeSheet->setCellValue('L' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('M' . $i, 'Создал'); // +
        $activeSheet->setCellValue('N' . $i, 'Изменил');
        $activeSheet->setCellValue('O' . $i, 'ID заявки'); // +
//        $activeSheet->setCellValue('P' . $i, 'Статус'); // +
//        $activeSheet->setCellValue('Q' . $i, 'Статус груза'); // +
//        $activeSheet->setCellValue('R' . $i, 'WMS'); // +
//        $activeSheet->setCellValue('S' . $i, 'TR'); // +
//        $activeSheet->setCellValue('T' . $i, 'FULL'); // +

        $searchModel = new BookkeeperSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

//        $storeArray = TLHelper::getStockPointArray(null,false,false,'full');
        $storeArray = TLHelper::getStockPointArray();
        $clientArray = Client::getActiveTMSItems();
        foreach ($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i, $model->showDateAt());
            $activeSheet->setCellValue('B' . $i, $model->showPlus());
            $activeSheet->setCellValue('C' . $i, $model->name_supplier); //
            $activeSheet->setCellValue('D' . $i, $model->description);
            $activeSheet->setCellValue('E' . $i, $model->getDepartmentIdValue());
            $activeSheet->setCellValue('F' . $i, $model->showMinus());
            $activeSheet->setCellValue('G' . $i, $model->balance_sum);
            $activeSheet->setCellValue('H' . $i, $model->getStatusValue());
            $activeSheet->setCellValue('I' . $i, $model->getDocTypeIdValue());
            $activeSheet->setCellValue('J' . $i, $model->showDp($storeArray,false));
            $activeSheet->setCellValue('K' . $i, $model->getCashTypeValue());
            $activeSheet->setCellValue('L' . $i, $model->showClientTitleDp($clientArray));
            $activeSheet->setCellValue('M' . $i, $model::getUserName($model->created_user_id));
            $activeSheet->setCellValue('N' . $i,$model::getUserName($model->updated_user_id));
            $activeSheet->setCellValue('O' . $i,$model->tl_delivery_proposal_id);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="bookkeeper-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

/*
 *
 * */
    public function actionFullRecalculate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        BookkeeperManager::recalculateBalance(true);
        return ['Y'];
    }
    /**
     * Finds the Bookkeeper model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bookkeeper the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bookkeeper::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}