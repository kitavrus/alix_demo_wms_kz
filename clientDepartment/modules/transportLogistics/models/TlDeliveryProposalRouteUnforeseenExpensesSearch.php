<?php

namespace common\modules\transportLogistics\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses;

/**
 * TlDeliveryProposalRouteUnforeseenExpensesSearch represents the model behind the search form about `frontend\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses`.
 */
class TlDeliveryProposalRouteUnforeseenExpensesSearch extends TlDeliveryProposalRouteUnforeseenExpenses
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'tl_delivery_proposal_id', 'tl_delivery_route_id', 'delivery_date', 'cash_no', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'comment'], 'safe'],
            [['price', 'with_vat'], 'number'],
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
        $query = TlDeliveryProposalRouteUnforeseenExpenses::find();

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
            'delivery_date' => $this->delivery_date,
            'price' => $this->price,
            'cash_no' => $this->cash_no,
            'with_vat' => $this->with_vat,
            'status' => $this->status,
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
