<?php

use yii\db\Migration;

class m160711_062406_add_field_base_type_table_route_directions extends Migration
{
    public function up()
    {
        $this->addColumn('{{%route_directions}}','base_type',$this->integer('11')->defaultValue(0)->comment("Type: base,custom")->after('status'));
    }

    public function down()
    {
        $this->dropColumn('{{%route_directions}}','base_type');
        return false;
    }
}