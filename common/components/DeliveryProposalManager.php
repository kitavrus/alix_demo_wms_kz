<?php
namespace common\components;

use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\product\models\Product;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\product\models\ProductBarcodes;
use common\modules\billing\components\BillingManager;
use common\modules\transportLogistics\models\TlOutboundRegistry;
use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use common\helpers\DateHelper;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use common\modules\agentBilling\components\AgentBillingManager;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses;
use common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute;

class DeliveryProposalManager extends Component
{
    /*
     * Current Delivery Proposal
     * */
    private $_proposalID;


    /*
     * init data
     * @param integer $id DeliveryProposal id
      * */
    public function __construct($config)
    {
        $this->_proposalID = $config['id'];
        parent::__construct();
    }

    public function onCreateProposal()
    {
        return $this->recalculateOrdersData()
                    ->recalculateRoutesData()
                    ->recalculateExpenses()
                    ->recalculateDeliveryPrice();

    }

    public function onUpdateProposal()
    {
        return $this->recalculateOrdersData()
                    ->recalculateRoutesData()
                    ->setCascadeRoutesDate()
                    ->setCascadeDeliveryDate()
                    ->setCascadedStatus()
                    ->recalculateExpenses()
                    ->recalculateDeliveryPrice();

    }


    public function onChangeRouteCar()
    {
        return $this->recalculateOrdersData()
                    ->recalculateRoutesData()
                    ->setCascadeRoutesDate()
                    ->setCascadeDeliveryDate()
                    ->setCascadedStatus()
                    ->recalculateExpenses()
                    ->recalculateDeliveryPrice();

    }

    public function onAddRegistry()
    {
        return $this ->recalculateRoutesData()
                    ->recalculateExpenses()
                    ->recalculateDeliveryPrice();

    }

    public function onChangeOrder()
    {
        return $this->recalculateOrdersData()
                    ->recalculateRoutesData()
                    ->setCascadeRoutesDate()
                    ->setCascadeDeliveryDate()
                    ->setCascadedStatus()
                    ->recalculateExpenses()
                    ->recalculateDeliveryPrice();

    }

    public function onChangeRoute()
    {
        return $this->recalculateOrdersData()
                    ->recalculateRoutesData()
                    ->setCascadeRoutesDate()
                    ->setCascadeDeliveryDate()
                    ->setCascadedStatus()
                    ->recalculateExpenses()
                    ->recalculateDeliveryPrice();

    }

    public function onEditable()
    {
        return $this->setCascadeRoutesDate()
                    ->setCascadeDeliveryDate()
                    ->setCascadedStatus();

    }

    public function onPrintTtn()
    {
        return $this->setCascadeDeliveryDate()
                    ->setCascadeRoutesDate()
                    ->setDateLeftWarehouse();
    }

    public function onRecalculateBilling()
    {
        return $this->recalculateOrdersData()
                    ->recalculateRoutesData()
                    ->recalculateExpenses()
                    ->recalculateDeliveryPrice();
    }

