<?php

namespace clientDepartment\modules\report\models;

use common\modules\outbound\models\OutboundOrderItem;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use common\modules\outbound\models\OutboundOrder;
use yii\helpers\VarDumper;


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
 * @property string  $data_created_on_client //Дата создания заказа в системе клиента
 * @property string  $date_left_warehouse // Дата создания заказа в системе клиента
 * @property string  $date_delivered // Дата доставки заказа в точку получения
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
            [['client_id', 'supplier_id', 'warehouse_id',  'order_type', 'cargo_status', 'expected_qty', 'accepted_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'updated_at'], 'integer'],
            [['parent_order_number','order_number'], 'string'],
            [['created_at','data_created_on_client','date_left_warehouse', 'date_delivered', 'to_point_id','product_barcode'], 'safe'],
			[['api_complete_status'], 'string'],
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
        $query = OutboundOrder::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort'=>['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'parent_order_number' => $this->parent_order_number,
            'order_number' => $this->order_number,
            'cargo_status' => $this->cargo_status,
            'to_point_id' => $this->to_point_id,
        ]);

        // DELIVERY DATE
        if(!empty($this->date_left_warehouse)) {
            $date = explode('/',$this->date_left_warehouse);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'date_left_warehouse', strtotime($date[0]),strtotime($date[1])]);
        }

        // DATA CREATED ON CLIENT
        if(!empty($this->data_created_on_client)) {
            $date = explode('/',$this->data_created_on_client);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'data_created_on_client', strtotime($date[0]),strtotime($date[1])]);
        }

        // DATA CREATED ON OUR SYSTEM
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

        return $dataProvider;
    }
}
