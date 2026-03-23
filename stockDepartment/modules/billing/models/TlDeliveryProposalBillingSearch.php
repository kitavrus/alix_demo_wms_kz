<?php

namespace stockDepartment\modules\billing\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\billing\models\TlDeliveryProposalBilling;

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
            [['rule_type','client_id', 'from_country_id', 'tariff_type', 'cooperation_type', 'delivery_type', 'from_region_id', 'from_city_id', 'to_region_id', 'to_city_id', 'to_country_id', 'route_from', 'route_to', 'number_places', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
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
            'to_country_id' => $this->to_country_id,
            'to_region_id' => $this->to_region_id,
            'to_city_id' => $this->to_city_id,
            'route_from' => $this->route_from,
            'route_to' => $this->route_to,
            'rule_type' => $this->rule_type,
            'mc' => $this->mc,
            'kg' => $this->kg,
            'number_places' => $this->number_places,
            'price_invoice' => $this->price_invoice,
            'price_invoice_with_vat' => $this->price_invoice_with_vat,
            'status' => $this->status,
            'tariff_type' => $this->tariff_type,
            'cooperation_type' => $this->cooperation_type,
            'delivery_type' => $this->delivery_type,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'formula_tariff', $this->formula_tariff])
                ->andFilterWhere(['like', 'comment', $this->comment])
                ->andFilterWhere(['like', 'delivery_term', $this->delivery_term,]);

        return $dataProvider;
    }
}
