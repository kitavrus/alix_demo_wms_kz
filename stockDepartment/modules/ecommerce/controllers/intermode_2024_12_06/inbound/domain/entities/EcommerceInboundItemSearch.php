<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
//use common\ecommerce\entities\EcommerceInbound;

/**
 * EcommerceInboundSearch represents the model behind the search form of `common\ecommerce\entities\EcommerceInbound`.
 */
class EcommerceInboundItemSearch extends EcommerceInboundItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','product_expected_qty','product_accepted_qty','status'], 'integer'],
            [['client_box_barcode','client_lot_sku','client_product_sku','our_box_barcode','product_barcode'], 'string'],
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
	public function search($params,$inboundId)
    {
        $query = EcommerceInboundItem::find();

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
            'inbound_id' =>$inboundId,
            'id' => $this->id,
            'product_expected_qty' => $this->product_expected_qty,
            'product_accepted_qty' => $this->product_accepted_qty,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);

        return $dataProvider;
    }
}
