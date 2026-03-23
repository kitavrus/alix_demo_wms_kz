<?php

namespace app\modules\tms\controllers;

use common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute;
use Yii;
use common\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRoute;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;

/**
 * DefaultSubRouteController implements the CRUD actions for TlDeliveryProposalDefaultSubRoute model.
 */
class DefaultSubRouteController extends Controller
{
//    /**
//     * Lists all TlDeliveryProposalDefaultSubRoute models.
//     * @return mixed
//     */
//    public function actionIndex()
//    {
//        $searchModel = new TlDeliveryProposalDefaultSubRouteSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }
//
//    /**
//     * Displays a single TlDeliveryProposalDefaultSubRoute model.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }

    /**
     * Creates a new TlDeliveryProposalDefaultSubRoute model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlDeliveryProposalDefaultSubRoute();
        $dRouteID = Yii::$app->request->get('default_route_id');
        $dRoute = TlDeliveryProposalDefaultRoute::findOne($dRouteID);
        $subRoutes = $dRoute->subRoutes;

        if(count($subRoutes) == 0){
            $model->from_point_id = $dRoute->from_point_id;
        } else {
            if($lastRoute = array_pop($subRoutes)){
                $model->from_point_id = $lastRoute->to_point_id;
            }
        }

        $model->transport_type = TlDeliveryProposalDefaultSubRoute::TRANSPORT_TYPE_AUTO;
        $model->tl_delivery_proposal_default_route_id = Yii::$app->request->get('default_route_id');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if(empty($model->agent_id)) {
                $model->car_id = '';
            }
            $model->save(false);
            return $this->redirect(['/tms/default-route/view', 'id' => $dRouteID]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlDeliveryProposalDefaultSubRoute model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if(empty($model->agent_id)) {
                $model->car_id = '';
            }
            $model->save(false);
            return $this->redirect(['/tms/default-route/view', 'id' => $model->tl_delivery_proposal_default_route_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the TlDeliveryProposalDefaultSubRoute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposalDefaultSubRoute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposalDefaultSubRoute::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Soft delete.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        if($model){
            $model->deleted = 1;
            $model->save(false);
        }
        return $this->redirect('/tms/default-route/view?id='.$model->tl_delivery_proposal_default_route_id);
    }
}
