<?php

namespace app\modules\city\controllers;

use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\stock\models\Stock;
use Yii;
use stockDepartment\components\Controller;
use common\modules\city\models\City;
use common\modules\city\models\Region;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use common\modules\city\models\CitySearch;
use yii\web\Response;


class DefaultController extends Controller
{
    /**
     * Lists all City models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

//        $cin = ConsignmentInboundOrders::find()->where(['id'=>10])->one();
//        if($cin) {
//            $inIds = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$cin->id])->asArray()->column();

//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            VarDumper::dump($inIds,10,true);
//            InboundOrderItem::updateAll(['deleted'=>1],['inbound_order_id'=>$inIds]);
//            InboundOrder::updateAll(['deleted'=>1],['id'=>$inIds]);
//            Stock::updateAll(['deleted'=>1],['inbound_order_id'=>$inIds]);
//        }


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single City model.
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
     * Creates a new City model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new City();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing City model.
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
     * Deletes an existing City model.
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
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /*
    * Get List city by region id
    * */
    public function actionGetCityByRegion()
    {
        $currentRegionID = Yii::$app->request->post('region_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
//        $data = [''=> Yii::t('transportLogistics/titles', 'Select city')];
        $data = \yii\helpers\ArrayHelper::map(City::find()->where(['region_id'=>$currentRegionID])->orderBy('name')->all(),'id','name');
        return [
            'message' => 'Success',
            'data_options' => $data,
        ];
    }

    /*
   * Get List redion by country id
   * */
    public function actionGetRegionByCountry()
    {
        $currentCountryID = Yii::$app->request->post('country_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = [''=> \app\modules\city\city::t('titles', 'Select region')];
        $data += \yii\helpers\ArrayHelper::map(Region::find()->where(['country_id'=>$currentCountryID])->orderBy('name')->all(),'id','name');
//        $data += [''=>Yii::t('forms','Please select')];
        return [
            'message' => 'Success',
            'data_options' => $data,
//            'data_options' => array_unshift($data,[''=>Yii::t('forms','Please select')]),
        ];
    }
}
