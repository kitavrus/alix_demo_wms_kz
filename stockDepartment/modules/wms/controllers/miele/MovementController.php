<?php

namespace app\modules\wms\controllers\miele;

use stockDepartment\modules\wms\models\miele\service\ServiceMovementReservation as ServiceReservation;
use stockDepartment\components\Controller;
use stockDepartment\modules\wms\managers\miele\MovementSyncService;
use stockDepartment\modules\wms\models\miele\form\MovementForm;
use stockDepartment\modules\wms\models\miele\service\ServiceMovement;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\VarDumper;
use yii\web\Response;

class MovementController extends Controller
{
    public function actionIndex()
    { // wms/miele/movement/index
        $movementForm = new MovementForm();

        return $this->render('index', [
            'movementForm' => $movementForm,
        ]);
    }

    public function actionPickList()
    { // /wms/miele/outbound/pick-list
        $service = new ServiceMovement();
        return $this->render('pick-list',[
            'dataProvider'=>$service->getOrdersForPrintPickList()
        ]);
    }

    public function actionPrintPickList($id)
    { // /wms/miele/outbound/print-pick-list
        $service = new ServiceMovement();

        $movementInfo = $service->getOrderInfo($id);
        ServiceReservation::run($movementInfo);

        $movementSync = new MovementSyncService();
        $movementSync->setOurStatusInWorking($movementInfo->order->client_order_id);

        return $this->render('print/pick-list-pdf',[
            'movementInfo'=>$movementInfo,
            'stockIDs'=>$service->getStockIDsByMovementID($movementInfo->order->id),
        ]);
    }

    public function actionScanningForm()
    {  // /wms/miele/outbound/scanning-form
        $form = new MovementForm();
        return $this->render('scanning-form',['model'=>$form]);
    }

    /*
    * Scanning form handler Is Employee Barcode
    * DONE
    * */
    public function actionEmployeeHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scanForm = new MovementForm();
        $scanForm->scenario = 'onEmployeeHandler';

        $errors = [];
        if (!($scanForm->load(Yii::$app->request->post()) && $scanForm->validate())) {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }
    //T
    public function actionPickListHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scanForm = new MovementForm();
        $scanForm->scenario = 'onPickListHandler';
        $errors = [];
        if (!($scanForm->load(Yii::$app->request->post()) && $scanForm->validate())) {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }
    /*
   * Scanning form handler Is Product Barcode
   * */
    public function actionProductHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scanForm = new MovementForm();
        $scanForm->scenario = 'onProductHandler';

        $errors = [];
        if (!($scanForm->load(Yii::$app->request->post()) && $scanForm->validate())) {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }
    /*
    * Scanning form handler Is Fub Barcode
    * */
    public function actionFubHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scanForm = new MovementForm();
        $scanForm->scenario = 'onFubHandler';

        $errors = [];
        if (!($scanForm->load(Yii::$app->request->post()) && $scanForm->validate())) {
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
    public function actionBoxHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scanForm = new MovementForm();
        $scanForm->scenario = 'onToBoxHandler';

        $errors = [];
        if (!($scanForm->load(Yii::$app->request->post()) && $scanForm->validate())) {
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
    public function actionAddressHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scanForm = new MovementForm();
        $scanForm->scenario = 'onToAddressHandler';

        $errors = [];
        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service  = new ServiceMovement();
            $service->moveToAddress($scanForm->getDTO());
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }
    /*
    * Print the list of differences
    * */
    public function actionPrintDiffList()
    { // print-diff
        $scanForm = new MovementForm();
        $scanForm->setScenario('onPrintDiffList');

        if($scanForm->load(Yii::$app->request->get()) && $scanForm->validate()) {
            $movementInfo = $scanForm->getDTO();
            $service = new ServiceMovement();

//            VarDumper::dump($service->getOrderItemsForDiffReport($movementInfo),10,true);
//            die();
            return $this->render('print/diff-list-pdf',['diffItems'=>$service->getOrderItemsForDiffReport($movementInfo),'outboundInfo'=>$movementInfo]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = ActiveForm::validate($scanForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];

    }
}