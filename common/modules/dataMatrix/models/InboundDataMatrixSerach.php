<?php

namespace common\modules\dataMatrix\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\dataMatrix\models\InboundDataMatrix;

/**
 * InboundDataMatrixSerach represents the model behind the search form of `\common\modules\dataMatrix\models\InboundDataMatrix`.
 */
class InboundDataMatrixSerach extends InboundDataMatrix
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['inbound_id', 'inbound_item_id', 'product_barcode', 'product_model', 'data_matrix_code', 'status', 'print_status'], 'safe'],
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
        $query = InboundDataMatrix::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
			'pagination' => [
				'pageSize' => 50,
			],
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
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'inbound_id', $this->inbound_id])
            ->andFilterWhere(['like', 'inbound_item_id', $this->inbound_item_id])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'product_model', $this->product_model])
            ->andFilterWhere(['like', 'data_matrix_code', $this->data_matrix_code])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'print_status', $this->print_status]);

        return $dataProvider;
    }
}