    /*
     * Высчитываем данные из заказов
     * Вес, обьем, места
     * @return self
     **/
    public function recalculateOrdersData()
    {
        $dp = $this->findProposal();

            $mc = 0;
            $kg = 0;
            $number_places = 0;
            $orderNumbers = '';

            //берем все Delivery Orders заявки
            if ($orders = $dp->getProposalOrders()->all()) {
                foreach ($orders as $order) {
                    if ($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK) {
                        if ($mainOrder = $order->crossDockOrder) {
//                            $mainOrder->accepted_number_places_qty = $order->number_places;
                            $mainOrder->accepted_number_places_qty = $order->number_places_actual;
                            $mainOrder->box_m3 = $order->mc;
                            $mainOrder->save(false);
                        }
                    } elseif ($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
                        if ($mainOrder = $order->outboundOrder) {
                            //Обновляем информацию про вес, обьем, места в связанной записи в OutboundOrders или Cross Dock
//                            $mainOrder->order_number = $order->order_number;
                            $mainOrder->title = $order->title;
                            $mainOrder->description = $order->description;
                            $mainOrder->mc = $order->mc;
                            $mainOrder->kg = $order->kg;
                            if($order->number_places_actual < 1) {
                                $mainOrder->accepted_number_places_qty = $order->number_places;
                            } else {
                                $mainOrder->accepted_number_places_qty = $order->number_places_actual;
                            }
                            $mainOrder->save(false);
                        }
                    }

                    if($oe = $order->extras){
                        foreach($oe as $e){
                            $number_places += $e->number_places;
                        }
                    }

                    $mc += $order->mc;
                    $kg += $order->kg;
                    $number_places += $order->number_places;
                    $orderNumbers .= $order->order_number . ' / М3: '.$order->mc.' / МЕСТ: '.$order->number_places.' ;, ';
                }

                $orderNumbers = trim($orderNumbers, ', ');

                if ($dp->change_mckgnp == TlDeliveryProposal::CHANGE_AUTOMATIC_MC_KG_NP_YES) {
                    $dp->kg = $kg;
                    $dp->mc = $mc;
                    $dp->number_places = $number_places;

                    $dp->kg_actual = $kg;
                    $dp->mc_actual = $mc;
                    $dp->number_places_actual = $number_places;
                }

                //S: Save extra fields
                $dp->saveExtraFieldValue('orders', $orderNumbers);
                //E: Save extra fields

                //S:
                $dp->CreateModifyBusinessLogicData();
                //E:

                $dp->save(false);
            }



        return $this;
    }

    /*
     * Высчитываем расходы и доход
     * @return self
     **/
    public function recalculateExpenses()
    {
        $dp = $this->findProposal();

        if ($routes = $dp->getProposalRoutes()->all()) {
            foreach ($routes as $route) {
                $priceExpensesCache = 0;
                $priceExpensesWithVatTotal = 0;

                // Get all cars on route
                if($carItems = $route->getCarItems()->all()) {
                    foreach ($carItems as $item) {

                        $routeOnCarsCount = $item->getRoutes()->count();

                        $priceExpensesCache += $item->price_invoice / $routeOnCarsCount;
                        $priceExpensesWithVatTotal += $item->price_invoice_with_vat / $routeOnCarsCount;
                    }
                }

                // Get all expenses on route
                if ($unforeseenExpenses = $route->getTlDeliveryProposalRouteUnforeseenExpenses()->all()) {
                    foreach ($unforeseenExpenses as $item) {
                        $priceExpensesCache += $item->price_cache;
                        $priceExpensesWithVatTotal += $item->price_with_vat;
                    }
                }

                $route->price_invoice = $priceExpensesCache;
                $route->price_invoice_with_vat = $priceExpensesWithVatTotal;
                $route->save(false);
            }
        }

        if ($routes = $dp->getProposalRoutes()->all()) {

            $priceExpensesCache = 0;
            $priceExpensesWithVatTotal = 0;

            foreach ($routes as $model) {
                $priceExpensesCache += $model->price_invoice;
                $priceExpensesWithVatTotal += $model->price_invoice_with_vat;
            }

            $dp->price_expenses_total = $priceExpensesCache + $priceExpensesWithVatTotal;
            $dp->price_expenses_with_vat = $priceExpensesWithVatTotal;
            $dp->price_expenses_cache = $priceExpensesCache;

        } else {
            $dp->price_expenses_total = 0;
            $dp->price_expenses_with_vat = 0;
            $dp->price_expenses_cache = 0;
        }

        $dp->save(false);

        return $this;
    }

    /*
     * Высчитываем стоимость доставки по тарифу и доход
     * @return bool
     **/
    public function recalculateDeliveryPrice()
    {
        $dp = $this->findProposal();
        if ($dp->change_price == TlDeliveryProposal::CHANGE_AUTOMATIC_PRICE_YES &&
            !in_array($dp->status_invoice, [TlDeliveryProposal::INVOICE_PAID, TlDeliveryProposal::INVOICE_SET])
        ) {
            $bm = new BillingManager();
            $dp->price_invoice = $bm->getInvoicePriceForDP($dp, false);
            $dp->price_invoice_with_vat = $bm->getInvoicePriceForDP($dp);

            if(!empty($dp->price_invoice) && !empty($dp->price_expenses_total)) {
                $dp->price_our_profit = $dp->price_invoice - $dp->price_expenses_total;
            }

            $dp->save(false);
        }

        return $this;
    }


