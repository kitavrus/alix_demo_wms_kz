<?php

use yii\db\Schema;
use yii\db\Migration;

class m150318_040226_add_fields_extra_fields_table_return_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%return_orders}}','extra_fields',Schema::TYPE_TEXT . ' DEFAULT "" COMMENT "Example JSON: order_number,
who received order, etc ... " AFTER `end_datetime`');
    }

    public function down()
    {
        $this->dropColumn('{{%return_orders}}','extra_fields');
    }
}
