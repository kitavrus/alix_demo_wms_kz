<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.07.2017
 * Time: 7:41
 */

namespace stockDepartment\modules\wms\managers\miele;


use common\modules\client\models\Client;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\inbound\models\InboundOrderSyncValue;
use common\modules\inbound\models\OutboundOrderSyncValue;
use common\modules\movement\models\Movement;
use common\modules\movement\models\MovementItems;
use common\modules\movement\models\MovementOrderSyncValue;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\ConstantZone;
use common\modules\stock\models\Stock;
use common\modules\product\models\Product;
use common\modules\outbound\models\OutboundOrder;
use common\modules\inbound\models\InboundOrder;

class Repository
{
    private $dtoForSync;
    private $dtoForSyncOutbound;
    private $dtoForSyncMovement;

    public function getDtoForSync() {
        file_put_contents('Repository-getDtoForSync.log',print_r($this->dtoForSync,true)." / ".date('Ymd-H:i:s')."\n",FILE_APPEND);
        return $this->dtoForSync;
    }

    public function getDtoForSyncOutbound() {
        file_put_contents('Repository-getDtoForSyncOutbound.log',print_r($this->dtoForSyncOutbound,true)." / ".date('Ymd-H:i:s')."\n",FILE_APPEND);
        return $this->dtoForSyncOutbound;
    }

    public function getDtoForSyncMovement() {
        file_put_contents('Repository-getDtoForSyncMovement.log',print_r($this->dtoForSyncMovement,true)." / ".date('Ymd-H:i:s')."\n",FILE_APPEND);
        return $this->dtoForSyncMovement;
    }