    /*
     * Update routes and routes cars kg, mc, places
     * and dates
     * @return self
     **/
    public function recalculateRoutesData()
    {
        if ($dp = $this->findProposal()) {
            if ($routes = $dp->getProposalRoutes()->all()) {
                foreach ($routes as $route) {
                    if ($routeCarItems = $route->getCarsByRoute()->all()) {

                        foreach ($routeCarItems as $dpRouteCar) {
                            $dpRouteCar->mc = $dp->mc;
                            $dpRouteCar->mc_actual = $dp->mc_actual;
                            $dpRouteCar->kg = $dp->kg;
                            $dpRouteCar->kg_actual = $dp->kg_actual;
                            $dpRouteCar->number_places = $dp->number_places;
                            $dpRouteCar->number_places_actual = $dp->number_places_actual;
                            $dpRouteCar->save(false);

                            $mc = $kg = 0;

                            if( $car = TlDeliveryProposalRouteCars::findOne($dpRouteCar->tl_delivery_proposal_route_cars_id) ) {
                                if($trRoutes = $car->getTransportItems()->all()) {

                                    foreach($trRoutes as $tRoute) {
                                        $mc += $tRoute->mc_actual;
                                        $kg += $tRoute->kg_actual;

                                    }
                                }

                                $car->mc_filled = $mc;
                                $car->kg_filled = $kg;

                                $price = AgentBillingManager::getInvoicePriceForCar($car, $route->route_from, $route->route_to,$dp->id, false);
                                $priceVat = AgentBillingManager::getInvoicePriceForCar($car, $route->route_from, $route->route_to,$dp->id);

                                if ($price){
                                    $car->price_invoice = $price;
                                }

                                if ($priceVat){
                                    $car->price_invoice_with_vat = $priceVat;
                                }

                                $car->save(false);
                            }

                        }
                    }


                }
            }
        }

        return $this;
    }

    /*
    * Update routes and routes cars dates
    * @return self
    **/
    public function setCascadeRoutesDate()
    {
        if ($dp = $this->findProposal()) {
            if ($routes = $dp->getProposalRoutes()->all()) {
                foreach ($routes as $route) {
                    $route->delivery_date = $dp->delivery_date;
                    $route->shipped_datetime = $dp->shipped_datetime;
                    $route->accepted_datetime = $dp->accepted_datetime;
                    $route->save(false);
                    if ($routeCarItems = $route->getCarsByRoute()->all()) {
                        foreach ($routeCarItems as $dpRouteCar) {
                            if ($car = $dpRouteCar->routeCar) {
                                $car->delivery_date = $dp->delivery_date;
                                $car->shipped_datetime = $dp->shipped_datetime;
                                $car->accepted_datetime = $dp->accepted_datetime;
                                $car->save(false);
                            }
                        }
                    }
                }
            }

        }

        return $this;
    }


