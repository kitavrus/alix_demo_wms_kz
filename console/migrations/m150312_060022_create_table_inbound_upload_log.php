<?php

use yii\db\Schema;
use yii\db\Migration;

class m150312_060022_create_table_inbound_upload_log extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%inbound_upload_log}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Client id"',

            'unique_key' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Unique key if update exist order"',
            'order_number' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Inbound order number"',
            'product_barcode' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product barcode"',
            'product_model' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product model"',
            'expected_qty' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT "Expected qty"',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%inbound_upload_log}}');

        return true;
    }
}
