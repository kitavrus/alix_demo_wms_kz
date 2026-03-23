<?php

namespace common\ecommerce\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "ecommerce_return_items".
 *
 * @property int $id
 * @property int $return_id Return id
 * @property int $product_id Product id
 * @property string $product_barcode Шк товара
 * @property string $product_barcode1 Шк товара
 * @property string $product_barcode2 Шк товара
 * @property string $product_barcode3 Шк товара
 * @property string $product_barcode4 Шк товара
 * @property int $expected_qty Product Expected qty
 * @property int $accepted_qty Product Accepted qty
 * @property int $status Status
 * @property int $begin_datetime Begin scanning datetime
 * @property int $end_datetime End scanning datetime
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceReturnItemSearch extends EcommerceReturnItem
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['product_barcode'], 'string'],
			[['expected_qty','accepted_qty'], 'integer'],
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
	public function search($params,$returnId)
	{
		$query = EcommerceReturnItem::find();

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
			'return_id' => $returnId,
			'id' => $this->id,
			'expected_qty' => $this->expected_qty,
			'accepted_qty' => $this->accepted_qty,
		]);

		$query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);

		return $dataProvider;
	}
}
