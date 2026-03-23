<?php

namespace app\modules\intermode\controllers;

use common\components\BarcodeManager;
use stockDepartment\components\Controller;

use Yii;
use common\modules\client\models\Client;
use stockDepartment\modules\product\models\ProductSearch;
use app\modules\intermode\controllers\inbound\domain\InboundScanningService;
use app\modules\intermode\controllers\outbound\domain\OutboundService;

class DefaultController extends Controller
{
    /**
     * Select order for print pick list
     *
     * */
    public function actionIndex()
    {
//        $clientsArray =  Client::getActiveItems();
        $clientsArray =  Client::getActiveWMSItems();

        return $this->render('index', ['clientsArray'=>$clientsArray]);
    }

    /**
     *
     * */
    public function actionResendInboundStatus()
    {	// http://intermode-kz.nmdx.kz/intermode/default/resend-inbound-status
		// (new InboundScanningService())->sendStatusInWork(122892);
		 // (new InboundScanningService())->sendStatusCompleted(122787);
		// (new InboundScanningService())->sendStatusCompleted(122856); // 130
//		 (new InboundScanningService())->sendStatusCompleted(122894); // 00TK-000092_16_05_2025 
		// (new InboundScanningService())->sendStatusCompleted(122988); // 122988	00TK-000093_17_05_2025 / 
		// (new InboundScanningService())->sendStatusCompleted(123032); // 00TK-000155_04_06_2025
	//	 (new InboundScanningService())->sendStatusCompleted(123033); // 00TK-000158_02_06_2025 / 
		 (new InboundScanningService())->sendStatusCompleted(123008); // 00TK-000181_23_05_2025  / 
		 

        return $this->redirect('index');
    }
    /**
     *
     * */
    public function actionResendOutboundStatus($outboundID = -1)
    {	// default/resend-inbound-status
//    	(new OutboundService())->sendStatusInCompleted($outboundID);
    	(new OutboundService())->sendStatusInCompleted(76410);
        return $this->redirect('index');
    }
}
