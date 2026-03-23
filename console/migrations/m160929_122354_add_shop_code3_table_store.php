<?php

use yii\db\Migration;

class m160929_122354_add_shop_code3_table_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','shop_code3',$this->string(64)->defaultValue('')->comment("client shop code 3")->after('shop_code2'));
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','shop_code3');
        return false;
    }
}