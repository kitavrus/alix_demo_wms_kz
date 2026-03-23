<?php

namespace app\modules\returnOrder\models;
use common\modules\inbound\models\InboundOrder;
use common\modules\stock\models\Stock;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use yii\data\ArrayDataProvider;
use yii\helpers\VarDumper;

/**
 * InboundOrderSearch represents the model behind the search form about `common\modules\inbound\models\InboundOrder`.
 */
class ReturnOrderSearch extends ReturnOrder
{
    public $extra1 = '';
    public $extra2 = '';
    public $extra3 = '';
	public $clientBoxBarcode = '';
		
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'warehouse_id', 'status', 'expected_qty', 'accepted_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'updated_at', 'deleted'], 'integer'],
            [['order_number','extra1','extra2','extra3'], 'string'],
            [['created_at'], 'safe'],
			[['clientBoxBarcode'], 'safe'],
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
        $query = ReturnOrder::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'warehouse_id' => $this->warehouse_id,
            'status' => $this->status,
            'expected_qty' => $this->expected_qty,
            'accepted_qty' => $this->accepted_qty,
            'begin_datetime' => $this->begin_datetime,
            'end_datetime' => $this->end_datetime,
        ]);

        $query->andFilterWhere(['like', 'order_number', $this->order_number]);

        if(!empty($this->created_at)) {
            $date = explode('/',$this->created_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'created_at', strtotime($date[0]),strtotime($date[1])]);
        }

        if(!empty($this->extra1)) {
            if($inboundIds = Stock::find()->select('inbound_order_id')->andWhere(['product_barcode'=>$this->extra1])->column()) {
               if($inboundOrders = InboundOrder::find()->select('order_number')->andWhere(['id'=>$inboundIds])->column()) {
                   $query->andWhere(['order_number'=>$inboundOrders]);
               } else {
                   $query->andWhere(['order_number'=>-1]);
               }
            } else {
                $query->andWhere(['order_number'=>-1]);
            }
        }
		
		if(!empty($this->clientBoxBarcode)) {
			$returnOrderIds = ReturnOrderItems::find()
											  ->select('return_order_id')
											  ->andWhere(['client_box_barcode'=>$this->clientBoxBarcode])
											  ->column();

			if($returnOrderIds) {
			   $query->andWhere(['id'=>$returnOrderIds]);
		    } else {
			   $query->andWhere(['id'=>-1]);
		    }
        }


        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchArray($params)
    {
        $query = ReturnOrder::find();

        $dataProvider = new ArrayDataProvider();
        $this->load($params);

        if (!$this->validate()) {
            $dataProvider->setModels([]);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'warehouse_id' => $this->warehouse_id,
            'status' => $this->status,
            'expected_qty' => $this->expected_qty,
            'accepted_qty' => $this->accepted_qty,
            'begin_datetime' => $this->begin_datetime,
            'end_datetime' => $this->end_datetime,
        ]);

        $query->andFilterWhere(['like', 'order_number', $this->order_number]);

        if(!empty($this->created_at)) {
            $date = explode('/',$this->created_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'created_at', strtotime($date[0]),strtotime($date[1])]);
        }

        if(!empty($this->extra1)) {
            if($inboundIds = Stock::find()->select('inbound_order_id')->andWhere(['product_barcode'=>$this->extra1])->column()) {
                if($inboundOrders = InboundOrder::find()->select('order_number')->andWhere(['id'=>$inboundIds])->column()) {
                    $query->andWhere(['order_number'=>$inboundOrders]);
                } else {
                    $query->andWhere(['order_number'=>-1]);
                }
            } else {
                $query->andWhere(['order_number'=>-1]);
            }
        }

        $allModels = $query->asArray()->all();
        $dataProvider->setModels($allModels);

        return $dataProvider;
    }
}
