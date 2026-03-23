<?php

namespace app\modules\store\controllers;

use Yii;
use common\modules\store\models\StoreReviews;
use clientDepartment\modules\store\models\StoreReviewSearch;
//use yii\web\Controller;
use yii\bootstrap\ActiveForm;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use clientDepartment\components\Controller;
use clientDepartment\modules\client\components\ClientManager;

/**
 * StoreReviewController implements the CRUD actions for StoreReviews model.
 */
class StoreReviewController extends Controller
{
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * Lists all StoreReviews models.
     * @return mixed
     */
//    public function actionIndex()
//    {
//        $searchModel = new StoreReviewSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    /**
     * Displays a single StoreReviews model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if(!ClientManager::canUpdateStoreReview($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $model->scenario = 'update';
        if ( $model->load(Yii::$app->request->post()) ) {
            if(empty($model->delivery_datetime)) {
                $model->delivery_datetime = time();
            }

            if(!$model->save()) {
                $errors = '';
                foreach ($model->getErrors() as $error) {
                    $errors .= array_shift($error)."<br />";
                }
                Yii::$app->session->setFlash('error',$errors);
            } else {
                Yii::$app->session->setFlash('success',"Дата доставки успешно установлена");
            }
        }

        return $this->redirect(['/transportLogistics/tl-delivery-proposal/view', 'id' => $model->tl_delivery_proposal_id]);
    }

    /**
     * Creates a new StoreReviews model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new StoreReviews();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing StoreReviews model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
////            return $this->redirect(['/transportLogistics/tl-delivery-proposal/view', 'id' => $model->tl_delivery_proposal_id]);
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing StoreReviews model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the StoreReviews model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StoreReviews the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StoreReviews::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
