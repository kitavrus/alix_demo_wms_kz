<?php

namespace app\modules\client\controllers;

use Yii;
use common\modules\client\models\Client;
use clientDepartment\modules\client\models\ClientSearch;
use clientDepartment\components\Controller;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use common\modules\user\models\User;
use clientDepartment\modules\client\components\ClientManager;
use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\components\TLHelper;


/**
 * ClientController implements the CRUD actions for Client model.
 */
class DefaultController extends Controller
{
    //client id
//    private $_clientId;

    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
        $model = $this->findModel();

        return $this->render($this->getViewByType('view'), [
            'model' => $model,
        ]);
    }

//    /**
//     * Displays a single Client model.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionProfile()
//    {
//        $user_id = Yii::$app->user->id;
//        if ($model = Client::findOne(['user_id'=>$user_id])) {
//            return $this->redirect(['view']);
//        } else {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//    }


    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $model = $this->findModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

//            $userModel = Yii::$app->getModule('user')->manager->findUserById($model->user_id);
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

//            $userModel = User::findOne(['id' => $model->user_id]);
//            $userModel->scenario = 'update';
//            $userModel->load(Yii::$app->request->post());
//            $userModel->save(false);

            return $this->redirect(['view']);
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
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel()
    {
        if (($model = Client::findOne(ClientManager::getClientEmployeeByAuthUser()->client_id)) !== null) {
//        if (($model = Client::findOne($this->clientId)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Finds the Client model based on logged user ID
     * @throws NotFoundHttpException if the model cannot be found
     */
//    protected function getClientId()
//    {
//        $user_id = Yii::$app->user->id;
//        if ($model = ClientManager::findModelUserInfo()) {
//        if ($model = Client::findOne(['user_id'=>$user_id])) {
//            $this->_clientId = $model->id;
//            return $this->_clientId;
//
//        } else {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//    }

    /*
     * Redirect employee by user type
     *
     * */
    public function actionProfile()
    {
        $route = '/';
        if (!Yii::$app->user->isGuest) {
//            if ($user = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {
                if ($client = ClientManager::getClientByUserID()) {
//                    VarDumper::dump($client,10,true);
                    switch ($client->manager_type) {
                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $route = ['/client/default/view'];
                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
//                        case ClientEmployees::TYPE_REGIONAL_OBSERVER:
                        case ClientEmployees::TYPE_REGIONAL_OBSERVER_RUSSIA:
                        case ClientEmployees::TYPE_REGIONAL_OBSERVER_BELARUS:
                            $ce = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);
                            $route = ['/client/employees/view', 'id' => $ce->id];
                            break;

//                    }
//                switch ($user->user_type) {
//                    case User::USER_TYPE_CLIENT:
//                        $route = ['/client/default/view'];
//                        break;
//                    case User::USER_TYPE_STORE_MANAGER:
//                        $ce = ClientManagers::findOne(['user_id'=>Yii::$app->user->id]);


//                        VarDumper::dump(Yii::$app->user->id,10,true);
//                        VarDumper::dump($ce,10,true);
//                        die;
//                        $route = ['/client/employees/view','id'=>$ce->id];
//                        break;
                        default:
                            break;
                    }
                }
//            }
        }

        $this->redirect($route);
    }

    /*
     * Create employee
     * */
//    public function actionCreateEmployee()
//    {
//        $model = new ClientEmployees();
//        $ce = ClientManager::getClientEmployeeByAuthUser();
//
//        if ( $model->load(Yii::$app->request->post()) ) {
//
//            $model->client_id = $ce->client_id;
//
//            if ($model->save()) {
//
//                $userModel = Yii::$app->getModule('user')->manager->createUser(['scenario' => 'create']);
//
//                $userModel->username = $model->name;
//                $userModel->email = $model->email;
//                $userModel->user_type = User::USER_TYPE_CLIENT;
//                $userModel->password = $model->password;
//
//                if ( $userModel->create() ) {
//                    $model->user_id = $userModel->id;
//                    // Clear password
//                    $model->password = '';
//                    $model->save(false);
//                }
//
//                return $this->redirect(['/client/default/view', 'id' => $model->client_id]);
//            }
//        }
//
//        $storeList = TLHelper::getStoreArrayByClientID($model->client_id, true);
//
//        return $this->render('../employees/create', [
//            'model' => $model,
//            'storeList' => $storeList,
//        ]);
//    }

    /*
     * Update employee
     * */
//    public function actionUpdateEmployee($id)
//    {
//        $model = $this->findEmployee($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//
//            if ($userModel = Yii::$app->getModule('user')->manager->findUserById($model->user_id)) {
//                $userModel->scenario = 'update';
//                $userModel->username = $model->name;
//                $userModel->email = $model->email;
//                $userModel->password = $model->password;
//                $userModel->save();
//            }
//
//            // Clear password
//            $model->password = '';
//            $model->save(false);
//
//            return $this->redirect(['/client/default/view', 'id' => $model->client_id]);
//
//        } else {
//
//            $storeList = TLHelper::getStoreArrayByClientID($model->client_id, true);
//
//            return $this->render('../employees/update', [
//                'model' => $model,
//                'storeList' => $storeList,
//            ]);
//        }
//    }

    /*
    * Delete employee
    * Finds the ClientManagers model based on its primary key value.
    *
    * */
//    public function actionDeleteEmployee($id)
//    {
//        if ($m = $this->findEmployee($id)) {
//
//            $m->deleted = 1;
//            $m->save(false);
//
//            if ($u = User::findOne($m->user_id)) {
//                $u->block();
//            }
//        }
//
//        return $this->redirect(['/client/default/view', 'id' => $m->client_id]);
//
//    }

    /**
     * Finds the ClientManagers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $employeeId
     * @param integer $clientId
     * @return ClientManagers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
//    protected static function findEmployee($employeeId, $clientId = null)
//    {
//        $client = ClientManager::findModelClient(Yii::$app->user->id);
//
//        if (($model = ClientManagers::findOne(['id' => $employeeId, 'client_id' => $client->id])) !== null) {
//            return $model;
//        } else {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//    }

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
