<?php

namespace common\clientObject\main\outbound\models;

use common\clientObject\constants\Constants;
use common\modules\stock\models\Stock;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;


/**
 * This is the model class for table "outbound_orders".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $supplier_id
 * @property integer $warehouse_id
 * @property integer $order_number
 * @property integer $parent_order_number
 * @property integer $order_type
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $accepted_number_places_qty
 * @property integer $expected_number_places_qty
 * @property integer $expected_datetime
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class ABCReportSearch extends Stock
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id'], 'integer'],
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
//        if (!($this->load($params) && $this->validate())) {
//            return $dataProvider;
//        }
        $subStock = (new \yii\db\Query())
            ->from(Stock::tableName())
            ->andWhere([
                'client_id' => !empty($this->client_id) ? $this->client_id : Constants::getCarPartClientIDs(),
            ])
            ->andWhere(['status_availability'=>Stock::STATUS_AVAILABILITY_RESERVED])
            ->groupBy('outbound_order_id, product_barcode');

        $query = (new \yii\db\Query())
            ->select('client_id, product_barcode, count(*) as productQty')
            ->from(['TS'=>$subStock])
            ->groupBy('product_barcode')
            ->orderBy('productQty DESC')
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => 200,
            ],
        ]);

        return $dataProvider;
    }
}