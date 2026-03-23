<?php

namespace app\modules\warehouseDistribution\controllers\colins;

use Codeception\Module\Cli;
use common\components\BarcodeManager;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use stockDepartment\modules\outbound\models\AllocationListForm;
use common\modules\outbound\models\OutboundOrderItem;
use stockDepartment\modules\outbound\models\ScanningColinsForm;
use Yii;
use common\modules\client\models\Client;
use clientDepartment\components\Controller;
use stockDepartment\modules\product\models\ProductSearch;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\db\Query;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\helpers\DateHelper;
use app\modules\warehouseDistribution\models\ColinsOutboundForm;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use common\modules\product\models\ProductBarcodes;
use common\modules\product\models\Product;



class OutboundController extends Controller
{

    /*
    *
    *
    * */
    public function actionOutboundForm()
    {
        $model = new ColinsOutboundForm();
        $fileData = [];
        $existRow = [];
        $dirPath = 'uploads/colins/colins-outbound/' . date('Ymd') . '/' . date('His');
        $session = Yii::$app->session;
        $upload = Yii::$app->request->get('upload');
        $thead=[];

        if (Yii::$app->request->isPost) {

            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->validate()) {
                //сохраняем файл
                BaseFileHelper::createDirectory($dirPath);
                $pathToCSVFile = $dirPath . '/' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs($pathToCSVFile);

                //читаем файл в массив для просмотра
                if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
                    $row = 0;
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        $row++;
                        foreach ($data as $key => $value){
                            $fileData[$row][$key] = $value;
                        }

                        if($row > 1 && $data[0]){
                            $existRow[] = $data[0];
                        }
                    }

                    fclose($handle);
                    $thead = isset ($fileData[1]) ? $fileData[1] : [];
                }

//                if($existRow){
//                    $doubles =[];
//                    //VarDumper::dump($double2, 10, true); die();
//                    $rows = array_count_values($existRow);
//                    foreach ($rows as $barcode=>$count){
//                        if($count > 1){
//                            $doubles[]=$barcode;
//                        }
//                    }
//
//                    if($doubles){
//                        $message  = 'Обнаружены следующие дубли товаров в таблице: <br>';
//                        $message.=implode(', ', $doubles);
//                        $session->remove('colinsOutboundFilePath');
//                        Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages',$message));
//                        return $this->redirect('outbound-form');
//                    }
//
//                }

                //Проверка на дубли магазинов из шапки
                if($thead){
                    $shopDoubles =[];
                    //Убираем первую ячейку шапки - ШК товара
                    array_shift($thead);
                    foreach ($thead as $head){
                        //форматируем код магазина для поиска по нему в базе
                        $shop_code =  iconv('windows-1251','utf-8',$head);
                        $shopCode = trim($shop_code);
                        $shopCode = str_replace(' ','=',$shopCode);
                        $shopCode = explode('=',$shopCode);
                        $shop = '';
                        if (isset($shopCode[0])) {
                            $shop = $shopCode[0];
                        }
                        if($store = \common\modules\store\models\Store::findClientStoreByShopCode(\common\modules\client\models\Client::CLIENT_COLINS, $shop)){
                            if($checkForCopy = OutboundOrder::find()
                                ->andWhere([
                                    'client_id'=>Client::CLIENT_COLINS,
                                    'to_point_id' => $store,
                                    'from_point_id' => Store::NOMADEX_MAIN_WAREHOUSE,
                                    'delivery_type' => OutboundOrder::DELIVERY_TYPE_CROSS_DOCK_A,
                                    //'parent_order_number' => 'Tir-'.date('dmy'),
                                    'parent_order_number' => 'Tir-'.date('dmy').'-dop-8',

                                ])
                                ->andWhere(['not in', 'status', [Stock::STATUS_OUTBOUND_PART_RESERVED, Stock::STATUS_OUTBOUND_FULL_RESERVED]])
                                ->one()){

                                        $session->remove('colinsOutboundFilePath');
                                        Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages','ERROR: Some orders from the file have already been downloaded today and processed in stock'));
                                        return $this->redirect('outbound-form');
                            }

                            if(in_array($store, $shopDoubles)){
                                $storeModel = Store::findOne($store);
                                $session->remove('colinsOutboundFilePath');
                                Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages','ERROR: Existing shop code [{0}] was found. Check the spelling of code stores in a downloadable file', [$storeModel->shop_code]));
                                return $this->redirect('outbound-form');
                            } else{
                                $shopDoubles[] = $store;
                            }
                        }
                    }

                }

