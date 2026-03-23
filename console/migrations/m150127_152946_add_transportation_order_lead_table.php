<?php

use yii\db\Schema;
use yii\db\Migration;

class m150127_152946_add_transportation_order_lead_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%transportation_order_lead}}', [
            'id' => Schema::TYPE_PK,
            'from_city_id' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'City from'",
            'customer_name' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Contact name"',
            'customer_phone' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Phone number"',
            'customer_address' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Address"',
            'places' => Schema::TYPE_SMALLINT . '(128)  NULL COMMENT "Number of places"',
            'customer_comment' => Schema::TYPE_STRING . '(255)  NULL COMMENT "Comment"',
            'weight' => Schema::TYPE_SMALLINT . '(128)  NULL',
            'volume' => Schema::TYPE_SMALLINT . '(128)  NULL',
            'declared_value' => Schema::TYPE_STRING . '(128)  NULL',
            'package_description' => Schema::TYPE_STRING . '(128)  NULL',
            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '1' COMMENT 'Status'",
            'order_number' => Schema::TYPE_INTEGER . " NOT NULL UNIQUE",
            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%transportation_order_lead}}');
    }
}
