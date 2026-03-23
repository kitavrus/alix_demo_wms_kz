<?php

namespace app\modules\agentBilling\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\agentBilling\models\TlAgentBillingConditions;

/**
 * TlAgentBillingConditionsSearch represents the model behind the search form about `common\modules\agentBilling\models\TlAgentBillingConditions`.
 */
class TlAgentBillingConditionsSearch extends TlAgentBillingConditions
{

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TlAgentBillingConditions::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tl_agents_billing_id' => $this->tl_agents_billing_id,
            'agent_id' => $this->agent_id,
            'price_invoice' => $this->price_invoice,
            'price_invoice_with_vat' => $this->price_invoice_with_vat,
            'route_from' => $this->route_from,
            'route_to' => $this->route_to,
            'rule_type' => $this->rule_type,
            'price_kg' => $this->price_kg,
            'price_kg_with_vat' => $this->price_kg_with_vat,
            'price_mc' => $this->price_mc,
            'price_mc_with_vat' => $this->price_mc_with_vat,
            'price_pl' => $this->price_pl,
            'price_pl_with_vat' => $this->price_pl_with_vat,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        $query->andFilterWhere(['like', 'formula_tariff', $this->formula_tariff])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
