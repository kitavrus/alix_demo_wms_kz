<?php

use yii\db\Schema;
use yii\db\Migration;

class m141119_124415_create_table_tl_agent_employees extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_agent_employees}}', [
            'id' => Schema::TYPE_PK,
            'tl_agent_id' => Schema::TYPE_INTEGER.'(11) NOT NULL COMMENT "Tl Agent ID"',
            'user_id' => Schema::TYPE_INTEGER.'(11) NOT NULL COMMENT "User ID" ',
            'username' => Schema::TYPE_STRING . '(128) DEFAULT ""',
            'first_name'=>Schema::TYPE_STRING . '(64) DEFAULT "" COMMENT "First name"',
            'middle_name'=>Schema::TYPE_STRING . '(64) DEFAULT "" COMMENT "Middle name" ',
            'last_name'=>Schema::TYPE_STRING . '(64)  DEFAULT "" COMMENT "Last name"',
            'phone'=>Schema::TYPE_STRING . '(64)  DEFAULT "" COMMENT "Phone" ',
            'phone_mobile'=>Schema::TYPE_STRING . '(64) DEFAULT "" COMMENT "Phone mobile" ',
            'email' => Schema::TYPE_STRING . '(64) DEFAULT "" COMMENT "email"',
            'manager_type' => Schema::TYPE_SMALLINT . ' DEFAULT 0 COMMENT "Manager type: Director, simple manager, etc ..."',
            'status' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'password' => Schema::TYPE_STRING . '(128) DEFAULT NULL COMMENT "Password"',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_agent_employees}}');
    }
}
