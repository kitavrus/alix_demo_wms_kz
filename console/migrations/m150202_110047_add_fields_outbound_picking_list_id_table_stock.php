<?php

use yii\db\Schema;
use yii\db\Migration;

class m150202_110047_add_fields_outbound_picking_list_id_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','outbound_picking_list_id',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal outbound picking list id" AFTER `outbound_order_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','outbound_picking_list_id');

        return false;
    }
}
