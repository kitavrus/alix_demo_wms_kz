<?php
namespace app\modules\operatorDella\models;

use common\modules\store\models\Store;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TlDeliveryProposalSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposal`.
 */
class DeliveryProposalSearch extends TlDeliveryProposal
{
    public $cityFrom;
    public $cityTo;
    public $dateLoadingCargo;
    public $m3;
    public $kg;
    public $places;
    public $phone;
    public $fio;
    public $client_id;

/*    public function getFormAttribs() {
        return [
            'cityFrom'=>[
                'type'=>Form::INPUT_WIDGET,
                'widgetClass'=>'\kartik\widgets\Select2',
                'options'=>['data'=>DeliveryCalculatorForm::getDefaultRoutesFrom(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT)],
            ],
            'cityTo'=>[
                'type'=>Form::INPUT_WIDGET,
                'widgetClass'=>'\kartik\widgets\Select2',
                'options'=>['data'=>DeliveryCalculatorForm::getDefaultRoutesTo(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT)],
            ],
            'm3'=>['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter password...']],
            'kg'=>['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter password...']],
            'places'=>['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter password...']],
        ];
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['places','cityTo','cityFrom','delivery_type','is_client_confirmed','id', 'client_id', 'route_from', 'route_to', 'mc_actual', 'kg', 'kg_actual', 'number_places', 'number_places_actual', 'cash_no', 'price_invoice', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'updated_at'], 'integer'],
            [['m3','kg','mc', 'price_invoice_with_vat'], 'number'],
            [['dateLoadingCargo','orders','shipped_datetime','expected_delivery_date','delivery_date', 'created_at'], 'string'],
            [[ 'comment', 'route_from', 'route_to',], 'safe'],
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

//        $query->with(['client','routeFrom','routeTo']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id'=> $this->id]);
        $query->andFilterWhere(['client_id'=> $this->client_id]);

        if(!empty($this->cityTo)) {
            if($ids = Store::find()->select('id')->andWhere(['city_id'=>$this->cityTo])->asArray()->column()) {
            } else {
                $ids = -1;
            }
            $query->andFilterWhere(['route_to'=> $ids]);
        }

        if(!empty($this->cityFrom)) {
            if($ids = Store::find()->select('id')->andWhere(['city_id'=>$this->cityFrom])->asArray()->column()) {
            } else {
                $ids = -1;
            }
            $query->andFilterWhere(['route_from'=> $ids]);
        }

        return $dataProvider;
    }
}