<?php

use yii\db\Migration;

class m170720_090107_add_field_client_order_id_table_in_outbound extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_orders}}','client_order_id',$this->string(64)->defaultValue(null)->comment("ID client order")->after('id'));
        $this->addColumn('{{%outbound_orders}}','client_order_id',$this->string(64)->defaultValue(null)->comment("ID client order")->after('id'));
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_orders}}','client_order_id');
        $this->dropColumn('{{%outbound_orders}}','client_order_id');
        return false;
    }
}