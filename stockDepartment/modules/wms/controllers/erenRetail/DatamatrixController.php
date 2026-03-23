<?php

namespace app\modules\wms\controllers\erenRetail;

use stockDepartment\modules\wms\models\erenRetail\InboundDataMatrix;
use common\models\ActiveRecord;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundUploadLog;
use common\modules\kpiSettings\models\KpiSetting;
use common\modules\stock\models\Stock;
use common\modules\inbound\models\InboundOrderItem;

use common\overloads\ArrayHelper;
use stockDepartment\modules\inbound\models\LoadFromDeFactoAPIForm;
use common\modules\client\models\Client;

use stockDepartment\modules\wms\models\erenRetail\form\DatamatrixForm;
use stockDepartment\modules\wms\models\erenRetail\form\InboundForm;
use Yii;
use stockDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\httpclient\Client as HttpClient;

//use yii\helpers\VarDumper;


class DatamatrixController extends Controller
{
    public function actionIndex()
    {
        $inboundForm = new DatamatrixForm();
        $client_id = Client::CLIENT_ERENRETAIL;
        $inboundForm->client_id = $client_id;
		$clientHttp = new HttpClient();
		$newUserResponse = $clientHttp->get('http://wms20.local/wms/erenRetail/scan-data-matrix-api/index')->send();

        return $this->render('index', [
            'inboundForm' => $inboundForm,
            'clientsArray' => $newUserResponse->getData()["clientsArray"],
            'partyNumberArray' => $newUserResponse->getData()["partyNumberArray"],
//            'partyNumberArray' => $partyNumberArray,
        ]);
    }

    /*
     * Get inbound orders in status new and in process by client
     * @param integer client_id
     * @return JSON
     * */
    public function actionGetInProcessInboundOrdersByClientId()
    {
        $clientID = Yii::$app->request->post('client_id');
        $type = '';
        $data = ['' => ''];
        if($cio =  ConsignmentInboundOrders::getNewAndInProcessItemByClientID($clientID)) {
            $data += $cio;
            $type = 'party-inbound';
        } else {
            $data += InboundOrder::getNewAndInProcessItemByClientID($clientID);
            $type = 'inbound';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'Success',
            'type' => $type,
            'dataOptions' => $data,
        ];
    }

