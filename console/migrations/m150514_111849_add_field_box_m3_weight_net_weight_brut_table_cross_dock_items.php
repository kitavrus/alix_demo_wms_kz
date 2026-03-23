<?php

use yii\db\Schema;
use yii\db\Migration;

class m150514_111849_add_field_box_m3_weight_net_weight_brut_table_cross_dock_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock_items}}','box_m3', Schema::TYPE_STRING . '(32) NULL DEFAULT 0 COMMENT "Box size m3" AFTER `expected_number_places_qty`');
        $this->addColumn('{{%cross_dock_items}}','weight_net', Schema::TYPE_STRING . '(32) NULL DEFAULT 0 COMMENT "Box net weight" AFTER `box_m3`');
        $this->addColumn('{{%cross_dock_items}}','weight_brut', Schema::TYPE_STRING . '(32) NULL DEFAULT 0 COMMENT "Box brut weight" AFTER `weight_net`');
    }

    public function down()
    {
        $this->dropColumn('{{%cross_dock_items}}','box_m3');
        $this->dropColumn('{{%cross_dock_items}}','weight_net');
        $this->dropColumn('{{%cross_dock_items}}','weight_brut');
    }
}
