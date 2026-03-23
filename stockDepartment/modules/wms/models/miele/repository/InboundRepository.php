<?php
namespace stockDepartment\modules\wms\models\miele\repository;


use common\modules\client\models\Client;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\product\models\Product;
use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\managers\miele\InboundSyncService;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class InboundRepository
{
//    private $dtoForSync;

//    public function getDtoForSync() {
//        return $this->dtoForSync;
//    }

    public function makeDtoForSync($orderID)
    {
        $inbound = InboundOrder::find()->andWhere(['id' => $orderID, 'client_id' => Client::CLIENT_MIELE])->one();
        if ($inbound) {
            $sync = new \stdClass();
            $sync->client_id = $inbound->id;
            $sync->client_order_id = $inbound->client_order_id;
            $sync->inbound_id = $inbound->id;
            $sync->status = $inbound->status;
            return $sync;
        }
        return [];
    }

    public function getNewAndInProcessOrder() {
        return InboundOrder::find()
            ->andWhere(['client_id'=>Client::CLIENT_MIELE])
            ->andWhere('status != :status',[':status'=>Stock::STATUS_INBOUND_COMPLETE])
            ->all();
    }

    public function getQtyInOrder($id) {
        return InboundOrder::find()->select('expected_qty, accepted_qty')
            ->andWhere(['id'=>$id,'client_id'=>Client::CLIENT_MIELE])
            ->one();
    }

    public function updateQtyScannedInOrder($orderId) {
        $inbound = InboundOrder::find()->andWhere(['id'=>$orderId,'client_id'=>Client::CLIENT_MIELE])->one();
        if($inbound) {
            $inbound->accepted_qty = $this->getScannedQtyByOrderInStock($orderId);
            $inbound->save(false);
        }
    }

    public function setOrderStatusInProcess($orderId) {
        $inbound = InboundOrder::find()->andWhere(['id'=>$orderId,'client_id'=>Client::CLIENT_MIELE])->one();
        if($inbound) {
            $inbound->status = Stock::STATUS_INBOUND_SCANNING;
            $inbound->save(false);
        }
    }

    public function setOrderItemStatusInProcess($orderId,$productBarcode) {

        $inboundItem = InboundOrderItem::find()->andWhere([
            'inbound_order_id'=>$orderId,
            'product_barcode'=>$productBarcode,
        ])->one();

        if($inboundItem) {
            $inboundItem->status = Stock::STATUS_INBOUND_SCANNING;
            $inboundItem->save(false);

        }
    }

    public function getQtyScannedInBox($inboundId,$boxBarcode) {
        return Stock::find()->andWhere([
            'client_id'=>Client::CLIENT_MIELE,
            'inbound_order_id'=>$inboundId,
            'primary_address'=>$boxBarcode,
            'status'=>Stock::STATUS_INBOUND_SCANNED,
        ])->count();
    }

    public function cleanOurBox($inboundId,$boxBarcode) {
       $stocks =  Stock::find()->andWhere([
            'client_id'=>Client::CLIENT_MIELE,
            'inbound_order_id'=>$inboundId,
            'primary_address'=>$boxBarcode,
            'status'=>Stock::STATUS_INBOUND_SCANNED,
        ])->all();

        foreach ($stocks as $stock) {
            $productBarcode = $stock->product_barcode;
            $stock->delete();

            $inboundItem = InboundOrderItem::find()->andWhere([
                'inbound_order_id' => $inboundId,
                'product_barcode' => $productBarcode,
            ])->one();

            if ($inboundItem) {
                $inboundItem->accepted_qty = $this->getScannedProductQtyByOrderInStock($inboundId, $productBarcode);
                $inboundItem->save(false);
            }
        }

        $this->updateQtyScannedInOrder($inboundId);
    }

    public function isBoxExist($inboundId,$boxBarcode) {
        return Stock::find()->andWhere([
            'client_id'=>Client::CLIENT_MIELE,
            'inbound_order_id'=>$inboundId,
            'primary_address'=>$boxBarcode,
        ])->exists();
    }

    public function isExistBarcodeInOrder($inboundId,$productBarcode) {
        return InboundOrderItem::find()->andWhere([
            'inbound_order_id'=>$inboundId,
            'product_barcode'=>$productBarcode,
        ])->exists();
    }


    public function isExtraBarcodeInOrder($inboundId,$productBarcode) {
        return InboundOrderItem::find()->andWhere([
                'inbound_order_id'=>$inboundId,
                'product_barcode'=>$productBarcode,
                ])
                ->andWhere('expected_qty = accepted_qty AND expected_qty != 0')->exists();
    }

    public function addScannedProductToStock($dto,$addNoExist = false)
    {
        $productModel = '';
        $productSKU = '';
        if($productInfo = $this->getProductInfo($dto->product_barcode)) {
            $productModel = $productInfo->model;
            $productSKU = $productInfo->sku;
        }

        $stock = new Stock();
        $stock->inbound_order_id = $dto->inbound->order->id;
        $stock->zone = $dto->inbound->order->zone;
        $stock->primary_address = $dto->primary_address;
        $stock->product_barcode = $dto->product_barcode;
        $stock->client_id = Client::CLIENT_MIELE;
        $stock->status = Stock::STATUS_INBOUND_SCANNED;
        $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
        $stock->product_model = $productModel;
        $stock->product_sku = $productSKU;
        $stock->save(false);

        $inboundItemID = $this->updateAcceptedQtyItemByProductBarcode($dto->inbound->order->id,$dto->product_barcode,$addNoExist);
        $stock->inbound_order_item_id = $inboundItemID;
        $stock->save(false);
        return $stock->id;
    }

    public function updateAcceptedQtyItemByProductBarcode($inboundId,$productBarcode,$addNoExist = false) {

        $inboundItem = InboundOrderItem::find()->andWhere([
            'inbound_order_id'=>$inboundId,
            'product_barcode'=>$productBarcode,
        ])->one();

        if($inboundItem) {
            $inboundItem->accepted_qty = $this->getScannedProductQtyByOrderInStock($inboundId,$productBarcode);
            $inboundItem->save(false);
            return $inboundItem->id;
        } elseif($addNoExist) {
            return $this->strategyAddNoExistInOrderProduct($inboundId,$productBarcode);
        }
        return 0;
    }

    /*
     * Добавляем товар которого нет в накладной. это случай пересорта
     * */
    public function strategyAddNoExistInOrderProduct($inboundId,$productBarcode)
    {
        $product = Product::find()->andWhere([
            "client_id"=> Client::CLIENT_MIELE,
            "barcode"=>$productBarcode,
        ])->one();
        if($product) {
            $productSerializeData = [
                'МатНомер' => $product->field_extra1,
                'Артикул' => $product->model,
                'Наименование' => $product->name,
                'ВесБрутто' => $product->weight_brutto,
                'ВесНетто' => $product->weight_netto,
                'Объем' => $product->m3,
                'Длина' => $product->length,
                'Ширина' => $product->width,
                'Высота' => $product->height,
                'EAN11' => $product->barcode,
                'УровеньШтабелирования' => $product->field_extra2,
                'УчетПоФабричнымНомерам' => $product->field_extra3,
                'УчетПоКоммерческимНомерам' => $product->field_extra4,
                'УчетПоСрокамГодности' => $product->field_extra5,
                'ТребуетсяЭтикетирование' => $product->field_extra6,
                'specification' => [
                    'МатНомер' => $product->field_extra1, // 06165000
                    'Артикул' =>$product->model, // 62782410
                    'ФабНомер' => '',
                    'КомНомер' => '',
                    'НомерГТД' => '',
                    'Количество' => 0, // 10
                    'КоличествоНеадаптированное' => '', // 0
                    'КоличествоБрак' => '', // 0
                ],
            ];

            $item = new InboundOrderItem();
            $item->inbound_order_id = $inboundId;
            $item->product_name = $product->name;
            $item->product_model = $product->model;
            $item->product_sku = $product->field_extra1;
            $item->product_barcode = $productBarcode;
            $item->product_serialize_data = serialize($productSerializeData);
            $item->status = Stock::STATUS_INBOUND_SCANNED;
            $item->expected_qty = 0;
            $item->accepted_qty = 1;
            $item->save(false);
            return $item->id;

        } else {
            file_put_contents('InboundRepository-strategyAddNoExistInOrderProduct.log',$inboundId."-".$productBarcode." / ".date('Y-m-d H:i:s')."\n",FILE_APPEND);
        }

        return 0;
    }

    private function getProductInfo($productBarcode) {
        return Product::find()->andWhere([
            "client_id"=> Client::CLIENT_MIELE,
            "barcode"=>$productBarcode,
        ])->one();
    }

    public function isProductExists($productBarcode) {
        return Product::find()->andWhere([
            "client_id"=> Client::CLIENT_MIELE,
            "barcode"=>$productBarcode,
        ])->exists();
    }

    public function isScanByFabBarcode($productBarcode) {
        return Product::find()->andWhere([
            "client_id"=> Client::CLIENT_MIELE,
            "barcode"=>$productBarcode,
            "field_extra3"=>1,
        ])->exists();
    }

    private function getScannedQtyByOrderInStock($inboundId) {
        return Stock::find()->andWhere([
            'inbound_order_id'=>$inboundId,
            'status'=>Stock::STATUS_INBOUND_SCANNED,
        ])->count();
    }

    private function getScannedProductQtyByOrderInStock($inboundId,$productBarcode) {
        return Stock::find()->andWhere([
            'inbound_order_id'=>$inboundId,
            'product_barcode'=>$productBarcode,
            'status'=>Stock::STATUS_INBOUND_SCANNED,
        ])->count();
    }

    public function getOrderInfo($id)
    {
        $order = InboundOrder::find()->andWhere([
            "id" => $id,
            "client_id" => Client::CLIENT_MIELE,
        ])->one();

        $items = InboundOrderItem::find()->andWhere(['inbound_order_id' => $order->id])->all();

        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;

        return $result;
    }
    public function addFabBarcodeToProduct($dto)
    {
       $stock = Stock::find()
                ->andWhere([
                    'inbound_order_id'=>$dto->inbound->order->id,
                    'product_barcode'=>$dto->product_barcode,
                    'primary_address'=>$dto->primary_address,
                    'status'=>Stock::STATUS_INBOUND_SCANNED,
                ])
                ->andWhere("field_extra1 = ''")
                ->one();
        if($stock) {
            $stock->field_extra1 = $dto->fab_barcode;
            $stock->save(false);
        }
    }

    public function getOrderItemsForDiffReport($orderId) {
        if( $io = InboundOrder::findOne($orderId)) {
            return   $io->getOrderItems()->select('*,(expected_qty - accepted_qty) as order_by')->orderBy(new Expression('box_barcode, order_by!=0 DESC'))->asArray()->all();
        }
        return [];
    }

    public function getOrderForComplete()
    {
        $query = InboundOrder::find()->andWhere([
            "client_id" => Client::CLIENT_MIELE,
//            'status' => [
//                Stock::STATUS_INBOUND_ACCEPTED,
//            ]
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
    }

    public function acceptedOrder($orderId)
    {
         //One function
        $orderInfo = $this->getOrderInfo($orderId);
        $orderInfo->order->status = Stock::STATUS_INBOUND_ACCEPTED;
        $orderInfo->order->date_confirm = time();
        $orderInfo->order->save(false);

        foreach($orderInfo->items as $item) {
            $item->status = Stock::STATUS_INBOUND_ACCEPTED;
            $item->save(false);
        }

        $stocks = Stock::find()->andWhere(["client_id" => Client::CLIENT_MIELE,'inbound_order_id'=> $orderInfo->order->id])->all();
        foreach($stocks as $stock ) {
            $stock->status = Stock::STATUS_INBOUND_ACCEPTED;
            $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
            $stock->save(false);
        }
    }
}