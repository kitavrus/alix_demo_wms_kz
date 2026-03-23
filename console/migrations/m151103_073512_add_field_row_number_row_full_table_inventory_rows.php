<?php

use yii\db\Schema;
use yii\db\Migration;

class m151103_073512_add_field_row_number_row_full_table_inventory_rows extends Migration
{
    public function up()
    {
        $this->addColumn('{{%inventory_rows}}', 'row_number', Schema::TYPE_STRING. '(28) NULL DEFAULT "" comment "" AFTER  `column_number`');
        $this->addColumn('{{%inventory_rows}}', 'floor_number', Schema::TYPE_SMALLINT. ' NULL DEFAULT "0" comment "" AFTER  `row_number`');
    }

    public function down()
    {
        $this->dropColumn('{{%inventory_rows}}', 'row_number');
        $this->dropColumn('{{%inventory_rows}}', 'floor_number');
    }
}