    public function setCascadedStatus()
    {
        if ($dp = $this->findProposal()) {
            //s: Если у заявки нет маршрута
            $routsAndCars = [];

            $status = '';
            if ($routeItems = $dp->getProposalRoutes()->all()) {
                foreach ($routeItems as $rItem) {
                    $routsAndCars[$rItem->id] = $rItem->getCarItems()->count();
                }
            }

            // Если у заявки нет ни одного маршрута
            if (empty($routsAndCars)) {
                $status = TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP;
            } elseif (in_array('0', $routsAndCars)) {
                // Проверяем если у маршрутов  количество машин ровно 0
                $status = TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE;
            }

            // E: Если у заявки нет маршрута

            // Проверяем в маршрутах заполнены ли поля m3
            if (empty($dp->mc) && in_array($dp->client_id, [2])) {
                $status = TlDeliveryProposal::STATUS_NOT_ADDED_M3;
            }

            if (empty($status)) {
                $status = TlDeliveryProposal::STATUS_ROUTE_FORMED;
            }

            $validArray = [
                TlDeliveryProposal::STATUS_ON_ROUTE,
                TlDeliveryProposal::STATUS_DELIVERED,
                TlDeliveryProposal::STATUS_DONE,
            ];

            if ($status != TlDeliveryProposal::STATUS_ROUTE_FORMED) {
                $dp->status = $status;
            } elseif (!in_array($dp->status, $validArray)) {
                $dp->status = TlDeliveryProposal::STATUS_ROUTE_FORMED;
            }

            //$dp->save(false);


            // STATUS_UNDEFINED = 0; //не указан
            // STATUS_NEW = 1; //новый
            // STATUS_ON_ROUTE = 2; //в дороге
            // STATUS_DELIVERED = 3; //доставлен
            // STATUS_DONE = 4;  //выполнен
            // STATUS_ADD_CAR = 5;  //добавлена машина
            // STATUS_ADD_ROUTE_TO_DP = 6;  //Добавьте маршрут к заявке
            // STATUS_ADD_CAR_TO_ROUTE = 7;  //Добавьте к маршруту машину
            // STATUS_ROUTE_FORMED = 8;  //Маршрут сформирован
            // STATUS_NOT_ADDED_M3 = 9;  //Не заполнен m3
            // STATUS_NOT_ADDED_M3_ON_ROUTE = 10;  //Не заполнен m3 в маршруте


            switch ($dp->status) {

                case TlDeliveryProposal::STATUS_ROUTE_FORMED:

                    $this->updateRouteItemsDateAndStatus(TlDeliveryProposalRouteCars::STATUS_CAR_ADDED_TO_ROUTE);
                    break;

                case TlDeliveryProposal::STATUS_NEW:

                    $this->updateRouteItemsDateAndStatus(TlDeliveryProposalRouteCars::STATUS_CAR_ADDED_TO_ROUTE);
                    break;

                case TlDeliveryProposal::STATUS_ON_ROUTE:

                    $this->updateRouteItemsDateAndStatus(TlDeliveryProposalRouteCars::STATUS_ON_ROUTE);
                    break;

                case TlDeliveryProposal::STATUS_DELIVERED: // Доставлен

                    $this->updateRouteItemsDateAndStatus(TlDeliveryProposalRouteCars::STATUS_DELIVERED);

                    if ($orderItems = $dp->getProposalOrders()->all()) {
                        foreach ($orderItems as $oItem) {
                            if ($oItem->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK) {
                                if ($order = $oItem->crossDockOrder) {
                                    $order->status = Stock::STATUS_OUTBOUND_DELIVERED;
                                    $order->cargo_status = OutboundOrder::CARGO_STATUS_DELIVERED;
                                    $order->save(false);
                                }
                            } elseif ($oItem->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT) {
                                if ($order = $oItem->outboundOrder) {
                                    $order->status = Stock::STATUS_OUTBOUND_DELIVERED;
                                    $order->cargo_status = OutboundOrder::CARGO_STATUS_DELIVERED;
                                    $order->save(false);
                                }
                            }
                        }
                    }

                    break;

                case TlDeliveryProposal::STATUS_DONE:

                    $this->updateRouteItemsDateAndStatus(TlDeliveryProposalRouteCars::STATUS_DONE);
                    break;

                default:
                    break;
            }

            $dp->save(false);
        }
        return $this;
    }

    /*
     * Устанавливаем дату доставки для связанных заказов
     * @return self
     **/
    public function setCascadeDeliveryDate()
    {
        $dp = $this->findProposal();

        if($dp->delivery_date){
            if ($relatedOrders = $dp->proposalOrders) {
                foreach ($relatedOrders as $order) {
                    if($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT){
                        if ($oo = $order->outboundOrder) {
                            $oo->date_delivered = $dp->delivery_date;
                            $oo->save(false);
                        }
                    } elseif ($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK){
                        if ($cd = $order->crossDockOrder) {
                            $cd->date_delivered = $dp->delivery_date;
                            $cd->save(false);
                        }
                    }

                }
            }
        }

        return $this;
    }

