<?php

use yii\db\Schema;
use yii\db\Migration;

class m150427_114103_add_field_box_size_m3_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','box_size_m3', Schema::TYPE_STRING . '(32) NULL DEFAULT 0 COMMENT "Box size m3" AFTER `box_size_barcode`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','box_size_m3');
    }
}
