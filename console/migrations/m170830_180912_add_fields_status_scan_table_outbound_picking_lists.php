<?php

use yii\db\Migration;

class m170830_180912_add_fields_status_scan_table_outbound_picking_lists extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_picking_lists}}','status_scan',$this->smallInteger()->defaultValue(1)->comment("Scan status")->after('status'));
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_picking_lists}}','status_scan');
        return false;
    }
}