<?php

namespace app\modules\wms\controllers\erenRetail;

use common\components\BarcodeManager;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use stockDepartment\modules\outbound\models\BeginEndPickListForm;
use stockDepartment\modules\outbound\models\OutboundOrderSearch;
use stockDepartment\modules\outbound\models\OutboundPickingListSearch;
use stockDepartment\modules\wms\managers\erenRetail\outbound_data_matrix\OutboundDataMatrixForm;
use stockDepartment\modules\wms\managers\erenRetail\outbound_data_matrix\OutboundDataMatrixService;
use Yii;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use stockDepartment\modules\outbound\models\OutboundPickListForm;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use common\helpers\DateHelper;


class OutboundDataMatrixController extends Controller
{
/*
* Begin scanning form
* */
	public function actionScanningForm()
	{
		return $this->render('scanning-form', ['model' => new OutboundDataMatrixForm()]);
	}

	/*
 * Scanning form handler Is Box Barcode
 * */
	public function actionBoxBarcodeScanning()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';
		$orderNumber = '';
		$countProductInBox = '0';
		$expCount = '0';
		$scanCount = '0';
		$expCountBox = '0';
		$scanCountBox = '0';

		$model = new OutboundDataMatrixForm();
		$model->scenario = 'IsBoxBarcode';

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$service = new OutboundDataMatrixService();
			$info = $service->getOrderInfoByBoxBarcode($model->box_barcode);
			$orderNumber = $info->order_number;
			$countProductInBox = $info->countProductInBox;
			$expCount = $info->expCount;
			$scanCount = $info->scanCount;
			$expCountBox = $info->expCountBox;
			$scanCountBox = $info->scanCountBox;
		} else {
			$errors = ActiveForm::validate($model);
		}

		return [
			'success' => (empty($errors) ? '1' : '0'),
			'errors' => $errors,
			'messages' => $messages,
			'orderNumber' => $orderNumber,
			'countProductInBox' => $countProductInBox,
			'expCount' => $expCount,
			'scanCount' => $scanCount,
			'expCountBox' => $expCountBox,
			'scanCountBox' => $scanCountBox,
		];
	}

    /*
     * */
    public function actionProductBarcodeScanning()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = [];
        $messages = '';

        $model = new OutboundDataMatrixForm();
        $model->scenario = 'IsProductBarcode';
		$post = Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'change_box' => 'no',
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }

    /*
     * */
    public function actionProductDatamatrixScanning()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';
		$expCount = '0';
		$scanCount = '0';
		$expCountBox = '0';
		$scanCountBox = '0';

        $model = new OutboundDataMatrixForm();
        $model->scenario = 'IsProductDatamatrix';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$service = new OutboundDataMatrixService();
			$service->setProductDataMatrix($model->box_barcode,$model->product_barcode,$model->product_datamatrix);
			$info = $service->getOrderInfoByBoxBarcode($model->box_barcode);
			$expCount = $info->expCount;
			$scanCount = $info->scanCount;
			$expCountBox = $info->expCountBox;
			$scanCountBox = $info->scanCountBox;
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
			'expCount' => $expCount,
			'scanCount' => $scanCount,
			'expCountBox' => $expCountBox,
			'scanCountBox' => $scanCountBox,
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
        $messages = [];
		$expCount = '0';
		$scanCount = '0';
		$expCountBox = '0';
		$scanCountBox = '0';

        $model = new OutboundDataMatrixForm();
        $model->scenario = 'ClearBox';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$service = new OutboundDataMatrixService();
			$service->clearBox($model->box_barcode);
			$info = $service->getOrderInfoByBoxBarcode($model->box_barcode);
			$expCount = $info->expCount;
			$scanCount = $info->scanCount;
			$expCountBox = $info->expCountBox;
			$scanCountBox = $info->scanCountBox;
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
			'expCount' => $expCount,
			'scanCount' => $scanCount,
			'expCountBox' => $expCountBox,
			'scanCountBox' => $scanCountBox,
        ];
    }
}
