<?php

namespace stockDepartment\modules\alix\controllers;

use common\components\BarcodeManager;
use stockDepartment\components\Controller;

use Yii;
use common\modules\client\models\Client;
use stockDepartment\modules\product\models\ProductSearch;
use stockDepartment\modules\alix\controllers\inbound\domain\InboundScanningService;
use stockDepartment\modules\alix\controllers\outbound\domain\OutboundService;

use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;

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
    public function actionResendInboundStatusComplete($id = -1)
    {	// http://alix-kz.nmdx.kz/alix/default/resend-inbound-status-complete?id=
    
	    (new InboundScanningService())->sendStatusCompleted($id);
        return $this->redirect('index');
    
    
		// (new InboundScanningService())->sendStatusInWork(122892);
		 // (new InboundScanningService())->sendStatusCompleted(122787);
		// (new InboundScanningService())->sendStatusCompleted(122856); // 130
//		 (new InboundScanningService())->sendStatusCompleted(122894); // 00TK-000092_16_05_2025 
		// (new InboundScanningService())->sendStatusCompleted(122988); // 122988	00TK-000093_17_05_2025 / 
		// (new InboundScanningService())->sendStatusCompleted(123032); // 00TK-000155_04_06_2025
	//	 (new InboundScanningService())->sendStatusCompleted(123033); // 00TK-000158_02_06_2025 / 
	//	 (new InboundScanningService())->sendStatusCompleted(123008); // 00TK-000181_23_05_2025  / 
		/* (new InboundScanningService())->sendStatusInWork(122988);  // 93
		 (new InboundScanningService())->sendStatusCompleted(122988); // 93		

		 (new InboundScanningService())->sendStatusInWork(123085);  // 00TK-000102_17_05_2025 / 
		 (new InboundScanningService())->sendStatusCompleted(123085); // 00TK-000102_17_05_2025 / 
		 
		 (new InboundScanningService())->sendStatusCompleted(122919);  // 00ТК-000100
		 
		 (new InboundScanningService())->sendStatusCompleted(122920); // 00ТК-000101
		 
		 (new InboundScanningService())->sendStatusCompleted(122922); // 00ТК-000104
		 
		 (new InboundScanningService())->sendStatusCompleted(122921); // 	00TK-000103_18_05_2025 / 
		 
		 (new InboundScanningService())->sendStatusCompleted(122918); //	00TK-000099_17_05_2025 / 
		 
		 (new InboundScanningService())->sendStatusCompleted(122917); //	00TK-000098_17_05_2025
		 
		 (new InboundScanningService())->sendStatusCompleted(122916); //	00TK-000097_17_05_2025
		 
		 (new InboundScanningService())->sendStatusCompleted(122915); //	00TK-000096_17_05_2025 / 	
		 
		 (new InboundScanningService())->sendStatusCompleted(122914); //	00TK-000095_17_05_2025 / 
		 
		 (new InboundScanningService())->sendStatusCompleted(122913); //	00TK-000094_17_05_2025 / 
		 */
		 
		 // (new InboundScanningService())->sendStatusCompleted(123241); //	00TK-000094_17_05_2025 / 
		//  (new InboundScanningService())->sendStatusCompleted(122999); //		00TK-000142_21_05_2025 / 
		//  (new InboundScanningService())->sendStatusCompleted(122991); //		00TK-000155_22_05_2025 / 
		 // (new InboundScanningService())->sendStatusCompleted(122989); //			00TK-000160_22_05_2025 / 
		//  (new InboundScanningService())->sendStatusCompleted(123007); //		00TK-000180_23_05_2025 / 	
		//  (new InboundScanningService())->sendStatusCompleted(123015); //	00TK-000183_28_05_2025 / 
		//  (new InboundScanningService())->sendStatusCompleted(123008); //	00TK-000181_23_05_2025 / 	
		
		// (new InboundScanningService())->sendStatusCompleted(123008); //	00TK-000181_23_05_2025 / 	
		
		$orderIDs = [
123121,
123139,
123140,
123141,
123150,
123152,
123146,
123153,
123154,
123157
		];
		
		foreach ($orderIDs as $id) {
			// (new InboundScanningService())->sendStatusCompleted($id);
		}
		



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

    /**
     *
     * */
    public function actionDiffGuid($outboundID = -1)
    {	// /alix/default/diff-guid?outboundID=76933

		$items = OutboundOrderItem::findAll(["outbound_order_id"=>$outboundID]);
		foreach ($items as $item) {
			$stocks = Stock::find()->andWhere(["outbound_order_item_id"=>$item->id])->all();
			foreach ($stocks as $stock) {
				if($stock->product_sku != $item->product_sku) {
					echo "stock: ".$stock->product_sku." / item: ".$item->product_sku."<br />";
					echo "stock: ".$stock->product_id." / item: ".$item->product_id."<br />";
					echo "stock: ".$stock->product_barcode." / item: ".$item->product_barcode."<br />";
					$stock->deleted = 1;
					// $stock->save(false);
				}
			}
		}
		echo "END"."<br />";
		die;
        return $this->render('index');
    }
	
	    /**
     *
     * */
    public function actionResetOutboundOrder($outboundID = -1)
    {	// http://alix-kz.nmdx.kz/alix/default/reset-outbound-order
//    	(new OutboundService())->sendStatusInCompleted($outboundID);
    	//Stock::resetByOutboundOrderId(76987);
    	// Stock::resetByOutboundOrderId(77129);
    	// Stock::resetByOutboundOrderId(77130);
    	 Stock::resetByOutboundOrderId(77559);
        return $this->redirect('index');
    }
}
