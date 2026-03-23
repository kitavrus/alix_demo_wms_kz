<?php

namespace app\modules\wms\controllers\colins;

use common\api\DeFactoSoapAPI;
use common\components\BarcodeManager;
use common\components\MailManager;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundUploadItemsLog;
use common\modules\outbound\models\OutboundUploadLog;
use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use stockDepartment\modules\outbound\models\BeginEndPickListForm;
use stockDepartment\modules\outbound\models\DeFactoAPIOutboundForm;
use stockDepartment\modules\outbound\models\OutboundOrderSearch;
use stockDepartment\modules\outbound\models\OutboundPickingListSearch;
use stockDepartment\modules\wms\models\ScanningForm;
use Yii;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use stockDepartment\modules\outbound\models\OutboundPickListForm;
use stockDepartment\modules\product\models\ProductSearch;
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

//use stockDepartment\assets\OutboundAsset;

class OutboundCommonController extends Controller
{
    /*
     * Select order for print pick list
     *
     * */
    public function actionIndex()
    {
        $clientsArray = Client::getActiveItems();
        return $this->render('index', [
            'clientsArray' => $clientsArray
        ]);

    }

    /*
     * Get order items by parent order id
     *
     * */
    public function actionGetSubOrderGrid()
    {
        $searchModel = new OutboundOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $type = Yii::$app->request->get('type');

        $viewFileName = '_download-file-sub-order-grid';
        if($type == '1') {
            $viewFileName = 'print/_print-picking-list-sub-order-grid';
        }

        return $this->renderAjax($viewFileName, [
//        return $this->renderAjax('_sub-order-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    /*
    * Print pick list
    *
    * */
    public function actionPrintPickList()
    {
        $idsData = Yii::$app->request->get('ids');

        $ids = [];
        if (!empty($idsData)) {
            $ids = explode(',', $idsData);
        }

        $items = [];
        if (is_array($ids)) {
            $items = OutboundOrder::find()->where(['id' => $ids])->asArray()->all();
        }

        if($this->printType=='html'){
            Yii::$app->layout = 'print-html';
            return $this->render('print/_print-pick-list-html', ['items' => $items]);
        }

        return $this->render('print/_print-pick-list-pdf', ['items' => $items]);
    }

    /*
    * Begin and End picking process
    * */
    public function actionBeginEndPickingHandler()
    {
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
                $model->addError('beginendpicklistform-picking_list_barcode', Yii::t('outbound/errors', 'Вы указали пеправильный сборочный лист'));
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

                $messagesSuccess[] = Yii::t('outbound/messages', 'Можете начинать сборку');
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

                $messagesSuccess[] = Yii::t('outbound/messages', 'Сборка успешно закончена');
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

        return $this->renderAjax('begin-end-picking-list-form', ['model' => $model]);
    }

    /*
     * Begin and End picking process
     * */
    public function actionBeginEndPickingHandler_OLD_NOT_USED()
    {
        $model = new BeginEndPickListForm();

        if ($model->load(Yii::$app->request->post())) {

            $messagesInfo = '';
            $messagesSuccess = '';
            $picking_list_barcode = $model->picking_list_barcode;
            $picking_list_id = $model->picking_list_id;
            $employee_barcode = $model->employee_barcode;
            $employee_id = $model->employee_id;
            $status = '';


            if ($oplModel = OutboundPickingLists::find()->where('barcode = :barcode OR id = :id', [':barcode' => $model->barcode_process, ':id' => $picking_list_id])->one()) {
                $picking_list_barcode = $oplModel->barcode;
                $picking_list_id = $oplModel->id;
                $status = $oplModel->status;
            }

            if ($employeeModel = Employees::find()->where('barcode = :barcode OR id = :id', [':barcode' => $model->barcode_process, ':id' => $employee_id])->one()) {
                $employee_barcode = $employeeModel->barcode;
                $employee_id = $employeeModel->id;
            }

            //ERRORS
            if (empty($model->barcode_process)) {
                $model->addError('beginendpicklistform-barcode_process', Yii::t('outbound/errors', 'Пожалуйста укажите штрих-код сборочного листа или сотрудника'));
            }

            if (empty($oplModel) && empty($employeeModel)) {
                $model->addError('beginendpicklistform-barcode_process', Yii::t('outbound/errors', 'Вы ввели несуществующий штрих-код сборочного листа или сотрудника'));
            }

            if (!empty($employeeModel) &&
                !empty($oplModel) &&
                $status == OutboundPickingLists::STATUS_BEGIN &&
                $oplModel->employee_id != $employee_id

            ) {
                $model->addError('beginendpicklistform-barcode_process', Yii::t('outbound/errors', 'Этот сборочный лист собирает другой работник склада'));
            }

            if (!empty($oplModel) && $status == OutboundPickingLists::STATUS_END
            ) {
                $model->addError('beginendpicklistform-barcode_process', Yii::t('outbound/errors', 'Этот сборочный лист уже собран'));
            }

            //Если еще не начали собирать заказы
            if ($status == OutboundPickingLists::STATUS_PRINT && empty($employeeModel)) {
                $messagesInfo[] = Yii::t('outbound/messages', 'Пожалуйста введите ваш штрих-код работника');
            }

            // Если заказ уже собирается
            if ($status == OutboundPickingLists::STATUS_BEGIN && empty($employeeModel)) {
                $messagesInfo[] = Yii::t('outbound/messages', 'Если Вы уже собрали заказ введите ваш штрих код');
            }

            //
            if (!empty($employeeModel) && empty($oplModel)) {
                $messagesInfo[] = Yii::t('outbound/messages', 'Пожалуйста введите ваш штрих-код сборочного листа');
            }

            $errors = $model->getErrors();


            if (empty($errors) && !empty($employeeModel) && !empty($oplModel) && $status == OutboundPickingLists::STATUS_PRINT) {
                $oplModel->status = OutboundPickingLists::STATUS_BEGIN;
                $oplModel->employee_id = $employeeModel->id;
                $oplModel->begin_datetime = time();
                $oplModel->save(false);

                //S: TODO сделать это через события
                OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKING],['id'=>$oplModel->outbound_order_id]);
                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKING],['outbound_order_id'=>$oplModel->outbound_order_id]);
                // E:
                Stock::updateAll(["status" => Stock::STATUS_OUTBOUND_PICKING], ['outbound_picking_list_id' => $oplModel->id]);


                $messagesSuccess[] = Yii::t('outbound/messages', 'Можете начинать сборку');
            }

            if (empty($errors) && !empty($employeeModel) && !empty($oplModel) && $status == OutboundPickingLists::STATUS_BEGIN) {
                $oplModel->status = OutboundPickingLists::STATUS_END;
                $oplModel->employee_id = $employeeModel->id;
                $oplModel->end_datetime = time();
                $oplModel->save(false);

                //S: TODO сделать это через события
                OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKED],['id'=>$oplModel->outbound_order_id]);
                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKED],['outbound_order_id'=>$oplModel->outbound_order_id]);
                // E:

                Stock::updateAll(["status" => Stock::STATUS_OUTBOUND_PICKED], ['outbound_picking_list_id' => $oplModel->id]);

                $messagesSuccess[] = Yii::t('outbound/messages', 'Сборка успешно закончена');
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => (empty($errors) ? '1' : '0'),
                'errors' => $errors,
                'messagesInfo' => $messagesInfo,
                'messagesSuccess' => $messagesSuccess,
                'picking_list_barcode' => $picking_list_barcode,
                'picking_list_id' => $picking_list_id,
                'employee_barcode' => $employee_barcode,
                'employee_id' => $employee_id,
            ];
        }

        return $this->renderAjax('begin-end-picking-list-form', ['model' => $model]);
    }

    /*
     * Show grid orders for select picking list
     *
     * */
    public function actionSelectAndPrintPickingList()
    {
        $outboundForm = new OutboundPickListForm();
        $outboundForm->client_id = Client::CLIENT_COLINS;
        $clientsArray = Client::getActiveItems();

        $searchModel = new OutboundOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderAjax('_select-and-print-pick-list', [
            'outboundForm' => $outboundForm,
            'clientsArray' => $clientsArray,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    /*
     * Get array parent order number array
     * @return JSON
     * */
    public function actionGetParentOrderNumber()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $client_id = $offset = Yii::$app->request->post('client_id');

        $data = ['' => ''];
//        $data += OutboundOrder::getParentOrderNumberByClientId($client_id);
        $data += OutboundOrder::getActiveParentOrderNumberByClientId($client_id);
        return [
            'dataOptions' => $data
        ];

    }

    /*
     * Return show picking list grid
     *
     * */
    public function actionPickingListGrid()
    {
        $searchModel = new OutboundPickingListSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('picking-list-grid', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

    /*
    * Begin scanning form
    *
    * */
    public function actionScanningForm()
    {
        return $this->renderAjax('scanning-form', ['model' => new ScanningForm()]);
    }


    /*
    * Scanning form handler Is Employee Barcode
    * */
    public function actionEmployeeBarcodeScanningHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';

        $model = new ScanningForm();
        $model->scenario = 'IsEmployeeBarcode';

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
    public function actionPickingListBarcodeScanningHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $plIds = '';
        $messages = '';
        $stockArrayByPL = [];
        $orderData = [];

        $model = new ScanningForm();
        $model->scenario = 'IsPickingListBarcode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $plIds = $model->picking_list_barcode_scanned;

            $qIDs = [];
            if ($opl = OutboundPickingLists::findOne(['barcode' => $model->picking_list_barcode, 'status' => OutboundPickingLists::STATUS_END])) {

                //S: TODO подумать как это сделать правильно
                $plIds = (empty($model->picking_list_barcode_scanned) ? '' : $model->picking_list_barcode_scanned . ',') . $opl->id;
                $plIds = OutboundPickingLists::prepareIDsHelper($plIds, true);
                $qIDs = OutboundPickingLists::prepareIDsHelper($plIds);
                //E: TODO подумать как это сделать правильно
            }

            $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);

            $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'plIds' => $plIds,
            'exp_qty' => isset($orderData['allocated_qty']) ? $orderData['allocated_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
     * Scanning form handler Is Box Barcode
     * */
    public function actionBoxBarcodeScanningHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';
        $countInBox = 0;
        $stockArrayByPL = [];

        $model = new ScanningForm();
        $model->scenario = 'IsBoxBarcode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);
//            $countInBox = OutboundPickingLists::getCountInBoxByPickingList($model->box_barcode, $qIDs);
            $countInBox = OutboundPickingLists::getCountInBoxByOutboundOrder($model->box_barcode, $qIDs);
            $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countInBox' => $countInBox,
            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
     * Scanning form handler Is Product Barcode
     * */
    public function actionProductBarcodeScanningHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;


        $errors = [];
        $messages = '';
        $countInBox = '0';
        $stockArrayByPL = [];
        $orderData = [];

        $model = new ScanningForm();
        $model->scenario = 'IsProductBarcode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            //TODO Найти товар по ШК в products ?

            $qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);

//            $ooIDs = OutboundPickingLists::find()->select('outbound_order_id')->where(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->column();

            // TODO Добавь проверку чбо был один заказ

//            if($ooIDs = OutboundPickingLists::find()->select('outbound_order_id')->where(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->column()) {
//                if($oos = OutboundOrder::findAll($ooIDs)) {
//
//                }
//            }

//            VarDumper::dump($ooIDs,10,true);
//            die; // 16,17,19

            // Box barcode
            $dirPath = 'log/outbound/'.date('Ymd');
            BaseFileHelper::createDirectory($dirPath);

            file_put_contents($dirPath.'/actionProductBarcodeScanningHandler.log',$model->employee_barcode.' ; '.$model->box_barcode.' ; '.$model->product_barcode.' ; '.$model->picking_list_barcode_scanned.' ; '.date('Ymd-H:i:s')."\n",FILE_APPEND);
            file_put_contents($dirPath.'/actionProductBarcodeScanningHandler-full.log',date('Ymd-H:i:s')."\n"."\n",FILE_APPEND);
            file_put_contents($dirPath.'/actionProductBarcodeScanningHandler-full.log',print_r($model,true)."\n"."\n",FILE_APPEND);


            if(BarcodeManager::isM3BoxBorder($model->product_barcode)) {
                $m3BoxValue = BarcodeManager::getBoxM3($model->product_barcode);

                if(empty($m3BoxValue)) {
                    $m3BoxValue = 0.096;
                }

                Stock::updateAll([
                    'box_size_m3'=>$m3BoxValue,
                    'box_size_barcode'=>$model->product_barcode,
                ],[
                    'box_barcode'=>$model->box_barcode,
                    'outbound_picking_list_id' => $qIDs,
                    'status' => Stock::STATUS_OUTBOUND_SCANNED,
                ]);

                return [
                    'change_box'=>'ok'
                ];

            } else {

            }

            if ($stock = Stock::find()->where([
                'status' => [
                        Stock::STATUS_OUTBOUND_PICKED,
                        Stock::STATUS_OUTBOUND_SCANNING
                ],
                'product_barcode' => $model->product_barcode,
                'outbound_picking_list_id' => $qIDs
            ])->one()) {

                $stock->status = Stock::STATUS_OUTBOUND_SCANNED;
                $stock->box_barcode = $model->box_barcode;
                $stock->save(false);

                $countStockForOrderItem = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'product_barcode'=>$model->product_barcode,'outbound_picking_list_id' => $qIDs])->count();

                if ($ioi = OutboundOrderItem::find()->where(['outbound_order_id' => $stock->outbound_order_id,
                    'product_barcode' => $model->product_barcode,
                ])->one()
                ) {

                    if (intval($ioi->accepted_qty) < 1) {
                        $ioi->begin_datetime = time();
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                    }

//                    $ioi->accepted_qty += 1;
                    $ioi->accepted_qty = $countStockForOrderItem;

                    if ($ioi->accepted_qty == $ioi->expected_qty || $ioi->accepted_qty == $ioi->allocated_qty ) {
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNED;
                    }

                    $ioi->end_datetime = time();
                    $ioi->save(false);

                }

//                OutboundOrder::updateAllCounters(['accepted_qty' =>1], ['id' => $stock->outbound_order_id]);
                // TODO убрать этот говно код, по свободе сделать миграуию и все полям которые integer значение по умолчанию постать 0
               $oModel = OutboundOrder::findOne($stock->outbound_order_id);

                $countStockForOrder = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'outbound_order_id'=>$stock->outbound_order_id])->count();

                if(intval($oModel->accepted_qty) < 1) {
                    $oModel->begin_datetime = time();
                    $oModel->status = Stock::STATUS_OUTBOUND_SCANNING;
                }

