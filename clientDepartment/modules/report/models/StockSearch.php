<?php

namespace app\modules\report\models;

use common\modules\inbound\models\InboundOrder;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use common\modules\stock\models\Stock;
use yii\db\Query;
use yii\helpers\VarDumper;
use common\modules\outbound\models\OutboundOrder;
use common\modules\client\models\ClientEmployees;

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
            [['id', 'warehouse_id', 'product_id', 'condition_type', 'status', 'status_lost', 'status_availability', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['inbound_order_id', 'outbound_order_id'], 'string'],
            [['product_name', 'product_barcode', 'product_model', 'product_sku', 'box_barcode', 'primary_address', 'secondary_address', 'order_number', 'parent_order_number', 'qty'], 'safe'],
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
        $query = Stock::find()->select('id, product_barcode, product_model, condition_type, count(product_barcode) as qty');
        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);

//        if (!($this->load($params) && $this->validate())) {
//            return new ArrayDataProvider();
//        }
        if (!($this->load($params) && $this->validate())) {
            $query->andWhere([
                'id' => -1,
            ]);
        }

        $query->andFilterWhere([
            'client_id' => $clientEmploy->client_id,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
            'condition_type' => $this->condition_type,
       ]);

        $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);
        $query->andFilterWhere(['like', 'product_model', $this->product_model]);

        $query->groupBy('product_barcode,condition_type');
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'sort' => [
                'attributes' => ['id'],
            ],
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @param string $params
     * @return ArrayDataProvider
     */
    public function searchBoxesArray($params)
    {
        $query = Stock::find()->select('id, product_barcode, product_model, primary_address, secondary_address, count(product_barcode) as qty, product_sku');
        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);

        $this->load($params);

        $query->andFilterWhere([
            'client_id' => $clientEmploy->client_id,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
       ]);

        $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);
        $query->andFilterWhere(['like', 'product_model', $this->product_model]);

//        $query->groupBy('primary_address');
        $query->groupBy('primary_address, secondary_address, product_barcode');
        $query->orderBy('primary_address, secondary_address, product_barcode');

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'sort' => false,
            'pagination'=>false,
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
        $query = Stock::find()->select('id, product_barcode, product_model, condition_type, count(product_barcode) as qty, outbound_order_id, inbound_order_id, ecom_outbound_id');
        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);

//        if (!($this->load($params) && $this->validate())) {
//            return new ArrayDataProvider();
//        }

        $this->load($params);

        $query->andFilterWhere([
            'client_id' => $clientEmploy->client_id,
//            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
       ]);

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
		
		// Фильтрация по ecom_outbound_id (екоммерс-заказы)
        if (!empty($this->ecom_outbound_id)) {
            if ($o = \common\ecommerce\entities\EcommerceOutbound::find()->where(['ecom_outbound_id' => $this->ecom_outbound_id])->one()) {
                $query->andWhere(['ecom_outbound_id' => $o->id]);
            }
        }


        $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);
        $query->andFilterWhere(['like', 'product_model', $this->product_model]);

        $query->groupBy('product_barcode, outbound_order_id, inbound_order_id, ecom_outbound_id');
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'sort' => [
                'attributes' => ['id'],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }
}
