<?php

use yii\db\Schema;
use yii\db\Migration;

class m150318_032514_create_table_return_order_items extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%return_order_items}}', [
            'id' => Schema::TYPE_PK,
            'return_order_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Internal inbound order id"',
            'product_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Internal product id"',
            'product_barcode' => Schema::TYPE_STRING . '(54) NULL DEFAULT "" COMMENT "Scanned product barcode"',
            'status' => Schema::TYPE_SMALLINT . " NULL DEFAULT 0 COMMENT 'Status new, scanned'",

            'expected_qty' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Expected product quantity in order"',
            'accepted_qty' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Accepted product quantity in order"',

            'begin_datetime' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "The start time of the scan order"',
            'end_datetime' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "The end time of the scan order"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',

            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%return_order_items}}');
    }
}
