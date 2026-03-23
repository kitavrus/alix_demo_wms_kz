<?php
namespace app\modules\intermode\controllers\ecommerce;

use app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundReservationService;
use app\modules\intermode\controllers\ecommerce\outbound\domain\repository\OutboundRepository;
use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;

class DefaultController extends  \stockDepartment\components\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionResendOutboundStatus()
    {
		// /intermode/ecommerce/default/resend-outbound-status
		
		 \Yii::$app->getSession()->setFlash('success', "status: resend-outbound-status empty");
		return $this->render('index');
		
		
		$service = new \app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundScanningService();
		$orderNumber = "3274";
		$apiStatus = $service->package($orderNumber);
		\Yii::$app->getSession()->setFlash('success', "status: ".print_r($apiStatus,true));
        return $this->render('index');
    }
	
	public function actionResetAndCancelOutbound($id)
    {
		// /intermode/ecommerce/default/reset-and-cancel-outbound
		$service = new \app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundReservationService();
		$apiStatus = $service->resetAnCancelByOutboundOrderId($id);
		\Yii::$app->getSession()->setFlash('success', "status: ".print_r($apiStatus,true));
        return $this->redirect('index');
    }
	
	public function actionResetOutbound($id)
    {
		// /intermode/ecommerce/default/reset-outbound?id=3877
		// http://intermode-kz.nmdx.kz//intermode/ecommerce/default/reset-outbound?id=3885
		// http://intermode-kz.nmdx.kz//intermode/ecommerce/default/reset-outbound?id=3881
		// http://intermode-kz.nmdx.kz//intermode/ecommerce/default/reset-outbound?id=3882
		
		// http://intermode-kz.nmdx.kz//intermode/ecommerce/default/reset-outbound?id=3876
		$service = new \app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundReservationService();
		//$bool = $service->resetByOutboundOrderId($id);
		//$bool = $service->resetByOutboundOrderId(4950);
		//$bool = $service->resetByOutboundOrderId(3876);
		//$bool = $service->resetByOutboundOrderId(3878);
		//$bool = $service->resetByOutboundOrderId(3879);
		//$bool = $service->resetByOutboundOrderId(3880);
		//$bool = $service->resetByOutboundOrderId(3887);
		//$bool = $service->resetByOutboundOrderId(3888);
		//$bool = $service->resetByOutboundOrderId(3889);
		\Yii::$app->getSession()->setFlash('success', "status: ".print_r($bool,true));
        return $this->redirect('index');
    }
	
 
	public function actionReReserving($id)
	{
		// /intermode/ecommerce/default/re-reserving?id=3877
		// http://intermode-kz.nmdx.kz/intermode/ecommerce/default/re-reserving?id=-1
		//die("STOP");
		
		$service = new \app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundReservationService();
		//$bool = $service->resetByOutboundOrderId($id);
		//$bool = $service->resetByOutboundOrderId(4950);

		$reservationService = new \app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundReservationService();
		$repository = new OutboundRepository();

		$orderIDs = [
		4982
//			4950,
//			4951,
//			4954,
//			4959,
//			4957,
//			4961,
//			4963,
		];
		foreach ($orderIDs as $id) {
			  $service->resetByOutboundOrderId($id);
			 $reservationService->run($repository->getOrderInfo($id));
		}

		\Yii::$app->getSession()->setFlash('success', "OK");
		return $this->redirect('index');
	}
	
	    public function actionResetAndSetRepeatOutbound($id)
    {
		// /intermode/ecommerce/default/reset-and-set-repeat-outbound
		$service = new OutboundReservationService();
		$service->resetByOutboundOrderId($id);

		$repository = new OutboundRepository();
		$order = $repository->getOrderByID($id);
		$order->status = OutboundStatus::getRepeatOrder();
		$order->save(false);
		
		\Yii::$app->getSession()->setFlash('success', "Заказ отменен т.к это дубль: ".$order->order_number);
        return $this->redirect('/intermode/ecommerce/outbound/report/index');
    }
	
}