                $session->set('colinsOutboundFile', $pathToCSVFile);

                return $this->render('colins-outbound', ['fileData' => $fileData, 'model' => $model]);
            }
        }

        if($upload){
            $outboundFilePath =  $session->get('colinsOutboundFile');
            if (file_exists($outboundFilePath)) {
                if($this->addOutbound($outboundFilePath)){
                    $session->remove('colinsOutboundFile');
                    Yii::$app->getSession()->setFlash('success', Yii::t('inbound/messages', 'All data was successfully saved to database'));

                    return $this->redirect('outbound-form');
                }
            }
        }

        return $this->render('colins-outbound', ['fileData' => $fileData, 'model' => $model]);
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
        //set_time_limit(0);
       //S:читаем содержимое файла в массив
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $row = 0;
            $storeArray = [];
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $row++;
                //Берем первую строку 'Шапку' таблицы
                if ($row == 1) {
                    //удаляем product_barcode
                    foreach ($data as $key => $shop_code) {
//                        $shop_code =  iconv('CP866','utf-8',$shop_code);
                        $shop_code =  iconv('windows-1251','utf-8',$shop_code);
                        $shopCode = trim($shop_code);
                        $shopCode = str_replace(' ','=',$shopCode);
                        $shopCode = explode('=',$shopCode);

                        $shop = '-not find-';
                        if (isset($shopCode[0])) {
                            $shop = $shopCode[0];
                        }
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
            //E:читаем содержимое файла в массив

            if ($fileDump && $this->checkExistByConsignmentOrder()) {

                if ($coo = $this->createConsignmentOutboundOrder()) {
                    Stock::resetAllocatedProductByConsignmentOutboundId($coo->id);
                    $coo = $this->createConsignmentOutboundOrder();
                    $storeMap = [];
                    foreach ($fileDump as $key => $value) {
                        if (isset($value['store']) && !empty($value['store'])) {
                            // в первой итерации создаем для каждого магазина OutboundOrder
                            foreach ($value['store'] as $storeId => $storeOrder) {
                                if ($store = Store::findOne($storeId)) {

                                    if(!($oo = OutboundOrder::find()->andWhere([
                                        'client_id'=>   Client::CLIENT_COLINS,
                                        'from_point_id'=>   Store::NOMADEX_MAIN_WAREHOUSE,
                                        'to_point_id'=>   $store->id,
                                        'order_number'=>   $coo->party_number . '-' . $store->internal_code,
                                        'consignment_outbound_order_id'=>   $coo->id,
//                                        'status'=>  Stock::STATUS_OUTBOUND_NEW,
                                    ])->one())) {

                                        $oo = new OutboundOrder();
                                        $oo->client_id = Client::CLIENT_COLINS;
                                        $oo->from_point_id = Store::NOMADEX_MAIN_WAREHOUSE;
                                        $oo->to_point_id = $store->id;
                                        $oo->order_number = $coo->party_number . '-' . $store->internal_code;
                                        $oo->parent_order_number = $coo->party_number;
                                        $oo->consignment_outbound_order_id = $coo->id;
                                        $oo->delivery_type = OutboundOrder::DELIVERY_TYPE_CROSS_DOCK_A;
                                        $oo->status = Stock::STATUS_OUTBOUND_NEW;

                                        if ($oo->save(false)) {
                                            //создаем заявки и заказы к заявкам
                                            $oo->createDeliveryProposal($oo->order_number);
                                            //записываем маппинг 'id магазина' => 'id созданного для него OutboundOrder'
                                            $storeMap[$storeId] = $oo->id;
                                        } else {
                                            Yii::error('Fail to save OutboundOrder');
                                        }
                                    } else {
                                        $storeMap[$storeId] = $oo->id;
                                    }
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
                                        if(!($ooi = OutboundOrderItem::find()->where(['outbound_order_id'=>$storeOutboundOrder->id,'product_barcode'=>$value['product_barcode']])->one())) {
                                            $ooi = new OutboundOrderItem();
                                            $ooi->outbound_order_id = $storeOutboundOrder->id;
                                            $ooi->product_barcode = $value['product_barcode'];
                                            $ooi->product_model = $productModel;
                                        }

//                                        $ooi->outbound_order_id = $storeOutboundOrder->id;
//                                        $ooi->product_barcode = $value['product_barcode'];
//                                        $ooi->product_model = $productModel;
                                        $ooi->status = Stock::STATUS_OUTBOUND_NEW;
                                        if($ooi->expected_qty > 0){
                                            $ooi->expected_qty += $productQty;
                                        } else {
                                            $ooi->expected_qty = $productQty;
                                        }
                                        $ooi->save(false);
                                    }
                                }
                            }
                        }
                    }
                    if($storeMap){
                        foreach($storeMap as $orderId){
                            if($outboundOrder = OutboundOrder::findOne($orderId)){
                                Stock::AllocateByOutboundOrderId($outboundOrder->id);
                                $outboundOrder->recalculateOrderItems();
                                $outboundOrder->checkOrderReservedStatus();
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
        //$partyNumber = 'Tir-'.date('dmy');
        $partyNumber = 'Tir-'.date('dmy').'-dop-8';
        $data = false;
        if(!($consignmentOutboundOrder = ConsignmentOutboundOrder::find()->andWhere(['client_id'=> Client::CLIENT_COLINS,'party_number'=>$partyNumber])->one())) {
            $consignmentOutboundOrder = new ConsignmentOutboundOrder();
            $consignmentOutboundOrder->client_id = Client::CLIENT_COLINS;
            $consignmentOutboundOrder->party_number = $partyNumber;
            $consignmentOutboundOrder->delivery_type = OutboundOrder::DELIVERY_TYPE_RPT;
            $consignmentOutboundOrder->status = Stock::STATUS_OUTBOUND_NEW;
            if($consignmentOutboundOrder->save(false)) {
                $data = $consignmentOutboundOrder;
            }
        } else{
            $data = $consignmentOutboundOrder;
        }

        return $data;
    }

    /**
     * Проверка по номеру партии
     * @return mixed
     */
    private function checkExistByConsignmentOrder(){
        //$partyNumber = 'Tir-'.date('dmy');
        $partyNumber = 'Tir-'.date('dmy').'-dop-8';
        if($coo = ConsignmentOutboundOrder::find()->andWhere(['party_number' => $partyNumber, 'client_id' => Client::CLIENT_COLINS])->one()){
            $outboundOrders = OutboundOrder::find()->where(['consignment_outbound_order_id'=>$coo->id])->all();
            if($outboundOrders){
//            if($outboundOrders = $coo->orders){
                foreach($outboundOrders as $order){
                    if(in_array($order->status,[
                        Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
                        Stock::STATUS_OUTBOUND_PICKING,
                        Stock::STATUS_OUTBOUND_PICKED,
                        Stock::STATUS_OUTBOUND_SCANNING,
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_SORTING,
                        Stock::STATUS_OUTBOUND_SORTED,
                        Stock::STATUS_OUTBOUND_PACKING,
                        Stock::STATUS_OUTBOUND_SHIPPING,
                        Stock::STATUS_OUTBOUND_SHIPPED,
                        Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
                        Stock::STATUS_OUTBOUND_ON_ROAD,
                        Stock::STATUS_OUTBOUND_DELIVERED,
                        Stock::STATUS_OUTBOUND_DONE,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ])){
//                    if($order->accepted_qty > 0){
                        Yii::$app->session->remove('colinsOutboundFilePath');
                        Yii::$app->getSession()->setFlash('error', Yii::t('inbound/messages','ERROR: Some orders from the file have already been downloaded today and processed in stock'));
                        return $this->redirect('outbound-form');
                    }
                }
            }

        }

        return true;
    }

}
