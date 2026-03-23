<?php

use yii\db\Schema;
use yii\db\Migration;

class m150318_032503_create_table_return_orders extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%return_orders}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT "Client store id"',
            'warehouse_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT "Warehouse store id"',

            'order_number' => Schema::TYPE_INTEGER . '  NULL COMMENT "Order number, received from the client"',

            'status' => Schema::TYPE_SMALLINT . " NULL DEFAULT '0' COMMENT 'Status new, in process, complete, etc'",

            'expected_qty' => Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT "Expected product quantity in return"',
            'accepted_qty' => Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT "Accepted product quantity in return"',

            'begin_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The start time of the scan order"',
            'end_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The end time of the scan order"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',

            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%return_orders}}');
    }
}
