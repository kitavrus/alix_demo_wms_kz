<?php

namespace common\ecommerce\main\outbound\models;

use common\ecommerce\constants\Constants;
use common\modules\stock\models\RackAddress;
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
class ABCZombieReportSearch extends Stock
{
    public $address_unit1;
    public $address_unit2;
    public $address_unit3;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id'], 'integer'],
            [['address_unit1','address_unit2','address_unit3'], 'integer'],
            [['product_barcode','secondary_address','primary_address'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'client_id' => 'Клиент',
            'address_unit1' => 'Ряд',
            'address_unit2' => 'Место',
            'address_unit3' => 'Уровень',
            'product_barcode' => 'Продукт',
            'secondary_address' => 'Адрес',
            'primary_address' => 'Коробка',
        ];
    }

    public function init() {
        $this->address_unit3 = 1;
        parent::init();
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
        $subStock = (new \yii\db\Query())
            ->select('id')
            ->from(RackAddress::tableName())
            ->andWhere(['warehouse_id' => 2])
            ->andFilterWhere(['address_unit1' => $this->address_unit1])
            ->andFilterWhere(['address_unit2' => $this->address_unit2])
            ->andFilterWhere(['address_unit3' => $this->address_unit3]);

        $query = (new \yii\db\Query())
            ->select('client_id, primary_address, secondary_address, product_barcode, count(*) as productQty')
            ->from(Stock::tableName())
            ->andWhere(['address_sort_order'=>$subStock])
            ->andWhere([
                'client_id' => !empty($this->client_id) ? $this->client_id : Constants::getCarPartClientIDs(),
                'status_availability'=>Stock::STATUS_AVAILABILITY_YES
            ])
            ->andFilterWhere(['primary_address' => $this->primary_address])
            ->andFilterWhere(['secondary_address' => $this->secondary_address])
            ->andFilterWhere(['product_barcode' => $this->product_barcode])
            ->groupBy('secondary_address, primary_address, product_barcode')
            ->orderBy('secondary_address, primary_address, product_barcode')
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => 200,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchZombieBox($params)
    {
        $this->load($params);

        $subStock = (new \yii\db\Query())
            ->select('id')
            ->from(RackAddress::tableName())
            ->andWhere(['warehouse_id' => 2])
            ->andFilterWhere(['address_unit1' => $this->address_unit1])
            ->andFilterWhere(['address_unit2' => $this->address_unit2])
            ->andFilterWhere(['address_unit3' => $this->address_unit3]);


        $queryReservedInBox = (new \yii\db\Query())
            ->select('product_barcode')
            ->from(Stock::tableName())
            ->andWhere([
                'client_id' => !empty($this->client_id) ? $this->client_id : Constants::getCarPartClientIDs(),
                'status_availability'=>Stock::STATUS_AVAILABILITY_RESERVED
            ]);

        $query = (new \yii\db\Query())
            ->from(Stock::tableName())
            ->andWhere(['address_sort_order'=>$subStock])
            ->andWhere([
                'client_id' => !empty($this->client_id) ? $this->client_id : Constants::getCarPartClientIDs(),
                'status_availability'=>Stock::STATUS_AVAILABILITY_YES
            ])
            ->andWhere(['NOT IN','product_barcode',$queryReservedInBox])
            ->andFilterWhere(['primary_address' => $this->primary_address])
            ->andFilterWhere(['secondary_address' => $this->secondary_address])
            ->andFilterWhere(['product_barcode' => $this->product_barcode])
            ->groupBy('primary_address')
            ->orderBy('primary_address')
            ->indexBy('primary_address')
            ->all();

//        $queryReservedInBox1 = (new \yii\db\Query())
//            ->select('product_barcode')
//            ->from(Stock::tableName())
//            ->andWhere([
//                'client_id' => !empty($this->client_id) ? $this->client_id : Constants::getCarPartClientIDs(),
//                'status_availability'=>Stock::STATUS_AVAILABILITY_RESERVED
//            ]);
//
//        $query2 = (new \yii\db\Query())
//            ->from(Stock::tableName())
//            ->andWhere(['address_sort_order'=>$subStock])
//            ->andWhere([
//                'client_id' => !empty($this->client_id) ? $this->client_id : Constants::getCarPartClientIDs(),
//                'status_availability'=>Stock::STATUS_AVAILABILITY_YES
//            ])
//            ->andWhere(['NOT IN','product_barcode',$queryReservedInBox1])
//            ->andFilterWhere(['primary_address' => $this->primary_address])
//            ->andFilterWhere(['secondary_address' => $this->secondary_address])
//            ->andFilterWhere(['product_barcode' => $this->product_barcode])
//            ->groupBy('primary_address')
//            ->orderBy('primary_address')
//            ->indexBy('primary_address')
//            ->all();

//        foreach($query2 as $key=>$q1) {
//            if(!array_key_exists($key,$query)) {
//                echo $key."<br />";
//            }
//        }
//
//        die("THE END");

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => 200,
            ],
        ]);

        return $dataProvider;
    }
}