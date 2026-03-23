<?php

use yii\db\Schema;
use yii\db\Migration;

class m150430_055409_add_field_accepted_rpt_places_qty_table_consignment_cross_dock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%consignment_cross_dock}}','accepted_rpt_places_qty', Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Accepted rpt places qty" AFTER `expected_number_places_qty`');
    }

    public function down()
    {
        $this->dropColumn('{{%consignment_cross_dock}}','accepted_rpt_places_qty');
    }
}
