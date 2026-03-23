<?php

use yii\db\Schema;
use yii\db\Migration;

class m150408_103802_add_column_points_table_consignment_inbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%consignment_inbound_orders}}','extra_fields',Schema::TYPE_TEXT . ' NULL DEFAULT "" COMMENT "Example JSON: order_number, who received order, etc ..." AFTER `end_datetime`');
        $this->addColumn('{{%consignment_inbound_orders}}','from_point_id', Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal from point id" AFTER `order_type`');
        $this->addColumn('{{%consignment_inbound_orders}}','to_point_id', Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal to point id" AFTER `from_point_id`');
        $this->addColumn('{{%consignment_inbound_orders}}','from_point_title', Schema::TYPE_STRING . '(255) NULL DEFAULT "" COMMENT  "Internal from point title" AFTER `to_point_id`');
        $this->addColumn('{{%consignment_inbound_orders}}','to_point_title', Schema::TYPE_STRING . '(255) NULL DEFAULT "" COMMENT  "Internal to point title" AFTER `from_point_title`');
//        $this->addColumn('{{%consignment_inbound_orders}}','parent_order_number', Schema::TYPE_STRING . '(64) NULL DEFAULT "" COMMENT  "Parent order number" AFTER `party_number`');
//        $this->addColumn('{{%consignment_inbound_orders}}','consignment_inbound_order_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT  "Consignment order internal id" AFTER `party_number`');
//        $this->addColumn('{{%consignment_inbound_orders}}','client_box_barcode',  Schema::TYPE_STRING . '(128) NULL DEFAULT "" COMMENT  "Client barcode box" AFTER `party_number`');
        $this->addColumn('{{%consignment_inbound_orders}}','data_created_on_client',  Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT  "Date time created order on client system" AFTER `extra_fields`');
    }

    public function down()
    {
        $this->dropColumn('{{%consignment_inbound_orders}}','extra_fields');
        $this->dropColumn('{{%consignment_inbound_orders}}','from_point_id');
        $this->dropColumn('{{%consignment_inbound_orders}}','to_point_id');
        $this->dropColumn('{{%consignment_inbound_orders}}','from_point_title');
        $this->dropColumn('{{%consignment_inbound_orders}}','to_point_title');
//        $this->dropColumn('{{%consignment_inbound_orders}}','parent_order_number');
//        $this->dropColumn('{{%consignment_inbound_orders}}','consignment_inbound_order_id');
//        $this->dropColumn('{{%consignment_inbound_orders}}','client_box_barcode');
        $this->dropColumn('{{%consignment_inbound_orders}}','data_created_on_client');
    }
}
