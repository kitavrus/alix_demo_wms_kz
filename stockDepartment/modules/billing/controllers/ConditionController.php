<?php

namespace app\modules\billing\controllers;

use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use common\modules\billing\models\TlDeliveryProposalBillingConditions;
use stockDepartment\modules\billing\models\TlDeliveryProposalBillingConditionsSearch;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ConditionController implements the CRUD actions for TlDeliveryProposalBillingConditions model.
 */
class ConditionController extends Controller
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
     * Lists all TlDeliveryProposalBillingConditions models.
     * @return mixed
     */
//    public function actionIndex()
//    {
//        $searchModel = new TlDeliveryProposalBillingConditionsSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    /**
     * Displays a single TlDeliveryProposalBillingConditions model.
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
     * Creates a new TlDeliveryProposalBillingConditions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlDeliveryProposalBillingConditions();

        if ($model->load(Yii::$app->request->post())) {

            if($billing = TlDeliveryProposalBilling::findOne(Yii::$app->request->post('tl_delivery_proposal_billing_id'))) {
                $model->client_id = $billing->client_id;
                $model->tl_delivery_proposal_billing_id = $billing->id;
            }

            if($model->save()) {
                return $this->redirect(['/billing/default/view', 'id' => $model->tl_delivery_proposal_billing_id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlDeliveryProposalBillingConditions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if($model->save()) {
                return $this->redirect(['/billing/default/view', 'id' => $model->tl_delivery_proposal_billing_id]);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TlDeliveryProposalBillingConditions model.
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
     * Finds the TlDeliveryProposalBillingConditions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposalBillingConditions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposalBillingConditions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
