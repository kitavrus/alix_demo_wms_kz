<?php

use yii\db\Schema;
use yii\db\Migration;

class m150224_102201_add_fields_outbound_id__order_number_etc_table_outbound_picking_lists extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_picking_lists}}','outbound_order_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" AFTER `id`');
        $this->addColumn('{{%outbound_picking_lists}}','page_number', Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" AFTER `employee_barcode`');
        $this->addColumn('{{%outbound_picking_lists}}','page_total', Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" AFTER `page_number`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_picking_lists}}','outbound_order_id');
        $this->dropColumn('{{%outbound_picking_lists}}','page_number');
        $this->dropColumn('{{%outbound_picking_lists}}','page_total');

        return false;
    }
}
