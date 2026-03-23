<?php

namespace common\clientObject\main\inbound\models;

use common\clientObject\constants\Constants;
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
            [['order_number', 'created_at', 'date_confirm', 'product_barcode'], 'safe'],
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
        $query->with(['client']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 55,
            ],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->andWhere([
                'client_id' =>  Constants::getCarPartClientIDs(),
            ]);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => !empty($this->client_id) ? $this->client_id : Constants::getCarPartClientIDs(),
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'order_number', $this->order_number]);

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
