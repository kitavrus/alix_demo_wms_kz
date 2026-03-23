<?php

namespace app\modules\ecommerce\controllers\intermode\stock\domain\entities;

use app\modules\ecommerce\controllers\intermode\barcode\domain\service\BarcodeService;
use app\modules\ecommerce\controllers\intermode\stock\domain\constants\StockAvailability;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutboundItem;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * EcommerceOutboundSearch represents the model behind the search form of `common\ecommerce\entities\EcommerceStock`.
 */
class EcommerceStockSearch extends EcommerceStock
{
    public $qty;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status_outbound','status_availability','client_id','condition_type'], 'integer'],
            [['product_barcode','box_address_barcode','place_address_barcode'], 'string'],
            [['reason_re_reserved','order_re_reserved'], 'string'],
			[['scan_out_datetime'], 'string'],
            [['transfer_id'], 'string'],
			[['client_product_sku'], 'string'],
			[['product_model'], 'string'],
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

    public function searchArray($params)
    {
        $query = self::find()->select('
            client_id,
            condition_type,
            product_barcode,
            box_address_barcode,
            place_address_barcode,
            client_product_sku,
            count(id) as qty,
            inventory_id,
            inventory_box_address_barcode,
            inventory_place_address_barcode,
            '
        );

        if (!($this->load($params) && $this->validate())) {
            $query->where('0=1');
        }
		
		if(!empty($this->product_model)) {
			$productSku = EcommerceOutboundItem::find()->select('product_sku')->andWhere(['like', 'product_model', $this->product_model])->scalar();
			$this->client_product_sku = $productSku;
		}

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'condition_type' => $this->condition_type,
            'status_availability' => StockAvailability::YES,
			'client_product_sku' => $this->client_product_sku,
        ]);


         $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'box_address_barcode', $this->box_address_barcode])
            ->andFilterWhere(['like', 'place_address_barcode', $this->place_address_barcode]);

         $query->groupBy('product_barcode, box_address_barcode, place_address_barcode, condition_type');

        $query->orderBy([
//            'address_sort_order'=>SORT_ASC,
            'place_address_barcode'=>SORT_DESC
        ]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
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
        $query = EcommerceStock::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'client_id' => $this->client_id,
            'responsible_delivery_id' => $this->responsible_delivery_id,
            'expected_qty' => $this->expected_qty,
            'allocated_qty' => $this->allocated_qty,
            'accepted_qty' => $this->accepted_qty,
            'place_expected_qty' => $this->place_expected_qty,
            'place_accepted_qty' => $this->place_accepted_qty,
            'mc' => $this->mc,
            'kg' => $this->kg,
            'status' => $this->status,
            'elevator' => $this->elevator,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'data_created_on_client' => $this->data_created_on_client,
            'print_picking_list_date' => $this->print_picking_list_date,
            'begin_datetime' => $this->begin_datetime,
            'end_datetime' => $this->end_datetime,
            'packing_date' => $this->packing_date,
            'date_left_warehouse' => $this->date_left_warehouse,
            'date_delivered_to_customer' => $this->date_delivered_to_customer,
            'client_Priority' => $this->client_Priority,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'order_number', $this->order_number])
            ->andFilterWhere(['like', 'external_order_number', $this->external_order_number])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'phone_mobile1', $this->phone_mobile1])
            ->andFilterWhere(['like', 'phone_mobile2', $this->phone_mobile2])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'zip_code', $this->zip_code])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'house', $this->house])
            ->andFilterWhere(['like', 'building', $this->building])
            ->andFilterWhere(['like', 'entrance', $this->entrance])
            ->andFilterWhere(['like', 'flat', $this->flat])
            ->andFilterWhere(['like', 'intercom', $this->intercom])
            ->andFilterWhere(['like', 'floor', $this->floor])
            ->andFilterWhere(['like', 'customer_address', $this->customer_address])
            ->andFilterWhere(['like', 'customer_comment', $this->customer_comment])
            ->andFilterWhere(['like', 'ttn', $this->ttn])
            ->andFilterWhere(['like', 'client_CargoCompany', $this->client_CargoCompany])
            ->andFilterWhere(['like', 'client_ShippingCountryCode', $this->client_ShippingCountryCode])
            ->andFilterWhere(['like', 'client_ShippingCity', $this->client_ShippingCity])
            ->andFilterWhere(['like', 'client_PackMessage', $this->client_PackMessage])
            ->andFilterWhere(['like', 'client_GiftWrappingMessage', $this->client_GiftWrappingMessage]);

        return $dataProvider;
    }


    public function searchFindProductOnStock($params)
    {
        $query = self::find()->select('
            client_id,
            condition_type,
            product_barcode,
            box_address_barcode,
            place_address_barcode,
            outbound_id,
            status_outbound,
            status_availability,
            count(id) as qty'
        );

        if (!($this->load($params) && $this->validate())) {
            $query->where('0=1');
        }
		
		if(!empty($this->product_model)) {
			$productSku = EcommerceOutboundItem::find()->select('product_sku')->andWhere(['like', 'product_model', $this->product_model])->scalar();
			$this->client_product_sku = $productSku;
		}

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'condition_type' => $this->condition_type,
            'status_availability' => $this->status_availability,
            'status_outbound' => $this->status_outbound,
			'client_product_sku' => $this->client_product_sku,
        ]);


        $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'box_address_barcode', $this->box_address_barcode])
            ->andFilterWhere(['like', 'place_address_barcode', $this->place_address_barcode]);

        $query->groupBy('product_barcode, box_address_barcode, place_address_barcode, condition_type, status_availability, status_outbound');

        $query->orderBy([
//            'address_sort_order'=>SORT_ASC,
            'status_availability'=>SORT_ASC,
            'place_address_barcode'=>SORT_DESC
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'allModels' => $query->asArray()->all(),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchFindDamageProductOnStock($params)
    {
        $query = EcommerceStock::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $query->andWhere([
            'reason_re_reserved' => array_keys((new \common\ecommerce\constants\OutboundCancelStatus())->getForPartReReservedList()),
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,

            'box_address_barcode' => $this->box_address_barcode,
            'place_address_barcode' => $this->place_address_barcode,
            'condition_type' => $this->condition_type,
            'product_barcode' => $this->product_barcode,
            'order_re_reserved' => $this->order_re_reserved,

            'reason_re_reserved' => $this->reason_re_reserved,

            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'order_re_reserved', $this->order_re_reserved]);

        return $dataProvider;
    }
	
	public function searchCheckOnStock($params)
	{
		$query = self::find()->select([
//			'SQL_CALC_FOUND_ROWS',
		'place_address_barcode',
		'box_address_barcode',
		'product_barcode',
		'COUNT(product_barcode) as qty',
		'client_product_sku'
		]);

		if (!($this->load($params) && $this->validate())) {
			$query->where('0=1');
		}

		$query->andWhere([
			'client_id' => 2,
		]);

		if (!empty($this->client_product_sku)) {
			$clientProductSkuList = explode(',', $this->client_product_sku);
			if (is_array($clientProductSkuList)) {

				$clientProductSkuList = array_map(function($item) {
					$item = BarcodeService::trim($item);
					return BarcodeService::onlyDigital($item);
					},$clientProductSkuList);
				$clientProductSkuList = array_filter($clientProductSkuList);
				$query->andFilterWhere(['IN', 'client_product_sku', $clientProductSkuList]);
			}
		}

		$query->groupBy('`place_address_barcode`, `box_address_barcode`, `product_barcode`, `client_product_sku`');

//		$query->orderBy([
//			'box_address_barcode'=>SORT_ASC,
//			'place_address_barcode'=>SORT_ASC
//		]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
//            'allModels' => $query->asArray()->all(),
			'pagination' => [
				'pageSize' => 50,
			],
		]);

		return $dataProvider;
	}
	
}
