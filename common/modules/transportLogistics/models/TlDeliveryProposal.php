<?php

namespace common\modules\transportLogistics\models;

use common\components\DeliveryProposalService;
use clientDepartment\modules\client\components\ClientManager;
use common\events\DpEvent;
use common\modules\billing\components\BillingManager;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\client\models\ClientEmployees;
use common\helpers\iHelper;
use common\modules\crossDock\models\CrossDock;
use common\modules\leads\models\ExternalClientLead;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use common\modules\store\models\StoreReviews;
use common\modules\transportLogistics\components\TLManager;
use Yii;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use common\models\ActiveRecord;
use common\modules\client\models\Client;
use common\modules\store\models\Store;
use app\modules\transportLogistics\transportLogistics;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\base\ModelEvent;
use common\components\MailManager;
use common\modules\transportLogistics\components\TLHelper;
use common\helpers\DateHelper;
use common\components\DeliveryProposalManager;


//use frontend\modules\transportLogistics\models\TlDeliveryRoutes;

/**
 * This is the model class for table "tl_delivery_proposals".
 *
 * @property integer $id
 * @property integer $delivery_method
 * @property integer $client_id
 * @property integer $external_client_lead_id
 * @property integer $transportation_order_lead_id
 * @property integer $source
 * @property integer $is_client_confirmed
 * @property integer $ready_to_invoicing
 * @property integer $route_from
 * @property integer $route_to
 * @property string  $sender_contact
 * @property integer  $sender_contact_id
 * @property string  $recipient_contact
 * @property integer  $recipient_contact_id
 * @property integer $company_transporter //  Помпиния перевозчикћ
 * @property integer $change_price
 * @property integer $change_mckgnp
 * @property integer $delivery_type //  Example: transfer
 * @property integer $car_id
 * @property integer $agent_id
 * @property string  $driver_name
 * @property string  $driver_phone
 * @property string  $driver_auto_number
 * @property integer $delivery_date // фактическая дата доставки в магазин
 * @property integer $expected_delivery_date // Предположительная дата доставки в магазин
 * @property integer $shipped_datetime // Фактическая дата отгрузки со склада, устанавливается когда напечатали ТТН
 * @property integer $accepted_datetime // Фактическая дата получения товара на склад. Должна устанавливаться работниками склада
 * @property string $mc
 * @property integer $mc_actual
 * @property integer $kg
 * @property integer $kg_actual
 * @property integer $volumetric_weight
 * @property integer $number_places
 * @property integer $number_places_actual
 * @property integer $cash_no
 * @property integer $price_invoice
 * @property string $price_invoice_with_vat
 * @property integer $status
 * @property integer $status_invoice
 * @property string $comment
 * @property string $extra_fields
 * @property string $bl_data
 * @property string $client_ttn
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $seal
 */
class TlDeliveryProposal extends ActiveRecord
{

//    public $delivery2day;

    /*
     * @var string Orders transported in delivery proposal
     * */
    public $orders;

    /*
     * @var string Who received order in delivery proposal
     * */
    public $whoReceivedOrder;

    /*
    * @var integer Company transporter
    * */
    const COMPANY_TRANSPORTER_NOMADEX = 0;
    const COMPANY_TRANSPORTER_RLC = 1; // теперь это компания Lomer point bridge. Для дефакто перевозки в белорусию
    const COMPANY_TRANSPORTER_APIS = 2;
    const COMPANY_TRANSPORTER_SCAANA = 3;
	const COMPANY_TRANSPORTER_EFFECTIVE_ENGINEERING = 4;

    /*
     * @var integer No change Price
     * */
    const CHANGE_AUTOMATIC_PRICE_YES = 1;
    const CHANGE_AUTOMATIC_PRICE_NO = 2;

    const CHANGE_AUTOMATIC_MC_KG_NP_YES = 1;
    const CHANGE_AUTOMATIC_MC_KG_NP_NO = 2;


    /*
     * @var integer delivery type
     * */
    const DELIVERY_TYPE_ONE_ROUTE = 1; // один маршрут --Не используется
    const DELIVERY_TYPE_MORE_ROUTE = 2; // Несколько под маршрутов точка->склад->точка1->точка2->конечный пунк --Не используется

    const DELIVERY_TYPE_INBOUND = 3; // Поступления
    const DELIVERY_TYPE_OUTBOUND = 4; // Отгрузки
    const DELIVERY_TYPE_RETURN = 5; // Возвраты
    const DELIVERY_TYPE_TRANSFER = 6; // Трансфер

    /*
     * @var integer status
     * */
    const STATUS_UNDEFINED = 0; //не указан
    const STATUS_NEW = 1; //новый
    const STATUS_ON_ROUTE = 2; //в дороге
    const STATUS_DELIVERED = 3; //доставлен
    const STATUS_DONE = 4;  //выполнен
    const STATUS_ADD_CAR = 5;  //добавлена машина
    const STATUS_ADD_ROUTE_TO_DP = 6;  //Добавьте маршрут к заявке
    const STATUS_ADD_CAR_TO_ROUTE = 7;  //Добавьте к маршруту машину
    const STATUS_ROUTE_FORMED = 8;  //Маршрут сформирован
    const STATUS_NOT_ADDED_M3 = 9;  //Не заполнен m3
    const STATUS_NOT_ADDED_M3_ON_ROUTE = 10;  //Не заполнен m3 в маршруте
    const STATUS_IN_PROCESSING_AT_WAREHOUSE = 11;  //В обработке на складе
    const STATUS_IN_TRANSFER_FROM_POINT = 12;  //транспортировка из точки

    /*
     * @var integer source
     * */
    const SOURCE_UNDEFINED = 0;  //Не определен
    const SOURCE_CLIENT = 1;  //Клиент
    const SOURCE_OUR_OPERATOR = 2;  //Наш оператор
    const SOURCE_API = 3;  // API
    const SOURCE_FRONTEND = 4;  // Frontend
    const SOURCE_POINT_OPERATOR = 5;  // Frontend
    const SOURCE_AUTO_CROSS_DOCK = 6;  // Auto Cross Dock
    const SOURCE_AUTO_OUTBOUND = 7;  // Auto outbound
    const SOURCE_AUTO_CUSTOMS = 8;  // Auto outbound
    const SOURCE_DELLA_OPERATOR = 9;  // Делла оператор

    /*
    * @var integer delivery method
     * Тип доставки (склад-склад, дверь-дверь)
    * */
    const DELIVERY_METHOD_UNDEFINED = 0;  //Не определен
    const DELIVERY_METHOD_WAREHOUSE_WAREHOUSE= 1;  //Склад-склад
    const DELIVERY_METHOD_DOOR_DOOR = 2;  //Дверь-дверь


    /*
     * @var integer is_client_confirmed
     * */
    const IS_CLIENT_CONFIRMED_NO_NEED = 0; // Не нужно подтверждения от клиента
    const IS_CLIENT_CONFIRMED_YES = 1; // ДА
    const IS_CLIENT_CONFIRMED_NO = 2; // НЕТ
    const IS_CLIENT_CONFIRMED_WAITING = 3; // Ожидает подтверждения от клиента

    /*
     * @var
     * */
    const EVENT_PRINT_TTN   = 'eventPrintTtn';
    const EVENT_RECALCULATE   = 'eventRecalculate';

    /*
    * @var integer ready_to_invoicing
    * */
    const READY_TO_INVOICING_NO = 0;
    const READY_TO_INVOICING_YES   = 1;

    /*
    * @var integer ready_to_invoicing
    * */
    const TRANSPORT_TYPE_TENT = 1; // тент
    const TRANSPORT_TYPE_KRITAY = 2; // крытая

