<?php

namespace app\modules\tms\controllers;

use Yii;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpensesType;
use stockDepartment\modules\tms\models\TlDeliveryProposalRouteUnforeseenExpensesTypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * RouteUnforeseenExpensesTypeController implements the CRUD actions for TlDeliveryProposalRouteUnforeseenExpensesType model.
 */
class RouteUnforeseenExpensesTypeController extends Controller
{
//    public function behaviors()
//{
//    return [
//        'verbs' => [
//            'class' => VerbFilter::className(),
//            'actions' => [
//                'delete' => ['post'],
//            ],
//        ],
//    ];
//}

    /**
     * Lists all TlDeliveryProposalRouteUnforeseenExpensesType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlDeliveryProposalRouteUnforeseenExpensesTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TlDeliveryProposalRouteUnforeseenExpensesType model.
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
     * Creates a new TlDeliveryProposalRouteUnforeseenExpensesType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlDeliveryProposalRouteUnforeseenExpensesType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlDeliveryProposalRouteUnforeseenExpensesType model.
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
     * Deletes an existing TlDeliveryProposalRouteUnforeseenExpensesType model.
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
     * Finds the TlDeliveryProposalRouteUnforeseenExpensesType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposalRouteUnforeseenExpensesType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposalRouteUnforeseenExpensesType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
