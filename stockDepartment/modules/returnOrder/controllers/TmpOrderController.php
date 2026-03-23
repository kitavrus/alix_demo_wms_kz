<?php

namespace app\modules\returnOrder\controllers;
use app\modules\returnOrder\formHtml\AccommodationReturnForm;
use app\modules\returnOrder\models\ReturnTmpOrderSearch;
use app\modules\returnOrder\models\ReturnTmpOrderTTNSearch;
use common\components\BarcodeManager;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItemProduct;
use common\modules\returnOrder\models\ReturnTmpOrders;
use common\modules\stock\models\RackAddress;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use stockDepartment\modules\returnOrder\entities\TmpOrder\ReturnTmpOrder;
use app\modules\returnOrder\formHtml\TmpOrderForm;
use stockDepartment\modules\returnOrder\entities\TmpOrder\Status;
use stockDepartment\modules\returnOrder\entities\TmpOrder\Ttn;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\modules\stock\service\ChangeAddressPlaceService;

class TmpOrderController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            'formModel'=> new TmpOrderForm()
        ]);
    }

    /**
     * Updates an existing RouteDirections model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->status != Status::SCANNED) {
            Yii::$app->getSession()->setFlash('danger', "Эту запись нельзя редактировать. Данные уже отправлены по API ");
            return $this->redirect(['search']);
        }

        $tmpOrderForm = new TmpOrderForm();
        $tmpOrderForm->scenario = 'UPDATE';
        $tmpOrderForm->id = $id;

        if ($tmpOrderForm->load(Yii::$app->request->post()) && $tmpOrderForm->validate()) {
            $model->setAttributes($tmpOrderForm->getAttributes(['our_box_to_stock_barcode','client_box_barcode'],['ttn']),false);
            $model->primary_address = $model->our_box_to_stock_barcode;
            $model->save(false);
            return $this->redirect(['search']);
        } else {
            $tmpOrderForm->setAttributes($model->getAttributes(),false);
            return $this->render('update', [
                'model' => $tmpOrderForm,
            ]);
        }
    }

    /**
     * Deletes an existing RouteDirections model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->status == Status::SCANNED) {
            $model->delete();
            Yii::$app->getSession()->setFlash('success', "Запись успешно удалена");
        } else {
            Yii::$app->getSession()->setFlash('warning', "Эту запись удалить нельзя. Данные уже отправлены по API ");
        }

        return $this->redirect(['search']);
    }

    /**
     * Finds the RouteDirections model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReturnTmpOrders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReturnTmpOrders::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCheckTtn()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new TmpOrderForm();
        $model->scenario = 'FIELD-TTN';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return [
                'success' => '1',
                'qtyPlacesInTtn' => ReturnTmpOrder::getQtyByTTN($model->ttn),
                'qtyScannedInTtn' => ReturnTmpOrder::getQtyScanned($model->ttn),
            ];
        } else {
            $errors = ActiveForm::validate($model);
            return [
                'success'=>(empty($errors) ? '1' : '0'),
                'errors' => $errors
            ];
        }
    }

    public function actionCheckOurBoxStockBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new TmpOrderForm();
        $model->scenario = 'FIELD-OUR-BOX-STOCK-BARCODE';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return [
                'success' => '1',
            ];
        } else {
            $errors = ActiveForm::validate($model);
            return [
                'success'=>(empty($errors) ? '1' : '0'),
                'errors' => $errors
            ];
        }
    }

    public function actionCheckClientBoxBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new TmpOrderForm();
        $model->scenario = 'FIELD-CLIENT-BOX-BARCODE';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $rTmpOrder = new ReturnTmpOrder();
            $rTmpOrder->makeBox($model->ttn,$model->our_box_to_stock_barcode,$model->client_box_barcode);

            return [
                'success' => '1',
                'qtyScannedInTtn' => ReturnTmpOrder::getQtyScanned($model->ttn),
            ];
        } else {
            $errors = ActiveForm::validate($model);
            return [
                'success'=>(empty($errors) ? '1' : '0'),
                'errors' => $errors
            ];
        }
    }

    /*
    * */
    public function actionPrintWithoutSecondaryAddress($ttn)
    {
        return $this->render('print/without-secondary-address-pdf',['withoutSecondaryAddressList'=>ReturnTmpOrder::getUnacceptedList($ttn)]);
    }

    /*
    * */
    public function actionPrintWithSecondaryAddress($ttn)
    {
        return $this->render('print/with-secondary-address-pdf',['withSecondaryAddressList'=>ReturnTmpOrder::getAcceptedList($ttn)]);
    }

    /*
  * Confirm return order data
  * @return JSON true or errors array
  * */
    public function actionConfirmOrder() // Ok
    { // /returnOrder/tmp-order/confirm-order
//        $errors = [];
//        $messages = [];
        //die("STOP");
		( new ReturnTmpOrder)->makeInboundAndStockForAPI();
        //ReturnTmpOrder::makeInboundAndStockForAPI();
        return "Y";
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        return [
//            'success'=>'OK',
//            'errors'=>$errors,
//            'messages'=>$messages,
//        ];
    }

    /*
    *
    * */
    public function actionMove()
    { // move-from-to-form
        $form = new AccommodationReturnForm();
        $form->type = 1;
        return $this->render('accommodation-return-form',[
            'af'=>$form,
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

        $model = new AccommodationReturnForm();
        $successMessages = [];
        $success = 0;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->from = trim($model->from,'#');
            $model->to = trim($model->to,'#');

            file_put_contents('accommodation-return-move-from-to-all.log',$model->type.";".$model->from.";".$model->to.";".(new \DateTime('now'))->format('Y-m-d H:i:s')."\n",FILE_APPEND);
            //S: Start
                    file_put_contents('accommodation-return-box-on.log',$model->type.";".$model->from.";".$model->to.";".(new \DateTime('now'))->format('Y-m-d H:i:s')."\n",FILE_APPEND);
                    //From - Проверяем ШК что это короб
                    //To - Проверяем ШК что это полка
                    // TODO Попробовать сделать через DynamicModel

                    if( !BarcodeManager::isBox($model->from) && !empty($model->from) ) {
						$message = Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']';
						ChangeAddressPlaceService::add($model->from, $model->to, $message);
                        $model->addError('accommodationform-return-from',$message);
						
                        //$model->addError('accommodationform-return-from',Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }
                    //Если в коробе нет товаров
                    if(BarcodeManager::isBox($model->from)) {
                        if ( !ReturnTmpOrder::boxIsPreparedForMoveTo($model->from)) {
							$message = Yii::t('stock/errors', 'Это короб уже принят на склад') . ' [' . $model->from . '] ' . ' Перемещение из/в ' . ' [' . $model->from . ']  [' . $model->to . ']';
							ChangeAddressPlaceService::add($model->from, $model->to, $message);
							$model->addError('accommodationform-return-from', $message);
							
							// $model->addError('accommodationform-return-from', Yii::t('stock/errors', 'Это кот короб уже принят на склад') . ' [' . $model->from . '] ' . ' Перемещение из/в ' . ' [' . $model->from . ']  [' . $model->to . ']');
                        }
                    }

                    if( !BarcodeManager::isRegiment($model->to) && !empty($model->to)) {
						
						$message = Yii::t('stock/errors','This is not shelf').' ['.$model->to.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']';
						ChangeAddressPlaceService::add($model->from, $model->to, $message);
                        $model->addError('accommodationform-return-to',$message);
						
                        //$model->addError('accommodationform-return-to',Yii::t('stock/errors','This is not shelf').' ['.$model->to.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }

                    // Primary address  - Это Короб или палета
                    // Secondary address - Это полка или стелаж
                    if(!$model->hasErrors() && !empty($model->to) && !empty($model->from)) {
//                        $address_sort_order = '';
//                        if($address = RackAddress::find()->where(['address'=>$model->to])->one()) {
//                            $address_sort_order = $address->sort_order;
//                        }
                        ReturnTmpOrder::boxMoveTo($model->from,$model->to);
//                        Stock::updateAll(['secondary_address'=>$model->to,'address_sort_order'=>$address_sort_order],'primary_address = :pa',[':pa'=>$model->from]);

						$message = Yii::t('stock/messages', 'Successfully moved the box {from} shelf {to}',['from'=>$model->from,'to'=>$model->to]);
						ChangeAddressPlaceService::add($model->from, $model->to, $message);
                        $successMessages[] = $message;

                        //$successMessages[] = Yii::t('stock/messages', 'Successfully moved the box {from} shelf {to}',['from'=>$model->from,'to'=>$model->to]);
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

    public function actionSearch()
    {
        $searchModel = new ReturnTmpOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $countWithoutSecondaryAddress = ReturnTmpOrder::countWithoutSecondaryAddress();
        $countWithSecondaryAddress = ReturnTmpOrder::countWithSecondaryAddress();
        $countSendByAPI = ReturnTmpOrder::countSendByAPI();

        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'countWithoutSecondaryAddress' => $countWithoutSecondaryAddress,
            'countWithSecondaryAddress' => $countWithSecondaryAddress,
            'countSendByAPI' => $countSendByAPI,
        ]);
    }

    public function actionTtnReport()
    { // ttn-report
        $searchModel = new ReturnTmpOrderTTNSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $q = clone $dataProvider->query;
        $ttnIDs = $q->select("ttn")->column();
        $deliveryProposals =  TlDeliveryProposal::find()->select('route_from, route_to, number_places, id')->andWhere(['id'=>$ttnIDs])->indexBy('id')->asArray()->all();

        $countWithoutSecondaryAddress = ReturnTmpOrder::countWithoutSecondaryAddress();
        $countWithSecondaryAddress = ReturnTmpOrder::countWithSecondaryAddress();
        $countSendByAPI = ReturnTmpOrder::countSendByAPI();
        $storeArray = TLHelper::getStockPointArray();

        return $this->render('ttn-report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'countWithoutSecondaryAddress' => $countWithoutSecondaryAddress,
            'countWithSecondaryAddress' => $countWithSecondaryAddress,
            'countSendByAPI' => $countSendByAPI,
            'deliveryProposals' => $deliveryProposals,
            'storeArray' => $storeArray,
        ]);
    }

    public function actionExportBoxInTtn()
    { // export-box-in-ttn
        $searchModel = new ReturnTmpOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('print/export-box-in-ttn', [
            'dataProvider' => $dataProvider->getModels(),
        ]);
    }
}