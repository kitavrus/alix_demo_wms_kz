<?php

use yii\db\Schema;
use yii\db\Migration;

class m150204_105353_create_client_lead_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%external_client_lead}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "ID in User table"',
            'client_name' => Schema::TYPE_STRING . ' NOT NULL COMMENT "Client full name"',
            'client_phone' => Schema::TYPE_STRING . ' NOT NULL',
            'client_email' => Schema::TYPE_STRING . ' NOT NULL',
            'client_type' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Corporative or personal"',
            'company_name' => Schema::TYPE_STRING . ' NULL COMMENT "Client company name"',
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
        $this->dropTable('{{%external_client_lead}}');
    }
}
