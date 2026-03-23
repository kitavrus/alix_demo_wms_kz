<?php

namespace common\ecommerce\entities;

use common\ecommerce\constants\TransferDefaultValue;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\ecommerce\entities\EcommerceTransfer;

/**
 * EcommerceTransferSearch represents the model behind the search form of `common\ecommerce\entities\EcommerceTransfer`.
 */
class EcommerceTransferSearch extends EcommerceTransfer
{
	
	public $boxBarcode;
		
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'expected_box_qty', 'expected_qty', 'allocated_qty', 'accepted_qty',  'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'updated_at', 'deleted'], 'integer'],
            [['client_BatchId', 'client_Status', 'client_LcBarcode', 'status', 'api_status'], 'safe'],
            [['print_picking_list_date','packing_date','date_left_warehouse','created_at'], 'safe'],
			[['boxBarcode'], 'safe'],
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
        $query = EcommerceTransfer::find();
        $query->andFilterWhere([
            'client_LcBarcode' => TransferDefaultValue::MAIN_VIRTUAL_BOX]
        );
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'expected_box_qty' => $this->expected_box_qty,
            'status' => $this->status,
//            'expected_qty' => $this->expected_qty,
//            'allocated_qty' => $this->allocated_qty,
//            'accepted_qty' => $this->accepted_qty,
//            'print_picking_list_date' => $this->print_picking_list_date,
//            'packing_date' => $this->packing_date,
//            'date_left_warehouse' => $this->date_left_warehouse,
//            'created_user_id' => $this->created_user_id,
//            'updated_user_id' => $this->updated_user_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);


        if(!empty($this->created_at)) {
            $date = explode('/',$this->created_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'created_at', strtotime($date[0]),strtotime($date[1])]);
        }

        if(!empty($this->print_picking_list_date)) {
            $date = explode('/',$this->print_picking_list_date);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'print_picking_list_date', strtotime($date[0]),strtotime($date[1])]);
        }

        if(!empty($this->packing_date)) {
            $date = explode('/',$this->packing_date);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'packing_date', strtotime($date[0]),strtotime($date[1])]);
        }

        if(!empty($this->date_left_warehouse)) {
            $date = explode('/',$this->date_left_warehouse);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'date_left_warehouse', strtotime($date[0]),strtotime($date[1])]);
        }
		
		 if(!empty($this->boxBarcode)) {

			$transferId = EcommerceStock::find()
								   ->select('transfer_id')
								   ->andWhere([
								   		'transfer_outbound_box'=>$this->boxBarcode,
								   ])
								   ->scalar();

            $query->andWhere(['id'=>$transferId]);
        }

        $query->andFilterWhere(['like', 'client_BatchId', $this->client_BatchId]);
//            ->andFilterWhere(['like', 'client_Status', $this->client_Status])
//            ->andFilterWhere(['like', 'client_LcBarcode', $this->client_LcBarcode])
//            ->andFilterWhere(['like', 'status', $this->status])
//            ->andFilterWhere(['like', 'api_status', $this->api_status]);

        return $dataProvider;
    }
}
