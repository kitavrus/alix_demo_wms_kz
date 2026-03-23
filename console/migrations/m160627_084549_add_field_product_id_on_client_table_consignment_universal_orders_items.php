<?php

use yii\db\Migration;

class m160627_084549_add_field_product_id_on_client_table_consignment_universal_orders_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%consignment_universal_orders_items}}','product_id_on_client',$this->string('64')->defaultValue('')->comment("Product id on client system")->after('product_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%consignment_universal_orders_items}}','product_id_on_client');
        return false;
    }
}