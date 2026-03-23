<?php

use yii\db\Migration;

class m160920_060923_add_field_inbound_client_box_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','inbound_client_box',$this->string('32')->defaultValue('')->comment("Короб в котором тавор прибыл к нас на склад от клиента")->after('scan_in_datetime'));
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','inbound_client_box');
        return false;
    }
}