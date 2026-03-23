<?php

namespace app\modules\tms\controllers;

use common\modules\city\models\RouteDirections;
use stockDepartment\components\Controller;

use common\components\BookkeeperManager;
use common\helpers\DateHelper;
use common\models\ActiveRecord;
use common\modules\bookkeeper\models\Bookkeeper;
use common\modules\city\models\City;
use common\modules\city\models\Country;
use common\modules\city\models\Region;
use common\modules\client\models\Client;
use common\modules\codebook\models\Codebook;
use common\modules\transportLogistics\models\TlAgents;
use stockDepartment\modules\tms\models\CarModelPopup;
use stockDepartment\modules\tms\models\SelectSubRouteDefault;
use stockDepartment\modules\tms\models\TlDeliveryProposalSearchExport;
use yii\helpers\ArrayHelper;
use common\modules\transportLogistics\models\TlDeliveryProposalOrderExtras;
use Yii;
use common\components\DeliveryProposalManager;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use stockDepartment\modules\tms\models\TlDeliveryProposalSearch;
use stockDepartment\modules\tms\models\SelectRouteCar;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlCars;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\client\models\ClientEmployees;
use common\events\DpEvent;
use common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute;
use yii\web\Response;
use stockDepartment\modules\tms\models\OldTtnForm;
/**
 * Default controller for the `operatorDella` module
 */
class DefaultController extends Controller
{

//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * Lists all TlDeliveryProposal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlDeliveryProposalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['not', ['status_invoice'=>TlDeliveryProposal::INVOICE_PAID]]);
//        $dataProvider->query->andWhere('status_invoice != :status_invoice OR delivery_date IS NULL', [':status_invoice'=>TlDeliveryProposal::INVOICE_PAID]);

        $clientArray = Client::getActiveTMSItems();
        $storeArray = TLHelper::getStockPointArray(null,false,false,"full");
        $cityArray = City::getArrayData();
        $regionArray = Region::getArrayData();
        $countryArray = Country::getArrayData();
        $routeDirectionArray = RouteDirections::getArrayData();

//        $q = $dataProvider->query;
//        $points =
//        VarDumper::dump($q->select('route_to')->limit(50)->column(),10,true);
//        die;
        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');
        $tomorrow= $tomorrow->format('Y-m-d');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientArray' => $clientArray,
            'storeArray' => $storeArray,
            'tomorrow' => $tomorrow,
            'cityArray' => $cityArray,
            'regionArray' => $regionArray,
            'countryArray' => $countryArray,
            'routeDirectionArray' => $routeDirectionArray,
        ]);
    }



    /**
     * Displays a single TlDeliveryProposal model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        //$view = '';
        if(( $model->status_invoice == TlDeliveryProposal::INVOICE_PAID || $model->status_invoice == TlDeliveryProposal::INVOICE_SET ||
                $model->ready_to_invoicing == TlDeliveryProposal::READY_TO_INVOICING_YES) && (!empty($model->delivery_date))
        ) {
            $view = 'no-edit/view-no-edit';
        } else {
            $view = 'view';
        }
        $model->setScenario('create-update-manager-warehouse');

        if ($model->load(Yii::$app->request->post())) {
//            if(in_array($model->company_transporter,BillingManager::getTransportCompanyOutTariff())) {
//                $model->price_invoice = 0;
//                $model->price_invoice_with_vat = 0;
//            }
            if ($model->save()) {

//                $model->recalculateExpensesOrder();
//                $model->setCascadedStatus();

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
            }
        } else {
        }


        $dataProviderProposalOrders = new ActiveDataProvider([
            'query' => $model->getProposalOrders(),
        ]);

        $dataProviderProposalRoutes = new ActiveDataProvider([
            'query' => $model->getProposalRoutes(),
        ]);

        $storeArray = TLHelper::getStockPointArray();

        return $this->render($view, [
            'model' => $model,
            'dataProviderProposalOrders' => $dataProviderProposalOrders,
            'dataProviderProposalRoutes' => $dataProviderProposalRoutes,
            'storeArray' => $storeArray,
        ]);
    }

    /**
     * Creates a new TlDeliveryProposal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlDeliveryProposal();

        $model->setScenario('create-update-manager-warehouse');

        if ($model->load(Yii::$app->request->post())) {
            $model->source = TlDeliveryProposal::SOURCE_OUR_OPERATOR;

            if($model->save()) {

                $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
                $dpManager->onCreateProposal();

                if($model->cash_no == TlDeliveryProposal::METHOD_CASH) {
                    BookkeeperManager::createOrUpdate([
                        'type_id' => Bookkeeper::TYPE_PLUS,
                        'tl_delivery_proposal_id' => $model->id,
                        'tl_delivery_proposal_route_unforeseen_expenses_id' => null,
                        'department_id' => Bookkeeper::DEPARTMENT_TRANSPORT,
                        'doc_type_id' => Bookkeeper::DOC_TYPE_NO_CHECK,
                        'name_supplier' => 'DELLA',
                        'description' => $model->comment,
                        'price' => $model->price_invoice,
//                       'date_at' => $created_at,
						'date_at' =>  $model->created_at,
                        'cash_type' => $model->cash_no,
                        'expenses_type_id' => null,
                        'unique_key' => 'DP-'.$model->id.'-'.Bookkeeper::TYPE_PLUS.'-'.Bookkeeper::DEPARTMENT_TRANSPORT,
                    ]);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $model->cash_no = TlDeliveryProposal::METHOD_CHAR;
		$model->client_id = Client::CLIENT_ERENRETAIL;
        $model->company_transporter = TlDeliveryProposal::COMPANY_TRANSPORTER_EFFECTIVE_ENGINEERING;

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TlDeliveryProposal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if( $model->status_invoice == TlDeliveryProposal::INVOICE_PAID ||
            $model->status_invoice == TlDeliveryProposal::INVOICE_SET ||
            $model->ready_to_invoicing == TlDeliveryProposal::READY_TO_INVOICING_YES
        ){
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->setScenario('create-update-manager-warehouse');

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
                $dpManager->onUpdateProposal();

                if($model->cash_no == TlDeliveryProposal::METHOD_CASH) {
                    BookkeeperManager::createOrUpdate([
                        'type_id' => Bookkeeper::TYPE_PLUS,
                        'tl_delivery_proposal_id' => $model->id,
                        'tl_delivery_proposal_route_unforeseen_expenses_id' => null,
                        'department_id' => Bookkeeper::DEPARTMENT_TRANSPORT,
                        'doc_type_id' => Bookkeeper::DOC_TYPE_NO_CHECK,
                        'name_supplier' => 'DELLA',
                        'description' => $model->comment,
                        'price' => $model->price_invoice,
//                      'date_at' => $created_at,
                        'date_at' =>  $model->created_at,
                        'cash_type' => $model->cash_no,
                        'expenses_type_id' => null,
                        'unique_key' => 'DP-'.$model->id.'-'.Bookkeeper::TYPE_PLUS.'-'.Bookkeeper::DEPARTMENT_TRANSPORT,
                    ]);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Finds the TlDeliveryProposal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findOrder($id)
    {
        if (($model = TlDeliveryProposalOrders::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Delivery Proposal Orders does not exist.');
        }
    }


    /*
     * Add orders to delivery proposal
     * @param integer id delivery proposal
     *
     * */
//    public function actionStep2($id)
//    {
//        $deliveryProposalModel = $this->findModel($id);
//        $model = new TlDeliveryProposalOrders();
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['step3', 'id' => $model->id]);
//        } else {
//            return $this->render('step2', [
//                'deliveryProposalModel' => $deliveryProposalModel,
//                'model' => $model,
//            ]);
//        }
//    }


    /*
     * Add routes to delivery proposal
     * @param integer id delivery proposal
     *
     * */
