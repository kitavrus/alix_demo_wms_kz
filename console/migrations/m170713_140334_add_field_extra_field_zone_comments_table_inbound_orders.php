<?php

use yii\db\Migration;

class m170713_140334_add_field_extra_field_zone_comments_table_inbound_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inbound_orders}}','zone',$this->smallInteger()->defaultValue(0)->comment("Zone inbound: good, bad, defect")->after('expected_number_places_qty'));
        $this->addColumn('{{%inbound_orders}}','comments',$this->string(128)->defaultValue('')->comment("Comments")->after('data_created_on_client'));
    }

    public function down()
    {
        $this->dropColumn('{{%inbound_orders}}','zone');
        $this->dropColumn('{{%inbound_orders}}','comments');
        return false;
    }
}
