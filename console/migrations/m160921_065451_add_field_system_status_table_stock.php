<?php

use yii\db\Migration;

class m160921_065451_add_field_system_status_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','system_status',$this->string('32')->defaultValue('')->comment("Системный статус: используется только для бизнес логики")->after('inbound_client_box'));
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','system_status');
        return false;
    }
}