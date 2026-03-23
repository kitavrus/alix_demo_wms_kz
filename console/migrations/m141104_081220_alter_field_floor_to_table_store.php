<?php

use yii\db\Schema;
use yii\db\Migration;

class m141104_081220_alter_field_floor_to_table_store extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `store` CHANGE `floor` `floor` SMALLINT( 6 ) NULL DEFAULT NULL");
    }

    public function down()
    {
        echo "m141104_081220_alter_field_floor_to_table_store cannot be reverted.\n";

        return false;
    }
}
