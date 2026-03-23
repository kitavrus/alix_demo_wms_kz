<?php

use yii\db\Schema;
use yii\db\Migration;

class m150114_060943_add_field_deleted_table_inbound_order_items_process extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_order_items_process}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_order_items_process}}','deleted');
    }
}
