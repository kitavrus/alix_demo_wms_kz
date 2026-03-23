<?php

namespace common\modules\transportLogistics\models;

use Yii;
use common\models\ActiveRecord;
use common\modules\store\models\Store;
use common\modules\city\models\City;
use app\modules\transportLogistics\transportLogistics;
use yii\helpers\ArrayHelper;
use common\modules\agentBilling\components\AgentBillingManager;

/**
 * This is the model class for table "tl_delivery_proposal_route_cars".
 *
 * @property integer $id
 * @property integer $route_city_from
 * @property integer $route_city_to
 * @property integer $delivery_date
 * @property integer $shipped_datetime
 * @property integer $accepted_datetime
 * @property string $mc_filled
 * @property string $kg_filled
 * @property string $driver_name
 * @property string $driver_phone
 * @property string $driver_auto_number
 * @property integer $agent_id
 * @property integer $car_id
 * @property integer $grzch // TO DELETE
 * @property integer $cash_no
 * @property integer $price_invoice
 * @property string $price_invoice_with_vat
 * @property integer $status
 * @property integer $status_invoice
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlDeliveryProposalRouteCars extends ActiveRecord
{

    /*
     * @var
     * */
//    public $cstm_route_car_id;

    /*
     * @var integer status
     * */
    const STATUS_UNDEFINED = 0; //не указан
    const STATUS_NEW = 1; //новый
    const STATUS_ON_ROUTE = 2; //в дороге
    const STATUS_DELIVERED = 3; //доставлен
    const STATUS_DONE = 4;  //выполнен
    const STATUS_CAR_ADDED_TO_ROUTE = 5;  //Машина добавлена к маршруту
    const STATUS_FREE = 6;  //Машина свободна, не прикреплена ни к одному маршруту
//    const STATUS_ADD_CAR = 5;  //добавлена машина
//    const STATUS_ADD_ROUTE_TO_DP = 6;  //Добавьте маршрут к заявке
//    const STATUS_ADD_CAR_TO_ROUTE = 7;  //Добавьте к маршруту машину
    const STATUS_ROUTE_FORMED = 8;  //Маршрут сформирован


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_route_cars';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['driver_name','driver_phone','driver_auto_number','agent_id', 'car_id', 'cash_no',], 'required'],
            [['driver_name','driver_phone','driver_auto_number'], 'string'],
