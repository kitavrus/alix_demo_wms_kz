<?php

namespace common\modules\transportLogistics\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;

/**
 * TlDeliveryProposalRouteCarsSearch represents the model behind the search form about `frontend\modules\transportLogistics\models\TlDeliveryProposalRouteCars`.
 */
class TlDeliveryProposalRouteCarsSearch extends TlDeliveryProposalRouteCars
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'route_city_from', 'route_city_to', 'agent_id', 'car_id', 'grzch', 'cash_no', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['agent_id','car_id','shipped_datetime','delivery_date', 'driver_name', 'driver_phone', 'driver_auto_number', 'comment'], 'safe'],
            [['mc_filled', 'kg_filled', 'price_invoice', 'price_invoice_with_vat'], 'number'],
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
//            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
            'sort'=> ['defaultOrder' => ['shipped_datetime'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'route_city_from' => $this->route_city_from,
            'route_city_to' => $this->route_city_to,
            'delivery_date' => $this->delivery_date,
            'shipped_datetime' => $this->shipped_datetime,
            'mc_filled' => $this->mc_filled,
            'kg_filled' => $this->kg_filled,
            'agent_id' => $this->agent_id,
            'car_id' => $this->car_id,
//            'grzch' => $this->grzch,
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

        $query->andFilterWhere(['like', 'driver_name', $this->driver_name])
            ->andFilterWhere(['like', 'driver_phone', $this->driver_phone])
            ->andFilterWhere(['like', 'driver_auto_number', $this->driver_auto_number])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
