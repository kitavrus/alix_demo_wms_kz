<?php

namespace app\modules\outbound\controllers;

use Yii;
use common\modules\outbound\models\OutboundBoxLabels;
use common\modules\outbound\models\OutboundBoxLabelsSearch;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\modules\client\models\Client;

/**
 * OutboundBoxSearchController implements the CRUD actions for OutboundBoxLabels model.
 */
class OutboundBoxLabelsController extends Controller
{

    /**
     * Lists all OutboundBoxLabels models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OutboundBoxLabelsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $clientArray = Client::getActiveItems();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientArray' => $clientArray,
        ]);
    }

//    /**
//     * Displays a single OutboundBoxLabels model.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }
//
//    /**
//     * Creates a new OutboundBoxLabels model.
//     * If creation is successful, the browser will be redirected to the 'view' page.
//     * @return mixed
//     */
//    public function actionCreate()
//    {
//        $model = new OutboundBoxLabels();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    /**
//     * Updates an existing OutboundBoxLabels model.
//     * If update is successful, the browser will be redirected to the 'view' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

//    /**
//     * Deletes an existing OutboundBoxLabels model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Deletes an existing OutboundBoxLabels model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDownloadLabelPdf($id)
    {
       $model = $this->findModel($id);

        return Yii::$app->response->sendFile($model->box_label_url,$model->filename);
    }

    /**
     * Finds the OutboundBoxLabels model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OutboundBoxLabels the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OutboundBoxLabels::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
