<?php

use yii\db\Schema;
use yii\db\Migration;

class m150129_091845_add_fields_in_out_order_number_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','inbound_order_number',Schema::TYPE_STRING . '(32) NULL DEFAULT "" COMMENT  "Inbound order number" AFTER `inbound_order_id`');
        $this->addColumn('{{%stock}}','outbound_order_number',Schema::TYPE_STRING . '(32) NULL DEFAULT "" COMMENT  "Outbound order number" AFTER `outbound_order_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','inbound_order_number');
        $this->dropColumn('{{%stock}}','outbound_order_number');

        return false;
    }
}
