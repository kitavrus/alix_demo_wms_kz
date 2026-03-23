<?php

namespace app\modules\wms\controllers\carParts\main;

use common\clientObject\constants\Constants;
use common\modules\stock\models\RackAddress;
use stockDepartment\components\Controller;
use common\clientObject\main\outbound\forms\OutboundForm;
use common\clientObject\main\outbound\service\OutboundService ;
use common\clientObject\main\outbound\forms\BeginEndPickListForm;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use Yii;
use yii\web\Response;

class OutboundController extends Controller
{
    public function actionIndex()
    {
        $outboundForm = new OutboundForm();
        return $this->render('index', [
            'outboundForm' => $outboundForm,
        ]);
    }

    public function actionAllPickingList()
    {
		
		$allList = Stock::find()
            ->andWhere('`address_sort_order` IS NULL AND `secondary_address` IS NOT NULL')
            ->andWhere(['client_id'=>Constants::getCarPartClientIDs()])
            ->all();
        foreach($allList as $stockRow) {
            $stockRow->address_sort_order = RackAddress::find()->select('id')->andWhere(['address'=>$stockRow->secondary_address])->scalar();
            $stockRow->save(false);
        }
		
		
        $service = new OutboundService();
        $storesArray = (new \common\modules\store\service\Service())->getStoreCityNameByClientWithPattern(Constants::getCarPartClientIDs());

        return $this->render('all-pick-list',[
            'dataProvider'=>$service->getOrdersForPrintPickingList(),
            'storesArray' => $storesArray,
        ]);
    }

    public function actionPrintPickingList($id)
    {
        $service = new OutboundService();
        $outboundInfo = $service->getOrderInfo($id);
		
		//if(!in_array($outboundInfo->id,[28822])) {
			$service->runReservation($outboundInfo);
		//}

       $outboundInfo = $service->getOrderInfo($id);

        if($outboundInfo->order->expected_qty != $outboundInfo->order->allocated_qty) {
            Yii::$app->session->setFlash('danger', 'Заказ <strong>'. $outboundInfo->order->order_number.'</strong> не полностью зарезервирован <strong>'.$outboundInfo->order->expected_qty.' из '.$outboundInfo->order->allocated_qty."</strong>");
            return $this->redirect('all-picking-list');
        }


        return $this->render('print/pick-list-pdf',[
            'outboundInfo'=>$outboundInfo
        ]);
    }
	
	
	public function actionPrintPickingListNoReserve($id)
    { // /wms/carParts/main/outbound/print-picking-list-no-reserve?id=27588	
        $service = new OutboundService();
        $outboundInfo = $service->getOrderInfo($id);
        
        return $this->render('print/pick-list-pdf',[
            'outboundInfo'=>$outboundInfo
        ]);
    }
	
	

    // Begin and End picking process
    public function actionBeginEndPickingHandler()
    { // begin-end-picking-handler
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

                //S: TODO сделать это через события
                OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKING, 'cargo_status'=>OutboundOrder::CARGO_STATUS_IN_PROCESSING],['id'=>$oplModel->outbound_order_id]);
                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKING],['outbound_order_id'=>$oplModel->outbound_order_id]);
                // E:
                Stock::updateAll(["status" => Stock::STATUS_OUTBOUND_PICKING], ['outbound_picking_list_id' => $oplModel->id]);

                $messagesSuccess[] = Yii::t('outbound/messages', 'You can start assembly');
                $step ='begin';
            }

            if ( $status == OutboundPickingLists::STATUS_BEGIN ) {

                $oplModel->status = OutboundPickingLists::STATUS_END;
                $oplModel->end_datetime = time();
                $oplModel->save(false);

                //S: TODO сделать это через события
                OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKED, 'cargo_status'=>OutboundOrder::CARGO_STATUS_IN_PROCESSING],['id'=>$oplModel->outbound_order_id]);
                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKED],['outbound_order_id'=>$oplModel->outbound_order_id]);
                // E:
                Stock::updateAll(["status" => Stock::STATUS_OUTBOUND_PICKED], ['outbound_picking_list_id' => $oplModel->id]);

                $messagesSuccess[] = Yii::t('outbound/messages', 'Assembling successfully completed');
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

        return $this->render('begin-end-pick-list-form', ['model' => $model]);
    }
}