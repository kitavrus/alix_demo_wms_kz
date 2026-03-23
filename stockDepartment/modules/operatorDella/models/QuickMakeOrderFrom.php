<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 27.05.16
 * Time: 22:56
 */

namespace app\modules\operatorDella\models;

use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use app\modules\operatorDella\models\ClientSearch;
use app\modules\operatorDella\models\DeliveryCalculatorForm;
use app\modules\operatorDella\models\DeliveryOrderSearch;
use app\modules\operatorDella\models\RouteOrderFormSearch;
use common\modules\city\models\City;
use common\modules\city\models\Region;
use common\modules\client\models\ClientEmployees;
use common\modules\leads\models\TransportationOrderLead;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\order\models\PersonalOrderLead;
use app\modules\order\models\TransportationOrderLeadSearch;
use personalDepartment\components\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\components\ClientManager;
use common\modules\store\models\Store;
use common\modules\client\models\Client;
use common\modules\codebook\models\Codebook;
use app\modules\operatorDella\models\DeliveryProposalSearch;
use common\components\DeliveryProposalManager;
use yii\web\Response;
use app\modules\operatorDella\models\SenderRecipientForm;
use common\components\DeliveryProposalService;
use common\modules\user\models\User;
use app\modules\operatorDella\models\TlDeliveryProposal;


class QuickMakeOrderFrom extends Model
{
    /*
     *  @var string Название компании
     * */
    public $companyName;
    /*
     *  @var string Телефон компании
     * */
    public $companyPhone;
    /*
     *  @var string Эл-почта компании
     * */
    public $companyEmail;

    //направление городов
    /*
     * @var string Адрес отправителя
     * */
    public $fromCity;
    public $fromStreet;
    public $fromHouse;
    public $fromAddressComment;
    /*
    * ФИО отправителя
    * */
    public $fromFirstName;
    public $fromLastName;
    public $fromMiddleName;
    /*
     * Телефон 1,2 отправителя
     * */
    public $fromPhoneOne;
    public $fromPhoneTwo;
    /*
     * Почта отправителя
     * */
    public $fromEmail;
    /*
     *@var string Адрес получателя
     * */
    public $toCity;
    public $toStreet;
    public $toHouse;
    public $toAddressComment;
    /*
     * ФИО получателя
     * */
    public $toFirstName;
    public $toLastName;
    public $toMiddleName;
    /*
     * Почта получателя
     * */
    public $toEmail;
    /*
     * Телефон
     * */
    public $toPhoneOne;
    public $toPhoneTwo;
    /*
     * Тип погрузки
     * */
    public $typeLoading; //Тип погрузки. сверху, сзади, сбоку
    /*
     * кто платит
     * */
    public $whoPays;
    /*
     * стоимость перевозки
     * */
    public $price;
    /*
     * Заявленая стоимость груза
     * */
    public $declaredValue;
    /*
     * Тип доставки
     * */
    public $deliveryType;
    /*
     * Название груза
     * */
    public $cargoComment;
    /*
     * масса
     * */
    public $kg;
    /*
     * объем
     * */
    public $m3;
    /*
     * Количество мест
     * */
    public $placeQty;

    /*
     *
     * */
    private $_client;
    private $_clientEmployee;
    private $_clientUser;

    /*
     *
     * */
    private $_fromAddress;
    private $_fromContact;
    private $_toAddress;
    private $_toContact;
    private $_deliveryProposal;

    /*
     * */
    public function rules()
    {
        return [
            [['companyName','companyPhone'], 'string'],
            [['companyEmail'], 'email'],

            [['fromCity', 'fromStreet','fromHouse','fromFirstName','fromLastName','fromMiddleName','fromPhoneOne','fromPhoneTwo'], 'string'],
            [['fromAddressComment'], 'string'],
            [['fromEmail'], 'email'],

            [['toCity', 'toStreet','toHouse','toFirstName','toLastName','toMiddleName','toPhoneOne','toPhoneTwo'], 'string'],
            [['toAddressComment'], 'string'],
            [['toEmail'], 'email'],

            [['price','declaredValue'], 'number'],
            [['kg','m3'], 'number'],

            [['typeLoading','whoPays','deliveryType','placeQty'], 'integer'],
            [['cargoComment'], 'string'],

            [['fromCity','fromStreet','fromHouse','fromFirstName','fromPhoneOne'], 'required'],
            [['toCity','toStreet','toHouse','toFirstName','toPhoneOne'], 'required'],

            [['price','whoPays','placeQty'], 'required'],
            [['kg','m3'], 'required'],

            [['companyName','companyPhone'], 'required'],
        ];
    }

