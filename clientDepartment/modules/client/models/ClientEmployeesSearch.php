<?php

namespace clientDepartment\modules\client\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\client\models\ClientEmployees;
use clientDepartment\modules\client\components\ClientManager;
use yii\helpers\VarDumper;
//use common\modules\store\models\Store;
//use common\modules\user\models\User;
//use common\modules\client\models\Client;


/**
 * ClientManagersSearch represents the model behind the search form about `frontend\modules\client\models\ClientManagers`.
 */
class ClientEmployeesSearch extends ClientEmployees
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'store_id', 'client_id', 'user_id', 'manager_type', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['username', 'first_name', 'middle_name', 'last_name', 'phone', 'phone_mobile', 'email'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ClientEmployees::find();


        // S: Create function
        $client_id = -1;
        $route_to = null;
        $store_id = null;
        if(!Yii::$app->user->isGuest) {
//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

//                switch($userModel->user_type) {
//                    case User::USER_TYPE_CLIENT:
//                        if($client = Client::findOne(['user_id'=>$userModel->id])) {
//                            $client_id = $client->id;
//                        }
//
//                        break;
//                    case User::USER_TYPE_STORE_MANAGER:
//                        if($clientManager = ClientManagers::findOne(['user_id'=>$userModel->id])) {
//                            $route_to = $clientManager->store_id;
//                            $client_id = $clientManager->client_id;
//                        }
//
//                        break;
//                }

                if ($client = ClientManager::getClientByUserID()) {
//                    VarDumper::dump($client,10,true);
                    switch ($client->manager_type) {
                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $client_id = $client->client_id;

                            break;
                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                            $client_id = $client->client_id;
                            $store_id = $client->store_id;

                            break;
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                            $route_to = $client->store_id;
                            $client_id = $client->client_id;

                            break;
                        default:
                            break;
                    }
                }


            }
//        }

        // E: Create function

        $query->andFilterWhere([
            'client_id' => $client_id,
//            'type_use' => self::TYPE_USE_STORE,
            'store_id' => $store_id,
        ]);





        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);


//        VarDumper::dump($dataProvider,10,true);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'store_id' => $this->store_id,
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'manager_type' => $this->manager_type,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
//            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone_mobile', $this->phone_mobile])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
