<?php

use yii\db\Migration;

class m170517_043605_add_field_accepted_expected_number_places_qty_table_inbound_order_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_order_items}}','accepted_number_places_qty',$this->integer()->defaultValue(0)->comment("Accepted number places quantity in order")->after('allocated_qty'));
        $this->addColumn('{{%inbound_order_items}}','expected_number_places_qty',$this->integer()->defaultValue(0)->comment("Expected number places quantity in order")->after('accepted_number_places_qty'));
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_order_items}}','accepted_number_places_qty');
        $this->dropColumn('{{%inbound_order_items}}','expected_number_places_qty');
        return false;
    }
}