//                $oModel->accepted_qty +=1;
                $oModel->accepted_qty = $countStockForOrder;

                if ($oModel->accepted_qty == $oModel->expected_qty || $oModel->accepted_qty == $oModel->allocated_qty ) {
                    $oModel->status = Stock::STATUS_OUTBOUND_SCANNED;
                }

                $oModel->end_datetime = time();
                $oModel->save(false);

            } else {
                $model->addError('product_barcode',Yii::t('outbound/errors',' Вы отсканировали [ <b>'.$model->product_barcode.'</b> ] лишний товар в короб '.' [<b> '.$model->box_barcode.' </b>]'));
                $errors = $model->getErrors();
//                $errors = ActiveForm::validate($model);
            }

//            $countInBox = OutboundPickingLists::getCountInBoxByPickingList($model->box_barcode, OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned));
//            $countInBox = OutboundPickingLists::getCountInBoxByOutboundOrder($model->box_barcode, $ooIDs);
            $countInBox = OutboundPickingLists::getCountInBoxByOutboundOrder($model->box_barcode, $qIDs);
            $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);
            $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'change_box' => 'no',
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countInBox' => $countInBox,
            'exp_qty' => isset($orderData['allocated_qty']) ? $orderData['allocated_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
    * Delete product by barcode  in box
    * @return JSON true or errors array
    * */
    public function actionClearProductInBoxByOne()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $stockArrayByPL = [];
        $countInBox = '0';
        $orderData = [];

        $model = new ScanningForm();
        $model->scenario = 'ClearProductInBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {


            $qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);


            if ($stock = Stock::findOne(['box_barcode' => $model->box_barcode,
                'product_barcode' => $model->product_barcode,
                'outbound_picking_list_id' => $qIDs,
                'status' => Stock::STATUS_OUTBOUND_SCANNED
            ])
            ) {

//                $stock->status = Stock::STATUS_OUTBOUND_SCANNING;
                $stock->status = Stock::STATUS_OUTBOUND_PICKED;
                $stock->box_barcode = '';
                $stock->save(false);

                $countStockForOrderItem = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'product_barcode'=>$model->product_barcode,'outbound_picking_list_id' => $qIDs])->count();

                if ($ioi = OutboundOrderItem::findOne(['product_barcode' => $model->product_barcode, 'outbound_order_id' => $stock->outbound_order_id])) {

//                    $ioi->accepted_qty -= 1;
//                    if ($ioi->accepted_qty < 1) {
//                        $ioi->accepted_qty = 0;
//                    }

                    $ioi->accepted_qty = $countStockForOrderItem;

//                    $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                    $ioi->status = Stock::STATUS_OUTBOUND_PICKED;
                    $ioi->save(false);
                }


                $oo = OutboundOrder::findOne($stock->outbound_order_id);
