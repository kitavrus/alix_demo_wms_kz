<?php

use yii\db\Schema;
use yii\db\Migration;

class m150209_064155_add_fields_outbound_picking_list_barcode_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','outbound_picking_list_barcode',Schema::TYPE_STRING . '(32) NULL DEFAULT "" COMMENT  "Internal outbound picking list barcode" AFTER `outbound_picking_list_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','outbound_picking_list_barcode');

        return false;
    }
}
