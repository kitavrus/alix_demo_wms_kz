<?php

use yii\db\Schema;
use yii\db\Migration;

class m150205_044210_add_fields_allocate_qty__allocate_number_places_qty_table_outbound_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','allocate_qty',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Allocate quantity" AFTER `accepted_qty`');
        $this->addColumn('{{%outbound_orders}}','allocate_number_places_qty',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Allocate number places quantity" AFTER `expected_number_places_qty`');

    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','allocate_qty');
        $this->dropColumn('{{%outbound_orders}}','allocate_number_places_qty');


        return false;
    }
}
