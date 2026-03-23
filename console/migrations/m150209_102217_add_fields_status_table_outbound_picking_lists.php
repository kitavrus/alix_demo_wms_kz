<?php

use yii\db\Schema;
use yii\db\Migration;

class m150209_102217_add_fields_status_table_outbound_picking_lists extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_picking_lists}}','status',Schema::TYPE_SMALLINT . ' NULL DEFAULT "0" COMMENT  "Status: print, begin, end " AFTER `employee_barcode`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_picking_lists}}','status');

        return false;
    }
}