//    public function actionStep3($id)
//    {
//        $deliveryProposalModel = $this->findModel($id);
//        $model = new TlDeliveryProposal();
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['step4', 'id' => $model->id]);
//        } else {
//            return $this->render('step3', [
//                'deliveryProposalModel' => $deliveryProposalModel,
//                'model' => $model,
//            ]);
//        }
//    }

    /*
    * Form. Add new route
    * @param integer $route_id
    * */
    public function actionAddNewRouteCar($route_id)
    {
        $modelDpRoute = TlDeliveryRoutes::findOne($route_id);
        $model = new TlDeliveryProposalRouteCars();
        $modelDpRouteCar = new TlDeliveryProposalRouteTransport();

        $model->status = TlDeliveryProposalRouteCars::STATUS_CAR_ADDED_TO_ROUTE;

        //S: Если у заявки есть несколько маршрутов, то ставил "-" для имени водителя и телефон и номер авто
        $dp = TlDeliveryProposal::findOne($modelDpRoute->tl_delivery_proposal_id);

        if( $dp->getProposalRoutes()->count()  > 1) {
            $model->driver_name = '-';
            $model->driver_phone = '-';
            $model->driver_auto_number = '-';
        } else {
            $model->agent_id = $dp->agent_id;
            $model->car_id = $dp->car_id;
            $model->driver_name = $dp->driver_name;
            $model->driver_phone = $dp->driver_phone;
            $model->driver_auto_number = $dp->driver_auto_number;
        }

        $model->delivery_date = $modelDpRoute->delivery_date;
        $model->shipped_datetime = $modelDpRoute->shipped_datetime;
        $model->accepted_datetime = $modelDpRoute->accepted_datetime;
        $dp_from = $modelDpRoute->getRouteFrom()->one();
        $dp_to = $modelDpRoute->getRouteTo()->one();

        $model->route_city_from  = $dp_from->city->id;
        $model->route_city_to  = $dp_to->city->id;
        $model->cash_no = TlDeliveryProposal::METHOD_CHAR;

        $modelDpRouteCar->mc = $dp->mc;
        $modelDpRouteCar->mc_actual = $dp->mc_actual;
        $modelDpRouteCar->kg = $dp->kg;
        $modelDpRouteCar->kg_actual = $dp->kg_actual;
        $modelDpRouteCar->number_places = $dp->number_places;
        $modelDpRouteCar->number_places_actual = $dp->number_places_actual;
//        }
        //E: Если у заявки есть несколько маршрутов, то ставил "-" для имени водителя и телефон и номер авто


        if ($model->load(Yii::$app->request->post()) && $model->save(false) && ( $modelDpRouteCar->load(Yii::$app->request->post()) && $modelDpRouteCar->save(false)) ) {
            //TODO попробовать сделать черех link unlink
            $modelDpRouteCar->tl_delivery_proposal_route_id = $route_id;
            $modelDpRouteCar->tl_delivery_proposal_route_cars_id = $model->id;
            $modelDpRouteCar->tl_delivery_proposal_id = $modelDpRoute->tl_delivery_proposal_id;
            $modelDpRouteCar->save();

//            if(!$model->price_invoice && !$model->price_invoice_with_vat){
//                $model->save(false);
//                $model->calculateCarPrice($dp_from->id, $dp_to->id);
//            }

            $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
            $dpManager->onChangeRouteCar();

            if($model->cash_no == ActiveRecord::METHOD_CASH) {
                $created_at = TlDeliveryProposal::findOne($modelDpRoute->tl_delivery_proposal_id)->shipped_datetime;

                $cityNameFrom = '';
                if($c =  City::findOne($model->route_city_from)) {
                    $cityNameFrom = $c->name;
                }
                $cityNameTo = '';
                if($c =  City::findOne($model->route_city_to)) {
                    $cityNameTo = $c->name;
                }
                $agentName = '';
                if($c =  TlAgents::findOne($model->agent_id)) {
                    $agentName = $c->name;
                }

                $description = $cityNameFrom.' '.$cityNameTo;

                BookkeeperManager::createOrUpdate([
                    'type_id' => Bookkeeper::TYPE_MINUS,
                    'tl_delivery_proposal_id' => $modelDpRoute->tl_delivery_proposal_id,
//                    'tl_delivery_proposal_route_unforeseen_expenses_id' => null,
                    'department_id' => Bookkeeper::DEPARTMENT_TRANSPORT,
                    'doc_type_id' => Bookkeeper::DOC_TYPE_NO_CHECK,
                    'name_supplier' => "Машина :".$agentName." ".$model->driver_name . ' ( ' . $model->driver_auto_number . ' ) ',
                    'description' => $description,
                    'price' => $model->price_invoice,
//                    'date_at' => $created_at,
                    'date_at' =>  $model->created_at,
                    'cash_type' => $model->cash_no,
                    'unique_key' => 'CAR-'.$model->id.'-'.$modelDpRoute->tl_delivery_proposal_id.'-'.$modelDpRouteCar->id.'-'.Bookkeeper::TYPE_MINUS.'-'.Bookkeeper::DEPARTMENT_TRANSPORT,
                ]);
            }


            return $this->redirect(['view', 'id' => $modelDpRoute->tl_delivery_proposal_id,'#'=>'title-order']);
        }
        return $this->render('_car-to-route-form', [
            'model' => $model,
            'modelDpRoute' => $modelDpRoute,
            'modelDpRouteCar' => $modelDpRouteCar,
        ]);
    }

    /*
    * Form. Update car on route
    *
    * */
    public function actionUpdateRouteCar($id,$route_id)
    {
        $model = TlDeliveryProposalRouteCars::findOne($id);
        $modelDpRoute = TlDeliveryRoutes::findOne($route_id);
        $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id'=>$id,'tl_delivery_proposal_route_id'=>$route_id]);

        if ( ($model->load(Yii::$app->request->post()) && $model->save(false))
            && ($modelDpRouteCar->load(Yii::$app->request->post()) && $modelDpRouteCar->save(false))
        ) {
            $dpManager = new DeliveryProposalManager(['id'=>$modelDpRoute->tl_delivery_proposal_id]);
            $dpManager->onChangeRouteCar();
            DeliveryProposalManager::recalculateCarProposals($id);

            if($model->cash_no == ActiveRecord::METHOD_CASH) {
                $created_at = TlDeliveryProposal::findOne($modelDpRoute->tl_delivery_proposal_id)->shipped_datetime;

                $cityNameFrom = '';
                if($c =  City::findOne($model->route_city_from)) {
                    $cityNameFrom = $c->name;
                }
                $cityNameTo = '';
                if($c =  City::findOne($model->route_city_to)) {
                    $cityNameTo = $c->name;
                }

                $agentName = '';
                if($c =  TlAgents::findOne($model->agent_id)) {
                    $agentName = $c->name;
                }

                $description = $cityNameFrom.' '.$cityNameTo;

                BookkeeperManager::createOrUpdate([
                    'type_id' => Bookkeeper::TYPE_MINUS,
                    'tl_delivery_proposal_id' => $modelDpRoute->tl_delivery_proposal_id,
//                    'tl_delivery_proposal_route_unforeseen_expenses_id' => null,
                    'department_id' => Bookkeeper::DEPARTMENT_TRANSPORT,
                    'doc_type_id' => Bookkeeper::DOC_TYPE_NO_CHECK,
                    'name_supplier' => "Машина :".$agentName." ".$model->driver_name . ' ( ' . $model->driver_auto_number . ' ) ',
                    'description' => $description,
                    'price' => $model->price_invoice,
//                    'date_at' => $created_at,
                    'date_at' =>  $model->created_at,
                    'cash_type' => $model->cash_no,
                    'unique_key' => 'CAR-'.$model->id.'-'.$modelDpRoute->tl_delivery_proposal_id.'-'.$modelDpRouteCar->id.'-'.Bookkeeper::TYPE_MINUS.'-'.Bookkeeper::DEPARTMENT_TRANSPORT,
                ]);
            }

            return $this->redirect(['view', 'id' => $modelDpRoute->tl_delivery_proposal_id,'#'=>'title-order']);
        }

        return $this->render('_car-to-route-form', [
            'model' => $model,
            'modelDpRouteCar'=>$modelDpRouteCar,
            'dpr_city_to' => null,
            'dpr_city_from' => null,
        ]);
    }


    /*
     * Select route car
     * @param integer $route_id Delivery Proposal route id
     *
     * */
    public function actionSelectRouteCar($route_id)
    {
        $modelDpRoute = TlDeliveryRoutes::findOne($route_id);
        $model = new SelectRouteCar();

        if ($model->load(Yii::$app->request->post())) {

            $dpRouteCar = new TlDeliveryProposalRouteTransport();
            $dpRouteCar->tl_delivery_proposal_route_id = $route_id;
            $dpRouteCar->tl_delivery_proposal_route_cars_id = $model->route_car_id;

            if($dp = TlDeliveryProposal::findOne($modelDpRoute->tl_delivery_proposal_id)) {
                $dpRouteCar->mc = $dp->mc;
                $dpRouteCar->mc_actual = $dp->mc_actual;
                $dpRouteCar->kg = $dp->kg;
                $dpRouteCar->kg_actual = $dp->kg_actual;
                $dpRouteCar->number_places = $dp->number_places;
                $dpRouteCar->number_places_actual = $dp->number_places_actual;
            }

            $dpRouteCar->save();

            //S: Подсчитываем расходи на машины

            //TLManager::recalculateDpAndDpr($modelDpRoute->tl_delivery_proposal_id,$route_id);
            $dpManager = new DeliveryProposalManager(['id'=>$modelDpRoute->tl_delivery_proposal_id]);
            $dpManager->onChangeRouteCar();
            DeliveryProposalManager::recalculateCarProposals($model->route_car_id);
            //S: MOVED TO DP MANAGER
//             if($car = TlDeliveryProposalRouteCars::findOne($model->route_car_id)) {
//                 if ($r = $car->getRoutes()->all()) {
//                     foreach ($r as $rItem) {
//                         if( $tlDp = TlDeliveryProposal::findOne($rItem->tl_delivery_proposal_id)) {
////                             if ($routes = $tlDp->getProposalRoutes()->all()) {
////                                 foreach ($routes as $route) {
////                                     $route->recalculateExpensesRoute();
////                                 }
////                             }
//                             $dpManager1 = new DeliveryProposalManager(['id'=>$tlDp->id]);
//                             $dpManager1->onUpdate();
//                         }
//                     }
//                 }
//             }
            //E:




            //E: Подсчитываем расходи на машины

            return $this->redirect(['view', 'id' => $modelDpRoute->tl_delivery_proposal_id,'#'=>'title-order']);
        }

        $dp_from = $modelDpRoute->getRouteFrom()->one();
        $dp_to = $modelDpRoute->getRouteTo()->one();

        $dpr_city_from = $dp_from->city->id;
        $dpr_city_to = $dp_to->city->id;


        $excludeUsedId = $modelDpRoute->getCarItems()->asArray()->select('id')->column();


        return $this->render('select-route-car',[
            'model' => $model,
            'dpr_city_to' => $dpr_city_to,
            'dpr_city_from' => $dpr_city_from,
            'excludeUsedId' => $excludeUsedId,
            'modelDpRoute' => $modelDpRoute,
        ]);
    }

    /*
     * Delete route car
     * @param integer $id Delivery route car id
    * */
    public function actionDeleteRouteCar($id,$route_id)
    {
        $model = TlDeliveryProposalRouteCars::findOne($id);
        // TODO fix Добавить проверку для связи многие ко многим delete 0
//        TlDeliveryProposalRouteTransport::updateAll(['deleted'=>1],['tl_delivery_proposal_route_cars_id'=>$model->id,'tl_delivery_proposal_route_id'=>$route_id]);
//        TlDeliveryProposalRouteTransport::DeleteAll(['tl_delivery_proposal_route_cars_id'=>$model->id,'tl_delivery_proposal_route_id'=>$route_id]);

        $modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id'=>$id,'tl_delivery_proposal_route_id'=>$route_id]);

        $modelDpRoute = TlDeliveryRoutes::findOne($route_id);
        $proposal_id = $modelDpRoute->tl_delivery_proposal_id;

        BookkeeperManager::deleteByUniqueKey('CAR-'.$model->id.'-'.$modelDpRoute->tl_delivery_proposal_id.'-'.$modelDpRouteCar->id.'-'.Bookkeeper::TYPE_MINUS.'-'.Bookkeeper::DEPARTMENT_TRANSPORT);
        $modelDpRouteCar->delete();
        // TODO сделать это через события
        //Проверяем прикреплена ли машина к другим маршрутам
        if ($model->getRoutes()->count() > 0) {
            $model->status  = TlDeliveryProposalRouteCars::STATUS_FREE;
            $model->save(false);
        } else {
            $model->deleted = TlDeliveryProposalRouteCars::SHOW_DELETED;
            $model->save(false);
            // TODO
        }

        //TLManager::recalculateDpAndDpr($modelDpRoute->tl_delivery_proposal_id,$route_id);
        $dpManager = new DeliveryProposalManager(['id'=>$modelDpRoute->tl_delivery_proposal_id]);
        $dpManager->onChangeRouteCar();
        DeliveryProposalManager::recalculateCarProposals($model->id);

        //S: MOVED TO DP MANAGER
//        if($car = TlDeliveryProposalRouteCars::findOne($model->id)) {
//            if ($r = $car->getRoutes()->all()) {
//                foreach ($r as $rItem) {
//                    if( $tlDp = TlDeliveryProposal::findOne($rItem->tl_delivery_proposal_id)) {
////                        if ($routes = $tlDp->getProposalRoutes()->all()) {
////                            foreach ($routes as $route) {
////                                $route->recalculateExpensesRoute();
////                            }
////                        }
//                        //TLManager::recalculateDpAndDpr($tlDp->id);
//                        $dpManager = new DeliveryProposalManager(['id'=>$tlDp->id]);
//                        $dpManager->onUpdate();
//                    }
//                }
//            }
//        }
        //E:

        return $this->redirect(['view','id'=>$proposal_id,'#'=>'title-route']);
    }


    /*
     * Add new order to proposal
     * @param integer $proposal_id Delivery proposal id
     * */
    public function actionAddOrder($proposal_id)
    {
        $deliveryProposalModel = $this->findModel($proposal_id);
        $model = new TlDeliveryProposalOrders();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $dpManager = new DeliveryProposalManager(['id'=>$deliveryProposalModel->id]);
            $dpManager->onChangeOrder();
            return $this->redirect(['view', 'id' => $proposal_id,'#'=>'title-order']);
        } else {
            return $this->render('add-order', [
                'deliveryProposalModel' => $deliveryProposalModel,
                'model' => $model,
            ]);
        }
    }

    /*
     * Copy DP and re-assign DP order to it
     * @param integer $proposal_id Delivery proposal id
     * @param integer $proposalOrderId Delivery proposal order id
     * */
    public function actionCopyOrder($proposal_id, $proposal_order_id)
    {
        $deliveryProposal = $this->findModel($proposal_id);
        $deliveryProposalOrder = $this->findOrder($proposal_order_id);

        if($deliveryProposal && $deliveryProposalOrder && $deliveryProposalOrder->tl_delivery_proposal_id == $deliveryProposal->id){
            $newDp = new TlDeliveryProposal();
            $newDp->setAttributes($deliveryProposal->getAttributes(null, ['id']), false);
            if( $newDp->save(false)){
                $deliveryProposalOrder->tl_delivery_proposal_id = $newDp->id;
                $deliveryProposalOrder->save(false);
                //пересчитываем заявку которую создали
                $dpManager1 = new DeliveryProposalManager(['id'=>$newDp->id]);
                $dpManager1->onChangeOrder();

                if($extras = $deliveryProposalOrder->extras) {
                    foreach ($extras as $e) {
                        $e->tl_delivery_proposal_id = $newDp->id;
                        $e->save(false);
                    }
                }
                //пересчитываем заявку из которой копировали
                $dpManager2 = new DeliveryProposalManager(['id'=>$deliveryProposal->id]);
                $dpManager2->onChangeOrder();

                return $this->redirect(['view', 'id' => $deliveryProposalOrder->tl_delivery_proposal_id,'#'=>'title-order']);
            }
        }
        throw new NotFoundHttpException('Вы уже сделали копию этого заказа');
    }

    /*
     * Soft delete DPs and assign DP orders to the last one
     * @return mixed
     * */
    public function actionMergeProposalOrders()
    {
        // проверяем чтобы принадлежало одному клиенты
        // небыл в статусе в пути, доставлен, выполнен


        if($ids = Yii::$app->request->post('ids')){
            arsort($ids);
            $targetProposalId = array_shift($ids);
            // Заявка в которую мы переносим заказы
            if($model = TlDeliveryProposal::find()
                ->andWhere(['id'=> $targetProposalId])
                ->andWhere(['not in', 'status', [
                    TlDeliveryProposal::STATUS_ON_ROUTE,
                    TlDeliveryProposal::STATUS_DELIVERED,
                    TlDeliveryProposal::STATUS_DONE
                ]
                ])
                ->one()) {

                foreach($ids as $checkId){
                    if(!$checkProposal = TlDeliveryProposal::find()
                        ->andWhere([
                            'id'=>$checkId,
                            'client_id'=>$model->client_id,
                        ])
                        ->andWhere(['not in', 'status', [
                            TlDeliveryProposal::STATUS_ON_ROUTE,
                            TlDeliveryProposal::STATUS_DELIVERED,
                            TlDeliveryProposal::STATUS_DONE
                        ]])
                        ->one()
                    ){
                        Yii::$app->session->setFlash('danger', 'Заявка номер <b>['.$checkId.']</b> имеет неверный статус или принадлежит другому клиенту');
                        return $this->redirect(['index']);
                    }
                }

                foreach($ids as $id){
                    $dp = TlDeliveryProposal::find()->andWhere(['id'=>$id, 'client_id'=>$model->client_id])->one();
                    if($dpOrders = $dp->proposalOrders){
                        foreach ($dpOrders as $order){
                            $order->tl_delivery_proposal_id = $targetProposalId;
                            $order->save(false);
                        }
                    }

                    $dp->deleted = TlDeliveryProposal::SHOW_DELETED;
                    $dp->save(false);
                }

                if($model->save(false)){
                    $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
                    $dpManager->onUpdateProposal();

                    return $this->redirect(['view', 'id' => $model->id,'#'=>'title-order']);
                }

            } else {
                Yii::$app->session->setFlash('danger', 'Заявка номер <b>['.$targetProposalId.']</b> имеет неверный статус или принадлежит другому клиенту');
                return $this->redirect(['index']);
            }
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /*
     * Soft delete DPs and assign DP orders to the last one
     * @return mixed
     * */
    public function actionMoveOrdersToFirstOrder()
    {
        // проверяем чтобы принадлежало одному клиенты
        // небыл в статусе в пути, доставлен, выполнен

        if ($ids = Yii::$app->request->post('ids')) {
            arsort($ids);
            $targetProposalId = array_shift($ids);
            // Заявка в которую мы переносим заказы
            if ($model = TlDeliveryProposal::find()
                ->andWhere(['id' => $targetProposalId])
                ->andWhere(['not in', 'status', [
                    TlDeliveryProposal::STATUS_ON_ROUTE,
                    TlDeliveryProposal::STATUS_DELIVERED,
                    TlDeliveryProposal::STATUS_DONE
                ]
                ])
                ->one()
            ) {

                foreach ($ids as $checkId) {
                    if (!$checkProposal = TlDeliveryProposal::find()
                        ->andWhere([
                            'id' => $checkId,
                            'client_id' => $model->client_id,
                        ])
                        ->andWhere(['not in', 'status', [
                            TlDeliveryProposal::STATUS_ON_ROUTE,
                            TlDeliveryProposal::STATUS_DELIVERED,
                            TlDeliveryProposal::STATUS_DONE
                        ]])
                        ->one()
                    ) {
                        Yii::$app->session->setFlash('danger', 'Заявка номер <b>[' . $checkId . ']</b> имеет неверный статус или принадлежит другому клиенту');
                        return $this->redirect(['index']);
                    }
                }

                foreach ($ids as $id) {
                    $dp = TlDeliveryProposal::find()->andWhere(['id' => $id, 'client_id' => $model->client_id])->one();
                    if ($dpOrders = $dp->proposalOrders) {
                        foreach ($dpOrders as $order) {
                            $order->tl_delivery_proposal_id = $targetProposalId;
                            $order->save(false);
                        }
                    }

                    $dp->deleted = TlDeliveryProposal::SHOW_DELETED;
                    $dp->save(false);
                }

                if ($model->save(false)) {
                    $dpManager = new DeliveryProposalManager(['id' => $model->id]);
                    $dpManager->onUpdateProposal();

                    return $this->redirect(['view', 'id' => $model->id, '#' => 'title-order']);
                }

            } else {
                Yii::$app->session->setFlash('danger', 'Заявка номер <b>[' . $targetProposalId . ']</b> имеет неверный статус или принадлежит другому клиенту');
                return $this->redirect(['index']);
            }
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /*
    * Update new order to proposal
    * @param integer $id Delivery proposal order id
    * */
    public function actionUpdateOrder($id)
    {
        $model = TlDeliveryProposalOrders::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->save(false);
            $dpManager = new DeliveryProposalManager(['id'=>$model->tl_delivery_proposal_id]);
            $dpManager->onChangeOrder();
            return $this->redirect(['view', 'id' => $model->tl_delivery_proposal_id,'#'=>'title-order']);
        } else {
            return $this->render('add-order', [
                'deliveryProposalModel' => $this->findModel($model->tl_delivery_proposal_id),
                'model' => $model,
            ]);
        }
    }

    /*
    * Delete order in proposal
    * @param integer $id Delivery proposal order id
    * */
    public function actionDeleteOrder($id)
    {
        $model = TlDeliveryProposalOrders::findOne($id);
        $proposal_id = $model->tl_delivery_proposal_id;
        $model->deleted = 1;
        $model->save(false);

        $dpManager = new DeliveryProposalManager(['id'=>$proposal_id]);
        $dpManager->onChangeOrder();
        return $this->redirect(['view','id'=>$proposal_id,'#'=>'title-order']);
    }

    /*
     * Add new route to proposal
     * @param integer $proposal_id Delivery proposal id
     * */
    public function actionAddRoute($proposal_id)
    {
        $deliveryProposalModel = $this->findModel($proposal_id);

        $model = new TlDeliveryRoutes();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $dpManager = new DeliveryProposalManager(['id'=>$deliveryProposalModel->id]);
            $dpManager->onChangeRoute();
            return $this->redirect(['view', 'id' => $proposal_id,'#'=>'title-route']);
        } else {

            $model->route_from = $deliveryProposalModel->route_from;
            $model->route_to = $deliveryProposalModel->route_to;
            $model->mc = $deliveryProposalModel->mc;
            $model->mc_actual = $deliveryProposalModel->mc_actual;
            $model->kg = $deliveryProposalModel->kg;
            $model->kg_actual = $deliveryProposalModel->kg_actual;
            $model->number_places = $deliveryProposalModel->number_places;
            $model->number_places_actual = $deliveryProposalModel->number_places_actual;
            $model->delivery_date = $deliveryProposalModel->delivery_date;
            $model->shipped_datetime = $deliveryProposalModel->shipped_datetime;
            $model->accepted_datetime = $deliveryProposalModel->accepted_datetime;


            return $this->render('route-to-dp-form', [
                'deliveryProposalModel' => $deliveryProposalModel,
//                'dp_city_to' => $dp_to->city->id,
//                'dp_city_from' => $dp_from->city->id,

//                'dpr_city_to' => null,
//                'dpr_city_from' => null,
                'model' => $model,
            ]);
        }
    }

    /*
    * Update route for proposal
    * @param integer $id Delivery Proposal Route id
    * */
    public function actionUpdateRoute($id)
    {
        $model = TlDeliveryRoutes::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

//            TLManager::recalculateDpAndDpr($model->tl_delivery_proposal_id,$model->id);
            $dpManager = new DeliveryProposalManager(['id'=>$model->tl_delivery_proposal_id]);
            $dpManager->onChangeRoute();

            return $this->redirect(['view', 'id' => $model->tl_delivery_proposal_id,'#'=>'title-route']);
        } else {

            $deliveryProposalModel  = $this->findModel($model->tl_delivery_proposal_id);
//            $dp_from = $deliveryProposalModel->getRouteFrom()->one();
//            $dp_to = $deliveryProposalModel->getRouteTo()->one();
//
//            $dpr_from = $model->getRouteFrom()->one();
//            $dpr_to = $model->getRouteTo()->one();
//
            return $this->render('route-to-dp-form', [

//                'dp_city_to' => $dp_to->city->id,
//                'dp_city_from' => $dp_from->city->id,
//
//                'dpr_city_to' => $dpr_to->city->id,
//                'dpr_city_from' => $dpr_from->city->id,

                'deliveryProposalModel' => $deliveryProposalModel,
                'model' => $model,
            ]);
        }
    }

    /*
    * Delete route in proposal
    * @param integer $id Delivery route id
    * */
    public function actionDeleteRoute($id)
    {
        $model = TlDeliveryRoutes::findOne($id);
        $proposal_id = $model->tl_delivery_proposal_id;
        $dpManager = new DeliveryProposalManager(['id'=>$proposal_id]);
        $dpManager->freeRouteCar($model->id);

        //MOVED TO DELIVERY PROPOSAL
        //Нужно найти все машины прикрепленные к маршруту и освободить их
//        if($carItems = $model->getCarItems()->all()) {
//            foreach ($carItems as $cItem) {
//
//                if($modelDpRouteCar = TlDeliveryProposalRouteTransport::findOne(['tl_delivery_proposal_route_cars_id'=>$cItem->id,'tl_delivery_proposal_route_id'=>$model->id])) {
//                    $modelDpRouteCar->deleted = 1;
//                    $modelDpRouteCar->save(false);
//                }
//
//                if ($cItem->getRoutes()->count() < 1) {
//                    $cItem->status  = TlDeliveryProposalRouteCars::STATUS_FREE;
//                    $cItem->save(false);
//                }
//            }
//        }

        $model->deleted = TlDeliveryProposal::SHOW_DELETED;
        $model->save(false);


        $dpManager->onChangeRoute();

        return $this->redirect(['view','id'=>$proposal_id,'#'=>'title-route']);
    }

    /*
     *
     *
     * */
    public function actionAddRouteOrder(){}

    /*
     * Form. Add new store route
     *
     * */
    public function actionAddStoreRoute()
    {
        $model = new Store();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = 'json';
            return [
                'message' => 'Success',
                'data_options' => TLHelper::getStockPointArray(),
            ];
        }

        $model->status = Store::STATUS_ACTIVE;

        return $this->renderAjax('_create-store-route', [
            'model' => $model,
        ]);
    }

    /*
     * Get List cars by agent id
     *
     *
     * */
    public function actionGetCarsByAgent()
    {
        $currentAgentID = Yii::$app->request->post('agent_id');
        Yii::$app->response->format = 'json';
        return [
            'message' => 'Success',
            'data_options' => ArrayHelper::map(TlCars::find()->where(['agent_id'=>$currentAgentID])->orderBy('title')->all(),'id','title'),
        ];
    }

    /*
     * Get List cars by agent id
     *
     *
     * */
    public function actionGetRoutesByClient()
    {
        $currentClientID = Yii::$app->request->post('client_id');
        Yii::$app->response->format = 'json';
        return [
            'message' => 'Success',
            'data_options' => TLHelper::getStockPointArray($currentClientID,true,false,'stock',[1]),
        ];
    }


    /*
      * Add new unforeseen expenses to route
      * @param integer $route_id Delivery proposal route id
      * */
    public function actionAddRouteUnforeseenExpenses($route_id)
    {
        $modelDRoute = TlDeliveryRoutes::findOne($route_id);
        $model = new TlDeliveryProposalRouteUnforeseenExpenses();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $dpManager = new DeliveryProposalManager(['id'=>$model->tl_delivery_proposal_id]);
            $dpManager->onChangeRoute();

            if($model->cash_no == ActiveRecord::METHOD_CASH) {
                $created_at = TlDeliveryProposal::findOne($model->tl_delivery_proposal_id)->shipped_datetime;
                BookkeeperManager::createOrUpdate([
                    'type_id' => Bookkeeper::TYPE_MINUS,
                    'tl_delivery_proposal_id' => $model->tl_delivery_proposal_id,
                    'tl_delivery_proposal_route_unforeseen_expenses_id' => $model->id,
                    'department_id' => Bookkeeper::DEPARTMENT_TRANSPORT,
                    'doc_type_id' => Bookkeeper::DOC_TYPE_NO_CHECK,
                    'name_supplier' => $model->name . ' ( ' . $model->getTypeValue() . ' ) ',
                    'description' => $model->comment,
                    'price' => $model->price_cache,
//                    'date_at' => $created_at,
                    'date_at' =>  $model->created_at,
                    'cash_type' => $model->cash_no,
                    'expenses_type_id' => $model->type_id,
                    'unique_key' => 'EXP-'.$model->tl_delivery_proposal_id.'-'.$model->id.'-'.Bookkeeper::TYPE_MINUS.'-'.Bookkeeper::DEPARTMENT_TRANSPORT,
                ]);
            }

            return $this->redirect(['view', 'id' => $model->tl_delivery_proposal_id,'#'=>'title-route']);
        } else {
            return $this->render('route-unforeseen-expenses-form', [
                'model' => $model,
                'modelDRoute' => $modelDRoute,
            ]);
        }
    }

    /*
    * Update unforeseen expenses for route
    * @param integer $id Unforeseen Expenses id
    * */
    public function actionUpdateRouteUnforeseenExpenses($id)
    {
        $model = TlDeliveryProposalRouteUnforeseenExpenses::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

//            TLManager::recalculateDpAndDpr($model->tl_delivery_proposal_id,$model->tl_delivery_route_id);
            $dpManager = new DeliveryProposalManager(['id'=>$model->tl_delivery_proposal_id]);
            $dpManager->onChangeRoute();

            if($model->cash_no == ActiveRecord::METHOD_CASH) {
                $created_at = TlDeliveryProposal::findOne($model->tl_delivery_proposal_id)->shipped_datetime;
                BookkeeperManager::createOrUpdate([
                    'type_id' => Bookkeeper::TYPE_MINUS,
                    'tl_delivery_proposal_id' => $model->tl_delivery_proposal_id,
                    'tl_delivery_proposal_route_unforeseen_expenses_id' => $model->id,
                    'department_id' => Bookkeeper::DEPARTMENT_TRANSPORT,
                    'doc_type_id' => Bookkeeper::DOC_TYPE_NO_CHECK,
                    'name_supplier' => $model->name . ' ( ' . $model->getTypeValue() . ' ) ',
                    'description' => $model->comment,
                    'price' => $model->price_cache,
//                    'date_at' => $created_at,
                    'date_at' =>  $model->created_at,
                    'cash_type' => $model->cash_no,
                    'expenses_type_id' => $model->type_id,
                    'unique_key' => 'EXP-'.$model->tl_delivery_proposal_id.'-'.$model->id.'-'.Bookkeeper::TYPE_MINUS.'-'.Bookkeeper::DEPARTMENT_TRANSPORT,
                ]);
            }

            return $this->redirect(['view', 'id' => $model->tl_delivery_proposal_id,'#'=>'title-route']);
        } else {
            return $this->render('route-unforeseen-expenses-form', [
                'modelDRoute' => TlDeliveryRoutes::findOne($model->tl_delivery_route_id),
                'model' => $model,
            ]);
        }
    }

    /*
    * Delete unforeseen expenses for route
    * @param integer $id Unforeseen Expenses id
    * */
    public function actionDeleteRouteUnforeseenExpenses($id)
    {
        $model = TlDeliveryProposalRouteUnforeseenExpenses::findOne($id);
        $proposal_id = $model->tl_delivery_proposal_id;
        $dprID = $model->tl_delivery_route_id;
        $model->deleted = 1;
        $model->save(false);
        $dpManager = new DeliveryProposalManager(['id'=>$model->tl_delivery_proposal_id]);
        $dpManager->onChangeRoute();
        BookkeeperManager::deleteByUniqueKey('EXP-'.$model->tl_delivery_proposal_id.'-'.$model->id.'-'.Bookkeeper::TYPE_MINUS.'-'.Bookkeeper::DEPARTMENT_TRANSPORT);
//        TLManager::recalculateDpAndDpr($proposal_id,$dprID);

        return $this->redirect(['view','id'=>$proposal_id,'#'=>'title-route']);
    }

    /**
     * Save model attributes via editable fields
     *
     */
    public function actionEditByField()
    {
        $editableIndex = Yii::$app->request->post('editableIndex');
        $model = TlDeliveryProposal::findOne(Yii::$app->request->post('editableKey'));

        if($model && Yii::$app->request->post('hasEditable')){
            Yii::$app->response->format = 'json';
            $formValue = Yii::$app->request->post('TlDeliveryProposal');
            $key = key($formValue[$editableIndex]);
            $model->$key = $formValue[$editableIndex][$key];
            $model->save(false);
            $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
            $dpManager->onEditable();
        }
        return ['output'=>'', 'message' => ''];
    }

    /**
     * Save status and datetime model attributes via editable fields
     *
     */
    public function actionStatusEditByField()
    {
        $editableIndex = Yii::$app->request->post('editableIndex');
        $model = TlDeliveryProposal::findOne(Yii::$app->request->post('editableKey'));

        if($model && Yii::$app->request->post('hasEditable')){
            Yii::$app->response->format = 'json';
            $formValue = Yii::$app->request->post('TlDeliveryProposal');
            $key = key($formValue[$editableIndex]);
            $model->$key = $formValue[$editableIndex][$key];
            $model->delivery_date = $formValue['delivery_date'];
            $model->save(false);
//           TLManager::recalculateDpAndDpr($model->id);
            $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
            $dpManager->onEditable();
        }
        return ['output'=>'', 'message' => ''];
    }


    /**
     * Save model attributes via editable fields
     *
     */
    public function actionEditByFieldRoute()
    {
        $editableIndex = Yii::$app->request->post('editableIndex');
        $model = TlDeliveryRoutes::findOne(Yii::$app->request->post('editableKey'));

        if($model && Yii::$app->request->post('hasEditable')){
            Yii::$app->response->format = 'json';
            $formValue = Yii::$app->request->post('TlDeliveryRoutes');
            $key = key($formValue[$editableIndex]);

            $value = $formValue[$editableIndex][$key];
//            if($key == 'shipped_datetime') {
//                $value = strtotime($value.' 19:20:27');
//            }
            $model->$key =  $value;
            $model->save(false);
//            TLManager::recalculateDpAndDpr($model->tl_delivery_proposal_id,$model->id);
            $dpManager = new DeliveryProposalManager(['id'=>$model->tl_delivery_proposal_id]);
            $dpManager->onEditable();
        }
        return ['output'=>'', 'message' => ''];
    }


    /*
     * Add new order extra data
     * @param integer $id Delivery Proposal Orders
     * */
    public function actionAddRouteOrderExtra($order_id)
    {
        $modelDRouteOrder = TlDeliveryProposalOrders::findOne($order_id);
        $model = new TlDeliveryProposalOrderExtras();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $dpManager = new DeliveryProposalManager(['id'=>$model->tl_delivery_proposal_id]);
            $dpManager->onChangeRoute();
            return $this->redirect(['view','id'=>$model->tl_delivery_proposal_id,'#'=>'title-order']);
        } else {
            return $this->render('form-route-order-extra', [
                'model' => $model,
                'modelDRouteOrder' => $modelDRouteOrder,
            ]);
        }
    }

    /*
     * Update order extra data
     * @param integer $id Delivery Proposal Orders
     * */
    public function actionUpdateRouteOrderExtra($id)
    {
        $model = TlDeliveryProposalOrderExtras::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $dpManager = new DeliveryProposalManager(['id'=>$model->tl_delivery_proposal_id]);
            $dpManager->onChangeRoute();
            return $this->redirect(['view','id'=>$model->tl_delivery_proposal_id,'#'=>'title-order']);
        } else {
            return $this->render('form-route-order-extra', [
                'model' => $model,
                'modelDRouteOrder' => TlDeliveryProposalOrders::findOne($model->tl_delivery_proposal_order_id),
            ]);
        }
    }

    /*
    * Delete order extra for route
    * @param integer $id Route Order Extra id
    * */
    public function actionDeleteRouteOrderExtra($id)
    {
        $model = TlDeliveryProposalOrderExtras::findOne($id);
        $proposal_id = $model->tl_delivery_proposal_id;
        $model->deleted = 1;
        $model->save(false);
        $dpManager = new DeliveryProposalManager(['id'=>$proposal_id]);
        $dpManager->onChangeRoute();
        return $this->redirect(['view','id'=>$proposal_id,'#'=>'title-order']);
    }

    /*
     * Search car by driver_auto_number
     * TODO not used
     * */
