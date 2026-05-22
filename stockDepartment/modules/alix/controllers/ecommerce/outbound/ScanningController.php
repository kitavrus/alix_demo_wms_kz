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
		$isKaspiOrder = $outbound !== null && !empty($outbound->external_order_number);
		// Waybill у Kaspi запрашиваем только для Kaspi-доставки. Для own-delivery /
		// самовывоза (is_kaspi_delivery=0) ASSEMBLE не нужен и Kaspi вернёт 400.
		if ($isKaspiOrder && (int) $outbound->is_kaspi_delivery === 1) {
			// Остаёмся на scanning-form, но триггерим скачивание Kaspi-накладной
			// через скрытый iframe (см. view). Кладовщик не теряет контекст формы.
			return $this->redirect([
				'/alix/ecommerce/outbound/scanning/scanning-form',
				'download' => $orderNumber,
			]);
		}

		// Kaspi-заказ на самовывоз: явная подсказка оператору, что Kaspi-этикетки
		// здесь не будет и заказ закрывается при выдаче покупателю (двухэтапный
		// sendCompletionCode + confirmCompletionWithCode). Без неё оператор ждёт
		// PDF, которого не появится.
		if ($isKaspiOrder && (int) $outbound->is_kaspi_delivery === 0) {
			return $this->redirect([
				'/alix/ecommerce/outbound/scanning/scanning-form',
				'info'        => 'ownpickup',
				'orderNumber' => $orderNumber,
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

		// При первом скачивании дёргаем ASSEMBLE; при повторном — только лейбл,
		// иначе Kaspi отдаст ошибку «заказ уже в ASSEMBLE».
		$alreadyFetched = !empty($outbound->kaspi_label_fetched_at);

		// PHP 5.6: \Throwable отсутствует, поэтому ловим \Exception — KaspiApiException
		// и Yii httpclient exceptions от него наследуются. Иначе исключение
		// пролетит мимо catch и кладовщик увидит yii-debug страницу прямо в iframe.
		try {
			if (!$alreadyFetched) {
				$kaspiService->transferToCourier($kaspiOrderId, ['numberOfSpace' => $numberOfSpace]);
			}
			$label = $kaspiService->getOrderLabel($kaspiOrderId);
		} catch (\Exception $e) {
			Yii::error('Kaspi label fetch failed for ' . $kaspiOrderId . ': ' . $e->getMessage(), __METHOD__);
			return $this->renderKaspiLabelError($kaspiOrderId, $e);
		}

		if (!is_array($label) || empty($label['body'])) {
			return $this->renderKaspiLabelError(
				$kaspiOrderId,
				new \RuntimeException('Kaspi вернул пустую накладную')
			);
		}

		// Этикетка получена — фиксируем, чтобы заказ ушёл из списка «ждут этикетку».
		$outbound->kaspi_label_fetched_at = time();
		$outbound->save(false, ['kaspi_label_fetched_at', 'updated_at']);

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

	/**
	 * Рендер ошибки Kaspi-этикетки внутри iframe сканирующей формы.
	 *
	 * Сама форма (`scanning-form.php`) встраивает `actionKaspiLabel` в скрытый iframe.
	 * Обычный `redirect` + flash оператор не видит — родительская страница не
	 * перезагружается. Поэтому отдаём HTML с inline-скриптом, который через
	 * `window.parent.postMessage` показывает сообщение в существующем `#error-list`
	 * родителя; внутри iframe тоже виден текст ошибки — если оператор сделает
	 * iframe видимым (debug) или его откроет CSP-fallback на новой вкладке.
	 *
	 * @param string     $kaspiOrderId
	 * @param \Exception $e
	 * @return string
	 */
	private function renderKaspiLabelError($kaspiOrderId, \Exception $e)
	{
		$httpStatus = '';
		if ($e instanceof \stockDepartment\modules\kaspi\exceptions\KaspiApiException) {
			$status = $e->getHttpStatusCode();
			if ($status !== null && $status !== '') {
				$httpStatus = ' (HTTP ' . (int) $status . ')';
			}
		}

		$displayMessage = 'Kaspi-этикетка: ' . $e->getMessage() . $httpStatus
			. ' [заказ ' . $kaspiOrderId . ']';

		Yii::$app->response->format = Response::FORMAT_HTML;
		Yii::$app->response->headers->set('Content-Type', 'text/html; charset=UTF-8');

		$safeMessage = \yii\helpers\Html::encode($displayMessage);
		$safeMessageJs = \yii\helpers\Json::htmlEncode($displayMessage);

		return <<<HTML
<!doctype html>
<html lang="ru"><head><meta charset="UTF-8"><title>Kaspi label error</title>
<style>
body{margin:0;padding:16px;font:14px/1.4 -apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;color:#a94442;background:#f2dede;border:1px solid #ebccd1;}
strong{display:block;margin-bottom:6px;}
</style></head>
<body>
<strong>Ошибка Kaspi-этикетки</strong>
<div>{$safeMessage}</div>
<script>
(function () {
    try {
        if (window.parent && window.parent !== window) {
            window.parent.postMessage({type: 'kaspi-label-error', message: {$safeMessageJs}}, '*');
        }
    } catch (e) { /* parent на другом origin — игнор, текст уже виден в iframe */ }
})();
</script>
</body></html>
HTML;
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

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['apiLogger'] = ['class' => \common\log\IncomingApiLogger::class, 'category' => 'alix.ui'];
        return $behaviors;
    }
}