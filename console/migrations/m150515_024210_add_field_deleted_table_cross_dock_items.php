<?php

use yii\db\Schema;
use yii\db\Migration;

class m150515_024210_add_field_deleted_table_cross_dock_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cross_dock_items}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
    }

    public function down()
    {
        echo "m150515_024210_add_field_deleted_table_cross_dock_items cannot be reverted.\n";

        return false;
    }
}
