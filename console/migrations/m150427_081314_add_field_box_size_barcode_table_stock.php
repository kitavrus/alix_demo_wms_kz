<?php

use yii\db\Schema;
use yii\db\Migration;

class m150427_081314_add_field_box_size_barcode_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','box_size_barcode', Schema::TYPE_STRING . '(32) NULL COMMENT "Box size barcode" AFTER `box_barcode`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','box_size_barcode');
    }
    // box_size_m3
}
