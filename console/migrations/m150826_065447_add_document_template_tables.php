<?php

use yii\db\Schema;
use yii\db\Migration;

class m150826_065447_add_document_template_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%customs_document_template}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING.' NULL DEFAULT "" COMMENT "Название"',
            'description' => Schema::TYPE_TEXT.' NULL DEFAULT "" COMMENT "Описание"',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',
        ], $tableOptions);

        $this->createTable('{{%customs_document_template_items}}', [
            'id' => Schema::TYPE_PK,
            'customs_document_template_id' => Schema::TYPE_INTEGER.' NULL',
            'title' => Schema::TYPE_STRING.' NULL DEFAULT "" COMMENT "Название"',
            'description' => Schema::TYPE_TEXT.' NULL DEFAULT "" COMMENT "Описание"',
            'file' => Schema::TYPE_STRING.' NULL DEFAULT "" COMMENT "Путь к файлу"',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',
        ], $tableOptions);
    }

    public function down()
    {
      $this->dropTable('{{%customs_document_template}}');
      $this->dropTable('{{%customs_document_template_items}}');
    }
}