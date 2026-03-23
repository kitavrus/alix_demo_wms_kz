<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 20.10.14
 * Time: 11:32
 */

namespace stockDepartment\modules\client\components;

use DateTime;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use common\modules\user\models\User;
use common\modules\client\models\Client;
use common\modules\client\models\ClientEmployees;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;


class EmployeeManager {


    /*
     * Get user info by type
     * @param integer $id Client id or Client employee
     * @param string $type User type Client or Manager
     * */
    public static function findModelUserInfo($id=null,$type)
    {
        $r = null;
        if(!Yii::$app->user->isGuest) {
            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {


                if ($client = EmployeeManager::getClientByUserID()) {
//                    VarDumper::dump($client,10,true);
                    switch ($client->manager_type) {
                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $client_id = $client->client_id;
                            break;
                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $route_to = $client->store_id;
                            $client_id = $client->client_id;
                            break;
                        default:
                            break;
                    }
                }

                switch($userModel->user_type) {
                    case User::USER_TYPE_CLIENT:

                        if ( $type == 'client') {
                            $r = static::findModelClient($userModel->id);
                        }

                        if($type == 'employee') {
                            $client = static::findModelClient($userModel->id);
                            $r = static::findModelClientEmployee($id,$client->id);
                        }

                        return $r;

                        break;
                    case User::USER_TYPE_STORE_MANAGER:

                        if ( $type == 'employee') {

                            if ( ($r = ClientEmployees::findOne(['user_id'=>$userModel->id])) !== null ) {
                                return $r;
                            } else {
                                throw new NotFoundHttpException('The requested page does not exist.');
                            }

                        }

                        break;
                    default:
                        break;
                }


            }
        } else {

        }

        return $r;
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $userId
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public  static function findModelClient($userId)
    {
        if (($model = Client::findOne(['user_id'=>$userId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the ClientManagers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $userId
     * @param integer $clientId
     * @return ClientManagers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public  static function findModelClientEmployee($userId,$clientId=null)
    {
        $q = ClientEmployees::find();

//        echo "<br />";
//        VarDumper::dump($userId,10,true);
//        echo "<br />";
//        VarDumper::dump($clientId,10,true);
//        echo "<br />";
//        VarDumper::dump($m = $q->where(['id'=>$userId])->andFilterWhere(['client_id'=>$clientId])->one(),10,true);
        $model = $q->where(['id'=>$userId])->andFilterWhere(['client_id'=>$clientId])->one();
//die;
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * Get client info by user
     * @param integer $userID Default null. Get user auth user data
     * */
    public static function getClientByUserID($userID = null)
    {
        $r = false;
        if(!Yii::$app->user->isGuest) {

            if(empty($userID)) {
                $userID = Yii::$app->user->id;
            }
//            echo "-----<br />";
//            echo "-----<br />";
//            echo "-----<br />";
//            echo "-----<br />";
//            echo "-----<br />";
//            VarDumper::dump($userID,10,true);
//            echo "-----<br />";
//            echo "-----<br />";
//            echo "-----<br />";
//            echo "-----<br />";
//            echo "-----<br />";
//            VarDumper::dump(Yii::$app->user->id,10,true);
//            die(Yii::$app->user->id);

            if($userModel = Yii::$app->getModule('user')->manager->findUserById($userID)) {
//                VarDumper::dump($userModel,10,true);
//                VarDumper::dump($userModel->user_type,10,true);
                switch($userModel->user_type) {
                    case ClientEmployees::TYPE_MANAGER:
                    case ClientEmployees::TYPE_DIRECTOR:
                    case ClientEmployees::TYPE_MANAGER_INTERN:
                    case ClientEmployees::TYPE_DIRECTOR_INTERN:
                    case ClientEmployees::TYPE_LOGIST:
                    case ClientEmployees::TYPE_BASE_ACCOUNT:
                    case ClientEmployees::TYPE_OBSERVER:
                    case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                        $r = ClientEmployees::findOne(['user_id'=>$userModel->id]);
                        break;

                    default:
                        break;
                }
            }
        }

//        VarDumper::dump($r,10,true);

        return $r;
    }

    /*
    * Get auth client employee
    * @return client employee
    * */
    public static function getClientEmployeeByAuthUser()
    {
        return ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
    }


//START
///////////////////////////////////////////////////////CAN/////////////////////////////////
///////////////////////////////////////////////////////CAN/////////////////////////////////
///////////////////////////////////////////////////////CAN/////////////////////////////////
///////////////////////////////////////////////////////CAN/////////////////////////////////

    /*
    * Can index
    * @param $model ActiveRecord
    * @return boolean If true can
    *
    * */
    public static function canIndexEmployee($model = null)
    {
//        if(empty($model)) {
//            return null;
//        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
                            $r = true;
//                            }

                            break;

//                        case ClientEmployees::TYPE_DIRECTOR:
//                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
//                                $r = true;
//                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    /*
     * Can update employees
     * @param $model  Employees ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canUpdateEmployee($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_OBSERVER;
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF;

                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }

                            break;

//                        case ClientEmployees::TYPE_DIRECTOR:
//                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }


                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    /*
      * Can view employees
      * @param $model  Employees ActiveRecord
      * @return boolean If true can
      *
      * */
    public static function canViewEmployee($model)
    {
        if(empty($model)) {
            return null;
        }
//        VarDumper::dump($userModel->id,10,true);
//        die('-STOP-');

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {




                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }

                            break;

//                        case ClientEmployees::TYPE_DIRECTOR:
//                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }


                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    /*
    * Can delete employees
    * @param $model  Employees ActiveRecord
    * @return boolean If true can
    *
    * */
    public static function canDeleteEmployee($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }

                            break;

//                        case ClientEmployees::TYPE_DIRECTOR:
//                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }


                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    //====================================================================================

    /*
     * Can view delivery proposal
     * @param $model ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canViewDeliveryProposal($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }


                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

  /*
     * Can view delivery proposal
     * @param $model ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canUpdateDeliveryProposal($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $client_id = $client->client_id;

                            $updatedStatus = [
                                TlDeliveryProposal::STATUS_NEW,// = 1;  //новый
//                                TlDeliveryProposal::STATUS_ADD_CAR,// = 5;  //добавлена машина
//                                TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,//'' = 6;  //Добавьте маршрут к заявке
//                                TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,// = 7;  //Добавьте к маршруту машину
//                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                                TlDeliveryProposal::STATUS_NOT_ADDED_M3 ,//= 9;  //Не заполнен m3
//                                TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE,// = 10;
                            ];

                            if($model->client_id == $client_id && in_array($model->status,$updatedStatus)) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                        $updatedStatus = [
                            TlDeliveryProposal::STATUS_NEW,// = 1;  //новый
//                            TlDeliveryProposal::STATUS_ADD_CAR,// = 5;  //добавлена машина
//                            TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,//'' = 6;  //Добавьте маршрут к заявке
//                            TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,// = 7;  //Добавьте к маршруту машину
//                            TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                            TlDeliveryProposal::STATUS_NOT_ADDED_M3 ,//= 9;  //Не заполнен m3
//                            TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE,// = 10;
                        ];

                            if($model->client_id == $client_id
                                && $model->created_user_id == $userModel->id
                                && in_array($model->status,$updatedStatus)

                            ) {
                                $r = true;
                            }

                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    /*
     * Can print ttn for route delivery proposal
     * @param $model ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canPrintTTNDeliveryProposal($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    $storeTo = $model->routeTo;
                    $storeFrom = $model->routeFrom;

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
//                            $client_id = $client->client_id;
//
//                            $updatedStatus = [
//                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                                TlDeliveryProposal::STATUS_ON_ROUTE,// = 2;  //В пути
//                            ];
//
//                            if ($model->client_id == $client_id
//                                && in_array($model->status,$updatedStatus)
//                            ) {
//                                $r = true;
//                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            $updatedStatus = [
                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                                TlDeliveryProposal::STATUS_ON_ROUTE,// = 2;  //В пути
                            ];

                            if($model->client_id == $client_id
                                && ($model->created_user_id == $userModel->id)
                                && in_array($model->status,$updatedStatus)

                            ) {
                                $r = true;

                            } elseif($model->client_id == $client_id
                                && (in_array($storeFrom->id,[$client->store_id]))
//                                && (in_array($storeFrom->id,[$client->store_id]) || in_array($storeTo->id,[$client->store_id]))
                                && in_array($model->status,$updatedStatus)
                            ) {
                                $r = true;
                            }

                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    /*
     * Can print box label for route delivery proposal
     * @param $model ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canPrintBoxLabelDeliveryProposal($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

//                    $storeTo = $model->routeTo;
                    $storeFrom = $model->routeFrom;

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
//                            $client_id = $client->client_id;
//
//                            $updatedStatus = [
//                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                                TlDeliveryProposal::STATUS_ON_ROUTE,// = 2;  //В пути
//                            ];
//
//                            if ($model->client_id == $client_id
//                                && in_array($model->status,$updatedStatus)
//                            ) {
//                                $r = true;
//                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            $updatedStatus = [
                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                                TlDeliveryProposal::STATUS_ON_ROUTE,// = 2;  //В пути
                            ];

//                            if($model->client_id == $client_id
//                                && ($model->created_user_id == $userModel->id)
//                                && in_array($model->status,$updatedStatus)
//
//                            ) {
//                                $r = true;
//
//                            } else

                            if($model->client_id == $client_id
                                && in_array($storeFrom->id,[$client->store_id])
                                && in_array($model->status,$updatedStatus)
                            ) {
                                $r = true;
                            }

                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    /*
     * Can delete delivery proposal
     * @param $model ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canDeleteDeliveryProposal($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;

        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $client_id = $client->client_id;

                            $updatedStatus = [
                                TlDeliveryProposal::STATUS_NEW,// = 1;  //новый
//                                /TlDeliveryProposal::STATUS_ADD_CAR,// = 5;  //добавлена машина
//                                TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,//'' = 6;  //Добавьте маршрут к заявке
//                                TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,// = 7;  //Добавьте к маршруту машину
//                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                                TlDeliveryProposal::STATUS_NOT_ADDED_M3 ,//= 9;  //Не заполнен m3
//                                TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE,// = 10;
                            ];

                            if($model->client_id == $client_id && in_array($model->status,$updatedStatus)) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                        $updatedStatus = [
                            TlDeliveryProposal::STATUS_NEW,// = 1;  //новый
//                            TlDeliveryProposal::STATUS_ADD_CAR,// = 5;  //добавлена машина
//                            TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,//'' = 6;  //Добавьте маршрут к заявке
//                            TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,// = 7;  //Добавьте к маршруту машину
//                            TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                            TlDeliveryProposal::STATUS_NOT_ADDED_M3 ,//= 9;  //Не заполнен m3
//                            TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE,// = 10;
                        ];


                            if($model->client_id == $client_id
                                && $model->created_user_id == $userModel->id
                                && in_array($model->status,$updatedStatus)
                            ) {
                                $r = true;
                            }

                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

//STORE==STORE=STORE=STORE==STORE=STORE=STORE==STORE=STORE=STORE==STORE=STORE=

    /*
    * Can index
    * @param $model ActiveRecord
    * @return boolean If true can
    *
    * */
    public static function canIndexStore($model = null)
    {
//        if(empty($model)) {
//            return null;
//        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
                            $r = true;
//                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
//                                $r = true;
//                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    /*
    * Can create
    * @param $model ActiveRecord
    * @return boolean If true can
    *
    * */
    public static function canCreateStore($model = null)
    {
//        if(empty($model)) {
//            return null;
//        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
                            $r = true;
//                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
//                                $r = true;
//                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    /*
     * Can view store
     * @param $model ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canViewStore($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;
//
                            if($model->client_id == $client_id && $model->id == $client->store_id) {
                                $r = true;
                            }


                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

  /*
     * Can view store
     * @param $model ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canUpdateStore($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $client_id = $client->client_id;

//                            $updatedStatus = [
//                                TlDeliveryProposal::STATUS_NEW,// = 1;  //новый
//                                TlDeliveryProposal::STATUS_ADD_CAR,// = 5;  //добавлена машина
//                                TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,//'' = 6;  //Добавьте маршрут к заявке
//                                TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,// = 7;  //Добавьте к маршруту машину
//                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                                TlDeliveryProposal::STATUS_NOT_ADDED_M3 ,//= 9;  //Не заполнен m3
//                                TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE,// = 10;
//                            ];

                            if($model->client_id == $client_id) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id && $model->id == $client->store_id) {
                                $r = true;
                            }

                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }


    /*
     * Can delete store
     * @param $model ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canDeleteStore($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;

        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $client_id = $client->client_id;

//                            $updatedStatus = [
//                                TlDeliveryProposal::STATUS_NEW,// = 1;  //новый
//                                TlDeliveryProposal::STATUS_ADD_CAR,// = 5;  //добавлена машина
//                                TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,//'' = 6;  //Добавьте маршрут к заявке
//                                TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,// = 7;  //Добавьте к маршруту машину
//                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
//                                TlDeliveryProposal::STATUS_NOT_ADDED_M3 ,//= 9;  //Не заполнен m3
//                                TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE,// = 10;
//                            ];

                            if($model->client_id == $client_id ) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $client_id = $client->client_id;
//
//                            if($model->client_id == $client_id && $model->created_user_id == $userModel->id) {
//                                $r = true;
//                            }

                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }


//STORE-REVIEW==STORE-REVIEW=STORE-REVIEW=STORE-REVIEW==STORE-REVIEW=STORE-REVIEW=STORE-REVIEW==STORE-REVIEW=STORE-REVIEW=STORE-REVIEW==STORE-REVIEW=STORE-REVIEW=

    /*
       * Can view store review
       * @param $model ActiveRecord
       * @return boolean If true can
       *
       * */
    public static function canUpdateStoreReview($model)
    {
        if(empty($model)) {
            return null;
        }

        if($model::className() == 'common\\modules\\store\\models\\StoreReviews') {
            if(($model = TlDeliveryProposal::findOne($model->tl_delivery_proposal_id)) == null) {
                return null;
            };
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {
                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $client_id = $client->client_id;



                            $updatedStatus = [
                                TlDeliveryProposal::STATUS_ON_ROUTE,// В пути
                                TlDeliveryProposal::STATUS_DELIVERED,// Доставлен
                            ];

                            if($model->client_id == $client_id && in_array($model->status,$updatedStatus)) {
                                $r = true;

                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            $updatedStatus = [
                                TlDeliveryProposal::STATUS_ON_ROUTE,// В пути
                                TlDeliveryProposal::STATUS_DELIVERED,// Доставлен
                            ];

                            if($model->client_id == $client_id
//                                && $model->created_user_id == $userModel->id
                                && in_array($model->status,$updatedStatus)

                            ) {
                                $r = true;
                            }

                            break;

                        default:
                            break;
                    }
                }
            }
        }

        if(($data = $model->getBLDataFieldValueByName('checkModifyStoreReview')) && $model->status == TlDeliveryProposal::STATUS_DELIVERED) {

//            VarDumper::dump($data,10,true);
            $dateEnd = new DateTime($data['value'],new \DateTimeZone('Asia/Almaty'));

//            $dateEnd->modify('+1 hours');
            $dateEnd->modify('+2 day');
//            echo $dateEnd->format('Y-m-d');


            $dateNow = new DateTime('now',new \DateTimeZone('Asia/Almaty'));

//            echo "<br />";
//            echo $dateEnd->format('U').' '.$dateEnd->format('Y-m-d H:i:s')." + 2 Day<br />";
//            $dateNow->modify('-2 day');
//            echo $dateNow->format('U').' '.$dateNow->format('Y-m-d H:i:s')." NOW <br />";

            if( $dateNow->format('U') <= $dateEnd->format('U') ) {
                $r = true;
            } else {
                $r = false;
            }
//            echo $r."<br />";
//            VarDumper::dump($r,10,true);
//            die($r);
//            if($data['value'] < date) {
//                $r = true;
//            }
        }



        return $r;
    }

    ////BASE//////////////////BASE/////
    ///////////////////////////
    ///////////BASE////////////////
    ///////////////////////////
    ///////////////////////////
    ///BASE//////////////////BASE//////

    /*
    * Can index
    * @param $model ActiveRecord
    * @return boolean If true can
    *
    * */
    public static function canIndex($model = null)
    {
//        if(empty($model)) {
//            return null;
//        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
                                $r = true;
//                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
//                                $r = true;
//                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }
    /*
    * Can view
    * @param $model ActiveRecord
    * @return boolean If true can
    *
    * */
    public static function canView($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }


                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }

    /*
       * Can view
       * @param $model ActiveRecord
       * @return boolean If true can
       *
       * */
    public static function canUpdate($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $client_id = $client->client_id;

                            $updatedStatus = [
                                TlDeliveryProposal::STATUS_NEW,// = 1;  //новый
                                TlDeliveryProposal::STATUS_ADD_CAR,// = 5;  //добавлена машина
                                TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,//'' = 6;  //Добавьте маршрут к заявке
                                TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,// = 7;  //Добавьте к маршруту машину
                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
                                TlDeliveryProposal::STATUS_NOT_ADDED_M3 ,//= 9;  //Не заполнен m3
                                TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE,// = 10;
                            ];

                            if($model->client_id == $client_id && in_array($model->status,$updatedStatus)) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id && $model->created_user_id == $userModel->id) {
                                $r = true;
                            }

                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }


    /*
     * Can delete
     * @param $model ActiveRecord
     * @return boolean If true can
     *
     * */
    public static function canDelete($model)
    {
        if(empty($model)) {
            return null;
        }


        $r = null;
        $client_id = null;

        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $client_id = $client->client_id;

                            $updatedStatus = [
                                TlDeliveryProposal::STATUS_NEW,// = 1;  //новый
                                TlDeliveryProposal::STATUS_ADD_CAR,// = 5;  //добавлена машина
                                TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP,//'' = 6;  //Добавьте маршрут к заявке
                                TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE,// = 7;  //Добавьте к маршруту машину
                                TlDeliveryProposal::STATUS_ROUTE_FORMED,// = 8;  //Маршрут сформирован
                                TlDeliveryProposal::STATUS_NOT_ADDED_M3 ,//= 9;  //Не заполнен m3
                                TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE,// = 10;
                            ];

                            if($model->client_id == $client_id && in_array($model->status,$updatedStatus)) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id && $model->created_user_id == $userModel->id) {
                                $r = true;
                            }

                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }



    //S: BILLING
    /*
  * Can index
  * @param $model ActiveRecord
  * @return boolean If true can
  *
  * */
    public static function canIndexBilling($model = null)
    {
//        if(empty($model)) {
//            return null;
//        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
                            $r = true;
//                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
//                                $r = true;
//                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }
    /*
    * Can view
    * @param $model ActiveRecord
    * @return boolean If true can
    *
    * */
    public static function canViewBilling($model)
    {
        if(empty($model)) {
            return null;
        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = true;
                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $client_id = $client->client_id;

                            if($model->client_id == $client_id) {
                                $r = false;
                            }


                            break;

                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }
    //E: BILLING


    //S: REPORTS
    /*
* Can index
* @param $model ActiveRecord
* @return boolean If true can
*
* */
    public static function canIndexReport($model = null)
    {
//        if(empty($model)) {
//            return null;
//        }

        $r = null;
        $client_id = null;
        if (!Yii::$app->user->isGuest) {

            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {

                    switch ($client->manager_type) {

                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
                            $r = true;
//                            }

                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $client_id = $client->client_id;

//                            if($model->client_id == $client_id) {
//                                $r = true;
//                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return $r;
    }
    /*
    * Can view
    * @param $model ActiveRecord
    * @return boolean If true can
    *
    * */
//    public static function canViewReport($model)
//    {
//        if(empty($model)) {
//            return null;
//        }
//
//        $r = null;
//        $client_id = null;
//        if (!Yii::$app->user->isGuest) {
//
//            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {
//
//                if( $client = ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {
//
//                    switch ($client->manager_type) {
//
//                        case ClientEmployees::TYPE_BASE_ACCOUNT:
//                        case ClientEmployees::TYPE_LOGIST:
//                            $client_id = $client->client_id;
//
//                            if($model->client_id == $client_id) {
//                                $r = true;
//                            }
//
//                            break;
//
//                        case ClientEmployees::TYPE_DIRECTOR:
//                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
//                        case ClientEmployees::TYPE_MANAGER:
//                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $client_id = $client->client_id;
//
//                            if($model->client_id == $client_id) {
//                                $r = true;
//                            }
//
//
//                            break;
//
//                        default:
//                            break;
//                    }
//                }
//            }
//        }
//
//        return $r;
//    }
    //E: REPORTS

}