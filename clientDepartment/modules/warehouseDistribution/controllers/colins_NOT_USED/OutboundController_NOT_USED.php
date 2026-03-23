<?php

namespace app\modules\warehouseDistribution\controllers\colins;

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
use stockDepartment\components\Controller;
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
use app\modules\outbound\models\ColinsOutboundForm;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use common\modules\product\models\ProductBarcodes;
use common\modules\product\models\Product;



class OutboundController extends Controller
{
    /*
      * Validate scanned box
      *
      * */
    public function actionValidateScanningBox()
    {
        $model = new AllocationListForm();
        $model->load(Yii::$app->request->post());
        $model->client_id = Client::CLIENT_COLINS;;
        $model->validate();
        if ($order = InboundOrder::find()->andWhere(['client_box_barcode' => $model->box_barcode, 'client_id' => $model->client_id])->one()) {

            $InboundOrderItems = InboundOrderItem::find()
                ->where(['inbound_order_id' => $order->id])
                ->all();

            if (!empty($InboundOrderItems) && is_array($InboundOrderItems)) {
                foreach ($InboundOrderItems as $inbound) {
                    if (!(Stock::find()->where([
                        'client_id' => $order->client_id,
                        'inbound_order_id' => $order->id,
                        'product_barcode' => $inbound->product_barcode,
                    ])->exists())
                    ) {

                        for ($i = 0; $i < $inbound->expected_qty; $i++) {
                            // STOCK

                            $stock = new Stock();
                            $stock->client_id = $order->client_id;
                            $stock->inbound_order_id = $order->id;
                            $stock->product_barcode = $inbound->product_barcode;
                            $stock->product_model = $inbound->product_model;
                            $stock->status = Stock::STATUS_INBOUND_SORTED;
                            $stock->status_availability = Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED;
                            $stock->save(false);

                        }
                    }
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }

    /*
    * Validate scanned box
    *
    * */
    public function actionPrintSortingListByBox()
    {
        $model = new AllocationListForm();
        $outputData = [];
        $remnantInBox = []; // остаток в коробке
        $client_id = Client::CLIENT_COLINS;

        $storeArray = TLHelper::getStockPointArray($client_id, true, false, '{internal_code}');

        if ($model->load(Yii::$app->request->get())) {

            if ($order = InboundOrder::find()->andWhere(['client_box_barcode' => $model->box_barcode, 'client_id' => $client_id])->one()) {

                $productBarcodes = InboundOrderItem::find()
                    ->select('product_barcode, expected_qty')
                    ->where(['inbound_order_id' => $order->id])
                    ->asArray()
                    ->all();

                $boxProductBarcodes = ArrayHelper::map($productBarcodes, 'product_barcode', 'expected_qty');

                $outboundIds = OutboundOrder::find()->select('id')->where(['client_id' => $client_id, 'status' => Stock::STATUS_OUTBOUND_NEW])->column();

                file_put_contents('colins-allocate.log', "\n" . "\n" . "\n" . "--NEW--" . "\n" . "\n" . "\n", FILE_APPEND);

                if (!empty($boxProductBarcodes)) {

                    foreach ($boxProductBarcodes as $productBarcode => $inBoxQty) {
                        $inBoxDiffAllocated = $inBoxQty;

                        while ($inBoxDiffAllocated) {

                            $outboundOrderItems = OutboundOrderItem::find()
                                ->where(['product_barcode' => $productBarcode, 'outbound_order_id' => $outboundIds])
                                ->andWhere('expected_qty != allocated_qty')
                                ->orderBy('expected_qty DESC')
                                ->limit(1)
                                ->all();

                            if (!empty($outboundOrderItems)) {
                                foreach ($outboundOrderItems as $outboundOrderItem) {

                                    $expectedQtyItem = intval($outboundOrderItem->expected_qty);
                                    $allocatedQtyItem = intval($outboundOrderItem->allocated_qty);
                                    $diffInOrder = $expectedQtyItem - $allocatedQtyItem;
                                    $diffWithBox = $diffInOrder - $inBoxDiffAllocated;

                                    file_put_contents('colins-allocate.log', "\n" . "\n", FILE_APPEND);
                                    file_put_contents('colins-allocate.log', "outboundOrderItem ID = " . $outboundOrderItem->id . "\n", FILE_APPEND);
                                    file_put_contents('colins-allocate.log', "productBarcode = " . $productBarcode . "\n", FILE_APPEND);
                                    file_put_contents('colins-allocate.log', "expectedQtyItem = " . $expectedQtyItem . "\n", FILE_APPEND);
                                    file_put_contents('colins-allocate.log', "allocatedQtyItem = " . $allocatedQtyItem . "\n", FILE_APPEND);
                                    file_put_contents('colins-allocate.log', "diffInOrder = " . $diffInOrder . "\n", FILE_APPEND);
                                    file_put_contents('colins-allocate.log', "diffWithBox = " . $diffWithBox . "\n", FILE_APPEND);
                                    file_put_contents('colins-allocate.log', "inBoxDiffAllocated = " . $inBoxDiffAllocated . "\n", FILE_APPEND);
                                    file_put_contents('colins-allocate.log', "inBoxQty = " . $inBoxQty . "\n", FILE_APPEND);


                                    if ($diffWithBox == 0) {
                                        if ($oo = $outboundOrderItem->outboundOrder) {
                                            $outputData[$outboundOrderItem->product_barcode][] = [
                                                'outbound_order_id' => $oo->id,
                                                'shop_id' => $oo->to_point_id,
                                                'product_barcode' => $outboundOrderItem->product_barcode,
                                                'product_model' => $outboundOrderItem->product_model,
                                                'expected_qty' => $inBoxDiffAllocated,
                                            ];

                                            $outboundOrderItem->allocated_qty += $inBoxDiffAllocated;
                                            $outboundOrderItem->save(false);

                                            // STOCK
                                            if ($inStocks = Stock::find()->where([
                                                    'client_id' => $client_id,
                                                    'inbound_order_id' => $order->id,
                                                    'product_barcode' => $outboundOrderItem->product_barcode,
                                                    'status_availability' => Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED]
                                            )->limit($inBoxDiffAllocated)->all()
                                            ) {

                                                foreach ($inStocks as $stockLine) {
                                                    $stockLine->outbound_order_id = $oo->id;
                                                    $stockLine->status = Stock::STATUS_OUTBOUND_PICKED;
                                                    $stockLine->status_availability = Stock::STATUS_AVAILABILITY_RESERVED;
                                                    $stockLine->save(false);
                                                }
                                            }


                                            $inBoxDiffAllocated = 0;
                                            $oo->recalculateOrderItems();
                                            file_put_contents('colins-allocate.log', "outbound_order_id = " . $oo->id . "\n", FILE_APPEND);
                                            continue;
                                        }
                                    }

                                    //Если одижаем 4 в коробе 3
                                    // 4 - 0 = 4
                                    // 4 - 3 = 1
                                    if ($diffWithBox > 0) {
                                        if ($oo = $outboundOrderItem->outboundOrder) {
                                            $outputData[$outboundOrderItem->product_barcode][] = [
                                                'outbound_order_id' => $oo->id,
                                                'shop_id' => $oo->to_point_id,
                                                'product_barcode' => $outboundOrderItem->product_barcode,
                                                'product_model' => $outboundOrderItem->product_model,
                                                'expected_qty' => $inBoxDiffAllocated,
                                            ];

                                            $outboundOrderItem->allocated_qty += $inBoxDiffAllocated;
                                            $outboundOrderItem->save(false);

                                            // STOCK
                                            if ($inStocks = Stock::find()->where([
                                                    'client_id' => $client_id,
                                                    'inbound_order_id' => $order->id,
                                                    'product_barcode' => $outboundOrderItem->product_barcode,
                                                    'status_availability' => Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED]
                                            )->limit($inBoxDiffAllocated)->all()
                                            ) {

                                                foreach ($inStocks as $stockLine) {
                                                    $stockLine->outbound_order_id = $oo->id;
                                                    $stockLine->status = Stock::STATUS_OUTBOUND_PICKED;
                                                    $stockLine->status_availability = Stock::STATUS_AVAILABILITY_RESERVED;
                                                    $stockLine->save(false);
                                                }
                                            }


                                            $inBoxDiffAllocated = 0;
                                            $oo->recalculateOrderItems();
                                            file_put_contents('colins-allocate.log', "outbound_order_id = " . $oo->id . "\n", FILE_APPEND);
                                            continue;
                                        }
                                    }

                                    //Если одижаем 4 в коробе 7
                                    // 4 - 0 = 4
                                    // 4 - 7 = -3
                                    if ($diffWithBox < 0) {
                                        if ($oo = $outboundOrderItem->outboundOrder) {
                                            $outputData[$outboundOrderItem->product_barcode][] = [
                                                'outbound_order_id' => $oo->id,
                                                'shop_id' => $oo->to_point_id,
                                                'product_barcode' => $outboundOrderItem->product_barcode,
                                                'product_model' => $outboundOrderItem->product_model,
                                                'expected_qty' => $diffInOrder,
                                            ];

                                            $outboundOrderItem->allocated_qty += $diffInOrder;
                                            $outboundOrderItem->save(false);


                                            // STOCK
                                            if ($inStocks = Stock::find()->where([
                                                    'client_id' => $client_id,
                                                    'inbound_order_id' => $order->id,
                                                    'product_barcode' => $outboundOrderItem->product_barcode,
                                                    'status_availability' => Stock::STATUS_AVAILABILITY_TEMPORARILY_RESERVED]
                                            )->limit($outboundOrderItem->expected_qty)->all()
                                            ) {

                                                foreach ($inStocks as $stockLine) {
                                                    $stockLine->outbound_order_id = $oo->id;
                                                    $stockLine->status = Stock::STATUS_OUTBOUND_PICKED;
                                                    $stockLine->status_availability = Stock::STATUS_AVAILABILITY_RESERVED;
                                                    $stockLine->save(false);
                                                }
                                            }

                                            $oo->recalculateOrderItems();
                                            $inBoxDiffAllocated = $diffWithBox * -1;
                                            file_put_contents('colins-allocate.log', "outbound_order_id = " . $oo->id . "\n", FILE_APPEND);
                                            continue;
                                        }
                                    }
                                }
                            } else {
                                $remnantInBox[$productBarcode] = $inBoxDiffAllocated;
                                $inBoxDiffAllocated = 0;
                            }
//                            }
                        } // end While
                    } // end foreach boxProductBarcodes
                }
                $order->status = Stock::STATUS_INBOUND_SORTED;
                $order->save(false);
            }

            return $this->render('print-allocate-list', [
                'outputData' => $outputData,
                'orderNumber' => $model->box_barcode,
                'storeArray' => $storeArray,
                'remnantInBox' => $remnantInBox,
            ]);
        }

        // TODO Тут добавить вывод сообщения об ошибке
//        return $this->redirect('/');
    }

    /*
     *
     *
     * */
    public function actionScanningBox()
    {
        $model = new AllocationListForm();
//        $outputData = [];
//        $client_id = Client::CLIENT_COLINS;
//        $storeArray = TLHelper::getStockPointArray($client_id, true, false, '{shop_code}');
        /*
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($order = InboundOrder::find()->andWhere(['client_box_barcode' => $model->box_barcode, 'client_id' => $client_id])->one()) {

                $productBarcodes = InboundOrderItem::find()
                    ->select('product_barcode, expected_qty')
                    ->where(['inbound_order_id' => $order->id])
                    ->asArray()
                    ->all();

                $productBarcodes = ArrayHelper::map($productBarcodes, 'product_barcode', 'expected_qty');

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

                                $outboundOrderItem->allocated_qty += $expected_qty;
                                $outboundOrderItem->save(false);
                            }
                        }
                    }
                }
            }

            return $this->render('print-allocate-list', [
                'outputData' => $outputData,
                'orderNumber' => $model->box_barcode,
                'storeArray' => $storeArray,

            ]);
        }
        */

        return $this->renderAjax('allocate-list', [
            'model' => $model,

        ]);
    }

    /*
     *
     *
     * */
    public function actionScanningForm()
    {
        $client_id = Client::CLIENT_COLINS;
        $storeArray = TLHelper::getStockPointArray($client_id, true, false, '{internal_code}');
        $orders = OutboundOrder::find()->where(['client_id' => $client_id, 'status' => [Stock::STATUS_OUTBOUND_NEW, Stock::STATUS_OUTBOUND_SCANNING]])->all();

        $orderShops = ArrayHelper::map($orders, 'id', function ($m) use ($storeArray) {
            return isset($storeArray[$m->to_point_id]) ? $storeArray[$m->to_point_id] . ' [' . $m->parent_order_number . ']' : '-НЕ НАЙДЕН-';
        });

        return $this->renderAjax('scanning-form', ['orderShops' => $orderShops, 'model' => new ScanningColinsForm()]);
    }

    /*
    * Scanning form handler Is Employee Barcode
    * */
    public function actionEmployeeBarcodeScanningHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';

        $model = new ScanningColinsForm();
        $model->scenario = 'IsEmployeeBarcode';

        if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }

    /*
     *
     * */
    public function actionOrderShopScanningHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';
        $stockArrayByPL = [];
        $orderData = [];
        $orderShopName = '';

        $model = new ScanningColinsForm();
        $model->scenario = 'IsOrderShop';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($order = OutboundOrder::findOne($model->order_shop)) {
                if ($store = Store::findOne($order->to_point_id)) {
                    $orderShopName = $store->getPointTitleByPattern('{city_name} / {shop_code} {shopping_center_name}');
                }
            }

            $stockArrayByPL = OutboundOrder::getItemsById($model->order_shop, true);
            $orderData = OutboundOrder::getAccExpById($model->order_shop);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'orderShopName' => $orderShopName,
            'exp_qty' => isset($orderData['expected_qty']) ? $orderData['expected_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_order-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
    * Scanning form handler Is Box Barcode
    * */
    public function actionBoxBarcodeScanningHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';
        $countInBox = 0;

        $model = new ScanningColinsForm();
        $model->scenario = 'IsBoxBarcode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $countInBox = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'box_barcode' => $model->box_barcode])->count();
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countInBox' => $countInBox,
        ];
    }

    /*
 * Scanning form handler Is Product Barcode
 * */
    public function actionProductBarcodeScanningHandler()
    {
        // 1 - Проверяем есть ли этот шк в заказе, если нет то ошибка
        // 2 - Создаем запись в Stock
        // 3 - Увеличиваем кол-во принятого товара в items на +1

        Yii::$app->response->format = Response::FORMAT_JSON;


        $errors = [];
        $messages = '';
        $countInBox = '0';
        $stockArrayByPL = [];
        $orderData = [];

        $model = new ScanningColinsForm();
        $model->scenario = 'IsProductBarcode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //Ищем запись в Stock
            if ($stock = Stock::find()->where([
                'status' => [
                    Stock::STATUS_INBOUND_SORTED,
                    Stock::STATUS_OUTBOUND_PART_RESERVED,
                    Stock::STATUS_OUTBOUND_PICKED
                ],
                'product_barcode' => $model->product_barcode,
                'outbound_order_id' => $model->order_shop
            ])->one()
            ) {
                $stock->status = Stock::STATUS_OUTBOUND_SCANNED;
                $stock->box_barcode = $model->box_barcode;
                $stock->save(false);

                $countStockForOrderItem = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'product_barcode' => $model->product_barcode, 'outbound_order_id' => $model->order_shop])->count();
                $countStockForInboundOrderItem = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'product_barcode' => $model->product_barcode, 'inbound_order_id' => $stock->inbound_order_id])->count();
                $outboundOrder = OutboundOrder::findOne($model->order_shop);
                $inboundOrder = InboundOrder::findOne($stock->inbound_order_id);

                //Пересчет количества товаров в OutboundOrderItems
                if ($ioi = OutboundOrderItem::find()->where([
                    'outbound_order_id' => $stock->outbound_order_id,
                    'product_barcode' => $model->product_barcode,
                ])->one()
                ) {
                    if (intval($ioi->accepted_qty) < 1) {
                        $ioi->begin_datetime = time();
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNING;
                    }

                    $ioi->accepted_qty = $countStockForOrderItem;
                    $ioi->allocated_qty = $countStockForOrderItem;

                    if ($ioi->accepted_qty == $ioi->expected_qty || $ioi->accepted_qty == $ioi->allocated_qty) {
                        $ioi->status = Stock::STATUS_OUTBOUND_SCANNED;
                    }

                    $ioi->end_datetime = time();
                    $ioi->save(false);
                }

                //Пересчет количества товаров в OutboundOrders
                if ($outboundOrder) {
                    $countStockForOrder = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'outbound_order_id' => $outboundOrder->id])->count();

                    if (intval($outboundOrder->accepted_qty) < 1) {
                        $outboundOrder->begin_datetime = time();
                        $outboundOrder->status = Stock::STATUS_OUTBOUND_SCANNING;
                    }

                    $outboundOrder->accepted_qty = $countStockForOrder;
                    $outboundOrder->allocated_qty = $countStockForOrder;

                    if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty || $outboundOrder->expected_qty == $outboundOrder->allocated_qty) {
                        $outboundOrder->status = Stock::STATUS_OUTBOUND_SCANNED;
                    }

                    $outboundOrder->end_datetime = time();
                    $outboundOrder->save(false);

                    //Пересчет количества товаров в ConsignmentOutboundOrders
                    if($consignmentOutboundOrder = $outboundOrder->parentOrder){

                        if (intval($consignmentOutboundOrder->accepted_qty) < 1) {
                            $consignmentOutboundOrder->begin_datetime = time();
                            $consignmentOutboundOrder->status = Stock::STATUS_OUTBOUND_SCANNING;
                        }

                        $consignmentOutboundOrder->recalculateOrderItems();
                    }

                }

                //Пересчет количества товаров в InboundOrderItems
                if ($inboundItem = InboundOrderItem::find()->where([
                    'inbound_order_id' => $stock->inbound_order_id,
                    'product_barcode' => $model->product_barcode,
                ])->one()
                ) {
                    if (intval($inboundItem->accepted_qty) < 1) {
                        $inboundItem->begin_datetime = time();
                        $inboundItem->status = Stock::STATUS_INBOUND_SCANNING;
                    }

                    $inboundItem->accepted_qty = $countStockForInboundOrderItem;

                    if ($inboundItem->accepted_qty == $inboundItem->expected_qty) {
                        $inboundItem->status = Stock::STATUS_INBOUND_SCANNED;
                    }

                    $inboundItem->end_datetime = time();
                    $inboundItem->save(false);
                }

                //Пересчет количества товаров в InboundOrders
                if ($inboundOrder) {
                    $countStockForInboundOrder = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'inbound_order_id' => $inboundOrder->id])->count();

                    if (intval($inboundOrder->accepted_qty) < 1) {
                        $inboundOrder->begin_datetime = time();
                        $inboundOrder->status = Stock::STATUS_INBOUND_SCANNING;
                    }

                    $inboundOrder->accepted_qty = $countStockForInboundOrder;

                    if ($inboundOrder->accepted_qty == $inboundOrder->expected_qty) {
                        $inboundOrder->status = Stock::STATUS_INBOUND_SCANNED;
                    }

                    $inboundOrder->end_datetime = time();
                    $inboundOrder->save(false);

                    //Пересчет количества товаров в ConsignmentInboundOrders
                    if($consignmentInboundOrder = $inboundOrder->parentOrder){

                        if (intval($consignmentInboundOrder->accepted_qty) < 1) {
                            $consignmentInboundOrder->begin_datetime = time();
                            $consignmentInboundOrder->status = Stock::STATUS_INBOUND_SCANNING;
                        }

                        $consignmentInboundOrder->recalculateOrderItems();
                    }

                }

            }


            $countInBox = OutboundOrder::getCountInBoxById($model->box_barcode, $model->order_shop);
            $stockArrayByPL = OutboundOrder::getItemsById($model->order_shop, true);
            $orderData = OutboundOrder::getAccExpById($model->order_shop);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'change_box' => 'no',
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countInBox' => $countInBox,
            'exp_qty' => isset($orderData['expected_qty']) ? $orderData['expected_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_order-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
    * Delete product by barcode  in box
    * @return JSON true or errors array
    * */
    public function actionClearProductInBoxByOne()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $stockArrayByPL = [];
        $countInBox = '0';
        $orderData = [];

        $model = new ScanningColinsForm();
        //$model->scenario = 'ClearProductInBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($stock = Stock::findOne(['box_barcode' => $model->box_barcode,
                'product_barcode' => $model->product_barcode,
                'outbound_order_id' => $model->order_shop,
                'status' => Stock::STATUS_OUTBOUND_SCANNED
            ])
            ) {

                $stock->status = Stock::STATUS_OUTBOUND_PICKED;
                $stock->box_barcode = '';
                $stock->save(false);

                $countStockForOrderItem = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'product_barcode' => $model->product_barcode, 'outbound_order_id' => $model->order_shop])->count();

                if ($ioi = OutboundOrderItem::findOne(['product_barcode' => $model->product_barcode, 'outbound_order_id' => $stock->outbound_order_id])) {

                    $ioi->accepted_qty = $countStockForOrderItem;
                    $ioi->status = Stock::STATUS_OUTBOUND_PICKED;
                    $ioi->save(false);
                }

                $oo = OutboundOrder::findOne($stock->outbound_order_id);
                $countStockForOrder = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'outbound_order_id' => $stock->outbound_order_id])->count();
                $oo->accepted_qty = $countStockForOrder;
                $oo->save(false);

                //Пересчет количества товаров в ConsignmentOutboundOrders
                if($consignmentOutboundOrder = $oo->parentOrder){
                    $consignmentOutboundOrder->recalculateOrderItems();
                }

                //Пересчет количества товаров в InboundOrderItems
                if ($inboundItem = InboundOrderItem::find()->where([
                    'inbound_order_id' => $stock->inbound_order_id,
                    'product_barcode' => $model->product_barcode,
                ])->one()
                ) {
                    $inboundItem->accepted_qty = Stock::find()
                        ->where([
                            'status' => Stock::STATUS_OUTBOUND_SCANNED,
                            'product_barcode' => $model->product_barcode,
                            'inbound_order_id' => $stock->inbound_order_id])
                        ->count();
                    $inboundItem->save(false);
                }

                //Пересчет количества товаров в InboundOrders
                if ($inboundOrder = InboundOrder::findOne($stock->inbound_order_id)) {
                    $countStockForInboundOrder = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'inbound_order_id' => $inboundOrder->id])->count();

                    $inboundOrder->accepted_qty = $countStockForInboundOrder;
                    $inboundOrder->save(false);

                    //Пересчет количества товаров в ConsignmentInboundOrders
                    if($consignmentInboundOrder = $inboundOrder->parentOrder){
                        $consignmentInboundOrder->recalculateOrderItems();
                    }

                }


            }

            $countInBox = OutboundOrder::getCountInBoxById($model->box_barcode, $model->order_shop);
            $stockArrayByPL = OutboundOrder::getItemsById($model->order_shop, true);
            $orderData = OutboundOrder::getAccExpById($model->order_shop);

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countInBox' => $countInBox,
            'exp_qty' => isset($orderData['expected_qty']) ? $orderData['expected_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_order-items', ['items' => $stockArrayByPL]),
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
        $stockArrayByPL = [];
        $orderData = [];

        $model = new ScanningColinsForm();
        $model->scenario = 'ClearBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($productsInBox = Stock::find()->select('count(product_barcode) as product_barcode_count, product_barcode, outbound_order_id, inbound_order_id')
                ->where([
                    'box_barcode' => $model->box_barcode,
                    'outbound_order_id' => $model->order_shop,
                    'status' => Stock::STATUS_OUTBOUND_SCANNED
                ])
                ->groupBy('product_barcode')->asArray()->all()
            ) {

                foreach ($productsInBox as $item) {
                    if ($ioi = OutboundOrderItem::findOne(['product_barcode' => $item['product_barcode'], 'outbound_order_id' => $item['outbound_order_id']])) {

                        // STATUS
                        Stock::updateAll([
                            'status' => Stock::STATUS_OUTBOUND_PICKED,
                            'box_barcode' => ''
                        ],
                            [
                                'box_barcode' => $model->box_barcode,
                                'product_barcode' => $item['product_barcode'],
                                'outbound_order_id' => $item['outbound_order_id'],
                                'status' => Stock::STATUS_OUTBOUND_SCANNED
                            ]
                        );

                        $countStock = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'product_barcode' => $item['product_barcode'], 'outbound_order_id' => $item['outbound_order_id']])->count();
                        $ioi->accepted_qty = $countStock;

                        // OUTBOUND ORDER ITEM
                        $ioi->status = Stock::STATUS_OUTBOUND_PICKED;
                        $ioi->save(false);

                        // OUTBOUND ORDER
                        $oo = OutboundOrder::findOne($item['outbound_order_id']);

                        $countStockForOrder = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'outbound_order_id' => $item['outbound_order_id']])->count();
                        $oo->accepted_qty = $countStockForOrder;
                        $oo->status = Stock::STATUS_OUTBOUND_PICKED;
                        $oo->save(false);

                        //Пересчет количества товаров в ConsignmentOutboundOrders
                        if($consignmentOutboundOrder = $oo->parentOrder){
                            $consignmentOutboundOrder->recalculateOrderItems();
                        }

                        //Пересчет количества товаров в InboundOrderItems
                        if ($inboundItem = InboundOrderItem::find()->where([
                            'inbound_order_id' => $item['inbound_order_id'],
                            'product_barcode' => $item['product_barcode'],
                        ])->one()
                        ) {
                            $inboundItem->accepted_qty = Stock::find()
                                ->where([
                                    'status' => Stock::STATUS_OUTBOUND_SCANNED,
                                    'product_barcode' => $item['product_barcode'],
                                    'inbound_order_id' => $item['inbound_order_id']])
                                ->count();
                            $inboundItem->save(false);
                        }

                        //Пересчет количества товаров в InboundOrders
                        if ($inboundOrder = InboundOrder::findOne($item['inbound_order_id'])) {
                            $countStockForInboundOrder = Stock::find()->where(['status' => Stock::STATUS_OUTBOUND_SCANNED, 'inbound_order_id' => $inboundOrder->id])->count();

                            $inboundOrder->accepted_qty = $countStockForInboundOrder;
                            $inboundOrder->save(false);

                            //Пересчет количества товаров в ConsignmentInboundOrders
                            if($consignmentInboundOrder = $inboundOrder->parentOrder){
                                $consignmentInboundOrder->recalculateOrderItems();
                            }

                        }


                        $stockArrayByPL = OutboundOrder::getItemsById($model->order_shop);
                        $orderData = OutboundOrder::getAccExpById($model->order_shop);
                    }
                }
            } else {
                $model->addError('box_barcode', '<b>[' . $model->box_barcode . ']</b> ' . Yii::t('outbound/errors', 'Вы ввели несуществующий штрихкод короба или короб пуст'));
                $errors = $model->getErrors();
            }
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'exp_qty' => isset($orderData['expected_qty']) ? $orderData['expected_qty'] : '0',
            'accept_qty' => isset($orderData['accepted_qty']) ? $orderData['accepted_qty'] : '0',
            'stockArrayByPL' => $this->renderPartial('_order-items', ['items' => $stockArrayByPL]),
        ];
    }

    /*
    * Print difference list
    *
    * */
    public function actionPrintingDifferencesList()
    {
        $order_shop = Yii::$app->request->get('order_shop');

        $subQuery = (new Query())
            ->select('count(*)')
            ->from('stock as stck')
            ->where(['stck.status'=>Stock::STATUS_OUTBOUND_SCANNED,'stck.outbound_order_id'=>$order_shop])
            ->andWhere('stck.product_barcode = stock.product_barcode');


        $items = Stock::find()
            ->select(['id', 'outbound_order_id', 'product_barcode', 'box_barcode', 'status', 'primary_address', 'secondary_address', 'product_model', 'count(*) as items','count_status_scanned'=>$subQuery])
            ->where([
                'outbound_order_id' => $order_shop,
                'status' => [
                    Stock::STATUS_OUTBOUND_PICKED,
                    Stock::STATUS_OUTBOUND_SCANNED,
//                    Stock::STATUS_OUTBOUND_SCANNING,
                    Stock::STATUS_OUTBOUND_PART_RESERVED,
                    Stock::STATUS_OUTBOUND_FULL_RESERVED,
                ],
            ])
            ->groupBy('product_barcode') // , box_barcode
            ->orderBy([
                'product_barcode' => SORT_DESC,
                'count_status_scanned' => SORT_DESC,
            ])
            ->asArray()
            ->all();

        return $this->render('printing-differences-list-pdf', ['items' => $items,'order_shop'=>$order_shop]);
    }

    /*
     * Printing box label
     * @param string $plids Picking list IDs
     * */
    public function actionPrintingBoxLabel()
    {
        //11404260-37390-1
        //$plIDs = Yii::$app->request->get('plids');
        $order_shop = Yii::$app->request->get('order_shop');

        // $qIDs = OutboundPickingLists::prepareIDsHelper($plIDs);

//        $outboundOrderIDs = OutboundPickingLists::find()
//            ->select('outbound_order_id')
//            ->where(['id'=>$qIDs])
//            ->groupBy('outbound_order_id')->asArray()->column();

        $items = Stock::find()
            ->select('id,outbound_order_id, inbound_order_id, box_barcode, box_size_m3')
            ->where([
//                'outbound_picking_list_id' => $qIDs,
                'outbound_order_id' => $order_shop,
                'status' => [
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                ],
            ])
            ->groupBy('box_barcode')
            ->asArray()
            ->all();

//        VarDumper::dump($items,10,true);
//        die('---STOP--');
        $model = '';
        $outboundOrderModel = '';
        if (isset($items[0]['outbound_order_id'])) {
            $outboundOrderModel = OutboundOrder::findOne($items[0]['outbound_order_id']);
            if ($dpo = TlDeliveryProposalOrders::findOne(['order_id' => $items[0]['outbound_order_id']])) {
                $model = TlDeliveryProposal::findOne($dpo->tl_delivery_proposal_id);

//                if($oo = OutboundOrder::findOne($items[0]['outbound_order_id'])){
//                    $oo->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
                $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL;
                $outboundOrderModel->save(false);
//                }
//                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL],'id = :id AND accepted_qty > 0',[':id'=>$items[0]['outbound_order_id']]);
                OutboundOrderItem::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL],'id = :id AND accepted_qty > 0',[':id'=>$items[0]['outbound_order_id']]);
//                Stock::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL],'outbound_order_id = :outbound_order_id AND status = :status',[':status'=>Stock::STATUS_OUTBOUND_SCANNED,':outbound_order_id'=>$items[0]['outbound_order_id']]);
                Stock::updateAll(['status'=>Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL],'outbound_order_id = :outbound_order_id AND status = :status',[':status'=>Stock::STATUS_OUTBOUND_SCANNED,':outbound_order_id'=>$items[0]['outbound_order_id']]);
            }

            // OutboundPickingLists::updateAll(['status'=>OutboundPickingLists::STATUS_PRINT_BOX_LABEL],['id'=>$qIDs]);

            //S: Проверяем все ли сборочные листа распечатаны
