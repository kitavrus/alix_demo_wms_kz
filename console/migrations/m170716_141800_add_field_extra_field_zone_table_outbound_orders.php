<?php

use yii\db\Migration;

class m170716_141800_add_field_extra_field_zone_table_outbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','zone',$this->smallInteger()->defaultValue(0)->comment("Zone outbound: good, bad, defect")->after('parent_order_number'));
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','zone');
        return false;
    }
}