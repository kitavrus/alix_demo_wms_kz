<?php

use yii\db\Migration;

/**
 * Class m191018_085223_ecommerce_outbound_list_add_order_number
 */
class m191018_085223_ecommerce_outbound_list_add_order_number extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound_list}}','client_order_number',$this->string(256)->defaultValue('')->comment("Client order number")->after('our_outbound_id'));
        $this->addColumn('{{%ecommerce_outbound_list}}','ttn_delivery_company',$this->string(256)->defaultValue('')->comment("Client order number")->after('client_order_number'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound_list}}','client_order_number');
        $this->dropColumn('{{%ecommerce_outbound_list}}','ttn_delivery_company');
        return false;
    }
}