//    public function actionSearchByDriverAutoNumber_TO_DELETE()
//    {
//        $searchModel = new TlDeliveryProposalRouteCarsSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('search-by-driver-auto-number', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    /*
     * Print TTN
     *
     * */
    public function actionPrintTtn()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        $userName = '';
        $storeFrom = $model->routeFrom;

        $managersNamesTo = $this->makeSippingContact($model->routeTo,$model->client_id);

        // если отправляем груз со склада, то печатаем 3 копии файла ТТН
        // 4 = DC - это наш склад
        if(in_array($storeFrom->id,[4])) {
            //Yii::$app->formatter->timeZone = 'Asia/Almaty';
            $model->shipped_datetime = DateHelper::getTimestamp();
            $model->status = TlDeliveryProposal::STATUS_ON_ROUTE;
            // Yii::$app->formatter->timeZone = '';
            $model->save(false);

        }
        $dpManager = new DeliveryProposalManager(['id'=>$model->id]);
        $dpManager->onPrintTtn();

        return $this->render('print-ttn-pdf',['model'=>$model,'userName'=>$userName,'managersNamesTo'=>$managersNamesTo]);
    }

    private function makeSippingContact($routeTo,$clientId)
    {
        $managersNamesTo = 'Контакты получателей:' . "<br />";
        if (empty($routeTo) || empty($clientId)) {
           return '';
        }

        // находим всех директоров магазина и отправляем им имейлы
        $clientEmployees = ClientEmployees::find()
            ->andWhere([
                'status'=>ClientEmployees::STATUS_ACTIVE,
                'client_id' => $clientId,
                'store_id' => $routeTo->id,
                'manager_type' => [
                    ClientEmployees::TYPE_BASE_ACCOUNT,
                    ClientEmployees::TYPE_DIRECTOR,
                    ClientEmployees::TYPE_DIRECTOR_INTERN,
                    ClientEmployees::TYPE_MANAGER,
                ]
            ])
            ->all();

        if (empty($clientEmployees)) {
            return '';
        }

        $managersNamesTo .= '<table width="100%" border="0">';
        $managersNamesTo .= "<tr>";
        foreach ($clientEmployees as $item) {
            $managersNamesTo .= "<td width=\"23%\">" . $item->first_name . ' ' . $item->last_name . ' / ' . $item->phone_mobile . ' ' . $item->phone . "</td>";
        }
        $managersNamesTo .= "</tr>";
        $managersNamesTo .= '</table>';

        return $managersNamesTo;
    }


    /*
     *
     * Export data to EXEL
     *
     * */
    public function actionExportToExcel()
    {
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));

        $activeSheet->setCellValue('A1', '');
        $activeSheet->setCellValue('A2', 'Приложения к акту');
        $activeSheet->setCellValue('A3', '');
        $activeSheet->setCellValue('A4', 'Клиент');
