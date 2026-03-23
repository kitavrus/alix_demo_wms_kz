<?php

namespace app\modules\report\controllers;

use common\clientObject\constants\Constants;
use common\modules\client\models\Client;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\overloads\ArrayHelper;
use Yii;
use common\modules\client\models\ClientEmployees;
use app\modules\report\models\StockSearch;
use clientDepartment\components\Controller;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use clientDepartment\modules\client\components\ClientManager;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\stock\models\Stock;
use app\modules\report\models\StockInventoryForm;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use app\modules\report\models\StockRemains;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
use common\components\BarcodeManager;
use clientDepartment\modules\report\services\QtyBoxDefactoService;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class StockController extends Controller
{

    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
        $conditionTypeArray = $searchModel->getConditionTypeArray();
        return $this->render('index', [
            'conditionTypeArray' => $conditionTypeArray,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Stock model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Finds the Stock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /*
     *
     * Export data to EXEL
     *
     * */
    public function actionExportToExcel()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $data = $dataProvider->getModels();
        $conditionTypeArray = $searchModel->getConditionTypeArray();
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 CSV Test Document")
            ->setSubject("Office 2007 CSV Test Document")
            ->setDescription("Test document for Office 2007 CSV, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A'.$i, Yii::t('stock/forms', 'Product barcode')); // +
        $activeSheet->setCellValue('B'.$i, Yii::t('stock/forms', 'Product model')); // +
        $activeSheet->setCellValue('C'.$i, Yii::t('stock/forms', 'Condition type')); // +
        $activeSheet->setCellValue('D'.$i, Yii::t('stock/forms', 'Qty')); // +


        foreach($data as $model) {
            $i++;
//            $activeSheet->setCellValue('A' . $i, $model['product_barcode']);
            $activeSheet->setCellValueExplicit('A' . $i, $model['product_barcode'], \PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->setCellValue('B' . $i, $model['product_model']);
            $activeSheet->setCellValue('C' . $i, isset($conditionTypeArray[$model['condition_type']]) ? $conditionTypeArray[$model['condition_type']] : '-');
            $activeSheet->setCellValue('D' . $i, $model['qty']);

        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/DeFacto/stock/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'stock-report-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

    /*
     *
     * Export data boxes
     *
     * */
    public function actionExportBoxesToExcel()
    { // export-boxes-to-excel
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchBoxesArray(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $data = $dataProvider->getModels();

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 CSV Test Document")
            ->setSubject("Office 2007 CSV Test Document")
            ->setDescription("Test document for Office 2007 CSV, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A'.$i, Yii::t('stock/forms', 'Box barcode')); // +
        $activeSheet->setCellValue('B'.$i, Yii::t('stock/forms', 'Product barcode')); // +
        $activeSheet->setCellValue('C'.$i, Yii::t('stock/forms', 'Product model')); // +
        $activeSheet->setCellValue('D'.$i, Yii::t('stock/forms', 'Qty')); // +
		$activeSheet->setCellValue('E'.$i, Yii::t('stock/forms', 'Box address')); // +
		$activeSheet->setCellValue('F'.$i, Yii::t('stock/forms', 'SkuID')); // +
		
        $boxes = [];
        $productQty = 0;
        foreach($data as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['primary_address']);
//            $activeSheet->setCellValue('B' . $i, $model['product_barcode']);
            $activeSheet->setCellValueExplicit('B' . $i, $model['product_barcode'], \PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->setCellValue('C' . $i, $model['product_model']);
            $activeSheet->setCellValue('D' . $i, $model['qty']);
			$activeSheet->setCellValue('E' . $i, $model['secondary_address']);
			$activeSheet->setCellValue('F' . $i, $model['product_sku']);

            $boxes[$model['primary_address']] = $model['primary_address'];
            $productQty += $model['qty'];
        }

        $i++;
        $activeSheet->setCellValue('A' . $i, count($boxes));
        $activeSheet->setCellValue('D' . $i, $productQty);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/DeFacto/stock/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'export-boxes-to-excel-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

    /**
     * Displays a single Stock model.
     * @param integer $id
     * @return mixed
     */
    public function actionInventory()
    {
        $model = new StockInventoryForm();
        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        $filePath='';

        if  (Yii::$app->request->isPost) {

            $model->file = UploadedFile::getInstance($model, 'file');
            //VarDumper::dump($model->file, 10, true); die();

            if ($model->validate()) {
                //Путь сохранения загруженного файла
                $dirPath = 'uploads/' .$client->title. '/stock/'.date('Ymd').'/'.date('His');

                //Путь сохранения сгенерированного нами файла
                $outPath = 'uploads/' .$client->title. '/output/'.date('Ymd').'/';
                BaseFileHelper::createDirectory($dirPath);
                $pathToCSVFile = $dirPath.'/' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs($pathToCSVFile);

                $row = 0;
                if ( ($handle = fopen($pathToCSVFile, "r")) !== FALSE ) {
                    $parsedData=[];
                    while ( ($data = fgetcsv($handle, 1000, ";")) !== FALSE ) {

                                $parsedData[$row]['product_barcode'] =  $data[0];
                                $parsedData[$row]['product_model'] =  $data[1];
                                $parsedData[$row]['product_qty'] =  $data[2];

                        $row++;
                    }
                    fclose($handle);

                    if($parsedData){

                        foreach($parsedData as $k=>$data) {
                            // 1 строка - "шапка"
                            if($k==0){
                                $parsedData[$k]['nmdx_qty'] = 'Nmdx qty';
                                $parsedData[$k]['nmdx_diff'] = 'Difference';
                                continue;
                            }
                            $ourData = Stock::find()
                                ->select('product_barcode, count(product_barcode) as qty')
                                ->andWhere([
                                    'product_barcode' => $data['product_barcode'],
                                    'status_availability' => Stock::STATUS_AVAILABILITY_YES,
                                    'client_id' => $client->id,
                                ])
                                ->asArray()
                                ->one();
                            //Если есть разница пишем результат в таблицу
                            if($ourData['qty'] != $data['product_qty']){
                                $parsedData[$k]['nmdx_qty'] = $ourData['qty'];
                                $parsedData[$k]['nmdx_diff'] = ($data['product_qty'] - $ourData['qty']) *-1;
                            } else {
                                unset($parsedData[$k]);
                            }

                        }
                        BaseFileHelper::createDirectory($outPath);
                        $fileName = 'inventory-report-' . time() . '.csv';
                        $fh = fopen($outPath.$fileName,'w');

                        foreach ($parsedData as $rec) {
                            fputcsv($fh, $rec, ';');
                        }

                        fclose($fh);
                        $filePath = Url::to('@web/'.$outPath.$fileName);

                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages', 'Не получилось загрузить файл'));
                }
                //E: Start test load demo data

                Yii::$app->getSession()->setFlash('success', Yii::t('inbound/messages', 'Файл успешно загружен. Вы можете скачать файл с нашими данными нажав на кнопку "Скачать файл"'));

                //return $this->redirect(['/inbound/default/upload-from-api','unique_key'=>$unique_key,'client_id'=>$client_id]);
            }
        }
        return $this->render('stock-inventory', [
            'model' => $model,
            'href' => $filePath
        ]);
    }


    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionSearchRemains()
    {
        $searchModel = new StockRemains();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
//        if(empty(Yii::$app->request->queryParams)){
//            $dataProvider->setModels([]);
//        }
        $conditionTypeArray = $searchModel->getConditionTypeArray();
        $statusArray = $searchModel->getStatusArray();
        $availabilityStatusArray = $searchModel->getAvailabilityStatusArray();
        $lostStatusArray = $searchModel->getLostStatusArray();
        $clientsArray = Client::getActiveItems();

        return $this->render('search-remains', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conditionTypeArray' => $conditionTypeArray,
            'statusArray' => $statusArray,
            'availabilityStatusArray' => $availabilityStatusArray,
            'lostStatusArray' => $lostStatusArray,
            'clientsArray' => $clientsArray,
        ]);
    }

    /*
 * Import to excel
 *
 **/
    public function actionRemainsExportToExcel()
    {
        $detail = Yii::$app->request->get('detail');

        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));


        $i = 1;
//        $activeSheet->setCellValue('A' . $i, 'Клиент'); // +
//        $activeSheet->setCellValue('A' . $i, 'TIR'); // +
        $activeSheet->setCellValue('A' . $i, 'ШК товара'); // +
        $activeSheet->setCellValue('B' . $i, 'Количество'); // +
        $activeSheet->setCellValue('C' . $i, 'Модель'); // +
        $activeSheet->setCellValue('D' . $i, 'SkuID'); // +
		$activeSheet->setCellValue('E'.$i, Yii::t('stock/forms', 'Condition type')); // +

//        if($detail) {
//            $activeSheet->setCellValue('E' . $i, 'Короб'); // +
//            $activeSheet->setCellValue('F' . $i, 'Полка'); // +
//            $activeSheet->setCellValue('G' . $i, 'Модель'); // +
//
//        }

        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);
        if(!in_array($clientEmploy->client_id,Constants::getCarPartClientIDs())) {
            $this->helperUpdateDefactoSkuIdUpdate();
        }

        $searchModel = new StockRemains();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
		 $conditionTypeArray = $searchModel->getConditionTypeArray();
        $products = $dataProvider->getModels();
//        $clientTitles = [];
        $inboundOrderTitles = [];
        foreach ($products as $model) {
            $i++;
//            $clientTitle = '';
//            if (!isset($clientTitles[$model['client_id']])) {
//                if($client = Client::findOne($model['client_id'])) {
//                    $clientTitles[$model['client_id']] = $client->title;
//                    $clientTitle = $client->title;
//                }
//            } else {
//                $clientTitle = $clientTitles[$model['client_id']];
//            }
//
//            $activeSheet->setCellValue('A' . $i, $clientTitle);

//            $inboundTitle = '';
//            if (!isset($inboundOrderTitles[$model['consignment_inbound_id']])) {
//                if($inbound = ConsignmentInboundOrders::findOne($model['consignment_inbound_id'])) {
//                    $inboundOrderTitles[$model['consignment_inbound_id']] = $inbound->party_number;
//                    $inboundTitle = $inbound->party_number;
//                }
//            } else {
//                $inboundTitle = $inboundOrderTitles[$model['consignment_inbound_id']];
//            }

//            $activeSheet->setCellValue('A' . $i, $inboundTitle);
//            $activeSheet->setCellValue('A' . $i, "'=".$model['product_barcode']);
            $activeSheet->setCellValueExplicit('A' . $i, $model['product_barcode'], \PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->setCellValue('B' . $i, $model['qty']);
            $activeSheet->setCellValue('C' . $i, $model['product_model']);
            $activeSheet->setCellValue('D' . $i, $model['product_sku']);
            $activeSheet->setCellValue('E' . $i,  isset($conditionTypeArray[$model['condition_type']]) ? $conditionTypeArray[$model['condition_type']] : '-');

//            if($detail) {
//                $activeSheet->setCellValue('E' . $i, $model['primary_address']);
//                $activeSheet->setCellValue('F' . $i, $model['secondary_address']);
//                $activeSheet->setCellValue('G' . $i, $model['product_model']);
//            }
        }

        $fileName = 'remains-product-in-stock-report-' . date('Ymd_H-i-s');
        if($detail) {
            $fileName .= '-detail';
        }

        $fileName .= '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die;
//        Yii::$app->end();
    }

    /*
     * */
    protected function helperUpdateDefactoSkuIdUpdate()
    {
        $lotsOnStock = Stock::find()
            ->select('product_barcode')
            ->andWhere([
                'client_id' => Client::CLIENT_DEFACTO,
                'status_availability' => Stock::STATUS_AVAILABILITY_YES,
            ])
            ->andWhere("field_extra1 = ''")
            ->groupBy('product_barcode')
            ->asArray()
            ->all();
        $x = [];
        foreach ($lotsOnStock as $lot) {

            $barcode = $lot['product_barcode'];
			
            $badLotBarcodeList = [
//                "2300020760761",
//                "2300020842931",
//                "2300021158086",
//                "2300021158093",
//                "2300021158123",
//                "2300021238146",
//                "2300021251091",
                "2330009574867", // вернул пустоту (нужно чтобы ребята проверили по факту, на складе)
            ];
            if(in_array($barcode,$badLotBarcodeList)) {
                continue;
            }
			

            if(!isset($x[$barcode])) {
                $skqId = $this->getAPISkuIdFromDefacto($lot['product_barcode']);

                $x[$barcode] = $skqId;
                Stock::updateAll(['field_extra1'=>$skqId],
                    [
                        'client_id'=>Client::CLIENT_DEFACTO,
                        'product_barcode'=>$lot['product_barcode'],
                    ]
                );
            }
        }
    }

    protected function getAPISkuIdFromDefacto($LotOrSingleBarcode)
    {
        if(!empty($LotOrSingleBarcode)) {

            $api = new DeFactoSoapAPIV2();
            $params['request'] = [
                'BusinessUnitId' => '1029',
                'PageSize' => 0,
                'PageIndex' => 0,
                'CountAllItems' => false,
                'ProcessRequestedDataType' => 'Full',
                'LotOrSingleBarcode' => $LotOrSingleBarcode,
            ];
//
            $result = $api->sendRequest('GetMasterData', $params);
            if ($resultDataArray = @ArrayHelper::getValue($result['response'], 'GetMasterDataResult.Data.MasterDataThreePL')) {
                $resultDataArray = count($resultDataArray) <= 1 ? [$resultDataArray] : $resultDataArray;
            } else {
                $resultDataArray = [];
            }

            foreach ($resultDataArray as $value) {
                return $value->SkuId;
            }
        }

        return '';
    }

    /*
     *
     * */
    public function actionSearchHistoryByBarcode()
    {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchHistoryArray(Yii::$app->request->queryParams);
//        $conditionTypeArray = $searchModel->getConditionTypeArray();
/*        return $this->render('index', [
            'conditionTypeArray' => $conditionTypeArray,
        ]);*/
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);


        return $this->render('search-history-by-barcode',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientStoreArray' => $clientStoreArray,
        ]);
    }
	
	  /*
     *
     * Export data to EXEL
     *
     * */
    public function actionHistoryExportToExcel()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);

        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchHistoryArray(Yii::$app->request->queryParams);

        $dataProvider->pagination = false;
        $data = $dataProvider->getModels();
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 CSV Test Document")
            ->setSubject("Office 2007 CSV Test Document")
            ->setDescription("Test document for Office 2007 CSV, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A'.$i, Yii::t('stock/forms', 'Product barcode'))->getColumnDimension('A')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('B'.$i, Yii::t('stock/forms', 'Product model'))->getColumnDimension('B')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('C'.$i, Yii::t('stock/forms', 'Inbound order'))->getColumnDimension('C')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('D'.$i, Yii::t('stock/forms', 'Outbound order'))->getColumnDimension('D')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('E'.$i, Yii::t('stock/forms', 'Qty'))->getColumnDimension('E')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('F'.$i, Yii::t('stock/forms', 'Store Name'))->getColumnDimension('F')->setAutoSize(true); // +; // +


        foreach($data as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('B' . $i, $model['product_model']);

            $inboundOrder = '';
            if($in = \common\modules\inbound\models\InboundOrder::findOne($model['inbound_order_id'])) {
                $inboundOrder = $in->order_number;
            }

            $outboundOrder = '';
            $outboundStoreName = '';
           if (!empty($model['ecom_outbound_id']) && $model['ecom_outbound_id'] != '0') {
                if ($ecomOrder = \common\ecommerce\entities\EcommerceOutbound::findOne($model['ecom_outbound_id'])) {
                    $outboundOrder = $ecomOrder->id . ' - ' . $ecomOrder->order_number;
                }
            } elseif (!empty($model['outbound_order_id'])) {
                if ($o = \common\modules\outbound\models\OutboundOrder::findOne($model['outbound_order_id'])) {
                    $outboundOrder = $o->parent_order_number . ' - ' . $o->order_number;
                    if (!empty($o->to_point_id) && isset($clientStoreArray[$o->to_point_id])) {
                        $outboundStoreName = $clientStoreArray[$o->to_point_id];
					}
				}
			}

            $activeSheet->setCellValue('C' . $i,$inboundOrder );
            $activeSheet->setCellValue('D' . $i, $outboundOrder);
            $activeSheet->setCellValue('E' . $i, $model['qty']);
            $activeSheet->setCellValue('F' . $i, $outboundStoreName);
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/DeFacto/stock/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'stock-report-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }
	

    /*
     *
     * Export data to EXEL
     *
     * */
    public function actionHistoryExportToExcel_old()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchHistoryArray(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $data = $dataProvider->getModels();
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 CSV Test Document")
            ->setSubject("Office 2007 CSV Test Document")
            ->setDescription("Test document for Office 2007 CSV, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A'.$i, Yii::t('stock/forms', 'Product barcode')); // +
        $activeSheet->setCellValue('B'.$i, Yii::t('stock/forms', 'Product model')); // +
        $activeSheet->setCellValue('C'.$i, Yii::t('stock/forms', 'Inbound order')); // +
        $activeSheet->setCellValue('D'.$i, Yii::t('stock/forms', 'Outbound order')); // +
        $activeSheet->setCellValue('E'.$i, Yii::t('stock/forms', 'Qty')); // +


        foreach($data as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('B' . $i, $model['product_model']);

            $inboundOrder = '-';
            if($in = \common\modules\inbound\models\InboundOrder::findOne($model['inbound_order_id'])) {
                $inboundOrder = $in->order_number;
            }

            $outboundOrder = '-';
            if($o = \common\modules\outbound\models\OutboundOrder::findOne($model['outbound_order_id'])) {
                $outboundOrder = $o->order_number;
            }

            $activeSheet->setCellValue('C' . $i,$inboundOrder );
            $activeSheet->setCellValue('D' . $i, $outboundOrder);
            $activeSheet->setCellValue('E' . $i, $model['qty']);

        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/DeFacto/stock/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'stock-report-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }
	
	
 public function actionQtyBoxDefacto() {
        // other/one/qty-box-defacto

			$qtyBoxService = new QtyBoxDefactoService();
			$b2bBoxCount = $qtyBoxService->getB2BBoxCount();
			$b2bLotCount = $qtyBoxService->getB2BLotCount();

			$b2cBoxCount = $qtyBoxService->getB2CBoxCount();
-			$b2cProductCount = $qtyBoxService->getB2CProductCount();

			$returnLotBoxCount = $qtyBoxService->getReturnLotBoxCount();
			$returnLotCount = $qtyBoxService->getReturnLotCount();

			$returnPalletBoxCount = $qtyBoxService->getReturnPalletBoxCount();
			$returnPalletCount = $qtyBoxService->getReturnPalletCount();


        return $this->render('qty-box',[
            'b2bBoxCount' => $b2bBoxCount,
            'b2bLotCount' => $b2bLotCount,

            'b2cBoxCount' => $b2cBoxCount,
            'b2cProductCount' => $b2cProductCount,

			'returnLotBoxCount' => $returnLotBoxCount,
			'returnLotCount' => $returnLotCount,

			'returnPalletBoxCount' => $returnPalletBoxCount,
            'returnPalletCount' => $returnPalletCount,
        ]);
    }
	

	    public function actionQtyBoxDefacto_OLD() {
        // other/one/qty-box-defacto

			$clientId = Client::CLIENT_DEFACTO;

			$query = Stock::find()->select('primary_address');
			$query->andWhere([
				'client_id' => $clientId,
				'status_availability' => Stock::STATUS_AVAILABILITY_YES,
			]);
			$query->andWhere("(`secondary_address` LIKE '1-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '3-%')");
			$query->groupBy('primary_address');
			$b2bBoxCount = $query->count();
///
			$query = Stock::find();
			$query->andWhere([
				'client_id' => $clientId,
				'status_availability' => Stock::STATUS_AVAILABILITY_YES,
			]);
			$query->andWhere("(`secondary_address` LIKE '1-%' OR `secondary_address` LIKE '2-%' OR `secondary_address` LIKE '3-%')");
			$b2bProductCount = $query->count();
///
			$query = Stock::find()->select('primary_address');
			$query->andWhere([
				'client_id' => $clientId,
				'status_availability' => Stock::STATUS_AVAILABILITY_YES,
			]);
			$query->andWhere("(`secondary_address` LIKE '4-%')");
			$query->groupBy('primary_address');
			$returnLotBoxCount = $query->count();
///
			$query = Stock::find();
			$query->andWhere([
				'client_id' => $clientId,
				'status_availability' => Stock::STATUS_AVAILABILITY_YES,
			]);
			$query->andWhere("(`secondary_address` LIKE '4-%')");
			$b2bProductLotCount = $query->count();
///
			$query = Stock::find()->select('primary_address');
			$query->andWhere([
				'client_id' => $clientId,
				'status_availability' => Stock::STATUS_AVAILABILITY_YES,
			]);
			$query->andWhere("(`secondary_address` LIKE '5-%')");
			$query->groupBy('primary_address');

			$returnPalletBoxCount = $query->count();
///
			$query = Stock::find();
			$query->andWhere([
				'client_id' => $clientId,
				'status_availability' => Stock::STATUS_AVAILABILITY_YES,
			]);
			$query->andWhere("(`secondary_address` LIKE '5-%')");
			$b2bProductPalletCount = $query->count();


        return $this->render('qty-box',[
            'b2bBoxCount' => $b2bBoxCount,
            'returnLotBoxCount' => $returnLotBoxCount,
            'returnPalletBoxCount' => $returnPalletBoxCount,
            'b2bProductCount' => $b2bProductCount,
            'b2bProductLotCount' => $b2bProductLotCount,
            'b2bProductPalletCount' => $b2bProductPalletCount,
        ]);
    }
	
	
	
	/*
	    public function actionQtyBoxDefacto() {
        // other/one/qty-box-defacto

        $clientId = Client::CLIENT_DEFACTO;

        $query = Stock::find()->select('primary_address');
        $query->andWhere([
            'client_id' => $clientId,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
        ]);
        $query->groupBy('primary_address');

         $allQtyBox = $query->count();

        $stocks = Stock::find()->select('primary_address')->andWhere([
            'client_id'=>$clientId,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES
        ])->groupBy('primary_address');

        $returnQtyBox = 0;
        foreach($stocks->each(500) as $stock) {
//            if(BarcodeManager::isReturnProductBarcode($stock->product_barcode,$clientId)) {
            if(BarcodeManager::isReturnBoxBarcode($stock->primary_address)) {
                $returnQtyBox++;
            }
        }

        return $this->render('qty-box',[
            'returnQtyBox' => $returnQtyBox,
            'allQtyBox' => $allQtyBox,
        ]);
    }
	*/
	
	    public function actionQtyBoxDefacto__OLD() {
        // other/one/qty-box-defacto

        $clientId = Client::CLIENT_DEFACTO;

        $query = Stock::find()->select('primary_address');
        $query->andWhere([
            'client_id' => $clientId,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
        ]);
        $query->groupBy('primary_address');

         $allQtyBox = $query->count();

        $stocks = Stock::find()->select('product_barcode')->andWhere([
            'client_id'=>$clientId,
            'status_availability' => Stock::STATUS_AVAILABILITY_YES
        ]);

        $returnQtyBox = 0;
        foreach($stocks->each(500) as $stock) {
            if(BarcodeManager::isReturnProductBarcode($stock->product_barcode,$clientId)) {
                $returnQtyBox++;
            }
        }

        return $this->render('qty-box',[
            'returnQtyBox' => $returnQtyBox,
            'allQtyBox' => $allQtyBox,
        ]);
    }
	
}
