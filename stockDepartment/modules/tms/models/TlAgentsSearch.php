<?php

namespace stockDepartment\modules\tms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlAgents;


/**
 * TlAgentsSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlAgents`.
 */
class TlAgentsSearch extends TlAgents
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'intercom', 'floor'], 'integer'],
            [['name', 'title', 'phone', 'phone_mobile', 'description', 'contact_first_name', 'contact_middle_name', 'contact_last_name', 'contact_phone', 'contact_phone_mobile', 'contact_first_name2', 'contact_middle_name2', 'contact_last_name2', 'contact_phone2', 'contact_phone_mobile2', 'address_title', 'country', 'region', 'city', 'zip_code', 'street', 'house', 'entrance', 'flat', 'comment', 'created_at', 'updated_at'], 'safe'],
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
        $query = TlAgents::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['status'=>SORT_ASC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'intercom' => $this->intercom,
            'floor' => $this->floor,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone_mobile', $this->phone_mobile])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'contact_first_name', $this->contact_first_name])
            ->andFilterWhere(['like', 'contact_middle_name', $this->contact_middle_name])
            ->andFilterWhere(['like', 'contact_last_name', $this->contact_last_name])
            ->andFilterWhere(['like', 'contact_phone', $this->contact_phone])
            ->andFilterWhere(['like', 'contact_phone_mobile', $this->contact_phone_mobile])
            ->andFilterWhere(['like', 'contact_first_name2', $this->contact_first_name2])
            ->andFilterWhere(['like', 'contact_middle_name2', $this->contact_middle_name2])
            ->andFilterWhere(['like', 'contact_last_name2', $this->contact_last_name2])
            ->andFilterWhere(['like', 'contact_phone2', $this->contact_phone2])
            ->andFilterWhere(['like', 'contact_phone_mobile2', $this->contact_phone_mobile2])
            ->andFilterWhere(['like', 'address_title', $this->address_title])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'zip_code', $this->zip_code])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'house', $this->house])
            ->andFilterWhere(['like', 'entrance', $this->entrance])
            ->andFilterWhere(['like', 'flat', $this->flat])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
