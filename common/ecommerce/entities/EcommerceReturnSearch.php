<?php

namespace common\ecommerce\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "ecommerce_return".
 *
 * @property int $id
 * @property int $outbound_id Outbound id
 * @property int $client_id Client id
 * @property string $order_number Order number
 * @property int $expected_qty Expected product qty
 * @property int $accepted_qty Accepted product qty
 * @property string $customer_name Customer full name
 * @property string $city city
 * @property string $customer_address Адрес
 * @property string $client_ReferenceNumber Cargo company ReferenceNumber
 * @property int $status Status
 * @property int $begin_datetime Begin scanning datetime
 * @property int $end_datetime End scanning datetime
 * @property int $date_confirm End scanning datetime
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 * @property string $client_ExternalShipmentId
 * @property string $client_ExternalOrderId
 * @property string $client_OrderSource
 * @property string $client_CargoReturnCode
 * @property string $client_IsRefundable
 * @property string $client_RefundableMessage
 * @property string $return_reason
 * @property string $outbound_box
 */
class EcommerceReturnSearch extends EcommerceReturn
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['outbound_box'], 'string'],
			[['status','expected_qty','accepted_qty','id'], 'integer'],
			[['client_OrderSource','client_ExternalOrderId','client_ReferenceNumber','order_number','created_at','updated_at'], 'safe'],
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
		$query = EcommerceReturn::find();

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
			'expected_qty' => $this->expected_qty,
			'accepted_qty' => $this->accepted_qty,
		]);

		$query->andFilterWhere(['like', 'order_number', $this->order_number])
			  ->andFilterWhere(['like', 'outbound_box', $this->outbound_box])
			  ->andFilterWhere(['like', 'client_ReferenceNumber', $this->client_ReferenceNumber])
			  ->andFilterWhere(['like', 'client_ExternalOrderId', $this->client_ExternalOrderId])
			  ->andFilterWhere(['like', 'client_OrderSource', $this->client_OrderSource]);

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


		return $dataProvider;
	}
}
