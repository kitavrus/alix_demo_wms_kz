<?php

use yii\db\Schema;
use yii\db\Migration;

class m150603_111322_add_indexes extends Migration
{
    public function up()
    {

        //delivery proposals
        $this->createIndex('deleted', 'tl_delivery_proposals', 'deleted');
        $this->createIndex('client_id', 'tl_delivery_proposals', 'client_id');
        $this->createIndex('route_from', 'tl_delivery_proposals', 'route_from');
        $this->createIndex('route_to', 'tl_delivery_proposals', 'route_to');
        $this->createIndex('status', 'tl_delivery_proposals', 'status');

        //cross dock
        $this->createIndex('deleted', 'cross_dock', 'deleted');
        $this->createIndex('client_id', 'cross_dock', 'client_id');
        $this->createIndex('status', 'cross_dock', 'status');
        $this->createIndex('order_number', 'cross_dock', 'order_number');
        $this->createIndex('consignment_cross_dock_id', 'cross_dock', 'consignment_cross_dock_id');

        //cross dock items
        $this->createIndex('deleted', 'cross_dock_items', 'deleted');
        $this->createIndex('cross_dock_id', 'cross_dock_items', 'cross_dock_id');
        $this->createIndex('status', 'cross_dock_items', 'status');
        $this->createIndex('box_barcode', 'cross_dock_items', 'box_barcode');

        //inbound orders
        $this->createIndex('deleted', 'inbound_orders', 'deleted');
        $this->createIndex('client_id', 'inbound_orders', 'client_id');
        $this->createIndex('status', 'inbound_orders', 'status');
        $this->createIndex('order_number', 'inbound_orders', 'order_number');
        $this->createIndex('client_box_barcode', 'inbound_orders', 'client_box_barcode');
        $this->createIndex('consignment_inbound_order_id', 'inbound_orders', 'consignment_inbound_order_id');

        //inbound order items
        $this->createIndex('deleted', 'inbound_order_items', 'deleted');
        $this->createIndex('inbound_order_id', 'inbound_order_items', 'inbound_order_id');
        $this->createIndex('status', 'inbound_order_items', 'status');
        $this->createIndex('box_barcode', 'inbound_order_items', 'box_barcode');


        //outbound orders
        $this->createIndex('deleted', 'outbound_orders', 'deleted');
        $this->createIndex('client_id', 'outbound_orders', 'client_id');
        $this->createIndex('status', 'outbound_orders', 'status');
        $this->createIndex('order_number', 'outbound_orders', 'order_number');
        $this->createIndex('consignment_outbound_order_id', 'outbound_orders', 'consignment_outbound_order_id');

        //outbound order items
        $this->createIndex('deleted', 'outbound_order_items', 'deleted');
        $this->createIndex('outbound_order_id', 'outbound_order_items', 'outbound_order_id');
        $this->createIndex('status', 'outbound_order_items', 'status');
        $this->createIndex('box_barcode', 'outbound_order_items', 'box_barcode');

        //stock
        $this->createIndex('deleted', 'stock', 'deleted');
        $this->createIndex('client_id', 'stock', 'client_id');
        $this->createIndex('inbound_order_id', 'stock', 'inbound_order_id');
        $this->createIndex('outbound_order_id', 'stock', 'outbound_order_id');
        $this->createIndex('product_id', 'stock', 'product_id');
        $this->createIndex('status', 'stock', 'status');
        $this->createIndex('box_barcode', 'stock', 'box_barcode');
        $this->createIndex('primary_address', 'stock', 'primary_address');
        $this->createIndex('secondary_address', 'stock', 'secondary_address');


    }

    public function down()
    {
       return false;
    }

}
