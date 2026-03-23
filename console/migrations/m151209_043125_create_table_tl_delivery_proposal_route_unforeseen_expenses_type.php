<?php

use yii\db\Schema;
use yii\db\Migration;

class m151209_043125_create_table_tl_delivery_proposal_route_unforeseen_expenses_type extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_proposal_route_unforeseen_expenses_type}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . '(128) NULL DEFAULT "" COMMENT "Name"',

            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'show, hide'",

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL DEFAULT 0',

            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_proposal_route_unforeseen_expenses_type}}');
    }
}