//                $oo->accepted_qty -= 1;
//                if ($oo->accepted_qty < 1) {
//                    $oo->accepted_qty = 0;
//                }
                $countStockForOrder = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'outbound_order_id'=>$stock->outbound_order_id])->count();
                $oo->accepted_qty = $countStockForOrder;

//                $oo->status = Stock::STATUS_OUTBOUND_SCANNING;
//                $oo->status = Stock::STATUS_OUTBOUND_PICKED;
                $oo->save(false);

            }

            $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);
//            $countInBox = OutboundPickingLists::getCountInBoxByPickingList($model->box_barcode, $qIDs);
            $countInBox = OutboundPickingLists::getCountInBoxByOutboundOrder($model->box_barcode, $qIDs);
            $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countInBox' => $countInBox,
            'exp_qty' => isset($orderData['allocated_qty']) ? $orderData['allocated_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
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
        $stockArrayByPL = [];
        $orderData = [];

        $model = new ScanningForm();
        $model->scenario = 'ClearBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);

            $outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->where(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->column();

            if ($productsInBox = Stock::find()->select('count(product_barcode) as product_barcode_count, product_barcode, outbound_order_id')
                ->where([
                    'box_barcode' => $model->box_barcode,
                    'outbound_order_id' => $outboundOrderIDs,
                    'status' => Stock::STATUS_OUTBOUND_SCANNED
                ])
                ->groupBy('product_barcode')->asArray()->all()
            ) {

                foreach ($productsInBox as $item) {
                    if ($ioi = OutboundOrderItem::findOne(['product_barcode' => $item['product_barcode'], 'outbound_order_id' => $item['outbound_order_id']])) {

                        // STATUS
                        Stock::updateAll([
                            'status' => Stock::STATUS_OUTBOUND_PICKED,
                            'box_barcode' => ''
                        ],
                        [
                            'box_barcode' => $model->box_barcode,
                            'product_barcode' => $item['product_barcode'],
                            'outbound_order_id' => $item['outbound_order_id'],
                            'status' => Stock::STATUS_OUTBOUND_SCANNED
                        ]
                        );

                        $countStock = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'product_barcode'=>$item['product_barcode'],'outbound_order_id' => $item['outbound_order_id']])->count();
                        $ioi->accepted_qty = $countStock;

                        // OUTBOUND ORDER ITEM
//                        $ioi->accepted_qty -= $item['product_barcode_count'];
//                        if ($ioi->accepted_qty < 1) {
//                            $ioi->accepted_qty = 0;
//                        }
//                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                        $ioi->status = Stock::STATUS_OUTBOUND_PICKED;
                        $ioi->save(false);

                        // OUTBOUND ORDER
                        $oo = OutboundOrder::findOne($item['outbound_order_id']);

                        $countStockForOrder = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'outbound_order_id'=>$item['outbound_order_id']])->count();
                        $oo->accepted_qty = $countStockForOrder;
//                        $oo->accepted_qty -= $item['product_barcode_count'];
//                        if ($oo->accepted_qty < 1) {
//                            $oo->accepted_qty = 0;
//                        }
//                        $oo->status = Stock::STATUS_OUTBOUND_SCANNING;
                        $oo->status = Stock::STATUS_OUTBOUND_PICKED;
                        $oo->save(false);



                        $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);
                        $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);
                    }
                }
            } else {
                $model->addError('box_barcode','<b>['.$model->box_barcode.']</b> '.Yii::t('outbound/errors','Вы ввели несуществующий штрихкод короба или короб пуст'));
                $errors = $model->getErrors();
            }
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'exp_qty' => isset($orderData['allocated_qty']) ? $orderData['allocated_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
     * Print difference list
     *
     * */
    public function actionPrintingDifferencesList()
    {
        $plIDs = Yii::$app->request->get('plids');

        $qIDs = OutboundPickingLists::prepareIDsHelper($plIDs);

//        $subQueryStatusPicked = Stock::find()->select('');
// b0000041815
//        (select count(*) FROM stock as stck WHERE stck.status= "'.Stock::STATUS_OUTBOUND_PICKED.'"  AND stck.product_barcode = stock.product_barcode) as count_status_picked,
//                     (select count(*) FROM stock as stck WHERE stck.status= "'.Stock::STATUS_OUTBOUND_SORTING.'"  AND stck.product_barcode = stock.product_barcode) as count_status_sorting ,
//                     (select count(*) FROM stock as stck WHERE stck.status= "'.Stock::STATUS_OUTBOUND_SORTED.'"  AND stck.product_barcode = stock.product_barcode) as count_status_sorted,
//                     (select count(*) FROM stock as stck WHERE stck.product_barcode = stock.product_barcode AND stck.outbound_picking_list_id = stock.outbound_picking_list_id) as count_exp
//
//        $plIDs = OutboundPickingLists::prepareIDsHelper($plIDs,true);

        $subQuery = (new Query())
            ->select('count(*)')
            ->from('stock as stck')
            ->where(['stck.status'=>Stock::STATUS_OUTBOUND_SCANNED,'stck.outbound_picking_list_id'=>$qIDs])
            ->andWhere('stck.product_barcode = stock.product_barcode');

//        select count(*) FROM stock as stck WHERE stck.status= "' . Stock::STATUS_OUTBOUND_SCANNED . '" AND stck.product_barcode = stock.product_barcode AND stck.outbound_picking_list_id IN ('.$in.')


//        ->select('id, outbound_order_id, product_barcode, box_barcode, status, primary_address, secondary_address, product_model, count(*) as items,
//                     (select count(*) FROM stock as stck WHERE stck.status= "' . Stock::STATUS_OUTBOUND_SCANNED . '" AND stck.product_barcode = stock.product_barcode AND stck.outbound_picking_list_id IN ('.$in.')) as count_status_scanned
//            ')

        $items = Stock::find()
            ->select(['id', 'outbound_order_id', 'product_barcode', 'box_barcode', 'status', 'primary_address', 'secondary_address', 'product_model', 'count(*) as items','count_status_scanned'=>$subQuery])
//                    ->select('id, outbound_order_id, product_barcode, box_barcode, status, primary_address, secondary_address, product_model, count(*) as items,
//                     (select count(*) FROM stock as stck WHERE stck.status= "' . Stock::STATUS_OUTBOUND_SCANNED . '" AND stck.product_barcode = stock.product_barcode AND stck.outbound_picking_list_id = stock.outbound_picking_list_id) as count_status_scanned
//            ')
            ->where([
                'outbound_picking_list_id' => $qIDs,
                'status' => [
                    Stock::STATUS_OUTBOUND_PICKED,
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_SCANNING
                ],
            ])
            ->groupBy('product_barcode') // , box_barcode
            ->orderBy([
                'product_barcode' => SORT_DESC,
//                'box_barcode' => SORT_DESC,
                'count_status_scanned' => SORT_DESC,
            ])
            ->asArray()
            ->all();

//        VarDumper::dump($items,10,true);
//        die('-----STOP-----');
        if($this->printType=='html'){
            Yii::$app->layout = 'print-html';
            return $this->render('print/printing-differences-list-html', ['items' => $items,'plIDs'=>$qIDs]);
        }
        return $this->render('print/printing-differences-list-pdf', ['items' => $items,'plIDs'=>$qIDs]);
    }

    /*
     * Printing box label
     * @param string $plids Picking list IDs
     * */
    public function actionPrintingBoxLabel()
    {
        //11404260-37390-1
        $plIDs = Yii::$app->request->get('plids');

        $qIDs = OutboundPickingLists::prepareIDsHelper($plIDs);

        $outboundOrderIDs = OutboundPickingLists::find()
            ->select('outbound_order_id')
            ->where(['id'=>$qIDs])
            ->groupBy('outbound_order_id')->asArray()->column();

        $items = Stock::find()
            ->select('id,outbound_order_id, box_barcode, box_size_m3')
            ->where([
//                'outbound_picking_list_id' => $qIDs,
                'outbound_order_id' => $outboundOrderIDs,
                'status' => [
//                    17,
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
            ])
            ->groupBy('box_barcode')
            ->asArray()
            ->all();

//        VarDumper::dump($items,10,true);
//        die('---STOP--');
        $model = '';
        $outboundOrderModel = '';
        if (isset($items[0]['outbound_order_id'])) {
            $outboundOrderModel = OutboundOrder::findOne($items[0]['outbound_order_id']);
            if ($dpo = TlDeliveryProposalOrders::findOne(['order_id' => $items[0]['outbound_order_id']])) {
                $model = TlDeliveryProposal::findOne($dpo->tl_delivery_proposal_id);

//                if($oo = OutboundOrder::findOne($items[0]['outbound_order_id'])){
//                    $oo->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
                $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL;
                $outboundOrderModel->save(false);
//                }
//                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL],'id = :id AND accepted_qty > 0',[':id'=>$items[0]['outbound_order_id']]);
                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL],'id = :id AND accepted_qty > 0',[':id'=>$items[0]['outbound_order_id']]);
//                Stock::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL],'outbound_order_id = :outbound_order_id AND status = :status',[':status'=>Stock::STATUS_OUTBOUND_SCANNED,':outbound_order_id'=>$items[0]['outbound_order_id']]);
                Stock::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL],'outbound_order_id = :outbound_order_id AND status = :status',[':status'=>Stock::STATUS_OUTBOUND_SCANNED,':outbound_order_id'=>$items[0]['outbound_order_id']]);
            }

            OutboundPickingLists::updateAll(['status'=>OutboundPickingLists::STATUS_PRINT_BOX_LABEL],['id'=>$qIDs]);

            //S: Проверяем все ли сборочные листа распечатаны
            if(!OutboundPickingLists::find()->where('outbound_order_id = :outbound_order_id AND status != :status',[
                ':outbound_order_id'=>$outboundOrderModel->id,
                ':status'=>OutboundPickingLists::STATUS_PRINT_BOX_LABEL
            ])->exists()) {
                $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
                $outboundOrderModel->packing_date = DateHelper::getTimestamp();
                $outboundOrderModel->save(false);
            }

            //S: если накладная для DeFacto. отправляем отчет по отгруженным товарам
