<?php

namespace stockDepartment\modules\billing\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\billing\models\TlDeliveryProposalBillingConditions;

/**
 * TlDeliveryProposalBillingConditionsSearch represents the model behind the search form about `common\modules\billing\models\TlDeliveryProposalBillingConditions`.
 */
class TlDeliveryProposalBillingConditionsSearch extends TlDeliveryProposalBillingConditions
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tl_delivery_proposal_billing_id', 'client_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['price_invoice', 'price_invoice_with_vat'], 'number'],
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
        $query = TlDeliveryProposalBillingConditions::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tl_delivery_proposal_billing_id' => $this->tl_delivery_proposal_billing_id,
            'client_id' => $this->client_id,
            'price_invoice' => $this->price_invoice,
            'price_invoice_with_vat' => $this->price_invoice_with_vat,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'formula_tariff', $this->formula_tariff])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
