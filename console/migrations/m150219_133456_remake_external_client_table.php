<?php

use yii\db\Schema;
use yii\db\Migration;

class m150219_133456_remake_external_client_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->dropTable('{{%external_client_lead}}');
        $this->createTable('{{%external_client_lead}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NULL',
            'client_type' => Schema::TYPE_INTEGER . ' DEFAULT 0',
            'username' => Schema::TYPE_STRING . ' NULL',
            'legal_company_name' => Schema::TYPE_STRING . ' NULL',
            'full_name' => Schema::TYPE_STRING . ' NULL',
            'phone' => Schema::TYPE_STRING . ' NOT NULL',
            'email' => Schema::TYPE_STRING . ' NOT NULL',
            'status' => Schema::TYPE_INTEGER . ' NULL',
            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        echo "m150219_133456_remake_external_client_table cannot be reverted.\n";

        return false;
    }
}
