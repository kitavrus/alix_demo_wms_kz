<?php

namespace common\ecommerce\deliveryProposal\models;

use common\ecommerce\constants\Constants;
use common\modules\city\models\RouteDirections;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\store\models\Store;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposal;


/**
 * TlDeliveryProposalSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposal`.
 */
class DeliveryProposalSearch extends TlDeliveryProposal
{
    public $city_to;
    public $region_to;
    public $country_to;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_to','region_to','city_to','delivery_type','id', 'client_id', 'route_from', 'route_to', 'mc_actual', 'kg', 'kg_actual', 'number_places', 'number_places_actual', 'cash_no', 'price_invoice', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'updated_at'], 'integer'],
            [['client_ttn','orders','created_at'], 'string'],
            [[ 'route_from', 'route_to',], 'safe'],
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
        $query = TlDeliveryProposal::find();

        $query->with(['client','routeTo','routeTo.region','routeTo.city','routeTo.country']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 55,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->andWhere([
                'client_id' =>  Constants::getCarPartClientIDs(),
            ]);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'delivery_type' => $this->delivery_type,
            'client_id' => !empty($this->client_id) ? $this->client_id : Constants::getCarPartClientIDs(),
            'route_to' => $this->route_to,
            'status' => $this->status,
            'client_ttn' => $this->client_ttn,
        ]);

        // SHIPPED DATETIME
        if(!empty($this->shipped_datetime)) {
            $date = explode('/',$this->shipped_datetime);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'shipped_datetime', strtotime($date[0]),strtotime($date[1])]);
        }

        // Filter by order number
        if(!empty($this->orders)) {
            if($ids = TlDeliveryProposalOrders::find()->select('tl_delivery_proposal_id')->andWhere(['like','order_number',$this->orders])->asArray()->column()) {
            } else {
                $ids = -1;
            }

            $query->andFilterWhere(['id'=> $ids]);
        }

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

        return $dataProvider;
    }
}
