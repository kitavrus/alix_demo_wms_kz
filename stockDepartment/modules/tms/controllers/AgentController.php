<?php

namespace app\modules\tms\controllers;

use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\VarDumper;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use stockDepartment\modules\tms\models\TlAgentsSearch;
use common\modules\transportLogistics\models\TlCars;
use common\modules\transportLogistics\models\TlAgents;
//use common\modules\city\models\City;
//use common\modules\city\models\Region;

/**
 * AgentController implements the CRUD actions for TlAgents model.
 */
class AgentController extends Controller
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
     * Lists all TlAgents models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlAgentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TlAgents model.
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
     * Creates a new TlAgents model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlAgents();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlAgents model.
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
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            VarDumper::dump(ActiveForm::validate($model),10,true);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TlAgents model.
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
     * Finds the TlAgents model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlAgents the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlAgents::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Creates a new TlCars model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateCar()
    {
        $model = new TlCars();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->agent_id,'#'=>'title-cars']);
        } else {
            $currentAgentID = Yii::$app->request->getQueryParam('agent_id');
            $model->agent_id = $currentAgentID;
            return $this->render('create-car', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlCars model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateCar($id)
    {
        $model = $this->findCarModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->agent_id,'#'=>'title-cars']);
        } else {
            return $this->render('update-car', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Updates an existing TlCars model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionPrintBarcode($id)
    {
        $model = $this->findModel($id);


            return $this->render('_print-barcode', [
                'model' => $model,
            ]);

    }

    /**
     * Deletes an existing TlCars model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteCar($id)
    {
        $model = $this->findCarModel($id);
        $agent_id = $model->agent_id;
        $model->delete();

        return $this->redirect(['view','id'=>$agent_id,'#'=>'title-cars']);
    }

    /**
     * Finds the TlCars model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlCars the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCarModel($id)
    {
        if (($model = TlCars::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
    * Get List city by region id
     * TODO not used
    * */
//    public function actionGetCityByRegion()
//    {
//        $currentRegionID = Yii::$app->request->post('region_id');
//        Yii::$app->response->format = 'json';
//        return [
//            'message' => 'Success',
//            'data_options' => \yii\helpers\ArrayHelper::map(City::find()->where(['region_id'=>$currentRegionID])->orderBy('name')->all(),'id','name'),
//        ];
//    }

    /*
   * Get List redion by country id
     * TODO not used
   * */
//    public function actionGetRegionByCountry()
//    {
//        $currentCountryID = Yii::$app->request->post('country_id');
//        Yii::$app->response->format = 'json';
//        return [
//            'message' => 'Success',
//            'data_options' => \yii\helpers\ArrayHelper::map(Region::find()->where(['country_id'=>$currentCountryID])->orderBy('name')->all(),'id','name'),
//        ];
//    }

}
