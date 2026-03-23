<?php

use yii\db\Migration;

class m170713_104004_add_field_extra_field_weight_brutto_netto_etc_table_product extends Migration
{
    public function up()
    {
        $this->addColumn('{{%product}}','weight_brutto',$this->decimal(6,3)->defaultValue(0)->comment("Weight brutto")->after('price'));
        $this->addColumn('{{%product}}','weight_netto',$this->decimal(6,3)->defaultValue(0)->comment("Weight netto")->after('weight_brutto'));
        $this->addColumn('{{%product}}','m3',$this->decimal(6,3)->defaultValue(0)->comment("The Value")->after('weight_netto'));
        $this->addColumn('{{%product}}','length',$this->decimal(6,3)->defaultValue(0)->comment("Length")->after('m3'));
        $this->addColumn('{{%product}}','width',$this->decimal(6,3)->defaultValue(0)->comment("Width")->after('length'));
        $this->addColumn('{{%product}}','height',$this->decimal(6,3)->defaultValue(0)->comment("Height")->after('width'));
        $this->addColumn('{{%product}}','barcode',$this->string(128)->defaultValue('')->comment("Barcode")->after('Height'));
    }

    public function down()
    {
        $this->dropColumn('{{%product}}','weight_brutto');
        $this->dropColumn('{{%product}}','weight_netto');
        $this->dropColumn('{{%product}}','m3');
        $this->dropColumn('{{%product}}','length');
        $this->dropColumn('{{%product}}','width');
        $this->dropColumn('{{%product}}','height');
        $this->dropColumn('{{%product}}','barcode');
        return false;
    }
}
