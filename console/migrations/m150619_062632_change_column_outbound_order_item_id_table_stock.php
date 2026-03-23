<?php

use yii\db\Schema;
use yii\db\Migration;

class m150619_062632_change_column_outbound_order_item_id_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','outbound_order_item_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Outbound order item id" AFTER `outbound_order_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','outbound_order_item_id');
    }
}
