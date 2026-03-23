<?php

use yii\db\Schema;
use yii\db\Migration;

class m140801_030710_create_new_table_order_process extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%order_process}}', [
            'id' => Schema::TYPE_PK,
            'store_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Internal store id"',
            'product_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Product id from table product"',
            'status' => Schema::TYPE_SMALLINT . " NOT NULL DEFAULT '0' COMMENT 'Status new, done, etc'",
            'box_barcode' => Schema::TYPE_STRING . '(28) NOT NULL COMMENT "Barcode box into which scan the goods"',
            'product_barcode' => Schema::TYPE_STRING . '(28) NOT NULL COMMENT "Product barcode from table product_barcode. This barcode is scanned item in the box"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%order_process}}');
    }
}
