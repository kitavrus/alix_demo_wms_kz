<?php

namespace common\modules\transportLogistics\models;

use common\events\DpEvent;
use Yii;
use common\modules\store\models\Store;
use common\models\ActiveRecord;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use app\modules\transportLogistics\transportLogistics;
use common\modules\client\models\Client;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "tl_delivery_routes".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $tl_delivery_proposal_id
 * @property integer $tl_delivery_proposal_route_car_id // TO DELETE
 * @property integer $route_from
 * @property integer $route_to
 * @property integer $delivery_date
 * @property integer $shipped_datetime
 * @property integer $accepted_datetime
 * @property integer $transportation_type
 * @property string $mc TO_DELETE
 * @property integer $mc_actual TO_DELETE
 * @property integer $kg TO_DELETE
 * @property integer $kg_actual TO_DELETE
 * @property integer $number_places TO_DELETE
 * @property integer $number_places_actual TO_DELETE
 * @property integer $cash_no TO_DELETE
 * @property integer $price_invoice
 * @property string $price_invoice_with_vat
 * @property integer $status
 * @property integer $status_invoice
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class TlDeliveryRoutes extends ActiveRecord
{
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

    const TRANSPORT_TYPE_AUTO = 1;
    const TRANSPORT_TYPE_RAIL = 2;
    const TRANSPORT_TYPE_AIR = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_routes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_delivery_proposal_route_car_id','client_id', 'tl_delivery_proposal_id', 'number_places', 'number_places_actual', 'cash_no',  'status', 'status_invoice', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'transportation_type'], 'integer'],
            [['mc', 'price_invoice_with_vat', 'route_from', 'route_to', 'mc_actual', 'kg', 'kg_actual','price_invoice',], 'number'],
            [['comment'], 'string'],
            [['shipped_datetime','accepted_datetime','delivery_date', 'transportation_type'], 'safe'],
        ];
    }

    /*
    *
    * */
    public function scenarios() {
        return [
            'default'=>['route_from','route_to','tl_delivery_proposal_id','client_id','delivery_date','shipped_datetime','accepted_datetime','comment', 'transportation_type'],
            'create-update-manager-warehouse'=>[
                'route_from',
                'route_to',
                'tl_delivery_proposal_id',
                'client_id',
                'delivery_date',
                'shipped_datetime',
                'comment',
                'accepted_datetime',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'client_id' => Yii::t('transportLogistics/forms', 'Client ID'),
            'tl_delivery_proposal_route_car_id' => Yii::t('transportLogistics/forms', 'Car'),
            'tl_delivery_proposal_id' => Yii::t('transportLogistics/forms', 'Tl Delivery Proposal ID'),
            'route_from' => Yii::t('transportLogistics/forms', 'Route From'),
            'route_to' => Yii::t('transportLogistics/forms', 'Route To'),
            'delivery_date' => Yii::t('transportLogistics/forms', 'Delivery Date'),
            'accepted_datetime' => Yii::t('transportLogistics/forms', 'Accepted date'),
            'shipped_datetime' => Yii::t('transportLogistics/forms', 'Shipped date'),
            'transportation_type' => Yii::t('transportLogistics/forms', 'Transportation type'),
            'mc' => Yii::t('transportLogistics/forms', 'Mc'),
            'mc_actual' => Yii::t('transportLogistics/forms', 'Mc Actual'),
            'kg' => Yii::t('transportLogistics/forms', 'Kg'),
            'kg_actual' => Yii::t('transportLogistics/forms', 'Kg Actual'),
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
        ];
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
        return $this->hasOne(Store::className(), ['id' => 'route_to']);
    }

    /**
     * @return array .
     */
    public static function getTransportationTypeArray()
    {
        return [
            self::TRANSPORT_TYPE_AUTO => Yii::t('titles', 'Auto'),
            self::TRANSPORT_TYPE_RAIL => Yii::t('titles', 'Train'),
            self::TRANSPORT_TYPE_AIR => Yii::t('titles', 'Air'),
        ];
    }

    /**
     * @return string .
     */
    public function getTransportationTypeValue($value = null)
    {
        if(is_null($value)) {
            $value = $this->transportation_type;
        }

        return ArrayHelper::getValue(self::getTransportationTypeArray(),$value);
    }

    /**
     * @return array Массив со статусами.
     */
    public static function getStatusArray($key=null)
    {
        $data = [
            self::STATUS_UNDEFINED => Yii::t('transportLogistics/titles', 'Undefined'), //Не определен
            self::STATUS_NEW => Yii::t('transportLogistics/titles', 'New'), //Новый
            self::STATUS_ON_ROUTE => Yii::t('transportLogistics/titles', 'On route'), //В пути
            self::STATUS_DELIVERED => Yii::t('transportLogistics/titles', 'Delivered'), //Доставлен
            self::STATUS_DONE => Yii::t('transportLogistics/titles', 'Done'),  //Выполнен
            self::STATUS_ADD_CAR => Yii::t('transportLogistics/titles', 'Car added'),  //Добавлена машина
            self::STATUS_ADD_ROUTE_TO_DP => Yii::t('transportLogistics/titles', 'Add route to proposal'),  //Добавьте маршрут к заявке
            self::STATUS_ADD_CAR_TO_ROUTE => Yii::t('transportLogistics/titles', 'Add car to route'),  //Добавьте к маршруту машину
            self::STATUS_ROUTE_FORMED => Yii::t('transportLogistics/titles', 'Route formed'),  //Маршрут сформирован
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return array Значение со статусом.
     */
    public function getStatusValue()
    {
        $data = self::getStatusArray();
        return isset($data[$this->status]) ? $data[$this->status] : '-';
    }

    /*
    * @return array with store id=>title
    */
    public static function getRouteFromTo($key = null)
    {
        $data =  ArrayHelper::map(Store::find()->orderBy('title')->all(), 'id', 'title');

        return isset($data[$key]) ? $data[$key] : $data;
    }

    /*
    * Relation has many with DeliveryProposalOrders
    * */
//    public function getDeliveryProposalRouteOrders()
//    {
//        return $this->hasMany(TlDeliveryRouteOrders::className(), ['tl_delivery_route_id'=>'id']);
//    }

    /*
    * Relation has many with TlDeliveryProposalRouteUnforeseenExpenses
    * */
    public function getTlDeliveryProposalRouteUnforeseenExpenses()
    {
        return $this->hasMany(TlDeliveryProposalRouteUnforeseenExpenses::className(), ['tl_delivery_route_id'=>'id']);
    }

    /*
    * Relation has One with Car
    *
    * */
//    public function getCar()
//    {
//        return $this->hasOne(TlCars::className(), ['id' => 'tl_delivery_proposal_route_car_id']);
//    }

    public function getCarItems()
    {
        return $this->hasMany(TlDeliveryProposalRouteCars::className(), ['id' => 'tl_delivery_proposal_route_cars_id'])->where(['deleted'=>0])
            ->viaTable('tl_delivery_proposal_route_transport', ['tl_delivery_proposal_route_id' => 'id'])->where(['deleted'=>0]);
    }

    public function getCarsByRoute()
    {
        return $this->hasMany(TlDeliveryProposalRouteTransport::className(), ['tl_delivery_proposal_route_id'=>'id'])->where(['deleted'=>0]);
    }


    /*
    * Relation with Client table
    * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
     * Get formatted title
     * @return string
     * */
    public function getDisplayTitle()
    {
        $kg = Yii::$app->formatter->asDecimal($this->kg_actual).' '.Yii::t('titles','kg');
        $mc = ' / '.Yii::$app->formatter->asDecimal($this->mc_actual).' '.Yii::t('titles','m3');

        return  (!empty($this->kg_actual) ? $kg : '') . ' '
              . (!empty($this->mc_actual) ? $mc : '') . ' '
              . Yii::$app->formatter->asCurrency($this->price_invoice);
    }


    /*
     * Get small formatted title
     * @return string
     * */
    public function getSmallDisplayTitle()
    {
        $kg = Yii::$app->formatter->asDecimal($this->kg_actual).' '.Yii::t('titles','kg');
        $mc = ' / '.Yii::$app->formatter->asDecimal($this->mc_actual).' '.Yii::t('titles','m3');

        $title =  '';
        $cityFromName = '';
        $routeFromName = '';
        $shoppingCenterFromName = '';
        if($rf = $this->routeFrom) {
            $routeFromName = $rf->name;
            $shoppingCenterFromName = $rf->shopping_center_name;
            if($rfc = $rf->city) {
                $cityFromName =   $rfc->name;
            }
        }

        $cityToName = '';
        $routeToName = '';
        $shoppingCenterToName = '';
        if($rt = $this->routeTo) {
            $routeFromName = $rt->name;
            $shoppingCenterToName = $rt->shopping_center_name;
            if($rtc = $rt->city) {
                $cityToName =   $rtc->name;
            }
        }

        $title .= $cityFromName . ' / ' . $routeFromName;
        $title .= ((!empty($shoppingCenterFromName) && $shoppingCenterFromName != '-')  ? '  [' . $shoppingCenterFromName . ' ] ' : '');

        $title .=  '  >>> ';

        $title .= $cityToName . ' / ' . $routeToName;
        $title .= ((!empty($shoppingCenterToName) && $shoppingCenterToName != '-')  ? '  [ ' . $shoppingCenterToName . ' ] ' : '');


        return  $title;
    }

    /*
    * After save add order to route order
    * */
    public function afterSave($insert, $changedAttributes )
    {
        //
//        $deliveryProposal = TlDeliveryProposal::findOne($this->tl_delivery_proposal_id);

//        if($pOrders = $deliveryProposal->getProposalOrders()->all()) {
//            foreach($pOrders as $order) {
//
//                if(!TlDeliveryRouteOrders::find()->where([
//                    'tl_delivery_proposal_id'=>$order->tl_delivery_proposal_id,
//                    'tl_delivery_route_id'=>$this->id,
//                    'order_number'=>$order->order_number,
//                    ])->count()) {
//
//                    $dpRouteOrder = new TlDeliveryRouteOrders();
//                    $dpRouteOrder->tl_delivery_proposal_id = $order->tl_delivery_proposal_id;
//                    $dpRouteOrder->tl_delivery_route_id = $this->id;
//                    $dpRouteOrder->client_id = $order->client_id;
//                    $dpRouteOrder->order_type = $order->order_type;
//                    $dpRouteOrder->order_id = $order->order_id;
//                    $dpRouteOrder->order_number = $order->order_number;
//                    $dpRouteOrder->number_places = $order->number_places;
//                    $dpRouteOrder->mc = $order->mc;
//                    $dpRouteOrder->mc_actual = $order->mc_actual;
//                    $dpRouteOrder->kg = $order->kg;
//                    $dpRouteOrder->kg_actual = $order->kg_actual;
//                    $dpRouteOrder->number_places_actual = $order->number_places_actual;
//                    $dpRouteOrder->save(false);
//                }
//            }
//        }

        //

//        TlDeliveryProposal::recalculateExpensesOrder($deliveryProposal->id);

        // найти все направления к которым добавлена эта машина и сложить их
        // TODO!!! Сделать правильный пересчет заполнености машины!!!!!
//        if($routeCars = TlDeliveryProposalRouteTransport::findAll(['tl_delivery_proposal_route_id'=>$this->id])) {
            //VarDumper::dump($routeCar, 10, true); die();

//            foreach($routeCars as $routeCar){
//               $car = $routeCar->routeCar;
//                $car->mc_filled += $routeCar->mc_actual;
//                $car->kg_filled += $routeCar->kg_actual;
//                $car->save(false);
//            }
//        }

        // изменение спатуса

//        VarDumper::dump($insert,10,true);
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($changedAttributes,10,true);
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($this,10,true);
//        die;

        switch($this->status) {
            case self::STATUS_NEW:
                break;
            case self::STATUS_ON_ROUTE: // в пути

                // Устанавливаем статус для заявки
//                $dp = TlDeliveryProposal::findOne($this->tl_delivery_proposal_id);
//                $dp->status = TlDeliveryProposal::STATUS_ON_ROUTE;
//                $dp->save(false);
//
//                // Устанавливаем статус на машины которые везут заявку
//                if($carItems = $this->getCarItems()->all()) {
//                    foreach ($carItems as $item) {
//                        $item->status = TlDeliveryProposalRouteCars::STATUS_ON_ROUTE;
//                        $item->save(false);
//                    }
//                }
                break;

            case self::STATUS_DELIVERED: // Доставлен

                $dp = TlDeliveryProposal::findOne($this->tl_delivery_proposal_id);

                $statusArray = [];

                if($dp && $routes = $dp->getProposalRoutes()->all()) {
                    foreach ($routes as $item ) {
                        if(isset($statusArray[$item->status])) {
                            $statusArray[$item->status] = count($statusArray[$item->status]);
                        } else {
                            $statusArray[$item->status] = 1;
                        }
                    }
                }

                // TODO нужно эту логику засунуть в отдельный метод или компонет
                // STATUS_UNDEFINED = 0; //не указан
                // STATUS_NEW = 1; //новый
                // STATUS_ON_ROUTE = 2; //в дороге
                // STATUS_DELIVERED = 3; //доставлен
                // STATUS_DONE = 4;  //выполнен

//                if(!empty($statusArray)) {
//                    foreach($statusArray as $k=>$item) {
//                        // если к заявке добавлена одно заявка и она выполнена
//                        if($k == self::STATUS_DELIVERED && count($statusArray) == $item) {
//                            $dp->status = TlDeliveryProposal::STATUS_DELIVERED;
//                            $dp->save(false);
//                        }
//                    }
//                }

                break;

            case self::STATUS_DONE: // выполнен
//                $dp = TlDeliveryProposal::findOne($this->tl_delivery_proposal_id);
//
//                $statusArray = [];
//
//                if($routes = $dp->getProposalRoutes()->all()) {
//                    foreach ($routes as $item ) {
//                        if(isset($statusArray[$item->status])) {
//                            $statusArray[$item->status] = count($statusArray[$item->status]);
//                        } else {
//                            $statusArray[$item->status] = 1;
//                        }
//                    }
//                }

                // TODO нужно эту логику засунуть в отдельный метод или компонет
                // STATUS_UNDEFINED = 0; //не указан
                // STATUS_NEW = 1; //новый
                // STATUS_ON_ROUTE = 2; //в дороге
                // STATUS_DELIVERED = 3; //доставлен
                // STATUS_DONE = 4;  //выполнен

//                if(!empty($statusArray)) {
//                    foreach($statusArray as $k=>$item) {
//                        // если к заявке добавлена одно заявка и она выполнена
//                        if($k == self::STATUS_DONE && count($statusArray) == $item) {
//                            $dp->status = TlDeliveryProposal::STATUS_DONE;
//                            $dp->save(false);
//                        }
//                    }
//                }

                break;

            default:
                break;
        }

//        $e = new DpEvent();
//        $e->deliveryProposalId = $this->tl_delivery_proposal_id;
////        $e->deliveryRouteId = $this->id;
//        Event::trigger(TlDeliveryProposal::className(),TlDeliveryProposal::EVENT_RECALCULATE,$e);

    }

    /*
 * This method is called at the beginning of inserting or updating a record.
 *
 * */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //S: Manage status


//            self::recalculateExpensesRoute($this->id);


            //E: Manage status

            return true;
        } else {
            return false;
        }
    }





    /*
     * Recalculate Recalculate Expenses delivery proposal route
    * */
//    public function recalculateExpensesRoute()
//    {
//        $priceExpensesCache = 0;
//        $priceExpensesWithVatTotal = 0;
//
//        // Get all cars on route
//        if($carItems = $this->getCarItems()->all()) {
//            foreach ($carItems as $item) {
//
//                $routeOnCarsCount = $item->getRoutes()->count();
//
//                $priceExpensesCache += $item->price_invoice / $routeOnCarsCount;
//                $priceExpensesWithVatTotal += $item->price_invoice_with_vat / $routeOnCarsCount;
//            }
//        }
//
//        // Get all expenses on route
//        if ($unforeseenExpenses = $this->getTlDeliveryProposalRouteUnforeseenExpenses()->all()) {
//            foreach ($unforeseenExpenses as $item) {
//                $priceExpensesCache += $item->price_cache;
//                $priceExpensesWithVatTotal += $item->price_with_vat;
//            }
//        }
//
//        $this->price_invoice = $priceExpensesCache;
//        $this->price_invoice_with_vat = $priceExpensesWithVatTotal;
//        $this->save(false);
//    }
}