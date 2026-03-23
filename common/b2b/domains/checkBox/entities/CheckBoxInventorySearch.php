<?php

namespace common\b2b\domains\checkBox\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\b2b\domains\checkBox\entities\CheckBoxInventory;

/**
 * CheckBoxInventorySearch represents the model behind the search form of `common\b2b\entities\B2bCheckBoxInventory`.
 */
class CheckBoxInventorySearch extends CheckBoxInventory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'expected_product_qty', 'scanned_product_qty', 'expected_box_qty', 'scanned_box_qty', 'begin_datetime', 'end_datetime', 'complete_date', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['inventory_key', 'status'], 'safe'],
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
        $query = CheckBoxInventory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $query->orderBy(['id'=>SORT_DESC]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'expected_product_qty' => $this->expected_product_qty,
            'scanned_product_qty' => $this->scanned_product_qty,
            'expected_box_qty' => $this->expected_box_qty,
            'scanned_box_qty' => $this->scanned_box_qty,
            'begin_datetime' => $this->begin_datetime,
            'end_datetime' => $this->end_datetime,
            'complete_date' => $this->complete_date,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'inventory_key', $this->inventory_key])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
