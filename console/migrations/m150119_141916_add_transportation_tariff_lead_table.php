<?php

use yii\db\Schema;
use yii\db\Migration;

class m150119_141916_add_transportation_tariff_lead_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%transportation_tariff_company_lead}}', [
            'id' => Schema::TYPE_PK,
            'customer_name' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Contact name"',
            'customer_company_name' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Company name"',
            'customer_position' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Position"',
            'customer_phone' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Phone number"',
            'customer_email' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Email"',
            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Status'",
            'cooperation_type_1' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Type: one-time'",
            'cooperation_type_2' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Type: contract-based full'",
            'cooperation_type_3' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Type: contract-based'",
            'customer_comment' => Schema::TYPE_STRING . '(255)  NULL COMMENT "Comment"',
            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%transportation_tariff_company_lead}}');
    }
}
