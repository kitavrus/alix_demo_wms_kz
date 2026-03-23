<?php

namespace clientDepartment\modules\report\models;

use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItems;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\inbound\models\InboundOrder;

/**
 * InboundOrderSearch represents the model behind the search form about `common\modules\inbound\models\InboundOrder`.
 */
class CrossDockItemSearch extends CrossDockItems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cross_dock_id', 'status', 'expected_number_places_qty', 'accepted_number_places_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['box_barcode'], 'string', 'max' => 54],
            [['box_m3','weight_net','weight_brut'], 'string', 'max' => 32],
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
        $query = CrossDockItems::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

//        $query->andFilterWhere([
//            'id' => $this->id,
//            'status' => $this->status,
//            'box_barcode' => $this->box_barcode,
//        ]);

//        $query->andFilterWhere(['like', 'order_number', $this->order_number]);

//        if(!empty($this->created_at)) {
//            $date = explode('/',$this->created_at);
//            $date[0] = trim($date[0]);
//            $date[1] = trim($date[1]);
//
//            $query->andWhere(['between', 'created_at', strtotime($date[0]),strtotime($date[1])]);
//        }

        return $dataProvider;
    }
}
