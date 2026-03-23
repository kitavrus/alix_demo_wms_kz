<?php

namespace stockDepartment\modules\outbound\models;

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
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class OutboundCarListSearch extends OutboundOrder
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'supplier_id', 'warehouse_id',  'order_type', 'status', 'expected_qty', 'accepted_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['parent_order_number','order_number'], 'string'],
            [['date_left_warehouse',], 'safe'],
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
        $query->andWhere(['cargo_status' => [self::CARGO_STATUS_IN_PROCESSING,self::CARGO_STATUS_NEW]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination'=>false,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'parent_order_number' => $this->parent_order_number,
            'order_number' => $this->order_number,

        ]);

        // DELIVERY DATE
        if(!empty($this->date_left_warehouse)) {
            $date = explode('/',$this->date_left_warehouse);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'date_left_warehouse', strtotime($date[0]),strtotime($date[1])]);
        }

        return $dataProvider;
    }
}
