<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\barcode;

use stockDepartment\modules\intermode\controllers\ecommerce\barcode\domain\forms\PrintBarcode;
use Yii;
use stockDepartment\modules\intermode\controllers\ecommerce\barcode\domain\entities\EcommerceBarcodeManager;
use stockDepartment\modules\intermode\controllers\ecommerce\barcode\domain\entities\EcommerceBarcodeManagerSerach;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DefaultController implements the CRUD actions for EcommerceBarcodeManager model.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all EcommerceBarcodeManager models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EcommerceBarcodeManagerSerach();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EcommerceBarcodeManager model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EcommerceBarcodeManager model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EcommerceBarcodeManager();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EcommerceBarcodeManager model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EcommerceBarcodeManager model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EcommerceBarcodeManager model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EcommerceBarcodeManager the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EcommerceBarcodeManager::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /*
 * Print any barcode
 *
 * */
    public function actionPrintBarcode()
    {
        $printBarcodeForm = new PrintBarcode();
        if($printBarcodeForm->load(Yii::$app->request->post()) && $printBarcodeForm->validate()) {

        }

        return $this->render('print-barcode',['printBarcodeForm'=>$printBarcodeForm]);
    }

    /*
    * Print barcode
    * @param integer $codebook_id
    * @param integer $qty
    * */
    public function actionPrintBarcodePdf($codebook_id,$qty)
    {
        return $this->render('_print_barcode',[
            'quantity'=>$qty,
            'model'=>EcommerceBarcodeManager::findOne($codebook_id),
        ]);
    }
}
