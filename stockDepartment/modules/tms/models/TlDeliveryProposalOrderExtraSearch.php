<?php

namespace stockDepartment\modules\tms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposalOrderExtras;

/**
 * TlDeliveryProposalOrderExtraSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposalOrderExtras`.
 */
class TlDeliveryProposalOrderExtraSearch extends TlDeliveryProposalOrderExtras
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'tl_delivery_proposal_id', 'tl_delivery_route_id', 'tl_delivery_proposal_order_id', 'number_places', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'comment'], 'safe'],
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
        $query = TlDeliveryProposalOrderExtras::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'tl_delivery_proposal_id' => $this->tl_delivery_proposal_id,
            'tl_delivery_route_id' => $this->tl_delivery_route_id,
            'tl_delivery_proposal_order_id' => $this->tl_delivery_proposal_order_id,
            'number_places' => $this->number_places,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
