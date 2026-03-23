<?php

use yii\db\Migration;

class m161004_114727_add_field_extra_field1_2_3_4_5_table_return_order_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%return_order_items}}','field_extra1',$this->string(64)->defaultValue('')->comment("Extra field 1")->after('product_serialize_data'));
        $this->addColumn('{{%return_order_items}}','field_extra2',$this->string(128)->defaultValue('')->comment("Extra field 2")->after('field_extra1'));
        $this->addColumn('{{%return_order_items}}','field_extra3',$this->string(256)->defaultValue('')->comment("Extra field 3")->after('field_extra2'));
        $this->addColumn('{{%return_order_items}}','field_extra4',$this->text()->defaultValue('')->comment("Extra field 4")->after('field_extra3'));
        $this->addColumn('{{%return_order_items}}','field_extra5',$this->text()->defaultValue('')->comment("Extra field 5")->after('field_extra4'));
    }

    public function down()
    {
        $this->dropColumn('{{%return_order_items}}','field_extra1');
        $this->dropColumn('{{%return_order_items}}','field_extra2');
        $this->dropColumn('{{%return_order_items}}','field_extra3');
        $this->dropColumn('{{%return_order_items}}','field_extra4');
        $this->dropColumn('{{%return_order_items}}','field_extra5');
        return false;
    }
}
