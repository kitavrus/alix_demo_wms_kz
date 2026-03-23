<?php

use yii\db\Schema;
use yii\db\Migration;

class m150430_061459_add_field_expected_rpt_places_qty_table_cross_dock_log extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock_log}}','expected_rpt_places_qty', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Expected rpt places qty" AFTER `expected_number_places_qty`');
    }

    public function down()
    {
        $this->dropColumn('{{%cross_dock_log}}','expected_rpt_places_qty');
    }
}
