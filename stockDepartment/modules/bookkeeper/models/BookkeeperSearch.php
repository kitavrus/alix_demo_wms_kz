<?php

namespace stockDepartment\modules\bookkeeper\models;

use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use stockDepartment\modules\bookkeeper\models\Bookkeeper;

/**
 * BookkeeperSearch represents the model behind the search form about `stockDepartment\modules\Bookkeeper\models\Bookkeeper`.
 */
class BookkeeperSearch extends Bookkeeper
{
    public $to_point_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['to_point_id','expenses_type_id','id', 'tl_delivery_proposal_id', 'tl_delivery_proposal_route_unforeseen_expenses_id', 'department_id', 'doc_type_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['doc_file', 'name_supplier', 'description', 'date_at'], 'safe'],
//            [['plus_sum', 'minus_sum', 'balance_sum'], 'number'],
            [['price', 'balance_sum'], 'number'],
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
        $query = Bookkeeper::find();

        $query->with('deliveryProposal','deliveryProposalUnforeseenExpenses');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => ['sort_order'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'expenses_type_id' => $this->expenses_type_id,
            'tl_delivery_proposal_id' => $this->tl_delivery_proposal_id,
            'tl_delivery_proposal_route_unforeseen_expenses_id' => $this->tl_delivery_proposal_route_unforeseen_expenses_id,
            'department_id' => $this->department_id,
            'doc_type_id' => $this->doc_type_id,
            'status' => $this->status,
        ]);

        if(!empty($this->date_at)) {
            $date = explode('/',$this->date_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'date_at', strtotime($date[0]),strtotime($date[1])]);
        }

        if(!empty($this->to_point_id)) {
            $ids = TlDeliveryProposal::find()->select('id')->andWhere(['route_to'=>$this->to_point_id])->column();
            if($ids) {
                $query->andWhere(['tl_delivery_proposal_id'=>$ids]);
            } else {
                $query->andWhere(['tl_delivery_proposal_id'=>-1]);
            }
        }

        return $dataProvider;
    }
}