    /*
     * Устанавливаем дату отгрузки для связанных заказов
     * Используется при печать ТТН
     * @return self
      **/
    public function setDateLeftWarehouse()
    {
        $dp = $this->findProposal();

        if ($relatedOrders = $dp->proposalOrders) {
            foreach ($relatedOrders as $order) {
                if($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_RPT){
                    if ($oo = $order->outboundOrder) {
                        $oo->date_left_warehouse = $dp->shipped_datetime;
                        $oo->status = Stock::STATUS_OUTBOUND_ON_ROAD;
                        $oo->save(false);
                    }
                }  elseif($order->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK){
                    if ($cd = $order->crossDockOrder) {
                        $cd->date_left_warehouse = $dp->shipped_datetime;
                        $cd->status = Stock::STATUS_OUTBOUND_ON_ROAD;
                        $cd->cargo_status = OutboundOrder::CARGO_STATUS_ON_ROUTE;
                        $cd->save(false);
                    }
                }

            }
        }

        return $this;
    }

    /*
     * Добавляем заранее созданный транспорт к первому маршруту DP
     * $ueData = [
     *  'ue_name' => 1,
     *  'ue_price_cache' => 1,
     *  'ue_cash_no' => 1,
     *  'ue_who_pays' => 1,
     *  'ue_comment' => 1,
     * ];
     * @param array $ueData if set then add unforseen expences to route
     * @param int $routeCarID
     * @return self
      **/
    public function addCarToFirstRoute($routeCarID, $ueData=[])
    {
        $dp = $this->findProposal();
        $modelDpRouteCar=TlDeliveryProposalRouteCars::findOne($routeCarID);

        //записываем данные о транспорте в заявку на доставку
        $dp->agent_id = $modelDpRouteCar->agent_id;
        $dp->car_id =  $modelDpRouteCar->car_id;
        $dp->driver_name = $modelDpRouteCar->driver_name;
        $dp->driver_phone = $modelDpRouteCar->driver_phone;
        $dp->driver_auto_number = $modelDpRouteCar->driver_auto_number;
        $dp->car_price_invoice = $modelDpRouteCar->price_invoice;
        $dp->car_price_invoice_with_vat = $modelDpRouteCar->price_invoice_with_vat;
        $dp->save(false);

        //создаем связь для первого маршрута и транспорта
        if ($dpRoutes = $dp->proposalRoutes){
            $modelDpRoute = isset ($dpRoutes[0]) ? $dpRoutes[0] : 0;
            if($modelDpRoute){
                $modelDpRouteTransport = new TlDeliveryProposalRouteTransport();
                $modelDpRouteTransport->mc = $dp->mc;
                $modelDpRouteTransport->mc_actual = $dp->mc_actual;
                $modelDpRouteTransport->kg = $dp->kg;
                $modelDpRouteTransport->kg_actual = $dp->kg_actual;
                $modelDpRouteTransport->number_places = $dp->number_places;
                $modelDpRouteTransport->number_places_actual = $dp->number_places_actual;
                $modelDpRouteTransport->tl_delivery_proposal_route_id = $modelDpRoute->id;
                $modelDpRouteTransport->tl_delivery_proposal_route_cars_id = $modelDpRouteCar->id;
                $modelDpRouteTransport->tl_delivery_proposal_id = $modelDpRoute->tl_delivery_proposal_id;
                $modelDpRouteTransport->save();

                if($ueData){
                    $routeUe = new TlDeliveryProposalRouteUnforeseenExpenses();
                    $routeUe->name = isset($ueData['name']) ? $ueData['name'] : '-';
                    $routeUe->price_cache = isset($ueData['price_cache']) ? $ueData['price_cache'] : 0;
                    $routeUe->cash_no = isset($ueData['cash_no']) ? $ueData['cash_no'] : 0;
                    $routeUe->who_pays = isset($ueData['who_pays']) ? $ueData['who_pays'] : 0;
                    $routeUe->comment = isset($ueData['comment']) ? $ueData['comment'] : '-';
                    $routeUe->tl_delivery_proposal_id = $dp->id;
                    $routeUe->tl_delivery_route_id= $modelDpRoute->id;
                    $routeUe->client_id= $dp->client_id;
                    $routeUe->save(false);
                }

            }
        }
        //пересчитываем стоимость транспорта для каждой DP где есть этот транспорт
        self::recalculateCarProposals($modelDpRouteCar->id);

        return $this;
    }

