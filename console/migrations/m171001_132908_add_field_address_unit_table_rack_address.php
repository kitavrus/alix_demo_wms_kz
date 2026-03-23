<?php

use yii\db\Migration;

class m171001_132908_add_field_address_unit_table_rack_address extends Migration
{
    public function up()
    {
        $this->addColumn('{{%rack_address}}','address_unit1',$this->string(4)->defaultValue('')->comment("Address unit 1")->after('sort_order'));
        $this->addColumn('{{%rack_address}}','address_unit2',$this->string(4)->defaultValue('')->comment("Address unit 2")->after('address_unit1'));
        $this->addColumn('{{%rack_address}}','address_unit3',$this->string(4)->defaultValue('')->comment("Address unit 3")->after('address_unit2'));
        $this->addColumn('{{%rack_address}}','address_unit4',$this->string(4)->defaultValue('')->comment("Address unit 4")->after('address_unit3'));
        $this->addColumn('{{%rack_address}}','address_unit5',$this->string(4)->defaultValue('')->comment("Address unit 5")->after('address_unit4'));
        $this->addColumn('{{%rack_address}}','address_unit6',$this->string(4)->defaultValue('')->comment("Address unit 6")->after('address_unit5'));
    }

    public function down()
    {
        $this->dropColumn('{{%rack_address}}','address_unit1');
        $this->dropColumn('{{%rack_address}}','address_unit2');
        $this->dropColumn('{{%rack_address}}','address_unit3');
        $this->dropColumn('{{%rack_address}}','address_unit4');
        $this->dropColumn('{{%rack_address}}','address_unit5');
        $this->dropColumn('{{%rack_address}}','address_unit6');
        return false;
    }
}