<?php

namespace common\modules\leads\models;

use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\user\models\User;
use Yii;
use common\models\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\modules\city\models\City;
use common\modules\store\models\Store;
use common\modules\client\models\Client;
use yii\helpers\VarDumper;
use common\modules\client\components\ClientManager as CManager;
use common\events\DpEvent;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\billing\components\BillingManager;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\components\DeliveryProposalManager;

/**
 * This is the model class for table "transportation_order_lead".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $from_city_id
 * @property string $customer_name
 * @property string $customer_name_2
 * @property string $customer_phone
 * @property string $customer_phone_2
 * @property string $delivery_type
 * @property string $delivery_method
 * @property string $customer_street
 * @property string $customer_house
 * @property string $customer_apartment
 * @property string $customer_floor
 * @property string $recipient_name
 * @property string $recipient_phone
 * @property string $recipient_street
 * @property string $recipient_house
 * @property string $recipient_apartment
 * @property string $recipient_floor
 * @property integer $places
 * @property string $customer_comment
 * @property integer $weight
 * @property integer $volume
 * @property string $declared_value
 * @property string $package_description
 * @property integer $status
 * @property integer $source
 * @property integer $order_number
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 * @property number $cost
 * @property number $cost_vat
 */
class TransportationOrderLead extends ActiveRecord
{
    /* Статусы для внешних клиентов
     * @var integer status
     */
    const STATUS_WAIT_FOR_CONFIRM = 1; //ждет подтверждения
    const STATUS_ON_ROUTE = 2; //в дороге
    const STATUS_DELIVERED = 3; //доставлен
    const STATUS_DONE = 4; //выполнен
    const STATUS_CONFIRMED = 5; //подтвержден

    /* Источник поступления заявки
     * @var integer source
     */
    const SOURCE_FRONTEND_PUBLIC = 1; //анонимно с сайта
    const SOURCE_PERSONAL_BRANCH = 2; //из личного кабинета
    const SOURCE_OPERATOR_POINT = 3; //по телефону

