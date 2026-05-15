<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound;

use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\OutboundPickingService;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\OutboundService;
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
		// alix/ecommerce/picking/print-picking-list-no-reserve?id=66383
		return $this->render('print/pick-list-pdf',[
			'outboundList'=>(new OutboundPickingService())->makeDataForPrintPickingListNoReserved([$id])
		]);
	}

	public function behaviors()
	{
		$behaviors = parent::behaviors();
		$behaviors['apiLogger'] = ['class' => \common\log\IncomingApiLogger::class, 'category' => 'alix.ui'];
		return $behaviors;
	}
}