//            if(!OutboundPickingLists::find()->where('outbound_order_id = :outbound_order_id AND status != :status',[
//                ':outbound_order_id'=>$outboundOrderModel->id,
//                ':status'=>OutboundPickingLists::STATUS_PRINT_BOX_LABEL
//            ])->exists()) {
            $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
            $outboundOrderModel->packing_date = DateHelper::getTimestamp();
            $outboundOrderModel->save(false);
            //}

//            //S: если накладная для DeFacto. отправляем отчет по отгруженным товарам
//            if($outboundOrderModel->client_id == Client::CLIENT_DEFACTO && YII_ENV == 'prod') { // id = 2 Дефакто
//
//                $rows = [];
//                if ($itemsAPI = $outboundOrderModel->getOrderItems()->all()) {
//                    foreach ($itemsAPI as $k => $itemAPi) {
//                        if($itemAPi->accepted_qty >= 1) {
//                            $rows[] = [
//                                'RezerveId'=>$outboundOrderModel->order_number,
//                                'Barkod'=>$itemAPi->product_barcode,
//                                'Miktar'=>$itemAPi->accepted_qty,
//                                'IrsaliyeNo'=>$outboundOrderModel->order_number,
//                                'KoliId'=>$k + 1,
//                                'KoliDesi'=>'25',
//                            ];
//                        }
//                    }
//                }
////                    die("-----01");
//
//                if(!empty($rows)) {
//                    $api = new DeFactoSoapAPI();
//                    $apiData = [];
//                    if($apiResponse = $api->confirmOutboundOrder($rows)) {
//                        if (empty($apiResponse['errors'])) {
//                            $apiData = $apiResponse['response'];
//                        }
//                    }
//                    $extraFields = [];
//                    if(!empty($outboundOrderModel->extra_fields)) {
//                        $extraFields = Json::decode($outboundOrderModel->extra_fields);
//                    }
//                    $extraFields ['requestToAPI'] = $rows;
//                    $extraFields ['RezerveDagitimResult'] = $apiData;
//
//                    $outboundOrderModel->extra_fields = Json::encode($extraFields);
//                    $outboundOrderModel->status = Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API;
//                    $outboundOrderModel->save(false);
//                }
//            }
            //E: если накладная для DeFacto. отправляем отчет по отгруженным товарам

            //S: Проверяем все ли сборочные листа распечатаны
            ConsignmentOutboundOrder::checkAndSetStatusComplete($outboundOrderModel->consignment_outbound_order_id);
            //E: Проверяем все ли сборочные листа распечатаны

            //S: Высчитываем m3 всех коробов заказа и выставляем всем inbound order статус complete
            $m3Sum = 0;
            foreach($items as $boxM3) {
                if(isset($boxM3['box_size_m3']) && !empty($boxM3['box_size_m3']))
                    $m3Sum += $boxM3['box_size_m3'];

                if(isset($boxM3['inbound_order_id'])){
                    if($io = InboundOrder::findOne($boxM3['inbound_order_id'])){
                        if($io->status != Stock::STATUS_INBOUND_COMPLETE && $io->expected_qty == $io->accepted_qty){
                            $io->status = Stock::STATUS_INBOUND_COMPLETE;
                            if($io->save(false)){
                                InboundOrderItem::updateAll(['status'=>Stock::STATUS_INBOUND_COMPLETE], ['inbound_order_id'=>$io->id]);
                            }
                        }
                    }
                }
            }

            if($model) {
//            VarDumper::dump($modelDP,10,true);
//            die($modelDP);
                $model->mc = $m3Sum;
                $model->mc_actual = $m3Sum;
                $model->number_places_actual = count($items);
                $model->number_places = count($items);
                $model->save(false);
            }

            $outboundOrderModel->mc = $m3Sum;
            $outboundOrderModel->accepted_number_places_qty = count($items);
            $outboundOrderModel->save(false);
            if($dpOrderModel = TlDeliveryProposalOrders::findOne(['order_id'=>$outboundOrderModel->id])) {
                $dpOrderModel->number_places = count($items);
                $dpOrderModel->mc = $m3Sum;
                $dpOrderModel->mc_actual = $m3Sum;
                $dpOrderModel->save(false);
            }
            //E: Высчитываем m3 всех коробов заказа
        }


        return $this->render('_box-label-pdf', ['boxes' => $items, 'model' => $model,'outboundOrderModel'=>$outboundOrderModel]);
    }

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
                }

                if($existRow){
                    $doubles =[];
                    //VarDumper::dump($double2, 10, true); die();
                    $rows = array_count_values($existRow);
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
                        return $this->redirect('colins-outbound');
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
                if ($coo =$this->createConsignmentOutboundOrder()) {
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
                                        $oo->delivery_type = OutboundOrder::DELIVERY_TYPE_RPT;
                                        $oo->status = Stock::STATUS_OUTBOUND_NEW;
                                        if ($oo->save(false)) {
                                            //создаем заявки и заказы к заявкам
                                            $oo->createDeliveryProposal('RPT ['.$oo->order_number.']');
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
                                Stock::AllocateByOutboundOrderId($outboundOrder->id);

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
        $partyNumber = 'Tir-'.date('dmy');
        $data = false;
        $consignmentOutboundOrder = new ConsignmentOutboundOrder();
        $consignmentOutboundOrder->client_id = Client::CLIENT_COLINS;
        $consignmentOutboundOrder->party_number = $partyNumber;
        $consignmentOutboundOrder->delivery_type = OutboundOrder::DELIVERY_TYPE_RPT;
        $consignmentOutboundOrder->status = Stock::STATUS_OUTBOUND_NEW;
        if($consignmentOutboundOrder->save(false)){
            $data = $consignmentOutboundOrder;
        }

        return $data;
    }
}
