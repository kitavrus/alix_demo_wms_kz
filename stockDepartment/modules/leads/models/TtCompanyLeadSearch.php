<?php

namespace app\modules\leads\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\leads\models\TtCompanyLead;

/**
 * TtCompanyLeadSearch represents the model behind the search form about `common\modules\leads\models\TtCompanyLead`.
 */
class TtCompanyLeadSearch extends TtCompanyLead
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'cooperation_type_1', 'cooperation_type_2', 'cooperation_type_3', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['customer_name', 'customer_company_name', 'customer_position', 'customer_phone', 'customer_email', 'customer_comment'], 'safe'],
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
        $query = TtCompanyLead::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'cooperation_type_1' => $this->cooperation_type_1,
            'cooperation_type_2' => $this->cooperation_type_2,
            'cooperation_type_3' => $this->cooperation_type_3,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'customer_company_name', $this->customer_company_name])
            ->andFilterWhere(['like', 'customer_position', $this->customer_position])
            ->andFilterWhere(['like', 'customer_phone', $this->customer_phone])
            ->andFilterWhere(['like', 'customer_email', $this->customer_email])
            ->andFilterWhere(['like', 'customer_comment', $this->customer_comment]);

        return $dataProvider;
    }
}
