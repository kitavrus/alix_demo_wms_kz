<?php

namespace app\modules\agentBilling\controllers;

use Yii;
use common\modules\agentBilling\models\TlAgentBillingConditions;
use app\modules\agentBilling\models\TlAgentBillingConditionsSearch;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\modules\agentBilling\models\TlAgentBilling;

/**
 * BillingConditionsController implements the CRUD actions for TlAgentBillingConditions model.
 */
class ConditionController extends Controller
{
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TlAgentBillingConditions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlAgentBillingConditions();
        $billing = TlAgentBilling::findOne(Yii::$app->request->get('tl_agents_billing_id'));

        $model->status = TlAgentBillingConditions::STATUS_ACTIVE;
        $model->transport_type = TlAgentBillingConditions::TRANSPORT_TYPE_AUTO;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                $model->agent_id = $billing->agent_id;
                $model->tl_agents_billing_id = $billing->id;


            if($model->save()) {
                return $this->redirect(['/agentBilling/default/view', 'id' => $model->tl_agents_billing_id]);
            }
        }
            return $this->render('create', [
                'model' => $model,
                'billing' => $billing,
            ]);

    }

    /**
     * Updates an existing TlAgentBillingConditions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $billing = TlAgentBilling::findOne($model->tl_agents_billing_id);

        if ($model->load(Yii::$app->request->post())) {

            if($model->save()) {
                return $this->redirect(['/agentBilling/default/view', 'id' => $model->tl_agents_billing_id]);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
                'billing' => $billing,
            ]);
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
        return $this->redirect('/agentBilling/default/view?id='.$model->tl_agents_billing_id);
    }

    /**
     * Finds the TlAgentBillingConditions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlAgentBillingConditions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlAgentBillingConditions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
