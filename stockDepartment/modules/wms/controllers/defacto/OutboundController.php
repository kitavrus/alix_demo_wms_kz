<?php

namespace app\modules\wms\controllers\defacto;

use common\api\DeFactoSoapAPI;
use common\components\BarcodeManager;
use common\components\MailManager;
use common\modules\kpiSettings\models\KpiSetting;
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
use stockDepartment\modules\outbound\models\ScanningForm;
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
use common\components\DeliveryProposalManager;
use common\components\OutboundManager;

//use stockDepartment\assets\OutboundAsset;

class OutboundController extends Controller
{
    /*
     * Select order for print pick list
     *
     * */
    public function actionIndex()
    {
     $clientsArray =  Client::getActiveItems();

        return $this->render('index', ['clientsArray' => $clientsArray]);

    }

    /*
    * Get order items by parent order id
    *
    * */
    public function actionDefactoOutboundGrid()
    {
        $searchModel = new OutboundOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['client_id' => Client::CLIENT_DEFACTO]);
        $dataProvider->pagination = [
            'pageSize' => 25,
        ];
        $dataProvider->sort = [
            'defaultOrder' => [
                'id'=>SORT_DESC
            ]
        ]
        ;

        return $this->render('defacto-outbound-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
            $viewFileName = '_print-picking-list-sub-order-grid';
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
                $messagesInfo[] = Yii::t('outbound/messages', 'Please enter your employee barcode');
            }

            // Если заказ уже собирается
            if ($status == OutboundPickingLists::STATUS_BEGIN && empty($employeeModel)) {
                $messagesInfo[] = Yii::t('outbound/messages', 'If you have already collected the order enter your barcode');
            }

