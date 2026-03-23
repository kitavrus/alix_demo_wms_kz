<?php

use yii\db\Schema;
use yii\db\Migration;

class m140812_153955_create_new_table_inbound_order_items extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%inbound_order_items}}', [
            'id' => Schema::TYPE_PK,
            'inbound_order_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Internal inbound order id"',
            'product_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Internal product id"',
            'product_barcode' => Schema::TYPE_STRING . '(54) NOT NULL COMMENT "Scanned product barcode"',
            'status' => Schema::TYPE_SMALLINT . " NOT NULL DEFAULT '0' COMMENT 'Status new, scanned'",

			'expected_qty' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Expected product quantity in order"',
			'accepted_qty' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Accepted product quantity in order"',

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
        $this->dropTable('{{%inbound_order_items}}');
    }
}
