<?php

namespace stockDepartment\modules\report\models;

use common\modules\billing\components\BillingManager;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use common\helpers\iHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposal;


/**
 * TlDeliveryProposalSearchExport represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposal`.
 */
class TlDeliveryProposalSearchReportExport extends TlDeliveryProposal
{
    /*
    * @var integer Agent id
    * */
    public $agent_id;
    public $country_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id','agent_id','is_client_confirmed','id', 'client_id', 'route_from', 'route_to', 'mc_actual', 'kg', 'kg_actual', 'number_places', 'number_places_actual', 'cash_no', 'price_invoice', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['mc', 'price_invoice_with_vat'], 'number'],
            [['shipped_datetime','expected_delivery_date','delivery_date'], 'string'],
            [['comment', 'route_from', 'route_to',], 'safe'],
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

        $query->with(['client']);

        $query->andWhere(['NOT IN', 'company_transporter', BillingManager::getTransportCompanyOutTariff()]);
        $query->orWhere('company_transporter IS NULL');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['shipped_datetime'=>SORT_DESC]]
        ]);

        if (!($this->load($params,'TlDeliveryProposalSearch') && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
//            'is_client_confirmed' => $this->is_client_confirmed,
            'client_id' => $this->client_id,
            'route_from' => $this->route_from,
            'route_to' => $this->route_to,
//            'mc' => $this->mc,
//            'mc_actual' => $this->mc_actual,
//            'kg' => $this->kg,
//            'kg_actual' => $this->kg_actual,
//            'number_places' => $this->number_places,
//            'number_places_actual' => $this->number_places_actual,
//            'cash_no' => $this->cash_no,
//            'price_invoice' => $this->price_invoice,
//            'price_invoice_with_vat' => $this->price_invoice_with_vat,
//            'status' => $this->status,
        ]);

//        $query->andFilterWhere(['like', 'comment', $this->comment]);


        // SHIPPED DATETIME
        if(!empty($this->shipped_datetime)) {
            $date = explode('/',$this->shipped_datetime);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'shipped_datetime', strtotime($date[0]), strtotime($date[1])]);
        }

        // EXPECTED DELIVERY DATETIME
        if(!empty($this->expected_delivery_date)) {
            $date = explode('/',$this->expected_delivery_date);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'expected_delivery_date', strtotime($date[0]), strtotime($date[1])]);
        }

        // DELIVERY DATE
        if(!empty($this->delivery_date)) {
            $date = explode('/',$this->delivery_date);
            $date[0] = trim($date[0]).' 00:00:00';
            $date[1] = trim($date[1]).' 23:59:59';

            $query->andWhere(['between', 'delivery_date', strtotime($date[0]), strtotime($date[1])]);
        }

        // Filter by agent id
        if(!empty($this->agent_id)) {
            $ids = [];

            // Найти все машины определенного перевозчика
            // Найти все маршруты в которых используется этот подрядчик

            if($carIds = TlDeliveryProposalRouteCars::find()->select('id')->where(['agent_id'=>$this->agent_id])->asArray()->column()) {
                if($ids = TlDeliveryProposalRouteTransport::find()->select('tl_delivery_proposal_id')->where(['tl_delivery_proposal_route_cars_id'=>$carIds])->asArray()->column()) {

                } else {
//                   $ids = -1;
                }
            }

            $query->andFilterWhere(['id'=> $ids]);
        }

        return $dataProvider;
    }
}
