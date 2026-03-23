<?php

namespace common\b2b\domains\outboundLogitrans\repository;

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
class OutboundOrderSearch extends  \stockDepartment\modules\outbound\models\OutboundOrderSearch
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'supplier_id', 'warehouse_id',  'order_type', 'status', 'expected_qty', 'accepted_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['parent_order_number','order_number'], 'string']
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

//        $query->select('*, "SK" as scanned');

        $dataProvider = new ActiveDataProvider([
//        $dataProvider = new ArrayDataProvider([
            'query' => $query,
//            'allModels' => $query->all(),
            'pagination'=>false,
//            'pagination' => [
//                'pageSize' => 25,
//            ],
            'sort'=> false, // ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

//        VarDumper::dump($params,10,true);
//        die('--------STOP------');

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

//        VarDumper::dump($this->client_id,10,true);
//        die;

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
//            'parent_order_number' => $this->parent_order_number,
            'order_number' => $this->order_number,
            'status' => $this->status,
        ]);
        $query->andWhere(['parent_order_number' => $this->parent_order_number]);
//        $query->andWhere(['like', 'parent_order_number', $this->parent_order_number]);

//        $query->andFilterWhere(['like', 'comment', $this->comment]);



        return $dataProvider;
    }
}
