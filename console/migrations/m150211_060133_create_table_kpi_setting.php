<?php

use yii\db\Schema;
use yii\db\Migration;

class m150211_060133_create_table_kpi_setting extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%kpi_setting}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "ID in Client table"',
            'operation_type' => Schema::TYPE_INTEGER . ' NULL COMMENT "Type: picking, scanning, etc"',
            'one_item_time' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Time second by one operation"',
//            'status' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%kpi_setting}}');
    }
}
