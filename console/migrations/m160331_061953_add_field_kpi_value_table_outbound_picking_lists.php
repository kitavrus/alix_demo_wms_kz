<?php

use yii\db\Schema;
use yii\db\Migration;

class m160331_061953_add_field_kpi_value_table_outbound_picking_lists extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_picking_lists}}', 'kpi_value', Schema::TYPE_STRING. '(512) NULL DEFAULT "" comment "КПЭ для сотрудника" AFTER  `end_datetime`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_picking_lists}}', 'kpi_value');
    }
}