<?php

namespace stockDepartment\modules\product\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\product\models\Product;

/**
 * ProductSearch represents the model behind the search form about `app\modules\product\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'status', 'created_user_id', 'modified_user_id', 'created_at', 'updated_at'], 'integer'],
            [['client_product_id', 'name', 'sku'], 'safe'],
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
        $query = Product::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'modified_user_id' => $this->modified_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'client_product_id', $this->client_product_id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'sku', $this->sku]);

        return $dataProvider;
    }
}