    /*
    * @var integer type_loading Тип погрузки
    * */
    const TRANSPORT_TYPE_LOADING_UNDEFINED = 0; // Не определено
    const TRANSPORT_TYPE_LOADING_TOP = 1; // Сверху
    const TRANSPORT_TYPE_LOADING_BACK = 2; // Сзади
    const TRANSPORT_TYPE_LOADING_SIDE = 3; // Сбоку


    /*
    * @var integer type_loading Кто платит за достаку
    * */
    const TRANSPORT_WHO_PAYS_UNDEFINED = 0; // не определено
    const TRANSPORT_WHO_PAYS_SENDER = 1; // отправитель
    const TRANSPORT_WHO_PAYS_RECIPIENT = 2; // получатель
    const TRANSPORT_WHO_PAYS_BY_CONTRACT = 3; // по договору
    const TRANSPORT_WHO_PAYS_BY_THIRD_PART = 4; // Платит третья сторона
    const TRANSPORT_WHO_PAYS_50 = 5; // 50%


    const SECURE_REVIEW_CODE_PREFIX = '-0138';


    public function getSecureReviewCodePrefix() {
        return $this->id.self::SECURE_REVIEW_CODE_PREFIX;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposals';
    }

    /*
     *
     * */
    public function init()
    {
        parent::init();
        $this->change_mckgnp = self::CHANGE_AUTOMATIC_MC_KG_NP_YES;
        $this->change_price = self::CHANGE_AUTOMATIC_PRICE_YES;

//        $this->on(self::EVENT_PRINT_TTN,[$this,'eventPrintTtn']);
//        $this->on(self::EVENT_RECALCULATE,[$this,'eventRecalculate']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number_places','client_id', 'route_from', 'route_to','cash_no'],'required','on'=>['create-on-client']], // 'mc',
            [['client_id', 'route_from', 'route_to','cash_no'],'required','on'=>['create-update-manager-warehouse']], // 'mc',
            [['change_price', 'external_client_lead_id', 'transportation_order_lead_id', 'change_mckgnp','delivery_type', 'delivery_method', 'agent_id','car_id','company_transporter','is_client_confirmed','source','client_id', 'route_from', 'route_to', 'number_places', 'number_places_actual', 'cash_no',  'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['mc','mc_actual', 'kg', 'kg_actual','volumetric_weight'], 'number'],
            [['price_our_profit', 'price_expenses_with_vat', 'price_expenses_cache', 'price_expenses_total', 'price_invoice_with_vat','price_invoice', 'declared_value'], 'number'],
            [['extra_fields','bl_data','comment','driver_name','driver_phone','driver_auto_number', 'shipment_description', 'seal'], 'string'],
            [['shipped_datetime','accepted_datetime','delivery_date','expected_delivery_date','sender_contact','sender_contact_id','recipient_contact','recipient_contact_id'], 'safe'],
            [['status'], 'default', 'value' => self::STATUS_NEW],
            [['is_client_confirmed'], 'default', 'value' => self::IS_CLIENT_CONFIRMED_NO_NEED],
            [['company_transporter'], 'default', 'value' => self::COMPANY_TRANSPORTER_EFFECTIVE_ENGINEERING],
            [['price_our_profit','price_expenses_with_vat','price_expenses_cache','price_expenses_total','price_invoice_with_vat','price_invoice',], 'default', 'value' => 0],
            [['status'],function ($attr) {
                if ($this->$attr == TlDeliveryProposal::STATUS_DELIVERED && (empty($this->delivery_date) || $this->delivery_date == '0000-00-00 00:00:00') ) {
                    $this->addError($attr, \Yii::t('transportLogistics/errors', 'Please enter delivery date'));
                }
            },'on'=>['create-on-client','create-update-manager-warehouse','mass-update']],
            [['change_price'], 'default', 'value' => self::CHANGE_AUTOMATIC_PRICE_YES],
            [['change_mckgnp'], 'default', 'value' => self::CHANGE_AUTOMATIC_MC_KG_NP_YES],
            [['route_from'], 'compare', 'compareAttribute'=>'route_to', 'operator'=>'!='],
            [['route_to'], 'compare', 'compareAttribute'=>'route_from', 'operator'=>'!='],
            [['client_ttn'], 'string'],
        ];
    }

    /*
     *
     * */
    public function scenarios() {
        return [
            'default'=>['status','change_price','change_mckgnp','delivery_type','number_places','client_id', 'route_from', 'route_to','cash_no','comment','driver_name','driver_phone','driver_auto_number'],
            'create-on-client'=>['delivery_date','change_price','change_mckgnp','delivery_type','expected_delivery_date','mc','mc_actual', 'kg', 'kg_actual','number_places','number_places_actual','client_id', 'route_from', 'route_to','cash_no','comment'],
            'create-update-manager-warehouse'=>[
                'number_places',
                'number_places_actual',
                'client_id',
                'route_from',
                'route_to',
                'cash_no',
                'mc',
                'mc_actual',
                'kg',
                'kg_actual',
                'shipped_datetime',
                'accepted_datetime',
                'delivery_date',
                'delivery_method',
                'expected_delivery_date',
                'price_invoice',
                'price_invoice_with_vat',
                'status_invoice',
                'status',
                'company_transporter',
                'comment',
                'driver_name',
                'driver_phone',
                'driver_auto_number',
                'agent_id',
                'car_id',
                'delivery_type',
                'change_price',
                'change_mckgnp',
                'status',
                'seal',
                'declared_value',
                'shipment_description',
//                'sender_contact',
//                'sender_contact_id',
//                'recipient_contact',
//                'recipient_contact_id'
            ],
            'mass-update'=>[
                'cash_no',
                'status_invoice',
                'status',
            ],
            'confirm-frontend-order'=>[
                'mc',
                'kg',
                'number_places',
                'status',
                'source',
                'change_price',
                'delivery_method',
                'declared_value',
                'shipment_description',
                'change_mckgnp',
                'cash_no',
                'price_our_profit',
                'price_expenses_with_vat',
                'price_expenses_cache',
                'price_expenses_total',
                'price_invoice_with_vat',
                'price_invoice',
                'external_client_lead_id',
                'transportation_order_lead_id',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'client_ttn' => Yii::t('transportLogistics/forms', 'ТТН Клиента'),
            'sender_contact' => Yii::t('transportLogistics/forms', 'Sender contact'),
//            'sender_contact_id' => Yii::t('transportLogistics/forms', 'sender_contact_id'),
            'recipient_contact' => Yii::t('transportLogistics/forms', 'Recipient contact'),
//            'recipient_contact_id' => Yii::t('transportLogistics/forms', 'recipient_contact_id'),

            'city_to' => Yii::t('transportLogistics/forms', 'City to'),
            'region_to' => Yii::t('transportLogistics/forms', 'Region to'),
            'country_to' => Yii::t('transportLogistics/forms', 'Country to'),
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'orders' => Yii::t('transportLogistics/forms', 'Orders'),
            'source' => Yii::t('transportLogistics/forms', 'Source'),
            'client_id' => Yii::t('transportLogistics/forms', 'Client ID'),
            'is_client_confirmed' => Yii::t('transportLogistics/forms', 'Is client confirmed'),
            'route_from' => Yii::t('transportLogistics/forms', 'Route From'),
            'route_to' => Yii::t('transportLogistics/forms', 'Route To'),
            'company_transporter' => Yii::t('titles', 'Shipping company'),
            'delivery_type' => Yii::t('transportLogistics/forms', 'Delivery type'),
            'delivery_method' => Yii::t('transportLogistics/forms', 'Delivery method'),
            'change_price' => Yii::t('transportLogistics/forms', 'Change price'),
            'change_mckgnp' => Yii::t('transportLogistics/forms', 'Change mc, kg, np'),
            'declared_value' => Yii::t('transportLogistics/forms', 'Declared value'),
            'shipment_description' => Yii::t('transportLogistics/forms', 'Shipment description'),

            'agent_id' => Yii::t('titles', 'Subcontractor'),
            'car_id' => Yii::t('titles', 'Car'),
            'driver_name' => Yii::t('titles', 'Driver name'),
            'driver_phone' => Yii::t('titles', 'Driver phone'),
            'driver_auto_number' => Yii::t('titles', 'Driver auto number'),

            'delivery_date' => Yii::t('transportLogistics/forms', 'Delivery Date'),
            'expected_delivery_date' => Yii::t('titles', 'Expected delivery date'),
            'accepted_datetime' => Yii::t('transportLogistics/forms', 'Accepted date'),
            'shipped_datetime' => Yii::t('transportLogistics/forms', 'Shipped date'),
            'mc' => Yii::t('transportLogistics/forms', 'Mc'),
            'mc_actual' => Yii::t('transportLogistics/forms', 'Mc Actual'),
            'kg' => Yii::t('transportLogistics/forms', 'kg'),
            'kg_actual' => Yii::t('transportLogistics/forms', 'Kg Actual'),
            'volumetric_weight' => Yii::t('transportLogistics/forms', 'Volumetric weight'),
            'number_places' => Yii::t('transportLogistics/forms', 'Number Places'),
            'number_places_actual' => Yii::t('transportLogistics/forms', 'Number Places Actual'),
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
            'price_expenses_total' => Yii::t('transportLogistics/forms', 'Price expenses total'),
            'price_expenses_cache' => Yii::t('transportLogistics/forms', 'Price expenses cache'),
            'price_expenses_with_vat' => Yii::t('transportLogistics/forms', 'Price expenses with vat'),
            'price_our_profit' => Yii::t('transportLogistics/forms', 'Price our profit'),
            'seal' => Yii::t('transportLogistics/forms', 'Seal'),

        ];
    }


    /**
     * @return array Массив с статусами.
     */
    public static function getDeliveryMethodArray($key = null)
    {
//        $data = [
//            self::DELIVERY_METHOD_UNDEFINED => Yii::t('transportLogistics/forms', 'Undefined'), //Не определен
//            self::DELIVERY_METHOD_WAREHOUSE_WAREHOUSE => Yii::t('forms', 'Warehouse-warehouse'),
//            self::DELIVERY_METHOD_DOOR_DOOR => Yii::t('forms', 'Door-Door'),
//        ];

        $data = TlDeliveryProposalBilling::getDeliveryTypeArray();

        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус.
     */
    public function getDeliveryMethod($delivery_method=null)
    {
        if(is_null($delivery_method)){
            $delivery_method = $this->delivery_method;
        }
        return ArrayHelper::getValue($this->getDeliveryMethodArray(), $delivery_method);
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray($key = null)
    {
        $data = [
            self::STATUS_UNDEFINED => Yii::t('transportLogistics/forms', 'Undefined'), //Не определен
            self::STATUS_NEW => Yii::t('transportLogistics/forms', 'New'), //Новый
            self::STATUS_ON_ROUTE => Yii::t('transportLogistics/forms', 'On route'), //В пути
            self::STATUS_DELIVERED => Yii::t('transportLogistics/forms', 'Delivered'), //Доставлен
            self::STATUS_DONE => Yii::t('transportLogistics/forms', 'Done'),  //Выполнен
            self::STATUS_ADD_CAR => Yii::t('titles', 'Add car'),  //Добавлена машина
            self::STATUS_ADD_ROUTE_TO_DP => Yii::t('titles', 'Add route to proposal'),  //Добавьте маршрут к заявке
            self::STATUS_ADD_CAR_TO_ROUTE => Yii::t('titles', 'Add car to route'),  //Добавьте к маршруту машину
            self::STATUS_ROUTE_FORMED => Yii::t('titles', 'Route formed'),  //Маршрут сформирован
            self::STATUS_NOT_ADDED_M3 => Yii::t('titles', 'Not added m3'),  //Не заполнен m3
            self::STATUS_IN_PROCESSING_AT_WAREHOUSE => Yii::t('titles', 'In processing at warehouse'),  //В обработке на складе
            self::STATUS_IN_TRANSFER_FROM_POINT => Yii::t('titles', 'Transfer from point'),  //Транспортировка из точки
        ];

        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус.
     */
    public function getStatusValue($status=null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(),$status);
    }

    /**
     * @return array Массив с источнико получения заявки.
     */
    public static function getSourceArray($key = null)
    {
        $data = [
            self::SOURCE_UNDEFINED => Yii::t('transportLogistics/titles', 'Undefined'), //Не определен
            self::SOURCE_CLIENT => Yii::t('transportLogistics/titles', 'Client'), //Клиент
            self::SOURCE_OUR_OPERATOR => Yii::t('transportLogistics/titles', 'Our operator'), //Наш операто
            self::SOURCE_API => Yii::t('transportLogistics/titles', 'API'), //API
            self::SOURCE_FRONTEND => Yii::t('transportLogistics/titles', 'Frontend'), //API
            self::SOURCE_POINT_OPERATOR => Yii::t('transportLogistics/titles', 'Point operator'), //API
            self::SOURCE_AUTO_CROSS_DOCK => Yii::t('transportLogistics/titles', 'Auto Cross-Dock'), //auto with cross dock order
            self::SOURCE_AUTO_OUTBOUND => Yii::t('transportLogistics/titles', 'Auto Outbound'), //auto with cross dock order
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус.
     */
    public function getSourceValue($source=null)
    {
        if(is_null($source)){
            $source = $this->source;
        }
        return ArrayHelper::getValue(self::getSourceArray(), $source);
    }

    /*
    * @return array with store id=>title
    */
    public static function getRouteFromTo($key = null)
    {
        $data = ArrayHelper::map(Store::find()->orderBy('title')->all(), 'id', 'title');

        return isset($data[$key]) ? $data[$key] : '-';
    }


    /*
    * Relation has many with DeliveryProposalOrders
    * */
    public function getProposalOrders()
    {
        return $this->hasMany(TlDeliveryProposalOrders::className(), ['tl_delivery_proposal_id' => 'id']);
    }

    /*
    * Relation has many with DeliveryRoutes
    * */
    public function getProposalRoutes()
    {
        return $this->hasMany(TlDeliveryRoutes::className(), ['tl_delivery_proposal_id' => 'id']);
    }

    /*
    * Relation has one with Client
    * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
   * Relation has one with external client lead
   * */
    public function getExternalClientLead()
    {
        return $this->hasOne(ExternalClientLead::className(), ['id' => 'external_client_lead_id']);
    }

    /*
     * Relation has One with Store
     *
     * */
    public function getRouteFrom()
    {
        return $this->hasOne(Store::className(), ['id' => 'route_from']);
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getRouteTo()
    {
        return $this->hasOne(Store::className(), ['id' => 'route_to'])->with('city');
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
     * Recalculate Expenses delivery proposal order
     * @param array $toRun Example: ['change_mckgnp'=>true,'change_price'=>false,'updateCascadedMcKgNp'=>true]
     * */
//    public function recalculateExpensesOrder($toRun = [])
//    {
//////        VarDumper::dump($toRun,10,true);
//////        die('--recalculateExpensesOrder---');
//////
////        //S: Set Price Invoice
////        if($this->change_price == self::CHANGE_AUTOMATIC_PRICE_YES && !in_array($this->status_invoice,[self::INVOICE_PAID,self::INVOICE_SET]) ) {
////
////            $bm = new BillingManager();
////
//////            if($price_invoice_with_vat = $bm->getInvoicePriceForDP($this)) {
////                $price_invoice_with_vat = $bm->getInvoicePriceForDP($this);
////                $this->price_invoice = $bm->getInvoicePriceForDP($this, false);
////                $this->price_invoice_with_vat = $price_invoice_with_vat;
//////            }
////        }
//        //E: Set Price Invoice
//
//
////        if ($routes = $this->getProposalRoutes()->all()) {
////            foreach ($routes as $route) {
////                $route->recalculateExpensesRoute();
////            }
////        }
//
////        if ($routes = $this->getProposalRoutes()->all()) {
////
////            $priceExpensesCache = 0;
////            $priceExpensesWithVatTotal = 0;
////
////            foreach ($routes as $model) {
////                $priceExpensesCache += $model->price_invoice;
////                $priceExpensesWithVatTotal += $model->price_invoice_with_vat;
////            }
////
////            $this->price_expenses_total = $priceExpensesCache + $priceExpensesWithVatTotal;
////            $this->price_expenses_with_vat = $priceExpensesWithVatTotal;
////            $this->price_expenses_cache = $priceExpensesCache;
////
////            if(!empty($this->price_invoice)) {
////                $this->price_our_profit = $this->price_invoice - $this->price_expenses_total;
////            }
////
////        } else {
////            $this->price_expenses_total = 0;
////            $this->price_expenses_with_vat = 0;
////            $this->price_expenses_cache = 0;
////        }
//
//        //S: Calculate sum m3, kg and places
//        $mc = 0;
//        $kg = 0;
//        $number_places = 0;
//        $orderNumbers = '';
//        if($orders = $this->getProposalOrders()->all()) {
//            foreach($orders as $order) {
//
//                if($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK){
//
//                    if($mainOrder = $order->crossDockOrder){
//                        //Обновляем информацию про вес, обьем, места в связанной записи в OutboundOrders или Cross Dock
////                        $mainOrder->mc = $order->mc;
////                        $mainOrder->kg = $order->kg;
//                        $mainOrder->accepted_number_places_qty = $order->number_places;
//                        $mainOrder->save(false);
//                    }
//                } elseif($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
//
//                    if($mainOrder = $order->outboundOrder){
//                        //Обновляем информацию про вес, обьем, места в связанной записи в OutboundOrders или Cross Dock
//                        $mainOrder->order_number = $order->order_number;
//                        $mainOrder->title = $order->title;
//                        $mainOrder->description = $order->description;
//                        $mainOrder->mc = $order->mc;
//                        $mainOrder->kg = $order->kg;
//                        $mainOrder->accepted_number_places_qty = $order->number_places;
//                        $mainOrder->save(false);
//                    }
//                }
//
//                $mc += $order->mc;
//                $kg += $order->kg;
//                $number_places += $order->number_places;
//                $orderNumbers .= $order->order_number.', ';
//            }
//
//            $orderNumbers = trim($orderNumbers,', ');
//        }
//
//        if( $this->change_mckgnp == self::CHANGE_AUTOMATIC_MC_KG_NP_YES ) {
//            $this->kg = $kg;
//            $this->mc = $mc;
//            $this->number_places = $number_places;
//
//            $this->kg_actual = $kg;
//            $this->mc_actual = $mc;
//            $this->number_places_actual = $number_places;
//
//        }
//
//        //E: Calculate sum m3, kg and places
//
//        //S: Save extra fields
//        $this->saveExtraFieldValue('orders',$orderNumbers);
//        //E: Save extra fields
//
//        //S:
//        $this->CreateModifyBusinessLogicData();
//        //E:
//
//        $this->save(false);
//
//        //S: Cascade Update KG,MC, NP
////        VarDumper::dump($toRun,10,true);
////        echo "<pre>";
////        var_dump((isset($toRun['updateCascadedMcKgNp']) && $toRun['updateCascadedMcKgNp'] == true)
////            || ( !isset($toRun['updateCascadedMcKgNp'])));
////        echo "<br />";
////        var_dump(isset($toRun['updateCascadedMcKgNp']));
////        var_dump((isset($toRun['updateCascadedMcKgNp']) && $toRun['updateCascadedMcKgNp'] == true));
////        die('<br />---Cascade Update KG,MC, NP--<br />');
////        if( (isset($toRun['updateCascadedMcKgNp']) && $toRun['updateCascadedMcKgNp'] == true)
////            || ( !isset($toRun['updateCascadedMcKgNp']) )  ) {
////
////            VarDumper::dump($toRun,10,true);
////            die('-----');
//            $this->updateCascadedMcKgNp();
////        }
//        //E: Cascade Update KG,MC, NP
//
//        return true;
//    }

    /*
     * Get count proposals is waiting confirm
     * */
    public static function getCountIsWaitingConfirm()
    {
        // S: Create function
        $client_id = null;
        $route_to = null;
        if(!Yii::$app->user->isGuest) {
//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if ($client = ClientManager::getClientByUserID()) {
                    switch ($client->manager_type) {
                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $client_id = $client->client_id;
                            break;
                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $route_to = $client->store_id;
                            $client_id = $client->client_id;
                            break;
                        default:
                            break;
                    }
                }


            }
//        }

        // E: Create function
        $q = static::find();
        $q->where(['is_client_confirmed'=>self::IS_CLIENT_CONFIRMED_WAITING,'deleted' => self::NOT_SHOW_DELETED]);
        $q->andFilterWhere([
            'client_id' => $client_id,
            'route_to' => $route_to,
        ]);



        return $q->count();
    }


    /*
    * After save add order to route order
    * */
    public function afterSave( $insert, $changedAttributes )
    {
        $storeType = Store::find()->where(['id'=>$this->route_to,'type_use'=>Store::TYPE_USE_STORE])->count();
        $storeReview = StoreReviews::find()->where(['tl_delivery_proposal_id'=>$this->id])->count();

        if( !empty($storeType) && empty($storeReview) ) {

            $storeReview = new StoreReviews();
            $storeReview->tl_delivery_proposal_id = $this->id;
            $storeReview->store_id = $this->route_to;
            $storeReview->client_id = $this->client_id;
            $storeReview->save(false);
        }

        //S:
        if ( (isset($changedAttributes['expected_delivery_date'])) ) {
            $mm =  new MailManager();
            if($mm->sendNewDeliveryProposalMessage($this)) {
            } else {
//                     echo "ypa";
//                     die('--STOP--');
            }
        }
        //E:

//        VarDumper::dump($changedAttributes,10,true);
//        die;

//        $e = new DpEvent();
//        $e->deliveryProposalId = $this->id;
//        $this->trigger(self::EVENT_RECALCULATE,$e);
//        $this->updateCascadedMcKgNp();
//        //Фиксируем и устанавливаем дату доставки для связаных заказов
//        if($this->delivery_date){
//            if ($relatedOrders = $this->proposalOrders) {
//                foreach ($relatedOrders as $order) {
//                    if($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT){
//                        if ($oo = $order->outboundOrder) {
//                            $oo->date_delivered = $this->delivery_date;
//                            $oo->save(false);
//                        }
//                    } elseif ($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK){
//                        if ($cd = $order->crossDockOrder) {
//                            $cd->date_delivered = $this->delivery_date;
//                            $cd->save(false);
//                        }
//                    }
//
//                }
//            }
//        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /*
     *
     * */
//    public function eventRecalculate($event = null)
//    {
//        Event::off(TlDeliveryProposal::className(),TlDeliveryProposal::EVENT_RECALCULATE);
//        if(!is_null($event)) {
//            TLManager::recalculateDpAndDpr($event->deliveryProposalId);
////            TLManager::recalculateDpAndDpr($event->deliveryProposalId, $event->deliveryRouteId);
//        }
//    }

    /*
     * This event usually trigger after
     * actionPrintTtn
     *
     **/
    public function eventPrintTtn($event = null)
    {
        if (!is_null($event)) {
                $dpManager = new DeliveryProposalManager(['id'=>$event->deliveryProposalId]);
                $dpManager->onPrintTtn();
        }
    }

    /*
     * This method is called at the beginning of inserting or updating a record.
     *
     * */
    public function beforeSave($insert)
    {
         if (parent::beforeSave($insert)) {

             //TODO сделать это через настройки значений по умолчанию для клиента
             if($insert && in_array($this->client_id,[4])) { // 4 = ID client Sharuakaz
                 $this->change_price = self::CHANGE_AUTOMATIC_PRICE_NO;
             }

            if($this->status_invoice == self::INVOICE_PAID) {
                $this->status = self::STATUS_DONE;
            }


             //S:
             $status = $this->getAttribute('status');
             $statusOld = $this->getOldAttribute('status');
             if ( $status != $statusOld
                 && $status == TlDeliveryProposal::STATUS_ROUTE_FORMED
                 && Store::find()->where('type_use = :type_use and id = :id',[':type_use'=>Store::TYPE_USE_STORE,':id'=>$this->route_from])->count()
             ) {
                 $mm =  new MailManager();
                 if($mm->sendIfStatusRouteFormattedDeliveryProposalToMessage($this)) {

                 } else {
//                     echo "ypa";
//                     die('--STOP--');
                 }
             }
             //E:
             //S: Set volumetric weight
             $this->volumetric_weight = DeliveryProposalManager::getVolumetricWeight($this->mc_actual);
             //E: Set volumetric weight

             //S:
//             $dpService = new DeliveryProposalService();
             $this->sender_contact = $this->prepareContactToStr($this->sender_contact_id);
             $this->recipient_contact = $this->prepareContactToStr($this->recipient_contact_id);
             //E:

             return true;
         } else {
             return false;
         }
    }

    /*
 *
 * @param integer $id
 * @return string
 * */
    public function prepareContactToStr($id)
    {
        $out = '';
        if($ce = ClientEmployees::findOne($id)) {
            $out = $ce->full_name.' Тел: '.Yii::$app->formatter->asPhone($ce->phone).' Тел2: '.Yii::$app->formatter->asPhone($ce->phone_mobile);
        }

        return $out;
    }

    /*
     * Set cascaded status
     * return void
     * */
//    public function setCascadedStatus()
//    {
//        //s: Если у заявки нет маршрута
//        $routsAndCars = [];
//
////        $status = $this->status;
//        $status = '';
//        if($routeItems = $this->getProposalRoutes()->all()) {
//            foreach ($routeItems as $rItem) {
//                $routsAndCars[$rItem->id] = $rItem->getCarItems()->count();
//            }
//        }
//
//        // Если у заявки нет ни одного маршрута
//        if(empty($routsAndCars)) {
//            $status = self::STATUS_ADD_ROUTE_TO_DP;
//        } elseif(in_array('0',$routsAndCars)) {
//            // Проверяем если у маршрутов  количество машин ровно 0
//            $status = self::STATUS_ADD_CAR_TO_ROUTE;
//        }
////        else {
////            $status = self::STATUS_ROUTE_FORMED;
////        }
//
//        // E: Если у заявки нет маршрута
//
//        // Проверяем в маршрутах заполнены ли поля m3
//        if(empty($this->mc) && in_array($this->client_id,[2])) {
//            $status = self::STATUS_NOT_ADDED_M3;
//        }
//
//        if(empty($status)) {
//            $status = self::STATUS_ROUTE_FORMED;
//        }
//
//        $validArray = [
//            self::STATUS_ON_ROUTE,
//            self::STATUS_DELIVERED,
//            self::STATUS_DONE,
//        ];
//
//        if($status != self::STATUS_ROUTE_FORMED) {
//            $this->status = $status;
//        } elseif(!in_array($this->status,$validArray)) {
//            $this->status = self::STATUS_ROUTE_FORMED;
//        }
//
//        $this->save(false);
//
//
//        // STATUS_UNDEFINED = 0; //не указан
//        // STATUS_NEW = 1; //новый
//        // STATUS_ON_ROUTE = 2; //в дороге
//        // STATUS_DELIVERED = 3; //доставлен
//        // STATUS_DONE = 4;  //выполнен
//        // STATUS_ADD_CAR = 5;  //добавлена машина
//        // STATUS_ADD_ROUTE_TO_DP = 6;  //Добавьте маршрут к заявке
//        // STATUS_ADD_CAR_TO_ROUTE = 7;  //Добавьте к маршруту машину
//        // STATUS_ROUTE_FORMED = 8;  //Маршрут сформирован
//        // STATUS_NOT_ADDED_M3 = 9;  //Не заполнен m3
//        // STATUS_NOT_ADDED_M3_ON_ROUTE = 10;  //Не заполнен m3 в маршруте
//
//
//        switch($this->status) {
//
//            case self::STATUS_ROUTE_FORMED:
//
//                if($routeItems = $this->getProposalRoutes()->all()) {
//                    foreach ($routeItems as $rItem ) {
//                        if($carItems = $rItem->getCarItems()->all()) {
//                            foreach ($carItems as $cItem) {
//                                $cItem->status = TlDeliveryProposalRouteCars::STATUS_CAR_ADDED_TO_ROUTE;
//                                $cItem->shipped_datetime = $this->shipped_datetime;
//                                $cItem->accepted_datetime = $this->accepted_datetime;
//                                $cItem->delivery_date = $this->delivery_date;
//
//                                $cItem->save(false);
//                            }
//                        }
//                        $rItem->status = self::STATUS_ROUTE_FORMED;
//                        $rItem->save(false);
//                    }
//                }
//                break;
//
//            case self::STATUS_NEW:
//
//                if($routeItems = $this->getProposalRoutes()->all()) {
//                    foreach ($routeItems as $rItem ) {
//                        if($carItems = $rItem->getCarItems()->all()) {
//                            foreach ($carItems as $cItem) {
//                                $cItem->status = TlDeliveryProposalRouteCars::STATUS_CAR_ADDED_TO_ROUTE;
//                                $cItem->shipped_datetime = $this->shipped_datetime;
//                                $cItem->accepted_datetime = $this->accepted_datetime;
//                                $cItem->delivery_date = $this->delivery_date;
//                                $cItem->save(false);
//                            }
//                        }
//                        $rItem->status = self::STATUS_NEW;
//                        $rItem->save(false);
//                    }
//
//                }
//                break;
//
//            case self::STATUS_ON_ROUTE: // в пути
//                // Нати все под пути и поставить ох в статус в пути
//                // В машинах которые прикреплены к путям поставить статус в пути
//
//                //S: create function
//                if($routeItems = $this->getProposalRoutes()->all()) {
//                    foreach ($routeItems as $rItem ) {
//                        if($carItems = $rItem->getCarItems()->all()) {
//                            foreach ($carItems as $cItem) {
//                                $cItem->status = TlDeliveryProposalRouteCars::STATUS_ON_ROUTE;
//                                $cItem->shipped_datetime = $this->shipped_datetime;
//                                $cItem->accepted_datetime = $this->accepted_datetime;
//                                $cItem->delivery_date = $this->delivery_date;
//                                $cItem->save(false);
//                            }
//                        }
//                        $rItem->status = self::STATUS_ON_ROUTE;
//                        $rItem->save(false);
//                    }
//
//                }
//
////                  $this->setCascadedStatus('STATUS_ON_ROUTE');
//
//                //E: create function
//
//                break;
//
//            case self::STATUS_DELIVERED: // Доставлен
//                // TODO нужно эту логику засунуть в отдельный метод или компонет
//
//                //S: create function
//                if($routeItems = $this->getProposalRoutes()->all()) {
//                    foreach ($routeItems as $rItem ) {
//                        if($carItems = $rItem->getCarItems()->all()) {
//                            foreach ($carItems as $cItem) {
//                                $cItem->status = TlDeliveryProposalRouteCars::STATUS_DELIVERED;
//                                $cItem->shipped_datetime = $this->shipped_datetime;
//                                $cItem->accepted_datetime = $this->accepted_datetime;
//                                $cItem->delivery_date = $this->delivery_date;
//                                $cItem->save(false);
//                            }
//                        }
//                        $rItem->status = self::STATUS_DELIVERED;
//                        $rItem->save(false);
//                    }
//                }
//
//                if ($orderItems = $this->getProposalOrders()->all()) {
//                    foreach ($orderItems as $oItem) {
//                        if ($oItem->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK) {
//                            if ($order = $oItem->crossDockOrder) {
//                                $order->status = Stock::STATUS_OUTBOUND_DELIVERED;
//                                $order->cargo_status = OutboundOrder::CARGO_STATUS_DELIVERED;
//                                $order->save(false);
//                            }
//                        } elseif($oItem->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
//                            if ($order = $oItem->outboundOrder) {
//                                $order->status = Stock::STATUS_OUTBOUND_DELIVERED;
//                                $order->cargo_status = OutboundOrder::CARGO_STATUS_DELIVERED;
//                                $order->save(false);
//                            }
//                        }
//                    }
//                }
//                //E: create function
//
//                break;
//
//            case self::STATUS_DONE: // выполнен
//
//                //S: create function
//                if($routeItems = $this->getProposalRoutes()->all()) {
//                    foreach ($routeItems as $rItem ) {
//                        if($carItems = $rItem->getCarItems()->all()) {
//                            foreach ($carItems as $cItem) {
//                                $cItem->status = TlDeliveryProposalRouteCars::STATUS_DONE;
//                                $cItem->shipped_datetime = $this->shipped_datetime;
//                                $cItem->accepted_datetime = $this->accepted_datetime;
//                                $cItem->delivery_date = $this->delivery_date;
//                                $cItem->save(false);
//                            }
//                        }
//                        $rItem->status = self::STATUS_DONE;
//                        $rItem->save(false);
//                    }
//                }
//                //E: create function
//
//                break;
//
//            default:
//                break;
//        }
//    }

    /**
     * @return array .
     */
    public static function getCompanyTransporterArray()
    {
        return [
            self::COMPANY_TRANSPORTER_NOMADEX => Yii::t('titles', 'TOO Nomadex 3PL'), // 0
            self::COMPANY_TRANSPORTER_RLC => Yii::t('titles', 'Lomer point bridge'), // 1
			self::COMPANY_TRANSPORTER_EFFECTIVE_ENGINEERING => Yii::t('titles', 'Effective Engineering'), // 4
//            self::COMPANY_TRANSPORTER_APIS => Yii::t('titles', 'TOO Apis'), // 2
//            self::COMPANY_TRANSPORTER_SCAANA => Yii::t('titles', 'TOO Scaana'), // 3
        ];
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getCompanyTransporterValue($company_transporter=null)
    {
        if(is_null($company_transporter)){
            $company_transporter = $this->company_transporter;
        }
        return ArrayHelper::getValue(self::getCompanyTransporterArray(), $company_transporter);
    }

    /*
     * EVENT
     *
     * */
//    public function event_print_ttn($event)
//    {
//        VarDumper::dump($event,10,true);
//        die('-event_print_ttn-');
//
//        $this->trigger(self::EVENT_PRINT_TTN,[]);
//    }

//    public function afterSave($insert)
//    {
//        $event = new ModelEvent; // Тут мы определяем параметр нашего события. То что мы передаём. В данном случае передаётся значение $this, то есть текущая сущность модели (новый юзер) со всеми сохранными полями
//        $this->trigger(self::EVENT_NEW_USER, $event); // Тут мы вызываем непосредственно наше событие а именно EVENT_NEW_USER что = "newUser", и передаём наш $event параметр, который можно будет потом использовать в обработчике событий.
//
//        parent::afterSave($insert); // Вызываем родительскую функцию afterSave()
//    }

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusForClientArray()
    {
        return [
            self::STATUS_UNDEFINED => Yii::t('transportLogistics/titles', 'Undefined'), //Не определен
            self::STATUS_NEW => Yii::t('transportLogistics/titles', 'New'), //Новый
            self::STATUS_ON_ROUTE => Yii::t('transportLogistics/titles', 'On route'), //В пути
            self::STATUS_DELIVERED => Yii::t('transportLogistics/titles', 'Delivered'), //Доставлен
            self::STATUS_DONE => Yii::t('transportLogistics/titles', 'Done'),  //Выполнен
//            self::STATUS_ADD_CAR => Yii::t('titles', 'Add car'),  //Добавлена машина
//            self::STATUS_ADD_ROUTE_TO_DP => Yii::t('titles', 'Add route to proposal'),  //Добавьте маршрут к заявке
//            self::STATUS_ADD_CAR_TO_ROUTE => Yii::t('titles', 'Add car to route'),  //Добавьте к маршруту машину
//            self::STATUS_ROUTE_FORMED => Yii::t('titles', 'Route formed'),  //Маршрут сформирован
//            self::STATUS_NOT_ADDED_M3 => Yii::t('titles', 'Not added m3'),  //Не заполнен m3
            self::STATUS_IN_PROCESSING_AT_WAREHOUSE => Yii::t('transportLogistics/titles', 'In processing at warehouse'),  //В обработке на складе
        ];
    }
    /*
     * Show status for client
     * @param $status Status
     * @return string status value
     * */
    public function getStatusForClient($status = null)
    {
        if(empty($status)) {
            $status = $this->status;
        }

        $value = '';
        switch($status) {
            case self::STATUS_ADD_CAR:
            case self::STATUS_ADD_ROUTE_TO_DP:
            case self::STATUS_ADD_CAR_TO_ROUTE:
            case self::STATUS_NOT_ADDED_M3:
                $value = self::STATUS_IN_PROCESSING_AT_WAREHOUSE;
                break;
            case self::STATUS_UNDEFINED:
                $value = self::STATUS_UNDEFINED;
                break;
            case self::STATUS_NEW:
                $value = self::STATUS_NEW;
                break;
            case self::STATUS_ON_ROUTE:
                $value = self::STATUS_ON_ROUTE;
                break;
            case self::STATUS_DELIVERED:
                $value = self::STATUS_DELIVERED;
                break;
            case self::STATUS_DONE:
                $value = self::STATUS_DONE;
                break;
            case self::STATUS_ROUTE_FORMED:
                $value = self::STATUS_ROUTE_FORMED;
                break;
            default:
                $value = '-';
                break;
        }

        $data = self::getStatusArray();
        if( $value != '-' && isset($data[$value]) ) {
            $value = $data[$value];
        }

        return $value;
    }

    /*
     * Get value from extra filed
     * @param string Name field. Example: orders
     * @return string
     * */
    public function getExtraFieldValueByName($name)
    {
        $r = '';
        $extraField = (array)Json::decode($this->extra_fields);

        if(isset($extraField[$name])) {
            $r = $extraField[$name];
        }

        return $r;
    }

    /*
     * Save extra filed value
     * @param string $name filed name
     * @param string $value filed value
     * @return boolean
     * */
    public function saveExtraFieldValue($name,$value)
    {
        $extraField = (array)Json::decode($this->extra_fields);
        $extraField[$name] = $value;
        $this->extra_fields = Json::encode($extraField);
        $this->save(false);

        return true;
    }

    /**
     * Get list Delivery type
     * @return array .
     */
    public static function getDeliveryTypeArray()
    {
        return [
//           self::DELIVERY_TYPE_ONE_ROUTE => Yii::t('transportLogistics/forms', 'One route'),
//           self::DELIVERY_TYPE_MORE_ROUTE => Yii::t('transportLogistics/forms', 'More route'),
           self::DELIVERY_TYPE_INBOUND => Yii::t('transportLogistics/forms', 'Inbound type'), // Поступления
           self::DELIVERY_TYPE_OUTBOUND => Yii::t('transportLogistics/forms', 'Outbound type'), // Отгрузки
           self::DELIVERY_TYPE_RETURN => Yii::t('transportLogistics/forms', 'Return type'), // Возвраты
           self::DELIVERY_TYPE_TRANSFER => Yii::t('transportLogistics/forms', 'Transfer type'), // Трансфер
        ];
    }

    /**
     * @return string Value delivery type.
     */
    public function getDeliveryTypeValue($delivery_type=null)
    {
        if(is_null($delivery_type)){
            $delivery_type = $this->delivery_type;
        }
        return ArrayHelper::getValue(self::getDeliveryTypeArray(), $delivery_type,'не указан');
    }


    /*
    * Get value from business logic data
    * @param string Name field. Example: orders
    * @return string
    * */
    public function getBLDataFieldValueByName($name)
    {
        $r = '';
        $data = (array)Json::decode($this->bl_data);

        if(isset($data[$name])) {
            $r = $data[$name];
        }

        return $r;
    }

    /*
    * Save business logic data field value
    * @param string $name filed name
    * @param string $value filed value
    * @param string $comment Description what is data
    * @return boolean
    * */
    public function saveBLDataExtraFieldValue($name,$value, $comment = '')
    {
        $data = (array)Json::decode($this->bl_data);

        $data[$name]['value'] = $value;
        $data[$name]['comment'] = $comment;

        $this->bl_data = Json::encode($data);
        $this->save(false);

        return true;
    }

    /*
     *
     *
     * */
    public function BusinessLogicHandler()
    {

    }

    /*
     * Fix Important for business data (KPI)
     *
     * */
    public function CreateModifyBusinessLogicData()
    {
        //
        if($this->status == self::STATUS_DELIVERED) {
            $this->saveBLDataExtraFieldValue('checkModifyStoreReview',date('Y-m-d H:i:s'),'Фиксируем дату когды был установлен статус "доставлен"');
        }
    }

    /**
     * Get list no change price
     * @return array .
     */
    public static function getNoChangePriceArray()
    {
        return [
            self::CHANGE_AUTOMATIC_PRICE_YES => Yii::t('forms', 'Yes'),
            self::CHANGE_AUTOMATIC_PRICE_NO => Yii::t('forms', 'No'),
        ];
    }

    /**
     * @return string Value no change price
     */
    public function getNoChangePriceValue($change_price=null)
    {
        if(is_null($change_price)){
            $change_price = $this->change_price;
        }
        return ArrayHelper::getValue(self::getNoChangePriceArray(), $change_price);
    }


    /**
     * Get list ready_to_invoicing
     * @return array .
     */
    public static function getReadToInvoicingArray()
    {
        return [
            self::READY_TO_INVOICING_NO => Yii::t('forms', 'Not ready to invoicing'),
            self::READY_TO_INVOICING_YES => Yii::t('forms', 'Ready to invoicing'),
        ];
    }

    /**
     * @return string Value ready_to_invoicing
     */
    public function getReadyToInvoicingValue($ready=null)
    {
        if(is_null($ready)){
            $ready = $this->ready_to_invoicing;
        }
        return ArrayHelper::getValue(self::getReadToInvoicingArray(), $ready);
    }

    /**
     * Get list no change Mc Kg Np
     * @return array .
     */
    public static function getNoChangeMcKgNpArray()
    {
        return [
            self::CHANGE_AUTOMATIC_MC_KG_NP_YES => Yii::t('forms', 'Yes'),
            self::CHANGE_AUTOMATIC_MC_KG_NP_NO => Yii::t('forms', 'No'),
        ];
    }

    /**
     * @return string Value no change Mc Kg Np
     */
    public function getNoChangeMcKgNpValue($change_mckgnp=null)
    {
        if(is_null($change_mckgnp)){
            $change_mckgnp = $this->change_mckgnp;
        }
        return ArrayHelper::getValue(self::getNoChangeMcKgNpArray(), $change_mckgnp);
    }


    /*
     * Update cascade mc,kg,np in route and cars
     *
     * */
    public function updateCascadedMcKgNp()
    {
        if($routes = $this->getProposalRoutes()->all()) {
            foreach($routes as $route) {
                $route->delivery_date = $this->delivery_date;
                $route->shipped_datetime = $this->shipped_datetime;
                $route->accepted_datetime = $this->accepted_datetime;
                $route->save(false);
                if($routeCarItems = $route->getCarsByRoute()->all()) {
                    foreach($routeCarItems as $dpRouteCar) {
                        if($car = $dpRouteCar->routeCar){
                            $car->delivery_date = $this->delivery_date;
                            $car->shipped_datetime = $this->shipped_datetime;
                            $car->accepted_datetime = $this->accepted_datetime;
                            $car->save(false);
                        }
                        $dpRouteCar->mc = $this->mc;
                        $dpRouteCar->mc_actual = $this->mc_actual;
                        $dpRouteCar->kg = $this->kg;
                        $dpRouteCar->kg_actual = $this->kg_actual;
                        $dpRouteCar->number_places = $this->number_places;
                        $dpRouteCar->number_places_actual = $this->number_places_actual;
                        $dpRouteCar->save(false);
                    }
                }
            }
        }
        return true;
    }

    /*
     * Route from readable title
     * @param int route_id
     * @return string
     **/
    public function getRouteFromValue($route_id = null){
        if(is_null($route_id)){
           $route_id=$this->route_from;
        }
        return ArrayHelper::getValue(self::getRouteArray(), $route_id);
    }

    /*
     * Route to readable title
     * @param int route_id
     * @return string
     **/
    public function getRouteToValue($route_id = null){
        if(is_null($route_id)){
            $route_id=$this->route_to;
        }
        return ArrayHelper::getValue(self::getRouteArray(), $route_id);
    }

    /*
     * Route from readable title
     * @param int route_id
     * @return string
     **/
    public function getRouteArray(){
       return TLHelper::getStoreArrayByClientID();
    }


    /*
     * Get grid row color, depend on
     * record status
     * @return string
     **/
    public function getGridColor(){

        switch($this->status) {
            case TlDeliveryProposal::STATUS_NEW:
                $class = 'color-new';
                break;
            case TlDeliveryProposal::STATUS_ON_ROUTE:
                $class = 'color-on-route';
                break;
            case TlDeliveryProposal::STATUS_DELIVERED:
                $class = 'color-delivered';
                break;
            case TlDeliveryProposal::STATUS_DONE:
                $class = 'color-done';
                break;
            case TlDeliveryProposal::STATUS_ADD_CAR:
                $class = 'color-add-route';
                break;
            case TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP:
                $class = 'color-add-route';
                break;
            case TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE:
                $class = 'color-add-route';
                break;
            case TlDeliveryProposal::STATUS_ROUTE_FORMED:
                $class = 'color-on-route';
                break;
            case TlDeliveryProposal::STATUS_NOT_ADDED_M3:
                $class = 'color-add-route';
                break;
            case TlDeliveryProposal::STATUS_NOT_ADDED_M3_ON_ROUTE:
                $class = 'color-add-route';
                break;
            case TlDeliveryProposal::STATUS_IN_PROCESSING_AT_WAREHOUSE:
                $class = 'color-in-process';
                break;
            case TlDeliveryProposal::STATUS_IN_TRANSFER_FROM_POINT:
                $class = 'color-in-process';
                break;
            default:
                $class = '';
                break;
        }
        return $class;
    }

    /*
    * Array with attribute values functions mapping
    * @return array
    **/
    public function getAttributesValuesMap($attribute)
    {
        $data = [
            'status'=>'getStatusValue',
            'source'=>'getSourceValue',
            'delivery_type'=>'getDeliveryTypeValue',
            'change_price'=>'getNoChangePriceValue',
            'company_transporter'=>'getCompanyTransporterValue',
            'cash_no'=>'getPaymentMethodValue',
            'change_mckgnp'=>'getNoChangeMcKgNpValue',
            'status_invoice'=>'getInvoiceStatusValue',
            'route_to'=>'getRouteToValue',
            'route_from'=>'getRouteFromValue',
        ];

        return ArrayHelper::getValue($data, $attribute);
    }

    /*
     * Indicates show print TTN button or not
     * @return bool
     **/
    public function canPrintTtn()
    {
        $flag = true;

        if($this->status == self::STATUS_DELIVERED || $this->status == self::STATUS_DONE){
            $flag=false;
        }
        $count = 0;
        //S: проверяем если к заявке прикреплен заказы которые еще не отсканированы
        $orders =  $this->proposalOrders;
        if(!empty($orders)) {
            foreach($orders as $order) {
                if($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
                    if($outboundOrderOne = OutboundOrder::findOne($order->order_id)) {
                        if(!in_array($outboundOrderOne->status,[
                            Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
                            Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                            Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                            Stock::STATUS_OUTBOUND_ON_ROAD,
                        ])) {
//                            return true;
                        //} else {
//                            return false;
                            ++$count;
                        }
                    }
                }

                if($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK) {
                    if($crossDockOrderOne = CrossDock::findOne($order->order_id)) {
                        if(!in_array($crossDockOrderOne->status,[
                            Stock::STATUS_CROSS_DOCK_COMPLETE,
                            Stock::STATUS_OUTBOUND_ON_ROAD,
                        ])) {
//                            return true;
                        //} else {
//                            return false;
                            ++$count;
                        }
                    }
                }
            }
        }
        //E: проверяем если к заявке прикреплен заказы которые еще не отсканированы
        if($count > 0) {
            $flag = false;
        }
        return $flag;
    }


    /*
    * Calculate TR
    * shipped_datetime or created_at  minus date_delivered
    * @return mixed
    **/
    public function calculateDiffTR()
    {
        if(empty($this->delivery_date) || empty($this->shipped_datetime)){
            return '0';
        }
        $deliveryDatetime = $this->delivery_date;
        $shippedDatetime = $this->shipped_datetime;

        if($this->client_id == 77) {
            $shippedDatetime = $this->created_at;
        }

        $tmp = new \DateTime();
        $tmp->setTimestamp($shippedDatetime);
        $s = strtotime($tmp->format('Y').'-'.$tmp->format('m').'-'.$tmp->format('d').' 00:00:00');
        $tmp->setTimestamp($deliveryDatetime);
        $e = strtotime($tmp->format('Y').'-'.$tmp->format('m').'-'.$tmp->format('d').' 00:00:00');

        $start = new \DateTime();
        //$this->shipped_datetime = $s;

        $start->setTimestamp($s);
//        $start->setTimestamp($this->shipped_datetime);
        $end = new \DateTime();
//        $this->delivery_date = $e;
//        $end->setTimestamp($this->delivery_date);
        $end->setTimestamp($e);

        $interval = $start->diff($end);
//        echo $this->shipped_datetime.' '.Yii::$app->formatter->asDatetime($this->shipped_datetime)."<br />";
//        echo $this->delivery_date.' '.Yii::$app->formatter->asDatetime($this->delivery_date)."<br />";
//
//        VarDumper::dump($interval,10,true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";

//        $workingDays = 0;
        $workingDays = $interval->d;
/*        for ($i = 0; $i < $interval->d; $i++) {
            $start->modify('+1 day');
            $weekday = $start->format('w');

            if($weekday != 0 && $weekday != 6) { // 0 for Sunday and 6 for Saturday
                $workingDays++;
            }
        }
*/

        if($workingDays == 0) {
            return 1;
//            if($interval->h <= 0) {
//                return iHelper::formatTextAfterNumber(1, Yii::t('titles', 'hour'), Yii::t('titles', 'hour'), Yii::t('titles', 'hour'));
//            }
//            return iHelper::formatTextAfterNumber($interval->h, Yii::t('titles', 'hour'), Yii::t('titles', 'chasa'), Yii::t('titles', 'hours'));
        }
//
//        return iHelper::formatTextAfterNumber($workingDays, Yii::t('titles', 'day'), Yii::t('titles', 'dnya'), Yii::t('titles', 'days'));
        return $workingDays;//, Yii::t('titles', 'day'), Yii::t('titles', 'dnya'), Yii::t('titles', 'days'));
    }

    /**
     * @return array Массив с типами погрузки.
     */
    public static function getTransportTypeLoadingArray()
    {
        return [
            self::TRANSPORT_TYPE_LOADING_UNDEFINED => Yii::t('operator/forms', 'TYPE_LOADING_UNDEFINED'), // не определено
            self::TRANSPORT_TYPE_LOADING_BACK => Yii::t('operator/forms', 'TYPE_LOADING_BACK'), // Сзади
            self::TRANSPORT_TYPE_LOADING_TOP => Yii::t('operator/forms', 'TYPE_LOADING_TOP'),   // Сверху
            self::TRANSPORT_TYPE_LOADING_SIDE => Yii::t('operator/forms', 'TYPE_LOADING_SIDE'), // Сбоку
        ];
    }

    /**
     * @param integer $value Тип погрузки.
     * @return string
     */
    public function getTransportTypeLoadingValue($value = null)
    {
        if(is_null($value)){
            $value = $this->transport_type_loading;
        }
        return ArrayHelper::getValue($this->getTransportTypeLoadingArray(), $value);
    }

    /**
     * @return array Массив с типами платильщиков
     */
    public static function getTransportWhoPaysArray()
    {
        return [
            self::TRANSPORT_WHO_PAYS_UNDEFINED => Yii::t('operator/forms', 'WHO_PAYS_UNDEFINED'), // не определено
            self::TRANSPORT_WHO_PAYS_SENDER => Yii::t('operator/forms', 'WHO_PAYS_SENDER'), // Отправитель
            self::TRANSPORT_WHO_PAYS_RECIPIENT => Yii::t('operator/forms', 'WHO_PAYS_RECIPIENT'),   // Получатель
            self::TRANSPORT_WHO_PAYS_BY_THIRD_PART => Yii::t('operator/forms', 'WHO_PAYS_BY_THIRD_PART'), // третья сторона
            self::TRANSPORT_WHO_PAYS_50 => Yii::t('operator/forms', 'WHO_PAYS_BY_50'), // 50% отправитель и получатель
            self::TRANSPORT_WHO_PAYS_BY_CONTRACT => Yii::t('operator/forms', 'WHO_PAYS_BY_CONTRACT'), // По договору
        ];
    }

    /**
     * @param integer $value Кто оплачивает.
     * @return string
     */
    public function getTransportWhoPays($value = null)
    {
        if(is_null($value)){
            $value = $this->transport_who_pays;
        }
        return ArrayHelper::getValue($this->getTransportWhoPaysArray(), $value);
    }

}