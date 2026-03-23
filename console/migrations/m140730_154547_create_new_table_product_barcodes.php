<?php

use yii\db\Schema;
use yii\db\Migration;

class m140730_154547_create_new_table_product_barcodes extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%product_barcodes}}', [
            'id' => Schema::TYPE_PK,
            'client_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'product_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'barcode' =>  Schema::TYPE_STRING . '(24) NOT NULL',
            'created_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'modified_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%product_barcodes}}');
    }
}
