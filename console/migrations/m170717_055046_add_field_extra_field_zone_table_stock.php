<?php

use yii\db\Migration;

class m170717_055046_add_field_extra_field_zone_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','zone',$this->smallInteger()->defaultValue(0)->comment("Zone: good, bad, defect")->after('warehouse_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','zone');
        return false;
    }
}