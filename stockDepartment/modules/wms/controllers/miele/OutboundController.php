<?php

namespace app\modules\wms\controllers\miele;

use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use stockDepartment\components\Controller;
use stockDepartment\modules\wms\models\miele\form\BeginEndPickListForm;
use stockDepartment\modules\wms\models\miele\form\OutboundForm;
use stockDepartment\modules\wms\models\miele\service\ServiceOutbound;
use stockDepartment\modules\wms\models\miele\service\ServiceOutboundReservation;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\VarDumper;
use yii\web\Response;

class OutboundController extends Controller
{
    public function actionIndex()
    { // wms/miele/outbound/index
        $outboundForm = new OutboundForm();

        return $this->render('index', [
            'outboundForm' => $outboundForm,
        ]);
    }

    public function actionPickingList()
    { // /wms/miele/outbound/picking-list
        $service = new ServiceOutbound();
        return $this->render('pick-list',[
            'dataProvider'=>$service->getOrdersForPrintPickingList()
        ]);
    }

    public function actionPrintPickingList($id)
    { // /wms/miele/outbound/print-picking-list
        $service = new ServiceOutbound();
        $outboundInfo = $service->getOrderInfo($id);
        // TEST
//        $outboundInfo->order->expected_qty = OutboundOrderItem::find()->andWhere(['outbound_order_id'=>$id])->sum('expected_qty');
//        $outboundInfo->order->save(false);
        // TEST RESERVATION
        $outboundReservation = new ServiceOutboundReservation();
        $outboundReservation->run($outboundInfo);
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
    //
    public function actionScanningForm()
    {  // /wms/miele/outbound/scanning-form
        $form = new OutboundForm();
        return $this->render('scanning-form',['model'=>$form]);
    }
    /*
    * Scanning form handler Is Employee Barcode
    * DONE
    * */
    public function actionEmployeeBarcodeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';

        $model = new OutboundForm();
        $model->scenario = 'onEmployeeBarcode';

        if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }
    /*
  * Scanning form handler Is Picking List Barcode
  * */
    public function actionPickListBarcodeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];

        $scanForm = new OutboundForm();
        $scanForm->scenario = 'onPickListBarcode';

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new ServiceOutbound($scanForm->getDTO());
            $orderInfo = $service->getOrderInfo();
            return [
                'success'=>1,
                'expected_qty'=> intval($orderInfo->order->expected_qty),
                'accepted_qty'=> intval($orderInfo->order->accepted_qty),
            ];
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }
    /*
   * Scanning form handler Is Box Barcode
   * */
    public function actionBoxBarcodeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $qtyInBox = 0;

        $scanForm = new OutboundForm();
        $scanForm->scenario = 'onBoxBarcode';

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new ServiceOutbound($scanForm->getDTO());
            $qtyInBox = $service->qtyProductInBox();
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'qtyInBox' => $qtyInBox,
        ];
    }
    /*
     * Scanning form handler Is Product Barcode
     * */
    public function actionProductBarcodeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $qtyInBox = 0;
        $expectedQty = 0;
        $acceptedQty = 0;

        $scanForm = new OutboundForm();
        $scanForm->scenario = 'onProductBarcode';
        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new ServiceOutbound($scanForm->getDTO());
            $service->makeScanned();
            $qtyInBox = $service->qtyProductInBox();
            $orderInfo = $service->getOrderInfo();
            $expectedQty = intval($orderInfo->order->expected_qty);
            $acceptedQty = intval($orderInfo->order->accepted_qty);
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'qtyInBox' => $qtyInBox,
            'expected_qty'=> $expectedQty,
            'accepted_qty'=> $acceptedQty,
        ];
    }
    /*
     * Scanning form handler Is Fub Barcode
     * */
    public function actionFubBarcodeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $qtyInBox = 0;
        $expectedQty = 0;
        $acceptedQty = 0;
        $scanForm = new OutboundForm();
        $scanForm->scenario = 'onFubBarcode';

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new ServiceOutbound($scanForm->getDTO());
            $service->makeScannedFab();
            $qtyInBox = $service->qtyProductInBox();
            $orderInfo = $service->getOrderInfo();
            $expectedQty = intval($orderInfo->order->expected_qty);
            $acceptedQty = intval($orderInfo->order->accepted_qty);
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'qtyInBox' => $qtyInBox,
            'expected_qty'=> $expectedQty,
            'accepted_qty'=> $acceptedQty,
        ];
    }
    /*
     *
     * */
    public function actionPrintBoxLabel()
    {
        $scanForm = new OutboundForm();
        $scanForm->scenario = 'onPrintBoxLabel';
        $boxes = [];
        if ($scanForm->load(Yii::$app->request->get()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();
            $service = new ServiceOutbound($dto);
            $service->makePrintBoxLabel();
            $boxes = $service->getBoxesInOrder();
        }

        return $this->render("print/box-label-pdf",['boxes'=>$boxes,'dto'=>$dto]);
    }
    /*
     *
     * */
    public function actionValidatePrintBoxLabel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $orderId = 0;
        $scanForm = new OutboundForm();
        $scanForm->scenario = 'onPrintBoxLabel';

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();
            $orderId = $dto->order->id;
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'orderId' => $orderId,
        ];
    }
    /*
   * Clear all product in box
   * @param string $box_barcode Box barcode
   * */
    public function actionClearBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $qtyInBox = 0;
        $expectedQty = 0;
        $acceptedQty = 0;
        $scanForm = new OutboundForm();
        $scanForm->scenario = 'onCleanBox';

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new ServiceOutbound($scanForm->getDTO());
            $service->cleanBox();
            $qtyInBox = $service->qtyProductInBox();
            $orderInfo = $service->getOrderInfo();
            $expectedQty = intval($orderInfo->order->expected_qty);
            $acceptedQty = intval($orderInfo->order->accepted_qty);
        } else {
            $errors = ActiveForm::validate($scanForm);
        }
        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'qtyInBox' => $qtyInBox,
            'expected_qty'=> $expectedQty,
            'accepted_qty'=> $acceptedQty,
        ];
    }
    /*
    * Print the list of differences
    * */
    public function actionPrintDiffList()
    { // print-diff
        $outboundForm = new OutboundForm();
        $outboundForm->setScenario('onPrintDiffList');

        if($outboundForm->load(Yii::$app->request->get()) && $outboundForm->validate()) {
            $outboundInfo = $outboundForm->getDTO();
            $service = new ServiceOutbound($outboundInfo);
            return $this->render('print/diff-list-pdf',['items'=>$service->getOrderItemsForDiffReport(),'outboundInfo'=>$outboundInfo]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = ActiveForm::validate($outboundForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];

    }
}