    /*
     * Создаем транспорт
     * $data = [
     *  'agent_id' => 1,
     *  'car_id' => 1,
     *  'car_id' => 1,
     *  'driver_name' => 1,
     *  'driver_phone' => 1,
     *  'driver_auto_number' => 1,
     *  'car_price_invoice' => 1,
     *  'car_price_invoice_with_vat' => 1,
     * ];
     * @return TlDeliveryProposalRouteCars
      **/
    public static function createRouteCar(array $data)
    {

        $modelDpRouteCar = new TlDeliveryProposalRouteCars();
        $modelDpRouteCar->status = TlDeliveryProposalRouteCars::STATUS_CAR_ADDED_TO_ROUTE;
        $modelDpRouteCar->agent_id = isset($data['agent_id']) ? $data['agent_id'] : 0;
        $modelDpRouteCar->car_id = isset($data['car_id']) ? $data['car_id'] : 0;
        $modelDpRouteCar->driver_name = isset($data['driver_name']) ? $data['driver_name'] : '-';
        $modelDpRouteCar->driver_phone = isset($data['driver_phone']) ? $data['driver_phone'] : '-';
        $modelDpRouteCar->driver_auto_number = isset($data['driver_auto_number']) ? $data['driver_auto_number'] : '-';
        $modelDpRouteCar->route_city_from  = isset($data['route_from']) ? Store::findOne($data['route_from'])->city_id : 0;
        $modelDpRouteCar->route_city_to  = isset($data['route_to']) ? Store::findOne($data['route_to'])->city_id : 0;
        $modelDpRouteCar->price_invoice = isset($data['price_invoice']) ? $data['price_invoice'] : 0;
        $modelDpRouteCar->price_invoice_with_vat = isset($data['price_invoice_with_vat']) ? $data['price_invoice_with_vat'] : 0;
        $modelDpRouteCar->cash_no = TlDeliveryProposal::METHOD_CHAR;
        $modelDpRouteCar->save(false);

        return $modelDpRouteCar;
    }


    protected function findProposal()
    {
        if ($dp = TlDeliveryProposal::findOne($this->_proposalID)) {
            return $dp;
        }
        throw new NotFoundHttpException('The requested record not found.');
    }

    /*
     * Устанавливаем даты и статусы для маршрутов и машин
     * в заявке
     * @param TlDeliveryProposalRouteCars status
     * @return self
     **/
    public function updateRouteItemsDateAndStatus($status)
    {
        $dp = $this->findProposal();

        if ($routeItems = $dp->getProposalRoutes()->all()) {
            foreach ($routeItems as $rItem) {
                if ($carItems = $rItem->getCarItems()->all()) {
                    foreach ($carItems as $cItem) {
                        $cItem->status = $status;
                        $cItem->shipped_datetime = $dp->shipped_datetime;
                        $cItem->accepted_datetime = $dp->accepted_datetime;
                        $cItem->delivery_date = $dp->delivery_date;

                        $cItem->save(false);
                    }
                }
                $rItem->status = $dp->status;
                $rItem->save(false);
            }
        }

        return $this;
    }

