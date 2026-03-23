<?php

use yii\db\Migration;

class m160621_054841_add_field_inbound_order_item_id_table_consignment_universal_orders_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%consignment_universal_orders_items}}','inbound_order_item_id',$this->integer()->defaultValue('0')->comment("inbound_order_item_id")->after('consignment_universal_order_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%consignment_universal_orders_items}}','inbound_order_item_id');
        return false;
    }
}