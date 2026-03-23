<?php

use yii\db\Migration;

class m170713_101929_add_field_extra_field1_2_3_4_5_table_product extends Migration
{
    public function up()
    {
        $this->addColumn('{{%product}}','field_extra1',$this->string(128)->defaultValue('')->comment("Extra field 1")->after('price'));
        $this->addColumn('{{%product}}','field_extra2',$this->string(256)->defaultValue('')->comment("Extra field 2")->after('field_extra1'));
        $this->addColumn('{{%product}}','field_extra3',$this->string(512)->defaultValue('')->comment("Extra field 3")->after('field_extra2'));
        $this->addColumn('{{%product}}','field_extra4',$this->text()->defaultValue('')->comment("Extra field 4")->after('field_extra3'));
        $this->addColumn('{{%product}}','field_extra5',$this->text()->defaultValue('')->comment("Extra field 5")->after('field_extra4'));
        $this->addColumn('{{%product}}','field_extra6',$this->text()->defaultValue('')->comment("Extra field 6")->after('field_extra5'));
    }

    public function down()
    {
        $this->dropColumn('{{%product}}','field_extra1');
        $this->dropColumn('{{%product}}','field_extra2');
        $this->dropColumn('{{%product}}','field_extra3');
        $this->dropColumn('{{%product}}','field_extra4');
        $this->dropColumn('{{%product}}','field_extra5');
        $this->dropColumn('{{%product}}','field_extra6');
        return false;
    }
}