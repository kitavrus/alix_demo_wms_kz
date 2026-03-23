<?php

namespace stockDepartment\modules\tms\models;

use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlAgentEmployees;
use yii\helpers\VarDumper;

//use tlAgentDepartment\components\AgentTLAuthRoleManager;

/**
 * TlDeliveryProposalRouteCarsSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposalRouteCars`.
 */
class TlDeliveryProposalRouteCarsSearchExport extends TlDeliveryProposalRouteCars
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'route_city_from', 'route_city_to', 'agent_id', 'car_id', 'grzch', 'cash_no', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['agent_id','car_id','shipped_datetime','delivery_date', 'driver_name', 'driver_phone', 'driver_auto_number', 'comment'], 'safe'],
            [['mc_filled', 'kg_filled', 'price_invoice', 'price_invoice_with_vat'], 'number'],
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
        $query = TlDeliveryProposalRouteCars::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['shipped_datetime'=>SORT_DESC]]
        ]);

        if (!($this->load($params,'TlDeliveryProposalRouteCarsSearch') && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'route_city_from' => $this->route_city_from,
            'route_city_to' => $this->route_city_to,
//            'mc_filled' => $this->mc_filled,
//            'kg_filled' => $this->kg_filled,
            'agent_id' => $this->agent_id,
            'car_id' => $this->car_id,
            'cash_no' => $this->cash_no,
            'price_invoice' => $this->price_invoice,
            'price_invoice_with_vat' => $this->price_invoice_with_vat,
            'status' => $this->status,
            'status_invoice' => $this->status_invoice,
        ]);




        $query->andFilterWhere(['like', 'driver_name', $this->driver_name])
            ->andFilterWhere(['like', 'driver_phone', $this->driver_phone])
            ->andFilterWhere(['like', 'driver_auto_number', $this->driver_auto_number]);

        // SHIPPED DATETIME
        if(!empty($this->shipped_datetime)) {
            $date = explode('/',$this->shipped_datetime);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'shipped_datetime', strtotime($date[0]),strtotime($date[1])]);
        }

//        $query->andWhere('price_invoice > 0');
        $query->andWhere('price_invoice > 0 AND (mc_filled >= 0.01 OR kg_filled >= 0.01)');

//        echo "sdfsdf";
//        die;
//        $query->andWhere('mc_filled  0');




//        $q = clone $query;
//        $q->select('id');
//        VarDumper::dump($q->asArray()->column(),10,true);
//        die('asdasda');

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchExport($params)
    {
//        $query = TlDeliveryProposalRouteCars::find();
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//            'pagination' => false,
//            'sort'=> ['defaultOrder' => ['shipped_datetime'=>SORT_DESC]]
//        ]);

        if (!($this->load($params,'TlDeliveryProposalRouteCarsSearch') && $this->validate())) {
//            return $dataProvider;
        }

//        $query->andFilterWhere([
//            'id' => $this->id,
//            'route_city_from' => $this->route_city_from,
//            'route_city_to' => $this->route_city_to,
//            'mc_filled' => $this->mc_filled,
//            'kg_filled' => $this->kg_filled,
//            'agent_id' => $this->agent_id,
//            'car_id' => $this->car_id,
//            'cash_no' => $this->cash_no,
//            'price_invoice' => $this->price_invoice,
//            'price_invoice_with_vat' => $this->price_invoice_with_vat,
//            'status' => $this->status,
//            'status_invoice' => $this->status_invoice,
//        ]);

//        $query->andFilterWhere(['like', 'driver_name', $this->driver_name])
//            ->andFilterWhere(['like', 'driver_phone', $this->driver_phone])
//            ->andFilterWhere(['like', 'driver_auto_number', $this->driver_auto_number]);

        // SHIPPED DATETIME
//        if(!empty($this->shipped_datetime)) {
//            $date = explode('/',$this->shipped_datetime);
//            $date[0] = trim($date[0]);
//            $date[1] = trim($date[1]);
//
//            $query->andWhere(['between', 'shipped_datetime', $date[0],$date[1]]);
//        }

//        $query->andWhere('price_invoice > 0');

//        $q = clone $query;
//        $q->select('id');
//        VarDumper::dump($q->asArray()->column(),10,true);
//        die('asdasda');

//        return $dataProvider;

        $query = TlDeliveryProposal::find();

//        $query->andFilterWhere([
//            'id' => $this->id,
//            'route_city_from' => $this->route_city_from,
//            'route_city_to' => $this->route_city_to,
//            'mc_filled' => $this->mc_filled,
//            'kg_filled' => $this->kg_filled,
//            'agent_id' => $this->agent_id,
//            'car_id' => $this->car_id,
//            'cash_no' => $this->cash_no,
//            'price_invoice' => $this->price_invoice,
//            'price_invoice_with_vat' => $this->price_invoice_with_vat,
//            'status' => $this->status,
//            'status_invoice' => $this->status_invoice,
//        ]);

//      SHIPPED DATETIME
        if(!empty($this->shipped_datetime)) {
            $date = explode('/',$this->shipped_datetime);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'shipped_datetime', strtotime($date[0]),strtotime($date[1])]);
        }

        return $this;
//        VarDumper::dump($this,10,true);
    }
}
