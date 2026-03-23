<?php

use yii\db\Schema;
use yii\db\Migration;

class m150408_101640_create_table_consignment_inbound_orders extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%consignment_inbound_orders}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Client store id"',
            'party_number' => Schema::TYPE_INTEGER . ' NULL COMMENT "Party number, received from the client"',
            'order_type' => Schema::TYPE_INTEGER . ' NULL COMMENT "Party type: stock, cross-doc, etc"',
            'status' => Schema::TYPE_SMALLINT . " NULL DEFAULT '0' COMMENT 'Status new, in process, complete, etc'",

            'expected_qty' => Schema::TYPE_INTEGER . ' NULL COMMENT "Expected product quantity in party"',
            'accepted_qty' => Schema::TYPE_INTEGER . ' NULL COMMENT "Accepted product quantity in party"',
            'allocated_qty' => Schema::TYPE_INTEGER . ' NULL COMMENT "Allocated product quantity in party"',

            'accepted_number_places_qty' => Schema::TYPE_INTEGER . ' NULL COMMENT "Accepted number places quantity in party"',
            'expected_number_places_qty' => Schema::TYPE_INTEGER . ' NULL COMMENT "Expected number places quantity in party"',

            'expected_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The expected date of delivery in stock"',

            'begin_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The start time of the scan party"',
            'end_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The end time of the scan party"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',

            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%consignment_inbound_orders}}');
    }
}
