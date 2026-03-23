<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 8/5/14
 * Time: 6:17 PM
 */

namespace console\controllers;

use app\modules\inbound\inbound;
use common\modules\stock\models\Stock;
use frontend\modules\inbound\models\InboundOrderItem;
use Yii;
use yii\console\Controller;
use Touki\FTP\Connection\Connection;
use Touki\FTP\FTPWrapper;
use yii\helpers\VarDumper;
use yii\helpers\FileHelper;
use frontend\modules\product\models\SyncProducts;
use frontend\modules\product\models\ProductBarcodes;
use frontend\modules\product\models\Product;
use frontend\modules\inbound\models\InboundOrder;
use League\Csv\Reader;

class SyncController extends Controller
{

    /*
     *
     * */
    public function actionPriceByFtp()
    {
        echo 'Price-By-Ftp' . "\n";

//        $Filepath = __DIR__ . '/../../frontend/data/upload-from-ftp/GAP.xlsx';
//
//        try {
//            $Reader = new \SpreadsheetReader($Filepath);
//
//            foreach ($Reader as $Row) {
//                print_r($Row);
//            }
//
//        } catch (Exception $E) {
//            echo $E->getMessage();
//        }
//
//
//        return 0;


		$connection = new Connection('95.85.8.137', 'test', 'testpass','21',1900);
		$connection->open();
		$wrapper = new FTPWrapper($connection);
        $wrapper->pasv(true);

        $pathToSave = __DIR__ . '/../../frontend/data/upload-from-ftp/';

        $dateTime = time();
        $date = date('dmY');
        @mkdir($pathToSave.$date);
        @mkdir($pathToSave.$date.'/'.$dateTime);

        $pathToFile = $pathToSave.$date.'/'.$dateTime.'/'.'GAP.xlsx';
		$wrapper->get($pathToFile, 'GAP.xlsx');
		$connection->close();
        $client_id = 1;

        try {

            $reader = new \SpreadsheetReader($pathToFile);

            foreach ($reader as $row) {
                // Найти товар у нас на складе. Если его нет то создать
                // Добавить Шк к новому товару

                $sync = new SyncProducts();
                $sync->client_id = $client_id;
                $sync->name = $row[1].' '.$row[2];
                $sync->price = $row[6]; // Price
                $sync->barcode = trim($row[12]); //  Barcode
                $sync->sync_file_datetime = $dateTime; //  Date time to sync file
                $sync->save(false);

                if($product = ProductBarcodes::getProductByBarcode($client_id,$sync->barcode) ) {
                    $product->price = $sync->price;
                    $product->save(false);
                } else {
                    $product = new Product();
                    $product->client_id = $client_id;
                    $product->name = $sync->name;
                    $product->price = $sync->price;
                    $product->save(false);

                    $pb = new ProductBarcodes();
                    $pb->client_id = $client_id;
                    $pb->product_id =  $product->id;
                    $pb->barcode =  $sync->barcode;
                    $pb->save(false);
                }
            }

        } catch (Exception $E) {
            echo $E->getMessage();
        }



        /** Define how many rows we want to read for each "chunk" **/
//        $chunkSize = 2;
        /** Create a new Instance of our Read Filter **/
//        $chunkFilter = new chunkReadFilter();


//        $cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
//        $cacheSettings = array(
//            'dir' => '.'
//        );
        // \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

//        $inputFileType = \PHPExcel_IOFactory::identify($pathToFile);
//        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

//        $sheets = $objReader->listWorksheetNames($pathToFile);
//        $objReader->setReadDataOnly(true);
//        $objReader->setLoadSheetsOnly(array($sheets[0]));
//
//        $objReader->setLoadSheetsOnly(0);
        /** Tell the Reader that we want to use the Read Filter **/
//        $objReader->setReadFilter($chunkFilter);

        /** Loop to read our worksheet in "chunk size" blocks **/
//        for ($startRow = 2; $startRow <= 4; $startRow += $chunkSize) {
//            echo $startRow . "\n";
            /** Tell the Read Filter which rows we want this iteration **/
//            $chunkFilter->setRows($startRow, $chunkSize);
            /** Load only the rows that match our filter **/
//            $excelObj = $objReader->load($pathToFile);
//            $excelObj->setActiveSheetIndex(0);
//            $data = $excelObj->getActiveSheet()->toArray();/
            //$data = $excelObj->getActiveSheet()->toArray(null, true,true,true);
//            $objWorksheet = $excelObj->getActiveSheet();
//            $data = $objWorksheet->getCellByColumnAndRow(0, 1)->getValue();
// Do some processing here - the $data variable will contain an array which is always limited to 2048 elements regardless of the size of the entire sheet
//            print_r($data);
//            $excelObj->disconnectWorksheets();
//            unset ($excelObj);
//        }

//------------------------------------------------


//        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
//        $cacheEnabled = \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
//        if (!$cacheEnabled)
//        {
//            echo "### WARNING - Sqlite3 not enabled ###" . PHP_EOL;
//        }
//        $inputFileType = \PHPExcel_IOFactory::identify($pathToFile);

//        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

//        $objReader->setReadDataOnly(true);
//        $objPHPExcel = $objReader->load($pathToFile);


//		$callStartTime = microtime(true);
//		echo $pathToFile."\n";
//		$objPHPexcel = \PHPExcel_IOFactory::createReader('Excel2003XML');
//		$objPHPexcel = \PHPExcel_IOFactory::createReader('Excel2007');
//		$objPHPexcel->setReadDataOnly(TRUE);
//		$excel = $objPHPexcel->load($pathToFile);
//        \PHPExcel_IOFactory::load($pathToFile);
//		var_dump($objPHPexcel,true);

//		$objPHPexcel = \PHPExcel_IOFactory::load($pathToFile);
//		$objWorksheet = $excel->getActiveSheet();
//		$highestRow = $objWorksheet->getHighestRow();
//		$encoding = 'UTF-8';

//		for ($row = 2; $row <= $highestRow; ++$row) {
//
//			$products[] = array(
//				'product_id' => iconv('UTF-8', $encoding, $objWorksheet->getCellByColumnAndRow(0, $row)->getValue()),
//				'product_name' => iconv('UTF-8', $encoding, $objWorksheet->getCellByColumnAndRow(1, $row)->getValue()),
//				'product_price' => iconv('UTF-8', $encoding, $objWorksheet->getCellByColumnAndRow(2, $row)->getValue()),
//			);
//			print_r($products);
//		}

//		$callEndTime = microtime(true);
//		$callTime = $callEndTime - $callStartTime;

//		echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
        // Echo memory usage
//		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
        // Echo memory peak usage
//		echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

        return 0;
    }


