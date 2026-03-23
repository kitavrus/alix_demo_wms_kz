<?php

namespace app\modules\ecommerce\controllers\defacto;

//use common\clientObject\constants\Constants;
use common\ecommerce\defacto\inbound\service\OutboundAPIService;
use stockDepartment\components\Controller;
use common\clientObject\main\outbound\forms\OutboundForm;
//use common\modules\employees\models\Employees;
//use common\modules\outbound\models\OutboundOrder;
//use common\modules\outbound\models\OutboundOrderItem;
//use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Response;

use common\ecommerce\entities\EcommerceEmployee as Employees;
use common\ecommerce\entities\EcommerceOutbound as OutboundOrder;
use common\ecommerce\entities\EcommerceOutboundItem as OutboundOrderItem;
use common\ecommerce\entities\EcommercePickingList as OutboundPickingLists;
use common\ecommerce\defacto\outbound\service\OutboundService ;
use common\ecommerce\defacto\outbound\forms\BeginEndPickListForm;

class PickingController extends Controller
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
        $service = new OutboundService();
//        $service->GetShipments(10);
        //$outboundID = '161'; // OMC-8221401
//        $outboundID = '202'; // OMC-8222938
//        $service->saveWaybillDocument($outboundID);

//        $service->SendShipmentFeedback(8);
//        $service->SendAcceptedShipments(3);
//        $service->resetByOutboundOrderId(3);
//        $service->resetByOutboundOrderId(4);
//        $service->resetByOutboundOrderId(5);
//        $service->resetByOutboundOrderId(6);
//        $service->resetByOutboundOrderId(7);
//        $service->resetByOutboundOrderId(8);
//        $service->CancelShipment(2);

//        $service->GetShipments();
// VarDumper::dump($response,10,true);
// die;
        return $this->render('all-pick-list',[
            'dataProvider'=>$service->getOrdersForPrintPickingList(),

        ]);
    }

    public function actionPrint($ids)
    {
//        $ids = explode(',',$ids);
//        $outboundList = [];
//        foreach($ids as $id) {
//            $service = new OutboundService();
//            $outboundInfo = $service->getOrderInfo($id);
//            $service->runReservation($outboundInfo);
//
//            $outboundList[] = $service->getOrderInfo($id);
//        }

        return $this->render('print/pick-list-pdf',[
            'outboundList'=>(new OutboundService())->reservationOrdersForPrintPickingList(explode(',',$ids))
        ]);
    }

    public function actionPrintPickingListNoReserve($id)
    { // ecommerce/defacto/picking/print-picking-list-no-reserve?id=66383
        return $this->render('print/pick-list-pdf',[
            'outboundList'=>(new OutboundService())->makeDataForPrintPickingListNoReserved([$id])
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