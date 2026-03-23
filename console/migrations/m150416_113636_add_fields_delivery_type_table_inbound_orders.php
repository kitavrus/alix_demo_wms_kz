<?php

use yii\db\Schema;
use yii\db\Migration;

class m150416_113636_add_fields_delivery_type_table_inbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_orders}}','delivery_type', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 COMMENT "CROSS-DOCK, RPT, etc ... " AFTER `order_type`');
        $this->addColumn('{{%outbound_orders}}','delivery_type', Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 COMMENT "CROSS-DOCK, RPT, etc ... " AFTER `order_type`');
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_orders}}','delivery_type');
        $this->dropColumn('{{%outbound_orders}}','delivery_type');
    }

}
