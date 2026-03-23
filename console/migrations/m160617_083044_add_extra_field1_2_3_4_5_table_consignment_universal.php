<?php

use yii\db\Migration;

class m160617_083044_add_extra_field1_2_3_4_5_table_consignment_universal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%consignment_universal}}','field_extra1',$this->text()->defaultValue('')->comment("Extra field 1")->after('extra_fields'));
        $this->addColumn('{{%consignment_universal}}','field_extra2',$this->text()->defaultValue('')->comment("Extra field 2")->after('field_extra1'));
        $this->addColumn('{{%consignment_universal}}','field_extra3',$this->text()->defaultValue('')->comment("Extra field 3")->after('field_extra2'));
        $this->addColumn('{{%consignment_universal}}','field_extra4',$this->text()->defaultValue('')->comment("Extra field 4")->after('field_extra3'));
        $this->addColumn('{{%consignment_universal}}','field_extra5',$this->text()->defaultValue('')->comment("Extra field 5")->after('field_extra4'));
    }

    public function down()
    {
        $this->dropColumn('{{%consignment_universal}}','field_extra1');
        $this->dropColumn('{{%consignment_universal}}','field_extra2');
        $this->dropColumn('{{%consignment_universal}}','field_extra3');
        $this->dropColumn('{{%consignment_universal}}','field_extra4');
        $this->dropColumn('{{%consignment_universal}}','field_extra5');
        return false;
    }
}