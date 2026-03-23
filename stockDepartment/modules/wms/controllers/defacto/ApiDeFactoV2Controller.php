<?php

namespace app\modules\wms\controllers\defacto;

use common\api\DeFactoSoapAPI;
use common\components\BarcodeManager;
use common\helpers\DateHelper;
use common\managers\TelegramDefactoNotification;
use common\modules\crossDock\models\ConsignmentCrossDock;
use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItemProducts;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\crossDock\models\CrossDockLog;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\inbound\models\InboundUploadLog;
use common\modules\outbound\models\OutboundUploadItemsLog;
use common\modules\outbound\models\OutboundUploadLog;
use common\modules\stock\models\ConsignmentUniversal;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use stockDepartment\modules\wms\managers\defacto\ConsignmentUniversalRepository;
use stockDepartment\modules\wms\managers\defacto\OutboundStatus;
use stockDepartment\modules\wms\managers\defacto\ConsignmentUniversalStatus;
use yii;
use stockDepartment\modules\wms\models\ApiDeFactoForm;
//use yii\web\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use common\modules\client\models\Client;

class ApiDeFactoV2Controller extends \stockDepartment\components\Controller
{
    public function actionIndex()
    {
        $clientsArray = Client::getActiveItems();
        return $this->render('index',[
            'clientsArray' => $clientsArray,
        ]);
    }
    /*
    * Form for get inbound order
    *
    * */
    public function actionGetInboundOrderGrid()
    {
        $apiManager = new DeFactoSoapAPIV2Manager();
        $apiDataResult =  $apiManager->getAndSaveInboundOrderParty();
        $dataProvider = [];
        $errorMessage = '';
        if(!$apiDataResult['HasError']) {
            $dataProvider = $apiManager->getConsignmentUniversalActiveDataProvider(ConsignmentUniversal::ORDER_TYPE_INBOUND);
        } else {
            $errorMessage = $apiDataResult['ErrorMessage'];
        }
        return $this->renderAjax('get-inbound-order-grid', ['dataProvider' => $dataProvider,'errorMessage'=>$errorMessage]);
    }
    /*
     *
     * */
    public function actionMarkedInboundParty($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $apiManager = new DeFactoSoapAPIV2Manager($id);
        return $apiManager->saveMarkInboundPartyById();
    }
    /*
     *
     * */
    public function actionGetInboundPartyItems($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $apiManager = new DeFactoSoapAPIV2Manager($id);
        $dataFromAPIResult = $apiManager->getAndSaveInboundOrderPartyItems(); // Вызываю для обновления статусов.
        if ($dataFromAPIResult['HasError']) {
            return $dataFromAPIResult;
        }
        $dataResult = $apiManager->saveInboundInStockToDb();
        if ($dataResult['HasError']) {
            return $dataResult;
        }

        $cu = ConsignmentUniversal::findOne(['id' => $id]);

        TelegramDefactoNotification::sendInboundMessage([
            'partyNumber'=>$cu->party_number,
            'orderNumber'=>$cu->field_extra1,
            'boxQty'=>$cu->expected_number_places_qty,
        ]);
        // Сохраняем приход на склад. модель Inbound и Cross Dock
        return $dataResult;
    }

    /*
    * Form for get outbound order
    *
    * */
    public function actionGetOutboundOrderGrid()
    {
        $apiManager = new DeFactoSoapAPIV2Manager();
        $apiDataResult =  $apiManager->getAndSaveOutboundOrderParty();
        $dataProvider = [];
        $errorMessage = '';
        if(!$apiDataResult['HasError']) {
            $dataProvider = $apiManager->getConsignmentUniversalActiveDataProvider(ConsignmentUniversal::ORDER_TYPE_OUTBOUND);
        } else {
            $errorMessage = $apiDataResult['ErrorMessage'];
        }
        return $this->renderAjax('get-outbound-order-grid', ['dataProvider' => $dataProvider,'errorMessage'=>$errorMessage]);
    }

    /*
     *
     * */
    public function actionMarkedOutboundParty($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $apiManager = new DeFactoSoapAPIV2Manager($id);
        return $apiManager->saveMarkOutboundPartyById();
    }

    /*
 *
 * */
    public function actionGetOutboundPartyItems($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $apiManager = new DeFactoSoapAPIV2Manager($id);
        $result = $apiManager->getAndSaveOutboundOrderPartyItems();
        if ($result['HasError']) {
            return [
                'isError'=>true,
                'errorMessage'=>$result['ErrorMessage'],
                'successMessage'=>'',
            ];
        }
        $status = new OutboundStatus(ConsignmentUniversalStatus::STATUS_OUTBOUND_LOADED);
        $cuRepository = new ConsignmentUniversalRepository($id);
        $cuRepository->saveStatus($status);

        return [
            'isError'=>false,
            'errorMessage'=>'',
            'successMessage'=>'Успешно загружено',
        ];
    }
    /*
    *
    * */
    public function actionGetOutboundPartyItemsSave($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $apiManager = new DeFactoSoapAPIV2Manager($id);
//        $apiManager->getAndSaveOutboundOrderPartyItems();
        $result = $apiManager->saveOutboundInStockToDb();
        if ($result['HasError']) {
            return [
                'isError'=>true,
                'errorMessage'=>$result['ErrorMessage'],
                'successMessage'=>'',
            ];
        }

//        ConsignmentUniversal::setOutboundStatus($id,ConsignmentUniversal::STATUS_OUTBOUND_SAVED_AND_CREATE_ORDERS);

        $cu = ConsignmentUniversal::findOne(['id' => $id]);
        TelegramDefactoNotification::sendOutboundMessage([
            'partyNumber'=>$cu->party_number,
            'lotQty'=>$cu->expected_qty,
        ]);
        return [
            'isError'=>false,
            'errorMessage'=>'',
            'successMessage'=>'Накладная успешно создана. Необходимо нажать кнопку резервировать',
        ];
    }

