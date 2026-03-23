<?php

use yii\db\Schema;
use yii\db\Migration;

class m140930_060327_create_table_outbound_orders extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%outbound_orders}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Client store id"',
            'supplier_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Supplier store id"',
            'warehouse_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Warehouse store id"',
            'order_number' => Schema::TYPE_INTEGER . ' NULL COMMENT "Order number, received from the client"',
            'order_type' => Schema::TYPE_INTEGER . ' NULL COMMENT "Order type: stock, cross-doc, etc"',
            'status' => Schema::TYPE_SMALLINT . " NULL DEFAULT '0' COMMENT 'Status new, in process, complete, etc'",

            'expected_qty' => Schema::TYPE_INTEGER . ' NULL COMMENT "Expected product quantity in order"',
            'accepted_qty' => Schema::TYPE_INTEGER . ' NULL COMMENT "Accepted product quantity in order"',

            'accepted_number_places_qty' => Schema::TYPE_INTEGER . ' NULL COMMENT "Accepted number places quantity in order"',
            'expected_number_places_qty' => Schema::TYPE_INTEGER . ' NULL COMMENT "Expected number places quantity in order"',

            'expected_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The expected date of delivery in stock"',

            'begin_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The start time of the scan order"',
            'end_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The end time of the scan order"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%outbound_orders}}');
    }
}
