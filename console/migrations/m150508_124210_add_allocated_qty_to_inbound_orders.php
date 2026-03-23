<?php

use yii\db\Schema;
use yii\db\Migration;

class m150508_124210_add_allocated_qty_to_inbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_order_items}}','allocated_qty', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Allocated qty" AFTER `accepted_qty`');
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_order_items}}','allocated_qty');

    }

}