    /*
   * Delivery type
   * */
    const DELIVERY_TYPE_UNDEFINED = 0;
    const DELIVERY_TYPE_WAREHOUSE_WAREHOUSE = 1;
    const DELIVERY_TYPE_DOOR_DOOR = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transportation_order_lead';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_method','from_city_id', 'to_city_id', 'places', 'deleted', 'status', 'source', 'delivery_type'], 'integer'],
            [['from_city_id', 'places', 'weight', 'volume', 'customer_street', 'customer_house', 'customer_apartment', 'customer_floor', 'customer_name', 'customer_phone', 'recipient_street', 'recipient_house', 'recipient_apartment', 'recipient_floor', 'recipient_phone', 'recipient_name'], 'required'],
            [['customer_name', 'customer_street', 'recipient_street', 'recipient_name','recipient_name_2', 'package_description'], 'string', 'max' => 128],
            [['customer_comment'], 'string', 'max' => 255],
            [['customer_phone', 'customer_phone_2', 'recipient_phone', 'recipient_phone_2'], 'number', 'min' => 7],
            [['order_number'], 'unique'],
            [['weight','volume', 'cost', 'cost_vat','declared_value'], 'filter', 'filter' => function ($value) {
                $value = trim ($value);
                $value = str_replace(',','.', $value);
                return $value;
            }],
            ['customer_phone', function ($attribute, $params) {
                if (empty($this->client_id) && $user = User::find()->andWhere(['blocked_at'=>NULL, 'username' => $this->customer_phone])->one()) {
                    $this->addError($attribute, 'Пользователь с таким номером телефона уже существует');
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('client/forms', 'ID'),
            'from_city_id' => Yii::t('client/forms', 'City from'),
            'to_city_id' => Yii::t('client/forms', 'City to'),
            'customer_name' => Yii::t('client/forms', 'Customer Name'),
            'customer_phone' => Yii::t('client/forms', 'Customer Phone'),
            'customer_phone_2' => Yii::t('client/forms', 'Additional Customer Phone'),
            'customer_street' => Yii::t('client/forms', 'Street'),
            'customer_house' => Yii::t('client/forms', 'House'),
            'customer_apartment' => Yii::t('client/forms', 'Apartment'),
            'customer_floor' => Yii::t('client/forms', 'Floor'),
            'recipient_name' => Yii::t('client/forms', 'Recipient Name'),
            'recipient_name_2' => Yii::t('client/forms', 'Additional Recipient Name'),
            'recipient_phone' => Yii::t('client/forms', 'Recipient Phone'),
            'recipient_phone_2' => Yii::t('client/forms', 'Additional Recipient Phone'),
            'recipient_street' => Yii::t('client/forms', 'Street'),
            'recipient_house' => Yii::t('client/forms', 'House'),
            'recipient_apartment' => Yii::t('client/forms', 'Apartment'),
            'recipient_floor' => Yii::t('client/forms', 'Floor'),
            'delivery_type' => Yii::t('client/forms', 'Delivery type'),
            'places' => Yii::t('client/forms', 'Places'),
            'customer_comment' => Yii::t('client/forms', 'Customer Comment'),
            'weight' => Yii::t('client/forms', 'Weight(kg)'),
            'volume' => Yii::t('client/forms', 'Volume(м³)'),
            'declared_value' => Yii::t('client/forms', 'Declared Value'),
            'cost' => Yii::t('client/forms', 'Delivery cost'),
            'cost_vat' => Yii::t('client/forms', 'Delivery cost (VAT inc)'),
            'package_description' => Yii::t('client/forms', 'Package Description'),
            'status' => Yii::t('client/forms', 'Status'),
            'source' => Yii::t('client/forms', 'Order source'),
            'order_number' => Yii::t('client/forms', 'Order Number'),
            'created_user_id' => Yii::t('client/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('client/forms', 'Updated User ID'),
            'created_at' => Yii::t('client/forms', 'Created At'),
            'updated_at' => Yii::t('client/forms', 'Updated At'),
            'deleted' => Yii::t('client/forms', 'Deleted'),
        ];
    }

    /*
    * Relation has one with City
    * */
    public function getDeliveryProposal()
    {
        return $this->hasOne(TlDeliveryProposal::className(), ['transportation_order_lead_id' => 'id']);
    }

    /*
     * Relation has one with City
     * */
    public function getFromCity()
    {
        return $this->hasOne(City::className(), ['id' => 'from_city_id']);
    }

    /*
     * Relation has one with City
     * */
    public function getToCity()
    {
        return $this->hasOne(City::className(), ['id' => 'to_city_id']);
    }

    /**
     * @return array Массив с статусами.
     */
    public function getStatusArray()
    {
        return [
            self::STATUS_WAIT_FOR_CONFIRM => Yii::t('client/titles', 'Wait for confirm'),
            self::STATUS_CONFIRMED => Yii::t('client/titles', 'Confirmed'),
            self::STATUS_DONE => Yii::t('client/titles', 'Done'),
            self::STATUS_ON_ROUTE => Yii::t('client/titles', 'On route'),// в пути
            self::STATUS_DELIVERED => Yii::t('client/titles', 'Delivered'),// доставлен
        ];
    }

    /*
   * Relation with Client table
   * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return array Массив с источниками заявки
     */
    public function getSourceArray()
    {
        return [
            self::SOURCE_FRONTEND_PUBLIC => Yii::t('client/titles', 'Site public'),
            self::SOURCE_PERSONAL_BRANCH => Yii::t('client/titles', 'Personal branch'),
            self::SOURCE_OPERATOR_POINT => Yii::t('client/titles', 'Operator point'),
        ];
    }

    /**
     * @return array Массив с типами доставки
     */
    public static function getDeliveryTypeArray($key = null)
    {
        $data = [
            self::DELIVERY_TYPE_UNDEFINED => Yii::t('forms', 'Undefined'),
            self::DELIVERY_TYPE_WAREHOUSE_WAREHOUSE => Yii::t('forms', 'Warehouse-warehouse'),
            self::DELIVERY_TYPE_DOOR_DOOR => Yii::t('forms', 'Door-Door'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getDeliveryType($delivery_type=null)
    {
        if(is_null($delivery_type)){
            $delivery_type = $this->delivery_type;
        }
        return ArrayHelper::getValue($this->getDeliveryTypeArray(), $delivery_type);
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getSourceValue($source = null)
    {
        if(is_null($source)){
            $source = $this->source;
        }
        return ArrayHelper::getValue($this->getSourceArray(), $source);
    }

    /*
    * Relation has one with ExternalClientLead
    * */
    public function getExternalClient()
    {
        return $this->hasOne(ExternalClientLead::className(), ['id' => 'client_id']);
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatusValue($status = null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(), $status);
    }

    /**
     * @inheritdoc
     */
    public static function generateOrderNumber()
    {

        $index = Yii::$app->db->createCommand("SELECT MAX(id) + 1 FROM " .self::tableName())
            ->queryScalar();

        return $index+=100000000000;
    }

    /**
     * Добавляет точку используя информацию
     * об отправителе из предварительноый заявки
     *
     * @return int store_id
     */
    public function createPointFrom()
    {
        //VarDumper::dump($this->client_id); die;
        $exist = $this->checkForExistPoint(
            $this->client_id,
            $this->from_city_id,
            $this->customer_name,
            $this->customer_phone,
            $this->customer_street,
            $this->customer_house,
            $this->customer_floor,
            $this->customer_apartment
        );

        if(empty($exist)){
            $point = new Store();
            $point->client_id = $this->client_id;
            $point->country_id = 1;
            $point->city_id = $this->from_city_id;
            $point->type_use = Store::TYPE_USE_POINT;
            $point->owner_type = is_object($this->client) ? $this->client->client_type : Store::OWNER_TYPE_UNDEFINED;
            $point->contact_full_name = $this->customer_name;
            $point->phone_mobile = $this->customer_phone;
            $point->status = Store::STATUS_ACTIVE;
            $point->street = $this->customer_street;
            $point->house = $this->customer_house;
            $point->floor = $this->customer_floor;
            $point->flat = $this->customer_apartment;
            $point->comment = $this->customer_comment;

            if($point->save(false)){
                return $point->id;
            }
        }
        return $exist->id;
    }

    /**
     * Добавляет точку используя информацию
     * о получателе из предварительноый заявки
     *
     * @return int store_id
     */
    public function createPointTo()
    {
        $exist = $this->checkForExistPoint(
            $this->client_id,
            $this->to_city_id,
            $this->recipient_name,
            $this->recipient_phone,
            $this->recipient_street,
            $this->recipient_house,
            $this->recipient_floor,
            $this->recipient_apartment
        );

        if(empty($exist)){
            $point = new Store();
            $point->client_id = $this->client_id;
            $point->country_id = 1;
            $point->city_id = $this->to_city_id;
            $point->type_use = Store::TYPE_USE_POINT;
            $point->owner_type = is_object($this->client) ? $this->client->client_type : Store::OWNER_TYPE_UNDEFINED;
            $point->contact_full_name = $this->recipient_name;
            $point->phone_mobile = $this->recipient_phone;
            $point->status = Store::STATUS_ACTIVE;
            $point->street = $this->recipient_street;
            $point->house = $this->recipient_house;
            $point->floor = $this->recipient_floor;
            $point->flat = $this->recipient_apartment;
            $point->comment = $this->customer_comment;

            if($point->save(false)){
                return $point->id;
            }
        }
        return $exist->id;
    }

    /**
     * Возвращает точку с искомыми параметрами
     * @return mixed
     */
    public function checkForExistPoint($client_id, $city_id, $name, $phone, $street, $house, $floor, $app)
    {
        $point = Store::findOne([
            'client_id' => $client_id,
            'city_id' => $city_id,
            'contact_full_name' => $name,
            'phone_mobile' => $phone,
            'street' => $street,
            'house' => $house,
            'floor' => $floor,
            'flat' => $app,
        ]);

        return $point;
    }

    /**
     * Create delivery proposal based on transportation order
     * @param int $lead_order_id
     * @return mixed
     */
    public function createProposalFromLeadOrder()
    {
        if (!$this->client_id && $this->status==TransportationOrderLead::STATUS_WAIT_FOR_CONFIRM) {
            if ($client_id = CManager::createClientFromOrder($this->id)) {
                $this->client_id = $client_id;
                Yii::$app->getSession()->setFlash('success', Yii::t('client/messages', 'User {0} was created', [$this->customer_phone]));
            }
        }
            //VarDumper::dump($this->client_id, 10, true); die;
            $dp = new TlDeliveryProposal();
            $dp->scenario = 'confirm-frontend-order';
            $dp->client_id = $this->client_id;
            $dp->transportation_order_lead_id = $this->id;
            $dp->status = TlDeliveryProposal::STATUS_NEW;
            $dp->source = TlDeliveryProposal::SOURCE_DELLA_OPERATOR;
            $dp->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_TRANSFER;
            $dp->shipment_description = $this->package_description;
            $dp->declared_value = $this->declared_value;
            $dp->change_price = TlDeliveryProposal::CHANGE_AUTOMATIC_PRICE_YES;
            $dp->change_mckgnp = TlDeliveryProposal::CHANGE_AUTOMATIC_MC_KG_NP_NO;
            $dp->cash_no = TlDeliveryProposal::METHOD_CASH;
            //$dp->price_invoice_with_vat = $model->cost_vat;
            $dp->delivery_method = $this->delivery_type;
            $dp->kg = $this->weight;
            $dp->mc = $this->volume;
            $dp->kg_actual =  $this->weight;
            $dp->mc_actual = $this->volume;
            $dp->number_places = $this->places;
            $dp->number_places_actual = $this->places;
            $dp->route_from = $this->createPointFrom();
            $dp->route_to = $this->createPointTo();
            $dp->save();

            if($this->recipient_name_2){
                $dp->saveExtraFieldValue('recipient_name_2', $this->recipient_name_2);
            }
            if($this->recipient_phone_2){
                $dp->saveExtraFieldValue('recipient_phone_2', $this->recipient_phone_2);
            }
            if($this->customer_phone_2){
                $dp->saveExtraFieldValue('customer_phone_2', $this->customer_phone_2);
            }
            $this->status = TransportationOrderLead::STATUS_CONFIRMED;
            $this->save();

        $deliveryOrder = new  TlDeliveryProposalOrders();
        $deliveryOrder->client_id = $dp->client_id;
        $deliveryOrder->tl_delivery_proposal_id = $dp->id;
        $deliveryOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_UNDEFINED;
        $deliveryOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK;
        $deliveryOrder->number_places = $this->places;
        $deliveryOrder->number_places_actual = $this->places;
        $deliveryOrder->mc_actual = $this->volume;
        $deliveryOrder->mc = $this->volume;
        $deliveryOrder->kg_actual = $this->weight;
        $deliveryOrder->kg = $this->weight;
        $deliveryOrder->order_number = '[OPERATOR-DELLA-'.$dp->id.'-'.$this->id.']';
        $deliveryOrder->save(false);
        $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
        $dpManager->onCreateProposal();


        return $dp->id;
    }

    /**
     * Высчитывает стоимость доставки
     * @return string
     * */
    public function setDeliveryCost()
    {
        $kg = $this->weight;
        $mc = $this->volume;
        $index = BillingManager::getWeightVolumeIndex();
        $weightVolumeIndex = $mc * $index;
        if($this->from_city_id == $this->to_city_id){
            if($billing=TlDeliveryProposalBilling::findOne([
                'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                'from_city_id' => $this->from_city_id,
                'to_city_id' => $this->to_city_id,
                'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_BY_POINT,
            ])){
                    $this->cost_vat = $billing->price_invoice_with_vat;
                    $this->cost = $billing->price_invoice;
            }
        } else {
            if($weightVolumeIndex >= $kg){
                if($billing=TlDeliveryProposalBilling::findOne([
                    'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                    'from_city_id' => $this->from_city_id,
                    'to_city_id' => $this->to_city_id,
                    'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,
                ])){
                        $this->cost_vat = $weightVolumeIndex * $billing->price_invoice_kg_with_vat;
                        $this->cost = $weightVolumeIndex * $billing->price_invoice_kg;
                }
            } elseif ($weightVolumeIndex < $kg){
                if($billing=TlDeliveryProposalBilling::findOne([
                    'tariff_type'=> TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT,
                    'from_city_id' => $this->from_city_id,
                    'to_city_id' => $this->to_city_id,
                    'rule_type' => TlDeliveryProposalBilling::RULE_TYPE_CITY_BY_WEIGHT_VOLUME_INDEX,
                ])){
                        $this->cost_vat = $kg * $billing->price_invoice_kg_with_vat;
                        $this->cost = $kg * $billing->price_invoice_kg;
                }
            }
        }

    }
    // проверяем существует ли клиент, если нет создаем нового
    // клиента ищем по номеру телефона.

    public function findOrCreateClient(){}
    //  новых сотрудникам если их еще нет. создаем по фио и телефону
    public function findOrCreateClientEmployees(){}
    //  Создаем новую заявку на доставку
    public function createDeliveryProposal(){}
    //  Создаем новый заказ на доставку
    public function createDeliveryProposalOrder(){}
}