    /*
     *
     * */
    public function afterValidate()
    {
        $this->companyPhone =  preg_replace('/[^\d]+/', '',$this->companyPhone);
        $this->fromPhoneOne =  preg_replace('/[^\d]+/', '',$this->fromPhoneOne);
        $this->fromPhoneTwo =  preg_replace('/[^\d]+/', '',$this->fromPhoneTwo);
        $this->toPhoneOne =  preg_replace('/[^\d]+/', '',$this->toPhoneOne);
        $this->toPhoneTwo =  preg_replace('/[^\d]+/', '',$this->toPhoneTwo);
    }

    /*
     * */
    public function attributeLabels()
    {
        return [
            'companyName' => 'название компании',
            'companyPhone' => 'телефон',
            'companyEmail' => 'эл. почта',

            'fromCity' => 'город',
            'fromStreet' => 'улица',
            'fromHouse' => 'дом',
            'fromAddressComment' => 'комментарий к адресу',
            'fromFirstName' => 'имя',
            'fromMiddleName' => 'очество',
            'fromLastName' => 'фамилия',
            'fromPhoneOne' => 'телефон',
            'fromPhoneTwo' => 'телефон доп',
            'fromEmail' => 'эл. почта',

            'toCity' => 'город',
            'toStreet' => 'улица',
            'toHouse' => 'дом',
            'toAddressComment' => 'комментарий к адресу',
            'toFirstName' => 'имя',
            'toMiddleName' => 'очество',
            'toLastName' => 'фамилия',
            'toPhoneOne' => 'телефон',
            'toPhoneTwo' => 'телефон доп',
            'toEmail' => 'эл. почта',

            'price' => 'Стоимость перевозки',
            'declaredValue' => 'Заявленная стоимость груза',
            'kg' => 'Вес груза в кг',
            'm3' => 'Объем груза в М3',
            'placeQty' => 'Кол-во мест',
            'typeLoading' => 'Тип погрузки',
            'cargoComment' => 'Описание груза',
            'whoPays' => 'Кто оплачивает',
            'deliveryType' => 'Тип доставки',
        ];
    }

    /**
     * @return array Массив с типами доставки
     */
    public function getDeliveryTypeArray()
    {
        return TlDeliveryProposalBilling::getDeliveryTypeArray();
    }

    /**
     * @return array Массив с типами доставки
     */
    public function getTransportTypeLoadingArray()
    {
        return TlDeliveryProposal::getTransportTypeLoadingArray();
    }

    /**
     * @return array Кто оплачивает
     */
    public function getTransportWhoPaysArray()
    {
        return TlDeliveryProposal::getTransportWhoPaysArray();
    }