    // INBOUND
    // +
    public function createInbound($data) {
        $inbound = $this->addInboundOrder($data['order']);
        $this->addInboundItem($data['items'],$inbound);
        // TODO подсчитать expected_qty для addInboundOrder;
    }
    // +
    private function addInboundOrder($item) {

            $inbound = InboundOrder::find()->andWhere(['client_id'=>Client::CLIENT_MIELE,'client_order_id'=>$item['client_order_id']])->one();
            if(empty($inbound)) {
                $inbound = new InboundOrder();
            }

            $inbound->client_id  = Client::CLIENT_MIELE;
            $inbound->client_order_id  = $item['client_order_id'];
            $inbound->order_number  = $item['order_number'];
//            $inbound->parent_order_number  = $item['parent_order_number'];
//            $inbound->status  = Stock::STATUS_INBOUND_NEW;
            $inbound->status  =  $item['status'];
            $inbound->zone  = $item['zone'];
            $inbound->data_created_on_client  = strtotime($item['data_created_on_client']);
            $inbound->comments  = $item['comments'];
            $inbound->extra_fields  = $item['extra_fields'];
            $inbound->save(false);

            $this->createDtoForSync($inbound);

        return $inbound;
    }
    // +
    private function createDtoForSync($inbound) {
        $sync = new \stdClass();
        $sync->client_id = $inbound->id;
        $sync->client_order_id = $inbound->client_order_id;
        $sync->inbound_id = $inbound->id;
        $sync->status = $inbound->status;
        $this->dtoForSync = $sync;

        file_put_contents('Repository-createDtoForSync.log',print_r($this->dtoForSync,true)." / ".date('Ymd-H:i:s')."\n",FILE_APPEND);
    }
    // +
    private function addInboundItem($items,$inbound) {
        if(!empty($items) && isset($inbound->id)) {
            $qty = 0;
            $toUpdateMATMAS = [];
            InboundOrderItem::deleteAll(['inbound_order_id'=> $inbound->id]);
            foreach($items as $item) {

//                $inboundItem = InboundOrderItem::find()
//                                ->andWhere([
//                                    'inbound_order_id'=>$inbound->id,
//                                    'product_sku'=>$item['product_sku'],
//                                    'product_model'=>$item['product_model'],
//                                    'product_barcode'=>$item['product_barcode'],
//                                ])
//                                ->one();
//
//                if(empty($inboundItem)) {
                    $inboundItem = new InboundOrderItem();
                    $inboundItem->status  = $inbound->status;
                    $inboundItem->inbound_order_id = $inbound->id;
//                }

                $inboundItem->product_sku = $item['product_sku'];
                $inboundItem->product_model = $item['product_model'];
                $inboundItem->product_name = $item['product_name'];
                $inboundItem->product_barcode = $item['product_barcode'];
                $inboundItem->expected_qty = $item['expected_qty'];
                $inboundItem->product_serialize_data = $item['product_serialize_data'];
                $inboundItem->save(false);

                $qty += $item['expected_qty'];

                if(!$this->isExistEAN11($inboundItem->product_barcode, $inboundItem->product_sku)) {
                    $dtoInbound = new InboundDTO();
                    $dtoMasterData = new MasterDataDTO();
                    $toUpdateMATMAS[] = $dtoMasterData->parseItem($dtoInbound->makeItemNomenclature($inboundItem));
                   //   $this->updateMATMAS($toUpdateMATMAS);
                }
            }
            $this->updateMATMAS($toUpdateMATMAS);
            $inbound->expected_qty = $qty;
            $inbound->save(false);
        }
    }
    //T
    private function isExistEAN11($barcode,$sku) {
        return Product::find()->andWhere([
            'client_id'=> Client::CLIENT_MIELE,
            'barcode'=> $barcode,
            'field_extra1'=> $sku,
        ])->exists();
    }
    //T
    public function getInbounds($dto) {

        $orders = InboundOrder::find()->andWhere([
            'client_id'=>Client::CLIENT_MIELE,
            'client_order_id'=>$dto['ids']
        ])->all();
        $result = [];
        if($orders) {
            foreach($orders as $order) {
                $items = InboundOrderItem::find()->andWhere(['inbound_order_id'=>$order->id])->all();
                $result[] = ['order'=>$order,'items'=>$items];
            }
        }

        return $result;
    }
    //T
    public function GetChangedInboundOrders() {

        $ids = InboundOrderSyncValue::find()
                ->select('inbound_client_id')
                ->andWhere(['client_id'=>Client::CLIENT_MIELE])
                ->andWhere('status_our != status_client')
                ->column();

        $orders = InboundOrder::find()->andWhere(['client_order_id'=>$ids])->all();
        $result = [];
        if($orders) {
            foreach($orders as $order) {
                    $items = InboundOrderItem::find()
                              ->andWhere(['inbound_order_id' => $order->id])
                              ->all();
                $result[] = ['order'=>$order,'items'=>$items];
            }
        }

        return $result;
    }
     //T
    public function markAsUnchangedInboundOrder($dto) {
        $inboundSync = new InboundSyncService();
        if(!$inboundSync->setClientStatus($dto['id'],DTO::mapClientStatusToOur($dto['status']))) {
//            throw new \SoapFault("303","Заявка ".$extraField->СтрочноеПредставлениеДокументаПрообраза.' не может быть отменена. Статус в системе ЛО : '."302");
        }
//        $inboundSync = InboundOrderSyncValue::find()->andWhere(['inbound_client_id'=>$dto['id']])->one();
//        if(!empty($inboundSync)) {
//            $inboundSync->status_client = DTO::mapClientStatusToOur($dto['status']);
//            $inboundSync->save(false);
//        } else {
//            throw new \SoapFault();
//        }
//        return InboundOrder::updateAll(['status'=>$dto['status']],['client_order_id'=>$dto['id']]);
    }
     //T
    public function cancelInbounds($dto) {
        $inbound = InboundOrder::find()->andWhere(['client_order_id'=>$dto['id']])->one();
        if(!empty($inbound)) {
            $inbound->status = Stock::STATUS_INBOUND_CANCEL;
            $inbound->save(false);
        } else {
//            throw new \SoapFault();
        }
    }

