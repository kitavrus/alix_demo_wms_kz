<?php

use yii\db\Schema;
use yii\db\Migration;

class m140730_155110_create_new_table_sync_products extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sync_products}}', [
            'id' => Schema::TYPE_PK,
            'client_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'client_product_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'name' =>  Schema::TYPE_STRING . '(255) NOT NULL',
            'barcode' =>  Schema::TYPE_STRING . '(24) NOT NULL',
            'sku' =>  Schema::TYPE_STRING . '(64) NOT NULL',
            'article' =>  Schema::TYPE_STRING . '(64) NOT NULL',
            'created_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'modified_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%sync_products}}');
    }
}
