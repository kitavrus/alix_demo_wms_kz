<?php

use yii\db\Schema;
use yii\db\Migration;

class m140826_002803_create_new_table_tl_cars extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_cars}}', [
            'id' => Schema::TYPE_PK,
            'agent_id' => Schema::TYPE_INTEGER . ' NULL comment "Agent"',
            'title' => Schema::TYPE_STRING . '(128) NULL',
            'name' => Schema::TYPE_STRING . '(128) NULL',
            'description' => Schema::TYPE_TEXT . ' NULL',
            'status' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',
            'mc' => Schema::TYPE_DECIMAL . '(26,3) NULL DEFAULT "0" comment "Meters cubic"',
            'kg' => Schema::TYPE_DECIMAL . '(26,3) NULL DEFAULT "0" comment "Kilogram"',


            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',

            'created_at' => Schema::TYPE_TIMESTAMP . '  NULL',
            'updated_at' => Schema::TYPE_TIMESTAMP . '  NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_cars}}');
    }
}
