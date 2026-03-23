<?php

namespace app\modules\stock\models;

use common\modules\inbound\models\InboundOrder;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\stock\models\Stock;
use yii\db\Query;
use yii\helpers\VarDumper;
use common\modules\outbound\models\OutboundOrder;
use yii\data\ArrayDataProvider;

/**
 * StockSearch represents the model behind the search form about `common\modules\stock\models\Stock`.
 */
class StockSearch extends Stock
{
    public $order_number;
    public $parent_order_number;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address_sort_order','id', 'inbound_order_id', 'outbound_order_id', 'client_id', 'warehouse_id', 'product_id', 'condition_type', 'status', 'status_lost', 'status_availability', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_name', 'product_barcode', 'product_model', 'product_sku', 'box_barcode', 'primary_address', 'secondary_address', 'order_number', 'parent_order_number'], 'safe'],
			 [['inbound_client_box'], 'string'],
			 
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
        $query = Stock::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 101,
            ],
            'sort' => [
                // Set the default sort by name ASC and created_at DESC.
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'inbound_order_id' => $this->inbound_order_id,
            'outbound_order_id' => $this->outbound_order_id,
            'warehouse_id' => $this->warehouse_id,
            'product_id' => $this->product_id,
            'condition_type' => $this->condition_type,
            'status' => $this->status,
            'status_availability' => $this->status_availability,
            'status_lost' => $this->status_lost,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

          $query->andFilterWhere(['like', 'product_name', $this->product_name])
                ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
                ->andFilterWhere(['like', 'product_model', $this->product_model])
                ->andFilterWhere(['like', 'product_sku', $this->product_sku])
                ->andFilterWhere(['like', 'box_barcode', $this->box_barcode])
                ->andFilterWhere(['like', 'primary_address', $this->primary_address])
                ->andFilterWhere(['like', 'secondary_address', $this->secondary_address]);

        if($this->order_number || $this->parent_order_number){
            $subQ1 =(new Query())
                ->select('id')
                ->from(InboundOrder::tableName())
                ->where(['deleted' => InboundOrder::NOT_SHOW_DELETED])
                ->andFilterWhere(['like', 'order_number', $this->order_number])
                ->andFilterWhere(['like', 'parent_order_number', $this->parent_order_number]);

            $subQ2 =(new Query())
                ->select('id')
                ->from(OutboundOrder::tableName())
                ->where(['deleted' => OutboundOrder::NOT_SHOW_DELETED])
                ->andFilterWhere(['like', 'order_number', $this->order_number])
                ->andFilterWhere(['like', 'parent_order_number', $this->parent_order_number]);

            $query->andWhere(['in', 'inbound_order_id', $subQ1])
                ->orWhere(['in', 'outbound_order_id', $subQ2]);
        }


        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ArrayDataProvider
     */
    public function searchArray($params)
    {
        $query = Stock::find()->select('inbound_client_box, inventory_id,inventory_primary_address, inventory_secondary_address, id, product_barcode, primary_address, secondary_address, status_availability, status_lost, product_model, condition_type, status, count(id) as qty');

        if (!($this->load($params) && $this->validate())) {
            $query->where('0=1');
        }

        $query->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'product_model', $this->product_model])
            ->andFilterWhere(['like', 'product_sku', $this->product_sku])
            ->andFilterWhere(['like', 'box_barcode', $this->box_barcode])
            ->andFilterWhere(['like', 'primary_address', $this->primary_address])
			->andFilterWhere(['like', 'inbound_client_box', $this->inbound_client_box])
            ->andFilterWhere(['like', 'secondary_address', $this->secondary_address]);

        $query->groupBy('product_barcode, primary_address, secondary_address, status_availability, status, condition_type');
        $query->orderBy([
                    'address_sort_order'=>SORT_ASC,
                    'primary_address'=>SORT_DESC
                ]);


        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
