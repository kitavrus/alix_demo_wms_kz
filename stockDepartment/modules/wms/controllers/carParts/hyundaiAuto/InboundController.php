<?php

namespace app\modules\wms\controllers\carParts\hyundaiAuto;

use common\clientObject\hyundaiAuto\inbound\service\InboundOrderService;
use common\modules\stock\models\Stock;
use common\overloads\ArrayHelper;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use common\clientObject\hyundaiAuto\inbound\forms\ScanInboundForm;

class InboundController extends Controller
{
    public function actionIndex()
    {
        $inboundForm = new ScanInboundForm();
        $service = new InboundOrderService();
        $conditionTypeArray = (new Stock())->getConditionTypeArray();
        return $this->render('index', [
            'inboundForm' => $inboundForm,
            'newAndInProcessOrders' => $service->getNewAndInProcessOrder(),
            'conditionTypeArray' => $conditionTypeArray,
        ]);
    }

    public function actionSelectOrderNumber()
    { // select-order-number
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario('onOrderNumber');

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $service = new InboundOrderService($inboundForm->getDTO());
            $qty = $service->getQtyInOrder();
            return [
                'success' => 'Y',
                'expected_qty' => intval($qty->expected_qty),
                'accepted_qty' => intval($qty->accepted_qty),
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    public function actionScanTransportedBoxBarcode()
    { // scan-transported-box-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario('onTransportedBoxBarcode');

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            $qtyProductInUnit = $inboundOrderService->getQtyInUnitByBarcodeInOrder();
            return [
                'success' => 'Y',
                'qtyProductInUnit' => intval($qtyProductInUnit),
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

//    public function actionScanModelBarcode()
//    { // scan-model-barcode
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        $inboundForm = new ScanInboundForm();
//        $inboundForm->setScenario('onProductModel');
//
//        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
//            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
//            $qtyModel = $inboundOrderService->getQtyModelsInOrder();
//            return [
//                'success' => 'Y',
//                'isEmptyProductBarcodeByModel' => $inboundOrderService->isEmptyProductBarcodeByModel(),
//                'expectedQtyModel' => $qtyModel->expected_qty,
//                'acceptedQtyModel' => $qtyModel->accepted_qty,
//            ];
//        }
//
//        $errors = ActiveForm::validate($inboundForm);
//        return [
//            'success' => (empty($errors) ? 'Y' : 'N'),
//            'errors' => $errors
//        ];
//    }

    public function actionScanProductBarcode()
    { // scan-product-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario('onProductBarcode');

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            $inboundOrderService->addScannedProductToStock($inboundForm->getDTO());
            $qtyModel = $inboundOrderService->getQtyModelsInOrder();
            $qty = $inboundOrderService->getQtyInOrder();
            $qtyProductInUnit = $inboundOrderService->getQtyInUnitByBarcodeInOrder();
            return [
                'success' => 'Y',
                'expectedQtyInOrderItem' => intval($qtyModel->expected_qty),
                'acceptedQtyInOrderItem' => intval($qtyModel->accepted_qty),
                'expectedQtyInOrder' => intval($qty->expected_qty),
                'acceptedQtyInOrder' => intval($qty->accepted_qty),
                'qtyProductInUnit' => intval($qtyProductInUnit),
            ];
        }
        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }
    //
    public function actionAddProductQty()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario('onProductQty');

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            $inboundOrderService->addScannedProductToStock($inboundForm->getDTO());
            $qtyModel = $inboundOrderService->getQtyModelsInOrder();
            $qty = $inboundOrderService->getQtyInOrder();
            $qtyProductInUnit = $inboundOrderService->getQtyInUnitByBarcodeInOrder();
            return [
                'success' => 'Y',
                'expectedQtyInOrderItem' => intval($qtyModel->expected_qty),
                'acceptedQtyInOrderItem' => intval($qtyModel->accepted_qty),
                'expectedQtyInOrder' => intval($qty->expected_qty),
                'acceptedQtyInOrder' => intval($qty->accepted_qty),
                'qtyProductInUnit' => intval($qtyProductInUnit),
            ];
        }
        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    //
    public function actionCleanTransportedBox()
    { // clean-transported-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario('onCleanTransportedBox');

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            $inboundOrderService->cleanTransportedBox();

//            $qtyModel = $inboundOrderService->getQtyModelsInOrder();
            $qty = $inboundOrderService->getQtyInOrder();
            $qtyProductInUnit = $inboundOrderService->getQtyInUnitByBarcodeInOrder();
            return [
                'success' => 'Y',
                'expectedQtyInOrderItem' => 0,
                'acceptedQtyInOrderItem' => 0,
                'expectedQtyInOrder' => intval($qty->expected_qty),
                'acceptedQtyInOrder' => intval($qty->accepted_qty),
                'qtyProductInUnit' => intval($qtyProductInUnit),
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }
    //
    public function actionPrintDiffInOrder()
    {
        $orderNumberId = ArrayHelper::getValue(Yii::$app->request->get('ScanInboundForm'), 'orderNumberId');
        $inboundOrderService = new InboundOrderService();
        return $this->render('print/diff-in-order-pdf', ['items' => $inboundOrderService->getItemsForDiffReportByOrderId($orderNumberId)]);
    }
    //
    public function actionShowOrderItems()
    { // show-order-items
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario('onShowOrderItems');

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            return [
                'success' => 'Y',
                'items' => $this->renderPartial('_show-order-items', ['items' => $inboundOrderService->getOrderItems()]),
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }
    //
    public function actionCloseOrder()
    { // close-order
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario('onCloseOrder');

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            $inboundOrderService->closeOrder();
            return [
                'success' => 'Y',
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }
}