     /*
     * Get inbound orders in status new and in process by party
     * @param integer client_id
     * @return JSON
     * */
    public function actionGetInProcessInboundOrdersByPartyId()
    {
        $party_id = Yii::$app->request->post('party_id');

		$clientHttp = new HttpClient();
		$newUserResponse = $clientHttp->post(
			'http://wms20.local/wms/erenRetail/scan-data-matrix-api/get-in-process-inbound-orders-by-party-id',
			['party_id' => $party_id]
		)->send();

		Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'Success',
            'dataOptions' => $newUserResponse->getData()['dataOptions'],
            'expectedQtyParty'=>$newUserResponse->getData()['expectedQtyParty'] + 10,
            'acceptedQtyParty'=>$newUserResponse->getData()['acceptedQtyParty'],
        ];
    }

    /*
     * Get inbound order in status complete by client
     * @param integer client_id
     * @return JSON
     * */
    public function actionGetCompleteInboundOrdersByClientId()
    {
        $clientID = Yii::$app->request->post('client_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = ['' => ''];
        $data += InboundOrder::getCompleteOrderByClientID($clientID);
        return [
            'message' => 'Success',
            'dataOptions' => $data,
        ];
    }

    /*
     * Get scanned products by inbound order id
     *
     * */
    public function actionGetScannedProductById()
    {
        $id = Yii::$app->request->post('inbound_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $countScannedProductInOrder = InboundOrder::getCountItemByID($id);
        $items = [];
        $timer = '0';
        if( $io = InboundOrder::findOne($id)) {
            $items = $io->getOrderItems()->orderBy([
                'accepted_qty'=>SORT_ASC
            ])->asArray()->all();

//            $timer = KpiSetting::getInboundScanningTime($io->client_id, $io->expected_qty - $countScannedProductInOrder);
        }


        return [
            'message' => 'Success',
            'countScannedProductInOrder' => $countScannedProductInOrder,
            'expected_qty' => $io->expected_qty,
            'cdTimer' => $timer,
            'items' =>$this->renderPartial('_order_items',['items'=>$items]),
        ];
    }

    /*
    * Validate scanned box
    * @return JSON true or errors array
    * */
//    public function actionValidateScannedBox()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        $model = new InboundForm();
////        $model->scenario = 'ScannedBox';
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//
//            return [
//                'success' => '1',
//                'countProductInBox'=>InboundOrderItem::getScannedProductInBox($model->box_barcode,$model->order_number),
//            ];
//        } else {
//            $errors = ActiveForm::validate($model);
//            return [
//                'success'=>(empty($errors) ? '1' : '0'),
//                'errors' => $errors
//            ];
//        }
//    }

    /*
    * Scanned product in box
    * @return JSON true or errors array
    * */
    public function actionScanProductInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $expected_qty = 0;
        $model = new DatamatrixForm();
        $model->scenario = 'ScannedProduct';

//        VarDumper::dump(Yii::$app->request->post(),10,true);
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {
			$idm = InboundDataMatrix::find()
							 ->andWhere([
							 	'inbound_id' => $model->order_number,
							 	'product_barcode' => $model->product_barcode,
								 'print_status' => InboundDataMatrix::PRINT_STATUS_NO,
							 ])
							 ->one();
//			VarDumper::dump($idm,10,true);
//			die("--------------------");
			$idm->print_status = InboundDataMatrix::PRINT_STATUS_YES;
			$idm->save(false);

			//---------------------- begin
			$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('wms 8d.com');
			$pdf->SetTitle('wms 8d 3PL labels');
			$pdf->SetSubject('wms 8d 3PL labels');
			$pdf->SetKeywords('wms 8d.com, receipt, box, label');

// remove default header/footer
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//set margins
			$pdf->SetMargins(2, 2, 2, true);
//set auto page breaks
			$pdf->SetAutoPageBreak(false, 0);
//set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//
			$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

				$pdf->AddPage('L','NOMADEX40X60', true);
//				$pdf->AddPage('L','CUSTOM_SIZE40x60', true);

				$productStyle = $idm->product_model;
				$pdf->SetFont('dejavusans', 'B', 7);
				$pdf->MultiCell(0, 0,$productStyle , 0, 'C',false,1, 0,1);
				$style = array(
					'border' => false,
					'padding' => 0,
					'fgcolor' => array(0,0,0),
					'bgcolor' => false
				);
				$productBarcode = $idm->product_barcode;
				$dmCode = $idm->data_matrix_code;
				$pdf->write2DBarcode($dmCode, 'DATAMATRIX', 34, 5, 40, 40, $style, "N");

				$pdf->SetFont('dejavusans', 'N', 7);
				$pdf->Text(0, 5, $productBarcode);

				$dmCodeSplit = str_split($dmCode, 17);

				$pdf->Text(0, 22, $dmCodeSplit[0]);
				$pdf->Text(0, 24, $dmCodeSplit[1]);

			$fileName = Yii::getAlias('@stockDepartment').'/web/resources/datamatrix_'.$productBarcode. '-'.$idm->id . '.pdf';
//			$pdf->Output($fileName, 'D');
			$pdf->Output($fileName, 'F');
			//---------------------- end

			$pdf = $fileName;
			// https://www.sumatrapdfreader.org/docs/Command-line-arguments
			$printerName = Yii::$app->params['printerName']; // "Xprinter_XP-365B";
			$printerOrientation = Yii::$app->params['printerOrientation']; // "Xprinter_XP-365B";
			$command = "SumatraPDF -print-to \"$printerName\" -exit-when-done -print-settings \"$printerOrientation,fit\" $pdf";
//			$command = "SumatraPDF -print-to \"$printerName\" -exit-when-done -print-settings \"portrait,paper=statement\" $pdf";

//			VarDumper::dump($pdf,10,true);
//			VarDumper::dump($printerName,10,true);
//			VarDumper::dump($printerOrientation,10,true);
//			VarDumper::dump($command,10,true);
//			die("--------------------");
			exec($command);

//			InboundDataMatrix::updateAll([
//				'box_barcode' => '',
//				'outbound_order_id' => '0',
//				'outbound_picking_list_id' => '0',
//				'outbound_picking_list_barcode' => '',
//				'status' => InboundDataMatrix::PRINT_STATUS_YES,
////                'status' => Stock::STATUS_NOT_SET,
//				'status_availability' => Stock::STATUS_AVAILABILITY_YES
//			], ['inbound_id' => $model->order_number,'status' => InboundDataMatrix::PRINT_STATUS_NO]);


//            Stock::setStatusInboundScannedValue($model->order_number,
//				$model->product_barcode,
//				$model->box_barcode,
//				$ioi->product_model,
//				$ioi->product_name
//			);

//           $countStockForItem =  Stock::find()->where([
//                                                        'inbound_order_id' => $model->order_number,
//                                                        'product_barcode' => $model->product_barcode,
//                                                        'status' => Stock::STATUS_INBOUND_SCANNED,
//                                                        'client_id' => $model->client_id,
//           ])->count();

//            if ( $ioi) {
////
//                if(intval($ioi->accepted_qty) < 1) {
//                    $ioi->begin_datetime = time();
//                    $ioi->status = Stock::STATUS_INBOUND_SCANNING;
//                }
//
////                $ioi->accepted_qty += 1;
//                $ioi->accepted_qty = $countStockForItem;
//
//                if($ioi->accepted_qty == $ioi->expected_qty) {
//                    $ioi->status = Stock::STATUS_INBOUND_SCANNED;
//                }
//
//                $ioi->end_datetime = time();
//                $ioi->save(false);
////
//            } else {}

//            Stock::setStatusInboundScannedValue($model->order_number,$model->product_barcode,$model->box_barcode,$ioi->product_model);

//            $countStockForOrder =  Stock::find()->where([
//                'inbound_order_id' => $model->order_number,
//                'status' => Stock::STATUS_INBOUND_SCANNED,
//                'client_id' => $model->client_id,
//            ])->count();

//            if($inboundModel = InboundOrder::findOne($model->order_number)) {
//
//                if(intval($inboundModel->accepted_qty) < 1) {
//                    $inboundModel->begin_datetime = time();
//                    $inboundModel->status = Stock::STATUS_INBOUND_SCANNING;
//                }
//
////                $inboundModel->accepted_qty += 1;
//                $inboundModel->accepted_qty = $countStockForOrder;
//
//                if( $inboundModel->accepted_qty == $inboundModel->expected_qty) {
//                    $inboundModel->status = Stock::STATUS_INBOUND_SCANNED;
//                }
//
//                $inboundModel->end_datetime = time();
//                $inboundModel->save(false);
//
//                $expected_qty = $inboundModel->expected_qty;
//            }

            //S: PARTY
//            $expectedQtyParty = 0;
//            $acceptedQtyParty = 0;
//            if($coi = ConsignmentInboundOrders::findOne($inboundModel->consignment_inbound_order_id)) {
//
//                $inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inboundModel->consignment_inbound_order_id])->asArray()->column();
//
//                $countStockForConsignment =  Stock::find()->where([
//                    'inbound_order_id' => $inboundIDs,
//                    'status' => Stock::STATUS_INBOUND_SCANNED,
//                    'client_id' => $model->client_id,
//                ])->count();
//
//                $coi->accepted_qty = $countStockForConsignment;
////                $coi->accepted_qty += 1;
//                $coi->save(false);
//                $expectedQtyParty = $coi->expected_qty;
//                $acceptedQtyParty = $coi->accepted_qty;
//            }
            //E: PARTY


            $colorRowClass = 'alert-danger';
//            if( $ioi->accepted_qty == $ioi->expected_qty) {
//                $colorRowClass = 'alert-success';
//            }elseif($ioi->accepted_qty > 0) {
//                $colorRowClass = 'alert-warning';
//            }

            return [
                'success' => (empty($errors) ? '1' : '0'),
                'countProductInBox'=>0,
                'countScannedProductInOrder'=>0,
                'expectedQtyParty'=>0,
                'acceptedQtyParty'=>0,
                'expected_qty'=> $expected_qty,
//                'xxxxx'=> $countStockForItem,
                'dataScannedProductByBarcode'=> [
                    'rowId'=>'0'.'-'.$model->product_barcode,
                    'expected_qty'=> 0,
                    'countValue'=> 0,
                    'colorRowClass'=> $colorRowClass
                ],
            ];
        } else {
            $errors = ActiveForm::validate($model);
            return [
                'success' => (empty($errors) ? '1' : '0'),
                'errors' => $errors
            ];
        }
    }



    /*
     * Print the list of differences
     * */
    public function actionPrintListDifferences()
    {
        $id = Yii::$app->request->get('inbound_id');

        $items = [];
        if( $io = InboundOrder::findOne($id)) {
            $items = $io->getOrderItems()
                ->orderBy([
                    'accepted_qty'=>SORT_ASC
                ])
                ->asArray()
                ->all();
        }
        if($this->printType == 'html'){
            Yii::$app->layout = 'print-html';
            return $this->render('print/list-differences-html',['items'=>$items]);
        }
        return $this->render('print/list-differences-pdf',['items'=>$items]);
    }

    /*
    * Confirm inbound order data
    * @return JSON true or errors array
    * */
    public function actionConfirmOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];

        $model = new InboundForm();
        $model->scenario = 'ConfirmOrder';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($io = InboundOrder::findOne($model->order_number)) {

                if($io->status == Stock::STATUS_INBOUND_CONFIRM) {
                    $messages [] = Yii::t('inbound/errors','Накладная с номером ' . $io->order_number . ' уже принята');
                } else {
                    $io->status = Stock::STATUS_INBOUND_CONFIRM;
                    $io->date_confirm = time();
                    $io->save(false);

                    Stock::updateAll([
                        'status'=>Stock::STATUS_INBOUND_CONFIRM,
                        'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
                    ],[
                        'inbound_order_id'=>$io->id,
                        'status'=>[
                            Stock::STATUS_INBOUND_SCANNED,
                            Stock::STATUS_INBOUND_OVER_SCANNED,
                        ]
                    ]);


                    Stock::deleteAll('inbound_order_id = :inbound_order_id AND status != :status',[':inbound_order_id'=>$io->id,':status'=>Stock::STATUS_INBOUND_CONFIRM]);

                    $messages [] =  Yii::t('inbound/errors','Накладная с номером ' . $io->order_number . ' успешно принята');

                    if($coi = ConsignmentInboundOrders::findOne($io->consignment_inbound_order_id)) {
                        // находим все пакладные проверяем в каком они статусе, если выполнены
                        // ставим статус выполнена если нет то в процессе (принимается)
                        // почему тут сразу не посчитать общее количество принятый товаров?

                        $coi->status = Stock::STATUS_INBOUND_SCANNING;
                        if(!InboundOrder::find()->andWhere('status != :status AND consignment_inbound_order_id = :consignment_inbound_order_id',[':status'=>Stock::STATUS_INBOUND_CONFIRM,':consignment_inbound_order_id'=>$io->consignment_inbound_order_id])->exists()) {
                          $coi->status = Stock::STATUS_INBOUND_CONFIRM;
                        }
                       $coi->save(false);
                    }


                    //S: отпарвляем данные приходной накладной по API для DeFacto
/*                    if($io->client_id == 2 && YII_ENV == 'prod') { // id 2 = Defacto

                        if($items = InboundOrderItem::findAll(['inbound_order_id'=>$io->id]) ) {
                            foreach($items as $item) {
                                if($item->accepted_qty >= 1) {
                                    $rows[] = [
                                        'YurtDisiIrsaliyeNo'=>$io->order_number,
                                        'Barkod'=>$item->product_barcode,
                                        'CrossDockType'=>'P',
                                        'Miktar'=>$item->accepted_qty,
                                    ];
                                }
                            }
                        }

                        if(!empty($rows)) {
                            $urunOnKabulTamamlandiResult = [];
                            $api = new DeFactoSoapAPI();
                            $api->confirmInboundOrder($rows);

                            $urunOnKabulTamamlandiResult = $api->getUrunOnKabulTamamlandiInbound($io->order_number);

                            $extraFields = [];
                            if($io->extra_fields) {
                                $extraFields = Json::decode($io->extra_fields);
                            }
//
                            $extraFields['UrunOnKabulSend'] = $rows;
                            $extraFields['UrunOnKabulTamamlandiResultRespons'] = $urunOnKabulTamamlandiResult;
                            $io->extra_fields = Json::encode($extraFields);
                            $io->status = Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API;
                            $io->save(false);
                        }
                    }*/
                    //E: отпарвляем данные приходной накладной по API для DeFacto

                }
            } else {
                // TODO сделать уведомление на почту
            }
        } else {
            $errors = ActiveForm::validate($model); //TODO Нет обработчика на стороне клиента, т.е. ошибки не выводятся
        }

        return [
            'success'=>'OK',
            'errors'=>$errors,
            'messages'=>$messages,
        ];
    }

    /*
     * Delete product by barcode  in box
     * @return JSON true or errors array
     * */
    public function actionClearProductInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $countProductInBox = 0;
        $countValue = 0;
        $colorRowClass = '';
        $rowId = '';
        $expected_qty = 0;
        //S: PARTY
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;
        //E: PARTY

        $model = new InboundForm();
        $model->scenario = 'ClearProductInBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

