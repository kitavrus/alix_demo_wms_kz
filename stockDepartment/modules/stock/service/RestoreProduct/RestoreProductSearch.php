<?php
namespace stockDepartment\modules\stock\service\RestoreProduct;


use Yii;
use yii\base\Model;
use common\modules\stock\models\Stock;
use yii\data\ArrayDataProvider;

/**
 * RestoreProductSearch represents the model behind the search form about `common\modules\stock\models\Stock`.
 */
class RestoreProductSearch extends Stock
{
    public $order_number;
    public $parent_order_number;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address_sort_order','id', 'inbound_order_id', 'outbound_order_id', 'client_id', 'warehouse_id', 'product_id', 'condition_type', 'status', 'status_lost', 'status_availability', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['product_name', 'product_barcode', 'product_model', 'product_sku', 'box_barcode', 'primary_address', 'secondary_address', 'order_number', 'parent_order_number'], 'safe'],
			 [['inbound_client_box'], 'string'],
			 
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
     * @return ArrayDataProvider
     */
    public function searchArray($params)
    {
        $query = Stock::find()->select('
        field_extra5, 
        inventory_id,
        inventory_primary_address, 
        inventory_secondary_address, 
        id, 
        product_barcode, 
        primary_address, 
        secondary_address, 
        status_availability, 
        status_lost, 
        product_model, 
        condition_type, 
        status
        ');

        if (!($this->load($params) && $this->validate())) {
            $query->where('0=1');
        }

        $query->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'product_model', $this->product_model])
            ->andFilterWhere(['like', 'product_sku', $this->product_sku])
            ->andFilterWhere(['like', 'box_barcode', $this->box_barcode])
            ->andFilterWhere(['like', 'primary_address', $this->primary_address])
			->andFilterWhere(['like', 'inbound_client_box', $this->inbound_client_box])
            ->andFilterWhere(['like', 'secondary_address', $this->secondary_address]);

//        $query->groupBy('product_barcode, primary_address, secondary_address, status_availability, status, condition_type');
        $query->orderBy([
                    'address_sort_order'=>SORT_ASC,
                    'primary_address'=>SORT_DESC
                ]);


        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
//            'sort' => [
//                // Set the default sort by name ASC and created_at DESC.
//                'defaultOrder' => [
//                    'address_sort_order'=>SORT_ASC,
//                ]
//            ],
//            'sort' => [
//                'defaultOrder' => [
//                    'address_sort_order'=>SORT_ASC,
//                    //'primary_address'=>SORT_DESC
//                ],
//            ],
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $dataProvider;
    }
}