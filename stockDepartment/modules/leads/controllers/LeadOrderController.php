<?php

namespace app\modules\leads\controllers;

use common\modules\client\components\ClientManager;
use Yii;
use common\modules\leads\models\TransportationOrderLead;
use app\modules\leads\models\TransportationOrderLeadSearch;
use stockDepartment\components\Controller;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\components\MailManager;
use common\modules\leads\models\ExternalClientLead;

/**
 * LeadOrderController implements the CRUD actions for TransportationOrderLead model.
 */
class LeadOrderController extends Controller
{
    /**
     * Lists all TransportationOrderLead models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransportationOrderLeadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TransportationOrderLead model.
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
     * Updates an existing TransportationOrderLead model.
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
     * Finds the TransportationOrderLead model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TransportationOrderLead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TransportationOrderLead::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Change status of selected model to confirmed
     */
    public function actionConfirmLeadOrder($id)
    {

        if ($model = $this->findModel($id)) {
                $model->createProposalFromLeadOrder();
                $this->redirect(['view', 'id' => $model->id]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
