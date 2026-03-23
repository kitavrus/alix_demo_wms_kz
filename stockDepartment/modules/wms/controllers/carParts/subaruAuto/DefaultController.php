<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:03
 */

namespace app\modules\wms\controllers\carParts\subaruAuto;

use common\clientObject\subaruAuto\outbound\forms\OutboundOrderUploadForm;
use common\clientObject\subaruAuto\inbound\forms\InboundOrderUploadForm;
use common\clientObject\subaruAuto\outbound\service\OutboundOrderUploadService;
use common\clientObject\subaruAuto\inbound\service\InboundOrderUploadService;
use common\modules\store\models\Store;
use common\modules\store\service\Service as StoreService;
use Yii;

class DefaultController extends  \stockDepartment\components\Controller
{
    //
    public function actionStart()
    {
        return $this->render('start', [
        ]);
    }
    //
    public function actionIndex()
    {
        return $this->redirect('start');
    }
    /*
    * Upload orders for inbound
    * */
    public function actionUploadOrderInbound()
    { // wms/carParts/hyundaiTruck/default/upload-order-inbound
        $inboundOrderUploadForm = new InboundOrderUploadForm();
        $inboundOrderUploadForm->setScenario('onCreate');
        if ($inboundOrderUploadForm->load(Yii::$app->request->post()) && $inboundOrderUploadForm->saveFileAndPreparedData() && $inboundOrderUploadForm->validate()) {
            $inboundOrderUploadService = new InboundOrderUploadService();
            $inboundOrderUploadService->create($inboundOrderUploadForm->getDTO());
            Yii::$app->getSession()->setFlash('success', "Накладная успешно загружена");
            return $this->refresh();
        }

        return $this->render('upload-order-inbound', [
            'inboundOrderUploadForm'=>$inboundOrderUploadForm
        ]);
    }

    /*
    * Upload orders for outbound
    * */
    public function actionUploadOrderOutbound()
    { // wms/carParts/hyundaiTruck/default/upload-order-inbound
        $outboundOrderUploadForm = new OutboundOrderUploadForm();
        $outboundOrderUploadForm->setScenario('onCreate');
        $store = new StoreService();
        $stores = $store->getStoreByClient($outboundOrderUploadForm->getClientID());

        if ($outboundOrderUploadForm->load(Yii::$app->request->post()) && $outboundOrderUploadForm->saveFileAndPreparedData() && $outboundOrderUploadForm->validate()) {
            $outboundOrderUploadService = new OutboundOrderUploadService();
            $outboundOrderUploadService->create($outboundOrderUploadForm->getDTO());
            Yii::$app->getSession()->setFlash('success', "Накладная успешно загружена");
            return $this->refresh();
        }

        return $this->render('upload-order-outbound', [
            'outboundOrderUploadForm'=>$outboundOrderUploadForm,
            'stores'=>$stores,
        ]);
    }
}