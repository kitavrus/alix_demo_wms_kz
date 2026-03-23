<?php

namespace stockDepartment\modules\tms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;

/**
 * TlDeliveryProposalRouteCarSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposalRouteCars`.
 */
class TlDeliveryProposalRouteCarSearch extends TlDeliveryProposalRouteCars
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'route_from', 'route_to', 'delivery_date', 'agent_id', 'car_id', 'grzch', 'cash_no', 'price_invoice', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['mc_filled', 'kg_filled', 'price_invoice_with_vat'], 'number'],
            [['comment'], 'safe'],
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
        $query = TlDeliveryProposalRouteCars::find();

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
            'route_from' => $this->route_from,
            'route_to' => $this->route_to,
            'delivery_date' => $this->delivery_date,
            'mc_filled' => $this->mc_filled,
            'kg_filled' => $this->kg_filled,
            'agent_id' => $this->agent_id,
            'car_id' => $this->car_id,
            'grzch' => $this->grzch,
            'cash_no' => $this->cash_no,
            'price_invoice' => $this->price_invoice,
            'price_invoice_with_vat' => $this->price_invoice_with_vat,
            'status' => $this->status,
            'status_invoice' => $this->status_invoice,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
