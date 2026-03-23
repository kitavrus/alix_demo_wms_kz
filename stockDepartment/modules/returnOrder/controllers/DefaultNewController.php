<?php

namespace app\modules\returnOrder\controllers;

use app\modules\returnOrder\models\ReturnFormNew;
use common\api\DeFactoSoapAPI;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use stockDepartment\components\Controller;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
use stockDepartment\modules\returnOrder\api\ReturnDeFactoSoapAPI;

class DefaultNewController extends Controller
{
    public function actionIndex()
    {
        $model = new ReturnFormNew();
        return $this->render('index', ['model' => $model]);
    }

    /*
     *
     * */
    public function actionSoap()
    {
        $api = new DeFactoSoapAPI();
        $BelgeKodu = '209623;388;1';
        $apiResponse = $api->GetKoli($BelgeKodu);
        $messageCode = '';
        $message = '';
        if( empty($apiResponse['errors']) ) {
            if(!$apiResponse['response']->GetKoliResult->IslemBasarili) {
                $messageCode = $apiResponse['response']->GetKoliResult->MessageCode;
                $message = $apiResponse['response']->GetKoliResult->Message;
            }
        }


        VarDumper::dump($apiResponse,10,true);
        die('----STOP----');
 /*       $api =  new ReturnDeFactoSoapAPI;
        $api->connect();


        $params = [
            'request' => [
                'UserName' => '',
                'Password' => '',
                'BelgeKodu' => "349563;321;1",
//                'BelgeKodu' => "344701;321;1",
            ]
        ];

        $response = $api->sendRequest('GetKoli',$params);

        VarDumper::dump($response,10,true);*/
//        die('----STOP----');
/*
        $client = new \SoapClient('http://service.defacto.com.tr/Depo/KzkDepoNew/KzkDCIadeIslemleri.asmx?WSDL',
//        $client = new \SoapClient('http://service.defacto.com.tr/depo/KzkDepo/KzkDcDepoOperations.asmx?WSDL',
            [
                'trace' => 1,
                "exceptions" => 1,
                "soap_version" => SOAP_1_1,
            ]
        );

        $params = [
            'request' => [
                'UserName' => '',
                'Password' => '',
                'BelgeKodu' => "349563;321;1",
//                'BelgeKodu' => "344701;321;1",
            ]
        ];

//        VarDumper::dump($client,10,true);

        try {
            $response = [];
//            $response = $client->GetKoli($params);
            $response = $client->IadeKabul($params);
//
            VarDumper::dump($response, 10, true);
//
        } catch (\SoapFault $exception) {
//
            echo "REQUEST:\n" . "\n";
//
            echo "<pre>";
            print_r($client->__getLastRequest());
            echo "</pre>";
            echo "<br />";
            echo "<br />";
            echo "<b>ERRORS</b><br />";
            echo "<pre>";
            print_r($exception);
            echo "</pre>";
//            VarDumper::dump($exception, 10, true);
        }

        die('----STOP----');
*/

    }

    /*
     *
     * */
    public function actionBoxBarcode()
    {
        $model = new ReturnFormNew();
        $model->scenario = 'BoxBarcode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        }

        $errors = ActiveForm::validate($model);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }

