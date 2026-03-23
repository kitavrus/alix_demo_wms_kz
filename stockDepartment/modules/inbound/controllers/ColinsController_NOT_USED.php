<?php

namespace app\modules\inbound\controllers;

//use Codeception\Module\Cli;
use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\product\models\Product;
use common\modules\store\models\Store;
use common\modules\product\models\ProductBarcodes;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Yii;
use common\modules\client\models\ClientEmployees;
use app\modules\report\models\StockSearch;
use stockDepartment\components\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use clientDepartment\modules\client\components\ClientManager;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\stock\models\Stock;
use app\modules\inbound\models\ColinsProcessForm;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use stockDepartment\modules\inbound\models\AllocationListForm;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\outbound\models\ConsignmentOutboundOrder;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class ColinsController extends Controller
{
    //flag if we update records
    private $_update;

    //party number
    private $_partyNumber;

    /**
     * Files upload.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new ColinsProcessForm();
        $step = 1;
        $fileData1 = [];
        $fileData2 = [];
       // $double1 = [];
        $double2 = [];

        $session = Yii::$app->session;

        if (Yii::$app->request->isPost) {

            $model->file_1 = UploadedFile::getInstance($model, 'file_1');
            $model->file_2 = UploadedFile::getInstance($model, 'file_2');

            if ($model->validate()) {
                $dirPath = 'uploads/colins/colins-process/' . date('Ymd') . '/' . date('His');
                if ($model->file_1) {
                    //Путь сохранения загруженного файла
                    BaseFileHelper::createDirectory($dirPath);
                    $pathToCSVFile = $dirPath . '/' . $model->file_1->baseName . '.' . $model->file_1->extension;
                    $model->file_1->saveAs($pathToCSVFile);

                    if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
                        $row = 0;
                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                            $row++;
                            if ($row > 1) {
                                //$key = $data[0].'-'.$data[1];
//                                if(!empty($fileData1[$key]['box_barcode']) && !empty($fileData1[$key]['product_barcode'])){
//                                    $double1[$row] = [
//                                        'double_box' =>$data[0],
//                                        'double_product' =>$data[1],
//                                    ];
//                                }

                                $fileData1[$row]['box_barcode'] = $data[0];
                                $fileData1[$row]['product_barcode'] = $data[1];
                                $fileData1[$row]['product_model'] = $data[2];
                                $fileData1[$row]['product_sku'] = $data[3];
                                $fileData1[$row]['product_color'] = $data[4];
                                $fileData1[$row]['product_size'] = $data[5];
                                $fileData1[$row]['product_season'] = $data[6];
                                $fileData1[$row]['product_qty'] = $data[7];
                                $fileData1[$row]['product_made_in'] = $data[8];
                                $fileData1[$row]['product_composition'] = $data[9];
                                $fileData1[$row]['product_category'] = $data[10];
                                $fileData1[$row]['product_gender'] = $data[11];
                                $fileData1[$row]['product_price'] = $data[12];
                            }

                        }

                        fclose($handle);
                        $session->set('colinsInboundFilePath', $pathToCSVFile);
                        $step = 2;
                    }

                }

                if ($model->file_2) {
                    //Путь сохранения загруженного файла
                    BaseFileHelper::createDirectory($dirPath);
                    $pathToCSVFile = $dirPath . '/' . $model->file_2->baseName . '.' . $model->file_2->extension;
                    $model->file_2->saveAs($pathToCSVFile);
                    if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
                        $row = 0;
                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                            $row++;

                            foreach ($data as $key => $value){
                                $fileData2[$row][$key] = $value;
                            }

                            if($row > 1 && $data[0]){
                                $double2[] = $data[0];
                            }
                        }

                        fclose($handle);
                        $session->set('colinsOutboundFilePath', $pathToCSVFile);
                        $step = 3;
                    }

                }

                if($fileData1){
                    foreach ($fileData1 as $row=>$checkExistData){
                        if(isset($checkExistData['box_barcode'])){
                            if($exist = InboundOrder::find()->andWhere([
                                'client_box_barcode' => $checkExistData['box_barcode'],
                                'order_number'=>$checkExistData['box_barcode'],
                                'client_id'=>Client::CLIENT_COLINS,
                                'order_type' =>  InboundOrder::ORDER_TYPE_INBOUND
                                    ])
                                ->andWhere(['not', ['status'=>Stock::STATUS_INBOUND_NEW]])
                                ->exists()
                            ){
                                $session->remove('colinsInboundFilePath');
                                Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages', 'Upload error: some orders from file already in processing at warehouse'));
                               return $this->redirect('index');
                            }

                        }

                    }
//                   if($double1){
//                       $message  = 'Обнаружены дублирующиеся строки в таблице: <br>';
//                       foreach ($double1 as $row => $errorData){
//                           $message .= 'Строка: '.$row.', ШК короба: '.$errorData['double_box']. '; ШК товара: '. $errorData['double_product']. '<br>';
//                       }
//
//                       $session->remove('colinsInboundFilePath');
//                       Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages',$message));
//                       return $this->redirect('index');
//                   }
                }
                if($fileData2){
                    if($double2){
                        $doubles =[];
                        //VarDumper::dump($double2, 10, true); die();
                        $rows = array_count_values($double2);
                        foreach ($rows as $barcode=>$count){
                            if($count > 1){
                                $doubles[]=$barcode;
                            }
                        }

                        if($doubles){
                            $message  = 'Обнаружены следующие дубли товаров в таблице: <br>';
                            $message.=implode(', ', $doubles);
                            $session->remove('colinsOutboundFilePath');
                            Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages',$message));
                            return $this->redirect('index');
                        }
                    }
                }
            }
        }
        //Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages', 'Не получилось загрузить файл'));
        return $this->render('colins-process', [
            'model' => $model,
            'fileData1' => $fileData1,
            'fileData2' => $fileData2,
            'step' => $step,
        ]);
    }

    /**
     * Парсим данные из загруженных таблиц в БД
     * @return mixed
     */
    public function actionProcessConfirm()
    {
        $session = Yii::$app->session;
        $this->_update = false;
        //формируем номер партии одинаковый для Inbound и Outbound. Ориентируемся на Inbound
        //так как он заливается первый
        $partyNumber = ConsignmentInboundOrders::find()->andWhere(['client_id' => Client::CLIENT_COLINS])->count();
        $partyNumber++;
        $this->_partyNumber = 'Tir' . '-'. $partyNumber;

        //Берем пути сохраненных файлов из сессии
        $inboundFilePath = $session->get('colinsInboundFilePath');
        $outboundFilePath = $session->get('colinsOutboundFilePath');

        //если файлы по указанным путям существуют, то для каждого дергаем функцию обработки
        //и записи данных в БД
        if (file_exists($inboundFilePath) && file_exists($outboundFilePath)) {
            if ($this->addInbound($inboundFilePath) && $this->addOutbound($outboundFilePath)) {

                //После успешного сохранение данных из файлов в БД
                //записываем пути к файлам в лог и удаляем их из сессии
                Yii::warning('Colins inbound file path: ' . $inboundFilePath, 'upload');
                Yii::warning('Colins outbound file path: ' . $outboundFilePath, 'upload');
                $session->remove('colinsInboundFilePath');
                $session->remove('colinsOutboundFilePath');

                if($this->_update){
                    Yii::$app->getSession()->setFlash('success', Yii::t('inbound/messages', 'All data was successfully updated'));
                } else {
                    Yii::$app->getSession()->setFlash('success', Yii::t('inbound/messages', 'All data was successfully saved to database'));
                }


            }
        } else {
            Yii::error('Unable to open CSV file');
        }

        return $this->redirect('index');
    }

    /**
     * Парсим данные из загруженного файла Inbound
     * и сохраняем в БД
     * @param $filePath string путь к CSV файлу
     * @return bool
     */
    private function addInbound($filePath)
    {
        $fileDump = [];
        $preparedData = [];
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $row = 0;
            //читаем файл в массив
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $sum = false;
                $row++;
                if ($row > 1) {
                    $key = $data[0].'-'.$data[1];
                    if(!empty($fileDump[$key]['box_barcode']) && !empty($fileDump[$key]['product_barcode'])){

                        $sum = true;
                    }

                        $fileDump[$key]['box_barcode'] = $data[0];
                        $fileDump[$key]['product_barcode'] = $data[1];
                        $fileDump[$key]['product_model'] = $data[2];
                        $fileDump[$key]['product_sku'] = $data[3];
                        $fileDump[$key]['product_color'] = $data[4];
                        $fileDump[$key]['product_size'] = $data[5];
                        $fileDump[$key]['product_season'] = $data[6];
                        $fileDump[$key]['product_made_in'] = $data[8];
                        $fileDump[$key]['product_composition'] = $data[9];
                        $fileDump[$key]['product_category'] = $data[10];
                        $fileDump[$key]['product_gender'] = $data[11];
                        $fileDump[$key]['product_price'] = $data[12];
                    if($sum){
                        $fileDump[$key]['product_qty'] += $data[7];
                    } else {
                        $fileDump[$key]['product_qty'] = $data[7];
                    }



                }
            }
            fclose($handle);

            // подготавливаем массив для записи в БД
            if ($fileDump) {
                //группируем товары по ШК короба
                foreach ($fileDump as $key => $value) {
                    if (!empty($value['box_barcode'])) {
                        $preparedData[$value['box_barcode']]['items'][$key]['product_barcode'] = $value['product_barcode'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_model'] = $value['product_model'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_sku'] = $value['product_sku'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_color'] = $value['product_color'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_size'] = $value['product_size'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_season'] = $value['product_season'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_qty'] = $value['product_qty'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_made_in'] = $value['product_made_in'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_composition'] = $value['product_composition'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_category'] = $value['product_category'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_gender'] = $value['product_gender'];
                        $preparedData[$value['box_barcode']]['items'][$key]['product_price'] = str_replace(',', '.', $value['product_price']);
                    }
                }
                if ($preparedData) {
                    //проверяем на наличие таких же записей в базе и если есть то обновляем
                    foreach ($preparedData as $barcode => $data) {
                            if($inbound = InboundOrder::find()->andWhere([
                                'client_id' => Client::CLIENT_COLINS,
                                'client_box_barcode' => $barcode,
                                'order_number' => $barcode,
                                'order_type' => InboundOrder::ORDER_TYPE_INBOUND,
                                'status' => Stock::STATUS_INBOUND_NEW,
                            ])->one()) {
                                if (isset($data['items']) && $data['items'] && is_array($data['items'])) {
                                    //удяляем items
                                    InboundOrderItem::deleteAll([
                                        'inbound_order_id' => $inbound->id,
                                        'box_barcode' => $barcode,
                                    ]);
                                    $itemCounter = 0;
                                    //записываем новые
                                    foreach ($data['items'] as $item) {
                                        $ioi = new InboundOrderItem();
                                        $ioi->inbound_order_id = $inbound->id;
                                        $ioi->product_barcode = $item['product_barcode'];
                                        $ioi->product_model = $item['product_model'];
                                        $ioi->box_barcode = $barcode;
                                        $ioi->expected_qty = $item['product_qty'];
                                        $ioi->status = Stock::STATUS_INBOUND_NEW;
                                        $ioi->save(false);
                                        $itemCounter += $item['product_qty'];
                                        //если ШК товара нет в базе, то добавляем
                                        if (!ProductBarcodes::checkBarcodeExistsFromClient($inbound->client_id, $ioi->product_barcode)) {
                                            $product = new Product();
                                            $product->client_id = $inbound->client_id;
                                            $product->sku = $item['product_sku'];
                                            $product->price = floatval(str_replace('.', ',', $item['product_price']));
                                            $product->model = $item['product_model'];
                                            $product->color = $item['product_color'];
                                            $product->size = $item['product_size'];
                                            $product->season = $item['product_season'];
                                            $product->made_in = $item['product_made_in'];
                                            $product->composition = $item['product_composition'];
                                            $product->category = $item['product_category'];
                                            $product->gender = $item['product_gender'];
                                            $product->status = Product::STATUS_ACTIVE;

                                            if ($product->save(false)) {
                                                $ioi->product_id = $product->id;
                                                $ioi->save(false);
                                            }

                                            $productBarcode = new ProductBarcodes();
                                            $productBarcode->client_id = $inbound->client_id;
                                            $productBarcode->product_id = $product->id;
                                            $productBarcode->barcode = $item['product_barcode'];
                                            $productBarcode->save(false);
                                        } else {
                                            //пытаемся найти товар по ШК и привязываем его ID к InboundItems
                                            if($product = ProductBarcodes::getProductByBarcode(Client::CLIENT_COLINS, $item['product_barcode'])){
                                                $ioi->product_id = $product->id;
                                                $ioi->save(false);
                                            }
                                        }
                                    }
                                    $inbound->expected_qty = $itemCounter;
                                    $inbound->save(false);
                                }
                                $this->_partyNumber = $inbound->parent_order_number;
                                $this->_update = true;
                            }


                    }
                    //если файл обновляли то выходим и пересчитываем кол-в товаров в партии
                    if($this->_update){
                        if($party = ConsignmentInboundOrders::find()->andWhere(['party_number'=>$this->_partyNumber])->one()){
                            $party->recalculateOrderItems();
                        }
                        return true;
                    }

                   $cio = $this->createConsignmentInboundOrder();

                    if ($cio) {
                        //добавляем заказы в БД: 1 ШК короба = 1 заказ
                        foreach ($preparedData as $barcode => $data) {

                            $io = new InboundOrder();
                            $io->client_id = Client::CLIENT_COLINS;
                            $io->parent_order_number = $cio->party_number;
                            $io->client_box_barcode = $barcode;
                            $io->order_number = $barcode;
                            $io->consignment_inbound_order_id = $cio->id;
                            $io->order_type = InboundOrder::ORDER_TYPE_INBOUND;
                            $io->delivery_type = InboundOrder::DELIVERY_TYPE_CROSS_DOCK_A;
                            $io->status = Stock::STATUS_INBOUND_NEW;

                            //добавляем к заказу items а также записи в product и product_barcodes
                            if ($io->save(false)) {
                                $itemCounter = 0;
                                foreach ($data['items'] as $item) {
                                    $ioi = new InboundOrderItem();
                                    $ioi->inbound_order_id = $io->id;
                                    $ioi->product_barcode = $item['product_barcode'];
                                    $ioi->product_model = $item['product_model'];
                                    $ioi->box_barcode = $barcode;
                                    $ioi->expected_qty = $item['product_qty'];
                                    $ioi->status = Stock::STATUS_INBOUND_NEW;
                                    $ioi->save(false);
                                    $itemCounter += $item['product_qty'];

                                    if (!ProductBarcodes::checkBarcodeExistsFromClient($io->client_id, $ioi->product_barcode)) {
                                        $product = new Product();
                                        $product->client_id = $io->client_id;
                                        $product->sku = $item['product_sku'];
                                        $product->price = floatval(str_replace('.', ',', $item['product_price']));
                                        $product->model = $item['product_model'];
                                        $product->color = $item['product_color'];
                                        $product->size = $item['product_size'];
                                        $product->season = $item['product_season'];
                                        $product->made_in = $item['product_made_in'];
                                        $product->composition = $item['product_composition'];
                                        $product->category = $item['product_category'];
                                        $product->gender = $item['product_gender'];
                                        $product->status = Product::STATUS_ACTIVE;

                                        if ($product->save(false)) {
                                            $ioi->product_id = $product->id;
                                            $ioi->save(false);
                                        }

                                        $productBarcode = new ProductBarcodes();
                                        $productBarcode->client_id = $io->client_id;
                                        $productBarcode->product_id = $product->id;
                                        $productBarcode->barcode = $item['product_barcode'];
                                        $productBarcode->save(false);
                                    } else {
                                        //пытаемся найти товар по ШК и привязываем его ID к InboundItems
                                        if($product = ProductBarcodes::getProductByBarcode(Client::CLIENT_COLINS, $item['product_barcode'])){
                                            $ioi->product_id = $product->id;
                                            $ioi->save(false);
                                        }
                                    }
                                }

                                $io->expected_qty = $itemCounter;
                                $io->save(false);

                            } else {
                                Yii::error('Fail to save InboundOrder');
                            }
                        }

                        $cio->recalculateOrderItems();

                    } else {
                        Yii::error('Fail to save ConsignmentInboundOrders');
                    }

                    return true;
                }
            }

        } else {
            Yii::error('Unable to open file: ' . $filePath);
        }

        return false;
    }

    /**
     * Парсим данные из загруженного файла Outbound
     * и сохраняем в БД
     * @param $filePath string путь к CSV файлу
     * @return bool
     */
    private function addOutbound($filePath)
    {
        $fileDump = [];

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $row = 0;
            $storeArray = [];
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $row++;
                //Берем первую строку 'Шапку' таблицы
                if ($row == 1) {
                    //удаляем product_barcode
                    unset($data[0]);
                    array_pop($data);
                    foreach ($data as $key => $shop_code) {
                        $shopCode = explode(' ', $shop_code);

                        if (isset($shopCode[1])) {
                            unset ($shopCode[1]);
                        }

                        $shop = implode('', $shopCode);
                        $storeArray[$key] = Store::findClientStoreByShopCode(Client::CLIENT_COLINS, $shop);
                    }

                } elseif ($row > 1) {
                    if ($data[0]) {
                        $fileDump[$row]['product_barcode'] = $data[0];
                        if ($storeArray) {
                            foreach ($storeArray as $key => $shopId) {
                                if ($shopId) {
                                    if ($data[$key]) {
                                        $fileDump[$row]['store'][$shopId] = $data[$key];
                                    }
                                }
                            }
                        }
                    }

                }

            }

            fclose($handle);
            if ($fileDump) {

                if($this->_update){
                    $this->deleteOutboundOrdersByConsignment();
                    $coo = ConsignmentOutboundOrder::find()->andWhere(['party_number' => $this->_partyNumber])->one();
                } else {
                    //создаем запись для группировки заказов
                    $coo = $this->createConsignmentOutboundOrder();
                }

                if ($coo) {
                    $orderFlag = false;
                    $storeMap = [];
                    foreach ($fileDump as $key => $value) {
                        if (!$orderFlag) {
                            if (isset($value['store']) && !empty($value['store'])) {
                                // в первой итерации создаем для каждого магазина OutboundOrder
                                foreach ($value['store'] as $storeId => $storeOrder) {
                                    if ($store = Store::findOne($storeId)) {
                                        $oo = new OutboundOrder();
                                        $oo->client_id = Client::CLIENT_COLINS;
                                        $oo->from_point_id = Store::NOMADEX_MAIN_WAREHOUSE;
                                        $oo->to_point_id = $store->id;
                                        $oo->order_number = $coo->party_number . '-' . $store->shop_code;
                                        $oo->parent_order_number = $coo->party_number;
                                        $oo->consignment_outbound_order_id = $coo->id;
                                        $oo->delivery_type = OutboundOrder::DELIVERY_TYPE_CROSS_DOCK_A;
                                        $oo->status = Stock::STATUS_OUTBOUND_NEW;
                                        if ($oo->save(false)) {
                                            //создаем заявки и заказы к заявкам
                                            $oo->createDeliveryProposal();
                                            //записываем маппинг 'id магазина' => 'id созданного для него OutboundOrder'
                                            $storeMap[$storeId] = $oo->id;
                                        } else {
                                            Yii::error('Fail to save OutboundOrder');
                                        }
                                    }

                                    $orderFlag = true;
                                }
                            }
                        }

                        if (isset($value['store']) && !empty($value['store'])) {
                            //теперь для каждой заявки (OutboundOrder) мы создаем OutboundOrderItem c кол-вом товара
                            foreach ($value['store'] as $storeId => $productQty) {
                                if ($value['product_barcode'] && $productQty && isset($storeMap[$storeId])) {
                                    //ищем для каждого магазина созданный ранее заказ и добавляем в него товары
                                    if ($storeOutboundOrder = OutboundOrder::findOne($storeMap[$storeId])) {
                                        $productModel ='';
                                        if($product = ProductBarcodes::getProductByBarcode(Client::CLIENT_COLINS,$value['product_barcode'])){
                                            $productModel = $product->model;
                                        }
                                        //проверяем на дубли строк
//                                        if($exist = OutboundOrderItem::find()->andWhere(['product_barcode' => $value['product_barcode'], 'outbound_order_id' => $storeOutboundOrder->id])->exists()){
//                                            Yii::warning('Duplicate Colins barcore found: '.$value['product_barcode']. ' Store id: '.$storeOutboundOrder->id, 'upload');
//                                            $storeOutboundOrder->saveExtraFieldValue('DuplicateColinsProduct', $value['product_barcode'], true);
//                                        }
                                            $ooi = new OutboundOrderItem();
                                            $ooi->outbound_order_id = $storeOutboundOrder->id;
                                            $ooi->product_barcode = $value['product_barcode'];
                                            $ooi->product_model = $productModel;
                                            $ooi->status = Stock::STATUS_OUTBOUND_NEW;
                                            $ooi->expected_qty = $productQty;
                                            $ooi->save(false);

                                    }
                                }
                            }
                        }
                    }
                    if($storeMap){
                        foreach($storeMap as $orderId){
                            if($outboundOrder = OutboundOrder::findOne($orderId)){
                                $outboundOrder->recalculateOrderItems();
                            }
                        }
                    }
                    $coo->recalculateOrderItems();
                    return true;

                } else {
                    Yii::error('Fail to save ConsignmentOutboundOrder');
                }

            }
        }

        return false;
    }

    /**
     * Создаем ConsignmentOutboundOrder
     * @return mixed
     */
    private function createConsignmentOutboundOrder(){
        $data = false;
        $consignmentOutboundOrder = new ConsignmentOutboundOrder();
        $consignmentOutboundOrder->client_id = Client::CLIENT_COLINS;
        $consignmentOutboundOrder->party_number = $this->_partyNumber;
        $consignmentOutboundOrder->delivery_type = OutboundOrder::DELIVERY_TYPE_CROSS_DOCK_A;
        $consignmentOutboundOrder->status = Stock::STATUS_OUTBOUND_NEW;
        if($consignmentOutboundOrder->save(false)){
            $data = $consignmentOutboundOrder;
        }

        return $data;
    }

    /**
     * Создаем ConsignmentInboundOrder
     * @return mixed
     */
    private function createConsignmentInboundOrder(){
        $data = false;
        $consignmentInboundOrder = new ConsignmentInboundOrders();
        $consignmentInboundOrder->client_id = Client::CLIENT_COLINS;
        $consignmentInboundOrder->party_number = $this->_partyNumber;
        $consignmentInboundOrder->delivery_type = InboundOrder::DELIVERY_TYPE_CROSS_DOCK_A;
        $consignmentInboundOrder->order_type = InboundOrder::ORDER_TYPE_INBOUND;
        $consignmentInboundOrder->status = Stock::STATUS_INBOUND_NEW;
        if($consignmentInboundOrder->save(false)){
            $data = $consignmentInboundOrder;
        }

        return $data;
    }

    /**
     * Удаляем OutboundOrders текущего тира
     * @return bool
     */
    private function deleteOutboundOrdersByConsignment(){
        if($coo = ConsignmentOutboundOrder::find()->andWhere(['party_number' => $this->_partyNumber])->one()){
           if($orders = $coo->orders){
               foreach ($orders as $order){
                   OutboundOrderItem::deleteAll(['outbound_order_id'=>$order->id]);
                   $order->delete();
               }

               return true;
           }
        }

      return false;
    }

    /**
     * Print allocate list by box barcode
     * @return mixed
     */
    public function actionPrintAllocateList()
    {
        $model = new AllocationListForm();
        $outputData = [];
        $client_id = Client::CLIENT_COLINS;
        $storeArray = TLHelper::getStockPointArray($client_id, true, false, '{shop_code}');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($order = InboundOrder::find()->andWhere(['client_box_barcode' => $model->box_barcode, 'client_id' => $client_id])->one()) {

                $productBarcodes = InboundOrderItem::find()
                    ->select('product_barcode, expected_qty')
                    ->where(['inbound_order_id' => $order->id])
                    ->asArray()
                    ->all();

                $productBarcodes = ArrayHelper::map($productBarcodes, 'product_barcode', 'expected_qty');
                //VarDumper::dump($order->id, 10, true);
                // echo "<br />";
                //VarDumper::dump($productBarcodes, 10, true);
                // echo "<br />";echo "<br />";echo "<br />";
//                die('-actionPrintAllocateList-');

//                if($productBarcodes = ArrayHelper::getColumn($productBarcodes, 'product_barcode')){

                if (!empty($productBarcodes)) {

                    foreach ($productBarcodes as $productBarcode => $expected_qty) {

                        $outboundOrderItem = OutboundOrderItem::find()
                            ->where(['product_barcode' => $productBarcode,])
                            ->andWhere('expected_qty = :expected_qty AND expected_qty != allocated_qty', [':expected_qty' => $expected_qty])
                            ->one();

                        if (empty($outboundOrderItem)) {
                            $outboundOrderItem = OutboundOrderItem::find()
                                ->where(['product_barcode' => $productBarcode,])
                                ->andWhere('expected_qty < :expected_qty AND expected_qty != allocated_qty', [':expected_qty' => $expected_qty])
                                ->one();
                        }

                        if ($outboundOrderItem) {
                            if ($oo = $outboundOrderItem->outboundOrder) {
                                $outputData [] = [
                                    'outbound_order_id' => $oo->id,
                                    'shop_id' => $oo->to_point_id,
                                    'product_barcode' => $outboundOrderItem->product_barcode,
                                    'product_model' => $outboundOrderItem->product_model,
                                    'expected_qty' => $outboundOrderItem->expected_qty,
                                ];
                                //$orderNumber = $oo->order_number;
                                $outboundOrderItem->allocated_qty += $expected_qty;
                                $outboundOrderItem->save(false);
                            }
                        }


                    }

                    //VarDumper::dump($outputData, 10, true); die;
//                    $outboundOrderItems = OutboundOrderItem::find()
//                                        ->where(['product_barcode' =>$productBarcode,])
//                                        ->all();

//                    foreach ($outboundOrderItems as $ooi){
//                        if($oo = $ooi->outboundOrder){
//                            $outputData [] = [
//                                'shop_id'=>$oo->to_point_id,
//                                'product_barcode'=>$ooi->product_barcode,
//                                'product_model'=>$ooi->product_model,
//                                'expected_qty'=>$ooi->expected_qty,
//                            ];
//                        }
//
//                    }
                    // VarDumper::dump($outputData, 10, true);
                    //die('-actionPrintAllocateList-');
                }

            }

            return $this->render('print-allocate-list', [
                'outputData' => $outputData,
                'orderNumber' => $model->box_barcode,
                'storeArray' => $storeArray,

            ]);

        }
        return $this->render('allocate-list', [
            'model' => $model,

        ]);
    }

}

