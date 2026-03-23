<?php

use yii\db\Schema;
use yii\db\Migration;

class m150205_052240_add_fields_allocate_qty__allocate_number_places_qty_table_outbound_order_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_order_items}}','expected_number_places_qty',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Expected number places" AFTER `accepted_qty`');
        $this->addColumn('{{%outbound_order_items}}','accepted_number_places_qty',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Accepted number places quantity" AFTER `expected_number_places_qty`');

        $this->addColumn('{{%outbound_order_items}}','allocate_qty',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Allocate number places quantity" AFTER `accepted_qty`');
        $this->addColumn('{{%outbound_order_items}}','allocate_number_places_qty',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Allocate number places quantity" AFTER `accepted_number_places_qty`');

    }

    public function down()
    {
        $this->dropColumn('{{%outbound_order_items}}','allocate_qty');
        $this->dropColumn('{{%outbound_order_items}}','allocate_number_places_qty');

        $this->dropColumn('{{%outbound_order_items}}','expected_number_places_qty');
        $this->dropColumn('{{%outbound_order_items}}','accepted_number_places_qty');


        return false;
    }
}
