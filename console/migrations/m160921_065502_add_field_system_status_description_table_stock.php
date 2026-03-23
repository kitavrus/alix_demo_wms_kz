<?php

use yii\db\Migration;

class m160921_065502_add_field_system_status_description_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','system_status_description',$this->text()->defaultValue('')->comment("Описание системных статусов")->after('system_status'));
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','system_status_description');
        return false;
    }
}