<?php

namespace common\ecommerce\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\ecommerce\entities\EcommerceOutbound;

/**
 * EcommerceOutboundSearch represents the model behind the search form of `common\ecommerce\entities\EcommerceOutbound`.
 */
class EcommerceOutboundItemSearch extends EcommerceOutboundItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_sku','product_barcode'], 'string'],
            [['expected_qty','allocated_qty','accepted_qty'], 'integer'],
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
    public function search($params,$outboundId)
    {
        $query = EcommerceOutboundItem::find();

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
            'outbound_id' => $outboundId,
            'id' => $this->id,
            'expected_qty' => $this->expected_qty,
            'allocated_qty' => $this->allocated_qty,
            'accepted_qty' => $this->accepted_qty,
        ]);

        $query->andFilterWhere(['like', 'product_sku', $this->product_sku])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);

        return $dataProvider;
    }
}
