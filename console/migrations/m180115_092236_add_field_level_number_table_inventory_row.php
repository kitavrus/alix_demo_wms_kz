<?php

use yii\db\Migration;

/**
 * Class m180115_092236_add_field_level_number_table_inventory_row
 */
class m180115_092236_add_field_level_number_table_inventory_row extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inventory_rows}}','level_number',$this->smallInteger()->defaultValue(0)->comment("Level number")->after('floor_number'));
    }

    public function down()
    {
        $this->dropColumn('{{%inventory_rows}}','level_number');
        return false;
    }
}