    /*
    * Генерируем маршруты по умолчанию для текущей заявки
    * @param bool $addCar Флаг добавляем ли машины к маршрутам или нет
    * @return bool
    **/
    public function generateDefaultRoutes($addCar = true, $defaultRouteID = null)
    {
        $dp = $this->findProposal();
        $defaultRoute = TlDeliveryProposalDefaultRoute::find()->andWhere(['from_point_id' => $dp->route_from, 'to_point_id' => $dp->route_to])->one();

        if(!is_null($defaultRouteID)) {
            $defaultRoute = TlDeliveryProposalDefaultRoute::findOne($defaultRouteID);
        }

        if($defaultRoute) {
            if($subRoutes = $defaultRoute->subRoutes){
                //VarDumper::dump(count($subRoutes), 10, true); die;
                foreach ($subRoutes as $k=>$route) {

                    $tlDeliveryRoute = new TlDeliveryRoutes();
                    $tlDeliveryRoute->route_from = $route->from_point_id;
                    $tlDeliveryRoute->route_to = $route->to_point_id;
                    $tlDeliveryRoute->delivery_date = $dp->delivery_date;
                    $tlDeliveryRoute->shipped_datetime = $dp->shipped_datetime;
                    $tlDeliveryRoute->accepted_datetime = $dp->accepted_datetime;
                    $tlDeliveryRoute->transportation_type = $route->transport_type;
                    $tlDeliveryRoute->tl_delivery_proposal_id = $dp->id;
                    $tlDeliveryRoute->client_id = $dp->client_id;
                    $tlDeliveryRoute->save(false);

                    if($unforeseenExpensesDefault = $route->getTlDeliveryProposalRouteUnforeseenExpenses()->all()) {
                        foreach($unforeseenExpensesDefault as $uEDKey=>$uEDValue) {
                            $dpRUEModel = new TlDeliveryProposalRouteUnforeseenExpenses();
                            $dpRUEModel->client_id =  $tlDeliveryRoute->client_id;
                            $dpRUEModel->tl_delivery_proposal_id = $tlDeliveryRoute->tl_delivery_proposal_id;
                            $dpRUEModel->tl_delivery_route_id = $tlDeliveryRoute->id;
                            $dpRUEModel->type_id = $uEDValue->type_id;
                            $dpRUEModel->name = $uEDValue->name;
                            $dpRUEModel->price_cache = $uEDValue->price_cache;
                            $dpRUEModel->cash_no = $uEDValue->cash_no;
                            $dpRUEModel->comment = $uEDValue->comment;
                            $dpRUEModel->who_pays = $uEDValue->who_pays;
                            $dpRUEModel->save(false);
                        }
                    }


                    $defaultCar = $route->car;
                    $defaultAgent = $route->agent;

                    if(($defaultCar && $defaultAgent && $addCar) || (empty($defaultCar) && empty($defaultAgent) && $k == 0) ) {

                        $model = new TlDeliveryProposalRouteCars();
                        $modelDpRouteCar = new TlDeliveryProposalRouteTransport();
                        $model->status = TlDeliveryProposalRouteCars::STATUS_CAR_ADDED_TO_ROUTE;

                        if($defaultCar && $defaultAgent && $addCar) {
                            $model->driver_name = '-';
                            $model->driver_phone = '-';
                            $model->driver_auto_number = '-';
                            $model->agent_id = $route->agent_id;
                            $model->car_id = $route->car_id;
                        } elseif(empty($defaultCar) && empty($defaultAgent) && $k == 0) {
                            $model->driver_name = $dp->driver_name;
                            $model->driver_phone = $dp->driver_phone;
                            $model->driver_auto_number = $dp->driver_auto_number;
                            $model->agent_id = $dp->agent_id;
                            $model->car_id = $dp->car_id;
                        }

                        if( !empty($defaultCar) && !empty($defaultAgent) && empty($dp->agent_id) && $k == 0 ) {
                            $dp->driver_name = '-';
                            $dp->driver_phone = '-';
                            $dp->driver_auto_number = '-';
                            $dp->agent_id = $route->agent_id;
                            $dp->car_id = $route->car_id;
                            $dp->save(false);
                        }

                        $model->delivery_date = $tlDeliveryRoute->delivery_date;
                        $model->shipped_datetime = $tlDeliveryRoute->shipped_datetime;
                        $model->accepted_datetime = $tlDeliveryRoute->accepted_datetime;

                        $dp_from = $tlDeliveryRoute->getRouteFrom()->one();
                        $dp_to = $tlDeliveryRoute->getRouteTo()->one();

                        $model->route_city_from  = $dp_from->city->id;
                        $model->route_city_to  = $dp_to->city->id;
                        $model->cash_no = TlDeliveryProposal::METHOD_CHAR;
                        $model->save(false);

                        $modelDpRouteCar->mc = $dp->mc;
                        $modelDpRouteCar->mc_actual = $dp->mc_actual;
                        $modelDpRouteCar->kg = $dp->kg;
                        $modelDpRouteCar->kg_actual = $dp->kg_actual;
                        $modelDpRouteCar->number_places = $dp->number_places;
                        $modelDpRouteCar->number_places_actual = $dp->number_places_actual;

                        $modelDpRouteCar->tl_delivery_proposal_route_id = $tlDeliveryRoute->id;
                        $modelDpRouteCar->tl_delivery_proposal_route_cars_id = $model->id;
                        $modelDpRouteCar->tl_delivery_proposal_id = $tlDeliveryRoute->tl_delivery_proposal_id;
                        $modelDpRouteCar->save(false);
                    }
                }
                return true;
            }
        }
        return false;
    }