//            if($outboundOrderModel->client_id == Client::CLIENT_DEFACTO && YII_ENV == 'prod') { // id = 2 Дефакто
//
//                $rows = [];
//                if ($itemsAPI = $outboundOrderModel->getOrderItems()->all()) {
//                    foreach ($itemsAPI as $k => $itemAPi) {
//                        if($itemAPi->accepted_qty >= 1) {
//                            $rows[] = [
//                                'RezerveId'=>$outboundOrderModel->order_number,
//                                'Barkod'=>$itemAPi->product_barcode,
//                                'Miktar'=>$itemAPi->accepted_qty,
//                                'IrsaliyeNo'=>$outboundOrderModel->order_number,
//                                'KoliId'=>$k + 1,
//                                'KoliDesi'=>'25',
//                            ];
//                        }
//                    }
//                }
//                    die("-----01");

//                if(!empty($rows)) {
//                    $api = new DeFactoSoapAPI();
//                    $apiData = [];
//                    if($apiResponse = $api->confirmOutboundOrder($rows)) {
//                        if (empty($apiResponse['errors'])) {
//                            $apiData = $apiResponse['response'];
//                        }
//                    }
//                    $extraFields = [];
//                    if(!empty($outboundOrderModel->extra_fields)) {
//                        $extraFields = Json::decode($outboundOrderModel->extra_fields);
//                    }
//                    $extraFields ['requestToAPI'] = $rows;
//                    $extraFields ['RezerveDagitimResult'] = $apiData;
//
//                    $outboundOrderModel->extra_fields = Json::encode($extraFields);
//                    $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
//                    $outboundOrderModel->save(false);
//                }
//            }
            //E: если накладная для DeFacto. отправляем отчет по отгруженным товарам

            //S: Проверяем все ли сборочные листа распечатаны
            ConsignmentOutboundOrder::checkAndSetStatusComplete($outboundOrderModel->consignment_outbound_order_id);
            //E: Проверяем все ли сборочные листа распечатаны

            //S: Высчитываем m3 всех коробов заказа
            $m3Sum = 0;
            foreach($items as $boxM3) {
                if(isset($boxM3['box_size_m3']) && !empty($boxM3['box_size_m3']))
                $m3Sum += $boxM3['box_size_m3'];
            }

            if($model) {
//            VarDumper::dump($modelDP,10,true);
//            die($modelDP);
                $model->mc = $m3Sum;
                $model->mc_actual = $m3Sum;
                $model->number_places_actual = count($items);
                $model->number_places = count($items);
                $model->save(false);
            }

            $outboundOrderModel->mc = $m3Sum;
            $outboundOrderModel->accepted_number_places_qty = count($items);
            $outboundOrderModel->save(false);
            if($dpOrderModel = TlDeliveryProposalOrders::findOne(['order_id'=>$outboundOrderModel->id])) {
                $dpOrderModel->number_places = count($items);
                $dpOrderModel->mc = $m3Sum;
                $dpOrderModel->mc_actual = $m3Sum;
                $dpOrderModel->save(false);
            }
            //E: Высчитываем m3 всех коробов заказа
        }

