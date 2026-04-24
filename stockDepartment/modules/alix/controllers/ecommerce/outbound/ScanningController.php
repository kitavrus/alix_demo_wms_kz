<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound;

use stockDepartment\components\Controller;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\OutboundForm;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\OutboundScanningService ;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

class ScanningController extends Controller
{
    public function actionIndex()
    {
        return $this->redirect('scanning-form');
    }

    //
    public function actionScanningForm()
    {
        $form = new OutboundForm();
        return $this->render('scanning-form',['model'=>$form]);
    }

    /**
    * Scanning form handler Is Employee Barcode
    * */
    public function actionEmployeeBarcodeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';

        $model = new OutboundForm();
        $model->setScenario(OutboundForm::SCENARIO_EMPLOYEE_BARCODE);

        if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }
	/**
	* Scanning form handler Is Picking List Barcode
	* */
    public function actionPickListBarcodeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $scanForm = new OutboundForm();
        $scanForm->setScenario(OutboundForm::SCENARIO_PICK_LIST_BARCODE);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();
            $service = new OutboundScanningService($dto);
            $orderInfo = $service->getOrderInfo($dto->order->id);
            return [
                'success'=>'Y',
                'expected_qty'=> intval($orderInfo->order->allocated_qty),
                'accepted_qty'=> intval($orderInfo->order->accepted_qty),
            ];
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
        ];
    }

    /**
    * Штрих код  пакета
    * */
    public function actionPackageBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $scanForm = new OutboundForm();
        $scanForm->setScenario(OutboundForm::SCENARIO_PACKAGE_BARCODE);

        $packageInfo = [];
        $packageInfo['qtyProductInPackage'] = 0;

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();
            $service = new OutboundScanningService($dto);
            $packageInfo = $service->packageBarcodeInfo($dto->pickListBarcode,$dto->packageBarcode);
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'qtyProductInPackage' => $packageInfo['qtyProductInPackage'],
        ];
    }

    /**
     * Scanning form handler Is Product Barcode
     * */
    public function actionProductBarcodeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $expectedQty = 0;
        $acceptedQty = 0;
        $packageInfo = [];
        $packageInfo['qtyProductInPackage'] = 0;
        $stockId = -1;

        $scanForm = new OutboundForm();
        $scanForm->setScenario(OutboundForm::SCENARIO_PRODUCT_BARCODE);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();
            $service = new OutboundScanningService();
            $stock  = $service->makeScanned($dto);

            $orderInfo = $service->getOrderInfo($dto->order->id);
            $expectedQty = intval($orderInfo->order->allocated_qty);
            $acceptedQty = intval($orderInfo->order->accepted_qty);
            $packageInfo = $service->packageBarcodeInfo($dto->pickListBarcode,$dto->packageBarcode);
			$stockId = $stock->id;
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'expected_qty'=> $expectedQty,
            'accepted_qty'=> $acceptedQty,
            'qtyProductInPackage' => $packageInfo['qtyProductInPackage'],
            'stockId' => $stockId,
        ];
    }

    /*
     * Scanning form handler Is Product  qr code
     * */
    public function actionProductQrcodeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $expectedQty = 0;
        $acceptedQty = 0;
        $packageInfo = [];
        $packageInfo['qtyProductInPackage'] = 0;

        $scanForm = new OutboundForm();
        $scanForm->setScenario(OutboundForm::SCENARIO_PRODUCT_QR_CODE);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();
			//VarDumper::dump($dto,10,true);
			//die;
            $service = new OutboundScanningService();
            $service->makeScannedQRCode($dto);

            $orderInfo = $service->getOrderInfo($dto->order->id);
			//$api = new \common\ecommerce\defacto\outbound\service\OutboundAPIService();

           // VarDumper::dump($api->makeGetCargoLabelRequest($orderInfo),10,true);
