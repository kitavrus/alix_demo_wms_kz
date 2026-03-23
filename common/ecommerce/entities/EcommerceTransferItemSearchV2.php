<?php

namespace common\ecommerce\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\ecommerce\entities\EcommerceTransferItems;

/**
 * EcommerceTransferItemSearch represents the model behind the search form of `common\ecommerce\entities\EcommerceTransferItems`.
 */
class EcommerceTransferItemSearchV2 extends EcommerceTransferItems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'transfer_id', 'client_Quantity', 'expected_box_qty', 'begin_datetime', 'end_datetime', 'expected_qty', 'allocated_qty', 'accepted_qty', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['client_BatchId', 'client_OutboundId', 'client_SkuId', 'client_Status', 'status', 'api_status', 'product_sku', 'product_name', 'product_model', 'product_barcode'], 'safe'],
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
    public function search($params,$aTransferId = null)
    {
        $query = EcommerceTransferItems::find();

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
            'transfer_id' => $aTransferId,
            'client_Quantity' => $this->client_Quantity,
            'expected_box_qty' => $this->expected_box_qty,
            'begin_datetime' => $this->begin_datetime,
            'end_datetime' => $this->end_datetime,
            'expected_qty' => $this->expected_qty,
            'allocated_qty' => $this->allocated_qty,
            'accepted_qty' => $this->accepted_qty,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'client_BatchId', $this->client_BatchId])
            ->andFilterWhere(['like', 'client_OutboundId', $this->client_OutboundId])
            ->andFilterWhere(['like', 'client_SkuId', $this->client_SkuId])
            ->andFilterWhere(['like', 'client_Status', $this->client_Status])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'api_status', $this->api_status])
            ->andFilterWhere(['like', 'product_sku', $this->product_sku])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'product_model', $this->product_model])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);

        return $dataProvider;
    }
}
