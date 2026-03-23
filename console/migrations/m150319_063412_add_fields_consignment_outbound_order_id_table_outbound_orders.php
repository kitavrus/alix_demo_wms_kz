<?php

use yii\db\Schema;
use yii\db\Migration;

class m150319_063412_add_fields_consignment_outbound_order_id_table_outbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','consignment_outbound_order_id',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT "Consignment outbound order id" AFTER `parent_order_number`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','consignment_outbound_order_id');
    }
}
