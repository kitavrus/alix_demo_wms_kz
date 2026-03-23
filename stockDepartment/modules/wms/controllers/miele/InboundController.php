<?php

namespace app\modules\wms\controllers\miele;

use common\components\BarcodeManager;
use common\modules\inbound\models\InboundOrder;
use common\modules\stock\models\RackAddress;
use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\models\miele\form\InboundChangeAddressForm;
use stockDepartment\components\Controller;
use stockDepartment\modules\wms\models\miele\form\InboundForm;
use stockDepartment\modules\wms\models\miele\service\ServiceInbound;
use Yii;
use yii\db\Expression;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\bootstrap\ActiveForm;

class InboundController extends Controller
{
    public function actionIndex()
    {
        $inboundForm = new InboundForm();
        $service = new ServiceInbound();

        return $this->render('index', [
            'inboundForm' => $inboundForm,
            'newAndInProcess' => $service->getNewAndInProcessOrder(),
        ]);
    }

    public function actionChangeOrderHandler()
    { // change-order-handler
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new InboundForm();
        $inboundForm->setScenario('onChangeOrderHandler');

        if($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $service = new ServiceInbound($inboundForm->getDTO());
            $qty = $service->getQtyInOrder();
            return [
                'success'=>1,
                'expected_qty'=> intval($qty->expected_qty),
                'accepted_qty'=> intval($qty->accepted_qty),
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];

    }

    public function actionOurBoxBarcodeHandler()
    { // our-box-barcode-handler

        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new InboundForm();
        $inboundForm->setScenario('onOurBoxBarcodeHandler');

        if($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            return [
                'success' => '1',
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];
    }

    public function actionProductBarcodeHandler()
    { //product-barcode-handler

        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new InboundForm();
        $inboundForm->setScenario('onProductBarcodeHandler');

        if($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $service = new ServiceInbound($inboundForm->getDTO());
            $service->addScannedToStock();

            $qtyInBox = $service->getQtyScannedInBox();
            $qty = $service->getQtyInOrder();
            return [
                'success' => '1',
                'qtyInBox' => $qtyInBox,
                'expected_qty'=> intval($qty->expected_qty),
                'accepted_qty'=> intval($qty->accepted_qty),
                'isWaitFabBarcode' =>  $service->isWaitFabBarcode(),
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];
    }

    public function actionFabBarcodeHandler()
    { //fab-barcode-handler

        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new InboundForm();
        $inboundForm->setScenario('onFabBarcodeHandler');

        if($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $service = new ServiceInbound($inboundForm->getDTO());
            $service->addFabBarcodeToProduct();
            return [
                'success' => '1',
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];
    }

    public function actionCleanOurBoxHandler()
    { //clean-our-box-handler

        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new InboundForm();
        $inboundForm->setScenario('onCleanOurBoxHandler');

        if($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $service = new ServiceInbound($inboundForm->getDTO());
            $service->cleanBox();
            $qtyInBox = $service->getQtyScannedInBox();
            $qty = $service->getQtyInOrder();
            return [
                'success' => '1',
                'qtyInBox' => $qtyInBox,
                'expected_qty'=> intval($qty->expected_qty),
                'accepted_qty'=> intval($qty->accepted_qty),
            ];
        }

        $errors = ActiveForm::validate($inboundForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];
    }
    /*
    * Print the list of differences
    * */
    public function actionPrintDiffHandler()
    { // print-diff-handler
        $inboundForm = new InboundForm();
        $inboundForm->setScenario('onPrintDiffHandler');

        if($inboundForm->load(Yii::$app->request->get()) && $inboundForm->validate()) {
            $service = new ServiceInbound($inboundForm->getDTO());
            return $this->render('print/diff-list-pdf',['items'=>$service->getOrderItemsForDiffReport()]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = ActiveForm::validate($inboundForm);
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];

    }

    public function actionAddresses() {
        $service = new ServiceInbound();
        return $this->render('addresses',[
            'af'=>new InboundChangeAddressForm(),
            'newAndInProcess' => $service->getNewAndInProcessOrder(),
        ]);
    }

    /*
     * Move from to
     * @return JSON
     *
     * */
    public function actionMoveFromTo()
    {


        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new InboundChangeAddressForm();
        $successMessages = [];
        $success = 0;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->from = trim($model->from,'#');
            $model->to = trim($model->to,'#');


            file_put_contents('InboundChangeAddressForm-all.log',$model->type.";".$model->from.";".$model->to.";".(new \DateTime('now'))->format('Y-m-d H:i:s')."\n",FILE_APPEND);
            //S: Start
            switch($model->type) {
                case '1': // Короб на Полку
                    file_put_contents('InboundChangeAddressForm-box-on.log',$model->type.";".$model->from.";".$model->to.";".(new \DateTime('now'))->format('Y-m-d H:i:s')."\n",FILE_APPEND);
                    //From - Проверяем ШК что это короб
                    //To - Проверяем ШК что это полка
                    // TODO Попробовать сделать через DynamicModel

                    if( !BarcodeManager::isBox($model->from) && !empty($model->from) ) {
                        $model->addError('inboundchangeaddressform-from',Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }
                    //Если в коробе нет товаров
                    if(BarcodeManager::isBox($model->from)) {
                        if (BarcodeManager::isEmptyBox($model->from)) {
                            $model->addError('inboundchangeaddressform-from', Yii::t('stock/errors', 'Этот короб пуст') . ' [' . $model->from . '] ' . ' Перемещение из/в ' . ' [' . $model->from . ']  [' . $model->to . ']');
                        }
                    }

                    if( !BarcodeManager::isRegiment($model->to) && !empty($model->to)) {
                        $model->addError('inboundchangeaddressform-to',Yii::t('stock/errors','This is not shelf').' ['.$model->to.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }

                    if(!empty($model->to) && !BarcodeManager::isZone($model->to)) {
                        $model->addError('inboundchangeaddressform-to',Yii::t('stock/errors','Вы ввели адрес недоступный в зарезервированных зонах').' ['.$model->to.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }

                    $order = $model->getOrder();
                    if(!empty($model->to) && !BarcodeManager::addressInZone($model->to,$order->zone) ) {
                        $model->addError('inboundchangeaddressform-to',Yii::t('stock/errors','Неверная зона для приходной накладной').' ['.$model->to.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }

                    // Primary address  - Это Короб или палета
                    // Secondary address - Это полка или стелаж
                    if(!$model->hasErrors() && !empty($model->to) && !empty($model->from)) {
                        $address_sort_order = '';
                        if($address = RackAddress::find()->where(['address'=>$model->to])->one()) {
                            $address_sort_order = $address->sort_order;
                        }
                        Stock::updateAll(['secondary_address'=>$model->to,'address_sort_order'=>$address_sort_order],'primary_address = :pa',[':pa'=>$model->from]);
                        $successMessages[] = Yii::t('stock/messages', 'Successfully moved the box {from} shelf {to}',['from'=>$model->from,'to'=>$model->to]);
                    }

                    break;

                case '2': // Из Короба в Короб
                    file_put_contents('InboundChangeAddressForm-from-box-in-box.log',$model->type.";".$model->from.";".$model->to.";".(new \DateTime('now'))->format('Y-m-d H:i:s')."\n",FILE_APPEND);
                    // 2220003693377 700000056424
                    if( !BarcodeManager::isBox($model->from) && !empty($model->from) ) {
                        $model->addError('inboundchangeaddressform-from',Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }
                    //Если в коробе нет товаров
                    if(BarcodeManager::isBox($model->from)) {
                        if (BarcodeManager::isEmptyBox($model->from)) {
                            $model->addError('inboundchangeaddressform-from', Yii::t('stock/errors', 'Этот короб пуст') . ' [' . $model->from . '] ' . ' Перемещение из/в ' . ' [' . $model->from . ']  [' . $model->to . ']');
                        }
                    }

                    if( !BarcodeManager::isBox($model->to) && !empty($model->to) ) {
                        $model->addError('inboundchangeaddressform-to',Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }

                    // Primary address  - Это Короб или палета
                    // Secondary address - Это полка или стелаж
                    if(!$model->hasErrors() && !empty($model->to) && !empty($model->from)) {

                        Stock::updateAll(['primary_address'=>$model->to],
                            [
                                'primary_address'=>$model->from,
                                'status'=>[
                                    Stock::STATUS_INBOUND_NEW,
                                    Stock::STATUS_INBOUND_CONFIRM,
                                    Stock::STATUS_OUTBOUND_NEW,
                                    Stock::STATUS_OUTBOUND_FULL_RESERVED,
                                    Stock::STATUS_OUTBOUND_RESERVING,
                                    Stock::STATUS_OUTBOUND_PART_RESERVED,
                                ]
                            ]);
                        $successMessages[] = Yii::t('stock/messages', 'Успешно переместили из короба {from} в короб {to}',['from'=>$model->from,'to'=>$model->to]);
                    }

                    break;
            }
            //E: End
            if(!$model->hasErrors()) {
                $success = 1;
            }

            return [
                'success'=> $success,
                'successMessages'=> $successMessages,
                'errors' => $model->getErrors(),
            ];

        } else {
            return [
                'success'=>0,
                'errors' => ActiveForm::validate($model)
            ];
        }
    }
}