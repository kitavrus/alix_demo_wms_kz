<?php

namespace app\modules\report\models;

use common\modules\client\models\ClientEmployees;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
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
class StockRemains extends Stock
{
    public $order_number;
    public $parent_order_number;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['parent_order_number'] = 'Номер партии';
        $labels['parent_order_number'] = 'Номер партии';

        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','id', 'inbound_order_id', 'outbound_order_id', 'warehouse_id', 'product_id', 'condition_type', 'status', 'status_lost', 'status_availability', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['parent_order_number','product_name', 'product_barcode', 'product_model', 'product_sku', 'box_barcode', 'primary_address', 'secondary_address', 'order_number', 'parent_order_number'], 'safe'],
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
//        die('0-0-0-0');
        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
//            'inbound_order_id' => $this->inbound_order_id,
//            'outbound_order_id' => $this->outbound_order_id,
//            'warehouse_id' => $this->warehouse_id,
//            'product_id' => $this->product_id,
            'condition_type' => $this->condition_type,
            'status' => $this->status,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
//            'status_availability' => $this->status_availability,
            'status_lost' => $this->status_lost,
//            'created_user_id' => $this->created_user_id,
//            'updated_user_id' => $this->updated_user_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//            'deleted' => $this->deleted,
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
//        $detail = Yii::$app->request->get('detail');
        $query = Stock::find()->select('field_extra1, consignment_inbound_id, inbound_order_id, client_id, id, product_barcode, primary_address, secondary_address, status_availability, status_lost, product_model, condition_type, status, count(id) as qty, product_sku');
        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);

        $this->load($params);

        $query->andFilterWhere([
            'client_id' => $clientEmploy->client_id,
        ]);

//        if (!($this->load($params) && $this->validate())) {
//            $query->where('0=1');
//        }

        $query->andFilterWhere([
//            'client_id' => $this->client_id,
            'condition_type' => $this->condition_type,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
        ]);

        if(!empty($this->parent_order_number)) {
//            $consignmentInbound = ConsignmentInboundOrders::findOne(['party_number'=>$this->parent_order_number]);
            $consignmentInbound = ConsignmentInboundOrders::find()->where(['like', 'party_number', $this->parent_order_number])->one();
            if($consignmentInbound) {
//                $ids = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$consignmentInbound->id])->column();
                $query->andFilterWhere([
//                    'inbound_order_id' => empty($ids) ? -1 : $ids,
                    'consignment_inbound_id' => $consignmentInbound->id,
                ]);
            } else {
                $query->andFilterWhere([
                    'inbound_order_id' => -1,
                ]);
            }
        }

        $query->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'product_model', $this->product_model])
            ->andFilterWhere(['like', 'product_sku', $this->product_sku])
            ->andFilterWhere(['like', 'box_barcode', $this->box_barcode])
            ->andFilterWhere(['like', 'primary_address', $this->primary_address])
            ->andFilterWhere(['like', 'secondary_address', $this->secondary_address]);


//        if($detail){
//            $query->groupBy('product_barcode, primary_address, secondary_address, consignment_inbound_id');
//        } else {
            $query->groupBy('product_barcode, condition_type');
//        }

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
}