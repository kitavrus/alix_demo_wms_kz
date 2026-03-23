<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:03
 */

namespace app\modules\wms\controllers\carParts\main;

use  common\clientObject\deliveryProposal\forms\TTNForm;
use common\clientObject\hyundaiTruck\inbound\forms\InboundOrderUploadForm;
use common\clientObject\hyundaiTruck\inbound\service\InboundOrderUploadService;
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

    public function actionTtnForm()
    {
        $ttnForm = new TTNForm();
        $ttnForm->setScenario('onTTN');
        if ($ttnForm->load(Yii::$app->request->post()) && $ttnForm->validate()) {
            $ttnForm->saveClientTTN();
            Yii::$app->getSession()->setFlash('success', "ТТНка клиента успешно сохранена");
            return $this->refresh();
        }

        return $this->render('ttn-form',['ttnForm'=>$ttnForm]);
    }

    /*
    * Upload orders for inbound
    * */
    public function actionUploadOrderInbound()
    { // wms/carParts/hyundaiTruck/default/upload-order-inbound
        $inboundOrderUploadForm = new InboundOrderUploadForm();

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
}