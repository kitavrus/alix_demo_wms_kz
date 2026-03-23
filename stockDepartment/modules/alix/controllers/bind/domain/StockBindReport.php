<?php

namespace stockDepartment\modules\alix\controllers\bind\domain;

use common\modules\inbound\models\InboundOrder;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\stock\models\Stock;
use yii\db\Query;
use common\modules\outbound\models\OutboundOrder;
use yii\data\ArrayDataProvider;

class StockBindReport extends Stock
{
    public $order_number;
    public $parent_order_number;
    public $client_id;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['parent_order_number'] = 'Номер партии';

        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'client_id',
                    'id',
                    'inbound_order_id',
                    'outbound_order_id',
                    'warehouse_id',
                    'product_id',
                    'condition_type',
                    'status',
                    'status_lost',
                    'status_availability',
                    'created_user_id',
                    'updated_user_id',
                    'created_at',
                    'updated_at',
                    'deleted'
                ],
                'integer'
            ],
            [
                [
                    'parent_order_number',
                    'product_name',
                    'product_barcode',
                    'product_model',
                    'product_sku',
                    'box_barcode',
                    'primary_address',
                    'secondary_address',
                    'order_number',
                    'parent_order_number',
                    'our_product_barcode',
                    'bind_qr_code'
                ],
                'safe'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = Stock::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                // Set the default sort by name ASC and created_at DESC.
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'condition_type' => $this->condition_type,
            'status' => $this->status,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
            'status_lost' => $this->status_lost,
        ]);

        // Фильтруем только записи где bind_qr_code и our_product_barcode не NULL
        $query
            ->andWhere('bind_qr_code IS NOT NULL')
            ->andWhere('our_product_barcode IS NOT NULL');

        $query
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'product_model', $this->product_model])
            ->andFilterWhere(['like', 'product_sku', $this->product_sku])
            ->andFilterWhere(['like', 'box_barcode', $this->box_barcode])
            ->andFilterWhere(['like', 'primary_address', $this->primary_address])
            ->andFilterWhere(['like', 'secondary_address', $this->secondary_address])
            ->andFilterWhere(['like', 'our_product_barcode', $this->our_product_barcode])
            ->andFilterWhere(['like', 'bind_qr_code', $this->bind_qr_code]);

        if ($this->order_number || $this->parent_order_number) {
            $subQ1 = (new Query())
                ->select('id')
                ->from(InboundOrder::tableName())
                ->where(['deleted' => 0])
                ->andFilterWhere(['like', 'order_number', $this->order_number])
                ->andFilterWhere(['like', 'parent_order_number', $this->parent_order_number]);

            $subQ2 = (new Query())
                ->select('id')
                ->from(OutboundOrder::tableName())
                ->where(['deleted' => 0])
                ->andFilterWhere(['like', 'order_number', $this->order_number])
                ->andFilterWhere(['like', 'parent_order_number', $this->parent_order_number]);

            $query
                ->andWhere(['in', 'inbound_order_id', $subQ1])
                ->orWhere(['in', 'outbound_order_id', $subQ2]);
        }


        return $dataProvider;
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
        $detail = Yii::$app->request->get('detail');
        $query = Stock::find()->select(
            'inventory_secondary_address, 
            inventory_primary_address, 
            inventory_id, 
            consignment_inbound_id, 
            inbound_order_id, 
            client_id, id, 
            product_barcode,
            our_product_barcode,
            bind_qr_code, 
            primary_address, 
            secondary_address, 
            status_availability,
            status_lost, 
            product_model, 
            condition_type, 
            status, 
            count(id) as qty, 
            product_sku'
        );


        if (!($this->load($params) && $this->validate())) {
            $query->andWhere('0=1');
        }

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'condition_type' => $this->condition_type,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
        ]);

        if ($this->order_number || $this->parent_order_number) {
            $subQ1 = (new Query())
                ->select('id')
                ->from(InboundOrder::tableName())
                ->where(['deleted' => 0])
                ->andFilterWhere(['like', 'order_number', $this->order_number])
                ->andFilterWhere(['like', 'parent_order_number', $this->parent_order_number]);

            $subQ2 = (new Query())
                ->select('id')
                ->from(OutboundOrder::tableName())
                ->where(['deleted' => 0])
                ->andFilterWhere(['like', 'order_number', $this->order_number])
                ->andFilterWhere(['like', 'parent_order_number', $this->parent_order_number]);

            $query
                ->andWhere(['in', 'inbound_order_id', $subQ1])
                ->orWhere(['in', 'outbound_order_id', $subQ2]);
        }

        $query
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'product_barcode', $this->product_barcode])
            ->andFilterWhere(['like', 'product_model', $this->product_model])
            ->andFilterWhere(['like', 'product_sku', $this->product_sku])
            ->andFilterWhere(['like', 'box_barcode', $this->box_barcode])
            ->andFilterWhere(['like', 'primary_address', $this->primary_address])
            ->andFilterWhere(['like', 'secondary_address', $this->secondary_address])
            ->andFilterWhere(['like', 'our_product_barcode', $this->our_product_barcode])
            ->andFilterWhere(['like', 'bind_qr_code', $this->bind_qr_code]);

        if ($detail) {
            $query->groupBy('product_barcode, primary_address, secondary_address, consignment_inbound_id, condition_type, product_sku');
        } else {
            $query->groupBy('product_barcode, primary_address, secondary_address, condition_type, product_sku');
        }
        $query->orderBy([
            'address_sort_order' => SORT_ASC,
            'primary_address' => SORT_DESC
        ]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return [$dataProvider, $query];
    }
}
