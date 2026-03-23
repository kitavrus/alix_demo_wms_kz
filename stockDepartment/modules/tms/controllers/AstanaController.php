<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 19.10.2018
 * Time: 12:49
 */

namespace app\modules\tms\controllers;

use common\modules\city\models\City;
use common\modules\city\models\Country;
use common\modules\city\models\Region;
use common\modules\city\models\RouteDirections;
use common\modules\city\RouteDirection\service\Service;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use stockDepartment\components\Controller;
use stockDepartment\modules\tms\models\TlDeliveryProposalSearch;
use common\modules\client\models\Client;
use stockDepartment\modules\report\service\reportToDay\Service as ReportToDay;
use common\modules\store\repository\Repository as StoreRepository;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class AstanaController extends Controller
{
    /**
     * Lists all TlDeliveryProposal models.
     * @return mixed
     */
    /* TODO TASKS
     * 1 + Add client tupperware
     * 2 + last day delivery
     * */
    public function actionIndex()
    {
        $routeDirectionService = new Service();

        $clientList = [
            Client::CLIENT_DEFACTO,
            Client::CLIENT_TUPPERWARE
        ];
        $fromToCondition = array_merge($routeDirectionService->getNorthStore($clientList),$routeDirectionService->getEasternStore($clientList));

        $searchModel = new TlDeliveryProposalSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['not', ['status_invoice'=>TlDeliveryProposal::INVOICE_PAID]]);
        $dataProvider->query->andWhere(['status'=>TlDeliveryProposal::STATUS_ON_ROUTE]);
        $dataProvider->query->andFilterWhere(['OR',
            ['route_from'=>$fromToCondition],
            ['route_to'=>$fromToCondition],
        ]);

        $clientArray = Client::getActiveTMSItems();
        $storeArray = TLHelper::getStockPointArray();
        $cityArray = City::getArrayData();
        $regionArray = Region::getArrayData();
        $countryArray = Country::getArrayData();
        $routeDirectionArray = RouteDirections::getArrayData();


        $clientList = array_filter($clientArray,function($item) use ($clientList) { return in_array($item,$clientList); },ARRAY_FILTER_USE_KEY);



        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientArray' => $clientArray,
            'storeArray' => $storeArray,
            'cityArray' => $cityArray,
            'regionArray' => $regionArray,
            'countryArray' => $countryArray,
            'routeDirectionArray' => $routeDirectionArray,
            'clientList' => $clientList,
        ]);
    }

    /*
     *
     * */
    public function actionLastDayDelivery() {

        $routeDirectionService = new Service();
        $clientList = [
            Client::CLIENT_DEFACTO,
            Client::CLIENT_TUPPERWARE
        ];
        $fromToCondition = array_merge($routeDirectionService->getNorthStore($clientList),$routeDirectionService->getEasternStore($clientList));


        $service = new ReportToDay($clientList);
        $moreDeliveryTime = $service->getMoreDeliveryTimeBetweenStore($fromToCondition,$fromToCondition);


//        VarDumper::dump($moreDeliveryTime,10,true);
//        die;

        return $this->render('last-day-delivery',[
            'moreDeliveryTime'=>$moreDeliveryTime,
        ]);
    }
}