    /*
     *
     * */
    public function actionOrderNumber()
    {
        $newReturnOrderID = 0;
        $byProduct = 0;
        $success = 1;
        $errors = [];
        $APICode = '';
        $APIMessage = '';

        $model = new ReturnFormNew();
        $model->scenario = 'OrderNumber';

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $api = new DeFactoSoapAPI();
//            $api->connect('r');
            $api->connectNew('r');
            $params = [
                'request' => [
                    'UserName' => '',
                    'Password' => '',
                    'BelgeKodu' => $model->order_number,
                ]
            ];

            $apiResponse = $api->sendRequest('GetKoli',$params);
//            VarDumper::dump($apiResponse,10,true);
//            die('---STOP---');
            // Save Date from API to DB
            if( empty($apiResponse['errors']) ) {

                if(in_array($apiResponse['response']->GetKoliResult->MessageCode,[
                    '011','098','099','007','097'
                ])) {
                    $byProduct = 1;
                }

                $APICode = $apiResponse['response']->GetKoliResult->MessageCode;
                $APIMessage = $apiResponse['response']->GetKoliResult->Message;

                if(!$apiResponse['response']->GetKoliResult->IslemBasarili && $byProduct !=1) {
                    $m = $apiResponse['response']->GetKoliResult->Message;
                    $mCode = $apiResponse['response']->GetKoliResult->MessageCode;
                    $model->addError('returnformnew-order_number',$mCode.' '.$m);
                    // TODO Send Mail to Admin (Kitavrus)
                } else {

                    $koliResponse = isset($apiResponse['response']->GetKoliResult->Koli) ? $apiResponse['response']->GetKoliResult->Koli : [];
                    $toSaveData = [];
                    $countProductInBox = 0;
                    if ($koliResponse) {

                        $toSaveData['BelgeID'] = isset($koliResponse->BelgeID) ? trim($koliResponse->BelgeID) : '';
                        $toSaveData['DepoID'] = isset($koliResponse->DepoID) ? trim($koliResponse->DepoID) : '';
                        $toSaveData['IrsaliyeNo'] = isset($koliResponse->IrsaliyeNo) ? trim($koliResponse->IrsaliyeNo) : '';
                        $toSaveData['IrsaliyeSeri'] = isset($koliResponse->IrsaliyeSeri) ? trim($koliResponse->IrsaliyeSeri) : '';
                        $toSaveData['DepoTanim'] = isset($koliResponse->DepoTanim) ? trim($koliResponse->DepoTanim) : '';
                        $toSaveData['KoliBarkod'] = isset($koliResponse->KoliBarkod) ? trim($koliResponse->KoliBarkod) : '';
                        $toSaveData['KarsiOnay'] = isset($koliResponse->KarsiOnay) ? trim($koliResponse->KarsiOnay) : '';
                        $toSaveData['BelgeTarih'] = isset($koliResponse->BelgeTarih) ? trim($koliResponse->BelgeTarih) : '';
                        $toSaveData['KarsiDepoId'] = isset($koliResponse->KarsiDepoId) ? trim($koliResponse->KarsiDepoId) : '';
                        $toSaveData['IrsaliyeBuyerGroup'] = isset($koliResponse->IrsaliyeBuyerGroup) ? trim($koliResponse->IrsaliyeBuyerGroup) : '';
                        $toSaveData['IrsaliyeMerchGroup'] = isset($koliResponse->IrsaliyeMerchGroup) ? trim($koliResponse->IrsaliyeMerchGroup) : '';
                        $toSaveData['IrsaliyeMiktar'] = isset($koliResponse->IrsaliyeMiktar) ? trim($koliResponse->IrsaliyeMiktar) : '';

                        $toSaveData['Sezon'] = isset($koliResponse->Sezon) ? trim($koliResponse->Sezon) : '';
                        $toSaveData['UrunKodu'] = isset($koliResponse->UrunKodu) ? trim($koliResponse->UrunKodu) : '';

                        $countProductInBox = isset($koliResponse->IrsaliyeMiktar) ? intval($koliResponse->IrsaliyeMiktar) : '0';
                    }

                    $productInOrder = [];

                    $KzkDCIadeDetayArray = isset($koliResponse->KoliDetay->KzkDCIadeDetay) ? $koliResponse->KoliDetay->KzkDCIadeDetay : [];
                    if (!empty($KzkDCIadeDetayArray)) {
                        foreach ($KzkDCIadeDetayArray as $product) {

                            $productOne = [];
                            $productOne['UrunId'] = isset($product->UrunId) ? trim($product->UrunId) : '';
                            $productOne['BarkodId'] = isset($product->BarkodId) ? trim($product->BarkodId) : '';
                            $productOne['KisaKod'] = isset($product->KisaKod) ? trim($product->KisaKod) : '';
                            $productOne['Barkod'] = isset($product->Barkod) ? trim($product->Barkod) : '';
                            $productOne['Renk'] = isset($product->Renk) ? trim($product->Renk) : '';
                            $productOne['Beden'] = isset($product->Beden) ? trim($product->Beden) : '';
                            $productOne['Merch'] = isset($product->Merch) ? trim($product->Merch) : '';
                            $productOne['AnaGrup'] = isset($product->AnaGrup) ? trim($product->AnaGrup) : '';
                            $productOne['Miktar'] = isset($product->Miktar) ? trim($product->Miktar) : '';
                            $productOne['Sayi'] = isset($product->Sayi) ? trim($product->Sayi) : '';

                            $productInOrder[] = $productOne;
                        }
                    }
                    // Создаем возврат
                    $client_id = 2;
                    if (!($return = ReturnOrder::findOne(['order_number' => $model->order_number, 'client_id' => $client_id]))) {
                        $return = new ReturnOrder();
                        $return->client_id = $client_id;
                        $return->order_number = $model->order_number;
                        $return->status = ReturnOrder::STATUS_NEW;
                        $return->expected_qty = $countProductInBox;
                        $return->accepted_qty = '0';
                        $json['koliResponse'] = $toSaveData;
                        $json['KoliDetay->KzkDCIadeDetay'] = $productInOrder;
                        $json['boxBarcode'] = $model->box_barcode;
                        $return->extra_fields = Json::encode($json);
                        $return->save(false);
                    } else {
                        $json['koliResponse'] = $toSaveData;
                        $json['KoliDetay->KzkDCIadeDetay'] = $productInOrder;
                        $json['boxBarcode'] = $model->box_barcode;
                        $return->extra_fields = Json::encode($json);
                        $return->save(false);
                    }

                    $newReturnOrderID = $return->id;
                }
            } else {
                $model->addError('returnform-order_number',$apiResponse['errors']['ExceptionMessage']);
                // TODO Send Mail to Admin (Kitavrus)
            }

            $errors = $model->getErrors();

            return [
                'APIMessage'=>$APIMessage,
                'APICode'=>$APICode,
                'success'=>(empty($errors) ? '1' : '0'),
                'errors' => $errors,
                'newReturnOrderID' => $newReturnOrderID,
                'byProduct' => $byProduct,
            ];

        } else {
            $errors = ActiveForm::validate($model);
            return [
                'APIMessage'=>$APIMessage,
                'APICode'=>$APICode,
                'success'=>(empty($errors) ? '1' : '0'),
                'errors' => $errors,
                'newReturnOrderID' => $newReturnOrderID,
                'byProduct' => $byProduct,
            ];
        }
    }

    /*
    *
    * */
    public function actionPrintBoxBarcode()
    {
        $client_id = 2;
        $errors = [];
        $koliResponseData = [];
        $responseAPIErrorMessage = 'no-error';

        $id = Yii::$app->request->get('id');
        $byProduct = Yii::$app->request->get('by-product');
        $boxBarcode = Yii::$app->request->get('box-barcode');

        if($id) {

            $model = ReturnOrder::findOne(['id' => $id, 'client_id' => $client_id]);

            $orderBarcode = $model->order_number;
            $extraFields = [];
            if ($model->extra_fields) {
                $extraFields = Json::decode($model->extra_fields);
            }

            $boxBarcode = isset($extraFields['boxBarcode']) ? $extraFields['boxBarcode'] : '-';

//            $api = new ReturnDeFactoSoapAPI();
//            $api->connect();
            if ($byProduct == 0) {

                $api = new DeFactoSoapAPI();
                $api->connectNew('r');

                $params = [
                    'request' => [
                        'UserName' => '',
                        'Password' => '',
                        'BelgeKodu' => $model->order_number,
                    ]
                ];

                $apiResponse = $api->sendRequest('IadeKabul', $params);
            } else {
                $api = new DeFactoSoapAPI();
                $api->connectNew('r');
                $preparedDataReturn = DeFactoSoapAPI::preparedDataForReturnConfirm($model->id);

                $BelgeID = isset($extraFields['koliResponse']['BelgeID']) ? $extraFields['koliResponse']['BelgeID'] : 0;
                $DepoID = isset($extraFields['koliResponse']['DepoID']) ? $extraFields['koliResponse']['DepoID'] : 0;
                $IrsaliyeNo = isset($extraFields['koliResponse']['IrsaliyeNo']) ? $extraFields['koliResponse']['IrsaliyeNo'] : 0;

                if($parsedOrderNumber = explode(';',$model->order_number)) {
                   $BelgeID = $parsedOrderNumber[0];
                   $DepoID = $parsedOrderNumber[1];
                }

                $params = [
                    'request' => [
                        'UserName' => '',
                        'Password' => '',
                        'BelgeKodu' => $model->order_number,
                        'KzkDCIade'=>[
                            'BelgeID'=>$BelgeID,
                            'DepoID'=>$DepoID,
                            'IrsaliyeNo'=>0,
//                            'IrsaliyeNo'=>$IrsaliyeNo,
                            'KoliDetay'=> [
                                'KzkDCKoliIadeDetay'=> $preparedDataReturn
                            ]
                        ]
                    ]
                ];
                $apiResponse = $api->sendRequest('KoliIadeKabul',$params);

            }
//            VarDumper::dump($params,10,true);
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            VarDumper::dump($apiResponse,10,true);
//            die('---STOP---');

//            $apiResponse = $api->sendRequest('GetKoli',$params);
            // Save Date from API to DB
            if( empty($apiResponse['errors']) ) {

                if($byProduct == 0) {
                    if (!$apiResponse['response']->IadeKabulResult->IslemBasarili) {
                        $responseAPIErrorMessage = $apiResponse['response']->IadeKabulResult->Message;
                        $model->addError('returnform-order_number', $responseAPIErrorMessage);
                        // TODO Send Mail to Admin (Kitavrus)
                    }
                    $koliResponse = $apiResponse['response']->IadeKabulResult->Koli;
                } else {
                    if (!$apiResponse['response']->KoliIadeKabulResult->IslemBasarili) {
                        $responseAPIErrorMessage = $apiResponse['response']->KoliIadeKabulResult->Message;
                        $model->addError('returnform-order_number', $responseAPIErrorMessage);
                        // TODO Send Mail to Admin (Kitavrus)
                    }
                    $koliResponse = $apiResponse['response']->KoliIadeKabulResult->Koli;
                }

                $koliResponseData['BelgeID'] = isset($koliResponse->BelgeID) ? trim($koliResponse->BelgeID) : '';
                $koliResponseData['DepoID'] = isset($koliResponse->DepoID) ? trim($koliResponse->DepoID) : '';
                $koliResponseData['IrsaliyeSeri'] = isset($koliResponse->IrsaliyeSeri) ? trim($koliResponse->IrsaliyeSeri) : '';
                $koliResponseData['IrsaliyeNo'] = isset($koliResponse->IrsaliyeNo) ? trim($koliResponse->IrsaliyeNo) : '';
                $koliResponseData['DepoTanim'] = isset($koliResponse->DepoTanim) ? trim($koliResponse->DepoTanim) : '';
                $koliResponseData['KoliBarkod'] = isset($koliResponse->KoliBarkod) ? trim($koliResponse->KoliBarkod) : '';
                $koliResponseData['KarsiOnay'] = isset($koliResponse->KarsiOnay) ? trim($koliResponse->KarsiOnay) : '';
                $koliResponseData['BelgeTarih'] = isset($koliResponse->BelgeTarih) ? trim($koliResponse->BelgeTarih) : '';
                $koliResponseData['KarsiDepoId'] = isset($koliResponse->KarsiDepoId) ? trim($koliResponse->KarsiDepoId) : '';
                $koliResponseData['IrsaliyeBuyerGroup'] = isset($koliResponse->IrsaliyeBuyerGroup) ? trim($koliResponse->IrsaliyeBuyerGroup) : '';
                $koliResponseData['IrsaliyeMerchGroup'] = isset($koliResponse->IrsaliyeMerchGroup) ? trim($koliResponse->IrsaliyeMerchGroup) : '';
                $koliResponseData['IrsaliyeMiktar'] = isset($koliResponse->IrsaliyeMiktar) ? trim($koliResponse->IrsaliyeMiktar) : '';
                $koliResponseData['Sezon'] = isset($koliResponse->Sezon) ? trim($koliResponse->Sezon) : '';
                $koliResponseData['UrunKodu'] = isset($koliResponse->UrunKodu) ? trim($koliResponse->UrunKodu) : '';
            }

//            VarDumper::dump($extraFields,10,true);
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            VarDumper::dump($params,10,true);
//            echo "<br />";
//            echo "<br />";
//            echo "<br />";
//            VarDumper::dump($apiResponse['response']->IadeKabulResult,10,true);
//            die;

            if((isset($koliResponseData['KoliBarkod']) && !empty($koliResponseData['KoliBarkod']))) {

                $lotBarcode = $koliResponseData['KoliBarkod'];

                // сохраняем запись в приходную таблицу
                if (!($inbound = InboundOrder::findOne(['client_id' => $client_id, 'order_number' => $orderBarcode]))) {
                    $inbound = new InboundOrder();
                }

                $inbound->client_id = $client_id;
                $inbound->order_number = $orderBarcode;
                $inbound->status = Stock::STATUS_INBOUND_COMPLETE;
                $inbound->order_type = InboundOrder::ORDER_TYPE_RETURN;
                $inbound->accepted_qty = $model->accepted_qty;
                $inbound->expected_qty = $model->expected_qty;
                $inbound->save(false);

                // Сохранить тавар на  склад(stock)
                if (!($stock = Stock::findOne(['inbound_order_id' => $inbound->id, 'client_id' => $client_id]))) {
                    $stock = new Stock();
                }

                $stock->client_id = $client_id;
                $stock->inbound_order_id = $inbound->id;
                $stock->product_barcode = $lotBarcode;
                $stock->primary_address = $boxBarcode;
                $stock->status = Stock::STATUS_INBOUND_CONFIRM;
                $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
                $stock->save(false);

                if($byProduct == 0) {
                    $extraFields['IadeKabulResult->responseAPIErrorMessage'] = $responseAPIErrorMessage;
                    $extraFields['IadeKabulResult->Koli'] = $koliResponseData;
                } else {
                    $extraFields['KoliIadeKabulResult->responseAPIErrorMessage'] = $responseAPIErrorMessage;
                    $extraFields['KoliIadeKabulResult->Koli'] = $koliResponseData;
                }

                $model->status = ReturnOrder::STATUS_COMPLETE;
                $model->extra_fields = Json::encode($extraFields);
                $model->save(false);

            } else {
                $m = '';
                if($responseAPIErrorMessage != 'no-error') {
                    $m = $responseAPIErrorMessage;
                }
                Yii::$app->getSession()->setFlash('error', Yii::t('return/messages', 'Api DeFacto не ответила, сообщите об этом Азамату, Турсуну, Игорю')."<br />".' DE FACTO API MESSAGE '.$m);
                return $this->redirect(['/returnOrder/default/index']);

            }
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('return/messages', 'Для этой накладной уже напечатали этикетку или вы не выбрали накладную'));
           return $this->redirect(['/returnOrder/default/index']);
        }

        return $this->render('_box-label-pdf', ['model' => $model,'koliResponseData'=>$koliResponseData]);
    }

    /*
     * Scan product barcode from box
     * */
    public function actionScanProductFromBox()
    {
        $expected_qty = 0;
        $accepted_qty = 0;

        $model = new ReturnFormNew();
        $model->scenario = 'ProductBarcode';

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $returnProduct = ReturnOrderItems::find()->where(
                    [
                        'return_order_id'=>$model->new_return_order_id,
                        'product_barcode'=>$model->product_barcode,
                    ]
            )->one();

            if(!$returnProduct){
                $returnProduct = new ReturnOrderItems();
                $returnProduct->return_order_id = $model->new_return_order_id;
                $returnProduct->product_barcode = $model->product_barcode;
                $returnProduct->expected_qty = 1;
                $returnProduct->accepted_qty = 1;
            } else {
                $returnProduct->expected_qty += 1;
                $returnProduct->accepted_qty += 1;
            }
            $returnProduct->save(false);

            $sum = ReturnOrderItems::find()->where(
                [
                    'return_order_id'=>$model->new_return_order_id,
                ]
            )->sum('accepted_qty');

            $returnOrder = ReturnOrder::find()->where(['id'=> $model->new_return_order_id])->one();
            if($returnOrder) {
                $returnOrder->accepted_qty = $sum;
                $returnOrder->save(false);

                $expected_qty = $returnOrder->expected_qty;
                $accepted_qty = $sum;
            }
        }

        $items = ReturnOrderItems::find()->where(['return_order_id'=>$model->new_return_order_id])->all();

        return [
            'return-action'=>'ScanProductFromBox',
            'accepted_qty'=>$accepted_qty,
            'expected_qty'=>$expected_qty,
            'success'=>1,
            'errors'=>[],
            'items'=>$this->renderPartial('_scanned_items',['items'=>$items]),
        ];
    }


    /*
     *
     * */
    public function actionClearProductInBoxByOne()
    {
        $errors = [];
        $expected_qty = 0;
        $accepted_qty = 0;
        $model = new ReturnFormNew();

        $model->scenario = 'ProductBarcodeClearOne';

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $returnProduct = ReturnOrderItems::find()->where(
                [
                    'return_order_id'=>$model->new_return_order_id,
                    'product_barcode'=>$model->product_barcode,
                ]
            )->one();

            $returnProduct->expected_qty -= 1;
            $returnProduct->accepted_qty -= 1;


            if($returnProduct->expected_qty < 1)  {
                $returnProduct->expected_qty = 0;
            }

            if($returnProduct->accepted_qty < 1)  {
                $returnProduct->accepted_qty = 0;
            }
            $returnProduct->save(false);


            $sum = ReturnOrderItems::find()->where(
                [
                    'return_order_id'=>$model->new_return_order_id,
                ]
            )->sum('accepted_qty');

            $returnOrder = ReturnOrder::find()->where(['id'=> $model->new_return_order_id])->one();
            if($returnOrder) {
                $returnOrder->accepted_qty = $sum;
                $returnOrder->save(false);

                $expected_qty = $returnOrder->expected_qty;
                $accepted_qty = $sum;
            }


        } else {
            $errors = ActiveForm::validate($model);
        }

        $items = ReturnOrderItems::find()->where(['return_order_id'=>$model->new_return_order_id])->all();

        return [
            'return-action'=>'actionClearProductInBoxByOne',
            'accepted_qty'=>$accepted_qty,
            'expected_qty'=>$expected_qty,
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'items'=>$this->renderPartial('_scanned_items',['items'=>$items]),

        ];
    }

    /*
    *
    * */
    public function actionClearAllInBox()
    {
        $expected_qty = 0;
        $accepted_qty = 0;
        $errors = [];
        $model = new ReturnFormNew();

        $model->scenario = 'ClearAll';

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $returnProductAll = ReturnOrderItems::find()->where(
                [
                    'return_order_id'=>$model->new_return_order_id,
                ]
            )->all();

            if(!empty($returnProductAll)) {
                foreach($returnProductAll as $product) {
                    $product->expected_qty = 0;
                    $product->accepted_qty = 0;
                    $product->save(false);
                }
            }

            $sum = ReturnOrderItems::find()->where(
                [
                    'return_order_id'=>$model->new_return_order_id,
                ]
            )->sum('accepted_qty');

            $returnOrder = ReturnOrder::find()->where(['id'=> $model->new_return_order_id])->one();
            if($returnOrder) {
                $returnOrder->accepted_qty = $sum;
                $returnOrder->save(false);

                $expected_qty = $returnOrder->expected_qty;
                $accepted_qty = $sum;
            }

        } else {
            $errors = ActiveForm::validate($model);
        }

        $items = ReturnOrderItems::find()->where(['return_order_id'=>$model->new_return_order_id])->all();

        return [
            'return-action'=>'actionClearProductInBoxByOne',
            'accepted_qty'=>$accepted_qty,
            'expected_qty'=>$expected_qty,
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'items'=>$this->renderPartial('_scanned_items',['items'=>$items]),
        ];
    }
}