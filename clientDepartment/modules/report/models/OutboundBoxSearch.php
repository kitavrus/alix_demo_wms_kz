<?php

namespace app\modules\report\models;

use common\modules\inbound\models\InboundOrder;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\stock\models\Stock;
use yii\db\Query;
use yii\helpers\VarDumper;
use common\modules\outbound\models\OutboundOrder;
use yii\data\ArrayDataProvider;
use common\modules\client\models\ClientEmployees;

/**
 * StockSearch represents the model behind the search form about `common\modules\stock\models\Stock`.
 */
class OutboundBoxSearch extends Stock
{
    public $order_number;
    public $parent_order_number;
    public $date_left_warehouse;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'inbound_order_id', 'outbound_order_id', 'warehouse_id', 'product_id', 'condition_type', 'status', 'status_lost', 'status_availability', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_name', 'product_barcode', 'product_model', 'product_sku', 'box_barcode', 'primary_address', 'secondary_address', 'order_number', 'parent_order_number', 'date_left_warehouse'], 'safe'],
        ];
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

        if ($this->order_number || $this->parent_order_number) {
            $subQ1 = (new Query())
                ->select('id')
                ->from(InboundOrder::tableName())
                ->where(['deleted' => InboundOrder::NOT_SHOW_DELETED])
                ->andFilterWhere(['like', 'order_number', $this->order_number])
                ->andFilterWhere(['like', 'parent_order_number', $this->parent_order_number]);

            $subQ2 = (new Query())
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

        $relatedAlias = OutboundOrder::tableName();
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $query = Stock::find()->select('stock.id, stock.outbound_order_id, box_barcode, stock.client_id, stock.status, box_size_m3,
                                        ' . $relatedAlias . '.to_point_id, ' . $relatedAlias . '.parent_order_number,
                                        ' . $relatedAlias . '.order_number, count(box_barcode) as product_qty'
        )   ->andWhere(['stock.client_id'=>$clientEmploy->client_id])
            ->joinWith('outboundOrder')
            ->groupBy('box_barcode');

        if (!($this->load($params) && $this->validate())) {
            $dataProvider = $dataProvider = new ArrayDataProvider([
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

        $query->andFilterWhere([
            'stock.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', $relatedAlias . '.parent_order_number', $this->parent_order_number])
            ->andFilterWhere(['like', $relatedAlias . '.order_number', $this->order_number]);

        // DELIVERY DATE
        if(!empty($this->date_left_warehouse)) {
            $date = explode('/',$this->date_left_warehouse);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', $relatedAlias . '.date_left_warehouse', strtotime($date[0]),strtotime($date[1])]);
        }
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
     * @return ArrayDataProvider
     */
    public function searchProductInBoxArray($outboundOrderId)
    {
        $relatedAlias = OutboundOrder::tableName();
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);

        $query = Stock::find()->select('stock.id, stock.outbound_order_id, box_barcode, stock.client_id, stock.status, box_size_m3, box_kg,
                                        ' . $relatedAlias . '.to_point_id, ' . $relatedAlias . '.parent_order_number,
                                        ' . $relatedAlias . '.order_number, count(box_barcode) as product_qty, stock.product_model, stock.product_name '
        )   ->andWhere(['stock.client_id'=>$clientEmploy->client_id])
            ->andWhere(['outbound_order_id'=>$outboundOrderId])
            ->joinWith('outboundOrder')
            ->groupBy('box_barcode');

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'sort' => [
                'attributes' => ['id'],
            ],
            'pagination' => [
                'pageSize' => 25000,
            ],
        ]);

        return $dataProvider;
    }
}
