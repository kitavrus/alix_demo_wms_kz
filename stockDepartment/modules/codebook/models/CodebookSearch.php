<?php

namespace stockDepartment\modules\codebook\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\codebook\models\Codebook;

/**
 * CodebookSearch represents the model behind the search form about `app\modules\codebook\models\Codebook`.
 */
class CodebookSearch extends Codebook
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'count_cell', 'barcode', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['cod_prefix', 'name'], 'safe'],
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
        $query = Codebook::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'count_cell' => $this->count_cell,
            'barcode' => $this->barcode,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'cod_prefix', $this->cod_prefix])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
