<?php

use yii\db\Schema;
use yii\db\Migration;

class m150603_083817_add_column_allocated_qty_table_inbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_orders}}','allocated_qty',Schema::TYPE_INTEGER . '(11) DEFAULT 0 AFTER `accepted_qty`');
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_orders}}','allocated_qty');
    }
}
