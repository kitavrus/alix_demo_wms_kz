<?php

namespace app\modules\wms\controllers\defacto;

use common\api\DeFactoSoapAPI;
use common\components\BarcodeManager;
use common\helpers\DateHelper;
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
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use yii;
use stockDepartment\modules\wms\models\ApiDeFactoForm;
//use yii\web\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use common\modules\client\models\Client;

class ApiDeFactoController extends \stockDepartment\components\Controller
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
    public function actionGetInboundOrderForm()
    {
        $model = new ApiDeFactoForm();
        $model->scenario = 'Inbound';

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $errors = [];
            if ($model->validate()) {

                $api = new DeFactoSoapAPI();

                $expectedQty = 0;
                $inboundModelID = 0;

                if ($apiResponse = $api->getInboundOrderByInvoice($model->invoice)) {
                    if (empty($apiResponse['errors'])) {

                        $apiData = $apiResponse['response'];

                        // Save to Inbound Order Logging
                        $client_id = 2; // DeFacto;
                        $unique_key = time() . 'P';
                        $order_number = $model->invoice;
                        $dataProvider = [];
                        $messages = '';
                        $updateStatus = 0;
                        $success = 1;

                        if (!empty($apiData) && is_array($apiData)) {

                            foreach ($apiData as $key => $inboundRow) {

                                if ($inboundRow['CrossDock'] == 'P') {

                                    if(!($iul = InboundUploadLog::findOne([
                                                                            'client_id'=>$client_id,
                                                                            'unique_key'=>$unique_key,
                                                                            'order_number'=>$order_number,
                                                                            'product_barcode'=>$inboundRow['Barkod'],
                                                                            'product_model'=>$inboundRow['KisaKod'],
                                        ]
                                    ))) {
                                        $iul = new InboundUploadLog();
                                        $iul->client_id = $client_id;
                                        $iul->unique_key = $unique_key;
                                        $iul->order_number = $order_number;
                                        $iul->product_barcode = $inboundRow['Barkod'];
                                        $iul->product_model = $inboundRow['KisaKod'];
                                        $iul->delivery_type = InboundOrder::DELIVERY_TYPE_RPT; // 1 - крос-док, 2 - Склад
                                        $iul->expected_qty = 0;
                                    }

                                    $iul->expected_qty += (int)$inboundRow['Miktar'];
                                    $iul->save(false);
                                }
                            }

                            $query = InboundUploadLog::find()->where(['client_id' => $client_id, 'unique_key' => $unique_key]);//->asArray()->all();
                            $dataProvider = new yii\data\ActiveDataProvider([
                                'query' => $query,
                                'pagination' => false,
                                'sort' => false,
                            ]);

                            $ioOrderNumber = InboundUploadLog::find()->select('order_number')->where(['client_id' => $client_id, 'unique_key' => $unique_key])->scalar();
                            $io = InboundOrder::find()
                                    ->andWhere(['client_id' => $client_id, 'order_number' => $ioOrderNumber])
                                    ->andWhere('false != :ioOrderNumber',[':ioOrderNumber'=>$ioOrderNumber])
                                    ->one();
                            if ($io) {
                                if (in_array($io->status, [
                                    Stock::STATUS_INBOUND_SCANNING,
                                    Stock::STATUS_INBOUND_SCANNED,
                                    Stock::STATUS_INBOUND_OVER_SCANNED,
                                    Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API,
                                    Stock::STATUS_INBOUND_COMPLETE,
                                    Stock::STATUS_INBOUND_CONFIRM,
                                ])
                                ) {
                                    $messages = Yii::t('inbound/messages', 'Накладная с номер {order-number} уже принимается, обновить нельзя', ['order-number' => $io->order_number]);
                                    $updateStatus = 0;
                                } else {
                                    $messages = Yii::t('inbound/messages', 'Накладная с номер {order-number} уже есть в системе, обновить?', ['order-number' => $io->order_number]);
                                    $updateStatus = 1;
                                }
                            } else {
                                $messages = Yii::t('inbound/messages', 'Подтвердите загрузку {order-number} в системе', ['order-number' => $model->invoice]);
                                $updateStatus = 1;
                            }

                        } else {
                            $messages = Yii::t('inbound/messages', 'Накладная с номер {order-number} не найдена, нет данных для загрузки', ['order-number' => $model->invoice]);
                            $updateStatus = 0;
                        }

                        return [
                            'gridData' => $this->renderAjax('upload-log-grid-inbound', [
                                'dataProvider' => $dataProvider,
                                'unique_key' => $unique_key,
                                'client_id' => $client_id,
                                'messages' => $messages,
                                'updateStatus' => $updateStatus,
                            ]),
                            'errors' => $errors,
                            'success' => $success,
                        ];

                    } else {
                        $model->addError('apidefactoform-invoice', $apiResponse['errors']);
                    }
                } else {
                    $model->addError('apidefactoform-invoice', Yii::t('other/api-de-facto/errors', 'When working API unknown error occurred'));
                }

//                $errors = ActiveForm::validate($model);
                $errors = $model->getErrors();
                return [
                    'success' => (empty($errors) ? '1' : '0'),
                    'errors' => $errors,
                    'redirect' => 'ok',
//                    'expectedQty' => $expectedQty,
//                    'inboundModelID' => $inboundModelID,
                ];

            } else {
                $errors = ActiveForm::validate($model);
                return [
                    'success' => (empty($errors) ? '1' : '0'),
                    'errors' => $errors
                ];

            }
        }

        return $this->renderAjax('get-inbound-order-form', ['model' => $model]);
    }

    /*
    * Form for get inbound order
    *
    * */
    public function actionGetCrossDockOrderForm()
    {
        $model = new ApiDeFactoForm();
        $model->scenario = 'CrossDock';

//        $storeArray = TLHelper::getStockPointArray();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $errors = [];
            if ($model->validate()) {

                $api = new DeFactoSoapAPI();

                $expectedQty = 0;
                $inboundModelID = 0;
                $x = [];
                if ($apiResponse = $api->getInboundOrderByInvoice($model->invoice)) {

                    /*
                        0 => '219402'         BelgeId
                        1 => 'D'              IrsaliyeSeri
                        2 => '125422'         IrsaliyeNo
                        3 => '9000002064865'  Barkod
                        4 => '1597703'        BarkodId
                        5 => '330'            DepoId
                        6 => '289'            KarsiDepoId
                        7 => '1581955'        KoliId
                        8 => '01658141'       KoliKapatmaBarkod
                        9 => 'C'              CrossDock
                        10 => '1612411'       UrunId
                        11 => 'D7647AG'       KisaKod
                        12 => '1'             Miktar
                    */

                    if (empty($apiResponse['errors'])) {

                        $apiData = $apiResponse['response'];

                        // Save to Inbound Order Logging
                        $client_id = 2; // DeFacto;
                        $unique_key = time();
                        $order_number = $model->invoice;
                        $dataProvider = [];
                        $messages = '';
                        $updateStatus = 0;
                        $success = 1;

                        if (!empty($apiData) && is_array($apiData)) {
                            $x = [];
                            $rpt = [];
//                            $zxc = [];


//                            yii\helpers\VarDumper::dump($apiData,10,true);
//                            die('-actionGetCrossDockOrderForm-');
                            $logTime = $order_number.'-'.time();
                            $head =  'BelgeId'.';'
                                .'IrsaliyeSeri'.';'
                                .'Barkod'.';'
                                .'BarkodId'.';'
                                .'DepoId'.';'
                                .'KarsiDepoId'.';'
                                .'KoliId'.';'
                                .'KoliKapatmaBarkod'.';'
                                .'CrossDock'.';'
                                .'UrunId'.';'
                                .'KisaKod'.';'
                                .'Miktar'.';'
                                .'Desi'.';'
                                .'NetAgirlik'.';'
                                .'BrutAgirlik'.';';
                            file_put_contents('cross-dock-'.$logTime.'.log',$head."\n",FILE_APPEND);

                            $fieldExtraItemProducts = [];

                            foreach ($apiData as $key => $inboundRow) {
                                if ($inboundRow['CrossDock'] == 'C') {

                                    $tofileRow =  $inboundRow['BelgeId'].';'
                                        .$inboundRow['IrsaliyeSeri'].';'
                                        .$inboundRow['Barkod'].';'
                                        .$inboundRow['BarkodId'].';'
                                        .$inboundRow['DepoId'].';'
                                        .$inboundRow['KarsiDepoId'].';'
                                        .$inboundRow['KoliId'].';'
                                        .$inboundRow['KoliKapatmaBarkod'].';'
                                        .$inboundRow['CrossDock'].';'
                                        .$inboundRow['UrunId'].';'
                                        .$inboundRow['KisaKod'].';'
                                        .$inboundRow['Miktar'].';'
                                        .$inboundRow['Desi'].';'
                                        .$inboundRow['NetAgirlik'].';'
                                        .$inboundRow['BrutAgirlik'].';'
                                    ;
                                    file_put_contents('cross-dock-'.$logTime.'.log',$tofileRow."\n",FILE_APPEND);

                                    $fieldExtraItemProducts[$inboundRow['KoliKapatmaBarkod']][] = $inboundRow;


                                    if(!isset($x[$inboundRow['KarsiDepoId']][$inboundRow['KoliId']])) {
                                        $x[$inboundRow['KarsiDepoId']][$inboundRow['KoliId']] = [
                                            'KarsiDepoId' => $inboundRow['KarsiDepoId'],
                                            'KoliId' => $inboundRow['KoliId'],
//                                            'KoliIdQty' => 0,
                                            'Desi' => $inboundRow['Desi'],
                                            'NetAgirlik' => $inboundRow['NetAgirlik'],
                                            'BrutAgirlik' => $inboundRow['BrutAgirlik'],
                                            'KisaKod' => $inboundRow['KisaKod'],
                                            'KoliKapatmaBarkod' => $inboundRow['KoliKapatmaBarkod'],
                                        ];



//                                        if( $inboundRow['KarsiDepoId'] == 423 ) {
//                                            $zxc[$inboundRow['KoliId']] = $inboundRow['KoliId'];
//                                            file_put_contents('actionGetCrossDockOrderFormX2.log', print_r($x,true) . "\n", FILE_APPEND);
//                                        //file_put_contents('actionGetCrossDockOrderFormX.log', $inboundRow['KoliId'] .' '.count($zxc) . "\n", FILE_APPEND);
//                                        }
                                    }
//                                    file_put_contents('actionGetCrossDockOrderFormXXX.log', $inboundRow['KoliId'] .' '.$inboundRow['KarsiDepoId'] .' '.$inboundRow['Desi']. "\n", FILE_APPEND);
//                                    if(isset($x[$inboundRow['KarsiDepoId']][$inboundRow['KoliId']])) {
//                                        $x[$inboundRow['KarsiDepoId']][$inboundRow['KoliId']]['KoliIdQty'] += 1;
//                                    }
                                } else {

                                    if(!isset($rpt[$inboundRow['KoliId']])) {
                                        $rpt[$inboundRow['KoliId']] = [
                                            'KoliId' => $inboundRow['KoliId'],
                                        ];
                                    }
                                }
                            }

//                            yii\helpers\VarDumper::dump($x,10,true);
//                            die('-- YPA --');
//                            file_put_contents('actionGetCrossDockOrderForm.log',"\n\n",FILE_APPEND);
//                            file_put_contents('actionGetCrossDockOrderForm.log',"\n\n",FILE_APPEND);
//                            file_put_contents('actionGetCrossDockOrderForm.log',$order_number,FILE_APPEND);
//                            file_put_contents('actionGetCrossDockOrderForm.log',"\n\n",FILE_APPEND);
                            // чистим память


                            $countRPT = count($rpt);

                            unset($apiData,$rpt);
//                            $countRow = 0;
//                            $crossDockLogData = [];

                            foreach($x as $kX=>$vX) {
                                if(is_array($vX)) {
                                    foreach($vX as $row) {

//                                        $weightNetSum += $row['NetAgirlik'];
//                                        $weightBrutSum += $row['BrutAgirlik'];
//                                        file_put_contents('actionGetCrossDockOrderForm.log',count($vX).' '.$row['Desi'].' '.$row['KisaKod']."\n",FILE_APPEND);
//
//                                        if($m3 = BarcodeManager::getBoxM3($row['Desi'])) {
//                                            $boxM3Sum += $m3;
//                                        } else {
//                                            $boxM3Sum += 0.096;
//                                            file_put_contents('actionGetCrossDockOrderForm.log',count($vX)." = DEFAULT 0.096 = ".' '.$row['Desi'].' '.$row['KisaKod'],FILE_APPEND);
//                                        }

                                        // START
                                        $cd = new CrossDockLog();
                                        $cd->client_id = $client_id; // +
                                        $cd->unique_key = $unique_key; // +
                                        $cd->party_number = $order_number; // +
                                        //S: Find
                                        $to_point_id = 0;
                                        if ($point = Store::find()->where(['client_id' => $client_id])->andWhere('shop_code = :shop_code OR shop_code2 = :shop_code2',[':shop_code' => $kX,':shop_code2' => $kX])->one()) {
                                            $cd->to_point_id = $point->id; // +
//                                            $to_point_id =  $point->id;
                                        }
                                        //E: Find
                                        $cd->to_point_title = $kX; // +
                                        $cd->weight_net = $row['NetAgirlik']; // +
                                        $cd->weight_brut = $row['BrutAgirlik']; // +
                                        $cd->box_m3 = $row['Desi']; // +
                                        $cd->box_barcode = $row['KoliKapatmaBarkod']; // +
                                        $cd->expected_rpt_places_qty = $countRPT; // +
                                        $cd->field_extra = yii\helpers\Json::encode($fieldExtraItemProducts[$row['KoliKapatmaBarkod']]); // +
                                        $cd->save(false);

//                                        $crossDockLogData [] = [
//                                            $client_id,
//                                            $unique_key,
//                                            $order_number,
//                                            $to_point_id,
//                                            $kX,
//                                            $row['NetAgirlik'],
//                                            $row['BrutAgirlik'],
//                                            $row['Desi'],
//                                            $row['KoliKapatmaBarkod'],
//                                            $countRPT
//                                        ] ;
                                        // END

//                                        $countRow++;
//                                        if($countRow == 50) {
//                                            Yii::$app->db->createCommand()->batchInsert(CrossDockLog::tableName(), [
//                                                'client_id',
//                                                'unique_key',
//                                                'party_number',
//                                                'to_point_id',
//                                                'to_point_title',
//                                                'weight_net',
//                                                'weight_brut',
//                                                'box_m3',
//                                                'box_barcode',
//                                                'expected_rpt_places_qty',
//                                            ], $crossDockLogData)->execute();
//
//                                            $countRow = 0;
//                                            $crossDockLogData = [];
//                                        }
                                    }

//                                    file_put_contents('actionGetCrossDockOrderForm.log',count($vX)." = ".$boxM3Sum,FILE_APPEND);
//                                    file_put_contents('actionGetCrossDockOrderForm.log',"\n\n",FILE_APPEND);

                                }

/*                                $cd = new CrossDockLog();
                                $cd->client_id = $client_id;
                                $cd->unique_key = $unique_key;
                                $cd->party_number = $order_number;

                                //S: Find
                                if ($point = Store::find()->where(['client_id' => $client_id])->andWhere('shop_code = :shop_code OR shop_code2 = :shop_code2',[':shop_code' => $kX,':shop_code2' => $kX])->one()) {
                                    $cd->to_point_id = $point->id;
                                }
                                //E: Find

                                $cd->to_point_title = $kX;
                                $cd->weight_net = $weightNetSum;
                                $cd->weight_brut = $weightBrutSum;
                                $cd->box_m3 = $boxM3Sum;
                                $cd->expected_number_places_qty = count($vX);
                                $cd->expected_rpt_places_qty = count($rpt);
                                $cd->save(false);*/
                            }

                            $query = CrossDockLog::find()->where(['client_id' => $client_id, 'unique_key' => $unique_key]);//->asArray()->all();
                            $dataProvider = new yii\data\ActiveDataProvider([
                                'query' => $query,
                                'pagination' => false,
                                'sort' => false,
                            ]);

                            $ioOrderNumber = CrossDockLog::find()->select('party_number')->where(['client_id' => $client_id, 'unique_key' => $unique_key])->scalar();

//                            if ($io = CrossDock::findOne(['client_id' => $client_id, 'party_number' => $ioOrderNumber])) {
                            $io = ConsignmentCrossDock::find()
                                    ->andWhere(['client_id' => $client_id, 'party_number' => $ioOrderNumber])
                                    ->andWhere('false != party_number')
                                    ->one();
                            if ($io) {
                                if (in_array($io->status, [
                                    Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST,
                                    Stock::STATUS_CROSS_DOCK_COMPLETE,
                                ])
                                ) {
                                    $messages = Yii::t('inbound/messages', 'Накладная с номером {order-number} уже принимается, обновить нельзя', ['order-number' => $io->party_number]);
                                    $updateStatus = 0;
                                } else {
                                    $messages = Yii::t('inbound/messages', 'Накладная с номером {order-number} уже есть в системе, обновить?', ['order-number' => $io->party_number]);
                                    $updateStatus = 1;
                                }
                            } else {
                                $messages = Yii::t('inbound/messages', 'Подтвердите загрузку {order-number} в системе', ['order-number' => $model->invoice]);
                                $updateStatus = 1;
                            }
                        } else {
                            $messages = Yii::t('inbound/messages', 'Накладная с номером {order-number} не найдена, нет данных для загрузки', ['order-number' => $model->invoice]);
                            $updateStatus = 0;
                        }

                        return [
                            'gridData' => $this->renderAjax('upload-log-grid-cross-dock', [
                                'dataProvider' => $dataProvider,
                                'unique_key' => $unique_key,
                                'client_id' => $client_id,
                                'messages' => $messages,
                                'updateStatus' => $updateStatus,
                            ]),
                            'errors' => $errors,
                            'success' => $success,
                        ];

                    } else {
                        $model->addError('apidefactoform-invoice', $apiResponse['errors']);
                    }
                } else {
                    $model->addError('apidefactoform-invoice', Yii::t('other/api-de-facto/errors', 'When working API unknown error occurred'));
                }

//                $errors = ActiveForm::validate($model);
                $errors = $model->getErrors();
                return [
                    'success' => (empty($errors) ? '1' : '0'),
                    'errors' => $errors,
                    'redirect' => 'ok',
//                    'expectedQty' => $expectedQty,
//                    'inboundModelID' => $inboundModelID,
                ];

            } else {
                $errors = ActiveForm::validate($model);
                return [
                    'success' => (empty($errors) ? '1' : '0'),
                    'errors' => $errors
                ];

            }
        }

        return $this->renderAjax('get-cross-dock-order-form', ['model' => $model]);
    }

    /*
     * Cross dock confirm
     *
     * */
    public function actionCrossDockConfirm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $unique_key = Yii::$app->request->post('unique_key');
        $client_id = Yii::$app->request->post('client_id');

        $arrayToSaveCSVFile = CrossDockLog::find()->where(['client_id' => $client_id, 'unique_key' => $unique_key])->asArray()->all();

        if (!empty($arrayToSaveCSVFile) && is_array($arrayToSaveCSVFile)) {

            if (isset($arrayToSaveCSVFile['0']['client_id']) && isset($arrayToSaveCSVFile['0']['party_number'])) {
                $cCD = ConsignmentCrossDock::findOne([
                    'client_id' => $arrayToSaveCSVFile['0']['client_id'],
                    'party_number' => $arrayToSaveCSVFile['0']['party_number'],
                ]);

                if ($cCD) {

                    $cCD->expected_rpt_places_qty = 0;
                    $cCD->expected_number_places_qty = 0;
                    $cCD->save(false);

                    CrossDock::updateAll([
                        'weight_net' => 0,
                        'weight_brut' => 0,
                        'box_m3' => 0,
                        'expected_number_places_qty' => 0,
                    ], [
                        'consignment_cross_dock_id' => $cCD->id
                    ]);

                    $crossDockIDs = CrossDock::find()->select('id')->where(['consignment_cross_dock_id' => $cCD->id])->column();
                    $crossDockItemsIDs = CrossDockItems::find()->select('id')->where(['cross_dock_id' => $crossDockIDs])->column();
                    CrossDockItems::deleteAll(['cross_dock_id' => $crossDockIDs]);
                    CrossDockItemProducts::deleteAll(['cross_dock_item_id' => $crossDockItemsIDs]);
                }

//            yii\helpers\VarDumper::dump($arrayToSaveCSVFile['0']['client_id'],10,true);
//            echo "<br />";
//            yii\helpers\VarDumper::dump($arrayToSaveCSVFile['0']['party_number'],10,true);
//            yii\helpers\VarDumper::dump($CrossDockIDs,10,true);
//            die('-actionCrossDockConfirm-');
            }

            foreach($arrayToSaveCSVFile as $item) {

                if(!($cCD = ConsignmentCrossDock::findOne([
                    'client_id' => $item['client_id'],
                    'party_number' => $item['party_number'],
                ]))) {
                    $cCD = new ConsignmentCrossDock();
                    $cCD->client_id = $item['client_id'];
                    $cCD->party_number = $item['party_number'];
                    $cCD->expected_rpt_places_qty = $item['expected_rpt_places_qty'];
                    $cCD->expected_number_places_qty = 0;
                }

                $cCD->expected_number_places_qty += 1;
                $cCD->save(false);

                if (!($cd = CrossDock::findOne([
                                                'client_id' => $item['client_id'],
                                                'party_number' => $item['party_number'],
                                                'to_point_title' => $item['to_point_title'],
                ]))) {
                    $cd = new CrossDock();
                    $cd->consignment_cross_dock_id = $cCD->id;
                    $cd->client_id = $item['client_id'];
                    $cd->party_number = $item['party_number'];
                    $cd->to_point_title = $item['to_point_title'];
                    $cd->to_point_id = $item['to_point_id'];
                    $cd->from_point_id = '4'; // НАШ склад ;
                    $cd->from_point_title = $item['to_point_id'];
                    $cd->weight_net = 0;
                    $cd->weight_brut = 0;
                    $cd->box_m3 = 0 ;
                    $cd->expected_number_places_qty = 0;
                }
                $cd->status = Stock::STATUS_CROSS_DOCK_NEW;

                if($m3 = BarcodeManager::getBoxM3($item['box_m3'])) {
                    $boxM3 = $m3;
                } else {
                    $boxM3 = 0.096;
                }

                $cd->expected_number_places_qty += 1;
                $cd->weight_net += $item['weight_net'];
                $cd->weight_brut +=  $item['weight_brut'];
                $cd->box_m3 += $boxM3;

                $cd->save(false);

                $crossDockItem = new CrossDockItems();
                $crossDockItem->cross_dock_id = $cd->id;
                $crossDockItem->box_barcode = $item['box_barcode'];
                $crossDockItem->expected_number_places_qty = 1;
                $crossDockItem->box_m3 = $boxM3;
                $crossDockItem->weight_net = $item['weight_net'];
                $crossDockItem->weight_brut = $item['weight_brut'];
                $crossDockItem->save(false);


                if(!empty($item['field_extra'])) {
                    try {
                        $itemProducts = yii\helpers\Json::decode($item['field_extra']);
                    } catch (yii\base\InvalidParamException $e) {
                        file_put_contents('json-decode-cross-dock.log',$item['field_extra']."\n",FILE_APPEND);
                    }

                    if(!empty($itemProducts) && is_array($itemProducts)) {
                        foreach($itemProducts as $product) {
                            $cdItemProduct = new CrossDockItemProducts();
                            $cdItemProduct->cross_dock_item_id = $crossDockItem->id;
                            $cdItemProduct->product_barcode = $product['Barkod'];
                            $cdItemProduct->expected_qty = $product['Miktar'];
                            $cdItemProduct->product_model = $product['KisaKod'];
                            $cdItemProduct->save(false);
                        }
                    }
                }
            }
        }

        return [
            'ok' => $arrayToSaveCSVFile,
        ];
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
            } else {
                $errors = $model->getErrors();
                return [
                    'success' => (empty($errors) ? '1' : '0'),
                    'errors' => $errors
                ];
            }
        }
        // FOR TEST
        // 10940537

        return $this->renderAjax('get-outbound-order-form', ['model' => $model]);
    }

    /*
     * Get outbound data by part number
     *
     * */

//    10940509

// 10995677
}
