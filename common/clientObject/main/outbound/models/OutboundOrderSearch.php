<?php

namespace common\clientObject\main\outbound\models;

use common\clientObject\constants\Constants;
use common\modules\outbound\models\OutboundOrderItem;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\outbound\models\OutboundOrder;


/**
 * This is the model class for table "outbound_orders".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $supplier_id
 * @property integer $warehouse_id
 * @property integer $order_number
 * @property integer $parent_order_number
 * @property integer $order_type
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $accepted_number_places_qty
 * @property integer $expected_number_places_qty
 * @property integer $expected_datetime
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class OutboundOrderSearch extends OutboundOrder
{
    public $product_barcode;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['to_point_id','client_id', 'supplier_id', 'warehouse_id',  'order_type', 'status', 'expected_qty', 'accepted_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'updated_at'], 'integer'],
            [['parent_order_number','order_number','product_barcode'], 'string'],
            [['date_left_warehouse','packing_date','created_at'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = OutboundOrder::find();
        $query->with(['orderItems','client']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination'=>false,
            'pagination' => [
                'pageSize' => 55,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
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
            'order_number' => $this->order_number,
            'status' => $this->status,
            'to_point_id' => $this->to_point_id,
        ]);

        // DELIVERY DATE
        if(!empty($this->date_left_warehouse)) {
            $date = explode('/',$this->date_left_warehouse);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'date_left_warehouse', strtotime($date[0]),strtotime($date[1])]);
        }

        if(!empty($this->packing_date)) {
            $date = explode('/',$this->packing_date);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'packing_date', strtotime($date[0]),strtotime($date[1])]);
        }
		
		
        if(!empty($this->created_at)) {
            $date = explode('/',$this->created_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'created_at', strtotime($date[0]),strtotime($date[1])]);
        }

        if (!empty($this->product_barcode)) {
            $subQ1 = OutboundOrderItem::find()
                ->select('outbound_order_id')
                ->andWhere(['like', 'product_barcode', $this->product_barcode]);
            $query->andWhere(['id' => $subQ1]);
        }

        $query->orderBy('id');

        return $dataProvider;
    }
}