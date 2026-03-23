<?php

use yii\db\Migration;

/**
 * Class m171221_084008_add_field_address_size_table_stock
 */
class m171221_084008_add_field_address_size_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','address_pallet_qty',$this->smallInteger()->defaultValue(1)->comment("Address pallet qty")->after('secondary_address'));
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','address_pallet_qty');
        return false;
    }
}
