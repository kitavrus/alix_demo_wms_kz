<?php

namespace app\modules\employee\controllers;

use Yii;
//use yii\web\Controller;
use yii\web\NotFoundHttpException;
use stockDepartment\components\Controller;
use common\modules\employees\models\Employees;
use common\modules\user\models\User;
use stockDepartment\modules\employee\models\EmployeeSearch;

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
     * Lists all Employees models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employees model.
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
     * Creates a new Employees model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Employees();
        //$model->setScenario('create');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            if ($model->save()) {

                /*
                    $userModel = \Yii::createObject([
                        'class'    => User::className(),
                        'scenario' => 'create',
                    ]);
                    //после сохранения записи Employees добавляем запись в таблицу User
                    $userModel->username = $model->username;
                    $userModel->email = $model->email;
                    $userModel->user_type = User::USER_TYPE_STOCK_WORKER;
                    $userModel->password = $model->password;

                    if ($userModel->create()) {
                        $model->user_id = $userModel->id;
                        // Clear password
                        $model->password = '';
                        $model->save(false);
                    }

                */
//            }

            return $this->redirect(['view', 'id' => $model->id]);

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Employees model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            if ($userModel = \Yii::$container->get(\dektrium\user\Finder::className())->findUserById($model->user_id)) {
//                $userModel->scenario = 'update';
//                $userModel->username = $model->username;
//                $userModel->email = $model->email;
//                $userModel->password = $model->password;
//                $userModel->save();
//            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Employees model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($model = $this->findModel($id)) {
            $model->deleted = 1;
            if ($model->save(false)) {
                if ($u = User::findOne($model->user_id)) {
                    $u->block();
                }
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Employees model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employees the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employees::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
