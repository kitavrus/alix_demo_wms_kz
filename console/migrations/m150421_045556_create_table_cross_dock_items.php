<?php

use yii\db\Schema;
use yii\db\Migration;

class m150421_045556_create_table_cross_dock_items extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cross_dock_items}}', [
            'id' => Schema::TYPE_PK,
            'cross_dock_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Cross dock id"',
            'box_barcode' => Schema::TYPE_STRING . '(54) NULL DEFAULT "" COMMENT "Scanned box barcode"',
            'status' => Schema::TYPE_SMALLINT . " NULL DEFAULT 0 COMMENT 'Status new, scanned'",

            'expected_number_places_qty' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Expected places quantity in order"',
            'accepted_number_places_qty' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Accepted places quantity in order"',

            'begin_datetime' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "The start time of the scan order"',
            'end_datetime' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "The end time of the scan order"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',

            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%cross_dock_items}}');
    }
}
