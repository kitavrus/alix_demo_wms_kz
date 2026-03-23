<?php

use yii\db\Schema;
use yii\db\Migration;

class m150619_062648_change_column_inbound_order_item_id_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','inbound_order_item_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Inbound order item id" AFTER `inbound_order_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','inbound_order_item_id');
    }
}
