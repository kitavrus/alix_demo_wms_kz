<?php

namespace app\modules\placementUnit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\placementUnit\models\PlacementUnit;

/**
 * PlacementUnitSearch represents the model behind the search form about `\app\modules\placementUnit\models\PlacementUnit`.
 */
class PlacementUnitSearch extends PlacementUnit
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'zone_id', 'count_unit', 'type_inout', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
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
        $query = PlacementUnit::find();

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
            'client_id' => $this->client_id,
            'zone_id' => $this->zone_id,
            'count_unit' => $this->count_unit,
            'type_inout' => $this->type_inout,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'barcode', $this->barcode]);

        return $dataProvider;
    }
}
