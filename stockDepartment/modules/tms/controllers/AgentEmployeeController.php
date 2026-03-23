<?php

namespace app\modules\tms\controllers;

use Yii;
use common\modules\transportLogistics\models\TlAgentEmployees;
use stockDepartment\modules\tms\models\TlAgentEmployeesSearch;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
//use yii\filters\VerbFilter;
use common\modules\user\models\User;

/**
 * AgentEmployeeController implements the CRUD actions for TlAgentEmployees model.
 */
class AgentEmployeeController extends Controller
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
     * Lists all TlAgentEmployees models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlAgentEmployeesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TlAgentEmployees model.
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
     * Creates a new TlAgentEmployees model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlAgentEmployees();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

//            if($userModel = \Yii::$container->get(\dektrium\user\Finder::className())->createUser(['scenario' => 'create'])) {
            $userModel = \Yii::createObject([
                'class'    => User::className(),
                'scenario' => 'create',
            ]);
                $userModel->username = $model->username;
                $userModel->email = $model->email;
                $userModel->user_type = User::USER_TYPE_AGENT_EMPLOYEE;
                $userModel->password = $model->password;

                if ($userModel->create()) {
                    $model->user_id = $userModel->id;
                    // Clear password
                    $model->password = '';
                    $model->save(false);
                }

                return $this->redirect(['/tms/agent/view', 'id' => $model->tl_agent_id]);
//            }
        } else {

            $model->tl_agent_id = Yii::$app->request->getQueryParam('tl_agent_id');

            return $this->render('create', [
                'model' => $model,
            ]);

        }
    }

    /**
     * Updates an existing TlAgentEmployees model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if($userModel = \Yii::$container->get(\dektrium\user\Finder::className())->findUserById($model->user_id) ) {
                $userModel->scenario = 'update';
                $userModel->username = $model->username;
                $userModel->email = $model->email;
                $userModel->password = $model->password;
                $userModel->save();
            } else {

                $userModel = \Yii::createObject([
                    'class'    => User::className(),
                    'scenario' => 'create',
                ]);

//                $userModel = \Yii::$container->get(\dektrium\user\Finder::className())->createUser(['scenario' => 'create']);
                $userModel->username = $model->username;
                $userModel->email = $model->email;
                $userModel->user_type = User::USER_TYPE_CLIENT;
                $userModel->password = $model->password;

                if ($userModel->create()) {
                    $model->user_id = $userModel->id;
                    // Clear password
                    $model->password = '';
                    $model->save(false);
                }
            }

            // Clear password
            $model->password = '';
            $model->save(false);

            return $this->redirect(['/tms/agent/view', 'id' => $model->tl_agent_id]);
        } else {

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TlAgentEmployees model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted = 1;
        $model->save(false);

        if($u = User::findOne($model->user_id)) {
            $u->block();
        }

        return $this->redirect(['/tms/agent/view', 'id' => $model->tl_agent_id]);
    }

    /**
     * Finds the TlAgentEmployees model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlAgentEmployees the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlAgentEmployees::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