    // OUTBOUND
    // T
    public function createOutbound($data) {
        $outbound = $this->addOutboundOrder($data['order']);
        $this->isAllocateByFabNumber($data['items']);
        $this->addOutboundItem($data['items'],$outbound);
        $this->calculateExpectedQtyOutbound($outbound);
    }
    // T
    private function addOutboundOrder($order)
    {
        $outbound = OutboundOrder::find()->andWhere(['client_id'=>Client::CLIENT_MIELE,'client_order_id'=>$order['client_order_id']])->one();
        if(empty($outbound)) {
            $outbound = new OutboundOrder();
            $outbound->client_id  = Client::CLIENT_MIELE;
        }

        $outbound->client_order_id = $order['client_order_id'];
        $outbound->parent_order_number = $order['parent_order_number'];
        $outbound->order_number = $order['order_number'];
        $outbound->data_created_on_client = strtotime($order['data_created_on_client']);
        $outbound->zone = $order['zone'];
        $outbound->status = Stock::STATUS_OUTBOUND_NEW;
        $outbound->description = $order['description'];
        $outbound->to_point_title = $order['to_point_title'];
        $outbound->extra_fields = $order['extra_fields'];
        $outbound->save(false);

        $this->createDtoForSyncOutbound($outbound);

        return $outbound;
    }
    // T
    private function isAllocateByFabNumber(&$items) {
        if(!empty($items) && is_array($items)) {
            foreach($items as $key=>$item) {
                $items[$key]['product_barcode'] = '';
                $items[$key]['product_name'] = '';
                $items[$key]['product_id'] = 0;

                $product = Product::find()->andWhere([
                    'client_id' =>Client::CLIENT_MIELE,
                    'field_extra1' =>$item['product_sku'],
                    'model' =>$item['product_model'],
                ])->one();
                if($product) {
                    $items[$key]['product_barcode'] = $product->barcode;
                    $items[$key]['product_name'] = $product->name;
                    $items[$key]['product_id'] = $product->id;
//                    if($product->field_extra3 && empty($items[$key]['field_extra1'])) {
//                        $items[$key]['field_extra1'] = Constants::ALLOCATION_BY_FAB_KEY;
//                    }
                } else {
                    $product = Product::find()->andWhere([
                        'client_id' => Client::CLIENT_MIELE,
                        'model' => $item['product_model'],
                        'sku' => $item['product_sku'],
                    ])->one();
                    if ($product) {
                        $items[$key]['product_barcode'] = $product->barcode;
                        $items[$key]['product_name'] = $product->name;
                        $items[$key]['product_id'] = $product->id;
//                        if($product->field_extra3 && empty($items[$key]['field_extra1'])) {
//                            $items[$key]['field_extra1'] = Constants::ALLOCATION_BY_FAB_KEY;
//                        }
                    }
                }
            }
        }
    }
    // T
    private function addOutboundItem($items,$outbound) {
        if(!empty($items) && is_array($items)) {
            OutboundOrderItem::deleteAll(['outbound_order_id'=> $outbound->id]);
            foreach($items as $item) {
                $outboundItem = new OutboundOrderItem();
                $outboundItem->outbound_order_id = $outbound->id;
                $outboundItem->status = Stock::STATUS_OUTBOUND_NEW;
                $outboundItem->product_id = $item['product_id'];
                $outboundItem->product_name = $item['product_name'];
                $outboundItem->product_barcode = $item['product_barcode'];
                $outboundItem->product_sku = $item['product_sku'];
                $outboundItem->product_model = $item['product_model'];
                $outboundItem->expected_qty = $item['expected_qty'];
                $outboundItem->field_extra1 = $item['field_extra1'];
                $outboundItem->product_serialize_data = $item['product_serialize_data'];
                $outboundItem->save(false);
            }
        }
    }
    private function calculateExpectedQtyOutbound($outbound) {
        $outbound->expected_qty = OutboundOrderItem::find()->andWhere(['outbound_order_id'=>$outbound->id])->sum('expected_qty');
        $outbound->save(false);
    }
    // T
    private function createDtoForSyncOutbound($outbound) {
        $sync = new \stdClass();
        $sync->client_id = $outbound->id;
        $sync->client_order_id = $outbound->client_order_id;
        $sync->outbound_id = $outbound->id;
        $sync->status = $outbound->status;
        $this->dtoForSyncOutbound = $sync;
        file_put_contents('Repository-dtoForSyncOutbound.log',print_r($this->dtoForSyncOutbound,true)." / ".date('Ymd-H:i:s')."\n",FILE_APPEND);
    }
    // T
    private function createDtoForSyncMovement($movement) {
        $sync = new \stdClass();
        $sync->client_id = $movement->id;
        $sync->client_order_id = $movement->client_order_id;
        $sync->movement_id = $movement->id;
        $sync->status = $movement->status;
        $this->dtoForSyncMovement = $sync;
        file_put_contents('Repository-createDtoForSyncMovement.log',print_r($this->dtoForSyncMovement,true)." / ".date('Ymd-H:i:s')."\n",FILE_APPEND);
    }
    // T
    public function getOutbounds($dto)
    {
        $orders = OutboundOrder::find()->andWhere([
            'client_id'=>Client::CLIENT_MIELE,
            'client_order_id'=>$dto['ids']]
        )->all();
        $result = [];
        if($orders) {
            foreach($orders as $order) {
                $items = OutboundOrderItem::find()->andWhere(['outbound_order_id'=>$order->id])->all();
                $result[] = ['order'=>$order,'items'=>$items];
            }
        }
        return $result;
    }
    // T
    public function getChangedOutbounds() {

        $ids = OutboundOrderSyncValue::find()
            ->select('outbound_client_id')
            ->andWhere(['client_id'=>Client::CLIENT_MIELE])
            ->andWhere('status_our != status_client')
            ->column();

        $orders = OutboundOrder::find()->andWhere(['client_order_id'=>$ids])->all();
        $result = [];
        if($orders) {
            foreach($orders as $order) {
                $items = OutboundOrderItem::find()
                    ->andWhere(['outbound_order_id' => $order->id])
                    ->all();
                $result[] = ['order'=>$order,'items'=>$items];
            }
        }

        return $result;
    }
    // T
    public function cancelOutbounds($dto) {
        $outbound = OutboundOrder::find()->andWhere(['client_order_id'=>$dto['id']])->one();
        if(!empty($outbound)) {
            $outbound->status = Stock::STATUS_OUTBOUND_CANCEL;
            $outbound->save(false);
        } else {
//            throw new \SoapFault();
        }
    }
    // T
    public function markAsUnchangedOutboundOrder($dto)
    {
        $outboundSync = new OutboundSyncService();
        if(!$outboundSync->setClientStatus($dto['id'],DTO::mapOutboundClientStatusToOur($dto['status']))) {
//            throw new \SoapFault("303","Заявка ".$extraField->СтрочноеПредставлениеДокументаПрообраза.' не может быть отменена. Статус в системе ЛО : '."302");
        }
    }
    //T
    public function updateMATMAS($dto)
    {
        if(!empty($dto) && is_array($dto)) {
            foreach($dto as $item) {
                $product = Product::find()->andWhere([
                    'client_id'=> Client::CLIENT_MIELE,
                    'barcode'=> $item['barcode'],
                    'field_extra1'=> $item['field_extra1'],
                ])->one();
                if(empty($product)) {
                    $product = new Product();
                }

                $item['client_id'] = Client::CLIENT_MIELE;
                $item['created_user_id'] = Client::CLIENT_MIELE;
                $item['updated_user_id'] = Client::CLIENT_MIELE;
                $product->setAttributes($item,false);
                $product->save(false);
            }
        }

    }


