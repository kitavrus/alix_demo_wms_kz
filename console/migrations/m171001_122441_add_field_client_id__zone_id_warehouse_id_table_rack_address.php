<?php

use yii\db\Migration;

class m171001_122441_add_field_client_id__zone_id_warehouse_id_table_rack_address extends Migration
{
    public function up()
    {
        $this->addColumn('{{%rack_address}}','client_id',$this->integer()->defaultValue(0)->comment("Client id")->after('id'));
        $this->addColumn('{{%rack_address}}','zone_id',$this->integer()->defaultValue(0)->comment("Zone id")->after('client_id'));
        $this->addColumn('{{%rack_address}}','warehouse_id',$this->smallInteger()->defaultValue(0)->comment("warehouse_id")->after('zone_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%rack_address}}','client_id');
        $this->dropColumn('{{%rack_address}}','zone_id');
        $this->dropColumn('{{%rack_address}}','warehouse_id');
        return false;
    }
}
