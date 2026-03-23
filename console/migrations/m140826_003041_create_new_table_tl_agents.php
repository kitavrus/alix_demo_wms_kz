<?php

use yii\db\Schema;
use yii\db\Migration;

class m140826_003041_create_new_table_tl_agents extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_agents}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . '(128)  NULL',
            'title' => Schema::TYPE_STRING . '(255)  NULL',
            'phone'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Main phone" ',
            'phone_mobile'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Mobile phone"',

            'description' => Schema::TYPE_TEXT . '  NULL',
            'status' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0',

            'contact_first_name'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Contact first name" ',
            'contact_middle_name'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Contact middle name" ',
            'contact_last_name'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Contact last name" ',
            'contact_phone'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Phone contact " ',
            'contact_phone_mobile'=>Schema::TYPE_STRING . '(64)  NULL COMMENT " Mobile phone contact " ',

            'contact_first_name2'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Contact first name 2" ',
            'contact_middle_name2'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Contact middle name 2" ',
            'contact_last_name2'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Contact last name 2" ',
            'contact_phone2'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Phone contact 2"',
            'contact_phone_mobile2'=>Schema::TYPE_STRING . '(64)  NULL COMMENT "Mobile phone contact 2"',

            'address_title' => Schema::TYPE_STRING . '(256)  NULL',
            'country' => Schema::TYPE_STRING . '(128)  NULL',
            'region' => Schema::TYPE_STRING . '(128)  NULL',
            'city' => Schema::TYPE_STRING . '(128)  NULL',
            'zip_code' => Schema::TYPE_STRING . '(9)  NULL',
            'street' => Schema::TYPE_STRING . '(128)  NULL',
            'house' => Schema::TYPE_STRING . '(6)  NULL',
            'entrance' => Schema::TYPE_STRING . '(6)  NULL',
            'flat' => Schema::TYPE_STRING . '(6)  NULL',
            'intercom' => Schema::TYPE_SMALLINT . '  NULL',
            'floor' => Schema::TYPE_SMALLINT . '  NULL',



            'comment' => Schema::TYPE_TEXT . '  NULL',
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%tl_agents}}');
    }
}