    /*
    * Form for get inbound order
    *
    * */
    public function actionGetReturnOrderGrid()
    {
        $apiManager = new DeFactoSoapAPIV2Manager();
        $apiDataResult =  $apiManager->getAndSaveReturnOrderParty();
        $dataProvider = [];
        $errorMessage = '';
        if(isset($apiDataResult['HasError']) && !$apiDataResult['HasError']) {
            $dataProvider = $apiManager->getConsignmentUniversalActiveDataProvider(ConsignmentUniversal::ORDER_TYPE_RETURN);
        } else {
            $errorMessage = isset($apiDataResult['ErrorMessage']) ? $apiDataResult['ErrorMessage'] : "Неизвестная ошибка";
        }
        return $this->renderAjax('get-return-order-grid', ['dataProvider' => $dataProvider,'errorMessage'=>$errorMessage]);
    }

    /*
     *
     * */
    public function actionSaveReturnPartyItems($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $apiManager = new DeFactoSoapAPIV2Manager();

        $dataFromAPIResult = $apiManager->saveConsignmentUniversalReturnOrderItemsToDb(399); // Вызываю для обновления статусов.
        if (!$dataFromAPIResult['HasError']) {
            $dataFromAPIResult = $apiManager->saveReturnOrderItemToOurDb(399);
        }
        return $dataFromAPIResult;

    }



    /*
     * Form for get outbound order
     *
     * */
    public function actionGetOutboundOrderForm()
    {
        $model = new ApiDeFactoForm();
        $model->scenario = 'Outbound';

        $errors = [];
        $success = 1;

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->validate()) {

                $api = new DeFactoSoapAPI();

                $expectedQty = 0;
                $inboundModelID = 0;

                if ($apiResponse = $api->getOutboundOrderByInvoice($model->invoice)) {
                    if (empty($apiResponse['errors'])) {

                        $apiData = $apiResponse['response'];

                        // Save to Inbound Order Logging
                        $client_id = 2; // DeFacto;
                        $unique_key = time();

                        foreach ($apiData as $key => $outboundRow) {

                            $partyOrder = !empty($outboundRow['PartiNo']) ? $outboundRow['PartiNo'] : trim($model->invoice) ;
                            $orderNumber = $outboundRow['RezerveId'];
                            $shopId = $outboundRow['CariId'];

                            if (!($oul = OutboundUploadLog::findOne([
                                'client_id' => $client_id,
                                'unique_key' => $unique_key,
                                'party_number' => $partyOrder,
                                'order_number' => $orderNumber,
                            ]))
                            ) {
                                $oul = new OutboundUploadLog();
                                $oul->unique_key = $unique_key;
                                $oul->client_id = $client_id;
                                $oul->party_number = $partyOrder;
                                $oul->order_number = $orderNumber;

                                $oul->data_created_on_client = DateHelper::formatDefactoDate($outboundRow['PartiOnayTarih']);// $orderInfo['data_created_on_client'];
                                //S: Find
                                if ($point = Store::find()->where(['client_id' => $oul->client_id, 'shop_code' => $shopId])->one()) {
                                    $oul->to_point_id = $point->id;
                                }
                                //E: Find
                                $oul->to_point_title = $shopId;
                                $oul->expected_qty = 0;
                                $oul->save(false);
                            }

                            if (!($ouIl = OutboundUploadItemsLog::findOne([
                                'outbound_upload_id' => $oul->id,
                                'product_barcode' => $outboundRow['Barkod'],
                            ]))
                            ) {
                                $ouIl = new OutboundUploadItemsLog();
                                $ouIl->outbound_upload_id = $oul->id;
                                $ouIl->product_barcode = $outboundRow['Barkod'];
                            }

                            $ouIl->expected_qty = $outboundRow['Miktar'];
                            $ouIl->save(false);

                            $oul->expected_qty += $outboundRow['Miktar'];
                            $oul->save(false);
                        }

//                        yii\helpers\VarDumper::dump($apiData,10,true);
                        $query = OutboundUploadLog::find()->where(['client_id' => $client_id, 'unique_key' => $unique_key]);

                        $dataProvider = new yii\data\ActiveDataProvider([
                            'query' => $query,
                            'pagination' => false,
                            'sort' => false,
                        ]);

//                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                            'gridData' => $this->renderAjax('upload-log-grid-outbound', [
                                'dataProvider' => $dataProvider,
                                'unique_key' => $unique_key,
                                'client_id' => $client_id,
                            ]),
                            'errors' => $errors,
                            'success' => $success,
                        ];
                    } else {
                        $model->addError('apidefactoform-invoice', $apiResponse['errors']);
                        $errors = $model->getErrors();
                        return [
                            'success' => (empty($errors) ? '1' : '0'),
                            'errors' => $errors
                        ];
                    }
                }
            }
        }
        // FOR TEST

        return $this->renderAjax('get-outbound-order-form', ['model' => $model]);
    }
}
