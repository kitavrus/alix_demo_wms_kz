<?php

namespace app\modules\leads\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\leads\models\ExternalClientLead;

/**
 * ExternalClientLeadSearch represents the model behind the search form about `common\modules\leads\models\ExternalClientLead`.
 */
class ExternalClientLeadSearch extends ExternalClientLead
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'client_type', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['full_name', 'phone', 'email', 'legal_company_name'], 'safe'],
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
        $query = ExternalClientLead::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'client_type' => $this->client_type,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
                ->andFilterWhere(['like', 'phone', $this->phone])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'legal_company_name', $this->legal_company_name]);

        return $dataProvider;
    }
}
