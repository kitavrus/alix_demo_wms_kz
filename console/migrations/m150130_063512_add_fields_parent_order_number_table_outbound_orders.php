<?php

use yii\db\Schema;
use yii\db\Migration;

class m150130_063512_add_fields_parent_order_number_table_outbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','parent_order_number',Schema::TYPE_STRING . '(32) NULL DEFAULT "" COMMENT  "Parent order number" AFTER `order_number`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','parent_order_number');

        return false;
    }
}
