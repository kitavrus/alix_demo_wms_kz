<?php

namespace stockDepartment\modules\outbound\models;

use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItems;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\VarDumper;

//use common\modules\inbound\models\InboundOrder;

/**
 * InboundOrderSearch represents the model behind the search form about `common\modules\inbound\models\InboundOrder`.
 */
class OperationCrossDockSearch extends CrossDock
{
    public $extra_status;
    public $expected_qty;
    public $allocated_qty;
    public $parent_order_number;
    public $x;
    public $order_type;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['consignment_cross_dock_id','client_id', 'from_point_id', 'to_point_id', 'order_type', 'status', 'accepted_number_places_qty', 'expected_number_places_qty', 'expected_datetime', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id',  'updated_at', 'deleted'], 'integer'],
            [['to_point_title', 'from_point_title','created_at'], 'string', 'max' => 255],
            [['weight_brut','weight_net','box_m3','party_number', 'order_number', 'internal_barcode'], 'string', 'max' => 128],
            [['parent_order_number','order_number'], 'string']
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
        $query = CrossDock::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>false,
            'sort'=> false, // ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'order_number' => $this->order_number,
            'status' => $this->status,
        ]);

        $query->andWhere(['like', 'party_number', $this->parent_order_number]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchReport($params)
    {
        $query = CrossDock::find()->select('cross_dock.id, cross_dock.internal_barcode, cross_dock.client_id, cross_dock.party_number, cross_dock.to_point_id')
                                  ->joinWith('orderItems');
                                  //->groupBy('cross_dock_items.box_m3');



        $this->load($params);

        if (!$this->validate()) {

            $dataProvider = new ArrayDataProvider([
                'allModels' => $query->asArray()->all(),
                'sort' => [
                    'attributes' => ['id'],
                ],
                'pagination' => [
                    'pageSize' => 25,
                ],
            ]);

            return $dataProvider;
        }

        $query->andFilterWhere([
            'cross_dock.id' => $this->id,
            'cross_dock.client_id' => $this->client_id,
            'cross_dock.status' => $this->status,
            'cross_dock.party_number' => $this->party_number,
            'cross_dock.to_point_id' => $this->to_point_id,
        ]);

        if(!empty($this->created_at)) {
            $date = explode('/',$this->created_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'cross_dock.created_at', strtotime($date[0]),strtotime($date[1])]);
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        return $dataProvider;
    }
}
