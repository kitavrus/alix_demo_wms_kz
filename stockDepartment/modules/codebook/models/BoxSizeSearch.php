<?php

namespace stockDepartment\modules\codebook\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\codebook\models\BoxSize;

/**
 * BoxSizeSearch represents the model behind the search form about `common\modules\codebook\models\BoxSize`.
 */
class BoxSizeSearch extends BoxSize
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['box_height', 'box_width', 'box_length', 'box_code'], 'safe'],
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
        $query = BoxSize::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
//            'created_user_id' => $this->created_user_id,
//            'updated_user_id' => $this->updated_user_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'box_height', $this->box_height])
            ->andFilterWhere(['like', 'box_width', $this->box_width])
            ->andFilterWhere(['like', 'box_length', $this->box_length])
            ->andFilterWhere(['like', 'box_code', $this->box_code]);

        return $dataProvider;
    }
}
