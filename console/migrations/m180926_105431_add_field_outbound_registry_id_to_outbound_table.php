<?php

use yii\db\Migration;

/**
 * Class m180926_105431_add_field_outbound_registry_id_to_outbound_table
 */
class m180926_105431_add_field_outbound_registry_id_to_outbound_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','outbound_registry_id',$this->integer(11)->defaultValue(0)->comment("Outbound registry id")->after('id'));
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','outbound_registry_id');
        return false;
    }
}