            //
            if (!empty($employeeModel) && empty($oplModel)) {
                $messagesInfo[] = Yii::t('outbound/messages', 'Please enter your picking list barcode');
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


                $messagesSuccess[] = Yii::t('outbound/messages', 'You can start assembly');
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

                $messagesSuccess[] = Yii::t('outbound/messages', 'Assembling successfully completed');
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
    * Scanning handler
    * TODO NOT USED
    * */
    public function actionScanningHandler_NOT_USED()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ScanningForm();

        $errors = [];
        $plIds = '';
        $messages = '';
        $step = 1;
        $stockArrayByPL = [];

        if ($model->load(Yii::$app->request->post())) {

            $step = $model->step;
            $plIds = $model->picking_list_barcode_scanned;

            switch ($step) {
                case 1:
                    $model->scenario = 'IsEmployeeBarcode';
                    break;
                case 2:
                    $model->scenario = 'IsPickingListBarcode';
                    break;
                case 3:
                    $model->scenario = 'IsBoxBarcode';
                    break;
                case 4:
                    $model->scenario = 'IsProductBarcode';
                    break;
            }

            if ($model->validate()) {

                if ($step == 2) {
                    $qIDs = [];
                    if ($opl = OutboundPickingLists::findOne(['barcode' => $model->picking_list_barcode, 'status' => OutboundPickingLists::STATUS_END])) {

                        //S: TODO подумать как это сделать правильно
                        $plIds = (empty($model->picking_list_barcode_scanned) ? '' : $model->picking_list_barcode_scanned . ',') . $opl->id;

                        if (!empty($plIds)) {
                            $plIds = trim($plIds, ',');
                            $tmp = explode(',', $plIds);
                            $qIDs = array_unique($tmp);
                            $plIds = implode(',', $qIDs);
                        }
                        //E: TODO подумать как это сделать правильно
                    }

                    $step++;
                    $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);
                }

                if ($step == 4) {
                    //TODO Найти товар по ШК в products ?
                    $qIDs = trim($model->picking_list_barcode_scanned, ',');
                    $tmp = explode(',', $qIDs);
                    $qIDs = array_unique($tmp);
                    $qIDs = implode(',', $qIDs);

                    if ($stock = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_PICKED, 'product_barcode' => $model->product_barcode, 'outbound_picking_list_id' => $qIDs])->one()) {
                        $stock->status = Stock::STATUS_OUTBOUND_PACKED;
                        $stock->save(false);
                    }
                }

            } else {

            }
        }

        // 1 - Сделать функция которая показывает количество товаров в коробе
        // 2 - Сделать проверку что товар находится в указаннах сборочных листах
        // 3 - Сделать вывод ошибок внизу как на приемке или в order process
        // 4 - Сделать вывод содержимого сборочных листов
        // 5 - сделать кнопки очистить короб и удалить товар из короба (т.е по одному)


        $errors = ActiveForm::validate($model); // $errors = $model->getErrors();

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'plIds' => $plIds,
            'step' => $step,
            'stockArrayByPL' => $this->renderPartial('_scanning-picking-items', ['items' => $stockArrayByPL]),
        ];
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
        $timer = 0;

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
            if($oo = $opl->outboundOrder){
                $timer = KpiSetting::getOutboundScanningTime($oo->client_id, $oo->expected_qty - $oo->accepted_qty);
            }


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
            'cdTimer' => $timer,
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
            $employeeID = 0;
            if($model->employee_barcode) {
                if($employeeOne = Employees::find()->andWhere(['barcode'=>$model->employee_barcode])->one()) {
                    $employeeID = $employeeOne->id;
                }
            }
            // Box barcode
            $dirPath = 'log/outbound/'.date('Ymd');
            BaseFileHelper::createDirectory($dirPath);

            file_put_contents($dirPath.'/actionProductBarcodeScanningHandler.log',$model->employee_barcode.' ; '.$model->box_barcode.' ; '.$model->product_barcode.' ; '.$model->picking_list_barcode_scanned.' ; '.date('Ymd-H:i:s')."\n",FILE_APPEND);
            file_put_contents($dirPath.'/actionProductBarcodeScanningHandler-full.log',date('Ymd-H:i:s')."\n"."\n",FILE_APPEND);
            file_put_contents($dirPath.'/actionProductBarcodeScanningHandler-full.log',print_r($model,true)."\n"."\n",FILE_APPEND);


            if(BarcodeManager::isM3BoxBorder($model->product_barcode)) {

//                $qIDsBox = OutboundPickingLists::prepareIDsHelper($plIDs);
                $oolsIDBox = array_shift($qIDs);
                $oplBox = OutboundPickingLists::findOne($oolsIDBox);


                $m3BoxValue = BarcodeManager::getBoxM3($model->product_barcode);

                if(empty($m3BoxValue)) {
                    $m3BoxValue = 0.096;
                }

                Stock::updateAll([
                    'box_size_m3'=>$m3BoxValue,
                    'box_size_barcode'=> BarcodeManager::getIntegerM3($m3BoxValue),
  //                  'box_size_barcode'=>$model->product_barcode,
                ],[
                    'box_barcode'=>$model->box_barcode,
//                    'outbound_picking_list_id' => $qIDs,
                    'outbound_order_id' => $oplBox->outbound_order_id,
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
                $stock->scan_out_employee_id = $employeeID;
                $stock->scan_out_datetime = time();
                $stock->save(false);

                //$countStockForOrderItem = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'product_barcode'=>$model->product_barcode,'outbound_picking_list_id' => $qIDs])->count();
                $countStockForOrderItemAll = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'product_barcode'=>$model->product_barcode,'outbound_order_id' => $stock->outbound_order_id])->count();

                if ($ioi = OutboundOrderItem::find()->where(['outbound_order_id' => $stock->outbound_order_id,
                    'product_barcode' => $model->product_barcode,
                ])->one()
                ) {

                    if (intval($ioi->accepted_qty) < 1) {
                        $ioi->begin_datetime = time();
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                    }

//                    $ioi->accepted_qty += 1;
//                    $ioi->accepted_qty = $countStockForOrderItem;
                    $ioi->accepted_qty = $countStockForOrderItemAll;

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

                //$countStockForOrderItem = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'product_barcode'=>$model->product_barcode,'outbound_picking_list_id' => $qIDs])->count();
                $countStockForOrderItemAll = Stock::find()->where(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'product_barcode'=>$model->product_barcode,'outbound_order_id' => $stock->outbound_order_id])->count();

                if ($ioi = OutboundOrderItem::findOne(['product_barcode' => $model->product_barcode, 'outbound_order_id' => $stock->outbound_order_id])) {

//                    $ioi->accepted_qty -= 1;
//                    if ($ioi->accepted_qty < 1) {
//                        $ioi->accepted_qty = 0;
//                    }

//                    $ioi->accepted_qty = $countStockForOrderItem;
                    $ioi->accepted_qty = $countStockForOrderItemAll;

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

            $outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->andWhere(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->column();

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

                        $countStock = Stock::find()->andWhere(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'product_barcode'=>$item['product_barcode'],'outbound_order_id' => $item['outbound_order_id']])->count();
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

                        $countStockForOrder = Stock::find()->andWhere(['status'=>Stock::STATUS_OUTBOUND_SCANNED,'outbound_order_id'=>$item['outbound_order_id']])->count();
                        $oo->accepted_qty = $countStockForOrder;
//                        $oo->accepted_qty -= $item['product_barcode_count'];
//                        if ($oo->accepted_qty < 1) {
//                            $oo->accepted_qty = 0;
//                        }
//                        $oo->status = Stock::STATUS_OUTBOUND_SCANNING;
                        $oo->status = Stock::STATUS_OUTBOUND_PICKED;
                        $oo->save(false);
//                        $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);
//                        $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);
                    }
                }

                $stockArrayByPL = OutboundPickingLists::getStockByPickingIDs($qIDs);
                $orderData = OutboundPickingLists::getAccExpByPickingListInOrder($qIDs);
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
    { // printing-box-label?plids=13758
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
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
            ])
            ->groupBy('box_barcode')
            ->orderBy('box_barcode')
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
//            if(!OutboundPickingLists::find()->where('outbound_order_id = :outbound_order_id AND status != :status',[
//                ':outbound_order_id'=>$outboundOrderModel->id,
//                ':status'=>OutboundPickingLists::STATUS_PRINT_BOX_LABEL
//            ])->exists()) {
                $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
                $outboundOrderModel->packing_date = DateHelper::getTimestamp();
                $outboundOrderModel->save(false);
