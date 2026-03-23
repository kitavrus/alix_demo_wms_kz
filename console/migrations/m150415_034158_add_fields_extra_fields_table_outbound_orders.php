<?php

use yii\db\Schema;
use yii\db\Migration;

class m150415_034158_add_fields_extra_fields_table_outbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','extra_fields',Schema::TYPE_TEXT . ' NULL DEFAULT "" COMMENT "Example JSON: order_number, who received order, etc ..." AFTER `data_created_on_client`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','extra_fields');
    }
}
