<?php

use yii\db\Schema;
use yii\db\Migration;

class m150814_120224_create_table_customs_documents extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%customs_documents}}', [
            'id' => Schema::TYPE_PK,
            'customs_account_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Номер инвойса"',
            'customs_account_cost_id' => Schema::TYPE_STRING . ' NULL COMMENT "Расход"',
            'version' => Schema::TYPE_SMALLINT . ' DEFAULT 0 NULL COMMENT "Версия файла"',
            'filename' => Schema::TYPE_STRING . '(256) NULL COMMENT "Название файла"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%customs_documents}}');
    }

}