//			VarDumper::dump($model,10,true);
//			die;
			Stock::deleteAll(                              [
				'primary_address'=>$model->box_barcode,
				'product_barcode'=>$model->product_barcode,
				'inbound_order_id'=>$model->order_number,
				'status'=>[
					Stock::STATUS_INBOUND_SCANNED,
					Stock::STATUS_INBOUND_OVER_SCANNED
				]
			]);

//                Stock::findAndUpdate([
//                                    'status'=> Stock::STATUS_INBOUND_SCANNING
//                                    ,'primary_address'=>''
//                                    ],
//                                    [
//                                        'primary_address'=>$model->box_barcode,
//                                        'product_barcode'=>$model->product_barcode,
//                                        'inbound_order_id'=>$model->order_number,
//                                        'status'=>[
//                                            Stock::STATUS_INBOUND_SCANNED,
//                                            Stock::STATUS_INBOUND_OVER_SCANNED
//                                        ]
//                                    ]
//                );


            $countStockForItem =  Stock::find()->where([
                'inbound_order_id' => $model->order_number,
                'product_barcode' => $model->product_barcode,
                'status' => Stock::STATUS_INBOUND_SCANNED,
            ])->count();

                if($ioi =  InboundOrderItem::findOne(['product_barcode'=>$model->product_barcode,'inbound_order_id'=>$model->order_number])) {

//                    $ioi->accepted_qty -= 1;
                    $ioi->accepted_qty = $countStockForItem;
                    $ioi->status = Stock::STATUS_INBOUND_SCANNING;
                    $ioi->save(false);

                    $colorRowClass = 'alert-danger';
                    if( $ioi->accepted_qty == $ioi->expected_qty) {
                        $colorRowClass = 'alert-success';
                    }elseif($ioi->accepted_qty > 0) {
                        $colorRowClass = 'alert-warning';
                    }

                    $countValue = $ioi->accepted_qty;
                    $rowId = $ioi->id.'-'.$model->product_barcode;
                };

            $countStockForOrder =  Stock::find()->where([
                'inbound_order_id' => $model->order_number,
                'status' => Stock::STATUS_INBOUND_SCANNED,
            ])->count();

                if($inbound = InboundOrder::findOne($model->order_number)) {
                   $inbound->status = Stock::STATUS_INBOUND_SCANNING;
//                   $inbound->accepted_qty -= 1;
                   $inbound->accepted_qty = $countStockForOrder;
                   $inbound->save(false);

                   $expected_qty = $inbound->expected_qty;

                    //S: PARTY
//                    if($coi = ConsignmentInboundOrders::findOne($inbound->consignment_inbound_order_id)) {

//                        $inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inbound->consignment_inbound_order_id])->asArray()->column();

//                        $countStockForConsignment =  Stock::find()->where([
//                            'inbound_order_id' => $inboundIDs,
//                            'status' => Stock::STATUS_INBOUND_SCANNED,
//                        ])->count();

//                        $coi->accepted_qty = $countStockForConsignment;

//                        $coi->accepted_qty -= 1;
//                        $coi->save(false);

                        $expectedQtyParty = $inbound->expected_qty;
                        $acceptedQtyParty = $inbound->accepted_qty;
//                    }
                    //E: PARTY
                }

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors'=>$errors,
            'messages'=>$messages,
            'countProductInBox'=>InboundOrderItem::getScannedProductInBox($model->box_barcode,$model->order_number),
            'countScannedProductInOrder'=>InboundOrder::getCountItemByID($model->order_number),
            'expectedQtyParty'=>$expectedQtyParty,
            'acceptedQtyParty'=>$acceptedQtyParty,
            'expected_qty'=> $expected_qty,
            'dataScannedProductByBarcode'=> [
                'rowId'=>$rowId,
                'countValue'=> $countValue,
                'colorRowClass'=> $colorRowClass
            ],
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
        $dataScannedProductByBarcode = [];
        $product_barcode_count = 0;
        $expected_qty = 0;
        //S: PARTY
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;
        //E: PARTY

        $model = new InboundForm();
        $model->scenario = 'ClearBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($productsInBox = Stock::find()
									 ->select('count(product_barcode) as product_barcode_count, product_barcode')
									 ->where([
									 	'primary_address'=>$model->box_barcode,
										 'inbound_order_id'=>$model->order_number,
										 'status'=>[
											 Stock::STATUS_INBOUND_SCANNED,
                                             Stock::STATUS_INBOUND_OVER_SCANNED
										 ]])
									 ->groupBy('product_barcode')
									 ->all()
			) {

                foreach($productsInBox as $item) {

                    if ($ioi = InboundOrderItem::findOne([
                    	'product_barcode' => $item->product_barcode,
						'inbound_order_id' => $model->order_number,
					])) {

//                        Stock::deleteAll(['status'=>Stock::STATUS_INBOUND_SCANNING,'primary_address'=>''],
                        Stock::deleteAll(
                            [
                                'primary_address'=>$model->box_barcode,
                                'inbound_order_id'=>$model->order_number,
//                                'outbound_order_id' => '',
                                'product_barcode'=>$item->product_barcode,
                                'status'=>[
                                	Stock::STATUS_INBOUND_SCANNED,
									Stock::STATUS_INBOUND_OVER_SCANNED
								]
                            ]);

//                        $product_barcode_count += $item->product_barcode_count;
//                        $ioi->accepted_qty -= $item->product_barcode_count;
                        $countStockForItem =  Stock::find()->where([
                            'inbound_order_id' => $model->order_number,
                            'product_barcode' => $item->product_barcode,
                            'status' => Stock::STATUS_INBOUND_SCANNED,
                        ])->count();

                        $ioi->accepted_qty = $countStockForItem;
                        $ioi->save(false);

                        $colorRowClass = 'alert-danger';
                        if ($ioi->accepted_qty == $ioi->expected_qty) {
                            $colorRowClass = 'alert-success';
                        } elseif ($ioi->accepted_qty >0) {
                            $colorRowClass = 'alert-warning';
                        }

                        $countValue = $ioi->accepted_qty;
                        $rowId = $ioi->id . '-' . $item->product_barcode;

                        $dataScannedProductByBarcode [] = [
                            'rowId' => $rowId,
                            'countValue' => $countValue,
                            'colorRowClass' => $colorRowClass
                        ];
                    };
                }

                $countStockForOrder =  Stock::find()->where([
                    'inbound_order_id' => $model->order_number,
                    'status' => Stock::STATUS_INBOUND_SCANNED,
                ])->count();


                if($inbound = InboundOrder::findOne($model->order_number)) {
                    $inbound->status = Stock::STATUS_INBOUND_SCANNING;
//                    $inbound->accepted_qty -= $product_barcode_count;
                    $inbound->accepted_qty = $countStockForOrder;
                    $inbound->save(false);

                    $expected_qty = $inbound->expected_qty;

                    //S: PARTY
                    if($coi = ConsignmentInboundOrders::findOne($inbound->consignment_inbound_order_id)) {

                        $inboundIDs = InboundOrder::find()->select('id')->where(['consignment_inbound_order_id'=>$inbound->consignment_inbound_order_id])->asArray()->column();

                        $countStockForConsignment =  Stock::find()->where([
                            'inbound_order_id' => $inboundIDs,
                            'status' => Stock::STATUS_INBOUND_SCANNED,
                        ])->count();

                        $coi->accepted_qty = $countStockForConsignment;


//                        $coi->accepted_qty -= $product_barcode_count;
                        $coi->save(false);

                        $expectedQtyParty = $coi->expected_qty;
                        $acceptedQtyParty = $coi->accepted_qty;
                    }
                    //E: PARTY
                }

//                Stock::updateAll(['status'=>Stock::STATUS_INBOUND_SCANNING,'primary_address'=>''],
//                                ['primary_address'=>$model->box_barcode,
//                                 'inbound_order_id'=>$model->order_number,
//                                 'status'=>[Stock::STATUS_INBOUND_SCANNED,Stock::STATUS_INBOUND_OVER_SCANNED]]);
            }

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success'=>(empty($errors) ? '1' : '0'),
            'errors'=>$errors,
            'messages'=>$messages,
            'countScannedProductInOrder'=>InboundOrder::getCountItemByID($model->order_number),
            'expected_qty'=> $expected_qty,
            'dataScannedProductByBarcode'=> $dataScannedProductByBarcode,
            'expectedQtyParty'=>$expectedQtyParty,
            'acceptedQtyParty'=>$acceptedQtyParty,
        ];
    }

    /*
  * Load Data from API for only client "DeFacto"
  * @return JSON true or errors array
  * */
    public function actionUploadFromApi()
    {
        $model = new LoadFromDeFactoAPIForm();

        $model->scenario = 'UploadFileForAPI';

        $unique_key = Yii::$app->request->get('unique_key');
        $client_id = Yii::$app->request->get('client_id');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->file = UploadedFile::getInstance($model, 'file');

            if ( $model->file ) {

                $client_id = 2;// DeFacto;
                $unique_key = time();

                $dirPath = 'uploads/de-facto/inbound/'.date('Ymd').'/'.date('His');
                BaseFileHelper::createDirectory($dirPath);
                $pathToCSVFile = $dirPath.'/' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs($pathToCSVFile);

                //S: Start test load demo data
                $row = 1;
                $arrayToSaveCSVFile = [];
                if ( ($handle = fopen($pathToCSVFile, "r")) !== FALSE ) {
                    while ( ($data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
                        $row++;
                        if($row>2) {
//                            if ($data[9] == 'P') {
                                $expected_qty = isset($arrayToSaveCSVFile[$model->invoice_number][$data[3]]['expected_qty']) ? $arrayToSaveCSVFile[$model->invoice_number][$data[3]]['expected_qty'] : 0;
                                $arrayToSaveCSVFile[$model->invoice_number][$data[3]] = [
                                    'product_barcode'=>  $data[3],
                                    'product_model'=>  $data[11],
                                    'expected_qty'=>  $data[12] + $expected_qty,
                                    'delivery_type'=>  $data[9],
                                ];
//                            }
                        }
                    }
                    fclose($handle);

                    foreach($arrayToSaveCSVFile as $inboundOrder=>$productBarcode) {
                        $order_number = $inboundOrder;
                        foreach($productBarcode as $inboundRow) {

                            $iul = new InboundUploadLog();
                            $iul->client_id = $client_id;
                            $iul->unique_key = $unique_key;
                            $iul->order_number = $order_number;
//                            $iul->order_type = $inboundRow['order_type'] == 'C' ? 1 : 2; // 1 - крос-док, 2 - Склад
                            $iul->delivery_type = $inboundRow['delivery_type'] == 'C' ? InboundOrder::DELIVERY_TYPE_CROSS_DOCK : InboundOrder::DELIVERY_TYPE_RPT; // 1 - крос-док, 2 - Склад
                            $iul->product_barcode = $inboundRow['product_barcode'];
                            $iul->product_model = $inboundRow['product_model'];
                            $iul->expected_qty = $inboundRow['expected_qty'];
                            $iul->save(false);
                        }
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages', 'Не получилось загрузить файл'));
                }
                //E: Start test load demo data

                Yii::$app->getSession()->setFlash('success', Yii::t('inbound/messages', 'Файл успешно загружен'));

                return $this->redirect(['/inbound/default/upload-from-api','unique_key'=>$unique_key,'client_id'=>$client_id]);
            }
        }

        $dataProvider = [];
        $messages = '';
        $updateStatus = 0;

        if($unique_key && $client_id) {
            $query = InboundUploadLog::find()->where(['delivery_type'=>InboundOrder::DELIVERY_TYPE_RPT,'client_id'=>$client_id,'unique_key'=>$unique_key]);//->asArray()->all();
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination'=>false,
                'sort'=> false,
            ]);
           $ioOrderNumber =  InboundUploadLog::find()->select('order_number')->where(['delivery_type'=>InboundOrder::DELIVERY_TYPE_RPT,'client_id'=>$client_id,'unique_key'=>$unique_key])->scalar();

           if($io =  InboundOrder::findOne(['client_id'=>$client_id,'order_number'=>$ioOrderNumber])) {

               if ( in_array($io->status,[
                        Stock::STATUS_INBOUND_SCANNING,
                        Stock::STATUS_INBOUND_SCANNED,
                        Stock::STATUS_INBOUND_OVER_SCANNED,
                        Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API,
                        Stock::STATUS_INBOUND_COMPLETE,
                        Stock::STATUS_INBOUND_CONFIRM,
                    ])
               ) {

                   $messages = Yii::t('inbound/messages', 'Накладная с номер {order-number} уже принимается, обновить нельзя',['order-number'=>$io->order_number]);
                   $updateStatus = 0;
               } else {
                   $messages = Yii::t('inbound/messages', 'Накладная с номер {order-number} уже есть в системе, обновить?', ['order-number' => $io->order_number]);
                   $updateStatus = 1;
               }
           } else {
               $messages = Yii::t('inbound/messages', 'Подтвердите загрузку {order-number} в системе', ['order-number' => $model->invoice_number]);
               $updateStatus = 1;
           }
        }


        return $this->render('upload-from-api', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'unique_key' => $unique_key,
            'client_id' => $client_id,
            'messages' => $messages,
            'updateStatus' => $updateStatus,
        ]);
    }

    /*
     * Upload inbound confirm
     *
     * */
    public function actionUploadFileConfirm()
    {
        $unique_key = Yii::$app->request->post('unique_key');
        $client_id = Yii::$app->request->post('client_id');

        $arrayToSaveCSVFile = InboundUploadLog::find()->where(['delivery_type' => InboundOrder::DELIVERY_TYPE_RPT, 'client_id' => $client_id, 'unique_key' => $unique_key])->asArray()->all();

        if(!empty($arrayToSaveCSVFile) && is_array($arrayToSaveCSVFile)) {

            $client_id = 2;// DeFacto;
            $expectedQty = 0;
            $inboundModelID = 0;
            foreach ($arrayToSaveCSVFile as $key => $productBarcode) {

                if ($key < 1) {

                    if (!($inboundModel = InboundOrder::findOne(['client_id' => $client_id, 'order_number' => $productBarcode['order_number']]))) {
                        $inboundModel = new InboundOrder();
                    }

                    $inboundModel->client_id = $client_id;
                    $inboundModel->order_number = $productBarcode['order_number'];
                    $inboundModel->status = Stock::STATUS_INBOUND_NEW;
                    $inboundModel->expected_qty = '0';
                    $inboundModel->accepted_qty = '0';
                    $inboundModel->accepted_number_places_qty = '0';
                    $inboundModel->expected_number_places_qty = '0';
                    $inboundModel->order_type = InboundOrder::ORDER_TYPE_INBOUND;
                    $inboundModel->save(false);

                    $inboundModelID = $inboundModel->id;
                }

//                if (!($ioi = InboundOrderItem::findOne(['inbound_order_id' => $inboundModelID, 'product_barcode' => $productBarcode['product_barcode'], 'expected_qty' => $productBarcode['expected_qty']]))) {
                if (!($ioi = InboundOrderItem::findOne(['inbound_order_id' => $inboundModelID, 'product_barcode' => $productBarcode['product_barcode']]))) {
                    $ioi = new InboundOrderItem();
                }

                $ioi->inbound_order_id = $inboundModelID;
                $ioi->product_barcode = $productBarcode['product_barcode'];
                $ioi->product_model = $productBarcode['product_model'];
                $ioi->expected_qty = $productBarcode['expected_qty'];
                $ioi->status = Stock::STATUS_INBOUND_NEW;
                $ioi->save(false);

                $expectedQty += $ioi->expected_qty;

                Stock::deleteAll(['client_id' => $client_id, 'inbound_order_id' => $ioi->inbound_order_id, 'product_barcode' => $ioi->product_barcode, 'product_model' => $ioi->product_model]);

                for ($i = 1; $i <= $ioi->expected_qty; $i++) {

                    $stock = new Stock();
                    $stock->client_id = $client_id;
                    $stock->inbound_order_id = $ioi->inbound_order_id;
                    $stock->product_barcode = $ioi->product_barcode;
                    $stock->product_model = $ioi->product_model;
                    $stock->status = Stock::STATUS_INBOUND_NEW;
                    $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
                    $stock->save(false);
                }
            }

            InboundOrder::updateAll(['expected_qty' => $expectedQty], ['id' => $inboundModelID]);
        }
//        $inboundModel->expected_qty = $expectedQty;
//        $inboundModel->save(false);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'ok'=>'ok'
        ];
    }

    /*
     * Confirm Data from API for only client "DeFacto"
     * @return JSON true or errors array
     * */
    public function actionDownloadFromApi()
    {
        $model = new LoadFromDeFactoAPIForm();

        $clientsArray = Client::getActiveItems();

        $model->scenario = 'DownloadFileForAPI';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $dirPath = 'uploads/de-facto/inbound/download/'.date('Ymd').'/'.date('His');
            BaseFileHelper::createDirectory($dirPath);

            $rows[] = [
                'YurtDisiIrsaliyeNo',
                'Barkod',
                'CrossDockType',
                'Miktar',
            ];

            if($inbound = InboundOrder::findOne($model->invoice_number) ) {
                if($items = InboundOrderItem::findAll(['inbound_order_id'=>$model->invoice_number]) ) {
                    foreach($items as $item) {
                        if($item->accepted_qty >= 1) {
                            $rows[] = [
                                $inbound->order_number,
                                $item->product_barcode,
                                'P',
                                $item->accepted_qty,
                            ];
                        }
                    }
                }

//                $inbound->status = InboundOrder::STATUS_COMPLETE_BY_API;
                $inbound->status = Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API;
                $inbound->save(false);
            }

            $fileName = 'inbound-file-download-for-api-'.time().'.csv';

            $fp = fopen($dirPath.'/'.$fileName, 'w');

            foreach ($rows as $fields) {
                fputcsv($fp, $fields,';');
            }

            fclose($fp);

            return Yii::$app->response->sendFile($dirPath.'/'.$fileName);
        }

        return $this->render('download-from-api', [
            'model' => $model,
            'clientsArray' => $clientsArray,
        ]);
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

        if($model = InboundOrder::findOne($id)) {
            $model->status = Stock::STATUS_INBOUND_COMPLETE;
            $model->save(false);
        }

        return  [];
    }

    /*
     * Check order status fow show or not button complete order
     * @param $id Order
     * @return JSON
     * */
    public function actionCheckOrderStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $status = 'NO';

        if($model = InboundOrder::findOne($id)) {
            if($model->status == Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API) {
                $status = 'PREPARED-DATA-FOR-API';
            }
        }

        return  [
            'status'=> $status
        ];
    }

    /*
     *
     *
     * */
    public function actionPrintUnallocatedList()
    {
        $id = Yii::$app->request->get('inbound_id');

        $items = [];
        if($io = InboundOrder::findOne($id)) {
            $items = Stock::find()
                ->select('primary_address, secondary_address')
                ->where([
                    'inbound_order_id' => $io->id,
                    'secondary_address' => '',
                ])
                ->andWhere([
                    'not', ['primary_address'=>'']
                ])
                ->groupBy('primary_address')
                ->orderBy([
                    'secondary_address' => SORT_DESC,
                    'primary_address' => SORT_DESC,
                ])
                ->asArray()
                ->all();

        }
        if($this->printType == 'html'){
            Yii::$app->layout = 'print-html';
            return $this->render('print/print-unallocated-box-html',['items'=>$items]);
        }
        return $this->render('print/print-unallocated-box-pdf',['items'=>$items]);
    }
}