<?php

namespace stockDepartment\modules\tms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRoute;

/**
 * TlDeliveryProposalDefaultSubRouteSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRoute`.
 */
class TlDeliveryProposalDefaultSubRouteSearch extends TlDeliveryProposalDefaultSubRoute
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tl_delivery_proposal_default_route_id', 'client_id', 'from_point_id', 'to_point_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
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
        $query = TlDeliveryProposalDefaultSubRoute::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tl_delivery_proposal_default_route_id' => $this->tl_delivery_proposal_default_route_id,
            'client_id' => $this->client_id,
            'from_point_id' => $this->from_point_id,
            'to_point_id' => $this->to_point_id,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        return $dataProvider;
    }
}
