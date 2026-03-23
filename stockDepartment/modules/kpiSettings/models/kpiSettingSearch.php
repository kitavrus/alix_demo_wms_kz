<?php

namespace stockDepartment\modules\kpiSettings\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\kpiSettings\models\KpiSetting;

/**
 * KpiSettingSearch represents the model behind the search form about `common\modules\kpiSettings\models\KpiSetting`.
 */
class kpiSettingSearch extends KpiSetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'operation_type', 'one_item_time', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
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
        $query = KpiSetting::find();

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
            'client_id' => $this->client_id,
            'operation_type' => $this->operation_type,
            'one_item_time' => $this->one_item_time,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        return $dataProvider;
    }
}
