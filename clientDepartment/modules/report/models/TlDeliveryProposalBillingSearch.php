<?php

namespace clientDepartment\modules\report\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\billing\models\TlDeliveryProposalBilling;
use clientDepartment\modules\client\components\ClientManager;
use common\modules\client\models\ClientEmployees;

/**
 * TlDeliveryProposalBillingSearch represents the model behind the search form about `common\modules\billing\models\TlDeliveryProposalBilling`.
 */
class TlDeliveryProposalBillingSearch extends TlDeliveryProposalBilling
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'from_country_id', 'from_region_id', 'from_city_id', 'route_from', 'rule_type', 'route_to', 'number_places', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['mc', 'kg', 'price_invoice', 'price_invoice_with_vat'], 'number'],
            [['formula_tariff', 'comment'], 'safe'],
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
        $query = TlDeliveryProposalBilling::find();


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
//                            $client_id = $client->client_id;
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
        ]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'from_country_id' => $this->from_country_id,
            'from_region_id' => $this->from_region_id,
            'from_city_id' => $this->from_city_id,
            'route_from' => $this->route_from,
            'route_to' => $this->route_to,
            'rule_type' => $this->rule_type,
            'mc' => $this->mc,
            'kg' => $this->kg,
            'number_places' => $this->number_places,
            'price_invoice' => $this->price_invoice,
            'price_invoice_with_vat' => $this->price_invoice_with_vat,
            'status' => $this->status,
//            'created_user_id' => $this->created_user_id,
//            'updated_user_id' => $this->updated_user_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

//        $query->andFilterWhere(['like', 'formula_tariff', $this->formula_tariff])
//            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
