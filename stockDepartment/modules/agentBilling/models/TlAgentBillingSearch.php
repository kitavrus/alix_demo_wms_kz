<?php

namespace app\modules\agentBilling\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\agentBilling\models\TlAgentBilling;

/**
 * TlAgentBillingSearch represents the model behind the search form about `common\modules\agentBilling\models\TlAgentBilling`.
 */
class TlAgentBillingSearch extends TlAgentBilling
{

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TlAgentBilling::find();

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
            'agent_id' => $this->agent_id,
            'status' => $this->status,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        return $dataProvider;
    }
}
