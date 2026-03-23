<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:03
 */

namespace app\modules\warehouseDistribution\controllers\carParts;

use common\clientObject\main\outbound\forms\OutboundOrderUploadForm;
use common\clientObject\main\inbound\forms\InboundOrderUploadForm;
use common\clientObject\main\outbound\service\OutboundOrderUploadService;
use common\clientObject\main\inbound\service\InboundOrderUploadService;
use clientDepartment\modules\client\components\ClientManager;
use common\clientObject\main\service\MailService;
use common\modules\store\models\Store;
use common\modules\store\service\Service as StoreService;
use Yii;

class DefaultController extends  \clientDepartment\components\Controller
{
    /*
    * Upload orders for inbound
    * */
    public function actionUploadOrderInbound()
    { ///carParts/main/default/upload-order-inbound
        $client = ClientManager::getClientEmployeeByAuthUser();
        $params = new \stdClass();
        $params->clientId = $client->client_id;
        $inboundOrderUploadForm = new InboundOrderUploadForm([],$params);
        $inboundOrderUploadForm->setScenario('onCreate');

        if ($inboundOrderUploadForm->load(Yii::$app->request->post()) && $inboundOrderUploadForm->saveFileAndPreparedData() && $inboundOrderUploadForm->validate()) {
            $inboundOrderUploadService = new InboundOrderUploadService($params);
            $inboundOrderUploadService->create($inboundOrderUploadForm->getDTO());

            $mailService = new MailService();
            $mailService->sendMailIfClientUploadNewOrder($mailService->makeDTOForInbound($inboundOrderUploadService->getInboundOrderID()));

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
    { // wms/carParts/main/default/upload-order-inbound
        $client = ClientManager::getClientEmployeeByAuthUser();
        $params = new \stdClass();
        $params->clientId = $client->client_id;;

        $outboundOrderUploadForm = new OutboundOrderUploadForm([],$params);
        $outboundOrderUploadForm->setScenario('onCreate');

        $store = new StoreService();
        $stores = $store->getStoreByClient($outboundOrderUploadForm->getClientID());

        if ($outboundOrderUploadForm->load(Yii::$app->request->post()) && $outboundOrderUploadForm->saveFileAndPreparedData() && $outboundOrderUploadForm->validate()) {
            $outboundOrderUploadService = new OutboundOrderUploadService($params);
            $outboundOrderUploadService->create($outboundOrderUploadForm->getDTO());

            $mailService = new MailService();
            $mailService->sendMailIfClientUploadNewOrder($mailService->makeDTOForOutbound($outboundOrderUploadService->getOrderId()));

            Yii::$app->getSession()->setFlash('success', "Накладная успешно загружена");
            return $this->refresh();
        }

        return $this->render('upload-order-outbound', [
            'outboundOrderUploadForm'=>$outboundOrderUploadForm,
            'stores'=>$stores,
        ]);
    }
}