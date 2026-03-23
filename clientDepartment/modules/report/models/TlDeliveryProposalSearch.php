<?php

namespace clientDepartment\modules\report\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use common\helpers\iHelper;
use clientDepartment\modules\client\components\ClientManager;
use common\modules\client\models\ClientEmployees;

/**
 * TlDeliveryProposalSearch represents the model behind the search form about `frontend\modules\transportLogistics\models\TlDeliveryProposal`.
 */
class TlDeliveryProposalSearch extends TlDeliveryProposal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_client_confirmed','id', 'client_id', 'route_from', 'route_to', 'mc_actual', 'kg', 'kg_actual', 'number_places', 'number_places_actual', 'cash_no', 'price_invoice', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['mc', 'price_invoice_with_vat'], 'number'],
            [['shipped_datetime','expected_delivery_date','delivery_date'], 'string'],
            [['comment', 'route_from', 'route_to',], 'safe'],
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
        $query = TlDeliveryProposal::find();

        // S: Create function
        $client_id = -1;
        $routeFromTo = null;
        if(!Yii::$app->user->isGuest) {
//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {
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
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
//                                $routeFromTo = $client->store_id;
                                $client_id = $client->client_id;
                            break;
                        default:
                            break;
                    }
                }


            }
//        }

        // E: Create function

//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($userModel,10,true);
//        echo "<br />";
//        VarDumper::dump($route_to,10,true);
//        echo "<br />";
//        VarDumper::dump($client_id,10,true);
//        die('');

        $query->andFilterWhere([
            'client_id' => $client_id,
//            'route_to' => $routeFromTo,
        ]);

//        if(!empty($routeFromTo)) {
//            $query->andWhere('route_to = :routeFromTo OR route_from=:routeFromTo',[':routeFromTo'=>$routeFromTo]);
//        }

        $query->with(['client']);

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
            'id' => $this->id,
            'is_client_confirmed' => $this->is_client_confirmed,
//            'client_id' => $this->client_id,
//            'client_id' => $client_id,
            'route_from' => $this->route_from,
            'route_to' => $this->route_to,
//            'delivery_date' => $this->delivery_date,
            'mc' => $this->mc,
            'mc_actual' => $this->mc_actual,
            'kg' => $this->kg,
            'kg_actual' => $this->kg_actual,
            'number_places' => $this->number_places,
            'number_places_actual' => $this->number_places_actual,
            'cash_no' => $this->cash_no,
            'price_invoice' => $this->price_invoice,
            'price_invoice_with_vat' => $this->price_invoice_with_vat,
            'status' => $this->status,
        ]);

//        $query->andFilterWhere(['like', 'comment', $this->comment]);


        // SHIPPED DATETIME
        if(!empty($this->shipped_datetime)) {
            $date = explode('/',$this->shipped_datetime);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'shipped_datetime', $date[0],$date[1]]);
        }

        // EXPECTED DELIVERY DATETIME
        if(!empty($this->expected_delivery_date)) {
            $date = explode('/',$this->expected_delivery_date);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'expected_delivery_date', $date[0],$date[1]]);
        }

        // DELIVERY DATE
        if(!empty($this->delivery_date)) {
            $date = explode('/',$this->delivery_date);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'delivery_date', $date[0],$date[1]]);
        }

        return $dataProvider;
    }
}