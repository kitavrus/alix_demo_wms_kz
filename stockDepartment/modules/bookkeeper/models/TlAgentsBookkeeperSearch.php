<?php
namespace stockDepartment\modules\bookkeeper\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use stockDepartment\modules\bookkeeper\models\TlAgentsBookkeeper;

/**
 * TlAgentsBookkeeperSearch represents the model behind the search form about `app\models\TlAgentsBookkeeper`.
 */
class TlAgentsBookkeeperSearch extends TlAgentsBookkeeper
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'agent_id', 'status', 'date_of_invoice', 'payment_date_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['name', 'description', 'month_from', 'month_to'], 'safe'],
            [['invoice'], 'number'],
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
        $query = TlAgentsBookkeeper::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'agent_id' => $this->agent_id,
            'invoice' => $this->invoice,
            'status' => $this->status,
            'date_of_invoice' => $this->date_of_invoice,
            'payment_date_invoice' => $this->payment_date_invoice,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'month_from', $this->month_from])
            ->andFilterWhere(['like', 'month_to', $this->month_to]);

        return $dataProvider;
    }
}