//            'sort' => [
//                // Set the default sort by name ASC and created_at DESC.
//                'defaultOrder' => [
//                    'address_sort_order'=>SORT_ASC,
//                ]
//            ],
//            'sort' => [
//                'defaultOrder' => [
//                    'address_sort_order'=>SORT_ASC,
//                    //'primary_address'=>SORT_DESC
//                ],
//            ],
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ArrayDataProvider
     */
    public function searchForItems($params)
    {
        $query = Stock::find()->select('id, product_barcode, primary_address, secondary_address, status_availability, status_lost, product_model, condition_type, status, count(id) as qty');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                // Set the default sort by name ASC and created_at DESC.
                'defaultOrder' => [
                  'address_sort_order'=>SORT_ASC,
                  'primary_address'=>SORT_DESC
                ]
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->where('0=1');
        }

        $query->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'product_model', $this->product_model])
            ->andFilterWhere(['like', 'product_sku', $this->product_sku])
            ->andFilterWhere(['like', 'box_barcode', $this->box_barcode])
            ->andFilterWhere(['like', 'primary_address', $this->primary_address])
            ->andFilterWhere(['like', 'secondary_address', $this->secondary_address]);

        $query->groupBy('product_barcode, primary_address, secondary_address, status_availability, status');
        $query->asArray();
        $query->orderBy([
            'address_sort_order'=>SORT_ASC,
            'primary_address'=>SORT_DESC
        ]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ArrayDataProvider
     */
    public function searchHistoryArray($params)
    {
        $query = Stock::find()->select('id, product_barcode, product_model, condition_type, count(product_barcode) as qty, outbound_order_id, inbound_order_id');
        //$clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);

        $this->load($params);

//        $query->andFilterWhere([
//            'client_id' => $clientEmploy->client_id,
////            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
//        ]);

        if(!empty($this->outbound_order_id)) {
            if($o = \common\modules\outbound\models\OutboundOrder::find()->where(['order_number'=>$this->outbound_order_id])->one()) {
                $query->andWhere(['outbound_order_id'=>$o->id]);
            }
        }

        if(!empty($this->inbound_order_id)) {
            if($o = \common\modules\inbound\models\InboundOrder::find()->where(['order_number'=>$this->inbound_order_id])->one()) {
                $query->andWhere(['inbound_order_id'=>$o->id]);
            }
        }


        $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);
        $query->andFilterWhere(['like', 'product_model', $this->product_model]);

        $query->groupBy('product_barcode, outbound_order_id, inbound_order_id');
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'sort' => [
                'attributes' => ['id'],
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $dataProvider;
    }
	
	
    public function searchWhereFromBox($params) {

//        $query = Stock::find()->select('product_barcode as product_barcode_count, product_barcode,  outbound_order_id,outbound_picking_list_barcode,primary_address, secondary_address');
        $query = Stock::find()->select('count(product_barcode) as product_barcode_count, product_barcode,  outbound_order_id,outbound_picking_list_barcode,primary_address, secondary_address');

        if (!($this->load($params) && $this->validate())) {
            $query->where('0=1');
        }

//        $query->andFilterWhere(['like', 'primary_address', $this->primary_address]);
        $query->andFilterWhere(['primary_address'=>$this->primary_address]);
        $query->groupBy('product_barcode, outbound_order_id');
        //$query->orderBy([
          //  'outbound_order_id'=>SORT_ASC,
        //]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort' => [
//                'attributes' => ['id'],
//            ],
            'pagination' => [
                'pageSize' => 100,
            ],

        ]);

        return $dataProvider;
    }
	
	    public function searchPlacementData($id, $params)
    {
        $query = Stock::find()
            ->select([
                'id',
                'secondary_address',
                'primary_address',
                'product_barcode',
                'COUNT(product_barcode) as qty'
            ])
            ->andWhere(['inbound_order_id' => $id])
            ->groupBy(['secondary_address', 'primary_address', 'product_barcode'])
            ->orderBy('address_sort_order,primary_address')
            ->asArray();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        $query->andFilterWhere(['like', 'primary_address', $this->primary_address])
            ->andFilterWhere(['like', 'secondary_address', $this->secondary_address])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);

        return $dataProvider;
    }
	
    public function searchDefect($params)
    {
        $query = Stock::find()
            ->select([
                'inventory_secondary_address',
                'inventory_primary_address',
                'inventory_id',
                'system_status_description',
                'inbound_order_id',
                'client_id',
                'id',
                'product_barcode',
                'primary_address',
                'product_sku',
                'secondary_address',
                'status_availability',
                'status_lost',
                'product_model',
                'condition_type',
                'status',
                'count(id) as qty'
            ])
            ->andWhere(['in', 'condition_type', [3, 4]]);

        $query->groupBy('product_barcode, primary_address, secondary_address, condition_type');
        $query->orderBy([
            'address_sort_order' => SORT_ASC,
            'primary_address' => SORT_DESC
        ]);

        if ($this->load($params) && $this->validate()) {
            if ($this->secondary_address) {
                $query->andFilterWhere(['like', 'secondary_address', $this->secondary_address]);
            }

            if ($this->primary_address) {
                $query->andFilterWhere(['like', 'primary_address', $this->primary_address]);
            }

            if ($this->product_barcode) {
                $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);
            }
        }

        return new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);
    }
	
	 public function searchDefect_OLD($params)
    {
        $query = Stock::find()
            ->select([
                'inventory_secondary_address',
                'inventory_primary_address',
                'inventory_id',
                'system_status_description',
                'inbound_order_id',
                'client_id',
                'id',
                'product_barcode',
                'primary_address',
                'product_sku',
                'secondary_address',
                'status_availability',
                'status_lost',
                'product_model',
                'condition_type',
                'status',
                'count(id) as qty'
            ])
            ->where(['in', 'condition_type', [3, 4]])
            ->andWhere(['deleted' => 0]);

        $query->groupBy('product_barcode, primary_address, secondary_address, condition_type');
        $query->orderBy([
            'address_sort_order' => SORT_ASC,
            'primary_address' => SORT_DESC
        ]);

        if ($this->load($params) && $this->validate()) {
            if ($this->secondary_address) {
                $query->andFilterWhere(['like', 'secondary_address', $this->secondary_address]);
            }

            if ($this->primary_address) {
                $query->andFilterWhere(['like', 'primary_address', $this->primary_address]);
            }

            if ($this->product_barcode) {
                $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);
            }
        }

        return new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);
    }
	
	
}