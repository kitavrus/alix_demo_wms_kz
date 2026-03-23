<?php

use yii\db\Migration;

class m170406_140808_add_field_party_number_table_return_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%return_orders}}','party_number',$this->string(64)->defaultValue(0)->comment("Party number")->after('warehouse_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%return_orders}}','party_number');
        return false;
    }
}