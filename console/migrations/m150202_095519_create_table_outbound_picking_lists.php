<?php

use yii\db\Schema;
use yii\db\Migration;

class m150202_095519_create_table_outbound_picking_lists extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%outbound_picking_lists}}', [
            'id' => Schema::TYPE_PK,
            'employee_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Employee id"',

            'barcode' => Schema::TYPE_STRING . '(32)  NULL COMMENT "barcode"',
            'employee_barcode' => Schema::TYPE_STRING . '(32)  NULL COMMENT "Barcode: employee "',

            'begin_datetime' => Schema::TYPE_INTEGER . ' NULL  COMMENT "The start time of the picking order"',
            'end_datetime' => Schema::TYPE_INTEGER . '  NULL  COMMENT "The end time of the picking order"',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',

            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%outbound_picking_lists}}');
    }
}
