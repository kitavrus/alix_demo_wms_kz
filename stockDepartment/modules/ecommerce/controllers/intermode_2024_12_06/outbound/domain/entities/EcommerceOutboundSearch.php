<?php

namespace app\modules\ecommerce\controllers\intermode\outbound\domain\entities;

use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\OutboundStatus;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * EcommerceOutboundSearch represents the model behind the search form of `common\ecommerce\entities\EcommerceOutbound`.
 */
class EcommerceOutboundSearch extends EcommerceOutbound
{
    public $productBarcode;
    public $outboundBoxBarcode;
    public $findType;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['findType','outboundBoxBarcode','productBarcode','client_ReferenceNumber','order_number','date_left_warehouse','packing_date','created_at','updated_at'], 'safe'],
            [['client_ShipmentSource'], 'string'],
			[['external_order_number'], 'string'],
            [['customer_address'], 'string'],
            [['customer_name'], 'string'],
            [['phone_mobile1'], 'string'],
            [['city'], 'string'],
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
        $query = EcommerceOutbound::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'client_ShipmentSource' => $this->client_ShipmentSource,
        ]);

        $query->andFilterWhere(['like', 'order_number', $this->order_number]);
        $query->andFilterWhere(['like', 'client_ReferenceNumber', $this->client_ReferenceNumber]);
        $query->andFilterWhere(['like', 'external_order_number', $this->external_order_number]);
		$query->andFilterWhere(['like', 'customer_address', $this->customer_address]);
		
		$query->andFilterWhere(['like', 'customer_name', $this->customer_name]);
		$query->andFilterWhere(['like', 'phone_mobile1', $this->phone_mobile1]);
		$query->andFilterWhere(['like', 'city', $this->city]);

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

            $query->andWhere(['between', '`ecommerce_outbound`.created_at', strtotime($date[0]),strtotime($date[1])]);
        }

        if(!empty($this->updated_at)) {
            $date = explode('/',$this->updated_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'updated_at', strtotime($date[0]),strtotime($date[1])]);
        }

        if (!empty($this->productBarcode)) {
            $subQuery = EcommerceOutboundItem::find()
                ->select('outbound_id')
                ->andWhere(['like', 'product_barcode', $this->productBarcode]);
            $query->andWhere(['id' => $subQuery]);
        }

        if (!empty($this->outboundBoxBarcode)) {
            $subQuery = EcommerceStock::find()
                ->select('outbound_id')
                ->andWhere(['like', 'outbound_box', $this->outboundBoxBarcode]);
            $query->andWhere(['id' => $subQuery]);
        }

        if (!empty($this->findType))
        {
            switch($this->findType) { // 5 4 3
                case '1': // есть расхождения между expected scanned
                    $query->andWhere('expected_qty != accepted_qty');
                break;
                case '2': // есть расхождения между reserved scanned
                    $query->andWhere('allocated_qty != accepted_qty');
                break;
                case '3': // есть расхождения между reserved scanned
                    $query->andWhere('expected_qty != accepted_qty');
                    $query->andWhere('status != :status',[':status'=>OutboundStatus::CANCEL]);
                break;
                case '4': // есть расхождения между  scanned == 0
                    $query->andWhere('allocated_qty < 1');
               break;
                case '5': // есть расхождения между  scanned == 0
                    $query->andWhere('accepted_qty < 1');
               break;
			   case '6': // заказы для которых нет маленькой этикетки у каспи
				   $query->andWhere('(path_to_cargo_label_file = "" OR path_to_cargo_label_file IS NULL)');
				   $query->andWhere('client_ShipmentSource = "KaspiKazakhistan"');
				   $query->andWhere(['status'=>[OutboundStatus::PRINT_BOX_LABEL]]);
				   
				   //$this->reGetCargoLabelForKaspi();
				   
               break;
			   
            }
        }

        return $dataProvider;
    }
	
	    private function reGetCargoLabelForKaspi() {
		$q = EcommerceOutbound::find();
		$q->select('order_number');
		$q->andWhere(['client_id'=>2]); // Defacto
		$q->andWhere('(path_to_cargo_label_file = "" OR path_to_cargo_label_file IS NULL)');
		$q->andWhere('client_ShipmentSource = "KaspiKazakhistan"');
		$q->andWhere(['status'=>[OutboundStatus::PRINT_BOX_LABEL]]);


		$service = new \common\ecommerce\defacto\outbound\service\OutboundService();
		foreach($q->column() as $orderNumber) {
			//$order = $service->resendGetCargoLabel($orderNumber);
		}

	}

    public static function findTypeList() {
        return [
		  '6'=>'Без этикеток для Каспи',
          '1'=>'Ждали <> Отсканировали',
          '2'=>'Резерв <> Отсканировали',
          '3'=>'Ждали <> Отсканировали, без возврата',
          '4'=>'Резерв < 0',
          '5'=>'Отсканировали < 0',
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByDefactoOrders($params)
    {
        $query = EcommerceOutbound::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            //$query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'client_ShipmentSource' => $this->client_ShipmentSource,
        ]);
		
		$query->andFilterWhere(['like', 'external_order_number', $this->external_order_number]);


        if (!empty($this->order_number)) {
            $outboundOrders = explode(',', $this->order_number);
            if (is_array($outboundOrders)) {

                $outboundOrders = array_map(function($item) { return trim($item);},$outboundOrders);
                $outboundOrders = array_filter($outboundOrders);
                $query->andFilterWhere(['IN', 'order_number', $outboundOrders]);
            }
        }

        if (!empty($this->client_ReferenceNumber)) {
            $outboundOrders = explode(',', $this->client_ReferenceNumber);
            if (is_array($outboundOrders)) {

                $outboundOrders = array_map(function($item) { return trim($item);},$outboundOrders);
                $outboundOrders = array_filter($outboundOrders);
                $query->andFilterWhere(['IN', 'client_ReferenceNumber', $outboundOrders]);
            }
        }
//        $query->andFilterWhere(['like', 'client_ReferenceNumber', $this->client_ReferenceNumber]);


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

        if(!empty($this->updated_at)) {
            $date = explode('/',$this->updated_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'updated_at', strtotime($date[0]),strtotime($date[1])]);
        }

        if (!empty($this->productBarcode)) {
            $subQuery = EcommerceOutboundItem::find()
                ->select('outbound_id')
                ->andWhere(['like', 'product_barcode', $this->productBarcode]);
            $query->andWhere(['id' => $subQuery]);
        }

        if (!empty($this->outboundBoxBarcode)) {
            $subQuery = EcommerceStock::find()
                ->select('outbound_id')
                ->andWhere(['like', 'outbound_box', $this->outboundBoxBarcode]);
            $query->andWhere(['id' => $subQuery]);
        }

        if (!empty($this->findType))
        {
            switch($this->findType) { // 5 4 3
                case '1': // есть расхождения между expected scanned
                    $query->andWhere('expected_qty != accepted_qty');
                    break;
                case '2': // есть расхождения между reserved scanned
                    $query->andWhere('allocated_qty != accepted_qty');
                    break;
                case '3': // есть расхождения между reserved scanned
                    $query->andWhere('expected_qty != accepted_qty');
                    $query->andWhere('status != :status',[':status'=>OutboundStatus::CANCEL]);
                    break;
                case '4': // есть расхождения между  scanned == 0
                    $query->andWhere('allocated_qty < 1');
                    break;
                case '5': // есть расхождения между  scanned == 0
                    $query->andWhere('accepted_qty < 1');
                    break;
            }
        }

        return $dataProvider;
    }
}
