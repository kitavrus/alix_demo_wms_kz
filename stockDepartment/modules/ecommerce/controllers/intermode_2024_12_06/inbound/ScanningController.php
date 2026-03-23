<?php

namespace app\modules\ecommerce\controllers\intermode\inbound;

use app\modules\ecommerce\controllers\intermode\stock\domain\constants\StockConditionType;
use app\modules\ecommerce\controllers\intermode\inbound\domain\InboundScanningService;
use common\overloads\ArrayHelper;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use app\modules\ecommerce\controllers\intermode\inbound\domain\forms\ScanInboundForm;

class ScanningController extends Controller
{
	/**
	 *
	 * */
    public function actionScanningForm()
    {
        $inboundForm = new ScanInboundForm();
        $service = new InboundScanningService();
        $conditionTypeArray = (new StockConditionType())->getConditionTypeArray();

        return $this->render('scanning-form', [
            'inboundForm' => $inboundForm,
            'newAndInProcessOrders' => $service->getNewAndInProcessOrder(),
            'conditionTypeArray' => $conditionTypeArray,
        ]);
    }

	/**
	 *
	 * */
    public function actionSelectOrderNumber()
    { // select-order-number
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_ORDER_NUMBER);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $service = new InboundScanningService($inboundForm->getDTO());
            $order = $service->getOrder($inboundForm->getDTO()->orderNumberId);
            return [
                'success' => 'Y',
                'expected_product_qty' =>$order->expected_product_qty,
                'accepted_product_qty' =>$order->accepted_product_qty,
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

	/**
	 *
	 * */
    public function actionScanOurBoxBarcode()
    { // scan-our-box-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_OUR_BOX_BARCODE);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
			$service = new InboundScanningService($inboundForm->getDTO());
            $ourBoxInfo = $service->scanOurBoxBarcode();
			$order = $service->getOrder($inboundForm->getDTO()->orderNumberId);

            return [
                'success' => 'Y',
                'productAcceptedQty'=>$ourBoxInfo['productAcceptedQty'],
				'expected_product_qty' =>$order->expected_product_qty,
				'accepted_product_qty' =>$order->accepted_product_qty,
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

	/**
	 *
	 * */
    public function actionScanProductBarcode()
    { // scan-product-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_PRODUCT_BARCODE);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundScanningService($inboundForm->getDTO());
            $productInfo = $inboundOrderService->scanProductBarcode();

            return [
                'success' => 'Y',
                'InOurBoxProductAcceptedQty'=>$productInfo['InOurBoxProductAcceptedQty'],
                'StockId'=>$productInfo['stockId'],
                'expected_product_qty'=>$productInfo['expected_product_qty'],
                'accepted_product_qty'=>$productInfo['accepted_product_qty'],
            ];
        }
        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

	/**
	 *
	 * */
	public function actionScanDatamatrix()
	{ // scan-product-barcode
		Yii::$app->response->format = Response::FORMAT_JSON;

		$inboundForm = new ScanInboundForm();
		$inboundForm->setScenario(ScanInboundForm::SCENARIO_SCAN_DATAMATRIX);

		if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
			$inboundOrderService = new InboundScanningService($inboundForm->getDTO());
			$inboundOrderService->scanDataMatrix();

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

	/**
	 *
	 * */
    public function actionCleanOurBox()
    { // clean-transported-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_CLEAN_OUR_BOX);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $service = new InboundScanningService($inboundForm->getDTO());
            $service->cleanOurBox();
			$order = $service->getOrder($inboundForm->getDTO()->orderNumberId);
            return [
                'success' => 'Y',
				'expected_product_qty' =>$order->expected_product_qty,
				'accepted_product_qty' =>$order->accepted_product_qty,
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }
	/**
	 *
	 * */
    public function actionPrintDiffInOrder()
    {
        $orderNumberId = ArrayHelper::getValue(Yii::$app->request->get('ScanInboundForm'), 'orderNumberId');
        $inboundOrderService = new InboundScanningService();
        return $this->render('print/diff-in-order-pdf', ['items' => $inboundOrderService->getItemsForDiffReportByOrderId($orderNumberId)]);
    }
	/**
	 *
	 * */
    public function actionShowOrderItems()
    { // show-order-items
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new ScanInboundForm();
        $inboundForm->setScenario(ScanInboundForm::SCENARIO_SHOW_ORDER_ITEMS);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundScanningService($inboundForm->getDTO());
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

	/**
	 *
	 * */
	public function actionDoneOrder()
	{ // done-order
		Yii::$app->response->format = Response::FORMAT_JSON;

		$inboundForm = new ScanInboundForm();
		$inboundForm->setScenario(ScanInboundForm::SCENARIO_CLOSE_ORDER);

		if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
			$inboundOrderService = new InboundScanningService($inboundForm->getDTO());
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

	/**
	*
	* */
    public function actionPrintUnallocatedList()
    { // print-unallocated-list
        $id = Yii::$app->request->get('inbound_id');

        $stockService = new \common\ecommerce\defacto\stock\service\Service();
        return $this->render('print/print-unallocated-box-pdf',['items'=>$stockService->boxWithoutPlaceAddress($id)]);
    }

	//
	public function actionTest()
	{ // test
		$inboundID = 14;
		$inboundOrderService = new InboundScanningService([]);
		$inboundOrderService->test($inboundID);
	}
}