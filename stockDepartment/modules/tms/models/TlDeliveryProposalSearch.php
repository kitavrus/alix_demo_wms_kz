<?php

namespace stockDepartment\modules\tms\models;

use common\modules\city\models\RouteDirections;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\store\models\Store;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use common\helpers\iHelper;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposal;


/**
 * TlDeliveryProposalSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposal`.
 */
class TlDeliveryProposalSearch extends TlDeliveryProposal
{
    public $delivery2day;
    public $notReadyToPayment;
    public $city_to;
    public $region_to;
    public $country_to;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_to','region_to','city_to','delivery_type','is_client_confirmed','id', 'client_id', 'route_from', 'route_to', 'mc_actual', 'kg', 'kg_actual', 'number_places', 'number_places_actual', 'cash_no', 'price_invoice', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'updated_at'], 'integer'],
            [['mc', 'price_invoice_with_vat'], 'number'],
            [['orders','shipped_datetime','expected_delivery_date','delivery_date', 'created_at'], 'string'],
            [['delivery2day', 'notReadyToPayment', 'comment', 'route_from', 'route_to',], 'safe'],
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

        $query = TlDeliveryProposal::find();

//        if(iHelper::isClient()) {
//            $query->andFilterWhere([
//                'client_id' => Yii::$app->user->id,
//            ]);
//        }

        $query->with(['client','routeTo','routeTo.region','routeTo.city','routeTo.country']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
//            'sort'=> ['defaultOrder' => ['shipped_datetime'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'id' => $this->id,
//            'is_client_confirmed' => $this->is_client_confirmed,
            'delivery_type' => $this->delivery_type,
            'client_id' => $this->client_id,
            'route_from' => $this->route_from,
            'route_to' => $this->route_to,
//            'delivery_date' => $this->delivery_date,
            'mc' => $this->mc,
            'mc_actual' => $this->mc_actual,
            'kg' => $this->kg,
            'kg_actual' => $this->kg_actual,
            'number_places' => $this->number_places,
            'number_places_actual' => $this->number_places_actual,
            'cash_no' => $this->cash_no,
            'price_invoice' => $this->price_invoice,
            'price_invoice_with_vat' => $this->price_invoice_with_vat,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);


        // SHIPPED DATETIME
        if(!empty($this->shipped_datetime)) {
            $date = explode('/',$this->shipped_datetime);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'shipped_datetime', strtotime($date[0]),strtotime($date[1])]);
        }

        // EXPECTED DELIVERY DATETIME
        if(!empty($this->expected_delivery_date)) {
            $date = explode('/',$this->expected_delivery_date);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'expected_delivery_date', strtotime($date[0]),strtotime($date[1])]);
        }

        // DELIVERY DATE
        if(!empty($this->delivery_date)) {
            $date = explode('/',$this->delivery_date);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'delivery_date', strtotime($date[0]),strtotime($date[1])]);
        }

        // DELIVERY DATE
        if(!empty($this->created_at)) {
            $date = explode('/',$this->created_at);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'created_at', strtotime($date[0]),strtotime($date[1])]);
        }

        // Filter by order number
        if(!empty($this->orders)) {
//            $ids = [];
            if($ids = TlDeliveryProposalOrders::find()->select('tl_delivery_proposal_id')->andWhere(['like','order_number',$this->orders])->asArray()->column()) {
            } else {
                $ids = -1;
            }

            $query->andFilterWhere(['id'=> $ids]);
        }


        if(!empty($this->delivery2day)) {

            $ids = TlDeliveryProposal::find()
                //->andWhere(['status' => TlDeliveryProposal::STATUS_ON_ROUTE])
                ->andWhere('TO_DAYS(NOW()) - TO_DAYS(FROM_UNIXTIME(shipped_datetime)) > 2 AND shipped_datetime IS NOT NULL AND delivery_date IS NULL AND TO_DAYS(NOW()) - TO_DAYS(FROM_UNIXTIME(created_at)) <= 30')
                ->column();

            if(empty($ids) || !is_array($ids)) {
                $ids = -1;
            }

            $query->andFilterWhere(['id'=> $ids]);
        }

        if(!empty($this->notReadyToPayment)) {
            $to = new \DateTime('today');
            $to->modify('+23 hour 59 minutes 59 seconds');
            $from = new \DateTime('today');
             $query
                ->andWhere(['between','shipped_datetime', $from->modify('-60 day')->getTimestamp(), $to->getTimestamp()])
                ->andWhere(['ready_to_invoicing' => TlDeliveryProposal::READY_TO_INVOICING_NO]);

//            if(empty($ids) || !is_array($ids)) {
//                $ids = -1;
//            }
//
//            $query->andFilterWhere(['id'=> $ids]);

            // Filter by order number
        }

/*        if(!empty($this->city_to)) {
            if($ids = Store::find()->select('id')->andWhere(['city_id'=>$this->city_to])->asArray()->column()) {
            } else {
                $ids = -1;
            }
            $query->andFilterWhere(['route_to'=> $ids]);
        }*/

/*        if(!empty($this->region_to)) {
            if($ids = Store::find()->select('id')->andWhere(['region_id'=>$this->region_to])->asArray()->column()) {
            } else {
                $ids = -1;
            }
            $query->andFilterWhere(['route_to'=> $ids]);
        } */

        if(!empty($this->city_to)) {
            $rd = RouteDirections::findOne($this->city_to);
            $ids = -1;
            if($rd){
                $rdCityIDs = $rd->getCityIDs();
                $ids = Store::find()->select('id')->andWhere(['city_id'=>$rdCityIDs])->asArray()->column();
                if(!$ids) {
                    $ids = -1;
                }
            }
            $query->andFilterWhere(['route_to'=> $ids]);
        }

//        if(!empty($this->country_to)) {
//            if($ids = Store::find()->select('id')->andWhere(['country_id'=>$this->country_to])->asArray()->column()) {
//            } else {
//                $ids = -1;
//            }
//            $query->andFilterWhere(['route_to'=> $ids]);
//        }


        return $dataProvider;
    }

    public function countByStatus($status)
    {
        $query = TlDeliveryProposal::find();

       $count = $query->andFilterWhere(['status' => $status])
                ->andFilterWhere(['between', 'created_at', strtotime('01.04.2015'), strtotime('tomorrow')])
                ->count();
        return $count;
    }

    public function countDelivery2Day()
    {
        return TlDeliveryProposal::find()
            //->andWhere(['status' => TlDeliveryProposal::STATUS_ON_ROUTE])
            ->andWhere('TO_DAYS(NOW()) - TO_DAYS(FROM_UNIXTIME(shipped_datetime)) > 2 AND shipped_datetime IS NOT NULL AND delivery_date IS NULL AND TO_DAYS(NOW()) - TO_DAYS(FROM_UNIXTIME(created_at)) <= 30')
            ->count();
    }
    /*
     * TODO NOT USED. TO REMOVE
     * */
    public function countNotReadyToPayment()
    {
        $to = new \DateTime('today');
        $to->modify('+23 hour 59 minutes 59 seconds');
        $from = new \DateTime('today');

        return TlDeliveryProposal::find()
            ->andWhere(['between','shipped_datetime', $from->modify('-60 day')->getTimestamp(),  $to->getTimestamp()])
            ->andWhere(['ready_to_invoicing' => TlDeliveryProposal::READY_TO_INVOICING_NO])
            ->count();
    }
}
