<?php

namespace app\modules\wms\controllers\carParts\main;

use common\clientObject\constants\Constants;
use common\clientObject\main\forms\CanManagerForm;
use common\clientObject\main\inbound\service\InboundOrderService;
use common\clientObject\main\inbound\service\InboundServiceReport;
use common\clientObject\deliveryProposal\service\DeliveryOrderService;
use common\clientObject\main\outbound\service\OutboundService;
use common\clientObject\main\outbound\service\OutboundServiceReport;
use common\clientObject\main\stock\service\StockRemainServiceReport;
use common\modules\city\models\RouteDirections;
use common\modules\client\models\Client;
use common\modules\stock\models\Stock;
use common\modules\stock\service\Service;
use stockDepartment\components\Controller;
use Yii;

class ForManagerController extends Controller
{

    public function actionEntryForm()
    {
        $canManagerForm = new CanManagerForm();
        $canManagerForm->setScenario('onCan');
        if ($canManagerForm->load(Yii::$app->request->post()) && $canManagerForm->validate()) {
            return $this->redirect('start');
        }

        return $this->render('default/can-manager-form',['canManagerForm'=>$canManagerForm]);
    }

    public function actionStart()
    {
        return $this->render('default/start');
    }

    public function actionDeleteInbound($id)
    {
        $service = new InboundOrderService();
        $inboundInfo = $service->getOrderInfo($id);
        $service->delete($id);
        Yii::$app->session->setFlash('danger', 'Заказ <strong>'. $inboundInfo->order->order_number.'</strong> успешно удален');

        return $this->redirect('inbound');
    }

    public function actionDeleteOutbound($id)
    {
        $service = new OutboundService();
        $outboundInfo = $service->getOrderInfo($id);
        $service->delete($id);

        Yii::$app->session->setFlash('danger', 'Заказ <strong>'. $outboundInfo->order->order_number.'</strong> успешно удален');

        return $this->redirect('outbound');
    }


    public function actionInbound()
    {
        $search = new InboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());

        return $this->render('inbound/index', [
            'activeDataProvider' => $activeDataProvider,
            'searchModel' => $search->getSearch(),
            'clientsArray' => $clientsArray,
        ]);
    }


    public function actionOutbound()
    {
        $search = new OutboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());
        $storesArray = (new \common\modules\store\service\Service())->getStoreCityNameByClientWithPattern(Constants::getCarPartClientIDs());

        return $this->render('outbound/index', [
            'activeDataProvider' => $activeDataProvider,
            'searchModel' => $search->getSearch(),
            'clientsArray' => $clientsArray,
            'storesArray' => $storesArray,
        ]);
    }

    public function actionDeliveryOrder()
    {
        $search = new DeliveryOrderService();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());
        $storesArray = (new \common\modules\store\service\Service())->getStoreCityNameByClientWithPattern(Constants::getCarPartClientIDs());
        $routeDirectionArray = RouteDirections::getArrayData();
        return $this->render('delivery-order/index', [
            'activeDataProvider' => $activeDataProvider,
            'searchModel' => $search->getSearch(),
            'clientsArray' => $clientsArray,
            'storesArray' => $storesArray,
            'routeDirectionArray' => $routeDirectionArray,
        ]);
    }

    public function actionDamageStock() {

        $search = new StockRemainServiceReport();
        $activeDataProvider = $search->getSearch()->searchArray(Yii::$app->request->queryParams);
        $conditionTypeArray = $search->getSearch()->getConditionTypeArray();

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());


        return $this->render('damage-stock/list', [
            'activeDataProvider' => $activeDataProvider,
            'searchModel' => $search->getSearch(),
            'clientsArray' => $clientsArray,
            'conditionTypeArray' => $conditionTypeArray,
        ]);
    }
    public function actionChangeToUndamaged($stockId) {
        $stockService = new Service();
        $stockService->changeProductCondition($stockId,Stock::CONDITION_TYPE_UNDAMAGED);

        Yii::$app->session->setFlash('success', 'Товар успешно востановлен!');

        return $this->redirect('damage-stock');
    }
}