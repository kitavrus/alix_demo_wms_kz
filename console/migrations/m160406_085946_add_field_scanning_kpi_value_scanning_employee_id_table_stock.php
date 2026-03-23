<?php

use yii\db\Schema;
use yii\db\Migration;

class m160406_085946_add_field_scanning_kpi_value_scanning_employee_id_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}', 'kpi_value', Schema::TYPE_STRING. '(512) NULL DEFAULT "" comment "kpi value" AFTER  `address_sort_order`');
        $this->addColumn('{{%stock}}', 'scan_out_employee_id', Schema::TYPE_INTEGER. '(11) NULL DEFAULT "0" comment "scanning outbound employee id" AFTER  `id`');
        $this->addColumn('{{%stock}}', 'scan_in_employee_id', Schema::TYPE_INTEGER. '(11) NULL DEFAULT "0" comment "scanning inbound employee id" AFTER  `id`');
        $this->addColumn('{{%stock}}', 'scan_out_datetime', Schema::TYPE_INTEGER. '(11) NULL DEFAULT "0" comment "datetime scanning outbound" AFTER  `kpi_value`');
        $this->addColumn('{{%stock}}', 'scan_in_datetime', Schema::TYPE_INTEGER. '(11) NULL DEFAULT "0" comment " datetime scanning inbound" AFTER  `scan_out_datetime`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}', 'kpi_value');
        $this->dropColumn('{{%stock}}', 'scan_out_employee_id');
        $this->dropColumn('{{%stock}}', 'scan_in_employee_id');
        $this->dropColumn('{{%stock}}', 'scan_out_datetime');
        $this->dropColumn('{{%stock}}', 'scan_in_datetime');
    }
}