    /*
     * @return Client model
     * */
    public function createUpdateClient()
    {
        $username =  preg_replace('/[^\d]+/', '',$this->companyPhone);
        $model = new Client();
        $model->legal_company_name = $this->companyName;
        $model->title = $this->companyName;
        $model->username = 'dellaU'.$username;
        $model->phone = $this->companyPhone;
        $model->email = $this->companyEmail;
        $model->password = 'DELLA-'.time();

        $model->user_id = 0;
        $model->client_type = Client::CLIENT_TYPE_PERSONAL;
        $model->status = Client::STATUS_ACTIVE;
        $model->on_stock = Client::ON_STOCK_TMS;
        $model->save(false);

//        $userModel = \Yii::createObject([
//            'class'    => User::className(),
//            'scenario' => 'create',
//        ]);
//
//        $userModel->username = $model->username;
//        $userModel->email = empty($model->email) ? time().'-demo-della@nmdx.kz' : $model->email;
//        $userModel->user_type = User::USER_TYPE_CLIENT;
//        $userModel->password = 'p'.$model->password;
//
//        if($userModel->create()) {
//            $model->user_id = $userModel->id;
//            $model->password = '';
//            $model->save(false);
//        }

        // Create base client employee account
        $clientEmployee = new ClientEmployees();
        $clientEmployee->store_id = 0;
        $clientEmployee->client_id = $model->id;
        $clientEmployee->user_id = $model->user_id;
        $clientEmployee->username = $model->username;
        $clientEmployee->first_name = $model->first_name;
        $clientEmployee->middle_name = $model->middle_name;
        $clientEmployee->last_name = $model->last_name;
        $clientEmployee->phone = $model->phone;
        $clientEmployee->phone_mobile = $model->phone_mobile;
        $clientEmployee->email = $model->email;
        $clientEmployee->password = 'dellaU'.time();
        $clientEmployee->status = Client::STATUS_ACTIVE;

        $clientEmployee->manager_type = ClientEmployees::TYPE_DIRECTOR;
        $clientEmployee->save(false);

        $this->_client = $model;
        $this->_clientEmployee = $clientEmployee;

        return $this;
    }
    /*
 *
 * */
    public function createUpdateClientAddressFrom()
    {
        $regionId = 0;
        $countryId = 0;
        if($city = City::find()->andWhere(['id'=>$this->fromCity])->one()) {
            $regionId = $city->region_id;
            if($region = Region::find()->andWhere(['id'=>$city->region_id])->one()) {
                $countryId = $region->country_id;
            }
        }

        $model = new Store();
        $model->region_id = $regionId;
        $model->country_id = $countryId;
        $model->city_id = $this->fromCity;
        $model->street = $this->fromStreet;
        $model->house = $this->fromHouse;
        $model->client_id = $this->_client->id;

        $model->name = 'DELLA-POINT-'.$model->client_id;
        $model->type_use = Store::TYPE_USE_POINT;
        $model->status = Store::STATUS_ACTIVE;
        $model->save(false);

        $this->_fromAddress = $model;

        return $this;
    }
    /*
     *
     * */
    public function createUpdateClientContactFrom()
    {
        $clientEmployee = new ClientEmployees();
        $clientEmployee->client_id = $this->_client->id;
        $clientEmployee->username = $this->fromFirstName;
        $clientEmployee->first_name = $this->fromFirstName;
        $clientEmployee->middle_name = $this->fromMiddleName;
        $clientEmployee->last_name = $this->fromLastName;
        $clientEmployee->phone = $this->fromPhoneOne;
        $clientEmployee->phone_mobile = $this->fromPhoneTwo;
        $clientEmployee->email = $this->fromEmail;

        $clientEmployee->password = 'dellaCE'.time();
        $clientEmployee->store_id = $this->_fromAddress->id;
        $clientEmployee->user_id = 0;
        $clientEmployee->status = Client::STATUS_ACTIVE;
        $clientEmployee->manager_type = ClientEmployees::TYPE_DIRECTOR;
        $clientEmployee->save(false);

        $this->_fromContact = $clientEmployee;

        return $this;
    }
    /*
 *
 * */
    public function createUpdateClientAddressTo()
    {
        $regionId = 0;
        $countryId = 0;
        if($city = City::find()->andWhere(['id'=>$this->toCity])->one()) {
            $regionId = $city->region_id;
            if($region = Region::find()->andWhere(['id'=>$city->region_id])->one()) {
                $countryId = $region->country_id;
            }
        }

        $model = new Store();
        $model->region_id = $regionId;
        $model->country_id = $countryId;
        $model->city_id = $this->toCity;
        $model->street = $this->toStreet;
        $model->house = $this->toHouse;
        $model->client_id = $this->_client->id;

        $model->name = 'DELLA-POINT-'.$model->client_id;
        $model->type_use = Store::TYPE_USE_POINT;
        $model->status = Store::STATUS_ACTIVE;
        $model->save(false);

        $this->_toAddress = $model;

        return $this;
    }
    /*
     *
     * */
    public function createUpdateClientContactTo()
    {
        $clientEmployee = new ClientEmployees();
        $clientEmployee->client_id = $this->_client->id;
        $clientEmployee->username = $this->toFirstName;
        $clientEmployee->first_name = $this->toFirstName;
        $clientEmployee->middle_name = $this->toMiddleName;
        $clientEmployee->last_name = $this->toLastName;
        $clientEmployee->phone = $this->toPhoneOne;
        $clientEmployee->phone_mobile = $this->toPhoneTwo;
        $clientEmployee->email = $this->toEmail;

        $clientEmployee->password = 'dellaCE'.time();
        $clientEmployee->store_id =  $this->_toAddress->id;
        $clientEmployee->user_id = 0;
        $clientEmployee->status = Client::STATUS_ACTIVE;
        $clientEmployee->manager_type = ClientEmployees::TYPE_DIRECTOR;
        $clientEmployee->save(false);

        $this->_toContact = $clientEmployee;

        return $this;
    }
    /*
     *
     * */
    public function createUpdateDeliveryProposal()
    {
        $dp = new TlDeliveryProposal();
        $dp->client_id = $this->_client->id;
        $dp->kg = $this->kg;
        $dp->mc = $this->m3;
        $dp->kg_actual =  $this->kg;
        $dp->mc_actual = $this->m3;
        $dp->number_places = $this->placeQty;
        $dp->number_places_actual = $this->placeQty;
        $dp->route_from = $this->_fromAddress->id;
        $dp->route_to =  $this->_toAddress->id;
//        $dp->sender_contact =  $this->prepareContactToStr('from');;
        $dp->sender_contact_id = $this->_fromContact->id;
//        $dp->recipient_contact = $this->prepareContactToStr('to');
        $dp->recipient_contact_id = $this->_toContact->id;
        $dp->declared_value = $this->declaredValue;
        $dp->delivery_method = $this->deliveryType;
        $dp->comment = $this->cargoComment."\n".$this->fromAddressComment."\n".$this->toAddressComment;
        $dp->transport_type_loading =  $this->typeLoading;
        $dp->transport_who_pays = $this->whoPays;
        $dp->price_invoice = $this->price;

        $dp->status = TlDeliveryProposal::STATUS_NEW;
        $dp->source = TlDeliveryProposal::SOURCE_DELLA_OPERATOR;
        $dp->delivery_type = TlDeliveryProposal::DELIVERY_TYPE_TRANSFER;
        $dp->change_price = TlDeliveryProposal::CHANGE_AUTOMATIC_PRICE_NO;
        $dp->change_mckgnp = TlDeliveryProposal::CHANGE_AUTOMATIC_MC_KG_NP_NO;
        $dp->cash_no = TlDeliveryProposal::METHOD_CASH;
        $dp->save(false);

        $deliveryOrder = new  TlDeliveryProposalOrders();
        $deliveryOrder->client_id = $dp->client_id;
        $deliveryOrder->tl_delivery_proposal_id = $dp->id;
        $deliveryOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_UNDEFINED;
        $deliveryOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK;
        $deliveryOrder->number_places = $dp->number_places;
        $deliveryOrder->number_places_actual = $dp->number_places;
        $deliveryOrder->mc_actual = $dp->mc;
        $deliveryOrder->mc = $dp->mc;
        $deliveryOrder->kg_actual = $dp->kg;
        $deliveryOrder->kg = $dp->kg;
        $deliveryOrder->order_number = '[DELLA-OPERATOR-'.$dp->id.'-'.date('Ymd').']';
        $deliveryOrder->save(false);

        $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
        $dpManager->onCreateProposal();

        $this->_deliveryProposal = $dp;

        return $this;
    }

    public function getDeliveryProposal()
    {
        return $this->_deliveryProposal;
    }

    /*
    *
    * @param string $fromTo
    * @return string
    * */
//    public function prepareContactToStr($fromTo)
//    {
//        $out = '';
//        $ce = $fromTo == 'from' ? $this->_fromContact : $this->_toContact;
//        if($ce) {
//            $out = $ce->full_name.' Тел: '.Yii::$app->formatter->asPhone($ce->phone).' Тел2: '.Yii::$app->formatter->asPhone($ce->phone_mobile);
//        }
//
//        return $out;
//    }
}