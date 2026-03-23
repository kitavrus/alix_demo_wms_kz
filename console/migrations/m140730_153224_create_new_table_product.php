<?php

use yii\db\Schema;
use yii\db\Migration;

class m140730_153224_create_new_table_product extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%product}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'client_product_id' => Schema::TYPE_STRING . '(32) NOT NULL',
            'name' => Schema::TYPE_STRING . '(256) NOT NULL',
            'sku' => Schema::TYPE_STRING . '(32) NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'created_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'modified_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%product}}');
    }
}
