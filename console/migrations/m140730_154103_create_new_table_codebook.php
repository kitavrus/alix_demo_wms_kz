<?php

use yii\db\Schema;
use yii\db\Migration;

class m140730_154103_create_new_table_codebook extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%codebook}}', [
            'id' => Schema::TYPE_PK,
            'cod_prefix' => Schema::TYPE_STRING . '(3) NOT NULL',
            'name' => Schema::TYPE_STRING . '(128) NOT NULL',
            'count_cell' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'barcode' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'created_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'modified_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%codebook}}');
    }
}
