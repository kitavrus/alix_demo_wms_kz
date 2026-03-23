<?php

namespace app\modules\tms\controllers;

use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
use common\components\BarcodeManager;
use common\modules\outbound\service\OutboundBoxService;
use stockDepartment\modules\crossDock\models\CrossDockSearch;
use common\modules\client\models\Client;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\components\TLHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use Yii;
use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use stockDepartment\components\Controller;


class BoxOutController extends Controller
{
    private $url = 'http://195.46.145.251/DFStore.ProxyServices.T1/ExternalWMS/ExternalWMSProxy.asmx';

    public function actionOutboundList() {
        // Добавить поля в  кросс доки и в аутбаунт
        // -- статс и json Того что отправили
        // показываем спимок отгрузок и крос-док в которых статус не отправлено
        // в списке номер заказ, магазин кол-во мест, тип (крос док или сборка), кнопка (кнопка при клике на которую мы отправляем данные)
        // после клика кнопка исчезает и строка в списке сереет

        return $this->render('outbound-list', [
            'dataProviderOutbound' => $this->outboundOrderGridSearch(),
            'dataProviderCrossDock' => $this->crossDockGridSearch(),
            'clientStoreArray' => TLHelper::getStoreArrayByClientID(),
        ]);
    }

    public function actionCrossDockList() {
        return $this->render('cross-dock-list', [
            'dataProviderOutbound' => $this->outboundOrderGridSearch(),
            'dataProviderCrossDock' => $this->crossDockGridSearch(),
            'clientStoreArray' => TLHelper::getStoreArrayByClientID(),
        ]);
    }

    public function actionOutboundSendByApi($id)
    {
        // /other/one/api-x-outbound
        $items = Stock::find()
            ->select(' box_barcode, box_size_barcode')
            ->andWhere([
                'outbound_order_id' => $id,
//                'status' => [
//                    Stock::STATUS_OUTBOUND_SCANNED,
//                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
//                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
//                ],
            ])
            ->groupBy('box_barcode')
            ->orderBy('box_barcode')
            ->asArray()
            ->all();

        $obs = new OutboundBoxService();
        foreach($items as &$item) {
            $item['client_box'] =  $obs->getClientBoxByBarcode($item['box_barcode']);
            $item['date_time'] = $this->makeDateTimeNow();
        }

        VarDumper::dump($items,10,true);
        VarDumper::dump($this->sendByAPI($items),10,true);
        die;

        return 'API X';
    }

    public function actionCrossDockSendByApi($id)
    {
        // /other/one/api-x-outbound
        $items = CrossDockItems::find()
            ->select('box_barcode, box_m3')
            ->andWhere(['cross_dock_id'=>$id])
            ->groupBy('box_barcode')
            ->asArray()
            ->all();



        foreach($items as &$item) {
            VarDumper::dump($item,10,true);
//            die;

            $item['client_box'] = $item['box_barcode'];
            $item['box_size_barcode'] = BarcodeManager::getIntegerM3($item['box_m3']);
            $item['date_time'] = $this->makeDateTimeNow();

        }

        VarDumper::dump($items,10,true);
        VarDumper::dump($this->sendByAPI($items),10,true);
        die;

        return 'API X';
    }

    private function outboundOrderGridSearch() {

        $searchModel = new OutboundOrderGridSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;
        $dataProvider->query->andWhere(['client_id'=>Client::CLIENT_DEFACTO]);

        return $dataProvider;
    }

    private function crossDockGridSearch() {
        $searchModel = new CrossDockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;
        $dataProvider->query->andWhere(['client_id'=>Client::CLIENT_DEFACTO]);

        return $dataProvider;
    }

    private function makeDateTimeNow() {
        return (new \DateTimeImmutable('now',new \DateTimeZone('Asia/Almaty')))->format('Y-m-d H:i:s P');
    }

    private function sendByAPI($aData) {
        $api = new DeFactoSoapAPIV2();
        $params['request'] = $this->prepareDataForSendByAPI($aData);
//        $result = $api->sendRequest('SendCargoDelivery', $params);

//        $params['request'] = [
//            'BusinessUnitId'=>$api::BUSINESS_UNIT_ID,
//            'PageSize'=>'0',
//            'PageIndex'=>'0',
//            'CountAllItems'=>false,
//        ];
//
//        $result = $api->sendRequest('GetWarehouseAppointments',$params);



//        $result = $api->GetWarehouseAppointments();
//        VarDumper::dump($params,10,true);
//        echo "<br />";
//        VarDumper::dump($result,10,true);
//        die;
//        return $params;
        return $params;
    }

    private function prepareDataForSendByAPI($aData) {

        $request = [];
        foreach($aData as $item) {
            $request = [
                'BusinessUnitId' => '1029',
                'LCBarcode' => ArrayHelper::getValue($item,'client_box'),
                'CargoShipmentNo' => '',
                'VolumetricWeight' => ArrayHelper::getValue($item,'box_size_barcode'),
                'DeliveryDate' => ArrayHelper::getValue($item,'date_time'),
            ];
        }

        return $request;
    }
}