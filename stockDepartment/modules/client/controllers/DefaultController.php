<?php

namespace app\modules\client\controllers;

use common\modules\client\models\ClientEmployees;
use Yii;
use common\modules\client\models\Client;
use stockDepartment\modules\client\models\ClientSearch;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
//use dektrium\user\ModelManager;
use common\modules\user\models\User;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class DefaultController extends Controller
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
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Client model.
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
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {


//            $userModel = Yii::$app->getModule('user')->manager->createUser(['scenario' => 'create']);
//            $userModel = \Yii::$container->get(\dektrium\user\Finder::className())->createUser(['scenario' => 'create']);

            $userModel = \Yii::createObject([
                'class'    => User::className(),
                'scenario' => 'create',
            ]);

//            $user = new ModelManager();
//            $user->userClass = 'stockDepartment\modules\user\models\User';
//            $userModel = $user->createUser(['scenario' => 'create']);

            $userModel->username = $model->username;
            $userModel->email = $model->email;
            $userModel->user_type = User::USER_TYPE_CLIENT;
            $userModel->password = $model->password;

           if($userModel->create()) {
                $model->user_id = $userModel->id;
               // Clear password
                $model->password = '';
                $model->save(false);
           }

            // Create base client employee account
            $clientEmployee = new ClientEmployees();
            $clientEmployee->store_id = 0;
            $clientEmployee->client_id = $model->id;
            $clientEmployee->user_id = $model->user_id;
            $clientEmployee->username = $model->username;
            $clientEmployee->first_name = $model->first_name;
            $clientEmployee->middle_name = $model->middle_name;
            $clientEmployee->last_name = $model->last_name;
            $clientEmployee->phone = $model->phone;
            $clientEmployee->phone_mobile = $model->phone_mobile;
            $clientEmployee->email = $model->email;
            $clientEmployee->password = $model->password;
            $clientEmployee->status = $model::STATUS_ACTIVE;

            $clientEmployee->manager_type = ClientEmployees::TYPE_BASE_ACCOUNT;
            $clientEmployee->save();

            return $this->redirect(['view', 'id' => $model->id]);

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if( $userModel = \Yii::$container->get(\dektrium\user\Finder::className())->findUserById($model->user_id) ) {
                $userModel->scenario = 'update';
                $userModel->username = $model->username;
                $userModel->email = $model->email;
                $userModel->password = $model->password;
                $userModel->save();

                // Clear password
                $model->password = '';
                $model->save(false);
            }

            //S: Create base client employee account
            if($clientEmployee = ClientEmployees::findOne(['client_id'=>$model->id,'user_id'=>$model->user_id])) {

//            $clientEmployee->store_id = 0;
//            $clientEmployee->client_id = $model->id;
//            $clientEmployee->user_id = $model->user_id;
                $clientEmployee->username = $model->username;
                $clientEmployee->first_name = $model->first_name;
                $clientEmployee->middle_name = $model->middle_name;
                $clientEmployee->last_name = $model->last_name;
                $clientEmployee->phone = $model->phone;
                $clientEmployee->phone_mobile = $model->phone_mobile;
                $clientEmployee->email = $model->email;
                $clientEmployee->password = $model->password;
//            $clientEmployee->status = $model::STATUS_ACTIVE;
//            $clientEmployee->manager_type = ClientEmployees::TYPE_BASE_ACCOUNT;
                $clientEmployee->save();

            } else {

                $clientEmployee = new ClientEmployees();

                $clientEmployee->store_id = 0;
                $clientEmployee->client_id = $model->id;
                $clientEmployee->user_id = $model->user_id;
                $clientEmployee->username = $model->username;
                $clientEmployee->first_name = $model->first_name;
                $clientEmployee->middle_name = $model->middle_name;
                $clientEmployee->last_name = $model->last_name;
                $clientEmployee->phone = $model->phone;
                $clientEmployee->phone_mobile = $model->phone_mobile;
                $clientEmployee->email = $model->email;
                $clientEmployee->password = $model->password;
                $clientEmployee->status = $model::STATUS_ACTIVE;

                $clientEmployee->manager_type = ClientEmployees::TYPE_BASE_ACCOUNT;
                $clientEmployee->save();

            }
            //E: Create base client employee account

//            $userModel = User::findOne(['id' => $model->user_id]);
//            $userModel->scenario = 'update';
//            $userModel->load(Yii::$app->request->post());
//            $userModel->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Client model.
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
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
