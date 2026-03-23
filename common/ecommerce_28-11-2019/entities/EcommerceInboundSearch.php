<?php

namespace common\ecommerce\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\ecommerce\entities\EcommerceInbound;

/**
 * EcommerceInboundSearch represents the model behind the search form of `common\ecommerce\entities\EcommerceInbound`.
 */
class EcommerceInboundSearch extends EcommerceInbound
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'expected_box_qty', 'accepted_box_qty', 'expected_lot_qty', 'accepted_lot_qty', 'expected_product_qty', 'accepted_product_qty', 'status', 'begin_datetime', 'end_datetime', 'date_confirm', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['party_number', 'order_number'], 'safe'],
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
        $query = EcommerceInbound::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'expected_box_qty' => $this->expected_box_qty,
            'accepted_box_qty' => $this->accepted_box_qty,
            'expected_lot_qty' => $this->expected_lot_qty,
            'accepted_lot_qty' => $this->accepted_lot_qty,
            'expected_product_qty' => $this->expected_product_qty,
            'accepted_product_qty' => $this->accepted_product_qty,
            'status' => $this->status,
            'begin_datetime' => $this->begin_datetime,
            'end_datetime' => $this->end_datetime,
            'date_confirm' => $this->date_confirm,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'party_number', $this->party_number])
            ->andFilterWhere(['like', 'order_number', $this->order_number]);

        return $dataProvider;
    }
}
