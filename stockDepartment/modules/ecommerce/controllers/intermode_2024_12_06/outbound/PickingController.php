<?php

namespace app\modules\ecommerce\controllers\intermode\outbound;

use app\modules\ecommerce\controllers\intermode\outbound\domain\OutboundPickingService;
use stockDepartment\components\Controller;

class PickingController extends Controller
{

	public function actionLists()
	{
		$service = new OutboundPickingService();
		return $this->render('lists',[
			'dataProvider'=>$service->getOrdersForPrintPickingList(),

		]);
	}

	public function actionPrint($ids)
	{
		return $this->render('print/pick-list-pdf',[
			'outboundList'=>(new OutboundPickingService())->reservationOrdersForPrintPickingList(explode(',',$ids))
		]);
	}
}