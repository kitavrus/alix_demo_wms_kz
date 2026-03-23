<?php

namespace stockDepartment\modules\product\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\product\models\ProductBarcodes;

/**
 * ProductBarcodesSearchSearch represents the model behind the search form about `common\modules\product\models\Product`.
 */
class ProductBarcodesSearch extends ProductBarcodes
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'product_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['barcode'], 'safe'],
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
        $query = ProductBarcodes::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
        ]);

        $query->andFilterWhere(['like', 'barcode', $this->barcode]);

        return $dataProvider;
    }
}
