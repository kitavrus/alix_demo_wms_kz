<?php
namespace console\controllers;

use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use stockDepartment\modules\intermode\controllers\common\notify\b2b\CompleteOutboundOrderMsgDTO;
use stockDepartment\modules\intermode\controllers\common\notify\b2b\TelegramIntermodeB2BNotification;
use stockDepartment\modules\intermode\controllers\cronManager\domains\cron_manager\CronManagerOrderType;
use stockDepartment\modules\intermode\controllers\cronManager\domains\cron_manager\CronManagerService;
use stockDepartment\modules\intermode\controllers\outbound\domain\OutboundService;
use yii\console\Controller;

class CronManagerController extends Controller
{
    public function actionRun()
    { // php yii cron-manager/run
		file_put_contents("cron-manager-run.log","BEGIN ".date('Y-m-d H:i:s')."\n",FILE_APPEND);

		$service = new CronManagerService();
		$service->increasingCounterForJob();
		
		// Проверяем, какие джобы зависли
		$service->checkAndResetWorkingJobs();
		
    	if ($service->isExistsAnyWorkingJobs()) {
			echo "isExistsAnyWorkingJobs: TRUE"."\n";
    		return 0;
		}

		$job = $service->getAnyOneNewJob();
    	if (empty($job)) {
			echo "getAnyOneNewJob: EMPTY"."\n";
    		return 0;
		}

    	switch ($job->type) {
			case CronManagerOrderType::B2B_INBOUND:
				file_put_contents("cron-manager-run.log","Start:".CronManagerOrderType::B2B_INBOUND." ".date('Y-m-d H:i:s')."\n",FILE_APPEND);
				$this->sendB2bInboundDataByAPI($job->order_id);
				file_put_contents("cron-manager-run.log","Finish:".CronManagerOrderType::B2B_INBOUND." ".date('Y-m-d H:i:s')."\n",FILE_APPEND);
				break;
			case CronManagerOrderType::B2B_RETURN:
				file_put_contents("cron-manager-run.log","Start:".CronManagerOrderType::B2B_RETURN." ".date('Y-m-d H:i:s')."\n",FILE_APPEND);
				$this->sendB2bReturnDataByAPI($job->order_id);
				file_put_contents("cron-manager-run.log","Finish:".CronManagerOrderType::B2B_RETURN." ".date('Y-m-d H:i:s')."\n",FILE_APPEND);
				break;
			case CronManagerOrderType::B2B_OUTBOUND:
				file_put_contents("cron-manager-run.log","Start:".CronManagerOrderType::B2B_OUTBOUND." ".date('Y-m-d H:i:s')."\n",FILE_APPEND);
				$this->sendB2bOutboundDataByAPI($job->order_id);
				file_put_contents("cron-manager-run.log","Finish:".CronManagerOrderType::B2B_OUTBOUND." ".date('Y-m-d H:i:s')."\n",FILE_APPEND);
				break;
		}

        file_put_contents("cron-manager-run.log","END ".date('Y-m-d H:i:s')."\n",FILE_APPEND);
        return 0;
    }
    private function sendB2bInboundDataByAPI($orderID) {
		echo "php yii cron-manager/send-b2b-inbound-data-by-api begin". "\n";
		$service = new CronManagerService();
		$service->changeStatusToWorkingB2BInbound($orderID);
		$service->changeStatusToFinishB2BInbound($orderID);
		echo "php yii cron-manager/send-b2b-inbound-data-by-api finish". "\n";
		return 0;
	}

	private function sendB2bOutboundDataByAPI($orderID) {
		echo "php yii cron-manager/send-b2b-outbound-data-by-api begin". "\n";
		$service = new CronManagerService();
		$cmRow = $service->changeStatusToWorkingB2BOutbound($orderID);

		$model = OutboundOrder::findOne($orderID);
		$os = new OutboundService();
		$data = $os->sendStatusInCompleted($model->id);
		$model->status = Stock::STATUS_OUTBOUND_COMPLETE;
		$model->api_complete_status = empty($data->response_message) ? "Успешно" : $data->response_message;
		$model->save(false);
		$service->changeStatusToFinishB2BOutbound($orderID,$model->api_complete_status);

		TelegramIntermodeB2BNotification::sendMessageIfCompletedOutboundOrder(new CompleteOutboundOrderMsgDTO($cmRow->name,$model->api_complete_status));
		echo "php yii cron-manager/send-b2b-outbound-data-by-api finish". "\n";
		return 0;
	}

    private function sendB2bReturnDataByAPI($orderID) {
		echo "php yii cron-manager/send-b2b-return-data-by-api begin". "\n";
		$service = new CronManagerService();
		//$service->changeStatusToWorkingB2BInbound($orderID);
		//$service->changeStatusToFinishB2BInbound($orderID);
		echo "php yii cron-manager/send-b2b-return-data-by-api finish". "\n";
		return 0;
	}
}