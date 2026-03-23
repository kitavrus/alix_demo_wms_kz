<?php

namespace stockDepartment\modules\inbound\models;

use common\modules\inbound\models\InboundOrderItem;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\inbound\models\InboundOrder;

/**
 * InboundOrderSearch represents the model behind the search form about `common\modules\inbound\models\InboundOrder`.
 */
class InboundOrderSearch extends InboundOrder
{
    public $product_barcode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'supplier_id', 'warehouse_id', 'order_type', 'status', 'expected_qty', 'accepted_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'updated_at', 'deleted'], 'integer'],
            [['order_number','parent_order_number', 'created_at', 'expected_datetime', 'product_barcode'], 'safe'],
			[['date_confirm'], 'safe'],
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
        $query = InboundOrder::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
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
            'supplier_id' => $this->supplier_id,
            'warehouse_id' => $this->warehouse_id,
            'order_type' => $this->order_type,
            'status' => $this->status,
            'expected_qty' => $this->expected_qty,
            'accepted_qty' => $this->accepted_qty,
            'accepted_number_places_qty' => $this->accepted_number_places_qty,
            'expected_number_places_qty' => $this->expected_number_places_qty,
            'begin_datetime' => $this->begin_datetime,
            'end_datetime' => $this->end_datetime,
        ]);

        $query->andFilterWhere(['like', 'order_number', $this->order_number]);
        $query->andFilterWhere(['like', 'parent_order_number', $this->parent_order_number]);

        if (!empty($this->created_at)) {
            $date = explode('/', $this->created_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'created_at', strtotime($date[0]), strtotime($date[1])]);
        }

        if (!empty($this->date_confirm)) {
            $date = explode('/', $this->date_confirm);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'date_confirm', strtotime($date[0]), strtotime($date[1])]);
        }

        if (!empty($this->product_barcode)) {
            $subQ1 = InboundOrderItem::find()
                ->select('inbound_order_id')
                ->andWhere(['like', 'product_barcode', $this->product_barcode]);
            $query->andWhere(['id' => $subQ1]);

        }
        return $dataProvider;
    }
}
