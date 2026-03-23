<?php
namespace app\modules\operatorDella\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\components\ClientManager;
use yii\helpers\ArrayHelper;
use common\modules\store\models\Store;


/**
 * TlDeliveryProposalSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposal`.
 */
class DeliveryOrderSearch extends TlDeliveryProposal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_type','is_client_confirmed','id', 'client_id', 'route_from', 'route_to', 'mc_actual', 'kg', 'kg_actual', 'number_places', 'number_places_actual', 'cash_no', 'price_invoice', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['mc', 'price_invoice_with_vat'], 'number'],
            [['orders','shipped_datetime','expected_delivery_date','delivery_date'], 'string'],
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
        $query = self::findOrderByClient();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
//            'sort'=> ['defaultOrder' => ['shipped_datetime'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'id' => $this->id,
            'delivery_type' => $this->delivery_type,
            'client_id' => $this->client_id,
            'route_from' => $this->route_from,
            'route_to' => $this->route_to,
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

        return $dataProvider;
    }

    /*
    * Define default scope
    * */
    public static function findOrderByClient()
    {
        $client = ClientManager::findClient();
        return parent::find()->andWhere(['client_id' => $client->id, 'deleted' => self::NOT_SHOW_DELETED]);

    }


    /*
     * Get points by authorised user
     * */
    public static function getClientPoints()
    {
        $client = ClientManager::findClient();
        $data = [];
        $data[0] = 'Не указан';
        if(ClientManager::isConfirmedClient($client)){
            $data += ArrayHelper::map(Store::find()->andWhere(['client_id'=>$client->id])->all(),'id', 'title');
        }
        return $data;
    }
    /*
    * Get points by authorised user
    * */
    public static function getPointsByClient($client_id)
    {
        $data[0] = 'Не указан';
        $data += ArrayHelper::map(Store::find()->andWhere(['client_id'=>$client_id])->all(),'id', 'title');
        return $data;
    }
}
