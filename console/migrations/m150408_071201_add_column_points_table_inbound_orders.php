<?php

use yii\db\Schema;
use yii\db\Migration;

class m150408_071201_add_column_points_table_inbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_orders}}','extra_fields',Schema::TYPE_TEXT . ' NULL DEFAULT "" COMMENT "Example JSON: order_number, who received order, etc ..." AFTER `end_datetime`');
        $this->addColumn('{{%inbound_orders}}','from_point_id', Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal from point id" AFTER `warehouse_id`');
        $this->addColumn('{{%inbound_orders}}','to_point_id', Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal to point id" AFTER `from_point_id`');
        $this->addColumn('{{%inbound_orders}}','from_point_title', Schema::TYPE_STRING . '(255) NULL DEFAULT "" COMMENT  "Internal from point title" AFTER `to_point_id`');
        $this->addColumn('{{%inbound_orders}}','to_point_title', Schema::TYPE_STRING . '(255) NULL DEFAULT "" COMMENT  "Internal to point title" AFTER `from_point_title`');
        $this->addColumn('{{%inbound_orders}}','parent_order_number', Schema::TYPE_STRING . '(64) NULL DEFAULT "" COMMENT  "Parent order number" AFTER `order_number`');
        $this->addColumn('{{%inbound_orders}}','consignment_inbound_order_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT  "Consignment order internal id" AFTER `parent_order_number`');
        $this->addColumn('{{%inbound_orders}}','client_box_barcode',  Schema::TYPE_STRING . '(128) NULL DEFAULT "" COMMENT  "Client barcode box" AFTER `parent_order_number`');
        $this->addColumn('{{%inbound_orders}}','data_created_on_client',  Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT  "Date time created order on client system" AFTER `extra_fields`');
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_orders}}','extra_fields');
        $this->dropColumn('{{%inbound_orders}}','from_point_id');
        $this->dropColumn('{{%inbound_orders}}','to_point_id');
        $this->dropColumn('{{%inbound_orders}}','from_point_title');
        $this->dropColumn('{{%inbound_orders}}','to_point_title');
        $this->dropColumn('{{%inbound_orders}}','parent_order_number');
        $this->dropColumn('{{%inbound_orders}}','consignment_inbound_order_id');
        $this->dropColumn('{{%inbound_orders}}','client_box_barcode');
        $this->dropColumn('{{%inbound_orders}}','data_created_on_client');
    }
}