    //FRK
    /*
     *
     *
     * */
    public function actionFrkGetInvoiceByFtp()
    {
        echo 'frk-get-invoice-by-ftp' . "\n";

        $ftpIP = '195.210.46.61';
        $ftpName = 'invoice';
        $ftpPswd = 'Innsbruck1976';

        $connection = new Connection($ftpIP, $ftpName, $ftpPswd,'21',1900);
        $connection->open();
        $wrapper = new FTPWrapper($connection);
        $wrapper->pasv(true);

//        $wrapper->get($pathToFile, 'GAP.xlsx');
//        VarDumper::dump($wrapper->nlist('/'));


        $clientId = 1;
        $fileName = 'FRK-MNS-INV64,65.txt';
        $invoiceNumber = '';

        $pathToSave = __DIR__ . '/../../frontend/data/upload-from-ftp/';

        $dateTime = time();
        $date = date('Ymd');

        $path = $pathToSave.'/'.$clientId.'/'.$date.'/'.$dateTime;
        FileHelper::createDirectory($path);


        $pathToFile = $path.'/'.$fileName;
        $wrapper->get($pathToFile, $fileName);
        $connection->close();

        $csv = Reader::createFromPath($pathToFile);
        $csv->setDelimiter('|');

        $csvs = $csv->fetchAll();

//        VarDumper::dump($csvs);

        $start = strripos($fileName, 'INV');
        $end = strripos($fileName, '.');

        if ($start !== false) {
            $invoiceNumber = substr($fileName,$start+3,strlen($fileName)-$end+1);
            $invoiceNumber = trim($invoiceNumber);
        }




        // Проверяем если заказ не начали сканировать то удаляем и создаем новый
        if($inbound = \common\modules\inbound\models\InboundOrder::find()->where(['client_id'=>$clientId,'order_number'=>$invoiceNumber])->one()) {
            echo 'Заказ найден'."\n";
            // Если заказ есть и его не начали сканировать
            if(!in_array($inbound->status,[Stock::STATUS_INBOUND_NEW])) {
                // Заказ уже начали сканировать его нельзя обновить
                echo 'Заказ уже начали сканировать'."\n";
                return 0;
            }

            \common\modules\inbound\models\InboundOrderItem::deleteAll(['inbound_order_id' => $inbound->id]);

        } else {
            echo 'Заказ новый'."\n";
            $inbound =  new InboundOrder();
        }


        $inbound->client_id = $clientId;
        $inbound->order_number = $invoiceNumber;
        $inbound->order_type = 1;// Cross-doc
        $inbound->status = 1; // new
        $inbound->expected_qty = 0; //
        $inbound->accepted_qty = 0; //
        $inbound->accepted_number_places_qty = 0; //
        $inbound->expected_number_places_qty = 0; //

        if(!$inbound->save()) {
            VarDumper::dump($inbound->getErrors());
        }


        foreach($csvs as $row) {

            if(isset($row[2])) {
                $productBarcode = $row['2'];
                $productModel = $row['3'];
                $productPrice = $row['4'];

                //-RU-
                $productNameRu = $row['11'];
                $productCompositionRu = $row['13'];
                $productMadeInRu = $row['14'];
                $productExpirationDateRu = $row['15'];
                $productExporterRu = $row['16'];
                $productImporterRu = $row['17'];

                //-KZ-
                $productNameKz = $row['19'];
                $productCompositionKz = $row['21'];
                $productMadeInKz = $row['22'];
                $productExpirationDateKz = $row['23'];
                $productExporterKz = $row['24'];
                $productImporterKz = $row['25'];

                $data = [
                  'productName'=>['ru'=>$productNameRu, 'kz'=>$productNameKz],
                  'productComposition'=>['ru'=>$productCompositionRu, 'kz'=>$productCompositionKz],
                  'productMadeIn'=>['ru'=>$productMadeInRu, 'kz'=>$productMadeInKz],
                  '$productExpirationDate'=>['ru'=>$productExpirationDateRu, 'kz'=>$productExpirationDateKz],
                  '$productExporter'=>['ru'=>$productExporterRu, 'kz'=>$productExporterKz],
                  '$productImporter'=>['ru'=>$productImporterRu, 'kz'=>$productImporterKz],
                ];


                $item = new InboundOrderItem();
                $item->inbound_order_id = $inbound->id;
                $item->product_model = $productModel;
                $item->product_barcode = $productBarcode;
                $item->product_price = $productPrice;
                $item->product_serialize_data = serialize($data);
                $item->expected_qty = 1;
                $item->accepted_qty = 0;

                echo "\n";
                echo "\n";
                if(!$item->save()) {
                    VarDumper::dump($item->getErrors());
                }

            }
        }

        return 0;
    }

    /*
     *
     * */
    public function actionIndex()
    {
        echo 'index' . "\n";
        return 0;
    }
} 