//            VarDumper::dump($orderInfo,10,true);
//            die;
            $expectedQty = intval($orderInfo->order->allocated_qty);
            $acceptedQty = intval($orderInfo->order->accepted_qty);
            $packageInfo = $service->packageBarcodeInfo($dto->pickListBarcode,$dto->packageBarcode);
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'expected_qty'=> $expectedQty,
            'accepted_qty'=> $acceptedQty,
            'qtyProductInPackage' => $packageInfo['qtyProductInPackage'],
        ];
    }

	/*
	* Clear all product in box
	* @param string $box_barcode Box barcode
	* */
    public function actionEmptyPackage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $expectedQty = 0;
        $acceptedQty = 0;
        $packageInfo = [];
        $packageInfo['qtyProductInPackage'] = 0;

        $scanForm = new OutboundForm();
        $scanForm->setScenario(OutboundForm::SCENARIO_EMPTY_PACKAGE);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();
            $service = new OutboundScanningService();
            $service->emptyPackage($dto);

            $orderInfo = $service->getOrderInfo($dto->order->id);
            $expectedQty = intval($orderInfo->order->allocated_qty);
            $acceptedQty = intval($orderInfo->order->accepted_qty);
            $packageInfo = $service->packageBarcodeInfo($dto->pickListBarcode,$dto->packageBarcode);

        } else {
            $errors = ActiveForm::validate($scanForm);
        }
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'expected_qty'=> $expectedQty,
            'accepted_qty'=> $acceptedQty,
            'qtyProductInPackage' => $packageInfo['qtyProductInPackage'],
        ];
    }

	public function actionShowPickingListItems()
	{ // show-picking-list-items
		Yii::$app->response->format = Response::FORMAT_JSON;

		$scanOutboundForm = new OutboundForm();
		$scanOutboundForm->setScenario(OutboundForm::SCENARIO_SHOW_PICKING_LIST_ITEMS);

		if ($scanOutboundForm->load(Yii::$app->request->post()) && $scanOutboundForm->validate()) {
			$outboundOrderService = new OutboundScanningService();
			return [
				'success' => 'Y',
				'items' => $this->renderPartial('_scanning-picking-items', ['items' => $outboundOrderService->showOrderItems($scanOutboundForm->pick_list_barcode)]),
			];
		}

		$errors = ActiveForm::validate($scanOutboundForm);
		return [
			'success' => (empty($errors) ? 'Y' : 'N'),
			'errors' => $errors
		];
	}

	public function actionPackage($orderNumber)
    { // /ecommerce/intermode/outbound/scanning/package?id=430
		$service = new OutboundScanningService();
		$isError = $service->package($orderNumber);
		if($isError) {
			Yii::$app->session->setFlash('danger', "Статус отправлен с ошибкой");
			return $this->redirect('/alix/ecommerce/outbound/scanning/scanning-form');
		}

		Yii::$app->session->setFlash('info', "Накладная успешно упакована");

		// Для Kaspi-заказов сразу дёргаем ASSEMBLE + открываем PDF накладной Kaspi
		// в новой вкладке. Kaspi-заказы — те, у которых заполнен external_order_number
		// (проставляется OrderImportService при импорте из cron/kaspi-poll-orders).
		$outbound = \common\ecommerce\entities\EcommerceOutbound::find()
			->andWhere(['order_number' => $orderNumber, 'deleted' => 0])
			->one();
		// Waybill у Kaspi запрашиваем только для Kaspi-доставки. Для own-delivery /
		// самовывоза (is_kaspi_delivery=0) ASSEMBLE не нужен и Kaspi вернёт 400.
		if ($outbound !== null
			&& !empty($outbound->external_order_number)
			&& (int) $outbound->is_kaspi_delivery === 1
		) {
			// Остаёмся на scanning-form, но триггерим скачивание Kaspi-накладной
			// через скрытый iframe (см. view). Кладовщик не теряет контекст формы.
			return $this->redirect([
				'/alix/ecommerce/outbound/scanning/scanning-form',
				'download' => $orderNumber,
			]);
		}

		return $this->redirect('/alix/ecommerce/outbound/scanning/scanning-form');
    }

	/**
	 * Запросить накладную у Kaspi (transfer-to-courier → ASSEMBLE) и стримить PDF
	 * клиенту на печать. Вызывается автоматом после actionPackage для Kaspi-заказов.
	 *
	 * В мок-режиме (useMock=true) KaspiMockFactory отдаёт фиктивный PDF.
	 *
	 * /alix/ecommerce/outbound/scanning/kaspi-label?orderNumber=KASPI-ODk2ODg0NjEw
	 */
	public function actionKaspiLabel($orderNumber)
	{
		$outbound = \common\ecommerce\entities\EcommerceOutbound::find()
			->andWhere(['order_number' => $orderNumber, 'deleted' => 0])
			->one();
		if ($outbound === null || empty($outbound->external_order_number)) {
			Yii::$app->session->setFlash('danger', 'Kaspi order id не найден для ' . $orderNumber);
			return $this->redirect('/alix/ecommerce/outbound/scanning/scanning-form');
		}

		$kaspiOrderId = (string) $outbound->external_order_number;

		$kaspiModule = Yii::$app->getModule('kaspi');
		if ($kaspiModule === null) {
			Yii::$app->session->setFlash('danger', 'Kaspi-модуль не сконфигурирован');
			return $this->redirect('/alix/ecommerce/outbound/scanning/scanning-form');
		}
		$kaspiService = $kaspiModule->get('kaspiService');

		// numberOfSpace — количество уникальных box_barcode в этом outbound.
		// Хардкодить 1 нельзя: крупные заказы могут паковаться в несколько коробок.
		$numberOfSpace = (int) \common\ecommerce\entities\EcommerceStock::find()
			->andWhere(['outbound_id' => (int) $outbound->id, 'deleted' => 0])
			->andWhere(['not', ['box_barcode' => null]])
			->andWhere(['!=', 'box_barcode', ''])
			->select('box_barcode')
			->distinct()
			->count();
		if ($numberOfSpace < 1) {
			$numberOfSpace = 1;
		}

		try {
			$kaspiService->transferToCourier($kaspiOrderId, ['numberOfSpace' => $numberOfSpace]);
			$label = $kaspiService->getOrderLabel($kaspiOrderId);
		} catch (\Throwable $e) {
			Yii::error('Kaspi label fetch failed for ' . $kaspiOrderId . ': ' . $e->getMessage(), __METHOD__);
			Yii::$app->session->setFlash('danger', 'Kaspi: ' . $e->getMessage());
			return $this->redirect('/alix/ecommerce/outbound/scanning/scanning-form');
		}

		if (!is_array($label) || empty($label['body'])) {
			Yii::$app->session->setFlash('danger', 'Kaspi вернул пустую накладную');
			return $this->redirect('/alix/ecommerce/outbound/scanning/scanning-form');
		}

		$mime = isset($label['mime']) ? (string) $label['mime'] : 'application/pdf';
		$fileName = 'kaspi-label-' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $kaspiOrderId) . '.pdf';

		// inline => false: Content-Disposition: attachment — браузер скачает файл,
		// а не отрендерит его в текущей/соседней вкладке.
		return Yii::$app->response->sendContentAsFile(
			(string) $label['body'],
			$fileName,
			['mimeType' => $mime, 'inline' => false]
		);
	}