//        $activeSheet->setCellValue('B4', '');
        $activeSheet->setCellValue('A5', '');
        $activeSheet->setCellValue('A6', '');
        $activeSheet->setCellValue('H1', date('d/m/Y'));

        $i = 7;
        $activeSheet->setCellValue('A'.$i, 'Из');
        $activeSheet->setCellValue('B'.$i, 'В');
        $activeSheet->setCellValue('C'.$i, 'Дата отгрузки');
        $activeSheet->setCellValue('D'.$i, 'Дата получения');
        $activeSheet->setCellValue('E'.$i, 'Кол-во мест');
        $activeSheet->setCellValue('F'.$i, 'Кол-во кг');
        $activeSheet->setCellValue('G'.$i, 'Кол-во М3');
        $activeSheet->setCellValue('H'.$i, 'Стоимость');
        $activeSheet->setCellValue('I'.$i, 'ID');
        $activeSheet->setCellValue('J'.$i, 'Компания перевозчик');

        $searchModel = new TlDeliveryProposalSearchExport();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dps = $dataProvider->getModels();

        $priceInvoiceWithVatSum = 0;

        foreach($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i, Store::getPointTitle($model->route_from));
            $activeSheet->setCellValue('B' . $i, Store::getPointTitle($model->route_to));

            $shippedDatetime = '';
            if(!empty($model->shipped_datetime)) {
                $shippedDatetime = Yii::$app->formatter->asDate($model->shipped_datetime,'php:d/m/Y');
            }
            $activeSheet->setCellValue('C' . $i, $shippedDatetime);

            $deliveryDatetime = '';
            if($model->delivery_date) {
                $deliveryDatetime = Yii::$app->formatter->asDate($model->delivery_date,'php:d/m/Y');
            }
            $activeSheet->setCellValue('D' . $i, $deliveryDatetime);

            $numberPlacesActual = $model->number_places_actual;
            $activeSheet->setCellValue('E' . $i, $numberPlacesActual);

            $kgActual = $model->kg_actual;
            $activeSheet->setCellValue('F' . $i, $kgActual);

            $mcActual = $model->mc_actual;
            $activeSheet->setCellValue('G' . $i, $mcActual);

            $priceInvoiceWithVat = $model->price_invoice_with_vat;
            $activeSheet->setCellValue('H' . $i, $priceInvoiceWithVat);

            $priceInvoiceWithVatSum += $priceInvoiceWithVat;

            $activeSheet->setCellValue('I' . $i, $model->id);

            $activeSheet->setCellValue('J' . $i, $model->getCompanyTransporterValue());

        }
        $clTitle = '';
        if($cl =  $model->client) {
            $clTitle = $cl->legal_company_name;
        }

        $activeSheet->setCellValue('B4', $clTitle);
        $activeSheet->setCellValue('H' . ($i+1), $priceInvoiceWithVatSum);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }


