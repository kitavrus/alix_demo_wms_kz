<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\StockConditionType;
use common\ecommerce\defacto\inbound\service\InboundAPIService;
use common\ecommerce\defacto\inbound\service\InboundOrderService;
//use common\modules\stock\models\Stock;
use common\ecommerce\defacto\inbound\service\SendInBoundDataRequestService;
use common\overloads\ArrayHelper;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\helpers\VarDumper;
use yii\web\Response;
use common\ecommerce\defacto\inbound\forms\ScanInboundForm;

class InboundController extends Controller
{
    public function actionIndex()
    {
        $inboundForm = new ScanInboundForm();
        $service = new InboundOrderService();
        $conditionTypeArray = (new StockConditionType())->getConditionTypeArray();

//        $lcBarcode = '2430007688586';
//        $inboundAPIService = new InboundAPIService();
//        VarDumper::dump($inboundAPIService->get($lcBarcode,1),10,true);
//        VarDumper::dump($service->lotQtyInBox(1,'2430007688586'),10,true);
//        VarDumper::dump($service->productQtyInLot(1,'2300007158864'),10,true);
//        VarDumper::dump($service->productQtyInBox(1,'2430007688586'),10,true);
//        VarDumper::dump($service->productAcceptedQtyInBox(1,'2430007688586'),10,true);
//        VarDumper::dump($service->productQty(1,'2430007688586','8681991024729'),10,true);

//        $stockService = new \common\ecommerce\defacto\stock\service\Service();
//        $inboundAPIService = new InboundAPIService();
//        $ourInboundId = 2;
//        $zx = $stockService->getDataForSendByAPI($ourInboundId);
//        $make = $inboundAPIService->makeSendInBoundFeedBackDataRequest($zx);
//        SendInBoundDataRequestService::save($make);
//        $inboundAPIService->send($zx,$ourInboundId);
//        VarDumper::dump($zx,10,true);
//        die;


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
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_ORDER_NUMBER);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $service = new InboundOrderService($inboundForm->getDTO());
            $response = $service->getQtyInOrder();
            return [
                'success' => 'Y',
                'expected_box_qty' =>$response->expected_box_qty,
                'accepted_box_qty' =>$response->accepted_box_qty,
                'expected_lot_qty' =>$response->expected_lot_qty,
                'accepted_lot_qty' =>$response->accepted_lot_qty,
                'expected_product_qty' =>$response->expected_product_qty,
                'accepted_product_qty' =>$response->accepted_product_qty,
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    public function actionScanClientBoxBarcode()
    { // scan-client-box-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_CLIENT_BOX_BARCODE);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            $clientBoxInfo = $inboundOrderService->scanClientBoxBarcode();
            return [
                'success' => 'Y',
                'productAcceptedQty'=>$clientBoxInfo['productAcceptedQty'],
                'productExpectedQty'=>$clientBoxInfo['productExpectedQty'],
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    public function actionScanOurBoxBarcode()
    { // scan-our-box-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_OUR_BOX_BARCODE);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            $ourBoxInfo = $inboundOrderService->scanOurBoxBarcode();
            return [
                'success' => 'Y',
                'productAcceptedQty'=>$ourBoxInfo['productAcceptedQty'],
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    public function actionScanProductBarcode()
    { // scan-product-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_PRODUCT_BARCODE);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            $productInfo = $inboundOrderService->scanProductBarcode();

            return [
                'success' => 'Y',
                'InClientBoxProductAcceptedQty'=>$productInfo['InClientBoxProductAcceptedQty'],
                'InClientBoxProductExpectedQty'=>$productInfo['InClientBoxProductExpectedQty'],
                'InOurBoxProductAcceptedQty'=>$productInfo['InOurBoxProductAcceptedQty'],
            ];
        }
        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }
    //
//    public function actionAddProductQty()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        $inboundForm = new ScanInboundForm();
//        $inboundForm->setScenario('onProductQty');
//
//        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
//            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
//            $inboundOrderService->addScannedProductToStock($inboundForm->getDTO());
//            $qtyModel = $inboundOrderService->getQtyModelsInOrder();
//            $qty = $inboundOrderService->getQtyInOrder();
//            $qtyProductInUnit = $inboundOrderService->getQtyByBoxBarcodeInOrder();
//            return [
//                'success' => 'Y',
//                'expectedQtyInOrderItem' => intval($qtyModel->expected_qty),
//                'acceptedQtyInOrderItem' => intval($qtyModel->accepted_qty),
//                'expectedQtyInOrder' => intval($qty->expected_qty),
//                'acceptedQtyInOrder' => intval($qty->accepted_qty),
//                'qtyProductInUnit' => intval($qtyProductInUnit),
//            ];
//        }
//        $errors = ActiveForm::validate($inboundForm);
//        return [
//            'success' => (empty($errors) ? 'Y' : 'N'),
//            'errors' => $errors
//        ];
//    }

    //
    public function actionCleanOurBox()
    { // clean-transported-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_CLEAN_OUR_BOX);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $service = new InboundOrderService($inboundForm->getDTO());
            $response = $service->cleanOurBox();

            return [
                'success' => 'Y',
                'InClientBoxProductAcceptedQty'=>$response['InClientBoxProductAcceptedQty'],
                'InClientBoxProductExpectedQty'=>$response['InClientBoxProductExpectedQty'],
                'InOurBoxProductAcceptedQty'=>$response['InOurBoxProductAcceptedQty'],
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
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_SHOW_ORDER_ITEMS);

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
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_CLOSE_ORDER);

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

    /*
 *
 *
 * */
    public function actionPrintUnallocatedList()
    { // print-unallocated-list
        $id = Yii::$app->request->get('inbound_id');

        $stockService = new \common\ecommerce\defacto\stock\service\Service();
        return $this->render('print/print-unallocated-box-pdf',['items'=>$stockService->boxWithoutPlaceAddress($id)]);
    }
	
	
    public function actionCheckOrder()
    { // check-order
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_SHOW_ORDER_ITEMS);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
			$problemListInfo = $inboundOrderService->checkOrder();
            return [
                'success' => 'Y',
                'items' => $this->renderPartial('_check-order', ['problemInfo' => $problemListInfo]),
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }
	
    //
    public function actionDoneOrder()
    { // done-order
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_CLOSE_ORDER);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundOrderService($inboundForm->getDTO());
            $inboundOrderService->done();
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