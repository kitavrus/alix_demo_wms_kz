<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:03
 */

namespace app\modules\ecommerce\controllers\defacto;

use common\clientObject\hyundaiAuto\outbound\forms\OutboundOrderUploadForm;
use common\clientObject\hyundaiAuto\inbound\forms\InboundOrderUploadForm;
use common\clientObject\hyundaiAuto\outbound\service\OutboundOrderUploadService;
use common\clientObject\hyundaiAuto\inbound\service\InboundOrderUploadService;
use common\ecommerce\defacto\inbound\forms\CreateInboundOrderForm;
use common\ecommerce\defacto\inbound\service\CreateInboundService;
use common\ecommerce\defacto\inbound\service\ECommerceAPI;
use Yii;
use common\modules\store\models\Store;
use common\modules\store\service\Service as StoreService;

class DefaultController extends  \stockDepartment\components\Controller
{
    //
    public function actionStart()
    {
//        $api = new ECommerceAPI();
//        $api->GetInBoundData();
//        $ShortCode = '';// 'J9495AZAA';
//        $SkuId = '';//'224051206';
//        $LotOrSingleBarcode = '2300005992309';
//        $api->GetMasterData($ShortCode,$SkuId,$LotOrSingleBarcode);
//
//        $ShortCode = ['J9495AZAA'];// 'J9495AZAA';
//        $LotOrSingleSkuIds = '';//['224051206'];//['224051206'];
//        $LotOrSingleBarcodes = ''; // ['2300005992309'];
//        $api->GetSkuInfo($ShortCode,$LotOrSingleSkuIds,$LotOrSingleBarcodes);

        return $this->render('start', []);
    }
    //
    public function actionIndex()
    {
        return $this->redirect('start');
    }
    /*
    * Upload orders for inbound
    * */
    public function actionCreate()
    { // wms/carParts/hyundaiAuto/default/upload-order-inbound
//        $inboundOrderUploadForm = new InboundOrderUploadForm();
        $inboundOrderUploadForm = new CreateInboundOrderForm();
        $inboundOrderUploadForm->setScenario('onCreate');

        if ($inboundOrderUploadForm->load(Yii::$app->request->post()) && $inboundOrderUploadForm->validate()) {
            $inboundOrderUploadService = new CreateInboundService();
            $inboundOrderUploadService->create($inboundOrderUploadForm->getDTO());
            Yii::$app->getSession()->setFlash('success', "Накладная успешно загружена");
            return $this->refresh();
        }

        return $this->render('create-inbound-order', [
            'inboundOrderUploadForm'=>$inboundOrderUploadForm
        ]);
    }

    /*
    * Upload orders for outbound
    * */
//    public function actionUploadOrderOutbound()
//    { // wms/carParts/hyundaiAuto/default/upload-order-inbound
//        $outboundOrderUploadForm = new OutboundOrderUploadForm();
//        $outboundOrderUploadForm->setScenario('onCreate');
//        $store = new StoreService();
//        $stores = $store->getStoreByClient($outboundOrderUploadForm->getClientID());
//
//        if ($outboundOrderUploadForm->load(Yii::$app->request->post()) && $outboundOrderUploadForm->saveFileAndPreparedData() && $outboundOrderUploadForm->validate()) {
//            $outboundOrderUploadService = new OutboundOrderUploadService();
//            $outboundOrderUploadService->create($outboundOrderUploadForm->getDTO());
//            Yii::$app->getSession()->setFlash('success', "Накладная успешно загружена");
//            return $this->refresh();
//        }
//
//        return $this->render('upload-order-outbound', [
//            'outboundOrderUploadForm'=>$outboundOrderUploadForm,
//            'stores'=>$stores,
//        ]);
//    }
}