//    /*
//    * Popup form for add car.
//    *
//    * */
//    public function actionAddCarPopup()
//    {
//        $model = new CarModelPopup();
//
//        if ( $model->load(Yii::$app->request->post()) && $model->validate()) {
//
//            $dpIds = Yii::$app->request->post('ids');
//
//            if(!empty($dpIds)) {
//                $dpIds = explode(',',$dpIds);
//            }
//
//            if(is_array($dpIds)) {
//
//                $update = [
//                    'agent_id' => $model->agent_id,
//                    'car_id' => $model->car_id,
//                    'driver_name' => $model->driver_name,
//                    'driver_phone' => $model->driver_phone,
//                    'driver_auto_number' => $model->driver_auto_number,
//                    'car_price_invoice' => $model->car_price_invoice,
//                    'car_price_invoice_with_vat' => $model->car_price_invoice_with_vat,
//                ];
//
//                $pd = new TlDeliveryProposal();
//                $pd->updateAll($update,['id'=>$dpIds]);
//
//            } else {}
//
//            Yii::$app->response->format = 'json';
//            return [
//                'message' => 'Success',
//            ];
//        }
//
//        return $this->renderAjax('_add-car-popup', [
//            'model' => $model,
//        ]);
//    }

    /*
  * Popup form for add car.
  *
  * */
    public function actionAddFirstRouteCarPopup()
    {
        $model = new CarModelPopup();

        if ( $model->load(Yii::$app->request->post())) {

            $dpIds = Yii::$app->request->post('ids');
            $model->ids = $dpIds;
            $error = '';
            $message = '';

            if($model->validate()){
                if(!empty($dpIds)) {
                    $dpIds = explode(',',$dpIds);
                }

                $firstDp = TlDeliveryProposal::findOne($dpIds[0]);

                if($firstDpRoutes =  $firstDp->proposalRoutes){
                    $firstDpRoute = $firstDpRoutes[0];
                    $dp_from = $firstDpRoute->getRouteFrom()->one();
                    $dp_to = $firstDpRoute->getRouteTo()->one();
                } else {
                    $dp_from = $firstDp->getRouteFrom()->one();
                    $dp_to = $firstDp->getRouteTo()->one();
                }

                $data = [
                    'agent_id' => $model->agent_id,
                    'car_id' => $model->car_id,
                    'driver_name' => $model->driver_name,
                    'driver_phone' => $model->driver_phone,
                    'driver_auto_number' => $model->driver_auto_number,
                    'price_invoice' => $model->car_price_invoice,
                    'price_invoice_with_vat' => $model->car_price_invoice_with_vat,
                    'route_from' => $dp_from->id,
                    'route_to' => $dp_to->id,
                ];
                if($routeCar = DeliveryProposalManager::createRouteCar($data)) {
                    $addUe = false;

                    foreach($dpIds as $dpID){
                        $ueData = [];
                        $dpManager = new DeliveryProposalManager(['id'=>$dpID]);
                        if(!$addUe && $model->ue_price_cache){
                            $ueData = [
                                'name' => $model->ue_name,
                                'price_cache' => $model->ue_price_cache,
                                'cash_no' => $model->ue_cash_no,
                                'who_pays' => $model->ue_who_pays,
                                'comment' => $model->ue_comment,
                            ];
                            $addUe = true;
                        }
                        $dpManager->addCarToFirstRoute($routeCar->id, $ueData);
                        $dpManager->onChangeRouteCar();
                    }

                    DeliveryProposalManager::recalculateCarProposals($routeCar->id);
                }
                $message = 'Данные успешно обновлены';

            } else {
                $error = $model->getErrors('agent_id');
                $message = 'error';
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'message' => $message,
                'error' => $error,
            ];
        }

        return $this->renderAjax('_add-car-popup', [
            'model' => $model,
        ]);
    }

    /*
    * Popup form for add car.
    *
    * */
    public function actionAddSeparateFirstRouteCarPopup()
    {
        $model = new CarModelPopup();

        if ( $model->load(Yii::$app->request->post())) {

            $dpIds = Yii::$app->request->post('ids');
            $model->ids = $dpIds;
            $error = '';
            $message = '';

            if($model->validate()) {
                if(!empty($dpIds)) {
                    $dpIds = explode(',',$dpIds);
                }
//                $addUe = false;
                foreach($dpIds as $dpID) {
                    $firstDp = TlDeliveryProposal::findOne($dpID);
                    if($firstDpRoutes =  $firstDp->proposalRoutes){
                        $firstDpRoute = $firstDpRoutes[0];
                        $dp_from = $firstDpRoute->getRouteFrom()->one();
                        $dp_to = $firstDpRoute->getRouteTo()->one();
                    } else {
                        $dp_from = $firstDp->getRouteFrom()->one();
                        $dp_to = $firstDp->getRouteTo()->one();
                    }

                    $data = [
                        'agent_id' => $model->agent_id,
                        'car_id' => $model->car_id,
                        'driver_name' => $model->driver_name,
                        'driver_phone' => $model->driver_phone,
                        'driver_auto_number' => $model->driver_auto_number,
                        'price_invoice' => $model->car_price_invoice,
                        'price_invoice_with_vat' => $model->car_price_invoice_with_vat,
                        'route_from' => $dp_from->id,
                        'route_to' => $dp_to->id,
                    ];

                    $firstDp->car_id = $model->car_id;
                    $firstDp->agent_id = $model->agent_id;
                    $firstDp->driver_name = $model->driver_name;
                    $firstDp->driver_phone = $model->driver_phone;
                    $firstDp->driver_auto_number = $model->driver_auto_number;
                    $firstDp->save(false);

                    $ueData = [];
                    $routeCar = DeliveryProposalManager::createRouteCar($data);
                    $dpManager = new DeliveryProposalManager(['id'=>$dpID]);

//                    if(!$addUe && $model->ue_price_cache){
//                        $ueData = [
//                            'name' => $model->ue_name,
//                            'price_cache' => $model->ue_price_cache,
//                            'cash_no' => $model->ue_cash_no,
//                            'who_pays' => $model->ue_who_pays,
//                            'comment' => $model->ue_comment,
//                        ];
//                        $addUe = true;
//                    }

                    $dpManager->addCarToFirstRoute($routeCar->id, $ueData);
                    $dpManager->onChangeRouteCar();

                    DeliveryProposalManager::recalculateCarProposals($routeCar->id);
                }
//                }
                $message = 'Данные успешно обновлены';

            } else {
                $error = $model->getErrors('agent_id');
                $message = 'error';
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'message' => $message,
                'error' => $error,
            ];
        }

        return $this->renderAjax('_add-car-separate-popup', [
            'model' => $model,
        ]);
    }

