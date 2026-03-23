<?php

namespace app\modules\agentBilling\controllers;

use common\modules\transportLogistics\components\TLHelper;
use Yii;
use common\modules\agentBilling\models\TlAgentBilling;
use app\modules\agentBilling\models\TlAgentBillingSearch;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\modules\transportLogistics\models\TlAgents;

/**
 * DefaultController implements the CRUD actions for TlAgentBilling model.
 */
class DefaultController extends Controller
{
/*    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }*/

    /**
     * Lists all TlAgentBilling models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlAgentBillingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $agentsArray = TlAgents::getActiveAgentsArray();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'agentsArray' => $agentsArray,
        ]);
    }

    /**
     * Displays a single TlAgentBilling model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   $model = $this->findModel($id);
        $storeArray = TLHelper::getStockPointArray();
        return $this->render('view', [
            'model' => $model,
            'storeArray' => $storeArray,
        ]);
    }

    /**
     * Creates a new TlAgentBilling model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlAgentBilling();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlAgentBilling model.
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
     * Deletes an existing TlAgentBilling model.
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
     * Finds the TlAgentBilling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlAgentBilling the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlAgentBilling::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
