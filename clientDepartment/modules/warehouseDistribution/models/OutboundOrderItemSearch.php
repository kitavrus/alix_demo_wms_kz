<?php

namespace app\modules\warehouseDistribution\models;

use common\modules\outbound\models\OutboundOrderItem;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "outbound_order_items".
 *
 * @property integer $id
 * @property integer $outbound_order_id
 * @property integer $product_id
 * @property string $product_name
 * @property string $product_barcode
 * @property string $product_price
 * @property string $product_model
 * @property string $product_sku
 * @property string $product_madein
 * @property string $product_composition
 * @property string $product_exporter
 * @property string $product_importer
 * @property string $product_description
 * @property string $product_serialize_data
 * @property string $box_barcode
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class OutboundOrderItemSearch extends OutboundOrderItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outbound_order_id', 'product_id', 'status', 'expected_qty', 'accepted_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['product_price'], 'number'],
            [['product_exporter', 'product_importer', 'product_description', 'product_serialize_data'], 'string'],
            [['product_name', 'product_model', 'product_sku', 'product_madein', 'product_composition'], 'string', 'max' => 128],
            [['product_barcode', 'box_barcode'], 'string', 'max' => 54]
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
        $query = OutboundOrderItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination'=>false,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'outbound_order_id' => $this->outbound_order_id,
            'product_barcode' => $this->product_barcode,
        ]);

        $query->andFilterWhere(['like', 'product_model', $this->product_model]);



        return $dataProvider;
    }

}