//    /*
//     * Popup form for add route
//     *
//     * */
//    public function actionAddRoutePopup()
//    {
//        $errors = [];
//        $messages = [];
//        $data = '';
//
//        Yii::$app->response->format = 'json';
//
//        $dpIds = Yii::$app->request->post('ids',[]);
//
//        if(!empty($dpIds)) {
//            $dpIds = explode(',',$dpIds);
//        }
//
//        $model = new TlDeliveryRoutes();
//
//        if ( $model->load(Yii::$app->request->post()) && $model->validate()) {
//
//            $deliveryProposalModel = TlDeliveryProposal::find()->where(['id'=>$dpIds])->all();
//
//            foreach($deliveryProposalModel as $dpItem) {
//
//                // добавляем маршрут
//                $dpRoute = new TlDeliveryRoutes();
//                $dpRoute->route_from = $model->route_from;
//                $dpRoute->route_to = $model->route_to;
//                $dpRoute->client_id = $dpItem['client_id'];
//                $dpRoute->tl_delivery_proposal_id = $dpItem['id'];
//                $dpRoute->comment = $model->comment;
//                $dpRoute->shipped_datetime = $dpItem->shipped_datetime;
//                $dpRoute->save(false);
//
//                // Если у заявки первый маршрут то добавляем машину автоматом из заявки
//                if( $dpItem->getProposalRoutes()->count() == 1 ) {
//
//                    $dpRouteCar = new TlDeliveryProposalRouteCars();
//
//                    $dpRouteCar->agent_id = $dpItem->agent_id;
//                    $dpRouteCar->car_id = $dpItem->car_id;
//                    $dpRouteCar->driver_name = $dpItem->driver_name;
//                    $dpRouteCar->driver_phone = $dpItem->driver_phone;
//                    $dpRouteCar->driver_auto_number = $dpItem->driver_auto_number;
//                    $dpRouteCar->delivery_date = $dpRoute->delivery_date;
//                    $dpRouteCar->shipped_datetime = $dpRoute->shipped_datetime;
//                    $dpRouteCar->accepted_datetime = $dpRoute->accepted_datetime;
//                    $dpRouteCar->price_invoice = $dpItem->car_price_invoice;
//                    $dpRouteCar->price_invoice_with_vat = $dpItem->car_price_invoice_with_vat;
//
//                    $dp_from = $model->getRouteFrom()->one();
//                    $dp_to = $model->getRouteTo()->one();
//                    $dpRouteCar->route_city_from  = $dp_from->city->id;
//                    $dpRouteCar->route_city_to  = $dp_to->city->id;
//                    $dpRouteCar->cash_no = TlDeliveryProposal::METHOD_CHAR;
//                    $dpRouteCar->save();
//
//
//                    $modelDpRouteCar = new TlDeliveryProposalRouteTransport();
//
//                    $modelDpRouteCar->mc = $dpItem->mc;
//                    $modelDpRouteCar->mc_actual = $dpItem->mc_actual;
//                    $modelDpRouteCar->kg = $dpItem->kg;
//                    $modelDpRouteCar->kg_actual = $dpItem->kg_actual;
//                    $modelDpRouteCar->number_places = $dpItem->number_places;
//                    $modelDpRouteCar->number_places_actual = $dpItem->number_places_actual;
//                    $modelDpRouteCar->tl_delivery_proposal_route_id = $dpRoute->id;
//                    $modelDpRouteCar->tl_delivery_proposal_route_cars_id = $dpRouteCar->id;
//                    $modelDpRouteCar->tl_delivery_proposal_id = $dpItem->id;
//                    $modelDpRouteCar->save();
//
//
//                    //TLManager::recalculateDpAndDpr($dpItem->id,$dpRoute->id);
//                    $dpManager = new DeliveryProposalManager(['id'=>$dpItem->id]);
//                    $dpManager->onUpdateProposal();
//
//                }
//            }
//
//        } else {
//
//            if(empty($dpIds)) {
//                $errors [] = Yii::t('titles','Please select one or more delivery proposals');
//            }
//
//            $deliveryProposalModel = TlDeliveryProposal::find()->where(['id'=>$dpIds])->asArray()->all();
//
//            //1 - У заявок должен быть один клиент
//            $client = [];
//            $client_id = null;
//            $routeFromId = null;
//            foreach($deliveryProposalModel as $dp) {
//                $client[$dp['client_id']] = $dp['client_id'];
//                $client_id = $dp['client_id'];
//                $routeFromId = $dp['route_from'];
//            }
//
//
//            if(count($client) > 1) {
//                $errors [] = Yii::t('titles','Вы выбрали заявки от разных клиентов');
//            }
//
//            $model->route_from = $routeFromId;
//
//            if(empty($errors)) {
//                $data = $this->renderAjax('_add-route-popup', [
//                    'model' => $model,
//                    'client_id' => $client_id,
//                ]);
//            }
//        }
//
//        return  [
//                'message' => $messages,
//                'errors' => $errors,
//                'data' => $data,
//            ];
//    }


    /*
    * Popup form for mass update.
    *
    * */
    public function actionMassUpdatePopup()
    {
        $model = new TlDeliveryProposal();

        $model->setScenario('mass-update');

        if ( $model->load(Yii::$app->request->post()) ) {

            $dpIds = Yii::$app->request->post('ids');
            if(!empty($dpIds)) {
                $dpIds = explode(',',$dpIds);
            } else {
                $searchModel = new TlDeliveryProposalSearchExport();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                $dataModels = $dataProvider->query->asArray()->all();
                $dpIds = ArrayHelper::getColumn($dataModels, 'id');
            }

            $errors = [];

            if(is_array($dpIds)) {
                TlDeliveryProposal::updateAll(['status_invoice' => $model->status_invoice], ['id' => $dpIds]);
//                if(!empty($model->cash_no)) {
//                    $update['cash_no'] = $model->cash_no;
//                }
//
//                if(!empty($model->status_invoice)) {
//                    $update['status_invoice'] = $model->status_invoice;
//                }
//
//                if(!empty($model->status)) {
//                    $update['status'] = $model->status;
//                }
//
////                VarDumper::dump($update,10,true);
////                die;
//
//                if(!empty($update)) {
//                    foreach($dpIds as $dpID) {
//
////                        $pd = new TlDeliveryProposal();
//                        $m = TlDeliveryProposal::findOne($dpID);
//                        $m->setScenario('mass-update');
//                        $m->setAttributes($update);
////                        $m->validate();
//
////                        VarDumper::dump($m->delivery_date,10,true);
////                        echo "<br />";
////                        VarDumper::dump($m->getErrors(),10,true);
////                        echo "<br />";
//
//                        if(!$m->validate()) {
//                           $errors[] = [
//                                        'id' => $m->id,
//                                        'errors' => $m->getErrors()
//                           ];
//                        } else {
//                            $m->save();
//                        }
//                    }
////                    $pd->updateAll($update, ['id' => $dpIds]);
////                    $pd->updateAll($update, ['id' => $dpIds]);
////                    VarDumper::dump($m->getErrors(),10,true);
////                    die;
//                }

            }
//
//            Yii::$app->response->format = 'html';
            Yii::$app->response->format = 'json';
//            return  '';
            return [
                'message' => 'Success',
                'errors' => $errors,
            ];
        } else {
//            VarDumper::dump($model->getErrors(),10,true);
//            die;
        }

        return $this->renderAjax('_mass-update-popup', [
            'model' => $model,
        ]);
    }

    /*
 * Form. Add new store route
 *
 * */
    public function actionMakeReadyInvoice()
    {
        Yii::$app->response->format = 'json';
        $title = '';
        $success = '';
        $errors = '';
        $id = Yii::$app->request->post('id');

        $model = $this->findModel($id);

        if($model->ready_to_invoicing == TlDeliveryProposal::READY_TO_INVOICING_NO){
            $model->ready_to_invoicing = TlDeliveryProposal::READY_TO_INVOICING_YES;
            $model->save(false);
        } else {
            $errors = 'Эта запись уже помечена';
        }

        $title = $model->getReadyToInvoicingValue();

        return [
            'success' => ($errors ? 0 : 1),
            'errors' => $errors,
            'title' => $title,
        ];

    }

    /*
     *
     * */
    public function actionFormProposalRoute()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $message = '';
        $html = '';

        if($dp = TlDeliveryProposal::findOne(Yii::$app->request->post('id'))){
            $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
            if($dpManager->generateDefaultRoutes()) {
                $dataProviderProposalRoutes = new ActiveDataProvider([
                    'query' => $dp->getProposalRoutes(),
                ]);

                $dpManager->onChangeRouteCar();

                $message = 'success';
                $html = $this->renderPartial('_delivery_routes_grid', ['dataProviderProposalRoutes' => $dataProviderProposalRoutes]);

            } else {
                $message = Yii::t('transportLogistics/titles', 'Для этой заявки маршруты по умолчанию не найдены');
            }
        }

        return [
            'message' => $message,
            'data' => $html,
        ];
    }

    /*
 *
 * */
    public function actionSaveSelectedDefaultRoute()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $message = '';
        $html = '';
        $model = new SelectSubRouteDefault();
        $model->load(Yii::$app->request->post());
        $dpID = $model->delivery_proposal_id;
        if($dp = TlDeliveryProposal::findOne($dpID)){
            $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
            if($dpManager->generateDefaultRoutes(true,$model->sub_default_route_id)) {
                $dataProviderProposalRoutes = new ActiveDataProvider([
                    'query' => $dp->getProposalRoutes(),
                ]);

                $dpManager->onChangeRouteCar();

                $message = 'success';
                $html = $this->renderPartial('_delivery_routes_grid', ['dataProviderProposalRoutes' => $dataProviderProposalRoutes]);

            } else {
                $message = Yii::t('transportLogistics/titles', 'Для этой заявки маршруты по умолчанию не найдены');
            }
        }

        return [
            'message' => $message,
            'data' => $html,
        ];
    }

    /*
     *
     * */
    public function actionCheckDefaultRoute()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = 'one';
        $message = '';
        $html = '';

        if($dp = TlDeliveryProposal::findOne(Yii::$app->request->get('id'))){
            $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
            $defaultRoutes = $dpManager->getCountDefaultRoutes();
            if(count($defaultRoutes) > 1) {
                $model = new SelectSubRouteDefault();
                $storeArray = TLHelper::getStockPointArray(null,false,false,'small');
                $drArray = ArrayHelper::map($defaultRoutes,'id',function($data) use ($storeArray) {
                    $from = isset($storeArray[$data->from_point_id]) ? $storeArray[$data->from_point_id] : '-';
                    $to = isset($storeArray[$data->to_point_id]) ? $storeArray[$data->to_point_id] : '-';
                    return $from.'  -  '.$to;
                });
                $model->delivery_proposal_id = $dp->id;
                $html = $this->renderAjax('_select-sub-default-route',['model'=>$model,'drArray'=>$drArray]);
                $status = 'more';
            }
        }

        return [
            'status'=>$status,
            'html'=>$html,
            'message'=>$message,
        ];
    }

    /*
     *
     * */
    public function actionGetGridDefaultSubRouts()
    {
        $id = Yii::$app->request->post('id');
        if($id) {
            if($model = TlDeliveryProposalDefaultRoute::findOne($id)) {
                $storeArray = TLHelper::getStockPointArray();
                return $this->renderPartial('_grid-default-sub-routs', ['model' => $model, 'storeArray' => $storeArray]);
            }
        }

        return '';
    }

    /*
    * Print box label
    *
    * */
    public function actionPrintBoxLabel()
    {
        $id = Yii::$app->request->get('id');
        //        $type = Yii::$app->request->get('type');

        $model = $this->findModel($id);
        //
        $codeBookModel = Codebook::findOne(['base_type'=>Codebook::BASE_TYPE_BOX]); // Короб

        //        VarDumper::dump($codeBookModel,10,true);
        //        die('----STOP----');

        //        $view = ($type == 1 ? 'print-box-label-a4-pdf' : 'print-box-label-self-adhesive-pdf' );

        return $this->render('print/box-label-self-adhesive-pdf',['model'=>$model,'codeBookModel'=>$codeBookModel]);
    }
	

    public function actionPrintOldTtn()
    { // tms/default/print-old-ttn?id=39773
//        $id = Yii::$app->request->get('id');
//        die('other/one/print-old-ttn?id=39773');
        $modelList = [];
        $formTTN = new OldTtnForm();
        if ( $formTTN->load(Yii::$app->request->post()) && $formTTN->validate()) {

            $iDs[] = $formTTN->ttn;
			            //VarDumper::dump($iDs,10,true);
						//die;
            foreach ($iDs as $id) {
                $model = TlDeliveryProposal::findOne($id);

                $managersNamesTo = $this->makeSippingContactOldTTn($model->routeTo, $model->client_id);

                $modelList[] = [
                    'model'=> $model,
                    'managersNamesTo'=> $managersNamesTo,
                ];
            }
            return $this->render('old-ttn/print-ttn-pdf', ['modelList'=>$modelList]);
        }
        return $this->render('old-ttn/_form', ['formTTN'=>$formTTN]);
    }

    private function makeSippingContactOldTTn($routeTo, $clientId)
    {
        $managersNamesTo = 'Контакты получателей:' . "<br />";
        if (empty($routeTo) || empty($clientId)) {
            return '';
        }

        // находим всех директоров магазина и отправляем им имейлы
        $clientEmployees = ClientEmployees::find()
            ->andWhere([
                'status' => ClientEmployees::STATUS_ACTIVE,
                'client_id' => $clientId,
                'store_id' => $routeTo->id,
                'manager_type' => [
                    ClientEmployees::TYPE_BASE_ACCOUNT,
                    ClientEmployees::TYPE_DIRECTOR,
                    ClientEmployees::TYPE_DIRECTOR_INTERN,
                    ClientEmployees::TYPE_MANAGER,
                ]
            ])
            ->all();

        if (empty($clientEmployees)) {
            return '';
        }

        $managersNamesTo .= '<table width="100%" border="0">';
        $managersNamesTo .= "<tr>";
        foreach ($clientEmployees as $item) {
            $managersNamesTo .= "<td width=\"23%\">" . $item->first_name . ' ' . $item->last_name . ' / ' . $item->phone_mobile . ' ' . $item->phone . "</td>";
        }
        $managersNamesTo .= "</tr>";
        $managersNamesTo .= '</table>';

        return $managersNamesTo;
    }
}