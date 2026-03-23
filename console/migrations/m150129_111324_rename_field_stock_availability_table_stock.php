<?php

use yii\db\Schema;
use yii\db\Migration;

class m150129_111324_rename_field_stock_availability_table_stock extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `stock` CHANGE `stock_availability` `status_availability` TINYINT( 2 ) NULL DEFAULT '0' COMMENT '1 - Yes, 0 - No'");
    }

    public function down()
    {
        echo "m150129_111324_rename_field_stock_availability_table_stock cannot be reverted.\n";

        return false;
    }
}
