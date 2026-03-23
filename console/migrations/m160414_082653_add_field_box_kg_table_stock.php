<?php

use yii\db\Schema;
use yii\db\Migration;

class m160414_082653_add_field_box_kg_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}', 'box_kg', Schema::TYPE_STRING. '(32) NULL DEFAULT "" comment "kg box" AFTER  `box_size_m3`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}', 'box_kg');
    }
}