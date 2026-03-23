<?php

namespace clientDepartment\modules\store\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\store\models\Store;
//use common\modules\user\models\User;
//use common\modules\client\models\Client;
use common\modules\client\models\ClientEmployees;
use clientDepartment\modules\client\components\ClientManager;

/**
 * StoreSearch represents the model behind the search form about `app\modules\store\models\Store`.
 */
class StoreSearch extends Store
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','type_use', 'address_type', 'status', 'intercom', 'floor', 'elevator', 'created_at', 'updated_at'], 'integer'],
            [['shopping_center_name','client_id','city_id', 'name', 'contact_first_name', 'contact_middle_name', 'contact_last_name', 'contact_first_name2', 'contact_middle_name2', 'contact_last_name2', 'email', 'phone', 'phone_mobile', 'title', 'description', 'zip_code', 'street', 'house', 'entrance', 'flat', 'comment', 'shop_code'], 'safe'],
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
        $query = Store::find();


        // S: Create function
        $client_id = -1;
        $route_to = null;
        if(!Yii::$app->user->isGuest) {
//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if ($client = ClientManager::getClientByUserID()) {
                    switch ($client->manager_type) {
                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $client_id = $client->client_id;
                            break;
                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $route_to = $client->store_id;
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
            'type_use' => self::TYPE_USE_STORE,
            'route_to' => $route_to,
        ]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
//            'client_id' => $this->client_id,
            'id' => $this->id,
            'city_id' => $this->city_id,
            'type_use' => $this->type_use,
            'status' => $this->status,
            'intercom' => $this->intercom,
            'floor' => $this->floor,
            'elevator' => $this->elevator,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'contact_first_name', $this->contact_first_name])
            ->andFilterWhere(['like', 'contact_middle_name', $this->contact_middle_name])
            ->andFilterWhere(['like', 'contact_last_name', $this->contact_last_name])
            ->andFilterWhere(['like', 'contact_first_name2', $this->contact_first_name2])
            ->andFilterWhere(['like', 'contact_middle_name2', $this->contact_middle_name2])
            ->andFilterWhere(['like', 'contact_last_name2', $this->contact_last_name2])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone_mobile', $this->phone_mobile])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
//            ->andFilterWhere(['like', 'country', $this->country])
//            ->andFilterWhere(['like', 'region', $this->region])
//            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'zip_code', $this->zip_code])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'house', $this->house])
            ->andFilterWhere(['like', 'entrance', $this->entrance])
            ->andFilterWhere(['like', 'flat', $this->flat])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'shopping_center_name', $this->shopping_center_name])
            ->andFilterWhere(['like', 'shop_code', $this->shop_code]);

        return $dataProvider;
    }
}
