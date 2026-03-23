<?php

namespace stockDepartment\modules\inbound\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\inbound\models\InboundOrderItem;

/**
 * InboundOrderSearch represents the model behind the search form about `common\modules\inbound\models\InboundOrder`.
 */
class InboundOrderItemSearch extends InboundOrderItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['id', 'client_id', 'supplier_id', 'warehouse_id', 'order_type', 'status', 'expected_qty', 'accepted_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'updated_at', 'deleted'], 'integer'],
            [['product_model', 'product_barcode'], 'safe'],
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
        $query = InboundOrderItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

//        $query->andFilterWhere([
//            'id' => $this->id,
//            'client_id' => $this->client_id,
//            'supplier_id' => $this->supplier_id,
//            'warehouse_id' => $this->warehouse_id,
//            'order_type' => $this->order_type,
//            'status' => $this->status,
//            'expected_qty' => $this->expected_qty,
//            'accepted_qty' => $this->accepted_qty,
//            'accepted_number_places_qty' => $this->accepted_number_places_qty,
//            'expected_number_places_qty' => $this->expected_number_places_qty,
//            'begin_datetime' => $this->begin_datetime,
//            'end_datetime' => $this->end_datetime,
//        ]);

        $query->andFilterWhere(['like', 'product_model', $this->product_model]);
        $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);

//        if(!empty($this->created_at)) {
//            $date = explode('/',$this->created_at);
//            $date[0] = trim($date[0]);
//            $date[1] = trim($date[1]);
//
//            $query->andWhere(['between', 'created_at', strtotime($date[0]),strtotime($date[1])]);
//        }
//
//        if(!empty($this->expected_datetime)) {
//            $date = explode('/',$this->expected_datetime);
//            $date[0] = trim($date[0]);
//            $date[1] = trim($date[1]);
//
//            $query->andWhere(['between', 'expected_datetime', strtotime($date[0]),strtotime($date[1])]);
//        }

        return $dataProvider;
    }
}
