<?php

use yii\db\Schema;
use yii\db\Migration;

class m140730_200738_create_new_table_store extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%store}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . '(128) NOT NULL',
            'title' => Schema::TYPE_STRING . '(255) NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NOT NULL',
            'address_type' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'country' => Schema::TYPE_STRING . '(128) NOT NULL',
            'region' => Schema::TYPE_STRING . '(128) NOT NULL',
            'city' => Schema::TYPE_STRING . '(128) NOT NULL',
            'zip_code' => Schema::TYPE_STRING . '(9) NOT NULL',
            'street' => Schema::TYPE_STRING . '(128) NOT NULL',
            'house' => Schema::TYPE_STRING . '(6) NOT NULL',
            'entrance' => Schema::TYPE_STRING . '(6) NOT NULL',
            'flat' => Schema::TYPE_STRING . '(6) NOT NULL',
            'intercom' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'floor' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'elevator' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'comment' => Schema::TYPE_TEXT . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%store}}');
    }
}