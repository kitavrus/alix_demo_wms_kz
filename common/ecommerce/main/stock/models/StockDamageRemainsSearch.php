<?php

namespace common\ecommerce\main\stock\models;

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
class StockDamageRemainsSearch extends Stock
{
    public $client_id;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

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
    * @param array $params
    * @return ArrayDataProvider
    */
    public function searchArray($params)
    {
        $query = Stock::find()->select('id,
        client_id,
        product_barcode,
        primary_address,
        secondary_address,
        condition_type'); // ,count(id) as qty

        if (!($this->load($params) && $this->validate())) {
            $query->where('0=1');
        }

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'condition_type' => [
                Stock::CONDITION_TYPE_PARTIAL_DAMAGED,
                Stock::CONDITION_TYPE_FULL_DAMAGED,
            ],
            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
        ]);

        $query->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'primary_address', $this->primary_address])
            ->andFilterWhere(['like', 'secondary_address', $this->secondary_address]);


//        $query->groupBy('condition_type');
//        $query->groupBy('product_barcode, primary_address, secondary_address, condition_type');

        $query->orderBy([
            'address_sort_order'=>SORT_ASC,
            'primary_address'=>SORT_DESC
        ]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $dataProvider;
    }
}
