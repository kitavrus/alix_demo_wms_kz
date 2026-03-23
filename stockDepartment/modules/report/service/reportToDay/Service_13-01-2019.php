<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.09.2017
 * Time: 9:53
 */

namespace stockDepartment\modules\report\service\reportToDay;


use common\modules\stock\models\ConsignmentUniversal;
use stockDepartment\modules\report\repository\reportToDay\repository;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use common\modules\client\models\Client;

class Service
{
    private $repository;
    private $currentDateTimeBegin;
    private $currentDateTimeEnd;

    /*
    * @param int $clientID
    */
    public function __construct($clientID = Client::CLIENT_DEFACTO)
    {
        $this->repository = new repository($clientID);
        $this->currentDateTimeBegin = date('Y-m-d', time()) . ' 00:00:00';
        $this->currentDateTimeEnd = date('Y-m-d', time()) . ' 23:59:59';
    }
    // Количество в сборках отсканированных лотов на сегодня
    public function qtyScannedOutboundLotToDay()
    {
        return $this->repository->qtyScannedOutboundLotToDay( $this->currentDateTimeBegin, $this->currentDateTimeEnd);
    }
    // Количество отгруженных заказов на сегодня
    public function qtyLeftOutboundToDay()
    {
        return $this->repository->qtyLeftOutboundAndCrossDockToDay($this->currentDateTimeBegin, $this->currentDateTimeEnd);
    }

    // Количество отгруженных заказов на сегодня
    public function getMoreDeliveryTime()
    {
        $tStart = time() - 30 * 24 * 3600;
        $tEnd = time();
        $fromDateTime = date('Y-m-d', $tStart) . ' 00:00:00';
        $toDateTime = date('Y-m-d', $tEnd) . ' 23:59:59';
        return $this->repository->getMoreDeliveryTime($fromDateTime, $toDateTime);
    }

    public function getMoreDeliveryTimeBetweenStore($fromStore,$toStore)
    {
        $tStart = time() - 30 * 24 * 3600;
        $tEnd = time();
        $fromDateTime = date('Y-m-d', $tStart) . ' 00:00:00';
        $toDateTime = date('Y-m-d', $tEnd) . ' 23:59:59';
        return $this->repository->getMoreDeliveryTimeBetweenStore($fromStore,$toStore,$fromDateTime, $toDateTime);
    }

    // Количество заказов в пути
    public function qtyOnRoadOutboundToDay()
    {
        return $this->repository->qtyOnRoadOutboundToDay();
    }

    // Количество не собраных лотов
    public function sumOutboundOrderInProcess()
    {
        return $this->repository->sumOutboundOrderInProcess();
    }

    // Количество не собраных лотов по направления
    public function sumOutboundOrderInProcessByRouteDirection()
    {
        $routeDirectionService = new \common\modules\city\RouteDirection\service\Service();
        $routeDirectionsWithStores = $routeDirectionService->getDirectionStoresIDsGroupType();
        $orderInProcessByStore = [];
        foreach($routeDirectionsWithStores as $directionName=>$directionWithStoresIDs) {
            $orderInProcessByStore [$directionName] = $this->repository->sumOutboundOrderInProcessByStoreIds($directionWithStoresIDs);
        }
        return $orderInProcessByStore;
    }

    // Количество в поступлений отсканированных лотов на сегодня
    public function qtyScannedInboundLotToDay()
    {
        return $this->repository->qtyScannedInboundLotToDay($this->currentDateTimeBegin, $this->currentDateTimeEnd);
    }


    // Готовы для отгрузки
    public function readyForDelivery()
    {
        return $this->repository->readyForDelivery();
    }
    // Готовы для отгрузки по магазинам
//    public function readyForDeliveryByStore()
//    {
//        return $this->repository->readyForDeliveryByStore();
//    }
    // Готовы для отгрузки по направлениям
    public function readyForDeliveryByRouteDirection()
    {
        $routeDirectionService = new \common\modules\city\RouteDirection\service\Service();
        $routeDirectionsWithStores = $routeDirectionService->getDirectionStoresIDsGroupType();
        $readyForDelivery = [];
        foreach($routeDirectionsWithStores as $directionName=>$directionWithStoresIDs) {
            $readyForDelivery [$directionName] = $this->repository->readyForDeliveryByRouteDirection($directionWithStoresIDs);
        }
        return $readyForDelivery;
    }
    // Общая информация о поступлениях которые принимаются в данный момент
    public function inboundOrderInProcess()
    {
        return $this->repository->inboundOrderInProcess();
    }
    // Поступления которые принимаются в данный момент
    public function inboundInProcessByOrders()
    {
        return $this->repository->inboundInProcessByOrders();
    }
    // Количество кросс-дов принятых на сегодня
    public function getAcceptedCrossDockBoxToDay() {
        return $this->repository->getAcceptedCrossDockBoxToDay($this->currentDateTimeBegin, $this->currentDateTimeEnd);
    }

    public function getCurrentDateTime($typeReturn = 'string') {
        $begin = date('m-d', time()) . ' 00:00';
        $end = date('m-d', time()) . ' 23:59';
        if($typeReturn == 'array') {
            return [
                'currentDateTimeBegin'=>$begin,
                'currentDateTimeEnd'=>$end
            ];
        }
        return $begin.' > '.date('m-d H:i').' < '.$end;
    }

    public function inboundOrderOnRoadToKz() {
        $apiManager = new DeFactoSoapAPIV2Manager();
        $apiDataResult =  $apiManager->getAndSaveInboundOrderParty();
        $dataProvider = [];
        $errorMessage = '';

        if(!$apiDataResult['HasError']) {
            $dataProvider = $apiManager->getConsignmentUniversalActiveDataProvider(ConsignmentUniversal::ORDER_TYPE_INBOUND);
            $dataProvider->query->andWhere(['status_created_on_client'=>DeFactoSoapAPIV2::INBOUND_STATUS_NOTHING]);
        } else {
            $errorMessage = $apiDataResult['ErrorMessage'];
        }

        return [
            'dataProvider' => $dataProvider,
            'errorMessage'=>$errorMessage
        ];
    }

    public function getInProcessCrossDockBoxToDay() {
        return $this->repository->getInProcessCrossDockBoxToDay();
    }
}