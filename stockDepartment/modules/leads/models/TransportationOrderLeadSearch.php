<?php

namespace app\modules\leads\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\leads\models\TransportationOrderLead;

/**
 * modelsTransportationOrderLeadSearch represents the model behind the search form about `common\modules\leads\models\TransportationOrderLead`.
 */
class TransportationOrderLeadSearch extends TransportationOrderLead
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'from_city_id', 'to_city_id', 'places', 'weight', 'volume', 'status', 'source', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['customer_name', 'customer_phone', 'customer_address', 'recipient_name', 'recipient_phone', 'recipient_address', 'customer_comment', 'declared_value', 'package_description', 'order_number'], 'safe'],
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
        $query = TransportationOrderLead::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'from_city_id' => $this->from_city_id,
            'to_city_id' => $this->to_city_id,
            'places' => $this->places,
            'weight' => $this->weight,
            'volume' => $this->volume,
            'status' => $this->status,
            'source' => $this->source,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'customer_phone', $this->customer_phone])
            ->andFilterWhere(['like', 'customer_address', $this->customer_address])
            ->andFilterWhere(['like', 'recipient_name', $this->recipient_name])
            ->andFilterWhere(['like', 'recipient_phone', $this->recipient_phone])
            ->andFilterWhere(['like', 'recipient_address', $this->recipient_address])
            ->andFilterWhere(['like', 'customer_comment', $this->customer_comment])
            ->andFilterWhere(['like', 'declared_value', $this->declared_value])
            ->andFilterWhere(['like', 'package_description', $this->package_description])
            ->andFilterWhere(['like', 'order_number', $this->order_number]);

        return $dataProvider;
    }
}