    /*
     * Get defaults sub routes
     * */
    public function getCountDefaultRoutes()
    {
        $dp = $this->findProposal();
        return  TlDeliveryProposalDefaultRoute::find()->andWhere(['from_point_id' => $dp->route_from, 'to_point_id' => $dp->route_to])->all();
    }

    /*
    * Пересчитываем стоимость доставки для всех заявок
    * Где есть этот автомобиль
    * @param TlDeliveryProposalRouteCars id
    * @return self
    **/
    public static function recalculateCarProposals($id)
    {
        if($car = TlDeliveryProposalRouteCars::findOne($id)) {
            if ($r = $car->getRoutes()->all()) {
                foreach ($r as $rItem) {
                    if( $tlDp = TlDeliveryProposal::findOne($rItem->tl_delivery_proposal_id)) {
                        $dpManager = new DeliveryProposalManager(['id'=>$tlDp->id]);
                        $dpManager->onChangeRouteCar();
                    }
                }
            }
        }

    }

    /*
   * Выставляем статус свободная
   * Если у машины нет маршрутов
   * @param TlDeliveryRoutes id
   * @return self
   **/
    public function freeRouteCar($id)
    {
        $dpRoute = TlDeliveryRoutes::findOne($id);

        if($carItems = $dpRoute->getCarItems()->all()) {
            foreach ($carItems as $cItem) {

                if($modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id'=>$cItem->id,'tl_delivery_proposal_route_id'=>$dpRoute->id])) {
                    $modelDpRouteCar->deleted = TlDeliveryProposal::SHOW_DELETED;
                    $modelDpRouteCar->save(false);
                }

                if ($cItem->getRoutes()->count() < 1) {
                    $cItem->status  = TlDeliveryProposalRouteCars::STATUS_FREE;
                    $cItem->save(false);
                }
            }
        }

        return $this;
    }

    /*
     * Пересчитываем вес, обьем, места в реестре
     * @param TlOutboundRegistry id
     * @return bool
     **/
    public static function recalculateRegistryData($id)
    {
        $dpRegistry = TlOutboundRegistry::findOne($id);

        $volume = 0;
        $weight = 0;
        $places = 0;

        if($rItems = $dpRegistry->getRegistryItems()->all()) {
            foreach ($rItems as $rItem) {
                $volume += $rItem->volume;
                $weight += $rItem->weight;
                $places += $rItem->places;
            }
        }

        $dpRegistry->volume = $volume;
        $dpRegistry->weight = $weight;
        $dpRegistry->places = $places;
        $dpRegistry->save(false);

        return;
    }

    /*
     * Get volumetric weight
     * @param number $m3
     * @return number
     * */
    public static function getVolumetricWeight($m3)
    {
        $weightVolumeIndex = 0;
        if($m3) {
            $index = AgentBillingManager::getWeightVolumeIndex();
            $weightVolumeIndex = $m3 * $index;
        }

//       if($weightVolumeIndex >= $dRouteCar->kg_filled){}
//       else($weightVolumeIndex < $dRouteCar->kg_filled){}
//       4059 - в него положить 3881, 3879, 3877, 3878, 3876, 3882, 3880
        // 503548

        return $weightVolumeIndex;
    }

}