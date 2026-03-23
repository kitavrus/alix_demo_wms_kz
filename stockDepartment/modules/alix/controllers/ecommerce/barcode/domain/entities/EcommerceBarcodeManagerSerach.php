<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\barcode\domain\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EcommerceBarcodeManagerSerach represents the model behind the search form of `common\ecommerce\entities\EcommerceBarcodeManager`.
 */
class EcommerceBarcodeManagerSerach extends EcommerceBarcodeManager
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'counter', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['barcode_prefix', 'title'], 'safe'],
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
        $query = EcommerceBarcodeManager::find();

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
            'counter' => $this->counter,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'barcode_prefix', $this->barcode_prefix])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
