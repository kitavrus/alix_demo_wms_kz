<?php

namespace app\modules\city\controllers;

use common\modules\city\models\City;
use common\modules\city\models\Country;
use common\modules\city\models\Region;
use stockDepartment\modules\city\models\RouteDirectionCitySearch;
use common\modules\city\models\RouteDirectionToCity;
use Yii;
use common\modules\city\models\RouteDirections;
use common\modules\city\models\RouteDirectionSearch;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * RouteDirectionController implements the CRUD actions for RouteDirections model.
 */
class RouteDirectionController extends Controller
{
    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
//        ];
//    }

    /**
     * Lists all RouteDirections models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RouteDirectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RouteDirections model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $routeDirection = $this->findModel($id);
        $cityLinked = $routeDirection->getCityIDs();

        $routeDirectionCitySearchModel = new RouteDirectionCitySearch();
        $cityProvider = $routeDirectionCitySearchModel->search(Yii::$app->request->queryParams);

        $cityArray = City::getArrayData();
        $regionArray = Region::getArrayData();
        $countryArray = Country::getArrayData();

        return $this->render('view', [
            'routeDirectionModel' => $routeDirection,
            'cityProvider' => $cityProvider,
            'cityLinked' => $cityLinked,
            'cityArray' => $cityArray,
            'regionArray' => $regionArray,
            'countryArray' => $countryArray,
            'rdCitySearchModel' => $routeDirectionCitySearchModel,
        ]);
    }

    /*
     *
     * */
    public function actionLinkCity()
    {
       $id = Yii::$app->request->post('id');
       $rdID = Yii::$app->request->post('rdID');
       $checked = Yii::$app->request->post('checked');

       if($id) {
//           VarDumper::dump($checked,10,true);
          if($checked == 'true') {
              $rdToCity = new RouteDirectionToCity();
              $rdToCity->city_id = $id;
              $rdToCity->route_direction_id = $rdID;
              $rdToCity->save(false);
          } else {
              RouteDirectionToCity::deleteAll(['city_id'=>$id,'route_direction_id'=>$rdID]);
          }
       }
       Yii::$app->response->format = Response::FORMAT_JSON;
       return [$checked];
    }

    /**
     * Creates a new RouteDirections model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RouteDirections();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RouteDirections model.
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
     * Deletes an existing RouteDirections model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        RouteDirectionToCity::deleteAll(['route_direction_id'=>$id]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the RouteDirections model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RouteDirections the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RouteDirections::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
