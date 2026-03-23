<?php

namespace app\modules\intermode\controllers\outbound;

use app\modules\intermode\controllers\api\v1\outbound\service\OutboundAPIService;
use app\modules\intermode\controllers\outbound\domain\OutboundService;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use stockDepartment\modules\outbound\models\BeginEndPickListForm;
use Yii;
use stockDepartment\components\Controller;
use yii\web\Response;


class PickingController extends Controller
{
    /**
    * Print pick list
    * */
    public function actionPrint()
    {
        $id = Yii::$app->request->get('id');
        $items = OutboundOrder::find()->andWhere(['id' => $id])->asArray()->all();
        $os =  new OutboundService();
		$os->reservation($id);
		$os->sendStatusInWork($id);

        return $this->render('_print-pick-list-pdf', ['items' => $items]);
    }

	/**
	* Begin and End picking process
	* */
	public function actionScanningForm()
	{
		$model = new BeginEndPickListForm();

		if ($model->load(Yii::$app->request->post())) {

			$messagesInfo = '';
			$messagesSuccess = '';
			$picking_list_barcode = $model->picking_list_barcode;
			$employee_barcode = $model->employee_barcode;
			$status = '';
			$step = '';

			// Если собирать еще не начали, просим ввести сборочный лист или шк сборщика

			if ($oplModel = OutboundPickingLists::find()->where('barcode = :barcode', [':barcode' => $picking_list_barcode])->one()) {
				$status = $oplModel->status;
			} elseif(!empty($picking_list_barcode)) {
				$model->addError('beginendpicklistform-picking_list_barcode', Yii::t('outbound/errors', 'Вы указали неправильный сборочный лист'));
			}

			if ($status == OutboundPickingLists::STATUS_END) {
				$model->addError('beginendpicklistform-picking_list_barcode', Yii::t('outbound/errors', 'Этот сборочный лист уже собран'));
			}


			if ( !empty($employee_barcode)  && !($employeeModel = Employees::find()->where('barcode = :barcode', [':barcode' => $employee_barcode])->one()) ) {
				$model->addError('beginendpicklistform-employee_barcode', Yii::t('outbound/errors', 'Сотрудник не найден'));
			}

			$errors = $model->getErrors();

			if (empty($errors) && !empty($employeeModel) && $status == OutboundPickingLists::STATUS_PRINT) {
				$oplModel->status = OutboundPickingLists::STATUS_BEGIN;
				$oplModel->employee_id = $employeeModel->id;
				$oplModel->begin_datetime = time();
				$oplModel->save(false);

				OutboundPickingLists::updateAll([
					'status'=>OutboundPickingLists::STATUS_BEGIN,
					'employee_id'=> $employeeModel->id,
					'begin_datetime'=> time(),
				],['outbound_order_id'=>$oplModel->outbound_order_id]);


				//S: TODO сделать это через события
				OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKING, 'cargo_status'=>OutboundOrder::CARGO_STATUS_IN_PROCESSING],['id'=>$oplModel->outbound_order_id]);
				OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKING],['outbound_order_id'=>$oplModel->outbound_order_id]);
				// E:
				Stock::updateAll(["status" => Stock::STATUS_OUTBOUND_PICKING], ['outbound_picking_list_id' => $oplModel->id]);
				Stock::updateAll(["status" => Stock::STATUS_OUTBOUND_PICKING], ['outbound_order_id' => $oplModel->outbound_order_id]);

				$messagesSuccess[] = Yii::t('outbound/messages', 'Можете начинать сборку');
				$step ='begin';
			}

			if ( $status == OutboundPickingLists::STATUS_BEGIN ) {

				$oplModel->status = OutboundPickingLists::STATUS_END;
				$oplModel->end_datetime = time();
				$oplModel->save(false);

				OutboundPickingLists::updateAll([
					'status'=>OutboundPickingLists::STATUS_END,
					'end_datetime'=> time(),
				],['outbound_order_id'=>$oplModel->outbound_order_id]);

				//S: TODO сделать это через события
				OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKED, 'cargo_status'=>OutboundOrder::CARGO_STATUS_IN_PROCESSING],['id'=>$oplModel->outbound_order_id]);
				OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKED],['outbound_order_id'=>$oplModel->outbound_order_id]);
				// E:
				Stock::updateAll(["status" => Stock::STATUS_OUTBOUND_PICKED], ['outbound_picking_list_id' => $oplModel->id]);
				Stock::updateAll(["status" => Stock::STATUS_OUTBOUND_PICKED], ['outbound_order_id' => $oplModel->outbound_order_id]);

				$messagesSuccess[] = Yii::t('outbound/messages', 'Сборка успешно закончена');
				$step ='end';
			}


			Yii::$app->response->format = Response::FORMAT_JSON;

			return [
				'success' => (empty($errors) ? '1' : '0'),
				'errors' => $errors,
				'messagesInfo' => $messagesInfo,
				'messagesSuccess' => $messagesSuccess,
				'step' => $step,
			];
		}

		return $this->render('scanning-form', ['model' => $model]);
	}
}







