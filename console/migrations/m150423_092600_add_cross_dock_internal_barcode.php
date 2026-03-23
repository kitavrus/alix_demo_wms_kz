<?php

use yii\db\Schema;
use yii\db\Migration;

class m150423_092600_add_cross_dock_internal_barcode extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock}}','internal_barcode', Schema::TYPE_STRING . '(128) NULL COMMENT "Our barcode" AFTER `from_point_title`');
    }

    public function down()
    {
        $this->dropColumn('{{%cross_dock}}','internal_barcode');
    }
}
