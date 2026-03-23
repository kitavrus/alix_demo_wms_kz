<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.10.14
 * Time: 10:49
 */
namespace common\components;

use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\product\models\Product;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\product\models\ProductBarcodes;
use common\components\DeliveryProposalManager;
use Yii;
use yii\base\Component;

class OutboundManager extends Component
{
    /*
     *
     * */
    private $_clientID;
    private $_partyNumber;
    private $_orderNumber;
    private $_consignmentID;
    private $_outboundID;

    /*
     * init base data
     * @param integer $clientId
     * @param string $partyNumber
     * @param string $orderNumber
     * */
    public function initBaseData($clientId,$partyNumber,$orderNumber)
    {
        $this->_clientID = $clientId;
        $this->_partyNumber = $partyNumber;
        $this->_orderNumber = $orderNumber;
    }

    public function setOutboundID($outboundID)
    {
        return $this->_outboundID = $outboundID;
    }

    public function setConsignmentID($consignmentID)
    {
        return $this->_consignmentID = $consignmentID;
    }

    /*
     * Reset outbound, outbound item, Consignment outbound, stock
     * @return boolean true if reset successful
     * */
    // TODO !!! Этот метод ставит статус заказам ОТМЕНА ( CANCEL )
    public function resetByPartyNumber()
    {
        if(!empty($this->_clientID) && !empty($this->_partyNumber)) {
            if ($outboundModelIDs = OutboundOrder::find()->select('id')->where(['client_id' => $this->_clientID, 'parent_order_number' => $this->_partyNumber])->column()) {
                OutboundOrder::updateAll(['data_created_on_client' => '', 'accepted_qty' => '0', 'allocated_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_CANCEL,'cargo_status'=>OutboundOrder::CARGO_STATUS_NEW], ['id' => $outboundModelIDs]);
                ConsignmentOutboundOrder::updateAll([ 'accepted_qty' => '0', 'allocated_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_CANCEL], ['client_id' => $this->_clientID, 'party_number' => $this->_partyNumber]);
                OutboundOrderItem::updateAll([ 'accepted_qty' => '0','allocated_qty' => '0', 'status' => Stock::STATUS_OUTBOUND_CANCEL], ['outbound_order_id' => $outboundModelIDs]);
                OutboundPickingLists::deleteAll(['outbound_order_id' => $outboundModelIDs]);

                Stock::updateAll([
                    'box_barcode' => '',
                    'box_size_barcode' => '',
                    'box_size_m3' => '',
                    'box_kg' => '',
                    'outbound_order_id' => '0',
                    'outbound_picking_list_id' => '0',
                    'outbound_picking_list_barcode' => '',
//                    'status' => Stock::STATUS_NOT_SET,
                    'status' => Stock::STATUS_INBOUND_NEW,
                    'status_availability' => Stock::STATUS_AVAILABILITY_YES
                ], ['outbound_order_id' => $outboundModelIDs]);

                return true;
            }
        }

        return false;
    }

    /*
     * Create or update Consignment outbound
     * @return ActiveRecord ConsignmentOutboundOrder
     * */
    public function createUpdateConsignmentOutbound()
    {
        if(!empty($this->_clientID) && !empty($this->_partyNumber)) {
            if (!($coo = ConsignmentOutboundOrder::findOne(['client_id' => $this->_clientID, 'party_number' => $this->_partyNumber]))) {
                $coo = new ConsignmentOutboundOrder();
                $coo->client_id = $this->_clientID;
                $coo->party_number = $this->_partyNumber;
                $coo->status = Stock::STATUS_OUTBOUND_NEW;
                $coo->save(false);
            }
            $this->_consignmentID = $coo->id;
            return $coo;
        }

        return false;
    }

    /*
    * Create or update Outbound order
    * @param array $data
    * Example:
    * $data = [
    *   'consignment_outbound_order_id'=> '124',
    *   'parent_order_number'=> '10aq12', // Party number External
    *   'order_number'=> '12323w', // Order number. External
    *   'from_point_id'=> '4', //From Our internal point id
    *   'from_point_title'=> '1903', //From Our external shop code
    *   'to_point_id'=> '190', //To Our internal point id
    *   'to_point_title'=> '2453', //To Our external shop code
    *   'data_created_on_client'=> '1426489138', // Format timestamp. Data created on client side
    *   'mc'=> '1426489138', // обьем
    *   'kg'=> '1426489138', // вес
    *   'accepted_number_places_qty'
        'expected_number_places_qty'
         'allocated_number_places_qty'
    * ]
    * @return ActiveRecord OutboundOrder
    * */
    public function createUpdateOutbound($data)
    {
        if(!empty($this->_clientID) && !empty($this->_partyNumber) && !empty($this->_orderNumber)) {
            if (!($o = OutboundOrder::findOne(['client_id' => $this->_clientID, 'parent_order_number' => $this->_partyNumber, 'order_number' => $this->_orderNumber]))) {
                $o = new OutboundOrder();
                $o->status = Stock::STATUS_OUTBOUND_NEW;
            }

            $o->client_id = $this->_clientID;
            $o->consignment_outbound_order_id = isset($data['consignment_outbound_order_id']) ? $data['consignment_outbound_order_id'] : 0;
            $o->parent_order_number = isset($data['parent_order_number']) ? $data['parent_order_number'] : 0;
            $o->order_number = isset($data['order_number']) ? $data['order_number'] : 0;
            $o->data_created_on_client = isset($data['data_created_on_client']) ? $data['data_created_on_client'] : 0;
            $o->from_point_id = isset($data['from_point_id']) ? $data['from_point_id'] : 0;
            $o->from_point_title = isset($data['from_point_title']) ? $data['from_point_title'] : 0;
            $o->to_point_id = isset($data['to_point_id']) ? $data['to_point_id'] : 0;
            $o->to_point_title = isset($data['to_point_title']) ? $data['to_point_title'] : 0;
            $o->expected_qty = 0;
            $o->accepted_qty = 0;
            $o->mc = isset($data['mc']) ? $data['mc'] : 0;
            $o->kg = isset($data['kg']) ? $data['kg'] : 0;
            $o->accepted_number_places_qty = isset($data['accepted_number_places_qty']) ? $data['accepted_number_places_qty'] : 0;
            $o->expected_number_places_qty = isset($data['expected_number_places_qty']) ? $data['expected_number_places_qty'] : 0;
            $o->title = isset($data['title']) ? $data['title'] : '';
            $o->description = isset($data['description']) ? $data['description'] : '';
            $o->save(false);
            $this->_outboundID = $o->id;

            return $o;
        }

        return false;
    }

    /*
     * Add outbound order item
     * $param array $items
     * Example:
     *  $item = [
     *   'product_barcode'=>'123456789',
     *   'expected_qty'=>'5',
     * ]
     * @return boolean
     * */
    public function addItems($items)
    {
        $expected_qty = 0;
        if ($items) {
            foreach ($items as $line) {
                if (!($ooiModel = OutboundOrderItem::findOne(['outbound_order_id' => $this->_outboundID, 'product_barcode' => $line['product_barcode']]))) {
                    $ooiModel = new OutboundOrderItem();
                    $ooiModel->expected_qty = 0;
                    $ooiModel->status = Stock::STATUS_OUTBOUND_NEW;
                    $ooiModel->outbound_order_id = $this->_outboundID;
                    $ooiModel->product_barcode = $line['product_barcode'];
                    $ooiModel->product_name = isset($line['product_name']) ? $line['product_name'] : '';
                }

                $ooiModel->expected_qty += $line['expected_qty'];
                $ooiModel->save(false);

                $expected_qty += $ooiModel->expected_qty;
            }

            if($outboundModel = OutboundOrder::findOne($this->_outboundID)) {
                $outboundModel->expected_qty = $expected_qty;
                $outboundModel->save(false);
            }

            if($consignmentModel = ConsignmentOutboundOrder::findOne($this->_consignmentID)) {
                $consignmentModel->expected_qty += $expected_qty;
                $consignmentModel->save(false);
            }

            return true;
        }

        return false;
    }

    /*
     * Add products
     * $param array $product
     * Example:
     *  $product = [
     *   'product_barcode'=>'123456789',
     *   'category'=>'sdsd',
     *   'article'=>'sdsd',
     *   'model'=>'sdsd',
     *   'color'=>'sdsd',
     *   'size'=>'sdsdsd',
     * ]
     * @return boolean
     * */
    public function addProducts($product)
    {
        if ($product) {
            foreach ($product as $line) {
                if (!($productModel = ProductBarcodes::getProductByBarcode($this->_clientID, $line['product_barcode']))) {
                    $productModel = new Product();
                    $productModel->client_id = $this->_clientID;
                    $productModel->status = Product::STATUS_ACTIVE;
                    $productModel->name = isset($line['product_name']) ? $line['product_name'] : '';
                    $productModel->save(false);

                    $productBarcode = new ProductBarcodes();
                    $productBarcode->client_id = $this->_clientID;
                    $productBarcode->barcode = $line['product_barcode'];
                    $productBarcode->product_id = $productModel->id;
                    $productBarcode->save(false);
                }

                $productModel->sku = $line['article'];
                $productModel->model = $line['model'];
                $productModel->color = $line['color'];
                $productModel->size = $line['size'];
                $productModel->category = $line['category'];
                $productModel->name = isset($line['product_name']) ? $line['product_name'] : '';
                $productModel->save(false);

            }

            return true;
        }

        return false;
    }

    /*
     * Create or update Delivery Proposal and Delivery Proposal Order
     * @return boolean
     * */
    public function createUpdateDeliveryProposalAndOrder($deliveryProposalAttributes = [], $deliveryProposalOrderAttributes = [] )
    {
        if(!empty($this->_clientID) && !empty($this->_outboundID) && !empty($this->_orderNumber)) {

            $outboundModel = OutboundOrder::findOne($this->_outboundID);
            $update = false;

            if($dp = TlDeliveryProposal::find()
                ->andWhere([
                    'route_from'=>$outboundModel->from_point_id,
                    'route_to' =>$outboundModel->to_point_id,
                    'client_id' => $this->_clientID,
                    'status'=>[TlDeliveryProposal::STATUS_NEW]
                ])->one()) {

                if (!($dpOrder = TlDeliveryProposalOrders::findOne(['client_id' => $this->_clientID, 'order_id' => $this->_outboundID, 'order_type'=>TlDeliveryProposalOrders::ORDER_TYPE_RPT, 'order_number' => $this->_orderNumber]))) {
                    $dpOrder = new TlDeliveryProposalOrders();
                }
                $update = true;
            } else {
                $dp = new TlDeliveryProposal();
//                $dpOrder = new TlDeliveryProposalOrders();

                if (!($dpOrder = TlDeliveryProposalOrders::findOne(['client_id' => $this->_clientID, 'order_id' => $this->_outboundID, 'order_type'=>TlDeliveryProposalOrders::ORDER_TYPE_RPT, 'order_number' => $this->_orderNumber]))) {
                    $dpOrder = new TlDeliveryProposalOrders();
                }

            }


//            if ($dpOrder = TlDeliveryProposalOrders::findOne([
//                'client_id' =>$this->_clientID,
//                'order_type'=>TlDeliveryProposalOrders::ORDER_TYPE_RPT,
//                'order_id' => $this->_outboundID,
//                'order_number' => $this->_orderNumber,
//            ])) {
//                $dp = TlDeliveryProposal::findOne($dpOrder->tl_delivery_proposal_id);
//                $update = true;
//            } else {
//                $dp = new TlDeliveryProposal();
//                $dpOrder = new TlDeliveryProposalOrders();
//            }



            $dp->status = TlDeliveryProposal::STATUS_NEW;
            $dp->client_id = $this->_clientID;
            $dp->route_from = $outboundModel->from_point_id;
            $dp->route_to = $outboundModel->to_point_id;
            $dp->cash_no = TlDeliveryProposal::METHOD_CHAR;
            $dp->save(false);

            if(!empty($deliveryProposalAttributes) && is_array($deliveryProposalAttributes)) {
                foreach($deliveryProposalAttributes as $field=>$value) {
                    if($dp->hasAttribute($field)) {
                        $dp->$field = $value;
                        $dp->save(false);
                    }
                }
            }

            if($dpOrder) {
                // Добавить заказы
                $dpOrder->client_id = $dp->client_id;
                $dpOrder->tl_delivery_proposal_id = $dp->id;
                $dpOrder->order_id = $outboundModel->id;
                $dpOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
                $dpOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
                $dpOrder->order_number = $outboundModel->parent_order_number . ' ' . $outboundModel->order_number;
                $dpOrder->kg = $outboundModel->kg;
                $dpOrder->kg_actual = $outboundModel->kg;
                $dpOrder->mc = $outboundModel->mc;
                $dpOrder->mc_actual = $outboundModel->mc;
                $dpOrder->number_places = $outboundModel->accepted_number_places_qty;
                $dpOrder->number_places_actual = $outboundModel->accepted_number_places_qty;
                $dpOrder->title = $outboundModel->title;
                $dpOrder->description = $outboundModel->description;
                $dpOrder->save(false);
            }

            $dpManager = new DeliveryProposalManager(['id' => $dp->id]);

            if($update){
                $dpManager->onUpdateProposal();
            } else {
                $dpManager->onCreateProposal();
            }


            return true;
        }

        return false;
    }

    /*
     * Reserve Outbound order on stock by party number
     * @return boolean
     * */
    public function reservationOnStockByPartyNumber($address = [])
    {
        if(!empty($this->_partyNumber)) {
            if ($oos = OutboundOrder::find()->select('id')->where(['parent_order_number' => $this->_partyNumber])->asArray()->all()) {
                foreach ($oos as $order) {
                    Stock::AllocateByOutboundOrderId($order['id'],$address);
                }
                return true;
            }
        }
        return false;
    }
}