<?php

namespace app\modules\client\controllers;

use Yii;
use common\modules\client\models\ClientSettings;
use common\modules\client\models\ClientSettingsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\client\components\ClientSettingsManager;
use yii\filters\VerbFilter;
use common\models\ActiveRecord;
use yii\helpers\BaseHtml;

/**
 * ClientSettingsController implements the CRUD actions for ClientSettings model.
 */
class ClientSettingsController extends Controller
{

    /**
     * Lists all ClientSettings models for specified client.
     * @return mixed
     */
    public function actionIndex($client_id)
    {
        $searchModel = new ClientSettingsSearch();
        $dataProvider = $searchModel->search($client_id);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'client' => $client_id,
        ]);


    }

    /**
     * Displays a single ClientSettings model.
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
     * Creates a new ClientSettings model for specified client.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($client_id)
    {
        $model = new ClientSettings();
        if ($model->load(Yii::$app->request->post())) {
            $model->client_id = $client_id;
            if ($model->save()) {
                $this->redirect(['index', 'client_id' => $client_id]);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing ClientSettings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'client_id'=> $model->client_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ClientSettings model.
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
     * Get default values for specified user via AJAX
     * @return mixed
     */
    public function actionGetDefaultValue()
    {
        $client_id = Yii::$app->request->post('client_id');

        $sManager = new ClientSettingsManager($client_id);
        $result = [];
        $dataParams = $sManager->getParams();
        Yii::$app->response->format = 'json';
        if (!empty($dataParams)) {
            foreach ($dataParams as $param) {
                if ($param->option_type == ClientSettingsManager::OPTION_TYPE_FUNCTION) {
                    //$defaultData = call_user_func('app\modules\client\components\ClientSettingsManager::' . $param->option_value);
                    $result[$param->option_name] = $param->default_value;
                }
            }
            return [
                'message' => 'Success',
                'data_options' => $result,
            ];
        }
        return [
            'message' => 'Error',
            'data_options' => $result,
        ];

    }

    /**
     * Get options for specified options name
     * @return mixed
     */
    public function actionGetOptionsByValue()
    {
        $function_name = Yii::$app->request->post('option_name');
        $result = '';
        if(!empty($function_name)) {
            $result = call_user_func('app\modules\client\components\ClientSettingsManager::' . $function_name);
            Yii::$app->response->format = 'json';

        }
        return [
            'message' => 'Success',
            'data_options' => $result,
        ];

    }

    /**
     * Finds the ClientSettings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClientSettings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClientSettings::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
