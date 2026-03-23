<?php

namespace app\modules\intermode\controllers\ecommerce\outbound;

use app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundPickingService;
use app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundService;
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

	public function actionPrintPickingListNoReserve($id)
	{
		// intermode/ecommerce/picking/print-picking-list-no-reserve?id=66383
		return $this->render('print/pick-list-pdf',[
			'outboundList'=>(new OutboundPickingService())->makeDataForPrintPickingListNoReserved([$id])
		]);
	}

}