    public function sendMovementOrder($dtoRequest) {
        $movement = $this->addMovementOrder($dtoRequest['order']);
        $this->isAllocateByFabNumber($dtoRequest['items']);
        $this->addMovementOrderItem($dtoRequest['items'],$movement);
        $this->calculateExpectedQtyMovement($movement);
    }

    private function addMovementOrder($data)
    {
        $movement = Movement::find()->andWhere(['client_id'=>Client::CLIENT_MIELE,'client_order_id'=>$data['client_order_id']])->one();
        if(empty($outbound)) {
            $movement = new Movement();
            $movement->client_id  = Client::CLIENT_MIELE;
        }

        $movement->client_order_id = $data['client_order_id'];
        $movement->order_number = $data['order_number'];
        $movement->from_zone = $data['from_zone'];
        $movement->to_zone = $data['to_zone'];
        $movement->client_datetime = $data['client_datetime'];
        $movement->status = $data['status'];
        $movement->comments = $data['comments'];
        $movement->extra_fields = $data['extra_fields'];

        $movement->save(false);

        $this->createDtoForSyncMovement($movement);

        return $movement;
    }

    private function addMovementOrderItem($data,$movement) {
        if(empty($data) || empty($movement)) {
            return false;
        }
        MovementItems::deleteAll(['movement_id'=> $movement->id]);
        foreach($data as $key=>$item) {
            $movementItem = new MovementItems();
            $movementItem->movement_id = $movement->id;
            $movementItem->product_sku = $item['product_sku'];
            $movementItem->product_model = $item['product_model'];
            $movementItem->product_name = $item['product_name'];
            $movementItem->product_barcode = $item['product_barcode'];
            $movementItem->field_extra1 = $item['field_extra1'];
            $movementItem->field_extra2 = $item['field_extra2'];
            $movementItem->field_extra3 = $item['field_extra3'];
            $movementItem->expected_qty = $item['expected_qty'];
            $movementItem->field_extra4 = $item['field_extra4'];
            $movementItem->field_extra5 = $item['field_extra5'];
            $movementItem->product_serialize_data = $item['product_serialize_data'];

            $movementItem->save(false);
        }

        return true;
    }

    private function calculateExpectedQtyMovement($movement) {
        $movement->expected_qty = MovementItems::find()->andWhere(['movement_id'=>$movement->id])->sum('expected_qty');
        $movement->save(false);
    }

