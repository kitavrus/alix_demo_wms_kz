<?php

namespace common\modules\stock\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\stock\models\ChangeAddressPlace;

/**
 * ChangeAddressPlaceSearch represents the model behind the search form of `common\modules\stock\models\ChangeAddressPlace`.
 */
class ChangeAddressPlaceSearch extends ChangeAddressPlace
{
	public  $anyPlace;
		public  $productBarcode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productBarcode','anyPlace','created_at'], 'safe'],
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
		$query = self::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
			'pagination' => [
				'pageSize' => 50,
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		if(!empty($this->anyPlace)) {
			$query->andFilterWhere(['or',
				['like', 'from_barcode', $this->anyPlace],
				['like', 'to_barcode', $this->anyPlace],
			]);
		}
		
		if(!empty($this->productBarcode)) {
			$query->andFilterWhere(
				['like', 'product_barcode', $this->productBarcode]
			);
		}

		if(!empty($this->created_at)) {
			$date = explode('/',$this->created_at);
			$date[0] = trim($date[0]).' 00:00:00';
			$date[1] = trim($date[1]).' 23:59:59';

			$query->andWhere(['between', 'created_at', strtotime($date[0]),strtotime($date[1])]);
		}


		return $dataProvider;
    }
}