//            [['grzch'], 'string'],
            [['route_city_from', 'route_city_to', 'agent_id', 'car_id', 'cash_no', 'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['mc_filled', 'kg_filled', 'price_invoice_with_vat','price_invoice',], 'number'],
            [['shipped_datetime','accepted_datetime','delivery_date'], 'safe'],
            [['comment'], 'string'],
            [['grzch'], 'default', 'value' => '1'],
//            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'route_city_from' => Yii::t('transportLogistics/forms', 'Route City From'),
            'route_city_to' => Yii::t('transportLogistics/forms', 'Route City To'),
            'delivery_date' => Yii::t('transportLogistics/forms', 'Delivery Date'),
            'accepted_datetime' => Yii::t('transportLogistics/forms', 'Accepted date'),
            'shipped_datetime' => Yii::t('transportLogistics/forms', 'Shipped date'),
            'mc_filled' => Yii::t('transportLogistics/forms', 'Mc Filled'),
            'kg_filled' => Yii::t('transportLogistics/forms', 'Kg Filled'),
            'agent_id' => Yii::t('transportLogistics/forms', 'Agent ID'),
            'car_id' => Yii::t('transportLogistics/forms', 'Car ID'),
            'grzch' => Yii::t('transportLogistics/forms', 'Grzch'),
            'route_car_id' => Yii::t('transportLogistics/forms', 'Route car 1'),

            'driver_name' => Yii::t('transportLogistics/forms', 'Driver name'),
            'driver_phone' => Yii::t('transportLogistics/forms', 'Driver phone'),
            'driver_auto_number' => Yii::t('transportLogistics/forms', 'Driver auto number'),

            'cash_no' => Yii::t('transportLogistics/forms', 'Cash No'),
            'price_invoice' => Yii::t('transportLogistics/forms', 'Price Invoice'),
            'price_invoice_with_vat' => Yii::t('transportLogistics/forms', 'Price Invoice With Vat'),
            'status' => Yii::t('transportLogistics/forms', 'Status'),
            'status_invoice' => Yii::t('transportLogistics/forms', 'Status Invoice'),
            'comment' => Yii::t('transportLogistics/forms', 'Comment'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
        ];
    }

    public function scenarios() {
        return [
            'mass-update'=>[
                //'cash_no',
                'status_invoice',
                //'status',
            ],
            'default'=>[
                'route_city_from',
                'route_city_to',
                'delivery_date',
                'accepted_datetime',
                'shipped_datetime',
                'agent_id',
                'car_id',
                'driver_name',
                'driver_phone',
                'driver_auto_number',
                'cash_no',
                'price_invoice',
                'price_invoice_with_vat',
                'status',
                'status_invoice',
                'comment',
            ]
        ];
    }

    /**
     * @return array Массив со статусами.
     */
    public static function getStatusArray($key=null)
    {
        $data = [
            self::STATUS_UNDEFINED => Yii::t('transportLogistics/forms', 'Undefined'), //Не определен
            self::STATUS_NEW => Yii::t('transportLogistics/forms', 'New'), //Новый
            self::STATUS_ON_ROUTE => Yii::t('transportLogistics/forms', 'On route'), //В пути
            self::STATUS_DELIVERED => Yii::t('transportLogistics/forms', 'Delivered'), //Доставлен
            self::STATUS_DONE => Yii::t('transportLogistics/forms', 'Done'),  //Выполнен
            self::STATUS_CAR_ADDED_TO_ROUTE => Yii::t('transportLogistics/custom', 'Машина добавлена к маршруту'),  //Машина добавлена к маршруту
//            self::STATUS_ADD_CAR => Yii::t('transportLogistics/custom', 'Добавлена машина'),  //Добавлена машина
//            self::STATUS_ADD_ROUTE_TO_DP => Yii::t('transportLogistics/custom', 'Добавьте маршрут к заявке'),  //Добавьте маршрут к заявке
//            self::STATUS_ADD_CAR_TO_ROUTE => Yii::t('transportLogistics/custom', 'Добавьте к маршруту машину'),  //Добавьте к маршруту машину
            self::STATUS_ROUTE_FORMED => Yii::t('transportLogistics/custom', 'Маршрут сформирован'),  //Маршрут сформирован
            self::STATUS_FREE => Yii::t('transportLogistics/custom', 'Машина свободна'),  //Машина свободна
        ];

        return isset($data[$key]) ? $data[$key] : (is_null($key) ? $data : $data[self::STATUS_UNDEFINED]);
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatusValue($status=null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(),$status);
    }


    /*
    * Relation has One with Store
    *
    * */
    public function getRouteCityFrom()
    {
        return $this->hasOne(City::className(), ['id' => 'route_city_from']);
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getRouteCityTo()
    {
        return $this->hasOne(City::className(), ['id' => 'route_city_to']);
    }

   /*
    * Relation has One with Car
    *
    * */
    public function getCar()
    {
        return $this->hasOne(TlCars::className(), ['id' => 'car_id']);
    }

   /*
    * Relation has One with Agent
    *
    * */
    public function getAgent()
    {
        return $this->hasOne(TlAgents::className(), ['id' => 'agent_id']);
    }

    /*
     * Relation has Many with Route
     *
     * */
    public function getRoutes()
    {
        return $this->hasMany(TlDeliveryRoutes::className(), ['id' => 'tl_delivery_proposal_route_id'])
            ->viaTable('tl_delivery_proposal_route_transport', ['tl_delivery_proposal_route_cars_id' => 'id']);
    }

    /*
     * Relation has Many with All Transport routes
     *
     * */
    public function getTransportItems()
    {
        return $this->hasMany(TlDeliveryProposalRouteTransport::className(), ['tl_delivery_proposal_route_cars_id' => 'id']);
    }

    /**
     * @return string city name
     */
    public function getCityName($city_id=null){
        if(!is_null($city_id)){
            $city = City::findOne($city_id);
            if(!empty($city))
                return $city->name;
        }
        return 'Не задан';
    }

    /**
     * @return string city name
     */
    public function getAgentName($agent_id=null){
        if(!is_null($agent_id)){
            $agent = TlAgents::findOne($agent_id);
            if(!empty($agent))
                return $agent->name;
        }
        return 'Не задан';
    }

    /**
     * @return string city name
     */
    public function getCarTitle($car_id=null){
        if(!is_null($car_id)){
            $car = TlCars::findOne($car_id);
            if(!empty($car))
                return  $car->title;
        }
        return 'Не задан';
    }


    /*
  * Array with attribute values functions mapping
  * @return array
  **/
    public function getAttributesValuesMap($attribute)
    {
        $data = [
            'route_city_from'=>'getCityName',
            'route_city_to'=>'getCityName',
            'cash_no'=>'getPaymentMethodValue',
            'status_invoice'=>'getInvoiceStatusValue',
            'status'=>'getStatusValue',
            'agent_id'=>'getAgentName',
            'car_id'=>'getCarTitle',
        ];

        return ArrayHelper::getValue($data, $attribute);
    }

}
