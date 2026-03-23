<?php

use yii\db\Schema;
use yii\db\Migration;

class m140812_153919_create_new_table_inbound_order extends Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%inbound_order}}', [
			'id' => Schema::TYPE_PK,
			'client_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Client store id"',
			'supplier_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Supplier store id"',
			'warehouse_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Warehouse store id"',
			'order_number' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Order number, received from the client"',
            'status' => Schema::TYPE_SMALLINT . " NOT NULL DEFAULT '0' COMMENT 'Status new, in process, complete, etc'",
			'expected_qty' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Expected product quantity in order"',
			'accepted_qty' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Accepted product quantity in order"',
			'expected_datetime' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "The expected date of delivery in stock"',

            'begin_datetime' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "The start time of the scan order"',
            'end_datetime' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "The end time of the scan order"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',

		], $tableOptions);
	}

	public function down()
	{
		$this->dropTable('{{%inbound_order}}');
	}
}
