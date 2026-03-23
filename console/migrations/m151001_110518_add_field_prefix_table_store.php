<?php

use yii\db\Schema;
use yii\db\Migration;

class m151001_110518_add_field_prefix_table_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}', 'city_prefix', Schema::TYPE_STRING . '(4) NULL DEFAULT "" comment "перфикс к городу на этикетки коробов" AFTER  `internal_code`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}', 'city_prefix');
    }
}