//    public function actionPrintBoxLabel()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        $errors = [];
//        $orderId = 0;
//        $pathToCargoLabelFile = '';
//        $pathToWaybillFile = '';
//        $scanForm = new OutboundForm();
////        $scanForm->scenario = 'onPrintBoxLabel';
//        $scanForm->setScenario(OutboundForm::SCENARIO_PRINT_BOX_LABEL);
//
//        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
//            $dto = $scanForm->getDTO();
//            $service = new OutboundScanningService($dto);
//            $resultPathToDocs = $service->printBoxLabel($dto);
//            $orderId = $dto->order->id;
//
//            $pathToCargoLabelFile = \yii\helpers\Url::to(['/ecommerce/defacto/outbound/print-cargo-label','id'=>$orderId]);
//            $pathToWaybillFile = \yii\helpers\Url::to(['/ecommerce/defacto/outbound/print-waybill','id'=>$orderId]);
//
////            $pathToCargoLabelFile = $resultPathToDocs['pathToCargoLabelFile'];
////            $pathToWaybillFile = $resultPathToDocs['pathToWaybillFile'];
//
//        } else {
//            $errors = ActiveForm::validate($scanForm);
//        }
//
//        return [
//            'success' => (empty($errors) ? 'Y' : 'N'),
//            'errors' => $errors,
//            'orderId' => $orderId,
//            'pathToCargoLabelFile' => $pathToCargoLabelFile,
//            'pathToWaybillFile' => $pathToWaybillFile,
//        ];
//    }
    //

//    public function actionPrintCargoLabel($id)
//    { // /ecommerce/defacto/outbound/print-cargo-label?id=430
//        $service = new OutboundService();
//        $orderInfo = $service->getOrderInfo($id);
//        return Yii::$app->response->sendFile(Yii::getAlias('@webroot/'.$orderInfo->order->path_to_cargo_label_file));
//    }

//    public function actionPrintWaybill($id)
//    { // /ecommerce/defacto/outbound/print-waybill?id=66114
//        $service = new OutboundService();
//        //$orderInfo = $service->getOrderInfo($id);
//		$path_to_order_doc = $service->saveWaybillDocument($id);
//        return Yii::$app->response->sendFile(Yii::getAlias('@webroot/'.$path_to_order_doc));
////        return Yii::$app->response->sendFile(Yii::getAlias('@webroot/'.$orderInfo->order->path_to_order_doc));
//    }

//    public function actionGetPrintWaybill($id)
//    { // /ecommerce/defacto/outbound/get-print-waybill?id=430
//        $service = new OutboundService();
//        return Yii::$app->response->sendFile(Yii::getAlias('@webroot/'.$service->saveWaybillDocument($id)));
//    }
	
//	public function actionResendGetCargoLabel($orderNumber)
//    { // /ecommerce/defacto/outbound/resend-get-cargo-label?orderNumber=430
//        $service = new OutboundService();
//        $order = $service->resendGetCargoLabel($orderNumber);
//        return Yii::$app->response->sendFile(Yii::getAlias('@webroot/'.$order->path_to_cargo_label_file));
//    }
}