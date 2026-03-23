<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 20.10.14
 * Time: 11:32
 */

namespace common\modules\client\components;

use common\modules\leads\models\TransportationOrderLead;
use common\modules\client\models\Client;
use Yii;
use yii\base\Component;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\modules\leads\models\ExternalClientLead;
use yii\web\HttpException;
use common\modules\client\models\ClientEmployees;
use common\modules\user\models\User;


class ClientManager extends Component {

    /*
    * Создает полноценного клиента
    * @param int $externalClientID id неподтвержденного клиента
    * */
    public static function confirmExternalClient($externalClientID){
        $externalClient = ExternalClientLead::findOne(['id'=>$externalClientID, 'status' => ExternalClientLead::CLIENT_STATUS_UNCONFIRMED]);

        if($externalClient){
            $client = new Client();
            $client->user_id = $externalClient->user_id;
            $client->username = $externalClient->email;
            $client->legal_company_name = ($externalClient->client_type==ExternalClientLead::CLIENT_TYPE_CORPORATE ? $externalClient->legal_company_name : $externalClient->full_name);
            $client->title = $externalClient->full_name;
            $client->full_name = $externalClient->full_name;
            $client->phone = $externalClient->phone;
            $client->phone_mobile = $externalClient->phone;
            $client->email = $externalClient->email;
            $client->status = Client::STATUS_ACTIVE;
            $client->client_type = $externalClient->client_type;
            if($client->save(false)){
                self::updateUnconfirmedClientOrders($externalClient->id, $client->id);
                // Create base client employee account
                $clientEmployee = new ClientEmployees();
                $clientEmployee->store_id = 0;
                $clientEmployee->client_id = $client->id;
                $clientEmployee->user_id = $client->user_id;
                $clientEmployee->username = $client->username;
                $clientEmployee->full_name = $client->full_name;
                $clientEmployee->phone_mobile = $client->phone_mobile;
                $clientEmployee->email = $client->email;
                $clientEmployee->password = $client->password;
                $clientEmployee->status = ClientEmployees::STATUS_ACTIVE;
                if($client->client_type==ExternalClientLead::CLIENT_TYPE_CORPORATE){
                    $clientEmployee->manager_type = ClientEmployees::TYPE_CORPORATE_CLIENT;
                } elseif($client->client_type==ExternalClientLead::CLIENT_TYPE_PERSON) {
                    $clientEmployee->manager_type = ClientEmployees::TYPE_PERSONAL_CLIENT;
                }
                if($clientEmployee->save(false)){
                    $externalClient->status = ExternalClientLead::CLIENT_STATUS_CONFIRMED;
                    if($externalClient->save()){
                        return true;
                    }
                }

            }
        }

        return false;
    }

    /*
     * Находит и обновляет client_id у всех заявок неподтвержденного клиента
     * @param int $externalClientID
     * @param int $internalClientID
     * */
    private static function updateUnconfirmedClientOrders($externalClientID, $internalClientID){
        $external_client = ExternalClientLead::findOne(['id' => $externalClientID, 'status' => ExternalClientLead::CLIENT_STATUS_UNCONFIRMED]);
        if($external_client){
            $leadOrders = TransportationOrderLead::findAll(['client_id'=>$externalClientID]);
            if(!empty($leadOrders)){
                foreach ($leadOrders as $leadOrder){
                    $leadOrder->client_id = $internalClientID;
                    $leadOrder->save(false);
                }
            }
        }
    }

    /*
     * Находит и обновляет client_id у всех анонимных заявок, с указанным номером телефона
     * @param numerical $phone
     * @param int $client_id
     * */
    private static function updateAnonymousClientOrders($phone, $client_id){

            $leadOrders = TransportationOrderLead::findAll(['customer_phone'=>$phone]);
            if(!empty($leadOrders)){
                foreach ($leadOrders as $leadOrder){
                    $leadOrder->client_id = $client_id;
                    $leadOrder->save(false);
                }
                return true;
            }
            return false;
    }

    /*
     * Создает клиента на основе данных об отправителе
     * из lead заявки
     * @param int $order_id
     * @return mixed
     * */
    public static function createClientFromOrder($order_id)
    {
        $leadOrder = TransportationOrderLead::findOne($order_id);
        if(!empty($leadOrder)){
            //Добавляем запись в Client
            $client = new Client();
            $client->username = $leadOrder->customer_phone;
            $client->title = $leadOrder->customer_name;
            $client->email = $leadOrder->customer_phone.'@dummy.com';
            $client->full_name = $leadOrder->customer_name;
            $client->phone_mobile = $leadOrder->customer_phone;
            $client->phone = $leadOrder->customer_phone;
            $client->status = Client::STATUS_ACTIVE;
            $client->client_type = Client::CLIENT_TYPE_PERSONAL;
            if($client->save(false)){
                //обновляем client_id у всех заявок с таким же номером телефона
                self::updateAnonymousClientOrders($leadOrder->customer_phone, $client->id);
                // Добавляем запись в ClientEmployees
                $clientEmployee = new ClientEmployees();
                $clientEmployee->store_id = 0;
                $clientEmployee->client_id = $client->id;
                $clientEmployee->email = $client->email;
                $clientEmployee->username = $client->username;
                $clientEmployee->full_name = $client->full_name;
                $clientEmployee->phone_mobile = $client->phone_mobile;
                $clientEmployee->status = ClientEmployees::STATUS_ACTIVE;
                $clientEmployee->manager_type = ClientEmployees::TYPE_PERSONAL_CLIENT;
                if($clientEmployee->save(false)){
                    $externalClient = new ExternalClientLead();
                    $externalClient->scenario = 'create-client-from-order';
                    $externalClient->full_name = $client->full_name;
                    $externalClient->phone = $client->phone_mobile;
                    $externalClient->email = $client->email;
                    $externalClient->save(false);

                    //Создаем пользователя
                    $userModel = \Yii::createObject([
                        'class'    => User::className(),
                        'scenario' => 'create_client_from_order',
                    ]);

                    $userModel->username = $client->phone;
                    $userModel->email = $client->email;
                    $userModel->user_type = User::USER_TYPE_EXTERNAL_CLIENT;
                    $userModel->password = $client->phone;

                    if ($userModel->create()) {
                        $client->user_id = $userModel->id;
                        $clientEmployee->user_id = $userModel->id;
                        $externalClient->user_id = $userModel->id;
                        $externalClient->status = ExternalClientLead::CLIENT_STATUS_CONFIRMED;
                        $externalClient->client_type = ExternalClientLead::CLIENT_TYPE_PERSON;
                        $externalClient->save(false);
                        $client->save(false);
                        $clientEmployee->save(false);
                        return $client->id;
                    }
                }
            }
        }

        return false;
    }
}