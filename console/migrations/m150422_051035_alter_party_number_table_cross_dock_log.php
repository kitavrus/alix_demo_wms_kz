<?php

use yii\db\Schema;
use yii\db\Migration;

class m150422_051035_alter_party_number_table_cross_dock_log extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%cross_dock_log}}','party_number',Schema::TYPE_STRING . '(128) NULL DEFAULT ""');
    }

    public function down()
    {
        $this->alterColumn('{{%cross_dock_log}}','party_number',Schema::TYPE_INTEGER . '(11)');

        return false;
    }
}