//            }

            //S: если накладная для DeFacto. отправляем отчет по отгруженным товарам
            if($outboundOrderModel->client_id == Client::CLIENT_DEFACTO && YII_ENV == 'prod') { // id = 2 Дефакто

                $rows = [];
                $rows = DeFactoSoapAPI::preparedDataForOutboundConfirm($outboundOrderModel->id);
/*                if ($itemsAPI = $outboundOrderModel->getOrderItems()->all()) {
                    foreach ($itemsAPI as $k => $itemAPi) {
                        if($itemAPi->accepted_qty >= 1) {
                            $rows[] = [
                                'RezerveId'=>$outboundOrderModel->order_number,
                                'Barkod'=>$itemAPi->product_barcode,
                                'Miktar'=>$itemAPi->accepted_qty,
                                'IrsaliyeNo'=>$outboundOrderModel->order_number,
                                'KoliId'=>$k + 1, // который короб
                                'KoliDesi'=>'25', // m3
                            ];
                        }
                    }
                }*/
//                    die("-----01");

                if(!empty($rows)) {
                    $api = new DeFactoSoapAPI();
                    $apiData = [];
                    if($apiResponse = $api->confirmOutboundOrder($rows)) {
                        if (empty($apiResponse['errors'])) {
                            $apiData = $apiResponse['response'];
                        }
                    }
                    $extraFields = [];
                    if(!empty($outboundOrderModel->extra_fields)) {
                        $extraFields = Json::decode($outboundOrderModel->extra_fields);
                    }
                    $extraFields ['requestToAPI'] = $rows;
                    $extraFields ['RezerveDagitimResult'] = $apiData;

                    $outboundOrderModel->extra_status = $apiData;
                    $outboundOrderModel->extra_fields = Json::encode($extraFields);
                    $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
                    $outboundOrderModel->save(false);
                }
            }
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
            if($model) {
                $dpManager = new DeliveryProposalManager(['id' => $model->id]);
                $dpManager->onUpdateProposal();
            }
            //E: Высчитываем m3 всех коробов заказа
        }


        return $this->render('print/_box-label-pdf', ['boxes' => $items, 'model' => $model,'outboundOrderModel'=>$outboundOrderModel]);
    }

    /*
    * Printing box label
    * @param string $plids Picking list IDs
    * */
    public function actionValidatePrintingBoxLabel()
    {
        // 15869074-43056-2-1
        Yii::$app->response->format = Response::FORMAT_JSON;

        $plIDs = Yii::$app->request->post('plids');
        $runNext = "no";

        $qIDs = OutboundPickingLists::prepareIDsHelper($plIDs);

        $outboundOrderIDs = OutboundPickingLists::find()
            ->select('outbound_order_id')
            ->where(['id'=>$qIDs])
            ->groupBy('outbound_order_id')->asArray()->column();

        if($oo = OutboundOrder::find()->andWhere(['id'=>$outboundOrderIDs])->one()) {
            if($oo->client_id == 2) {
             $accepted_qty = $oo->accepted_qty;
             $allocated_qty = $oo->allocated_qty;

             $min = ($allocated_qty * 0.9);
             $min = floor($min);

             if (($accepted_qty >= $min) || ($accepted_qty == $allocated_qty)) {
                 $runNext = 'ok';
             }
            } else {
             $runNext = 'ok';
            }
        }

       return [
           'runNext'=>$runNext,
       ];
    }


    /*
    * Load Data from API for only client "DeFacto"
    * @return JSON true or errors array
    * */
    public function actionUploadFileDeFactoApi()
    {
        $model = new DeFactoAPIOutboundForm();

        $model->scenario = 'UploadFileForAPI';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->file = UploadedFile::getInstance($model, 'file');

            if ( $model->file ) {

                $dirPath = 'uploads/de-facto/outbound/' . date('Ymd') . '/' . date('His');
                BaseFileHelper::createDirectory($dirPath);
                $pathToCSVFile = $dirPath . '/' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs($pathToCSVFile);


                //S: Start test load demo data

                $row = 1;
                $arrayToSaveCSVFile = [];
                if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
//                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $row++;

                        if ($row > 2) {

                            $RezerveId = trim($data[0]); // RezerveId => order_number
                            $CariId =  trim($data[1]); // CariId => Shop id external code
                            $Barkode =  trim($data[5]); // Barkode => Barcode
                            $Miktar = intval(trim($data[6])); // Miktar  => Quantity
                            $PartiNo =  trim($data[7]); // PartiNo => Parent_number _order
                            $createdDataOnClient =  trim($data[8]); // PartiOnayTarih => date created order on client site

                            $arrayToSaveCSVFile[$PartiNo][$RezerveId]['shop_id'] = $CariId;
                            $arrayToSaveCSVFile[$PartiNo][$RezerveId]['data_created_on_client'] = DateHelper::formatDefactoDate($createdDataOnClient);

                            $arrayToSaveCSVFile[$PartiNo][$RezerveId]['items'][] = [
                                'CariId' => $CariId,
                                'Barkode' => $Barkode,
                                'Miktar' => $Miktar,
                            ];
                        }
                    }

                    fclose($handle);

                    $unique_key = time();
                    $client_id = 2; // DeFacto
                    if(!empty($arrayToSaveCSVFile) && is_array($arrayToSaveCSVFile)) {
                        foreach ($arrayToSaveCSVFile as $partyOrder => $orders) {
                            if (!empty($arrayToSaveCSVFile) && is_array($arrayToSaveCSVFile)) {
                                foreach ($orders as $orderNumber => $orderInfo) {

                                    $oul = new OutboundUploadLog();
                                    $oul->unique_key = $unique_key;
                                    $oul->client_id = $client_id;
                                    $oul->party_number = $partyOrder;
                                    $oul->order_number = $orderNumber;
                                    $oul->save(false);

                                    $oul->data_created_on_client = $orderInfo['data_created_on_client'];
                                    //S: Find
                                    if ($point = Store::find()->where(['client_id' => $oul->client_id, 'shop_code' => $orderInfo['shop_id']])->one()) {
                                        $oul->to_point_id = $point->id;
                                    }
                                    //E: Find
                                    $oul->to_point_title = $orderInfo['shop_id'];
                                    $oul->expected_qty = 0;

                                    foreach ($orderInfo['items'] as $orderRow) {
                                        $oul->expected_qty += $orderRow['Miktar'];

                                        $ouIl = new OutboundUploadItemsLog();
                                        $ouIl->outbound_upload_id = $oul->id;
                                        $ouIl->product_barcode = $orderRow['Barkode'];
                                        $ouIl->expected_qty = $orderRow['Miktar'];
                                        $ouIl->save(false);
                                    }

                                    $oul->save(false);
                                }

                            }

                        }
                    }

                    Yii::$app->getSession()->setFlash('success', Yii::t('outbound/messages', 'File was successfully uploaded'));
                    $this->redirect(['/outbound/default/upload-log-grid','unique_key'=>$unique_key,'client_id'=>$client_id]);
                }



            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('outbound/messages', 'File upload was failed'));

                return $this->redirect('index');
            }
        } else {
//            VarDumper::dump(ActiveForm::validate($model));
        }

        return $this->renderAjax('upload-file-de-facto-api', [
            'model' => $model
        ]);
    }

    /*
     * Upload for DeFacto API after upload confirm
     *
     * */
    public function actionUploadLogGrid()
    {
        $unique_key = Yii::$app->request->get('unique_key');
        $client_id = Yii::$app->request->get('client_id');

        $query = OutboundUploadLog::find()->where(['client_id'=>$client_id,'unique_key'=>$unique_key]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>false,
            'sort'=> false,
        ]);

        return $this->render('upload-log-grid',[
            'dataProvider'=>$dataProvider,
            'unique_key'=>$unique_key,
            'client_id'=>$client_id,
        ]);

    }


    /*
     * Upload for DeFacto API after upload confirm
     *
     * */
    public function actionUploadedOrderSaveToDb()
    {
        $errors = [];
        $messages = [];

        $unique_key = Yii::$app->request->post('unique-key');
        $client_id = Yii::$app->request->post('client-id');

        $arrayToSaveCSVFile = OutboundUploadLog::find()->where(['client_id' => $client_id, 'unique_key' => $unique_key])->asArray()->all();
        $first = 1;
        if ($arrayToSaveCSVFile) {
            foreach ($arrayToSaveCSVFile as $oolItem) {

                if($first) {
                    if ($outboundModelIDs = OutboundOrder::find()->select('id')->where(['client_id' => $client_id, 'parent_order_number' => $oolItem['party_number']])->column()) {

                        // TODO Доделать !!!!!
                        //S: Reset
                        OutboundOrder::updateAll(['data_created_on_client' => '', 'expected_qty' => '0', 'accepted_qty' => '0', 'allocated_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_NEW], ['id' => $outboundModelIDs]);
                        ConsignmentOutboundOrder::updateAll(['expected_qty' => '0', 'accepted_qty' => '0', 'allocated_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_NEW], ['client_id' => $client_id, 'party_number' => $oolItem['party_number']]);
                        OutboundOrderItem::updateAll(['expected_qty' => '0', 'accepted_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_NEW], ['outbound_order_id' => $outboundModelIDs]);
                        OutboundPickingLists::deleteAll(['outbound_order_id' => $outboundModelIDs]);
                        Stock::updateAll([
                            'box_barcode' => '',
                            'outbound_order_id' => '0',
                            'outbound_picking_list_id' => '0',
                            'outbound_picking_list_barcode' => '',
                            'status' => Stock::STATUS_NOT_SET,
                            'status_availability' => Stock::STATUS_AVAILABILITY_YES
                        ], ['outbound_order_id' => $outboundModelIDs]);
                        //E: Reset
                    }
                    $first = 0;
                }
                $oManager = new OutboundManager();
                $oManager->initBaseData($client_id, $oolItem['party_number'], $oolItem['order_number']);
                $coo = $oManager->createUpdateConsignmentOutbound();
                $data = [
                    'consignment_outbound_order_id' => $coo->id,
                    'parent_order_number' => $coo->party_number,
                    'order_number' => $oolItem['order_number'],
                    'from_point_id' => Store::NOMADEX_MAIN_WAREHOUSE,
                    'to_point_id' => $oolItem['to_point_id'],
                    'to_point_title' => $oolItem['to_point_title'],
                    'data_created_on_client' => $oolItem['data_created_on_client'],
                ];

                $oo = $oManager->createUpdateOutbound($data);

//                if (!($consignmentModel = ConsignmentOutboundOrder::findOne(['client_id' => $client_id, 'party_number' => $oolItem['party_number']]))) {
//                    $consignmentModel = new ConsignmentOutboundOrder();
//
//                    $consignmentModel->client_id = $client_id;
//                    $consignmentModel->party_number = $oolItem['party_number'];
//                    $consignmentModel->status = Stock::STATUS_OUTBOUND_NEW;
//                    $consignmentModel->save(false);
//                }

//                if (!($outboundModel = OutboundOrder::findOne(['client_id' => $client_id, 'parent_order_number' => $oolItem['party_number'], 'order_number' => $oolItem['order_number']]))) {
//                    $outboundModel = new OutboundOrder();
//                }
//
//                $outboundModel->status = Stock::STATUS_OUTBOUND_NEW;
//                $outboundModel->client_id = $client_id;
//                $outboundModel->consignment_outbound_order_id = $consignmentModel->id;
//                $outboundModel->parent_order_number = $oolItem['party_number'];
//                $outboundModel->order_number = $oolItem['order_number'];
//                $outboundModel->data_created_on_client = $oolItem['data_created_on_client'];
//                $outboundModel->save(false);
//
//                $outboundModel->to_point_id = $oolItem['to_point_id'];
//                $outboundModel->to_point_title = $oolItem['to_point_title'];
                //E: Find


                $items = OutboundUploadItemsLog::find()->where(['outbound_upload_id' => $oolItem['id']])->asArray()->all();
                $oManager->addItems($items);
                $oManager->createUpdateDeliveryProposalAndOrder();

//                if ($items) {
//                    foreach ($items as $line) {
//                        if (!($ooiModel = OutboundOrderItem::findOne(['outbound_order_id' => $outboundModel->id, 'product_barcode' => $line['product_barcode'], 'expected_qty' => [$line['expected_qty'], '0']]))) {
//                            $ooiModel = new OutboundOrderItem();
//                        }
//
//                        $ooiModel->status = Stock::STATUS_OUTBOUND_NEW;
//                        $ooiModel->outbound_order_id = $outboundModel->id;
//                        $ooiModel->product_barcode = $line['product_barcode'];
//                        $ooiModel->expected_qty = $line['expected_qty'];
//                        $ooiModel->save(false);
//
//                        $expected_qty += $ooiModel->expected_qty;
//                    }
//                }
//
//                $outboundModel->expected_qty = $expected_qty;
//                $outboundModel->save(false);
//
//                //
//                $consignmentModel->expected_qty += $expected_qty;
//                $consignmentModel->save(false);


//                $dpOrderNumber = $outboundModel->parent_order_number . ' ' . $outboundModel->order_number;

//                if ($dpOrder = TlDeliveryProposalOrders::findOne(['client_id' => $client_id, 'order_id' => $outboundModel->id, 'order_number' => $dpOrderNumber])) {
//                    $dp = TlDeliveryProposal::findOne($dpOrder->tl_delivery_proposal_id);
//                } else {
//                    $dp = new TlDeliveryProposal();
//                    $dpOrder = new TlDeliveryProposalOrders();
//                }
//
//                $dp->status = TlDeliveryProposal::STATUS_NEW;
//                $dp->client_id = $outboundModel->client_id;
//                $dp->route_from = '4'; // НАШ склад
//                $dp->route_to = $outboundModel->to_point_id;
//                $dp->cash_no = TlDeliveryProposal::METHOD_CHAR;
//                $dp->save(false);
//
//                // Добавить заказы
//                $dpOrder->client_id = $dp->client_id;
//                $dpOrder->tl_delivery_proposal_id = $dp->id;
//                $dpOrder->order_id = $outboundModel->id;
//                $dpOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
//                $dpOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
//                $dpOrder->order_number = $outboundModel->parent_order_number . ' ' . $outboundModel->order_number;
//                $dpOrder->save(false);
//
            }

            // Reservation on stock
            if (isset ($oManager)) {
                $oManager->reservationOnStockByPartyNumber();
            }

//            if ($oos = OutboundOrder::find()->select('id')->where(['parent_order_number' => $outboundModel->parent_order_number])->asArray()->all()) {
//                foreach ($oos as $order) {
//                    Stock::AllocateByOutboundOrderId($order['id']);
//                }
//            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;


        return [
            ''=>'ok',
        ];
    }



    // START
    //////////////////////////////////////////////////////////
    /////////////////////// Confirm From Api//////////////////
    //////////////////////////////////////////////////////////


    /*
     * Confirm Data from API for only client "DeFacto"
     * @return JSON true or errors array
     * */
    public function actionDownloadFileDeFactoApi()
    {
        $model = new DeFactoAPIOutboundForm();

        return $this->renderAjax('download-file-de-facto-api', [
            'model' => $model,
            'clientsArray' => Client::getActiveItems(),
        ]);
    }

    /*
     * Download outbound file for import to DeFacto API
     *
     * */
    public function actionDownloadFileForDeFactoApi()
    {
        $idsData = Yii::$app->request->get('ids');

        $ids = [];
        if (!empty($idsData)) {
            $ids = explode(',', $idsData);
        }

        $outboundModels = [];
        if (is_array($ids)) {
            $outboundModels = OutboundOrder::find()->where(['id' => $ids])->all();
        }

        if($outboundModels) {

            $dirPath = 'uploads/de-facto/outbound/download/'.date('Ymd').'/'.date('His');
            BaseFileHelper::createDirectory($dirPath);

            $rows[] = [
                'RezerveId', // +
                'Barkod', // +
                'Miktar', // +
                'IrsaliyeNo', // +
                'KoliID', // +
                'KoliDesi', // +
            ];

            foreach ($outboundModels as $outbound) {
                if ($items = $outbound->getOrderItems()->all()) {
//                if ($items = OutboundOrderItem::findAll(['outbound_order_id' => $outbound->id])) {
                    foreach ($items as $k => $item) {
                        if($item->accepted_qty >= 1) {
                            $rows[] = [
                                $outbound->order_number,
                                $item->product_barcode,
                                $item->accepted_qty,
                                $outbound->order_number,
                                $k + 1,
                                '25',
                            ];
                        }

                    }
                }
                $outbound->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
                $outbound->save(false);
            }

            $fileName = 'outbound-file-download-for-api-'.time().'.csv';

            $fp = fopen($dirPath.'/'.$fileName, 'w');

            foreach ($rows as $fields) {
                fputcsv($fp, $fields,';');
            }

            fclose($fp);

            return Yii::$app->response->sendFile($dirPath.'/'.$fileName);

        }


        return $this->render('/outbound/default/confirm-from-api');
    }

    /*
     * Download outbound file for import to DeFacto API
     *
     * */
    public function actionDownloadOutboundOrderForApi()
    {
        $id = Yii::$app->request->get('id');
        $outboundModel = OutboundOrder::findOne($id);
        if ($outboundModel) {
            $dirPath = 'uploads/de-facto/outbound/download/' . date('Ymd') . '/' . date('His');
            BaseFileHelper::createDirectory($dirPath);

            $rows[] = [
                'RezerveId', // +
                'Barkod', // +
                'Miktar', // +
                'IrsaliyeNo', // +
                'KoliID', // +
                'KoliDesi', // +
            ];
            if ($items = $outboundModel->getOrderItems()->all()) {
                foreach ($items as $k => $item) {
                    if ($item->accepted_qty >= 1) {
                        $rows[] = [
                            $outboundModel->order_number,
                            $item->product_barcode,
                            $item->accepted_qty,
                            $outboundModel->order_number,
                            $k + 1,
                            '25',
                        ];
                    }

                }
            }
            $outboundModel->save(false);


            $fileName = 'outbound-file-download-for-api-' . time() . '.csv';

            $fp = fopen($dirPath . '/' . $fileName, 'w');

            foreach ($rows as $fields) {
                fputcsv($fp, $fields, ';');
            }

            fclose($fp);

            return Yii::$app->response->sendFile($dirPath . '/' . $fileName);

        }
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
     * Get parent order number in process by client
     * @return JSON  dataOptions ['id'=>'parent order number title']
     * */
//    public function actionGetParentOrderNumberInProcess()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        $client_id = Yii::$app->request->post('client_id');
//
//        $data = ['' => ''];
//        $data += OutboundOrder::getParentOrderNumberByClientId($client_id);
//        return [
//            'dataOptions' => $data
//        ];
//    }
    /*
     * Actions
     *
     * */
    public function actions()
    {
        return [
            'get-parent-order-number-in-process'=>[ //TODO NOT USED ONLY FOR EXAMPLE
                'class'=>'app\modules\outbound\controllers\defaultActions\GetParentOrderNumberInProcessAction',
            ]
        ];
    }


    // END
    //////////////////////////////////////////////////////////
    /////////////////////// Confirm From Api//////////////////
    //////////////////////////////////////////////////////////

    public function actionResendApi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->get('id');
        $orders = [$id];
        $str = 'RezerveId;Barkod;Miktar;IrsaliyeNo;KoliId;KoliDesi;KoliKargoEtiketId;' . "\n";

        foreach($orders as $outID) {
            if($outboundOrderModel = OutboundOrder::findOne($outID)) {
                $data = DeFactoSoapAPI::preparedDataForOutboundConfirm($outID);

                foreach ($data as $row) {
                    $str .= '"' . $row['RezerveId'] . '";"' . $row['Barkod'] . '";"' . $row['Miktar'] . '";"' . $row['IrsaliyeNo'] . '";"' . $row['KoliId'] . '";"' . $row['KoliDesi'] . '";"' . $row['KoliKargoEtiketId'] . '";' . "\n";
                }

                $rows = $data;
                if (!empty($rows) && 1) {
                    $api = new DeFactoSoapAPI();
                    $apiData = [];
                    if ($apiResponse = $api->confirmOutboundOrder($rows)) {
                        if (empty($apiResponse['errors'])) {
                            $apiData = $apiResponse['response'];
                        }
                    }
                    $extraFields = [];
                    if (!empty($outboundOrderModel->extra_fields)) {
                        $extraFields = Json::decode($outboundOrderModel->extra_fields);
                    }
                    $extraFields ['requestToAPI'] = $rows;
                    $extraFields ['RezerveDagitimResult'] = $apiData;

                    $outboundOrderModel->extra_status = $apiData;
                    $outboundOrderModel->extra_fields = Json::encode($extraFields);
                    $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
                    $outboundOrderModel->save(false);
                }

                $dirPath = 'api/de-facto/all/'.date('Ymd');
                BaseFileHelper::createDirectory($dirPath);
                file_put_contents($dirPath.'/resend-by-api-' . date('Ymd_H-i-s') . '-outbound-order.csv', $str, FILE_APPEND);
            }
        }

        return ['ok'];
    }

    /*
     *
     * */
    public function actionSaveBoxKg()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ScanningForm();
        $model->scenario = 'sSaveBoxKg';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $qIDs = OutboundPickingLists::prepareIDsHelper($model->picking_list_barcode_scanned);

            $outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->where(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->column();

            Stock::updateAll([
                'box_kg'=>$model->box_kg,
            ],[
                'box_barcode'=>$model->box_barcode,
                'outbound_order_id' => $outboundOrderIDs,
                'status' => Stock::STATUS_OUTBOUND_SCANNED,
            ]);

            return [
                'success' => '1',
                'plids' => $model->picking_list_barcode_scanned,
            ];
        }

        return [
            'success'=>'0',
            'errors' => ActiveForm::validate($model)
        ];
    }

    public function actionValidatePrintBoxKgList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ScanningForm();
        $model->scenario = 'sPrintBoxKgList';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            return [
                'success' => '1',
                'plids' => $model->picking_list_barcode_scanned,
            ];
        }

        return [
            'success'=>'0',
            'errors' => ActiveForm::validate($model)
        ];
    }

    /*
    *
    * */
    public function actionPrintBoxKgList()
    {
        $plIDs = Yii::$app->request->get('plids');
        if ($plIDs) {

            $qIDs = OutboundPickingLists::prepareIDsHelper($plIDs);
            $oolsID = array_shift($qIDs);
            $opl = OutboundPickingLists::findOne($oolsID);

            $stockItems = Stock::find()
                ->select('id, box_barcode, box_kg, box_size_barcode')
                ->where([
                    'outbound_order_id' => $opl->outbound_order_id,
                    'status' => [
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ],
                ])
                ->andWhere('box_size_barcode != ""')
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->asArray()
                ->all();

            $toPoint = '';
            $orderNumberTitle = '';
            if($outboundOrder = OutboundOrder::findOne($opl->outbound_order_id)) {
                $orderNumberTitle = $outboundOrder->parent_order_number.' '.$outboundOrder->order_number;
                if($point = $outboundOrder->toPoint){
                    $toPoint = $point->getPointTitleByPattern('stock');
                }
            }


            return $this->render('print\_box-kg-list-pdf', ['stockItems' => $stockItems,'toPoint'=>$toPoint,'orderNumberTitle'=>$orderNumberTitle]);
        }

        Yii::$app->session->setFlash('danger', 'Необходимо указать штрикод листа сборки и сотрудника');
        return $this->redirect('index');
    }

}

//- 1 - В систему загружается по апи приходы. АТЭ указываю ПАРЕНТ ИД полученный по почте
//- 2 - Происходит автоматическая резервация товаров на складе
//> 3 - АТЭ печат листы на сборку, выбирая заказы из списка (по парента ид )  и отдают ребятам
//- 4 - Ребята на отдельной странице сканирую свой ШК и шк листа на сборку и в этот момент начинается время отсчета сборки
//- 5 - Собрав весь лист сборки. Ребята идут на специальную страинцу и сканируют новай свой ШК и шк листа на сборку. фиксирыем это время
//- 6 - Начинается сортировка по магазинам.
//?  6.1 - Как это будет происходить? Выбирается магазин->короб->товар
// TRUNCATE TABLE `outbound_orders`
// TRUNCATE TABLE `outbound_order_items`



