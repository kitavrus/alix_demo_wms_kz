<?php

use yii\db\Schema;
use yii\db\Migration;

class m150422_051052_add_field_order_number_table_cross_dock_log extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock_log}}','order_number', Schema::TYPE_STRING . '(128) NULL DEFAULT "" COMMENT "Order number" AFTER `party_number`');
    }

    public function down()
    {
        $this->dropColumn('{{%cross_dock_log}}','order_number');
    }
}
