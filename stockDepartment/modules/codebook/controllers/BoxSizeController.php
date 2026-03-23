<?php

namespace  app\modules\codebook\controllers;

use common\components\BarcodeManager;
use Yii;
use common\modules\codebook\models\BoxSize;
use stockDepartment\modules\codebook\models\BoxSizeSearch;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BoxSizeController implements the CRUD actions for BoxSize model.
 */
class BoxSizeController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all BoxSize models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BoxSizeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

//        $constM3 = 333;
//        $precision = 3;
//        $separateCode = 'x';
//        $code = '';
//        $code = '60X40X40/20';
//        $code = '60X40X40\20';
//        $code = '60X40X40';
//        $code = '20';
//        $code = 'CHANGE-BOX';
//        $code = '123123123';
//        $code = '10363353-36242-1';
//        $code = 'b0000047476';
//        $code = '9000003335094';
//        $code = '700000047476';
//        VarDumper::dump(strlen($code),10,true);
//        VarDumper::dump(BarcodeManager::isM3BoxBorder($code),10,true);
//        VarDumper::dump(BarcodeManager::getBoxM3($code),10,true);
//        echo "<br />";
//        die('--STOP--');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BoxSize model.
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
     * Creates a new BoxSize model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BoxSize();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing BoxSize model.
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
            ]);
        }
    }

    /**
     * Deletes an existing BoxSize model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BoxSize model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BoxSize the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BoxSize::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