//        if($this->printType == 'html'){
//            Yii::$app->layout = 'print-html';
//            return $this->render('_box-label-html', ['boxes' => $items, 'model' => $model,'outboundOrderModel'=>$outboundOrderModel]);
//        }
        return $this->render('print/_box-label-pdf', ['boxes' => $items, 'model' => $model,'outboundOrderModel'=>$outboundOrderModel]);
    }



    /*
    * Set status complete
    * @param $id Order
    * @return JSON
    * */
    public function actionComplete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->get('id');

        if ($model = OutboundOrder::findOne($id)) {
            $model->status = Stock::STATUS_OUTBOUND_COMPLETE;
            $model->save(false);

            ConsignmentOutboundOrder::checkAndSetStatusComplete($model->consignment_outbound_order_id);
        }

        return  ['ok'];
    }


    /*
     * Operation report
     *
     * */
    public function actionOperationReport()
    {
        $searchModel = new OutboundOrderGridSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        $noFindDataProvider = null;
        $noReservedDataProvider = null;

        if(!empty($searchModel->order_number) || !empty($searchModel->parent_order_number)) {
           $q =  OutboundOrder::find();
           $q->andFilterWhere([
               'id' => $searchModel->id,
               'client_id' => $searchModel->client_id,
               'parent_order_number' => $searchModel->parent_order_number,
               'order_number' => $searchModel->order_number,
               'status' => $searchModel->status,
           ]);
            $q->select('id');

            $ids = $q->column();

          $noReservedQuery = OutboundOrderItem::find()->where('expected_qty != allocated_qty')->andWhere(['outbound_order_id'=>$ids]);
          $noReservedDataProvider = new ActiveDataProvider([
                'query' => $noReservedQuery,
                'pagination' => [
                    'pageSize' => 300,
                ],
                'sort'=> ['defaultOrder' => ['outbound_order_id'=>SORT_DESC]]
            ]);

          $noFindQuery =  OutboundOrderItem::find()->where('accepted_qty != allocated_qty')->andWhere(['outbound_order_id'=>$ids]);
          $noFindDataProvider = new ActiveDataProvider([
                'query' => $noFindQuery,
                'pagination' => [
                    'pageSize' => 300,
                ],
                'sort'=> ['defaultOrder' => ['outbound_order_id'=>SORT_DESC]]
            ]);
        }


//        $allocated_qty =

        return $this->render('operation-report-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'noFindDataProvider' => $noFindDataProvider,
            'noReservedDataProvider' => $noReservedDataProvider,
        ]);
    }

    /*
     * Printing box content
     * @param string $box_barcode
     * */
    public function actionPrintingBoxContent()
    {
        // $items = [];
        $client_id = Client::CLIENT_COLINS;
        $box_barcode = Yii::$app->request->get('box_barcode');
        $outboundOrderId = Yii::$app->request->get('order');
        $opl = OutboundPickingLists::findOne($outboundOrderId);
        $toPoint = '';
        //$orderNumber ='';

        $stockItems = Stock::find()
            ->select('id, product_barcode, count(product_barcode) as product_qty')
            ->where([
                'client_id' => $client_id,
                'status' => [
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
                'box_barcode' => $box_barcode,
            ])
            ->groupBy('product_barcode')
            ->asArray()
            ->all();
        if($opl){
            if($outboundOrder = OutboundOrder::findOne($opl->outbound_order_id)){
                if($point = $outboundOrder->toPoint){
                    $toPoint = $point->getPointTitleByPattern('stock');
                }

                //$orderNumber = $outboundOrder->order_number;
            }
        }



        if($this->printType == 'html'){
            Yii::$app->layout = 'print-html';
            return $this->render('print/_box-content-html', [
                'items' => $stockItems,
                'box_barcode' => $box_barcode,
                'toPoint' => $toPoint,
                'clientID' => $client_id,
            ]);
        }
        return $this->render('print/_box-content-pdf', [
            'items' => $stockItems,
            'box_barcode' => $box_barcode,
            'toPoint' => $toPoint,
            'clientID' => $client_id,
        ]);
    }

}




