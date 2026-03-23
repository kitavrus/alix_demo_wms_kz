<?php

namespace app\modules\tms\controllers;

use common\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRoute;
use common\modules\transportLogistics\models\TlDeliveryProposalDefaultUnforeseenExpenses;
use Yii;
use common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute;
use stockDepartment\modules\tms\models\TlDeliveryProposalDefaultRouteSearch;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use common\modules\transportLogistics\components\TLHelper;

/**
 * DefaultRouteController implements the CRUD actions for TlDeliveryProposalDefaultRoute model.
 */
class DefaultRouteController extends Controller
{

    /**
     * Lists all TlDeliveryProposalDefaultRoute models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlDeliveryProposalDefaultRouteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       //$clientArray = Client::getActiveItems();
        $storeArray = TLHelper::getStockPointArray();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //'clientArray' => $clientArray,
            'storeArray' => $storeArray,
        ]);
    }

    /**
     * Displays a single TlDeliveryProposalDefaultRoute model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $storeArray = TLHelper::getStockPointArray();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'storeArray' => $storeArray,
        ]);
    }

    /**
     * Creates a new TlDeliveryProposalDefaultRoute model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlDeliveryProposalDefaultRoute();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlDeliveryProposalDefaultRoute model.
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
     * Finds the TlDeliveryProposalDefaultRoute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposalDefaultRoute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposalDefaultRoute::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
  * Add new unforeseen expenses to route
  * @param integer $route_id Delivery proposal route id
  * */
    public function actionAddRouteUnforeseenExpenses($sub_route_id)
    {
        $modelDRoute = TlDeliveryProposalDefaultSubRoute::findOne($sub_route_id);
        $model = new TlDeliveryProposalDefaultUnforeseenExpenses();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->tl_delivery_proposal_default_route_id,'#'=>'title-route']);
        } else {
            return $this->render('route-unforeseen-expenses-form', [
                'model' => $model,
                'modelDRoute' => $modelDRoute,
            ]);
        }
    }

    /*
    * Update unforeseen expenses for route
    * @param integer $id Unforeseen Expenses id
    * */
    public function actionUpdateRouteUnforeseenExpenses($id)
    {
        $model = TlDeliveryProposalDefaultUnforeseenExpenses::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['view', 'id' => $model->tl_delivery_proposal_default_route_id,'#'=>'title-route']);
        } else {
            return $this->render('route-unforeseen-expenses-form', [
                'modelDRoute' => TlDeliveryProposalDefaultSubRoute::findOne($model->tl_delivery_proposal_default_sub_route_id),
                'model' => $model,
            ]);
        }
    }

    /*
    * Delete unforeseen expenses for route
    * @param integer $id Unforeseen Expenses id
    * */
    public function actionDeleteRouteUnforeseenExpenses($id)
    {
        $model = TlDeliveryProposalDefaultUnforeseenExpenses::findOne($id);
        $route_id = $model->tl_delivery_proposal_default_route_id;
        $model->deleted = 1;
        $model->save(false);

        return $this->redirect(['view','id'=>$route_id,'#'=>'title-route']);
    }
}
