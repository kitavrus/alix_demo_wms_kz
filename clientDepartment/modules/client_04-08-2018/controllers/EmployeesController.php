<?php

namespace app\modules\client\controllers;

use Yii;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use common\modules\client\models\ClientEmployees;
use clientDepartment\modules\client\models\ClientEmployeesSearch;
use clientDepartment\components\Controller;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\user\models\User;
use clientDepartment\modules\client\components\ClientManager;

//use yii\filters\VerbFilter;

/**
 * ClientManagersController implements the CRUD actions for ClientManagers model.
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
     * Lists all ClientManagers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientEmployeesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if(!ClientManager::canIndexEmployee() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }


        return $this->render($this->getViewByType('index'), [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClientManagers model.
     * @param integer $id
     * @return mixed
     */
//    public function actionView($id)
    public function actionView($id)
    {
        $model = ClientEmployees::findOne($id);

        if(!ClientManager::canViewEmployee($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        return $this->render($this->getViewByType('view'), [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new ClientManagers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClientEmployees();
        $store_id = Yii::$app->request->get('store_id');
//        $rt = Yii::$app->request->get('rt');


        $model->setScenario('create');

        if ($model->load(Yii::$app->request->post()) ) {

            $model->client_id = ClientManager::getClientEmployeeByAuthUser()->client_id;

            if ( ($clientEmpl = ClientManager::getClientEmployeeByAuthUser()) && empty($store_id)) {
                $model->store_id = $clientEmpl->store_id;
            }

            if ($model->save()) {

//                $userModel = Yii::$app->getModule('user')->manager->createUser(['scenario' => 'create']);
                //if(!($userModel = User::find()->andWhere(['username'=>$model->username,'email'=>$model->email])->one())) {
                    $userModel = \Yii::createObject([
                        'class' => User::className(),
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
                //}

                $rt = ['/client/employees/view', 'id' => $model->id];

                switch(Yii::$app->request->post('rt')) {
                    case 'c':
                        $rt = ['/client/default/view'];
                        break;
                    case 'store':
                        $rt = ['/store/default/view', 'id' => $model->store_id];
                        break;
                    default:
                        $rt = ['/client/employees/view', 'id' => $model->id];
                        break;
                }

                $this->redirect($rt);

            } else {
            }
        } else {
        }

        if ($clientId = ClientManager::getClientEmployeeByAuthUser()) {
            $clientId = $clientId->client_id;
        }

        $model->store_id = $store_id;

        return $this->render('create', [
            'model' => $model,
            'storeList' => TLHelper::getStoreArrayByClientID($clientId, true),
//            'rt' => $rt,
        ]);
    }

    /**
     * Updates an existing ClientManagers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(!ClientManager::canUpdateEmployee($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

//        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if($userModel = User::find()->andWhere(['username'=>$model->username,'email'=>$model->email])->one()) {
                $model->user_id = $userModel->id;
                $userModel->blocked_at = '';
                $userModel->save(false);
            }

            if ($userModel = \Yii::$container->get(\dektrium\user\Finder::className())->findUserById($model->user_id)) {
                $userModel->scenario = 'update';
                $userModel->username = $model->username;
                $userModel->email = $model->email;
                $userModel->password = $model->password;
                $userModel->save(false);
            } else {

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

            $rt = ['/client/employees/view', 'id' => $model->id];

            switch(Yii::$app->request->post('rt')) {
                case 'c':
                    $rt = ['/client/default/view'];
                    break;
                case 'store':
                    $rt = ['/store/default/view', 'id' => $model->store_id];
                    break;
                default:
                    $rt = ['/client/employees/view', 'id' => $model->id];
                    break;
            }

            $this->redirect($rt);

//            if (Yii::$app->request->post('rt') == 'c') {
//                return $this->redirect(['/client/default/view']);
//            } elseif () {
//                return $this->redirect(['/store/default/view', 'id' => $model->store_id]);
//
//                } else {
//                return $this->redirect(['/client/employees/view', 'id' => $model->id]);
//            }

        } else {

            return $this->render($this->getViewByType('update'), [
                'model' => $model,
                'storeList' => TLHelper::getStoreArrayByClientID($model->client_id,true),
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


        if(!ClientManager::canDeleteEmployee($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $model->deleted = 1;
        $model->save(false);

        if($u = User::findOne($model->user_id)) {
            $u->block();
        }

        $rt = ['/client/employees/index'];

        switch(Yii::$app->request->get('rt')) {
            case 'c':
                $rt = ['/client/default/view'];
                break;
            case 'store':
                $rt = ['/store/default/view', 'id' => $model->store_id];
                break;
            default:
                $rt = ['/client/employees/index'];
                break;
        }

        $this->redirect($rt);
    }

    /**
     * Finds the ClientManagers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $ceId
     * @return ClientManagers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ceId)
    {
        if (($model = ClientEmployees::findOne($ceId)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
    * Get view type
    * @param string $view Action name
    * @return string view name
    * */
    protected function getViewByType($view)
    {
        if(!Yii::$app->user->isGuest) {

//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if ($client = ClientManager::getClientEmployeeByAuthUser()) {
//                    VarDumper::dump($client,10,true);
                    switch ($client->manager_type) {
                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $view .= '';
                            break;
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
//                        case ClientEmployees::TYPE_REGIONAL_OBSERVER:
                        case ClientEmployees::TYPE_REGIONAL_OBSERVER_RUSSIA:
                        case ClientEmployees::TYPE_REGIONAL_OBSERVER_BELARUS:
                            $view = 'observer/'.$view;
                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $view = 'manager/'.$view;
                              $view .= '';
                            break;

                        default:
//                            $view .= 'default/'.$view;
                            $view = 'empty-default';
                            break;
                    }
//                }
            } else {
                $view = 'empty-default';
            }
        } else {
            $view = 'empty-default';
        }
        return $view;
    }

}
