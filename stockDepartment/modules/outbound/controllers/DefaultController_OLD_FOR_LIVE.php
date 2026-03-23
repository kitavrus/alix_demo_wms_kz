<?php

namespace app\modules\outbound\controllers;

use common\components\BarcodeManager;
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
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use common\helpers\DateHelper;

//use stockDepartment\assets\OutboundAsset;

class DefaultController extends Controller
{
    /*
     * Select order for print pick list
     *
     * */
    public function actionIndex()
    {
//       Stock::updateAll([
//                      'outbound_order_id' => 0,
//                      'status' => Stock::STATUS_NOT_SET,
//                      'status_availability' => Stock::STATUS_AVAILABILITY_YES,
//                      'outbound_picking_list_id' => 0,
//                      'outbound_picking_list_barcode' => '',
//                      'box_barcode' => '',
//       ]);

//echo "<br />";
//echo "<br />";
//echo "<br />";
//echo "<br />";
//echo "<br />";
//       if($oos =  OutboundOrder::find()->select('id')->where(['parent_order_number'=>'35700'])->asArray()->all()) {
//           foreach($oos as $order) {
//               echo $order['id']."<br />";
//               Stock::AllocateByOutboundOrderId($order['id']);
//           }
//       }
//        die('STOP');

        return $this->render('index', []);

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

        return $this->render('_print-pick-list-pdf', ['items' => $items]);
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
                OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKING],['id'=>$oplModel->outbound_order_id]);
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
                OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PICKED],['id'=>$oplModel->outbound_order_id]);
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
            if ($stock = Stock::find()->where(['status' => [Stock::STATUS_OUTBOUND_PICKED,Stock::STATUS_OUTBOUND_SCANNING], 'product_barcode' => $model->product_barcode, 'outbound_picking_list_id' => $qIDs])->one())
            {
//            if ($stock = Stock::find()->where(['status' => [Stock::STATUS_OUTBOUND_PICKED,Stock::STATUS_OUTBOUND_PICKING,Stock::STATUS_OUTBOUND_SCANNING], 'product_barcode' => $model->product_barcode, 'outbound_order_id' => $ooIDs])->one()) {

                $stock->status = Stock::STATUS_OUTBOUND_SCANNED;
                $stock->box_barcode = $model->box_barcode;
                $stock->save(false);

                if ($ioi = OutboundOrderItem::find()->where(['outbound_order_id' => $stock->outbound_order_id,
                    'product_barcode' => $model->product_barcode,
                ])->one()
                ) {

                    if (intval($ioi->accepted_qty) < 1) {
                        $ioi->begin_datetime = time();
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                    }

                    $ioi->end_datetime = time();
                    $ioi->accepted_qty += 1;

                    if ($ioi->accepted_qty == $ioi->expected_qty || $ioi->accepted_qty == $ioi->allocated_qty ) {
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNED;
                    }

                    $ioi->save(false);

                }

//                OutboundOrder::updateAllCounters(['accepted_qty' =>1], ['id' => $stock->outbound_order_id]);
                // TODO убрать этот говно код, по свободе сделать миграуию и все полям которые integer значение по умолчанию постать 0
               $oModel = OutboundOrder::findOne($stock->outbound_order_id);

               $oModel->accepted_qty +=1;

                if(intval($oModel->accepted_qty) <= 1) {
                    $oModel->begin_datetime = time();
                    $oModel->status = Stock::STATUS_OUTBOUND_SCANNING;
                }

                if ($oModel->accepted_qty == $oModel->expected_qty || $oModel->accepted_qty == $oModel->allocated_qty ) {
                    $oModel->status = Stock::STATUS_OUTBOUND_SCANNED;
                }

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

                $stock->status = Stock::STATUS_OUTBOUND_SCANNING;
                $stock->box_barcode = '';
                $stock->save(false);

                if ($ioi = OutboundOrderItem::findOne(['product_barcode' => $model->product_barcode, 'outbound_order_id' => $stock->outbound_order_id])) {

                    $ioi->accepted_qty -= 1;

                    if ($ioi->accepted_qty < 1) {
                        $ioi->accepted_qty = 0;
                    }

                    $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                    $ioi->save(false);
                }


                $oo = OutboundOrder::findOne($stock->outbound_order_id);
                $oo->accepted_qty -= 1;

                if ($oo->accepted_qty < 1) {
                    $oo->accepted_qty = 0;

                }

                $oo->status = Stock::STATUS_OUTBOUND_SCANNING;

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

            if ($productsInBox = Stock::find()->select('count(product_barcode) as product_barcode_count, product_barcode, outbound_order_id')->where(['box_barcode' => $model->box_barcode, 'outbound_order_id' => $outboundOrderIDs,'status' => Stock::STATUS_OUTBOUND_SCANNED])->groupBy('product_barcode')->asArray()->all()) {
//            if ($productsInBox = Stock::find()->select('count(product_barcode) as product_barcode_count, product_barcode, outbound_order_id')->where(['box_barcode' => $model->box_barcode, 'outbound_picking_list_id' => $qIDs,'status' => Stock::STATUS_OUTBOUND_SCANNED])->groupBy('product_barcode')->asArray()->all()) {

                foreach ($productsInBox as $item) {

                    if ($ioi = OutboundOrderItem::findOne(['product_barcode' => $item['product_barcode'], 'outbound_order_id' => $item['outbound_order_id']])) {
                        // OUTBOUND ORDER ITEM
                        $ioi->accepted_qty -= $item['product_barcode_count'];
                        if ($ioi->accepted_qty < 1) {
                            $ioi->accepted_qty = 0;
                        }
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                        $ioi->save(false);

                        // OUTBOUND ORDER
                        $oo = OutboundOrder::findOne($item['outbound_order_id']);
                        $oo->accepted_qty -= $item['product_barcode_count'];
                        if ($oo->accepted_qty < 1) {
                            $oo->accepted_qty = 0;
                        }
                        $oo->status = Stock::STATUS_OUTBOUND_SCANNING;
                        $oo->save(false);

                        // STATUS
                        Stock::updateAll([
                            'status' => Stock::STATUS_OUTBOUND_SCANNING,
                            'box_barcode' => ''
                            ],
                            ['box_barcode' => $model->box_barcode,
                             'outbound_order_id' => $item['outbound_order_id'],
                             'status' => Stock::STATUS_OUTBOUND_SCANNED
                            ]
                        );

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

        return $this->render('printing-differences-list-pdf', ['items' => $items,'plIDs'=>$qIDs]);
    }

    /*
     * Printing box label
     * @param string $plids Picking list IDs
     * */
    public function actionPrintingBoxLabel()
    {

        $plIDs = Yii::$app->request->get('plids');

        $qIDs = OutboundPickingLists::prepareIDsHelper($plIDs);

        $outboundOrderIDs = OutboundPickingLists::find()->select('outbound_order_id')->where(['id'=>$qIDs])->groupBy('outbound_order_id')->asArray()->column();

        $items = Stock::find()
            ->select('id,outbound_order_id, box_barcode')
            ->where([
//                'outbound_picking_list_id' => $qIDs,
                'outbound_order_id' => $outboundOrderIDs,
                'status' => [
//                    17,
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
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

               // OutboundOrder::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL],['id'=>$items[0]['outbound_order_id']]);
                if($oo = OutboundOrder::findOne($items[0]['outbound_order_id'])){
                    $oo->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
                    $oo->save(false);
                }
                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL],'id = :id AND accepted_qty > 0',[':id'=>$items[0]['outbound_order_id']]);
                Stock::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL],'outbound_order_id = :outbound_order_id AND status = :status',[':status'=>Stock::STATUS_OUTBOUND_SCANNED,':outbound_order_id'=>$items[0]['outbound_order_id']]);
            }

            OutboundPickingLists::updateAll(['status'=>OutboundPickingLists::STATUS_PRINT_BOX_LABEL],['id'=>$qIDs]);
        }


        return $this->render('_box-label-pdf', ['boxes' => $items, 'model' => $model,'outboundOrderModel'=>$outboundOrderModel]);
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

                    \yii\helpers\VarDumper::dump($arrayToSaveCSVFile, 10, true);
                    die;

//                    die('-STOP-');

                    $client_id = 2; // DeFacto
                    foreach ($arrayToSaveCSVFile as $k1 => $v1) {

                        if($outboundModelIDs = OutboundOrder::find()->select('id')->where(['client_id'=>$client_id,'parent_order_number'=>$k1])->column()) {

                            // TODO Доделать !!!!!
                            //S: Reset

                            OutboundOrder::updateAll(['data_created_on_client'=>'','expected_qty'=>'0','accepted_qty'=>'0','allocated_qty'=>'0','status'=>Stock::STATUS_OUTBOUND_NEW],['id'=>$outboundModelIDs]);

                            ConsignmentOutboundOrder::updateAll(['expected_qty'=>'0','accepted_qty'=>'0','allocated_qty'=>'0','status'=>Stock::STATUS_OUTBOUND_NEW],['client_id'=>$client_id,'party_number'=>$k1]);

                            OutboundOrderItem::updateAll(['expected_qty'=>'0','accepted_qty'=>'0','status'=>Stock::STATUS_OUTBOUND_NEW],['outbound_order_id'=>$outboundModelIDs]);

                            OutboundPickingLists::deleteAll(['outbound_order_id'=>$outboundModelIDs]);

                            Stock::updateAll([
                                'box_barcode'=>'',
                                'outbound_order_id'=>'0',
                                'outbound_picking_list_id'=>'0',
                                'outbound_picking_list_barcode'=>'',
                                'status'=> Stock::STATUS_NOT_SET,
                                'status_availability' => Stock::STATUS_AVAILABILITY_YES
                            ],['outbound_order_id'=>$outboundModelIDs]);

                            //E: Reset
                        }

                        foreach ($v1 as $k2 => $v2) {

                            if( !($consignmentModel = ConsignmentOutboundOrder::findOne(['client_id'=>$client_id,'party_number'=>$k1])) ) {
                                $consignmentModel =  new ConsignmentOutboundOrder();

                                $consignmentModel->client_id = $client_id;
                                $consignmentModel->party_number = $k1;
                                $consignmentModel->status = Stock::STATUS_OUTBOUND_NEW;
                                $consignmentModel->save(false);
                            }

                            if( !($outboundModel = OutboundOrder::findOne(['client_id'=>$client_id,'parent_order_number'=>$k1,'order_number'=>$k2])) ) {
                                $outboundModel =  new OutboundOrder();
                            }
                            $outboundModel->status = Stock::STATUS_OUTBOUND_NEW;
                            $outboundModel->client_id = $client_id;
                            $outboundModel->parent_order_number = $k1;
                            $outboundModel->order_number = $k2;
                            $outboundModel->data_created_on_client = $v2['data_created_on_client'];
                            $outboundModel->save(false);

                            //S: Find
                            $toStoreID = 0;
                            if ($point = Store::find()->where(['client_id' => $outboundModel->client_id, 'shop_code' => $v2['shop_id']])->one()) {
                                $outboundModel->to_point_id = $point->id;
                                $toStoreID = $outboundModel->to_point_id;
                            }
                            $outboundModel->to_point_title = $v2['shop_id'];

                            //E: Find

                            $expected_qty = 0;

                            foreach ($v2['items'] as $k3 => $v3) {

                                if( !($ooiModel = OutboundOrderItem::findOne(['outbound_order_id'=>$outboundModel->id,'product_barcode'=>$v3['Barkode'],'expected_qty'=>[$v3['Miktar'],'0']])) ) {
                                    $ooiModel =  new OutboundOrderItem();
                                }

                                $ooiModel->status = Stock::STATUS_OUTBOUND_NEW;
                                $ooiModel->outbound_order_id = $outboundModel->id;
                                $ooiModel->product_barcode = $v3['Barkode'];
                                $ooiModel->expected_qty = $v3['Miktar'];
                                $ooiModel->save(false);
//                                echo $outboundModel->id.' = '.$v3['Barkode'].' = '.$v3['Miktar']."==<br />";

                                $expected_qty += $ooiModel->expected_qty;
                            }

                            $outboundModel->expected_qty = $expected_qty;
                            $outboundModel->save(false);

                            //
                            $consignmentModel->expected_qty += $expected_qty;
                            $consignmentModel->save(false);


                            $dpOrderNumber = $outboundModel->parent_order_number . ' ' . $outboundModel->order_number;

                            if( $dpOrder = TlDeliveryProposalOrders::findOne(['client_id'=>$client_id,'order_id'=>$outboundModel->id,'order_number'=>$dpOrderNumber]) ) {
                                $dp = TlDeliveryProposal::findOne($dpOrder->tl_delivery_proposal_id);
                            } else {
                                $dp = new TlDeliveryProposal();
                                $dpOrder = new TlDeliveryProposalOrders();
                            }

                            $dp->status = TlDeliveryProposal::STATUS_NEW;
                            $dp->client_id = $outboundModel->client_id;
                            $dp->route_from = '4'; // НАШ склад
                            $dp->route_to = $toStoreID;
                            $dp->save(false);

                            // Добавить заказы
                            $dpOrder->client_id = $dp->client_id;
                            $dpOrder->tl_delivery_proposal_id = $dp->id;
                            $dpOrder->order_id = $outboundModel->id;
                            $dpOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
                            $dpOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
                            $dpOrder->order_number = $outboundModel->parent_order_number . ' ' . $outboundModel->order_number;
                            $dpOrder->save(false);

                        }
                    }

                    fclose($handle);

                    // Reservation on stock
                    if($oos =  OutboundOrder::find()->select('id')->where(['parent_order_number'=>$outboundModel->parent_order_number])->asArray()->all()) {
                        foreach($oos as $order) {
//                            echo $order['id']."<br />";
                            Stock::AllocateByOutboundOrderId($order['id']);
                        }
                    }

                    Yii::$app->getSession()->setFlash('success', Yii::t('outbound/messages', 'Файл успешно загружен'));
//                    die('-09-09-09-09-09-09-09');
//                    return $this->refresh();
                    return $this->redirect('index');
                }

            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('outbound/messages', 'Не получилось загрузить файл'));

                return $this->redirect('index');
//                return $this->refresh();
            }
        } else {
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            VarDumper::dump(ActiveForm::validate($model));
        }

        return $this->renderAjax('upload-file-de-facto-api', [
            'model' => $model
        ]);
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
        }

        $all = OutboundOrder::find()->where(['consignment_outbound_order_id' => $model->consignment_outbound_order_id])->count();
        $complete = OutboundOrder::find()->where(['consignment_outbound_order_id' => $model->consignment_outbound_order_id, 'status' => Stock::STATUS_OUTBOUND_COMPLETE])->count();

        if( ($all == $complete) && ($consignmentModel = ConsignmentOutboundOrder::findOne(['client_id' => $model->client_id, 'party_number' => $model->parent_order_number])) ) {
            $consignmentModel->status = Stock::STATUS_OUTBOUND_COMPLETE;
            $consignmentModel->save(false);
        }

        return  [];
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



