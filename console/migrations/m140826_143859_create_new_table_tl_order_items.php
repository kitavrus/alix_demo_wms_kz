<?php

use yii\db\Schema;
use yii\db\Migration;

class m140826_143859_create_new_table_tl_order_items extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_order_items}}', [
            'id' => Schema::TYPE_PK,
            'tl_order_id' => Schema::TYPE_INTEGER . '  NULL COMMENT "Internal transport logistic order id"',
            'box_barcode' => Schema::TYPE_STRING . '(54)  NULL COMMENT "Scanned box barcode"',
            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Status new, scanned'",


//            'begin_datetime' => Schema::TYPE_INTEGER . '  NULL COMMENT "The start time of the scan order"',
//            'end_datetime' => Schema::TYPE_INTEGER . '  NULL COMMENT "The end time of the scan order"',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',

            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_order_items}}');
    }
}
