<?php

use yii\db\Schema;
use yii\db\Migration;

class m150417_062522_add_fields_delivery_type_table_consignment_in_out_bound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%consignment_inbound_orders}}','delivery_type', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 COMMENT "CROSS-DOCK, RPT, etc ... " AFTER `order_type`');
        $this->addColumn('{{%consignment_outbound_orders}}','delivery_type', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 COMMENT "CROSS-DOCK, RPT, etc ... " AFTER `order_type`');
    }

    public function down()
    {
        $this->dropColumn('{{%consignment_inbound_orders}}','delivery_type');
        $this->dropColumn('{{%consignment_outbound_orders}}','delivery_type');
    }
}
