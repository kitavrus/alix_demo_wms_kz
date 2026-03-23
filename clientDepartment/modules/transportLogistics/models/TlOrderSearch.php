<?php

namespace common\modules\transportLogistics\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlOrder;

/**
 * TlOrderSearch represents the model behind the search form about `frontend\modules\transportLogistics\models\TlOrder`.
 */
class TlOrderSearch extends TlOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'route_from', 'route_to', 'number_places', 'number_places_scanned', 'cross_doc', 'dc', 'hangers', 'other', 'auto_type', 'angar', 'grzch', 'total_qty', 'agent_id', 'cash_no', 'sale_for_client', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['delivery_date', 'comment'], 'safe'],
            [['mc', 'mc_actual', 'kg', 'kg_actual', 'price_square_meters', 'price_total', 'costs_region', 'our_profit', 'costs_cache', 'with_vat'], 'number'],
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
        $query = TlOrder::find();

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
            'client_id' => $this->client_id,
            'route_from' => $this->route_from,
            'route_to' => $this->route_to,
            'delivery_date' => $this->delivery_date,
            'mc' => $this->mc,
            'mc_actual' => $this->mc_actual,
            'kg' => $this->kg,
            'kg_actual' => $this->kg_actual,
            'number_places' => $this->number_places,
            'number_places_scanned' => $this->number_places_scanned,
            'cross_doc' => $this->cross_doc,
            'dc' => $this->dc,
            'hangers' => $this->hangers,
            'other' => $this->other,
            'auto_type' => $this->auto_type,
            'angar' => $this->angar,
            'grzch' => $this->grzch,
            'total_qty' => $this->total_qty,
            'price_square_meters' => $this->price_square_meters,
            'price_total' => $this->price_total,
            'costs_region' => $this->costs_region,
            'agent_id' => $this->agent_id,
            'cash_no' => $this->cash_no,
            'sale_for_client' => $this->sale_for_client,
            'our_profit' => $this->our_profit,
            'costs_cache' => $this->costs_cache,
            'with_vat' => $this->with_vat,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
