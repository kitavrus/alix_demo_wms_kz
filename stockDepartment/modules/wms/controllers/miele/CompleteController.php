<?php

namespace app\modules\wms\controllers\miele;

use stockDepartment\components\Controller;
use stockDepartment\modules\wms\models\miele\service\ServiceInbound;
use stockDepartment\modules\wms\models\miele\service\ServiceMovement;
use stockDepartment\modules\wms\models\miele\service\ServiceOutbound;
use Yii;

class CompleteController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    // SHOW
    public function actionShowInbound()
    { // show-inbound
        $service =  new ServiceInbound();
        $provider = $service->getOrderForComplete();
        return $this->render('show-inbound',['dataProvider'=>$provider]);
    }
    // DONE
    public function actionDoneInbound($id)
    { // done-inbound
        $service =  new ServiceInbound();
        $service->acceptedOrder($id);
        Yii::$app->session->setFlash('success', 'Накладная успешно закрыта');
        return $this->redirect('show-inbound');
    }

    //
    public function actionShowOutbound()
    { // show-outbound
        $service =  new ServiceOutbound();
        $provider = $service->getOrderForComplete();
        return $this->render('show-outbound',['dataProvider'=>$provider]);
    }
    //
    public function actionShowMovement()
    { // show-movement
        return 'show ok';
    }


    //
    public function actionDoneOutbound($id)
    { // done-outbound
        $service = new ServiceOutbound();
        $service->acceptedOrder($id); //
        Yii::$app->session->setFlash('success', 'Накладная успешно закрыта');
        return $this->redirect('show-outbound');
    }
    //
    public function actionDoneMovement($id)
    { // done-movement
        $service = new ServiceMovement();
        $service->acceptedOrder($id); //
        Yii::$app->session->setFlash('success', 'Накладная успешно закрыта');
        return $this->redirect('show-outbound');
    }
}