    public function cancelMovementOrder($dto) {
        $move = Movement::find()->andWhere(['client_order_id'=>$dto['id']])->one();
        $move->status = ConstantZone::STATUS_CANCEL;
        $move->save(false);
    }

    public function getMovementOrders($dto)
    {
        $orders = Movement::find()->andWhere([
            'client_id'=>Client::CLIENT_MIELE,
            'client_order_id'=>$dto['ids']
        ])->all();

        $result = [];
        if($orders) {
            foreach($orders as $order) {
                $items = MovementItems::find()->andWhere(['movement_id'=>$order->id])->all();
                $result[] = ['order'=>$order,'items'=>$items];
            }
        }
        return $result;
    }

    public function getChangedMovementOrders()
    {
        $ids = MovementOrderSyncValue::find()
            ->select('movement_client_id')
            ->andWhere(['client_id'=>Client::CLIENT_MIELE])
            ->andWhere('status_our != status_client')
            ->column();

        $orders = Movement::find()->andWhere(['client_order_id'=>$ids])->all();
        $result = [];
        if($orders) {
            foreach($orders as $order) {
                $items = MovementItems::find()
                    ->andWhere(['movement_id' => $order->id])
                    ->all();
                $result[] = ['order'=>$order,'items'=>$items];
            }
        }

        return $result;
    }

    public function markAsUnchangedMovementOrder($dto) {
        $movementSync = new MovementSyncService();
        if(!$movementSync->setClientStatus($dto['id'],DTO::mapOutboundClientStatusToOur($dto['status']))) {
        }
    }

    //?
    public function getStock($dtoRequest) {
       return Stock::find()
            ->select('*,count(id) as product_barcode_count')
            ->andWhere(['client_id'=>Client::CLIENT_MIELE])
            ->groupBy('product_barcode, zone')
            ->all();

//        $stock = Stock::find()
//            ->select('*,count(id) as product_barcode_count')
//            ->andWhere(['client_id'=>Client::CLIENT_MIELE])
//            ->groupBy('product_barcode, zone')
//            ->all();
//
//        return [];
    }
    //?
    public function getSerialStock($dtoRequest) {
       return Stock::find()
//            ->select('*,count(id) as product_barcode_count')
            ->andWhere(['client_id'=>Client::CLIENT_MIELE])
            ->groupBy('product_barcode, zone')
            ->all();
//        return Stock::find()->andWhere(['client_id'=>Client::CLIENT_MIELE])->all();
    }

    public function getProductMovement($dto) {
        $order = [];
        $items = [];
        return ['order'=>$order,'items'=>$items];
    }

    public function updateProductRange($productsData) {}

    public function inboundFindByID($id) {
        return InboundOrder::find()->andWhere([
            'client_id'=>Client::CLIENT_MIELE,
            'client_order_id'=>$id
        ])->one();
    }

    public function outboundFindByID($id) {
        return OutboundOrder::find()->andWhere([
            'client_id'=>Client::CLIENT_MIELE,
            'client_order_id'=>$id
        ])->one();
    }

    public function movementFindByID($id) {
        return Movement::find()->andWhere([
            'client_id'=>Client::CLIENT_MIELE,
            'client_order_id'=>$id
        ])->one();
    }

    public function makeTestInboundChangedOrders()
    {
        $iOrderSync1 = new InboundOrderSyncValue();
        $iOrderSync1->client_id = Client::CLIENT_MIELE;
        $iOrderSync1->inbound_client_id = '8e4504a0-6b8f-11e7-a28a-94de80bd5cf8';
        $iOrderSync1->inbound_id = 28426;
        $iOrderSync1->status_our = DTO::mapOurStatusToClient(Stock::STATUS_INBOUND_NEW);
        $iOrderSync1->status_client = DTO::mapOurStatusToClient(Stock::STATUS_INBOUND_SCANNED);
        $iOrderSync1->save(false);

        $iOrderSync2 = new InboundOrderSyncValue();
        $iOrderSync2->client_id = Client::CLIENT_MIELE;
        $iOrderSync2->inbound_client_id = '8e45049f-6b8f-11e7-a28a-94de80bd5cf8';
        $iOrderSync2->inbound_id = 28425;
        $iOrderSync2->status_our = DTO::mapOurStatusToClient(Stock::STATUS_INBOUND_NEW);
        $iOrderSync2->status_client = DTO::mapOurStatusToClient(Stock::STATUS_INBOUND_SCANNED);
        $iOrderSync2->save(false);
    }
}