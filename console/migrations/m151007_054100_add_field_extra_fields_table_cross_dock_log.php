<?php

use yii\db\Schema;
use yii\db\Migration;

class m151007_054100_add_field_extra_fields_table_cross_dock_log extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock_log}}', 'field_extra', Schema::TYPE_TEXT . ' NULL DEFAULT "" comment "Записываем содержимое (товары) короба" AFTER  `weight_brut`');
    }

    public function down()
    {
        $this->dropColumn('{{%cross_dock_log}}', 'field_extra');
    }
}
