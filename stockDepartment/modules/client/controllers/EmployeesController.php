<?php

namespace app\modules\client\controllers;

use common\modules\transportLogistics\components\TLHelper;
use Yii;
use common\modules\client\models\ClientEmployees;
use stockDepartment\modules\client\models\ClientEmployeesSearch;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use common\modules\user\models\User;
//use yii\filters\VerbFilter;

/**
 * ClientEmployeesController implements the CRUD actions for ClientEmployees model.
 */
class EmployeesController extends Controller
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
     * Lists all ClientEmployees models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientEmployeesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClientEmployees model.
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
     * Creates a new ClientEmployees model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClientEmployees();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {


//            if($userModel = Yii::$app->getModule('user')->manager->createUser(['scenario' => 'create'])) {
//            if($userModel = \Yii::$container->get(\dektrium\user\Finder::className())->createUser(['scenario' => 'create'])) {

            $userModel = \Yii::createObject([
                'class'    => User::className(),
                'scenario' => 'create',
            ]);

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
//            }

            return $this->redirect(['/client/default/view', 'id' => $model->client_id]);
        } else {


            $model->client_id = Yii::$app->request->getQueryParam('client_id');

            $storeList = TLHelper::getStockPointArray($model->client_id, true);

            return $this->render('create', [
                'model' => $model,
                'storeList' => $storeList,
            ]);
        }
    }

    /**
     * Updates an existing ClientEmployees model.
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
//                $userModel = \Yii::$container->get(\dektrium\user\Finder::className())->createUser(['scenario' => 'create']);
                $userModel = \Yii::createObject([
                    'class'    => User::className(),
                    'scenario' => 'create',
                ]);
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

            return $this->redirect(['/client/default/view', 'id' => $model->client_id]);
        } else {

            $storeList = TLHelper::getStockPointArray($model->client_id, true);

            return $this->render('update', [
                'model' => $model,
                'storeList' => $storeList,
            ]);
        }
    }

    /**
     * Deletes an existing ClientManagers model.
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

        return $this->redirect(['/client/default/view', 'id' => $model->client_id]);
    }

    /**
     * Finds the ClientManagers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClientEmployees the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClientEmployees::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
