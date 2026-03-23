<?php

namespace stockDepartment\modules\outbound\models;

use common\modules\outbound\models\OutboundPickingLists;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\outbound\models\OutboundOrder;
use yii\helpers\VarDumper;


/**
 * This is the model class for table "outbound_picking_lists".
 *
 * @property integer $id
 * @property integer $employee_id
 * @property string $barcode
 * @property string $employee_barcode
 * @property integer $status
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class OutboundPickingListSearch extends OutboundPickingLists
{
    public $order_number;
    public $parent_order_number;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['barcode'], 'string'],
            [['order_number', 'parent_order_number'], 'safe'],

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
        $query = OutboundPickingLists::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'status' => $this->status,
            'barcode' => $this->barcode,
        ]);
        // Filter by order number
        if(!empty($this->order_number) || !empty($this->parent_order_number)){
            $subQuery = OutboundOrder::find();
            $subQuery->andFilterWhere([
                'order_number' => $this->order_number,
                'parent_order_number' => $this->parent_order_number,
            ]);
            $ids = $subQuery->asArray()->column();
            $query->andFilterWhere(['outbound_order_id'=> $ids]);
        }

        return $dataProvider;
    }
}
