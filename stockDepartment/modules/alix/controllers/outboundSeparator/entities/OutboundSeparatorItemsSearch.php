<?php

namespace stockDepartment\modules\alix\controllers\outboundSeparator\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use stockDepartment\modules\alix\controllers\outboundSeparator\entities\OutboundSeparatorItems;

/**
 * OutboundSeparatorItemsSearch represents the model behind the search form of `stockDepartment\modules\alix\controllers\outboundSeparator\domain\entities\OutboundSeparatorItems`.
 */
class OutboundSeparatorItemsSearch extends OutboundSeparatorItems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'outbound_separator_id', 'outbound_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['order_number', 'out_box_barcode', 'product_barcode', 'status'], 'safe'],
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
        $query = OutboundSeparatorItems::find();

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
            'outbound_separator_id' => $this->outbound_separator_id,
            'outbound_id' => $this->outbound_id,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'order_number', $this->order_number])
            ->andFilterWhere(['like', 'out_box_barcode', $this->out_box_barcode])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
