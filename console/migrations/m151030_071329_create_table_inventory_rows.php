<?php

use yii\db\Schema;
use yii\db\Migration;

class m151030_071329_create_table_inventory_rows extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%inventory_rows}}', [
            'id' => Schema::TYPE_PK,
            'inventory_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Inventory id"',
            'column_number' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT "Column number"',

            'expected_qty' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Expected qty"',
            'accepted_qty' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Accepted qty"',

            'expected_places_qty' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Expected place qty"',
            'accepted_places_qty' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Accepted place qty"',

            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Status new, scanned'",

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',

            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_INTEGER . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%inventory_rows}}');
    }
}
